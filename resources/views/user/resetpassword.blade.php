@extends('layouts.master-without-nav')

@section('title')
    @lang('create password')
@endsection

@section('body')

    <body>
    @endsection

    @section('content')
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">@lang('translation.Welcome_Back') !</h5>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ URL::asset('/assets/images/profile-img.png') }}"
                                            alt="{{ config('app.name') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="auth-logo">
                                    <div class="auth-logo-light logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <a href="{{ route('home') }}">
                                                <span class="avatar-title rounded-circle bg-light">
                                                    <img src="{{ URL::asset('/assets/images/logo-light.svg') }}"
                                                        alt="{{ config('app.name') }}" class="" height="30">
                                                </span>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="auth-logo-dark logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <a href="{{ route('home') }}">
                                                <span class="avatar-title rounded-circle bg-light">
                                                    <img src="{{ URL::asset('/assets/images/logo.svg') }}"
                                                        alt="{{ config('app.name') }}" class="" height="30">
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" method="POST" action="{{route('confirm_new_password')}}">
                                        @csrf
                                         <input type="hidden" id="password_token" name="password_token" value="{{$token}}">
                                        <div class="mb-3">
                                            <label for="userpassword">@lang('translation.Password')</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                id="userpassword" placeholder="@lang('translation.Enter_Password')">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="userpassword">@lang('translation.Confirm_Password')</label>
                                            <input id="password-confirm" type="password" name="password_confirmation"
                                                class="form-control" placeholder="@lang('translation.Enter_Confirm_Password')">
                                        </div>                                       
                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light"
                                                type="submit">@lang('Create password')</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">

                            <div>
                                
                                <p>©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> {{ config('app.name') }}. @lang('translation.Crafted_with') <i
                                        class="mdi mdi-heart text-danger"></i>
                                    @lang('translation.by') <a href="https://amcodr.com/" target="_blank">Amcodr IT Solutions</a>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->
    @endsection
