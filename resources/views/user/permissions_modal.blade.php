@if($user->user_type == 'Admin' || $user->user_type == 'Super Admin')
    <p>Can Access Everything</p>
@else
    <form id="permissionsForm">
        <div class="row">
            <div class="col-md-6">
                <h5>Employees</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Add
                        <input type="checkbox" class="permission-checkbox" data-field="employees_add" {{ $user->employees_add ? 'checked' : '' }}>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Edit
                        <input type="checkbox" class="permission-checkbox" data-field="employees_edit" {{ $user->employees_edit ? 'checked' : '' }}>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Delete
                        <input type="checkbox" class="permission-checkbox" data-field="employees_delete" {{ $user->employees_delete ? 'checked' : '' }}>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>Patients</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="patients" {{ $user->patients ? 'checked' : '' }}>
                    </li>
                </ul>
                <h5>Test Category</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="testcategory" {{ $user->testcategory ? 'checked' : '' }}>
                    </li>
                </ul>
                <h5>Referral</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="referral" {{ $user->referral ? 'checked' : '' }}>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h5>Billing</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Add
                        <input type="checkbox" class="permission-checkbox" data-field="billing_add" {{ $user->billing_add ? 'checked' : '' }}>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Edit
                        <input type="checkbox" class="permission-checkbox" data-field="billing_edit" {{ $user->billing_edit ? 'checked' : '' }}>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Delete
                        <input type="checkbox" class="permission-checkbox" data-field="billing_delete" {{ $user->billing_delete ? 'checked' : '' }}>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>Pathology</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Add
                        <input type="checkbox" class="permission-checkbox" data-field="pathology_add" {{ $user->pathology_add ? 'checked' : '' }}>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Edit
                        <input type="checkbox" class="permission-checkbox" data-field="pathology_edit" {{ $user->pathology_edit ? 'checked' : '' }}>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Delete
                        <input type="checkbox" class="permission-checkbox" data-field="pathology_delete" {{ $user->pathology_delete ? 'checked' : '' }}>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h5>Radiology</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="radiology" {{ $user->radiology ? 'checked' : '' }}>
                    </li>
                </ul>
                <h5>Ultrasonography</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="ultrasonography" {{ $user->ultrasonography ? 'checked' : '' }}>
                    </li>
                </ul>
                <h5>Electrocardiography</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="electrocardiography" {{ $user->electrocardiography ? 'checked' : '' }}>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>Report Booth</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="reportbooth" {{ $user->reportbooth ? 'checked' : '' }}>
                    </li>
                </ul>
                <h5>Financial Management</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="financial" {{ $user->financial ? 'checked' : '' }}>
                    </li>
                </ul>
                <h5>Report Generation</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="report_g" {{ $user->report_g ? 'checked' : '' }}>
                    </li>
                </ul>
                <h5>Inventory Generation</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Access
                        <input type="checkbox" class="permission-checkbox" data-field="inventory" {{ $user->inventory ? 'checked' : '' }}>
                    </li>
                </ul>
            </div>
        </div>
    </form>
@endif