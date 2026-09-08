<script>
(function () {
    const formSelector = @json($formSelector ?? '#account_information_frm');
    const phoneSelector = @json($phoneSelector ?? '#contact_number');
    const phoneErrorSelector = @json($phoneErrorSelector ?? '#contact_number_error');
    const firstNameSelector = @json($firstNameSelector ?? '#name');
    const lastNameSelector = @json($lastNameSelector ?? '#lastname');
    const submitButtonSelector = @json($submitButtonSelector ?? null);

    function isValidUrl(value) {
        if (!value) {
            return true;
        }
        try {
            const url = new URL(value);
            return url.protocol === 'http:' || url.protocol === 'https:';
        } catch (e) {
            return false;
        }
    }

    function setFieldError(selector, message) {
        const $input = $(selector);
        if (!$input.length) {
            return;
        }
        let $err = $input.closest('.mb-3').find('.js-field-error');
        if (!$err.length) {
            $err = $('<span class="text-danger text-left js-field-error"></span>');
            $input.closest('.mb-3').append($err);
        }
        $err.html(message || '');
    }

    function validateAdminUserForm() {
        let valid = true;
        const firstName = ($(firstNameSelector).val() || '').trim();
        const lastName = ($(lastNameSelector).val() || '').trim();
        const phone = ($(phoneSelector).val() || '').trim();

        setFieldError(firstNameSelector, '');
        setFieldError(lastNameSelector, '');
        $(phoneErrorSelector).html('');

        if (firstName === '' || firstName.length < 2) {
            setFieldError(firstNameSelector, 'Please enter first name (at least 2 characters)!');
            valid = false;
        }

        if (lastName === '') {
            setFieldError(lastNameSelector, 'Please enter last name!');
            valid = false;
        }

        if (phone === '') {
            $(phoneErrorSelector).html('Please enter contact number!');
            valid = false;
        } else if (!/^\d+$/.test(phone)) {
            $(phoneErrorSelector).html('Please enter only numeric number!');
            valid = false;
        } else if (phone.length !== 10) {
            $(phoneErrorSelector).html('Please enter 10 digit numeric number!');
            valid = false;
        }

        ['website_url', 'linkedin_url', 'facebook_url', 'instagram_url', 'twitter_url'].forEach(function (id) {
            const $input = $('#' + id);
            if (!$input.length) {
                return;
            }
            const val = ($input.val() || '').trim();
            const $err = $('#' + id + '_error');
            if ($err.length) {
                $err.html('');
            }
            if (val !== '' && !isValidUrl(val)) {
                if ($err.length) {
                    $err.html('Please enter a valid URL starting with http:// or https://');
                }
                valid = false;
            }
        });

        return valid;
    }

    $(document).ready(function () {
        if (submitButtonSelector) {
            $('body').on('click', submitButtonSelector, function () {
                if (validateAdminUserForm()) {
                    $(formSelector).submit();
                }
            });
            return;
        }

        $(formSelector).on('submit', function (e) {
            if (!validateAdminUserForm()) {
                e.preventDefault();
            }
        });
    });
})();
</script>
