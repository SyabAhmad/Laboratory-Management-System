@extends('Layout.master')
@section('title', 'Add Referral')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-3 bg-cards">
        <div class="card-header bg-surface border-0 py-3 d-flex align-items-center justify-content-between">
            <h4 class="mb-0">
                Add New Referral
            </h4>
            <div>
                <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back();">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back
                </button>
                <a href="{{ route('referrels.list') }}" class="btn btn-secondary">
                    <i class="bi bi-list-ul me-1"></i> All Referrals
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="referral-form" action="{{ route('referrals.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-4">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="commission_percentage" class="form-label">Commission Percentage <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" min="0" max="100" step="0.01" value="0" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text text-muted">Commission percentage to be earned on each test/bill (0-100)</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back();">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" id="save-referral-btn" class="btn btn btn-primary-custom px-4">
                        <i class="bi bi-check-circle me-1"></i> Save Referral
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('referral-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('save-referral-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';
        }

        const fd = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(async res => {
            if (!res.ok) {
                let data = null;
                try { data = await res.json(); } catch (err) {}
                const message = (data && data.message) ? data.message : 'Failed to save referral.';
                alert(message);
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Referral'; }
                return;
            }
            // Refresh the page to reflect changes
            window.location.reload();
            // On success, show success message and refresh the page
            const data = await res.json();
            alert('Referral saved successfully!');

            
        }).catch(err => {
            console.error(err);
            alert('An error occurred while saving the referral.');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Referral'; }
        });
    });
</script>
@endsection
