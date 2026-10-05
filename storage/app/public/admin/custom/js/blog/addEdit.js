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
    $('#title').val(response.title);
    // For Images :
    $('.blog_image').attr('src', response.blog_image);
    // Update Trumbowyg editor if initialized
    const $editor = $('#content');
    if ($editor.data('trumbowyg')) {
        $editor.trumbowyg('html', response.content);
    } else {
        console.warn('Trumbowyg instance is not ready. Setting value in textarea.');
        // Fallback to just setting textarea value
        $editor.val(response.content);
    }
}