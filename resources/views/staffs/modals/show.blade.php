<div class="nk-block">
    <div class="nk-block-head">
        <h5 class="title">Staff Information</h5>
    </div>
    <div class="profile-ud-list">
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Name</span>
                <span class="profile-ud-value">{!! canEmpty($staff->full_name) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Email</span>
                <span class="profile-ud-value">{!! canEmpty($staff->email) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Phone</span>
                <span class="profile-ud-value">{!! canEmpty($staff->phone) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Gender</span>
                <span class="profile-ud-value">{!! empty($staff->gender) ? canEmpty(null) : ucfirst($staff->gender) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Date of Birth</span>
                <span class="profile-ud-value">{!! empty($staff->dob) ? canEmpty(null) : frontendDate($staff->dob) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Status</span>
                <span class="profile-ud-value">{!! $staff->status_badge !!}</span>
            </div>
        </div>
        <div class="profile-ud-item profile-ud-item-description">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Address</span>
                <span class="profile-ud-description">{!! canEmpty($staff->address) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Date Created</span>
                <span class="profile-ud-value">{!! $staff->createdAt(isHumanTime: false) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Date Updated</span>
                <span class="profile-ud-value">{!! $staff->updatedAt(isHumanTime: false) !!}</span>
            </div>
        </div>
    </div>
</div>
