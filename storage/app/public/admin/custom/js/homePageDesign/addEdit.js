document.addEventListener('DOMContentLoaded', function () {
    var langSelect = document.getElementById('lang_change');
    if (!langSelect) return;

    langSelect.addEventListener('change', function () {
        var selected = langSelect.options[langSelect.selectedIndex];
        var url = selected.getAttribute('data-action');
        var id = selected.getAttribute('data-id');
        var langCode = selected.value;

        fetch(url + '?id=' + id + '&langCode=' + encodeURIComponent(langCode), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                Object.keys(data).forEach(function (key) {
                    var field = document.querySelector('[name="' + key + '"]');
                    if (field && field.type !== 'file') {
                        field.value = data[key] || '';
                    }
                });
                document.querySelectorAll('.lang_code_field').forEach(function (el) {
                    el.value = langCode;
                });
            })
            .catch(function (err) {
                console.error('Failed to load language data', err);
            });
    });
});
