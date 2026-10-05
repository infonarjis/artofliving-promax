$(document).ready((function() {
    $('#confirm_password').keyup(function(){
        $(this).next('label').remove();
        if(isSameValue($(this).val(),$('#new_password').val()) == false){
            $(this).after('<label id="confirm_password-error" class="error" for="confirm_password">Please enter the same password again.</label>');
        }
    });
    $('#new_password').keyup(function(){
        $('#confirm_password').next('label').remove();
        if(isSameValue($(this).val(),$('#confirm_password').val()) == false && $('#confirm_password').val() !=''){
            $('#confirm_password').after('<label id="confirm_password-error" class="error" for="confirm_password">Please enter the same password again.</label>');
        }
    });


    $("#submitChangePassword").click((function(e) {
        e.preventDefault();
        if ($("#changePasswordUpdate").valid()) {
            $(this).text("");
            $(this).append('<i class="me-1 bx bx-loader-alt bx-spin text-white"></i>').append("Loading...");
            $(this).prop("disabled", true);
            let e = new FormData($("#changePasswordUpdate")[0]);
            let s = $("#changePasswordUpdate").attr("action");
            ajaxRequest("#changePasswordUpdate", e, s, "responseAdd")
        } else {
            return false
        }
    }))
}));

function responseAdd(e, s) {
    $("#submitChangePassword").text("Submit");
    $("#submitChangePassword").prop("disabled", false);
    if (s.status == "success") {
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", s.msg, "Success");
    } else {
        showNotification("top-0", "end-0", "bg-success", "withicon", "fa fa-check", s.msg, "Success");
    }
}