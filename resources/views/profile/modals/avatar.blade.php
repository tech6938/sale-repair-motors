<h5 class="title pb-3">Update Avatar</h5>

<form action="{{ route('profile.avatar.update') }}" method="post" async-form on-async-modal data-event="avatar.updated">
    @csrf

    @method('put')

    <div class="row gy-4 text-center" image-picker data-toggle-submit-btn="true" data-max-size="500000">
        <div class="col-md-12">
            <input type="file" name="avatar" class="d-none" accept=".jpg,.jpeg.png,.gif">
            <div class="user-avatar auth-avatar xl" style="margin: 0 auto;">
                {!! getAvatarHtml() !!}
            </div>
        </div>
        <div class="col-md-12">
            <a href="javascript:void(0);" class="btn btn-sm btn-light">Choose Avatar</a>
            <br>
            <button type="submit" class="btn btn-sm btn-secondary mt-2" style="display: none;">Start Upload</button>
        </div>
    </div>
</form>
