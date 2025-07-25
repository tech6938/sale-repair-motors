<h5 class="title pb-3">Assign Vehicle</h5>

<form action="{{ route('vehicles-assign.store') }}" method="POST" async-form on-async-modal data-datatable="#admins-dt">
    @csrf

    <div class="row gy-4">

        <div class="col-md-12">
            <div class="form-group">
                <label for="vehicle_id" class="form-label">
                    Vehicle <span class="text-danger">*</span>
                </label>
                <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                    <option value="">Select Vehicle</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->model }}</option>
                    @endforeach
                </select>
                <span class="invalid-feedback d-block" role="alert"></span>
            </div>
        </div>

        <!-- Preparation Managers Multi Select as Tags -->
        <div class="col-md-12">
            <div class="form-group">
                <label for="preparation_managers" class="form-label">
                    Preparation Managers <span class="text-danger">*</span>
                </label>
                <select id="preparation_managers" class="form-control js-tags-select" name="preparation_manager_id[]" multiple="multiple" required>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                    @endforeach
                </select>
                <span class="invalid-feedback d-block" role="alert"></span>
            </div>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.js-tags-select').select2({
            tags: true,
            placeholder: 'Select or type Preparation Managers',
            width: '100%',
        });
    });
</script>
@endpush
