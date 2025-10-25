@extends('Layout.master')
@section('title', 'Add Test Parameters')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-3 bg-cards">
        <div class="card-header bg-surface border-0 py-3 d-flex align-items-center justify-content-between">
            <h4 class="mb-0">
                Add Parameters for: <span class="text-primary-custom fw-semibold">{{ $test->cat_name }}</span>
            </h4>
            <div>
                <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back();">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back
                </button>
                <a href="{{ route('labtest.index') }}" class="btn btn-secondary">
                    <i class="bi bi-list-ul me-1"></i> Tests
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="lab-params-form" action="{{ route('labtest.parameters.store', $test->id) }}" method="POST">
                @csrf

                <div id="parameter-container">
                    <div class="row parameter-row mb-3 g-3">
                        <div class="col-md-3">
                            <label class="form-label visually-hidden">Parameter Name</label>
                            <input type="text" name="parameter_name[]" class="form-control" placeholder="Parameter Name" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label visually-hidden">Unit</label>
                            <input type="text" name="unit[]" class="form-control" placeholder="Unit (e.g., mg/dL)">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label visually-hidden">Reference Range</label>
                            <input type="text" name="reference_range[]" class="form-control" placeholder="Reference Range (e.g., 5–20)">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger w-100 remove-row">
                                <i class="bi bi-trash me-1"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap gap-2 mt-3">
                    <button type="button" class="btn btn-outline-secondary" id="add-row">
                        <i class="bi bi-plus-circle me-1"></i> Add Another Parameter
                    </button>
                    <button type="submit" id="save-params-btn" class="btn btn btn-primary-custom px-4">
                        <i class="bi bi-check-circle me-1"></i> Save Parameters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Parameters Section -->
    <div class="card shadow-sm border-0 rounded-3 mt-5 bg-cards">
        <div class="card-header bg-surface border-0 py-3">
            <h5 class="mb-0 text-heading">Existing Parameters</h5>
        </div>
        <div class="card-body">
            @if($test->parameters && $test->parameters->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Unit</th>
                                <th scope="col">Reference Range</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($test->parameters as $param)
                                <tr data-param-id="{{ $param->id }}">
                                    <td class="param-name">{{ $param->parameter_name }}</td>
                                    <td class="param-unit">{{ $param->unit }}</td>
                                    <td class="param-range">{{ $param->reference_range }}</td>
                                    <td class="text-end param-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $param->id }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('labtest.parameters.destroy', $param->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this parameter?');" style="display:inline; margin-left:6px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No parameters added yet for this test.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.getElementById('add-row').addEventListener('click', function() {
        const container = document.getElementById('parameter-container');
        const newRow = document.querySelector('.parameter-row').cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newRow);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('.parameter-row');
            if (row) row.remove();
        }
    });

    // AJAX submit: submit parameters without redirect; refresh current page on success
    (function() {
        const form = document.getElementById('lab-params-form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('save-params-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';
            }

            const fd = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(async res => {
                if (!res.ok) {
                    // try to parse JSON errors
                    let data = null;
                    try { data = await res.json(); } catch (err) {}
                    const message = (data && data.message) ? data.message : 'Failed to save parameters.';
                    alert(message);
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Parameters'; }
                    return;
                }

                // On success we reload the current page so the Existing Parameters list is refreshed
                location.reload();
            }).catch(err => {
                console.error(err);
                alert('An error occurred while saving parameters.');
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Parameters'; }
            });
        });
    })();

    // Inline edit handler for existing parameters
    (function() {
        const table = document.querySelector('table');
        if (!table) return;

        table.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit');
            if (!editBtn) return;

            const row = editBtn.closest('tr');
            const id = row.dataset.paramId;

            // If already in edit mode, ignore
            if (row.classList.contains('editing')) return;
            row.classList.add('editing');

            const nameCell = row.querySelector('.param-name');
            const unitCell = row.querySelector('.param-unit');
            const rangeCell = row.querySelector('.param-range');
            const actionsCell = row.querySelector('.param-actions');

            const currentName = nameCell.textContent.trim();
            const currentUnit = unitCell.textContent.trim();
            const currentRange = rangeCell.textContent.trim();

            nameCell.innerHTML = `<input type="text" class="form-control form-control-sm edit-name" value="${escapeHtml(currentName)}">`;
            unitCell.innerHTML = `<input type="text" class="form-control form-control-sm edit-unit" value="${escapeHtml(currentUnit)}">`;
            rangeCell.innerHTML = `<input type="text" class="form-control form-control-sm edit-range" value="${escapeHtml(currentRange)}">`;

            actionsCell.innerHTML = `
                <button class="btn btn-sm btn-success btn-save me-1" data-id="${id}"><i class="bi bi-check-lg"></i> Save</button>
                <button class="btn btn-sm btn-secondary btn-cancel" data-id="${id}"><i class="bi bi-x-lg"></i> Cancel</button>
            `;
        });

        // Save / Cancel handlers
        table.addEventListener('click', function(e) {
            const saveBtn = e.target.closest('.btn-save');
            if (saveBtn) {
                const row = saveBtn.closest('tr');
                const id = row.dataset.paramId;
                const name = row.querySelector('.edit-name').value.trim();
                const unit = row.querySelector('.edit-unit').value.trim();
                const range = row.querySelector('.edit-range').value.trim();

                // basic client-side validation
                if (!name) { alert('Parameter name is required'); return; }

                // send AJAX PUT
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name=_token]')?.value;

                fetch("{{ url('/labtest/parameters') }}/" + id, {
                    method: 'POST', // Laravel expects POST with _method=PUT when not using true PUT in some setups
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: (() => {
                        const fd = new FormData();
                        fd.append('_method', 'PUT');
                        fd.append('_token', token);
                        fd.append('parameter_name', name);
                        fd.append('unit', unit);
                        fd.append('reference_range', range);
                        return fd;
                    })()
                }).then(async res => {
                    if (!res.ok) {
                        let data = null;
                        try { data = await res.json(); } catch (err) {}
                        const message = (data && data.message) ? data.message : 'Failed to update parameter.';
                        alert(message);
                        return;
                    }

                    const data = await res.json();
                    if (data && data.success) {
                        // update row display
                        row.querySelector('.param-name').textContent = data.param.parameter_name;
                        row.querySelector('.param-unit').textContent = data.param.unit || '';
                        row.querySelector('.param-range').textContent = data.param.reference_range || '';
                        row.querySelector('.param-actions').innerHTML = `
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit"><i class="bi bi-pencil"></i> Edit</button>
                            <form action="{{ route('labtest.parameters.destroy', '__ID__') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this parameter?');" style="display:inline; margin-left:6px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        `;

                        // replace placeholder with actual ID in the form action
                        const frag = document.createElement('div');
                        frag.innerHTML = row.querySelector('.param-actions').innerHTML;
                        const form = frag.querySelector('form');
                        if (form) {
                            form.action = form.action.replace('__ID__', id);
                            row.querySelector('.param-actions').innerHTML = frag.innerHTML;
                        }

                        row.classList.remove('editing');
                    } else {
                        alert('Update failed');
                    }
                }).catch(err => {
                    console.error(err);
                    alert('Network or server error while updating parameter.');
                });

                return;
            }

            const cancelBtn = e.target.closest('.btn-cancel');
            if (cancelBtn) {
                const row = cancelBtn.closest('tr');
                // reload page to reset row, or you could re-render original values from data attributes
                location.reload();
            }
        });

        function escapeHtml(str) {
            return String(str).replace(/[&<>"'`=\/]/g, function(s) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                    '/': '&#x2F;',
                    '`': '&#x60;',
                    '=': '&#x3D;'
                })[s];
            });
        }
    })();
</script>
@endsection