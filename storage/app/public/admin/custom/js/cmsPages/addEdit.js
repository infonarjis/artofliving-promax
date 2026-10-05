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
    // For Language Change:
    $('#lang_change_new').change(function() {
        let selectedOption = $(this).find('option:selected');
        let dataId = selectedOption.data('id');
        let action = selectedOption.data('action');
        let selectedLangCode = selectedOption.val();
        let formData = new FormData();
        formData.append("id", dataId);
        formData.append("langCode", selectedLangCode);
        ajaxRequest($(this), formData, action, "ajaxgetLangDataAboutUsResponce");
    });
});

function ajaxgetLangDataAboutUsResponce(_this, response){
    $('#lang_id').val(response.lang_id);
    $('#lang_code').val(response.lang_code);
    $('#about_us_title').val(response.about_us_title);
    $('#about_us_desc').val(response.about_us_desc);
    $('#about_us_brow_sec1').val(response.about_us_brow_sec1);
    $('#about_us_brow_sec2').val(response.about_us_brow_sec2);
    $('#about_us_brow_sec3').val(response.about_us_brow_sec3);
    $('#about_us_desc').trumbowyg('html',response.about_us_desc);

    // For Images :
    if(response.about_us_image == ''){
        $('.about_us_image').hide();
    }else{
        $('.about_us_image').show();
        $('.about_us_image').attr('src', response.about_us_image);
    }
    if(response.about_us_logo == ''){
        $('.about_us_logo').hide();
    }else{
        $('.about_us_logo').show();
        $('.about_us_logo').attr('src', response.about_us_logo);
    }
}

function ajaxgetLangDataResponce(_this, response){
    $('#lang_id').val(response.lang_id);
    $('#lang_code').val(response.lang_code);
    $('#page_title').val(response.page_title);
    $('#seo_title').val(response.seo_title);
    $('#seo_description').val(response.seo_description);
    $('#seo_keywords').val(response.seo_keywords);
    // Update Trumbowyg editor if initialized
    const $editor = $('#page_content');
    if ($editor.data('trumbowyg')) {
        $editor.trumbowyg('html', response.page_content);
    } else {
        console.warn('Trumbowyg instance is not ready. Setting value in textarea.');
        // Fallback to just setting textarea value
        $editor.val(response.page_content);
    }
}