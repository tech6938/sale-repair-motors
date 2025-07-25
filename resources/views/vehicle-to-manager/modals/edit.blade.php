<h5 class="title pb-3">Update Staff</h5>

<form action="{{ route('prepration-staffs.update', $staffs->uuid) }}" method="post" async-form on-async-modal data-datatable="#staffss-dt">
    @csrf

    @method('put')

    <div class="row gy-4">
        <div class="col-md-6">
            <div class="custom-control custom-switch checked">
                <input type="checkbox" class="custom-control-input" name="status" id="status" value="true" @checked($staffs->isActive())>
                <label class="custom-control-label" for="status">Active</label>
            </div>
            <span class="invalid-feedback" role="alert"></span>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="comments">Comments</label>
                <textarea class="form-control" id="comments" name="comments" placeholder="Enter your comments here. Typically for suspension.">{{ $staffs->prepration_staff_comments }}</textarea>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-12 pt-1">
            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                <li><button type="submit" class="btn btn-lg btn-primary">Update</button></li>
                <li><button type="button" class="btn btn-lg btn-outline-danger" async-modal-close>Cancel</button></li>
            </ul>
        </div>
    </div>
</form>
