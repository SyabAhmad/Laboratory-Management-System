@extends('Layout.master')
@section('title', 'Patients')

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Patient</a></li>
                            <li class="breadcrumb-item active">New Patient</li>
                        </ol>
                    </div>
                    <h4 class="page-title">New Patient</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="card">
            <div class="card-body">
                <h3 class="text-center">New Patients Registration</h3>
                <h6 class="text-center text-danger">Please fill the form below to register a new patient</h6>
                <form method="POST" class="mt-5" enctype="multipart/form-data" action="{{ route('patients.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label for="name" class="col-sm-4 col-form-label">Full Name<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" required parsley-type="text" class="form-control" id="name"
                                        name="name" placeholder="Mr. Jon Rechard">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mobile_phone" class="col-sm-4 col-form-label">Mobile Number<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-7">
                                    <input type="phone" required parsley-type="text" class="form-control"
                                        id="mobile_phone" name="mobile_phone" placeholder="Mobile Number">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="gender" class="col-sm-4 col-form-label">Gender<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control" id="gender" required name="gender">
                                        <option selected disabled>Choose One Option</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="age" class="col-sm-4 col-form-label">Age<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-7">
                                    <input type="number" parsley-type="text" class="form-control" id="age"
                                        name="age">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="address" class="col-sm-4 col-form-label">Address<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-7">
                                    <textarea class="form-control" required id="address" name="address" placeholder="Address"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="blood_group" class="col-sm-4 col-form-label">Blood Group</label>
                                <div class="col-sm-7">
                                    <select class="form-control" id="blood_group" name="blood_group">
                                        <option selected disabled>Choose One Option</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="receiving_date" class="col-sm-4 col-form-label">Receiving Date<span class="text-danger">*</span></label>
                                <div class="col-sm-7">
                                    <input type="date" required class="form-control" id="receiving_date"
                                        name="receiving_date">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label for="lmp" class="col-sm-4 col-form-label">LMP</label>
                                <div class="col-sm-7">
                                    <input type="text" parsley-type="text" class="form-control" id="lmp"
                                        name="lmp">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="reporting_date" class="col-sm-4 col-form-label">Reporting Date<span class="text-danger">*</span></label>
                                <div class="col-sm-7">
                                    <input type="date" required class="form-control" id="reporting_date"
                                        name="reporting_date">
                                </div>
                            </div>

                            <div class="form-group row">
                                <!-- replaced blood pressure input with test category submission fields -->
                                <input type="hidden" name="test_category" id="test_category" value="">
                                <label class="col-sm-4 col-form-label">Selected Tests</label>
                                <div class="col-sm-7">
                                    <input type="text" readonly id="selected_tests_display" class="form-control" placeholder="No tests selected">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="referred_by" class="col-sm-4 col-form-label">Referred By<span
                                        class="text-danger">*</span></label>
                                    <div class="col-sm-7">
                                    <input type="text" parsley-type="text" class="form-control" id="referred_by"
                                        name="referred_by" placeholder="Referred By">
                                </div>
                                
                            </div>

                            <!-- Test Categories Section -->
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Test Categories<span class="text-danger">*</span></label>
                                <div class="col-sm-7">
                                    <div class="checkbox-group">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="Blood Test" id="test1">
                                            <label class="form-check-label" for="test1">Blood Test</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="Urine Test" id="test2">
                                            <label class="form-check-label" for="test2">Urine Test</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="X-Ray" id="test3">
                                            <label class="form-check-label" for="test3">X-Ray</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="ECG" id="test4">
                                            <label class="form-check-label" for="test4">ECG</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="MRI" id="test5">
                                            <label class="form-check-label" for="test5">MRI</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="CT Scan" id="test6">
                                            <label class="form-check-label" for="test6">CT Scan</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="Ultrasound" id="test7">
                                            <label class="form-check-label" for="test7">Ultrasound</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="Laboratory Test" id="test8">
                                            <label class="form-check-label" for="test8">Laboratory Test</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="tests[]" value="Other" id="test9">
                                            <label class="form-check-label" for="test9">Other</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Test Categories Section -->

                            <div class="form-group row">
                                <label for="address" class="col-sm-4 col-form-label">Note</label>
                                <div class="col-sm-7">
                                    <textarea class="form-control" id="note" name="note"></textarea>
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-sm-8 offset-sm-4">
                            <button onclick="history.back()" class="btn btn-info">Back</button>
                            <button type="submit" class="btn btn-success waves-effect waves-light mr-1">
                                Register
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[method="POST"]');
    const checkboxes = Array.from(document.querySelectorAll('input[name="tests[]"]'));
    const hidden = document.getElementById('test_category');
    const display = document.getElementById('selected_tests_display');

    function updateTests() {
        const values = checkboxes.filter(cb => cb.checked).map(cb => cb.value);
        hidden.value = values.join(',');
        display.value = values.length ? values.join(', ') : '';
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateTests));
    updateTests();

    form.addEventListener('submit', function () {
        updateTests();
    });
});
</script>
@endsection