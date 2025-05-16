<div class="nk-block">
    <div class="nk-block-head">
        <h5 class="title">Admin Information</h5>
    </div>
    <div class="profile-ud-list">
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Name</span>
                <span class="profile-ud-value">{!! canEmpty($admin->full_name) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Email</span>
                <span class="profile-ud-value">{!! canEmpty($admin->email) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Phone</span>
                <span class="profile-ud-value">{!! canEmpty($admin->phone) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item profile-ud-item-description">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Address</span>
                <span class="profile-ud-description">{!! canEmpty($admin->address) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Status</span>
                <span class="profile-ud-value">{!! $admin->status_badge !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Date Created</span>
                <span class="profile-ud-value">{!! $admin->createdAt(isHumanTime: false) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Date Updated</span>
                <span class="profile-ud-value">{!! $admin->updatedAt(isHumanTime: false) !!}</span>
            </div>
        </div>
    </div>
</div>
