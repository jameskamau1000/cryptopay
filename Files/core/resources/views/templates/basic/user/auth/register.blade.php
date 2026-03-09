@extends($activeTemplate.'layouts.app')

@php
    $policyPages = getContent('policy_pages.element', orderById:true);
    $register = @getContent('login_register.content', true)->data_values; 
@endphp

@section('app')
    <!-- =============================== Account Section Start Here ====================== -->
    <section class="account">
        <div class="account__shape">
            <img src="{{ asset($activeTemplateTrue.'frontend/images/shapes/account-shape.png') }}" alt="">
        </div>
        <a href="{{ route('home') }}" class="account__logo">
            <img src="{{ siteLogo() }}" alt="@lang('Logo')" class="account__logo-img light">
            <img src="{{ siteLogo('dark') }}" alt="" class="account__logo-img dark">
        </a>
        <a href="{{ route('home') }}" class="account__backtohome btn btn--light btn--icon"><i class="fas fa-times"></i></a>

        <div class="container">
            <div class="row gy-5 align-items-center flex-wrap-reverse">
                <div class="col-md-6">
                    <div class="account-thumb">
                        <img src="{{ getImage('assets/images/frontend/login_register/' .@$register->image, '615x620') }}" alt="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="account-form">  
                        <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha @if (!gs('registration')) form-disabled @endif">
                            @csrf
                            <h2 class="account-form__title">@lang('Become Merchant')</h2>

                            @include($activeTemplate.'partials.social_login')

                            @if (!gs('registration'))
                                <p class="form-disabled-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="80"
                                        height="80" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512"
                                        xml:space="preserve">
                                        <g>
                                            <path
                                                d="M255.999 0c-79.044 0-143.352 64.308-143.352 143.353v70.193c0 4.78 3.879 8.656 8.659 8.656h48.057a8.657 8.657 0 0 0 8.656-8.656v-70.193c0-42.998 34.981-77.98 77.979-77.98s77.979 34.982 77.979 77.98v70.193c0 4.78 3.88 8.656 8.661 8.656h48.057a8.657 8.657 0 0 0 8.656-8.656v-70.193C399.352 64.308 335.044 0 255.999 0zM382.04 204.89h-30.748v-61.537c0-52.544-42.748-95.292-95.291-95.292s-95.291 42.748-95.291 95.292v61.537h-30.748v-61.537c0-69.499 56.54-126.04 126.038-126.04 69.499 0 126.04 56.541 126.04 126.04v61.537z"
                                                fill="hsl(var(--danger))" opacity="1" data-original="hsl(var(--danger))"></path>
                                            <path
                                                d="M410.63 204.89H101.371c-20.505 0-37.188 16.683-37.188 37.188v232.734c0 20.505 16.683 37.188 37.188 37.188H410.63c20.505 0 37.187-16.683 37.187-37.189V242.078c0-20.505-16.682-37.188-37.187-37.188zm19.875 269.921c0 10.96-8.916 19.876-19.875 19.876H101.371c-10.96 0-19.876-8.916-19.876-19.876V242.078c0-10.96 8.916-19.876 19.876-19.876H410.63c10.959 0 19.875 8.916 19.875 19.876v232.733z"
                                                fill="hsl(var(--danger))" opacity="1" data-original="hsl(var(--danger))"></path>
                                            <path
                                                d="M285.11 369.781c10.113-8.521 15.998-20.978 15.998-34.365 0-24.873-20.236-45.109-45.109-45.109-24.874 0-45.11 20.236-45.11 45.109 0 13.387 5.885 25.844 16 34.367l-9.731 46.362a8.66 8.66 0 0 0 8.472 10.436h60.738a8.654 8.654 0 0 0 8.47-10.434l-9.728-46.366zm-14.259-10.961a8.658 8.658 0 0 0-3.824 9.081l8.68 41.366h-39.415l8.682-41.363a8.655 8.655 0 0 0-3.824-9.081c-8.108-5.16-12.948-13.911-12.948-23.406 0-15.327 12.469-27.796 27.797-27.796 15.327 0 27.796 12.469 27.796 27.796.002 9.497-4.838 18.246-12.944 23.403z"
                                                fill="hsl(var(--danger))" opacity="1" data-original="hsl(var(--danger))"></path>
                                        </g>
                                    </svg>
                    
                                    <span>@lang('Registration is currently disabled')</span>
                                </p>
                            @endif

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="floating-label">@lang('First Name')</label>
                                        <input type="text" class="form--control" name="firstname" value="{{ old("firstname") }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="floating-label">@lang('Last Name')</label>
                                        <input type="text" class="form--control" name="lastname" value="{{ old("lastname") }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="floating-label">@lang('E-Mail Address')</label>
                                        <input type="email" class="form--control checkUser" name="email" value="{{ old("email") }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="floating-label powerZIndex">@lang('Password')</label>
                                        <div class="input--group">
                                            <input type="password" class="form--control @if (gs('secure_password')) secure-password @endif"
                                            name="password" id="password" required>
                                            <div class="password-show-hide fa-solid toggle-password fa-eye-slash" id="#password"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="floating-label">@lang('Confirm Password')</label>
                                        <div class="input--group">
                                            <input type="password" class="form--control" name="password_confirmation" id="password_confirmation" required>
                                            <div class="password-show-hide fa-solid toggle-password fa-eye-slash" id="#password_confirmation"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <x-captcha />

                            @if(gs('agree'))
                                <div class="form-group">
                                    <input type="checkbox" id="agree" @checked(old('agree')) name="agree" required>
                                    <label for="agree">@lang('I agree with')</label>
                                    <span>
                                        @foreach($policyPages as $policy) 
                                            <a href="{{ route('policy.pages',[slug($policy->data_values->title)]) }}" class="anchor-color" target="_blank">
                                                {{ __($policy->data_values->title) }}
                                            </a> @if(!$loop->last), @endif 
                                        @endforeach
                                    </span>
                                </div>
                            @endif

                            <div class="form-group">        
                                <button type="submit" class="btn btn--base w-100">@lang('Register')</button>
                            </div>
                            <div class="form-group">        
                                <div class="account-form__or">
                                    <span class="account-form__or-text">@lang('Or')</span>
                                </div>
                            </div>
                            <div class="form-group mb-0">        
                                <a href="{{ route('user.login') }}" class="btn btn--base outline w-100">@lang('Login')</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =============================== Account Section End Here ====================== -->

    <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-center mb-0">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--sm btn--base btn-sm">@lang('Login')</a>
                </div>
            </div>
        </div> 
    </div>
@endsection

@if (gs('registration'))
    @push('style')
        <style>
            .social-login-btn {
                border: 1px solid #cbc4c4;
            }

            .register-disable {
            height: 100vh;
            width: 100%;
            background-color: #fff;
            color: black;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-disable-image {
            max-width: 300px;
            width: 100%;
            margin: 0 auto 32px;
        }

        .register-disable-title {
            color: rgb(0 0 0 / 80%);
            font-size: 42px;
            margin-bottom: 18px;
            text-align: center
        }

        .register-disable-icon {
            font-size: 16px;
            background: rgb(255, 15, 15, .07);
            color: rgb(255, 15, 15, .8);
            border-radius: 3px;
            padding: 6px;
            margin-right: 4px;
        }

        .register-disable-desc {
            color: rgb(0 0 0 / 50%);
            font-size: 18px;
            max-width: 565px;
            width: 100%;
            margin: 0 auto 32px;
            text-align: center;
        }

        .register-disable-footer-link {
            color: #fff;
            background-color: #5B28FF;
            padding: 13px 24px;
            border-radius: 6px;
            text-decoration: none
        }

        .register-disable-footer-link:hover {
            background-color: #440ef4;
            color: #fff;
        }
        </style>
    @endpush

    @if (gs('secure_password'))
        @push('script-lib')
            <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
        @endpush
    @endif

    @push('script')
        <script>
            "use strict";
            (function($) {

                $('.checkUser').on('focusout', function(e) {
                    var url = '{{ route('user.checkUser') }}';
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        if (response.data != false) {
                            $('#existModalCenter').modal('show');
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush
    
@else 
    @push('style')
        <style>
            .form-disabled {
                overflow: hidden;
                position: relative;
                user-select: none;
            }

            .form-disabled::after {
                content: "";
                position: absolute;
                height: 100%;
                width: 100%;
                background-color: rgba(255, 255, 255, 0.2);
                top: 0;
                left: 0;
                backdrop-filter: blur(4px);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
                z-index: 99;
            }

            .form-disabled-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 991;
                font-size: 24px;
                height: auto;
                width: 100%;
                text-align: center;
                color: hsl(var(--dark-600));
                font-weight: 800;
                line-height: 1.2;
                user-select: auto;
            }

            @media(max-width: 767px){
                .form-disabled-text{
                    font-size: 22px;
                }
            }

            .form-disabled-text span {
                display: block;
                margin-top: 12px;
                color: hsl(var(--danger));
            }

        </style>
    @endpush
@endif
