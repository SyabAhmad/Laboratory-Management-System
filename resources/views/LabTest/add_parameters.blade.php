@extends('Layout.master')
@section('title', 'Add Test Parameters')

@section('content')
    <div class="container mt-4">
        <h4>Add Parameters for: <span class="text-primary">{{ $test->cat_name }}</span></h4>
        <form action="{{ route('labtest.parameters.store', $test->id) }}" method="POST">
            @csrf

            <div id="parameter-container">
                <div class="row parameter-row mb-2">
                    <div class="col-md-3">
                        <input type="text" name="parameter_name[]" class="form-control" placeholder="Parameter Name"
                            required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="unit[]" class="form-control" placeholder="Unit">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="reference_range[]" class="form-control" placeholder="Reference Range">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary mb-3" id="add-row">Add Another Parameter</button><br>
            <button type="submit" class="btn btn-primary">Save Parameters</button>
        </form>
    </div>

    <script>
        document.getElementById('add-row').addEventListener('click', function() {
            const container = document.getElementById('parameter-container');
            const newRow = document.querySelector('.parameter-row').cloneNode(true);
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            container.appendChild(newRow);
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('.parameter-row').remove();
            }
        });
    </script>
@endsection
