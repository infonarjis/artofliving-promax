$(document).ready(function () {
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

    
    toggleStoryFields();
    $('#story_type').change(function() {
        toggleStoryFields();
    });
    $('input[name="video_type"]').change(function () {
        toggleStoryFields();
    });
});
function toggleStoryFields() {
    let type = $('#story_type').val();
    let video_type = $('input[name="video_type"]:checked').val();
    /* Hide everything first */
    $('.video_type').hide();
    $('.video_link').hide();
    $('.wedding_photo').hide();
    $('.wedding_video_file').hide();
    $('.wedding_video_thumbnail').hide();
    /* Photo Story */
    if (type === 'Photo Story') {
        $('.wedding_photo').show();
    }
    /* Video Story */
    else if (type === 'Video Story') {
        $('.wedding_photo').hide();
        $('.video_type').show();
        if (video_type === 'youtube') {
            $('.video_link').show();
        }
        if (video_type === 'video') {
            $('.wedding_video_file').show();
            $('.wedding_video_thumbnail').show();
        }
    }
}

function ajaxgetLangDataResponce(_this, response){
    toggleStoryFields();

    $('#lang_id').val(response.lang_id);
    $('#lang_code').val(response.lang_code);
    $('#bridename').val(response.bridename);
    $('#groomname').val(response.groomname);
    $('#successmessage').html(response.successmessage);

    // Reset image first
    $('.weddingphoto').hide().attr('src', '');

    if(response.weddingphoto){
        $('.weddingphoto').show().attr('src', response.weddingphoto);
    }
}
