@extends('layouts.master')
@section('title')
    @lang('User')
@endsection
@push('admin_css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
          type="text/css"/>
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>

    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/css/intlTelInput.css">
    <style>
        .iti.iti--allow-dropdown {
            display: block;
        }

        span#error-msg {
            color: #F00;
            font-size: 11px;
            position: absolute;
            top: 51px;
        }

    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('All User')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                                class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="f_name_filter" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="f_name_filter"
                                       name="f_name">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="l_name_filter" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="l_name_filter"
                                       name="l_name">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="mobile_filter" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="mobile_filter"
                                       name="mobile">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status_filter" class="form-label">Status</label>
                                <select name="status_filter" id="status_filter" class="form-select status_filter">
                                    <option value=" ">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" data-simplebar>
                        <table id="usertable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                            <tr>
                                <th>@lang('First name')</th>
                                <th>@lang('Last name')</th>
                                <th>@lang('Image')</th>
                                <th>@lang('Role')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Actions')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- Page Models -->
    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span
                            class="modal-lable-class">@lang('translation.Add')</span> @lang('User')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-form" method="post" class="form-horizontal" action="{{ route('user.addupdate') }}">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input id="first_name" type="text" class="form-control first_name" name="first_name"
                                           value="{{ old('first_name') }}">
                                    <span class="invalid-feedback" id="first_nameError" data-ajax-feedback="first_name"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input id="last_name" type="text" class="form-control last_name" name="last_name"
                                           value="{{ old('last_name') }}">
                                    <span class="invalid-feedback" id="last_nameError" data-ajax-feedback="last_name"
                                          role="alert"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="score" class="form-label">Score</label>
                                    <input id="score" type="text" class="form-control score" name="score"
                                           value="{{ old('score') }}">
                                    <span class="invalid-feedback" id="scoreError" data-ajax-feedback="score"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Type</label>
                                    <select name="role_type" id="role_type" class="form-select role_type">
                                        <option value="">Select Type</option>
                                        <option value="1">Admin</option>
                                        <option value="2">User</option>
                                    </select>
                                    <span class="invalid-feedback" id="role_typeError" data-ajax-feedback="role_type"
                                          role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label>Mobile</label>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="tel-input-container">
                                                <div class="inputBox">
                                                    <div class="inputBox-icon">
                                                        <i class="icon icon-call"></i>
                                                    </div>
                                                    <input type="text"
                                                           class="form-control custom-input phone-input-wrapper phone-input"
                                                           id="phoneNumber"
                                                           dir="ltr" name="mobile_number">
                                                    <input type="hidden" name="dial_code">
                                                    <input type="hidden" name="mobile_country_code">
                                                    <span class="line"></span>
                                                </div>
                                                <span id="error-msg" class="hide" style=""></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            {{--                        <div class="col-md-6">--}}
                            {{--                            <div class="mb-3">--}}
                            {{--                                <label for="mobile" class="form-label">Mobile</label>--}}
                            {{--                                <input id="mobile" type="text" class="form-control mobile" name="mobile" value="{{ old('mobile') }}">--}}
                            {{--                                <span class="invalid-feedback" id="mobileError" data-ajax-feedback="mobile" role="alert"></span>--}}
                            {{--                            </div>--}}
                            {{--                        </div>--}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="token" class="form-label">Token</label>
                                    <input id="token" type="text" class="form-control token" name="token"
                                           value="{{ old('token') }}">
                                    <span class="invalid-feedback" id="tokenError" data-ajax-feedback="token"
                                          role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="device_id" class="form-label">Device ID</label>
                                    <input id="device_id" type="text" class="form-control device_id" name="device_id"
                                           value="{{ old('device_id') }}">
                                    <span class="invalid-feedback" id="device_idError" data-ajax-feedback="device_id"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status_type" class="form-select status">
                                        <option value="1">Active</option>
                                        <option value="0">Deactive</option>
                                    </select>
                                    <span class="invalid-feedback" id="statusError" data-ajax-feedback="status"
                                          role="alert"></span>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="email" class="control-label">Email:</label>
                                    <input type="text" class="form-control" id="email" name="email">
                                    <span class="invalid-feedback" id="emailError" data-ajax-feedback="email"
                                    role="alert"></span>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row" id="password_group">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="control-label">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <span class="invalid-feedback" id="passwordError" data-ajax-feedback="password"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="control-label">Confirm Password:</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                           name="password_confirmation">
                                    <span class="invalid-feedback" id="password_confirmationError"
                                          data-ajax-feedback="password_confirmation" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-6">
        <div class="mb-3">
            <label for="governorate" class="form-label">governorate</label>
            <input
                id="governorate"
                type="text"
                class="form-control country"
                name="governorate"
                value="{{ old('governorate') }}"
            >
            <span class="invalid-feedback" id="governorateError"
                  data-ajax-feedback="governorate" role="alert"></span>
        </div>
   
</div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="specialization" class="form-label">Specialization</label>
            <input
                id="specialization"
                type="text"
                class="form-control specialization"
                name="specialization"
                value="{{ old('specialization') }}"
            >
            <span class="invalid-feedback" id="specializationError"
                  data-ajax-feedback="specialization" role="alert"></span>
        </div>
    </div>

                        <div class="row">
                            <div class="col-md-6">
        <div class="mb-3">
            <label for="birth_date" class="form-label">Birth Date</label>
            <input
                id="birth_date"
                type="date"
                class="form-control birth_date"
                name="birth_date"
                max="1999-12-31"
            >
            <span class="invalid-feedback" id="birth_dateError"
                  data-ajax-feedback="birth_date" role="alert"></span>
        </div>
    </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="thumb" class="form-label">Image</label>
                                    <input id="thumb" type="file" class="form-control thumb" name="thumb" title="thumb">
                                    <span class="invalid-feedback" id="thumbError" data-ajax-feedback="thumb"
                                          role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <div class="view-image" style="display: none;">
                                        <img src="" class="user_img" height="50px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                aria-label="Close">@lang('translation.Close')</button>
                        <button type="submit"
                                class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Inputmask js -->
    {{-- <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script> --}}
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('user.list') }}";
        var detailUrl = "{{ route('user.detail') }}";
        var deleteUrl = "{{ route('user.delete') }}";
        var verifyUrl = "{{ route('user.verify') }}";
        var addUrl = $('#add-form').attr('action');
        var imgpath = "{{asset('storage/user_image')}}/";

    </script>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/intlTelInput.min.js"></script>
    <script>
        initializeTelInput(".phone-input", true);
        var iti;
        function initializeTelInput(input_identifier, form_has_countries_select = false) {
            var input = document.querySelector(input_identifier);
            if (input) {
                iti = window.intlTelInput(input, {
                    initialCountry: "{{ @$item->mobile_country_code??"auto" }}",
                    geoIpLookup: function (success, failure) {
                        $.get("https://ipinfo.io", function () {
                        }, "jsonp").always(function (resp) {
                            var countryCode = (resp && resp.country) ? resp.country : "us";
                            success(countryCode);

                            if (form_has_countries_select) {
                                $(`select option[data-code=${countryCode.toLowerCase()}]`).attr('selected', true);
                            }
                        });
                    },
                    nationalMode: false,
                    autoHideDialCode: true,
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js",
                });

                input.addEventListener("countrychange", function () {
                    $(input).closest('.inputBox').find('input[name=dial_code]').val(iti.getSelectedCountryData().dialCode)
                    $(input).closest('.inputBox').find('input[name=mobile_country_code]').val(iti.getSelectedCountryData().iso2)
                });

                input.addEventListener('keyup', function () {
                    var errorMsg = $(input).closest('.tel-input-container').find('#error-msg');
                    reset(input, errorMsg);
                    if (input.value.trim()) {
                        if (iti.isValidNumber()) {
                            $(errorMsg).removeClass("hide");
                        } else {
                            $(input).addClass("error");
                            var errorCode = iti.getValidationError();
                            if (errorCode == -99) {
                                errorCode = 5;
                            }
                            $(errorMsg).text(errorMap[errorCode]);
                            $(errorMsg).removeClass("hide");
                        }
                    }
                });
            }
            if (form_has_countries_select) {
                $(document).on('change', 'select[name=country_id]', function () {
                    var code = $(this).find('option:selected').attr('data-code');
                    iti.setCountry(code)
                })
            }
        }

        var errorMap;
        if (window.locale == "ar") {
            errorMap = [
                "رقم الجوال لايتوافق مع الدولة المختارة",
                "رمز الدولة غير صالح",
                "رقم الجوال قصير جداً بالنسبة الدولة المختارة",
                "رقم الجوال طويل جداً بالنسبة الدولة المختارة",
                "رقم الجوال لايتوافق مع الدولة المختارة",
                "صيغة رقم الهاتف خاطئة",
            ];
        } else {
            errorMap = [
                "Invalid number",
                "Invalid country code",
                "Too short",
                "Too long",
                "Invalid number",
                "Wrong phone number format",
            ];
        }

        var reset = function (input, errorMsg) {
            $(input).removeClass("error");
            $(errorMsg).html("");
            $(errorMsg).addClass("hide");
            $("button[type=submit]").prop("disabled", false);
        };
    </script>

@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('user.js') }}"></script>
@endsection
