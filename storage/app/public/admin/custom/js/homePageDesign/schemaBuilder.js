document.addEventListener('DOMContentLoaded', function () {
    var tabsWrapper = document.getElementById('tabsWrapper');
    var tabTemplate = document.getElementById('tabTemplate');
    var fieldTemplate = document.getElementById('fieldTemplate');
    var addTabBtn = document.getElementById('addTabBtn');
    var form = document.getElementById('designForm');
    var schemaInput = document.getElementById('schemaInput');

    function addTab(tabKey, tabLabel) {
        var clone = tabTemplate.content.cloneNode(true);
        var block = clone.querySelector('.tab-block');

        if (tabKey) block.querySelector('.tab-key').value = tabKey;
        if (tabLabel) block.querySelector('.tab-label').value = tabLabel;

        block.querySelector('.removeTabBtn').addEventListener('click', function () {
            block.remove();
        });

        block.querySelector('.addFieldBtn').addEventListener('click', function () {
            addField(block.querySelector('.fields-wrapper'));
        });

        tabsWrapper.appendChild(clone);
        // clone is a DocumentFragment; grab the actual appended node for further population
        return tabsWrapper.lastElementChild;
    }

    function addField(fieldsWrapper, fieldKey, config) {
        config = config || {};
        var clone = fieldTemplate.content.cloneNode(true);
        var block = clone.querySelector('.field-block');

        if (fieldKey) block.querySelector('.field-key').value = fieldKey;
        if (config.label) block.querySelector('.field-label').value = config.label;
        if (config.type) block.querySelector('.field-type').value = config.type;
        if (config.maxLength) block.querySelector('.field-maxlength').value = config.maxLength;
        if (config.column) block.querySelector('.field-column').value = config.column;
        block.querySelector('.field-required').checked = config.is_required === undefined
            ? true
            : !!config.is_required;

        block.querySelector('.removeFieldBtn').addEventListener('click', function () {
            block.remove();
        });

        fieldsWrapper.appendChild(clone);
    }

    addTabBtn.addEventListener('click', function () {
        addTab();
    });

    // Pre-fill from an existing schema (Manage Fields screen) if present,
    // otherwise start with one empty tab + field (Create Design screen).
    var existing = window.existingSchema && window.existingSchema.tabs
        ? window.existingSchema.tabs
        : null;

    if (existing && Object.keys(existing).length) {
        Object.keys(existing).forEach(function (tabKey) {
            var tab = existing[tabKey];
            var tabNode = addTab(tabKey, tab.label);
            var fieldsWrapper = tabNode.querySelector('.fields-wrapper');
            var fields = tab.fields || {};
            Object.keys(fields).forEach(function (fieldKey) {
                addField(fieldsWrapper, fieldKey, fields[fieldKey]);
            });
            if (Object.keys(fields).length === 0) {
                addField(fieldsWrapper);
            }
        });
    } else {
        var firstTab = addTab();
        addField(firstTab.querySelector('.fields-wrapper'));
    }

    form.addEventListener('submit', function (e) {
        var tabs = {};

        document.querySelectorAll('.tab-block').forEach(function (tabBlock) {
            var tabKey = tabBlock.querySelector('.tab-key').value.trim();
            var tabLabel = tabBlock.querySelector('.tab-label').value.trim();
            if (!tabKey) return;

            var fields = {};
            tabBlock.querySelectorAll('.field-block').forEach(function (fieldBlock) {
                var fieldKey = fieldBlock.querySelector('.field-key').value.trim();
                if (!fieldKey) return;

                var type = fieldBlock.querySelector('.field-type').value;
                var config = {
                    is_required: fieldBlock.querySelector('.field-required').checked ? 'required' : '',
                    label: fieldBlock.querySelector('.field-label').value.trim() || fieldKey,
                    column: fieldBlock.querySelector('.field-column').value,
                };
                var maxLength = fieldBlock.querySelector('.field-maxlength').value;
                if (maxLength) config.maxLength = maxLength;

                if (type === 'file') {
                    config.type = 'file';
                    config.path_value = 'upload_path.HOMEPAGE_BANNER_IMAGE_URL';
                    config.class = 'required';
                } else if (type === 'textarea') {
                    config.type = 'textarea';
                } else if (type === 'number') {
                    config.type = 'number';
                }

                fields[fieldKey] = config;
            });

            tabs[tabKey] = { label: tabLabel || tabKey, fields: fields };
        });

        if (Object.keys(tabs).length === 0) {
            e.preventDefault();
            alert('Add at least one section/tab with one field before saving.');
            return;
        }

        schemaInput.value = JSON.stringify({ tabs: tabs });
    });
});
