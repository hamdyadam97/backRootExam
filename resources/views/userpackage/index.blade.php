@extends('layouts.master')
@section('title')
    @lang('User package')
@endsection
@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
          type="text/css" />
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">

@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('All User package')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new">
                            <i class="bx bx-plus label-icon"></i>@lang('Add New')</button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="Text" class="form-label">Package</label>
                                <select name="package_id" id="package_id" class="form-control select2 package_id"
                                        placeholder="@lang('Package')">
                                    <option value="">Select Package</option>
                                    @foreach (\App\Models\Packges::orderBy('status','desc')->get() as $package)
                                        <option
                                            value="{{ $package->id }}" {{ $package->id == request()->package_id ? 'selected':'' }}>#{{$package->id. '-'. $package->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="package_idError" data-ajax-feedback="text_question"
                                      role="alert"></span>
                            </div>
                        </div>

                        {{--                    <div class="col-md-4">--}}
                        {{--                        <div class="mb-3">--}}
                        {{--                            <label for="Text" class="form-label">Exam Section</label>--}}
                        {{--                            <select required name="exam_section_id" id="exam_section_id"--}}
                        {{--                                    class="form-control select2 exam_section_id"--}}
                        {{--                                    placeholder="@lang('Select Exam Section')">--}}
                        {{--                                <option value="">Select Exam section</option>--}}
                        {{--                                @foreach ($sections as $section)--}}
                        {{--                                    <option--}}
                        {{--                                        value="{{ $section->id }}" {{ $section->id == $session_exam_section_id ? 'selected':'' }}>{{ $section->name }}</option>--}}
                        {{--                                @endforeach--}}
                        {{--                            </select>--}}
                        {{--                            <span class="invalid-feedback" id="exam_section_idError"--}}
                        {{--                                  data-ajax-feedback="exam_section_id" role="alert"></span>--}}
                        {{--                        </div>--}}
                        {{--                    </div>--}}

                    </div>

                    <div class="table-responsive" data-simplebar>
                        <table id="userpackage" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Package</th>
                                <th>price Before Discount</th>
                                <th>price after Discount</th>
                                <th>Discount</th>
                                <th>Coupon</th>
                                <th>Start date</th>
                                <th>End date</th>

                                <th>Actions</th>
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
    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('Add')</span> @lang('User package')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-form" method="post" class="form-horizontal" action="{{ route('userpackage.addupdate') }}">
                    @csrf
                    <input type="hidden" name="id" value="0" id="edit-id">
                    <div class="modal-body">
                        <div class="row">
                            @if (isset($users) && !empty($users))
                                <div class="mb-3">
                                    <label for="User" class="form-label">User</label>
                                    <select name="user_id" id="user_id"
                                            class="form-control select2  user_id" placeholder="@lang('Select User')">
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user['id'] }}">{{ $user['full_name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="user_idError" data-ajax-feedback="user_id"
                                          role="alert"></span>
                                </div>
                            @endif
                            @if (isset($packages) && !empty($packages))
                                <div class="mb-3">
                                    <label for="Package" class="form-label">Package</label>
                                    <select name="package_id" id="package_id"
                                            class="form-control select2  package_id" placeholder="@lang('Select package')">
                                        <option value="">Select package</option>
                                        @foreach ($packages as $package)
                                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="package_idError" data-ajax-feedback="package_id"
                                          role="alert"></span>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="start_date" class="control-label">Start date</label>
                                    <div class="input-group" id="start_date_container">
                                        <input type="text" id="start_date" name="start_date" class="form-control"
                                               value="{{ old('start_date') }}"
                                               data-date-format="dd.mm.yyyy" data-date-container='#start_date_container'
                                               data-provide="datepicker" data-date-autoclose="true">
                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>

                                        <span class="invalid-feedback" id="start_dateError"
                                              data-ajax-feedback="start_date" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="end_date" class="control-label">End date</label>
                                    <div class="input-group" id="end_date_container">
                                        <input type="text" id="end_date" name="end_date" class="form-control"
                                               value="{{ old('end_date') }}"
                                               data-date-format="dd.mm.yyyy" data-date-container='#end_date_container'
                                               data-provide="datepicker" data-date-autoclose="true">
                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>

                                        <span class="invalid-feedback" id="end_dateError"
                                              data-ajax-feedback="end_date" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect waves-light" data-bs-dismiss="modal"
                                aria-label="Close">@lang('translation.Close')</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>
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

    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>

    <script>
        var apiUrl = "{{ route('userpackage.list') }}";
        var detailUrl = "{{ route('userpackage.detail') }}";
        var deleteUrl = "{{ route('userpackage.delete') }}";
        var addUrl = $('#add-form').attr('action');

    </script>
@endsection
@section('script-bottom')
    <script src="{{ addPageJsLink('userpackage.js') }}"></script>
    <script>
        $(function (){
            $('#package_id').select2()

        })
        function reloadUserPackageTable() {
            $('#userpackage').DataTable().ajax.reload(); // Keep the current page
        }

        $('#package_id').on('change',function (e){

            e.preventDefault()
            reloadUserPackageTable();

        })
    </script>
@endsection
