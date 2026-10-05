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
    $.each(response, function(index, value) {
        let el = $('#'+index);

        if(el.is('textarea')){
            el.text(value);
        } else if(el.attr('type') !== 'file'){
            el.val(value);
        }

        // image preview
        if(index.includes('_image') || index.includes('_banner') || index.includes('_logo')){
            $('#preview_'+index).attr('src', value);
        }
    });
}