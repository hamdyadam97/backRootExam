<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu-test">
                <li class="">
                    <a href="{{ route('home') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span>@lang('translation.Dashboard')</span>
                    </a>
                </li>
                @if(Auth::user()->isAdmin())
                <li class="">
                    <a href="{{ route('user') }}" class="waves-effect">
                        <i class="bx bx-group"></i>
                        <span>User</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('instructors.index') }}" class="waves-effect">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Instructors</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('category') }}" class="waves-effect">
                        <i class="bx bx-purchase-tag"></i>
                        <span>Category</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('subcategory') }}" class="waves-effect">
                        <i class="bx bx-purchase-tag"></i>
                        <span>SubCategory</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('sub-subcategory') }}" class="waves-effect">
                        <i class="bx bx-purchase-tag"></i>
                        <span>Sub-SubCategory</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('topics.index') }}" class="waves-effect">
                        <i class="bx bx-purchase-tag"></i>
                        <span>Topics</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('exam_section.index') }}" class="waves-effect">
                        <i class="bx bx-purchase-tag"></i>
                        <span>Exam Section</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('package') }}" class="waves-effect">
                        <i class="bx bx-package"></i>
                        <span>Packages</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('userpackage') }}" class="waves-effect">
                        <i class="bx bx-package"></i>
                        <span>User package</span>
                    </a>
                </li>
                   <li class="">
                    <a href="{{ route('billing.invoices') }}" class="waves-effect">
                        <i class="bx bx-package"></i>
                        <span> Bill package</span>
                    </a>
                </li>
{{--                <li class="">--}}
{{--                    <a href="{{ route('exam') }}" class="waves-effect">--}}
{{--                        <i class="bx bx-book-content"></i>--}}
{{--                        <span>Exam</span>--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li class="">
                    <a href="{{ route('notification') }}" class="waves-effect">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                        <span>Notification</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('question') }}" class="waves-effect">
                        <i class="bx bx-receipt"></i>
                        <span>Question</span>
                    </a>
                </li>
                {{-- <li class="">
                    <a href="{{ route('questionanswer') }}" class="waves-effect">
                        <i class="bx bx-receipt"></i>
                        <span>Question answer</span>
                    </a>
                </li> --}}
{{--                <li class="">--}}
{{--                    <a href="{{ route('examquestion') }}" class="waves-effect">--}}
{{--                        <i class="bx bx-book-content"></i>--}}
{{--                        <span>Exam question</span>--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li class="">
                    <a href="{{ route('userexam') }}" class="waves-effect">
                        <i class="bx bx-book-content"></i>
                        <span>User exam</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('discountscode') }}" class="waves-effect">
                        <i class="bx bxs-discount"></i>
                        <span>Discounts Code</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('payment-types') }}" class="waves-effect">
                        <i class="bx bxs-credit-card"></i>
                        <span>Payment Types</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('appinfo') }}" class="waves-effect">
                        <i class="bx bx-info-circle"></i>
                        <span>App info</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('blogs.index') }}" class="waves-effect">
                        <i class="bx bxl-blogger"></i>
                        <span>Blogs</span>
                    </a>
                </li>
                @endif


                <li class="">
                    <a href="{{ route('lab_value.create') }}" class="waves-effect">
                        <i class='bx bx-cog'></i>
                        <span>Lab value</span>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('categories_export.export') }}" class="waves-effect">
                        <i class='bx bx-export'></i>
                        <span>Categories Export</span>
                    </a>
                </li>

                <li>
                    <a href="javascript:void(0)" class="waves-effect"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bx bx-log-out"></i>
                        <span class="hide-menu">@lang('translation.Logout')</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
