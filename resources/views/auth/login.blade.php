@extends('layouts.app')

@section('content')
<style>
    .otp-input-container {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    .otp-input {
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: bold;
        border: 2px solid #ced4da;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    .otp-input:focus {
        border-color: #007bff;
        background-color: #fff;
        outline: none;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
    }
    .otp-input.has-value {
        border-color: #28a745;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card {{ isset($isAdmin) && $isAdmin ? 'border-primary shadow' : '' }}">
                <div class="card-header {{ isset($isAdmin) && $isAdmin ? 'bg-primary text-white' : '' }}">{{ isset($isAdmin) && $isAdmin ? __('Admin Login') : __('Login') }}</div>

                <div class="card-body">
                    @if(isset($isAdmin) && $isAdmin)
                        {{-- Admin Password Login --}}
                        <form method="POST" action="{{ route('admin.login.submit') }}">
                            @csrf
                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        {{-- Attendee OTP Login --}}
                        <form method="POST" action="{{ isset($event) ? route('event.user.login.submit', $event->id) : route('login') }}">
                            @csrf
                            <div id="login-form-container">
                                <div id="email-step">
                                    <div class="row mb-4 justify-content-center text-center">
                                        <div class="col-md-10">
                                            <h5 class="mb-3 text-primary">{{ __('Sign In') }}</h5>
                                            <div class="mb-4">
                                                <input id="email" type="email" class="form-control form-control-lg text-center" name="email" value="{{ old('email') }}" required placeholder="name@example.com" autocomplete="email" autofocus>
                                                <span class="invalid-feedback d-block mt-2" role="alert" id="email-error"></span>
                                            </div>
                                            <button type="button" id="send-otp-btn" class="btn btn-primary btn-lg px-5">
                                                {{ __('Send OTP') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="otp-step" style="display: none;">
                                    <div class="text-center mb-4">
                                        <h5 class="mb-3 text-primary">{{ __('Enter Verification Code') }}</h5>
                                        <div class="otp-input-container mb-3">
                                            <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                                            <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                                            <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                                            <input type="text" class="otp-input" maxlength="1" pattern="\d*" inputmode="numeric">
                                        </div>
                                        <input type="hidden" id="otp" name="otp">
                                        <span class="invalid-feedback d-block mb-2" role="alert" id="otp-error"></span>
                                        <p class="text-muted small mb-4">{{ __('A 4-digit code has been sent to your email.') }}</p>
                                        
                                        <div class="form-check d-inline-block mb-4">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 d-md-block">
                                            <button type="button" id="verify-otp-btn" class="btn btn-primary btn-lg px-5">
                                                {{ __('Login') }}
                                            </button>
                                        </div>

                                        <div class="mt-4">
                                            <button type="button" id="change-email-btn" class="btn btn-link text-decoration-none">
                                                <i class="bx bx-chevron-left"></i> {{ __('Change Email') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
            @if(!isset($isAdmin) || !$isAdmin)
                @if(isset($event))
                <h3 class="mt-3">
                    Don't have an account?
                    <a href="{{ route('event.user.register', $event->id) }}">Register here</a>
                </h3>
                @else
                <h3 class="mt-3">
                    Don't have an account?
                    <a href="{{ route('register') }}">Register here</a>
                </h3>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const isAdmin = "{{ (request()->is('admin/login') || request()->is('admin')) ? 'true' : 'false' }}";
    const eventId = "{{ isset($event) ? $event->id : '' }}";

    $('#send-otp-btn').click(function() {
        const email = $('#email').val();
        $('#email-error').text('');
        
        if (!email) {
            $('#email-error').text('Please enter your email.');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).text('Sending...');

        $.ajax({
            url: "{{ route('send.otp') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                email: email,
                isAdmin: isAdmin,
                event_id: eventId
            },
            success: function(response) {
                if (response.success) {
                    $('#email-step').hide();
                    $('#otp-step').show();
                    $('#otp').focus();
                } else {
                    $('#email-error').text(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                $('#email-error').text(response ? response.message : 'Failed to send OTP. Please try again.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Send OTP');
            }
        });
    });

    $('#verify-otp-btn').click(function() {
        const email = $('#email').val();
        const otp = $('#otp').val();
        const remember = $('#remember').is(':checked');
        $('#otp-error').text('');

        if (!otp || otp.length !== 4) {
            $('#otp-error').text('Please enter a 4-digit OTP.');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).text('Verifying...');

        $.ajax({
            url: "{{ route('verify.otp') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                email: email,
                otp: otp,
                remember: remember,
                event_id: eventId
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    $('#otp-error').text(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                $('#otp-error').text(response ? response.message : 'Invalid or expired OTP.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Login');
            }
        });
    });

    $('#change-email-btn').click(function() {
        $('#otp-step').hide();
        $('#email-step').show();
        $('#email').focus();
        $('.otp-input').val('');
        $('#otp').val('');
    });

    // Handle OTP input navigation
    $('.otp-input').on('input', function(e) {
        const $this = $(this);
        const val = $this.val();
        
        // Ensure only numbers
        if (!/^\d*$/.test(val)) {
            $this.val('');
            return;
        }

        if (val) {
            $this.addClass('has-value');
            $this.next('.otp-input').focus();
        } else {
            $this.removeClass('has-value');
        }
        
        updateOtpValue();
    });

    $('.otp-input').on('keydown', function(e) {
        const $this = $(this);
        if (e.key === 'Backspace' && !$this.val()) {
            $this.prev('.otp-input').focus();
        }
    });

    // Handle paste
    $('.otp-input').on('paste', function(e) {
        e.preventDefault();
        const data = (e.originalEvent || e).clipboardData.getData('text');
        const digits = data.match(/\d/g);
        
        if (digits && digits.length >= 4) {
            $('.otp-input').each(function(index) {
                $(this).val(digits[index]).addClass('has-value');
            });
            updateOtpValue();
            $('.otp-input').last().focus();
        }
    });

    function updateOtpValue() {
        let otp = '';
        $('.otp-input').each(function() {
            otp += $(this).val();
        });
        $('#otp').val(otp);
    }
});
</script>
@endsection