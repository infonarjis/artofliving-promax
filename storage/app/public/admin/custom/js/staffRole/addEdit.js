$(document).ready(function () {
    // Wire up every "master" permission row so its dependents react to it.
    $('.sr-perm-row[data-master="1"]').each(function () {
        const masterField = $(this).data('field');
        $(`input[name="${masterField}"]`).on('change', function () {
            applyDependency(masterField, $(this).val());
        });
        // set initial state on load
        const checkedVal = $(`input[name="${masterField}"]:checked`).val();
        if (checkedVal) applyDependency(masterField, checkedVal);
    });

    // Basic client-side check before submit (server still validates).
    $('#addEditForm').on('submit', function (e) {
        const roleName = $('input[name="role_name"]').val().trim();
        if (!roleName) {
            e.preventDefault();
            $('input[name="role_name"]').addClass('sr-input-error').focus();
        }
    });
});

/**
 * When a "master" field (e.g. view_member, photo_approval) changes,
 * enable/disable/force the rows that declared data-depends-on that field.
 *
 *  - master = "No"           -> dependents disabled and forced to "No"
 *  - master = "Own Members"  -> dependents can't be "All Members"
 *  - master = "All Members"  -> dependents fully enabled
 */
function applyDependency(masterField, masterValue) {
    $(`.sr-perm-row[data-depends-on="${masterField}"]`).each(function () {
        const field = $(this).data('field');
        const $inputs = $(`input[name="${field}"]`);

        $inputs.each(function () {
            const $input = $(this);
            const val = $input.val();

            if (masterValue === 'No') {
                $input.prop('disabled', true);
                if (val === 'No') $input.prop('checked', true);
            } else if (masterValue === 'Own Members') {
                $input.prop('disabled', val === 'All Members');
                if (val === 'All Members' && $input.is(':checked')) {
                    $(`input[name="${field}"][value="Own Members"]`).prop('checked', true);
                }
            } else {
                $input.prop('disabled', false);
            }
        });
    });
}

/** Open a section and scroll it into view (used by the quick-jump chips). */
function srJumpTo(sectionKey) {
    const section = document.getElementById('sec-' + sectionKey);
    if (!section) return;
    section.classList.add('is-open');
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}