<div class="nk-block">
    <div class="nk-block-head">
        <h5 class="title">Staff Info</h5>
    </div>
    <div class="profile-ud-list">
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Name</span>
                <span class="profile-ud-value">{!! canEmpty($assign->name) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Email</span>
                <span class="profile-ud-value">{!! canEmpty($assign->email) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Phone</span>
                <span class="profile-ud-value">{!! canEmpty($assign->phone) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Address</span>
                <span class="profile-ud-value">{!! canEmpty($assign->address) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Status</span>
                <span class="profile-ud-value">{!! $assign->status_badge !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Date Created</span>
                <span class="profile-ud-value">{!! $assign->createdAt(isHumanTime: false) !!}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">Date Updated</span>
                <span class="profile-ud-value">{!! $assign->updatedAt(isHumanTime: false) !!}</span>
            </div>
        </div>
    </div>
</div>
