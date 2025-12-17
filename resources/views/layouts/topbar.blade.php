<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('home') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('/assets/images/dummy_logo.png') }}" alt="" height="15">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('/assets/images/dummy_logo.png') }}" alt="" height="50">
                    </span>
                </a>

                <a href="{{ route('home') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('/assets/images/dummy_logo.png') }}" alt="" height="15">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('/assets/images/dummy_logo.png') }}" alt="" height="50">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            <div class="dark-light-switch d-flex align-items-center">
                <label for="theme" class="theme m-0">
                    {{-- <span>Light</span> --}}
                    <span class="theme__toggle-wrap">
                        <input type="checkbox" name="theme" id="theme" class="theme__toggle" role="switch"
                            value="dark" x-model="dark">
                        <span class="theme__fill"></span>
                        <span class="theme__icon">
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                        </span>
                    </span>
                    {{-- <span>Dark</span> --}}
                </label>
            </div>
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @php
                        $login_user = Auth::user();
                        if (isset($login_user) && !empty($login_user) && $login_user->role_type == 2 && !empty($login_user->company_logo) && file_exists(public_path() . '/storage/company_logo/' . $login_user->company_logo)) {
                            $image_path = asset('/storage/company_logo/' . $login_user->company_logo);
                        } else {
                            $image_path = asset('/assets/images/avatar.png');
                        }
                    @endphp
                    <img class="rounded-circle header-profile-user"
                        src="{{ isset($image_path) ? $image_path : asset('/assets/images/avatar.png') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1">{{ ucfirst(Auth::user()->first_name) }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item d-block change-password-modalbtn" href="javascript:void(0)"><i
                            class="bx bx-wrench font-size-16 align-middle me-1"></i>
                        <span>@lang('translation.Settings')</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                        <span>@lang('translation.Logout')</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
           {{--  <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="bx bx-cog bx-spin"></i>
                </button>
            </div> --}}
        </div>
    </div>
</header>

{{-- <!-- Right Sidebar -->
<div class="right-bar">
    <div data-simplebar class="h-100">
        <div class="rightbar-title d-flex align-items-center px-3 py-4">

            <h5 class="m-0 me-2">Settings</h5>

            <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                <i class="mdi mdi-close noti-icon"></i>
            </a>
        </div>

        <!-- Settings -->
        <hr class="mt-0" />
        <h6 class="text-center mb-0">Choose Layouts</h6>

        <div class="p-4">
            <div class="mb-2">
                <img src="{{asset('/')}}assets/images/layouts/layout-1.jpg" class="img-thumbnail" alt="layout images">
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input theme-choice" type="checkbox" id="light-mode-switch" checked>
                <label class="form-check-label" for="light-mode-switch">Light Mode</label>
            </div>

            <div class="mb-2">
                <img src="{{asset('/')}}assets/images/layouts/layout-2.jpg" class="img-thumbnail" alt="layout images">
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input theme-choice" type="checkbox" id="dark-mode-switch">
                <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
            </div>

            <div class="mb-2">
                <img src="{{asset('/')}}assets/images/layouts/layout-3.jpg" class="img-thumbnail" alt="layout images">
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input theme-choice" type="checkbox" id="rtl-mode-switch">
                <label class="form-check-label" for="rtl-mode-switch">RTL Mode</label>
            </div>

            <div class="mb-2">
                <img src="{{asset('/')}}assets/images/layouts/layout-4.jpg" class="img-thumbnail" alt="layout images">
            </div>
            <div class="form-check form-switch mb-5">
                <input class="form-check-input theme-choice" type="checkbox" id="dark-rtl-mode-switch">
                <label class="form-check-label" for="dark-rtl-mode-switch">Dark RTL Mode</label>
            </div>
        </div>

    </div> <!-- end slimscroll-menu-->
</div>
<!-- /Right-bar --> --}}

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<!--  Change-Password example -->
<div class="modal fade change-password" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">@lang('translation.Change_Password')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="change-password" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" value="{{ Auth::user()->id }}" id="data_id">
                    <div class="mb-3">
                        <label for="current_password">@lang('translation.Current_Password')</label>
                        <input id="current-password" type="password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            name="current_password" autocomplete="current_password" placeholder="@lang('translation.Enter_Current_Password')"
                            value="{{ old('current_password') }}">
                        <span class="invalid-feedback" id="current_passwordError"
                            data-ajax-feedback="current_password" role="alert"></span>
                        {{-- </div> --}}
                    </div>

                    <div class="mb-3">
                        <label for="newpassword">@lang('translation.New_Password')</label>
                        <input id="spassword" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password"
                            autocomplete="new_password" placeholder="@lang('translation.Enter_New_Password')">
                        <div class="text-danger invalid-feedback" id="passwordError" data-ajax-feedback="password">
                        </div>
                        <span class="invalid-feedback" id="spasswordError" data-ajax-feedback="password"
                            role="alert"></span>
                    </div>

                    <div class="mb-3">
                        <label for="userpassword">@lang('translation.Confirm_Password')</label>
                        <input id="spassword-confirm" type="password" class="form-control"
                            name="password_confirmation" autocomplete="new_password" placeholder="@lang('translation.Enter_New_Confirm_password')">
                        <span class="invalid-feedback" id="spassword_confirmError"
                            data-ajax-feedback="password_confirmation" role="alert"></span>
                    </div>

                    <div class="mt-3 d-grid">
                        <button class="btn btn-primary waves-effect waves-light UpdatePassword"
                            data-id="{{ Auth::user()->id }}" type="submit">@lang('translation.Update_Password')</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
