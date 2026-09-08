<script>
    @if(Session::has('success'))
        alertify.success("{{ Session::get('success') }}");
    @endif
    $(document).ready(function () {
        $('body').on('click', '#change_password_submit_btn', function() {
            let old_password=$("#old_password").val();
            let new_password=$("#new_password").val();
            let confirm_password=$("#confirm_password").val();
            $("#old_password_error").html('');
            $("#new_password_error").html('');
            $("#confirm_password_error").html('');
            if(old_password=='') {
                $("#old_password_error").html('Please enter old password!');
            }else if(new_password=='') {
                $("#new_password_error").html('Please enter new password!');
            }else if(confirm_password=='') {
                $("#confirm_password_error").html('Please enter confirm password!');
            }else if(new_password!=confirm_password) {
                $("#confirm_password_error").html('The new password and confirm password not matched!');
            }else {
                $("#change_password_frm").submit();
            }
        })
    });
</script>
@include('account_settings.scripts.profile-form-validation', [
    'formSelector' => '#account_information_frm',
    'phoneSelector' => '#contact_number',
    'phoneErrorSelector' => '#contact_number_error',
    'firstNameSelector' => '#name',
    'lastNameSelector' => '#lastname',
    'submitButtonSelector' => '#account_info_submit_btn',
])
