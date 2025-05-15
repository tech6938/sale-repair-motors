<h5 class="title pb-3">Update Password</h5>

<form action="{{ route('profile.password.update') }}" method="post" async-form on-async-modal>
    @csrf

    @method('put')

    <div class="row gy-4">
        <div class="col-md-12">
            <div class="form-group">
                <div class="form-label-group">
                    <label class="form-label" for="old_password">Old Password <span class="text-danger">*</span></label>
                </div>
                <div class="form-control-wrap">
                    <a href="javascript:void(0);" class="form-icon form-icon-right passcode-switch lg" data-target="old_password" tabindex="-1">
                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                    </a>
                    <input type="password" class="form-control form-control-lg" id="old_password" name="old_password" required autofocus placeholder="Enter old password">
                    <span class="invalid-feedback" role="alert"></span>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="form-label-group">
                    <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
                </div>
                <div class="form-control-wrap">
                    <a href="javascript:void(0);" class="form-icon form-icon-right passcode-switch lg" data-target="password" tabindex="-1">
                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                    </a>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required autofocus placeholder="Enter new password">
                    <span class="invalid-feedback" role="alert"></span>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="form-label-group">
                    <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                </div>
                <div class="form-control-wrap">
                    <a href="javascript:void(0);" class="form-icon form-icon-right passcode-switch lg" data-target="password_confirmation" tabindex="-1">
                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                    </a>
                    <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required autofocus
                        placeholder="Re-enter new password">
                    <span class="invalid-feedback" role="alert"></span>
                </div>
            </div>
        </div>
        <div class="col-12">
            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                <li><button type="submit" class="btn btn-lg btn-primary">Save</button></li>
                <li><button type="button" class="btn btn-lg btn-outline-danger" async-modal-close>Cancel</button></li>
            </ul>
        </div>
    </div>
</form>
