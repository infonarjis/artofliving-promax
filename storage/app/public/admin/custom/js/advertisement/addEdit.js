$(function () {

    function toggleAdFields() {
        const type = $('input[name="adv_type"]:checked').val();

        if (type === 'banner') {
            $('.banner_adv').stop(true, true).slideDown();
            $('.adsence_adv').stop(true, true).slideUp();
        } else if (type === 'adsense') {
            $('.adsence_adv').stop(true, true).slideDown();
            $('.banner_adv').stop(true, true).slideUp();
        }
    }

    // On change
    $(document).on('change', 'input[name="adv_type"]', toggleAdFields);

    // On page load (no timeout needed)
    toggleAdFields();
});