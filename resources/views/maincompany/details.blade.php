@extends('Layout.master')
@section('title', 'Company Details')
@section('content')
<style>
    .lab-card {
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .lab-card:hover {
        transform: translateY(-5px);
    }
    .lab-header-bg {
        background: linear-gradient(135deg, var(--primary) 0%, #2c3e50 100%);
        height: 150px;
        position: relative;
    }
    .lab-logo-container {
        position: relative;
        margin-top: -75px;
        text-align: center;
    }
    .lab-logo {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 4px solid #fff;
        background: #fff;
        padding: 5px;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    .info-item {
        padding: 20px;
        border-radius: 12px;
        background: #f8f9fa;
        margin-bottom: 20px;
        transition: all 0.2s;
    }
    .info-item:hover {
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
        color: #fff;
    }
    .btn-float-edit {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255,255,255,0.2);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 50px;
        padding: 8px 20px;
        backdrop-filter: blur(5px);
        transition: all 0.3s;
    }
    .btn-float-edit:hover {
        background: #fff;
        color: var(--primary);
    }
</style>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Lab Information</li>
                    </ol>
                </div>
                <h4 class="page-title">Lab Details</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        @php
            $lab = App\Models\MainCompanys::where('id', 1)->first();
        @endphp
        
        @if($lab)
        <div class="col-xl-10">
            <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                <!-- Header with Background -->
                <div class="lab-header-bg">
                    <button class="btn btn-float-edit editbtn" data-id="{{ $lab->id }}">
                        <i class="fas fa-edit me-1"></i> Edit Details
                    </button>
                </div>
                
                <div class="card-body pt-0">
                    <!-- Logo and Title Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="lab-logo-container">
                                <img src="{{ asset('/assets/HMS/lablogo/' . $lab->lab_image) }}" alt="{{ $lab->lab_name }}" class="lab-logo">
                                <h2 class="mt-3 mb-1 text-dark font-weight-bold">{{ $lab->lab_name }}</h2>
                                <p class="text-muted"><i class="fas fa-circle text-success font-size-10 me-1"></i> Operational</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Information Grid -->
                    <div class="row mt-4 px-3">
                        <div class="col-md-4">
                            <div class="info-item text-center">
                                <div class="d-flex justify-content-center">
                                    <div class="info-icon bg-primary shadow-primary">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted text-uppercase font-size-13 mb-2">Contact Number</h5>
                                <h4 class="mb-0 text-dark">{{ $lab->lab_phone }}</h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-item text-center">
                                <div class="d-flex justify-content-center">
                                    <div class="info-icon bg-success shadow-success">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted text-uppercase font-size-13 mb-2">Email Address</h5>
                                <h4 class="mb-0 text-dark">{{ $lab->lab_email }}</h4>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-item text-center">
                                <div class="d-flex justify-content-center">
                                    <div class="info-icon bg-warning shadow-warning">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                                <h5 class="text-muted text-uppercase font-size-13 mb-2">Location</h5>
                                <h4 class="mb-0 text-dark">{{ $lab->lab_address }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info (Optional) -->
                    <div class="row mt-3 px-3">
                        <div class="col-12">
                            <div class="alert alert-light bg-light border-0" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-information-outline font-size-24 text-primary me-3"></i>
                                    <div>
                                        <h5 class="mt-0 text-primary">About this Information</h5>
                                        <p class="mb-0 text-muted">This information appears on all generated reports, invoices, and official documents. Please ensure it is always up to date.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="col-xl-6 col-lg-8">
            <div class="card text-center py-5 border-0 shadow-lg" style="border-radius: 20px;">
                <div class="card-body">
                    <img src="{{ asset('assets/images/maintenance.svg') }}" alt="No Data" class="img-fluid mb-4" style="max-height: 200px;">
                    <h3 class="mb-3">No Lab Information Found</h3>
                    <p class="text-muted mb-4">Please configure your laboratory details to get started.</p>
                    <button class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg">
                        <i class="fas fa-plus-circle me-2"></i> Add Lab Details
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade modal-demo2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0">
                <h4 class="modal-title"><i class="fas fa-edit me-2"></i> Update Lab Information</h4>
                <button type="button" class="close text-white opacity-1" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-4">
                <form class="forms-sample labinfo_update" method="post" id="lab_infoForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="lab_name" class="font-weight-bold">Lab Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-hospital"></i></span>
                                    </div>
                                    <input type="text" required class="form-control border-left-0 bg-light" id="lab_name" name="lab_name" placeholder="Enter Lab Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="lab_phone" class="font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" required class="form-control border-left-0 bg-light" id="lab_phone" name="lab_phone" placeholder="Enter Phone Number">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="lab_email" class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" required class="form-control border-left-0 bg-light" id="lab_email" name="lab_email" placeholder="Enter Email Address">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="lab_image" class="font-weight-bold">Update Logo</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="lab_image" name="lab_image">
                                    <label class="custom-file-label" for="lab_image">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="lab_address" class="font-weight-bold">Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-map-marker-alt"></i></span>
                            </div>
                            <textarea required class="form-control border-left-0 bg-light" id="lab_address" name="lab_address" rows="3" placeholder="Enter Full Address"></textarea>
                        </div>
                    </div>

                    <div class="text-end border-top pt-3">
                        <button type="button" class="btn btn-light px-4 rounded-pill mr-2" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm"><i class="fas fa-save me-1"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('body').on('click', '.editbtn', function() {
        var id = $(this).data('id');
        var url = "{{ URL::route('labdetails.edit', '') }}" + "/" + id;
        
        // Show loading state if needed
        
        $.ajax({
            dataType: "json",
            url: url,
            method: 'get',
            success: function(labtest) {
                $('#id').val(labtest.id);
                $('#lab_name').val(labtest.lab_name);
                $('#lab_phone').val(labtest.lab_phone);
                $('#lab_email').val(labtest.lab_email);
                $('#lab_address').val(labtest.lab_address);
                $('.modal-demo2').modal('show');
            },
            error: function(xhr) {
                let errorMessage = 'We have some error';
                if (xhr.status === 404) {
                    errorMessage = 'Lab information not found';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: errorMessage,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    timer: 1800
                });
            }
        });
    });

    $('#lab_infoForm').on('submit', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var formData = new FormData(this);
        
        // Disable button to prevent double submit
        var submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            type: "POST",
            url: "{{ route('labdetails.update') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(response) {
                $('.modal-demo2').modal("hide");
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Lab Details Updated!',
                    text: 'Your changes have been saved successfully.',
                    showConfirmButton: false,
                    timerProgressBar: true,
                    timer: 1500
                });
                
                // Add a small delay before reload to show the success message
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnText);
                
                let errorMessage = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Update Failed',
                    text: errorMessage,
                    icon: 'warning',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Okay'
                });
            }
        });
    });

    // Custom file input label update
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endsection