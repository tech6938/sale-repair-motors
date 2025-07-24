<h5 class="title pb-3">Create New Admin</h5>

<form action="{{ route('prepration-staffs.store') }}" method="post" async-form on-async-modal data-datatable="#admins-dt">
    @csrf

    <div class="row gy-4">
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter admin's full name" required>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter admin email" required>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Password confirmation <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password_confirmation" name="password confirmation" placeholder="Enter admin password confirmation" required>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-12 pt-1">
            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                <li><button type="submit" class="btn btn-lg btn-primary">Create</button></li>
                <li><button type="button" class="btn btn-lg btn-outline-danger" async-modal-close>Cancel</button></li>
            </ul>
        </div>
    </div>
</form>
