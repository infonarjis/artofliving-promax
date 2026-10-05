$(document).ready((function () {
    $(".single").select2({
        placeholder: "Please Select",
        allowClear: true
    });

    var all_single = $('#all_single').val();
    $('.member_list_email').hide();
    $('#member_list_email').val('');
    if(all_single == 'Single'){
        $('.member_list_email').show();
    }else{
        $('.member_list_email').hide();
        $('#member_list_email').val('');
    }

    $('#member_list_email').select2({
        placeholder: 'Search Member',
        allowClear: !0,
        closeOnSelect: false,
        minimumInputLength: 2,
        ajax: {
            url: $("#base_url").val() + "/member-list-bulk-notification",
            type: "POST",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term,
                    get_list: $('#status').val(),
                    disp_on: 'Select Member',
                    _token: $("input[name=_token]").val()
                };
            },
            processResults: function (data) {
                return { results: data };
            }
        }
    });
}));

function change_after_staus(){
    $('#all_single').val('');
    $('.member_list_email').hide();
    $('#member_list_email').val('');
}

function get_email_list(){
    var status = $('#status').val();
    if(status==''){
        alert('Please Select Status');
        $('#all_single').val('');
    }
    var all_single = $('#all_single').val();
    if(all_single == 'Single'){
        $('.member_list_email').show();
    }else{
        $('.member_list_email').hide();
        $('#member_list_email').val('');
    }
}