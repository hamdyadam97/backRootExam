@extends('layouts.master-without-nav')

@section('title', 'Verify Password')

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
                                            <h5 class="text-primary"> Verify Password</h5>
                                            <p>Verify Password with {{ config('app.name') }}.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ URL::asset('/assets/images/profile-img.png') }}" alt=""
                                            class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div>
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <a href="{{ route('home') }}">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ URL::asset('/assets/images/logo.svg') }}" alt=""
                                                    class="" height="30">
                                            </span>
                                        </a>
                                    </div>
                                </div>

                                @if (session('resent'))
                                    <div class="alert alert-success" role="alert">
                                        {{ __('A fresh verification link has been sent to your email address.') }}
                                    </div>
                                @endif

                                {{ __('Before proceeding, please check your email for a verification link.') }}
                                {{ __('If you did not receive the email') }},
                                <div class="p-2">
                                    <form class="form-horizontal" method="POST"
                                        action="{{ route('verification.resend') }}">
                                        @csrf

                                        <div class="text-end">
                                            <button class="btn btn-primary w-md waves-effect waves-light"
                                                type="submit">{{ __('click here to request another') }}</button>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>Remember It ? <a href="{{ url('login') }}" class="fw-medium text-primary"> Sign In here</a>
                            </p>
                            <p>©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> {{ config('app.name') }}. Crafted with <i
                                    class="mdi mdi-heart text-danger"></i> by <a href="https://amcodr.com/"
                                    target="_blank">Amcodr IT Solutions</a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection
