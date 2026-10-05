$(document).ready(function () {
    // For Language Change:
    $('#lang_change').change(function() {
        let selectedOption = $(this).find('option:selected');
        let dataId = selectedOption.data('id');
        let action = selectedOption.data('action');
        let selectedLangCode = selectedOption.val();
        let formData = new FormData();
        formData.append("id", dataId);
        formData.append("langCode", selectedLangCode);
        ajaxRequest($(this), formData, action, "ajaxgetLangDataResponce");
    });
});
function ajaxgetLangDataResponce(_this, response){
    $('#lang_id').val(response.lang_id);
    $('#lang_code').val(response.lang_code);
    $('#page_title').val(response.page_title);
    $('#seo_title').val(response.seo_title);
    $('#seo_description').val(response.seo_description);
    $('#seo_keywords').val(response.seo_keywords);
    $('#og_title').val(response.og_title);
    $('#og_image').val(response.og_image );
    $('#og_description').val(response.og_description );
}