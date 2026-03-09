@extends($activeTemplate.'layouts.app')

@php
    $login = @getContent('login_register.content', true)->data_values;
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
                    <img src="{{ getImage('assets/images/frontend/login_register/' .@$login->image, '615x620') }}" alt="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="account-form"> 
                    <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
                        @csrf
                        <h2 class="account-form__title">@lang('Login Your Account')</h2>

                        @include($activeTemplate.'partials.social_login')

                        <div class="form-group">
                            <label class="floating-label" for="email">@lang('Username or Email')</label>
                            <input type="text" name="username" value="{{ old('username') }}" class="form--control" required>
                        </div>
                        <div class="form-group">
                            <label class="floating-label" for="your-password">@lang('Password')</label>
                            <div class="input--group">
                                <input id="password" type="password" class="form--control" name="password" required>
                                <div class="password-show-hide fa-solid toggle-password fa-eye-slash" id="#password"></div>
                            </div>
                        </div>

                        <x-captcha />

                        <div class="form-group custom--checkbox d-flex justify-content-between flex-wrap">
                            <div>
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">@lang('Remember Me')</label>
                            </div>
                            <a href="{{ route('user.password.request') }}" class="anchor-color">@lang('Forgot Password')?</a>
                        </div>

                        <div class="form-group">        
                            <button type="submit" id="recaptcha" class="btn btn--base w-100">@lang('Login')</button>
                        </div>
                        <div class="form-group">        
                            <div class="account-form__or">
                                <span class="account-form__or-text">@lang('Or')</span>
                            </div>
                        </div>
                        <div class="form-group mb-0">        
                            <a href="{{ route('user.register') }}" class="btn btn--base outline w-100">@lang('Become a Merchant')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- =============================== Account Section End Here ====================== -->
@endsection
