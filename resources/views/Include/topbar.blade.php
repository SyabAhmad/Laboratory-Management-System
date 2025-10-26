<div class="navbar-custom accent-primary">
    <ul class="list-unstyled topnav-menu float-right mb-0">






        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown"
                href="#" role="button" aria-haspopup="false" aria-expanded="false">
                @if (Auth::user()->profile_photo_path == null)
                    <img src="{{ asset('assets/HMS/default/user.png') }}" alt="user-image" class="rounded-circle">
                @else
                    <img src="{{ asset('assets/HMS/employees/' . Auth::user()->profile_photo_path) }}" alt="user-image"
                        class="rounded-circle">
                @endif

                <span class="pro-user-name ml-1 text-white">
                    {{ Auth::user()->name }} <i class="mdi mdi-chevron-down"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown bg-cards"
                style="border: 1px solid rgba(100,116,139,0.12);">
                <!-- item-->
                <div class="dropdown-item noti-title" style="background: var(--primary);">
                    <h6 class="m-0 text-white">
                        Welcome !
                    </h6>
                </div>

                <!-- item-->
                <a href="{{ route('user.profile') }}" class="dropdown-item notify-item" style="color: var(--text-heading); border-bottom: 1px solid rgba(100,116,139,0.06);">
                    <i class="dripicons-user text-primary-custom"></i>
                    <span>My Account</span>
                </a>

                <a href="javascript:void(0);" class="dropdown-item notify-item" data-toggle="modal"
                    data-target="#attendance" style="color: var(--text-heading); border-bottom: 1px solid rgba(100,116,139,0.06);">
                    <i class="dripicons-user text-primary-custom"></i>
                    <span>Attendance</span>
                </a>

                <a href="javascript:void(0);" class="dropdown-item notify-item" data-toggle="modal"
                    data-target="#dailyactivities" style="color: var(--text-heading); border-bottom: 1px solid rgba(100,116,139,0.06);">
                    <i class="dripicons-user text-primary-custom"></i>
                    <span>Daily Activity</span>
                </a>

                <a href="javascript:void(0);" class="dropdown-item notify-item" data-toggle="modal"
                    data-target="#support" style="color: var(--text-heading); border-bottom: 1px solid rgba(100,116,139,0.06);">
                    <i class="dripicons-help text-primary-custom"></i>
                    <span>Support</span>
                </a>

                <div class="dropdown-divider" style="background-color: rgba(100,116,139,0.12);"></div>

                <!-- item-->
                <a href="{{ route('logout') }}" class="dropdown-item notify-item"
                    onclick="event.preventDefault();
                document.getElementById('logout-form').submit();"
                    style="color: var(--danger);">
                    <i class="dripicons-power text-danger"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>

            </div>
        </li>

    </ul>

    <style>
        body.enlarged .logo-lg {
            display: none !important;
        }
        body.enlarged .logo-sm {
            display: inline-block !important;
        }
        .logo-sm {
            display: none;
        }
    </style>
    <ul class="list-unstyled menu-left mb-0">
        <li class="float-left">
            <a href="/" class="logo" style="display: flex; align-items: center; gap: 12px; padding: 10px 15px;">
                <span class="logo-lg" style="display: flex; align-items: center; gap: 12px;">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Bacha Lab Logo" height="50"
                        style="border-radius: 50%; background: white; padding: 2px;">
                    <div style="display: flex; flex-direction: column; justify-content: center;">
                        <span style="font-size: 20px; font-weight: 900; color: var(--bg-cards); letter-spacing: 1px; line-height: 1;">BACHA
                            KHAN</span>

                    </div>
                </span>

                <span class="logo-sm">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Bacha Lab Logo" height="32"
                        style="border-radius: 50%; background: white; padding: 2px;">
                </span>
            </a>
        </li>
        <li class="float-left">
            <a class="button-menu-mobile navbar-toggle">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </a>
        </li>
        <li class="app-search d-none d-md-block">
            <form>
                <input type="text" placeholder="Search..." class="form-control bg-surface text-body">
                <button type="submit" class="sr-only"></button>
            </form>
        </li>
    </ul>
</div>


<!-- Attendance Modal -->
<div class="modal fade" id="attendance" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-cards">
            <div class="modal-header" style="background: var(--primary); color: var(--surface);">
                <h4 class="modal-title" id="exampleModalLabel">Daily Attendance</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: var(--surface);">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                    date_default_timezone_set('Asia/Dhaka');
                    $date = date('d/m/Y');
                    $time = date('H:i:s');
                    $items = DB::table('attendances')
                        ->where('user_id', Auth::user()->id)
                        ->get();

                    $items2 = DB::table('attendances')
                        ->where('user_id', Auth::user()->id)
                        ->get()
                        ->count();

                    $items3 = DB::table('attendances')
                        ->where('user_id', Auth::user()->id)
                        ->where('enter_date', $date)
                        ->get()
                        ->count();

                    $items4 = DB::table('attendances')
                        ->where('user_id', Auth::user()->id)
                        ->where('enter_date', $date)
                        ->latest()
                        ->first();
                    // ->get()
                @endphp
                @if ($items2 == 0)
                    <div class="alert" role="alert" style="background:var(--primary); color:var(--surface); border-color:rgba(37,99,235,0.9);">
                        Welcome to the system. Please take your <strong>attendance</strong>.
                    </div>
                    <form role="form" class="parsley-examples" id="AttendanceForm" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input value="{{ Auth::user()->id }}" name="user_id" style="display: none">
                        <input id="entry_date" value="{{ $date }}" name="entry_date" style="display: none">
                        <input id="entry_time" value="{{ $time }}" name="entry_time" style="display: none">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1 btn-primary-custom">
                                Entry
                            </button>
                        </div>
                    </form>
                @elseif ($items2 > 0 && $items3 == 0)
                    <div class="alert" role="alert" style="background:var(--primary); color:var(--surface); border-color:rgba(37,99,235,0.9);">
                        Welcome to the system. Please take your <strong>attendance</strong>.
                    </div>
                    <form role="form" class="parsley-examples" id="AttendanceForm" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input value="{{ Auth::user()->id }}" name="user_id" style="display: none">
                        <input id="entry_date" value="{{ $date }}" name="entry_date" style="display: none">
                        <input id="entry_time" value="{{ $time }}" name="entry_time" style="display: none">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1 btn-primary-custom">
                                Entry
                            </button>
                        </div>
                    </form>
                @elseif($items3 == 1 && $items4->exit_date == null)
                    <div class="alert" role="alert" style="background:var(--warning); color:var(--surface); border-color:rgba(245,158,11,0.9);">
                        We Recoard Your Enter Time At <strong>{{ $items4->enter_date }}
                            ({{ $items4->enter_time }})</strong>
                    </div>
                    <form role="form" class="parsley-examples" id="ExitForm" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="text" value="{{ $items4->id }}" id="id" style="display:none">
                        <input value="{{ Auth::user()->id }}" id="user_id_" name="user_id_" style="display: none">
                        <input id="exit_date" value="{{ $date }}" name="exit_date" style="display: none">
                        <input id="exit_time" value="{{ $time }}" name="exit_time" style="display: none">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" style="background:var(--warning); border-color:rgba(230,126,34,0.9); color:var(--surface);">
                                Exit
                            </button>
                        </div>
                    </form>
                @elseif($items4->exit_date != null)
                    <div class="alert" role="alert" style="background:var(--success); color:var(--surface); border-color:rgba(16,185,129,0.9);">
                        We Recoard Your Exit Time At <strong>{{ $items4->exit_date }}
                            ({{ $items4->exit_time }})</strong>
                    </div>
                @endif


            </div>
        </div>
    </div>
</div>

<!-- Activites Modal -->
<div class="modal fade" id="dailyactivities" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-cards">
            <div class="modal-header" style="background:var(--primary); color:var(--surface);">
                <h4 class="modal-title" id="exampleModalLabel">Daily Activities</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: var(--surface);">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                    date_default_timezone_set('Asia/Dhaka');
                    $date = date('d/m/Y');

                    $items = DB::table('attendances')
                        ->where('user_id', Auth::user()->id)
                        ->get();

                    $items2 = DB::table('dailyactivities')
                        ->where('user_id', Auth::user()->id)
                        ->get()
                        ->count();

                    $items3 = DB::table('dailyactivities')
                        ->where('user_id', Auth::user()->id)
                        ->where('date', $date)
                        ->get()
                        ->count();

                    $items4 = DB::table('dailyactivities')
                        ->where('user_id', Auth::user()->id)
                        ->where('date', $date)
                        ->latest()
                        ->first();
                    // ->get()
                @endphp
                @if ($items2 == 0)
                    <div class="alert" role="alert" style="background:var(--primary); color:var(--surface); border-color:rgba(37,99,235,0.9);">
                        Please take your <strong>Record Your Daily Activities</strong>.
                    </div>
                    <form role="form" class="parsley-examples" id="ActivitiesForm" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input value="{{ Auth::user()->id }}" name="user_id" style="display: none">
                        <input id="date" value="{{ $date }}" name="date" style="display: none">

                        <div class="form-group row">
                            <label for="activity" class="col-sm-2 col-form-label">Daily Activity<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea id="activity" name="activity" class="activity" required></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1 btn-primary-custom">
                                Save
                            </button>
                        </div>
                    </form>
                @elseif ($items2 > 0 && $items3 == 0)
                    <div class="alert" role="alert" style="background:var(--primary); color:var(--surface); border-color:rgba(37,99,235,0.9);">
                        Please take your <strong>Record Your Daily Activities</strong>.
                    </div>
                    <form role="form" class="parsley-examples" id="ActivitiesForm" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input value="{{ Auth::user()->id }}" name="user_id" style="display: none">
                        <input id="date" value="{{ $date }}" name="date" style="display: none">

                        <div class="form-group row">
                            <label for="activity" class="col-sm-2 col-form-label">Daily Activity<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea id="activity" name="activity" class="activity" required></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1 btn-primary-custom">
                                Save
                            </button>
                        </div>
                    </form>
                @elseif($items3 == 1)
                    <div class="alert" role="alert" style="background:var(--warning); color:var(--surface); border-color:rgba(245,158,11,0.9);">
                        Your Today Activities are <strong>Recoded If You Want You Can Change !!</strong>
                    </div>
                    <form role="form" class="parsley-examples" id="updateActivitiesForm" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="text" value="{{ $items4->id }}" id="ac_id" name="ac_id"
                            style="display:none">
                        <input value="{{ Auth::user()->id }}" name="user_id" style="display: none">

                        <div class="form-group row">
                            <label for="activity_" class="col-sm-2 col-form-label">Daily Activity<span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea id="activity_" name="activity_" class="activity" required>{!! $items4->activity !!}</textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" style="background:var(--warning); border-color:rgba(230,126,34,0.9); color:var(--surface);">
                                Update
                            </button>
                        </div>
                    </form>
                @endif


            </div>
        </div>
    </div>
</div>


<!-- Activites Modal -->
<div class="modal fade" id="support" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-cards">
            <div class="modal-header" style="background:var(--primary); color: var(--surface);">
                <h4 class="modal-title" id="exampleModalLabel">Support Desk</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: var(--surface);">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert" role="alert" style="background:var(--primary); color:var(--surface); border-color:rgba(37,99,235,0.9);">
                    <p class="text-center font-weight-bold">If You Face Any Error Please Inform Us</p>
                </div>

                <h4>Email: iamtalhakhn@gmail.com</h4>

            </div>
        </div>
    </div>
</div>

<script>
    $('#AttendanceForm').on('submit', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var myformData = new FormData($('#AttendanceForm')[0]);
        $.ajax({
            type: "post",
            url: "/Attendance/add",
            data: myformData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                console.log(response);
                $("#AttendanceForm").find('input').val('');
                $('#attendance').modal('hide');
                // $('#medicineaddform')[0].reset();
                Swal.fire({
                    position: 'top-mid',
                    icon: 'success',
                    title: 'Your work has been saved',
                    showConfirmButton: false,
                    timerProgressBar: true,
                    timer: 1800
                });
                // table.draw();
                location.reload();
            },
            error: function(error) {
                console.log(error);
                alert("Data Not Save");
            }
        });
    });

    $('#ExitForm').submit(function(e) {
        e.preventDefault();

        let id = $('#id').val();
        // let user_id_ = $('#user_id_').val();
        let exit_date = $('#exit_date').val();
        let exit_time = $('#exit_time').val();
        let _token = $('input[name=_token]').val();

        $.ajax({
            type: "PUT",
            url: "/Attendance/update",
            data: {
                id: id,
                // user_id_: user_id_,
                exit_date: exit_date,
                exit_time: exit_time,
                _token: _token,
            },
            dataType: "json",
            success: function(response) {
                Swal.fire({
                    position: 'top-mid',
                    icon: 'success',
                    title: 'Update Successfull',
                    showConfirmButton: false,
                    timerProgressBar: true,
                    timer: 1800
                });
                location.reload();
                $('#ExitForm')[0].reset();

            },
            error: function(data) {
                Swal.fire({
                    title: 'Alert!',
                    text: 'Something Wrong',
                    icon: 'warning',
                    showConfirmButton: false,
                });
                // console.log('Error:', data);
            }
        });

    });


    $('#ActivitiesForm').on('submit', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var myformData = new FormData($('#ActivitiesForm')[0]);
        $.ajax({
            type: "post",
            url: "/activities/add",
            data: myformData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                console.log(response);
                $("#ActivitiesForm").find('input').val('');
                $('#dailyactivities').modal('hide');
                // $('#medicineaddform')[0].reset();
                Swal.fire({
                    position: 'top-mid',
                    icon: 'success',
                    title: 'Your work has been saved',
                    showConfirmButton: false,
                    timerProgressBar: true,
                    timer: 1800
                });
                // table.draw();
                location.reload();
            },
            error: function(error) {
                console.log(error);
                alert("Data Not Save");
            }
        });
    });

    $('#updateActivitiesForm').submit(function(e) {
        e.preventDefault();

        let ac_id = $('#ac_id').val();
        let activity_ = $('#activity_').val();
        let _token = $('input[name=_token]').val();

        $.ajax({
            type: "PUT",
            url: "/activities/update",
            data: {
                ac_id: ac_id,
                // user_id_: user_id_,
                activity_: activity_,
                _token: _token,
            },
            dataType: "json",
            success: function(response) {
                Swal.fire({
                    position: 'top-mid',
                    icon: 'success',
                    title: 'Update Successfull',
                    showConfirmButton: false,
                    timerProgressBar: true,
                    timer: 1800
                });
                location.reload();
                $('#updateActivitiesForm')[0].reset();

            },
            error: function(data) {
                Swal.fire({
                    title: 'Alert!',
                    text: 'Something Wrong',
                    icon: 'warning',
                    showConfirmButton: false,
                });
                // console.log('Error:', data);
            }
        });

    });
</script>

<script>
    $(document).ready(function() {
        $('.activity').summernote({
            height: 300,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['height', ['height']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ]
        });
    });
</script>
