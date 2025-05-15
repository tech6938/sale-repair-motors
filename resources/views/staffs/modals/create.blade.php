<h5 class="title pb-3">Invite New Staff</h5>

<form action="{{ route('staffs.store') }}" method="post" async-form on-async-modal data-datatable="#staffs-dt">
    @csrf

    <div class="row gy-4">
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter staff's full name" required>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter staff email" required>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-12 pt-1">
            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                <li><button type="submit" class="btn btn-lg btn-primary">Send Invitation</button></li>
                <li><button type="button" class="btn btn-lg btn-outline-danger" async-modal-close>Cancel</button></li>
            </ul>
        </div>
    </div>
</form>
