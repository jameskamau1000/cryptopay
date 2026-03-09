@extends($activeTemplate.'layouts.frontend')
@php
    $contact = @getContent('contact_us.content', true)->data_values;
@endphp

@section('content')
<!-- ==================== Contact Start Here ==================== -->
<section class="contact pt-120 pb-60">
    <div class="container">
        <div class="contact-box">
            <div class="row gy-4 align-items-center">
                <div class="col-md-5">
                    <div class="contact-item">
                        <div class="contact-item__icon">
                            <img src="{{ asset($activeTemplateTrue.'frontend/images/icons/contact-icon1.png') }}" alt="">
                        </div>
                        <div class="contact-item__content">
                            <h5 class="contact-item__title">@lang('Phone Number')</h5>
                            <p class="contact-item__desc">{{ @$contact->phone }}</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-item__icon">
                            <img src="{{ asset($activeTemplateTrue.'frontend/images/icons/contact-icon2.png') }}" alt="">
                        </div>
                        <div class="contact-item__content">
                            <h5 class="contact-item__title">@lang('Email Address')</h5>
                            <p class="contact-item__desc">{{ @$contact->email }}</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-item__icon">
                            <img src="{{ asset($activeTemplateTrue.'frontend/images/icons/contact-icon3.png') }}" alt="">
                        </div>
                        <div class="contact-item__content">
                            <h5 class="contact-item__title">@lang('Office Address')</h5>
                            <p class="contact-item__desc">{{ @$contact->office_address }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="contact-thumb">
                        <img src="{{ getImage('assets/images/frontend/contact_us/' .@$contact->image, '680x585') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ==================== Contact End Here ==================== -->

<!-- ==================== Contact Form & Map Start Here ==================== -->
 <section class="contact-bottom pt-60 pb-120 position-relative">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-5 col-md-6">
                <div class="contactus-form">
                    <h2 class="contactus-form__title">@lang('Contact With Us')</h2>
                    <form method="post" action="" class="verify-gcaptcha" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label">@lang('Name')</label>
                                    <input name="name" type="text" class="form--control" value="@if(auth()->user()){{ auth()->user()->fullname }} @else{{ old('name') }}@endif" @if(auth()->user()) readonly @endif required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label" for="email">@lang('Email Address')</label>
                                    <input name="email" type="email" class="form--control" value="@if(auth()->user()){{ auth()->user()->email }}@else{{  old('email') }}@endif" @if(auth()->user()) readonly @endif required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="floating-label" for="email">@lang('Subject')</label>
                                    <input name="subject" type="text" class="form--control" value="{{old('subject')}}" required>
                                </div>
                            </div>
                            <div class="col-sm-12"> 
                                <div class="form-group">
                                    <label class="floating-label" for="message">@lang('Your Message')</label>
                                    <textarea name="message" class="form--control" required>{{old('message')}}</textarea>
                                </div>
                            </div>

                            <x-captcha />

                            <div class="col-sm-12">
                                <div class="form-group mb-0">        
                                    <button class=" btn btn--primary w-100">@lang('Send Message') 
                                        <span class="button__icon ms-1"><i class="fas fa-paper-plane"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>  
            <div class="col-lg-7 col-md-6 pe-lg-5">
                <div class="contact-map">
                    <img src="{{ getImage('assets/images/frontend/contact_us/' .@$contact->map_image, '885x535') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ==================== Contact Form & Map End Here ==================== -->

@if(@$sections->secs != null)
    @foreach(json_decode($sections->secs) as $sec)
        @include($activeTemplate.'sections.'.$sec)
    @endforeach
@endif
@endsection
