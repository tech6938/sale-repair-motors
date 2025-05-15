<h5 class="title pb-3">Update Profile</h5>

<form action="{{ route('profile.update') }}" method="post" async-form on-async-modal data-event="profile.updated">
    @csrf

    @method('put')

    <div class="row gy-4">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" placeholder="Enter full name" required>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ auth()->user()->phone }}" placeholder="Enter contact phone number">
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <textarea class="form-control" id="address" name="address" placeholder="Enter address">{{ auth()->user()->address }}</textarea>
                <span class="invalid-feedback" role="alert"></span>
            </div>
        </div>

        <div class="col-12 pt-1">
            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                <li><button type="submit" class="btn btn-lg btn-primary">Save</button></li>
                <li><button type="button" class="btn btn-lg btn-outline-danger" async-modal-close>Cancel</button></li>
            </ul>
        </div>
    </div>
</form>
