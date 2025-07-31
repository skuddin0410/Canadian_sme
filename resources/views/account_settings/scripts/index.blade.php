<script>
    @if(Session::has('success'))
        alertify.success("{{ Session::get('success') }}");
    @endif
    $(document).ready(function () {
        $('body').on('click', '#account_info_submit_btn', function() {
            let contact_number=$("#contact_number").val();
            $("#contact_number_error").html('');
            if(contact_number=='') {
                $("#contact_number_error").html('Please enter contact number!');
            }else if(isNaN(contact_number)) {
                $("#contact_number_error").html('Please enter only numeric number!');
            }else if(contact_number.length<10 || contact_number.length>10) {
                $("#contact_number_error").html('Please enter 10 digit numeric number!');
            }else {
                $("#account_information_frm").submit();
            }
        })
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