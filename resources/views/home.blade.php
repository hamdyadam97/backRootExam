@extends('layouts.master')

@section('title')
    @lang('translation.Dashboard')
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">                                
                                <p>@lang('translation.Dashboard')</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="assets/images/profile-img.png" alt="{{ config('app.name') }}" class="img-fluid">
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection
