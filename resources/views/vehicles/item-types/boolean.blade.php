<div class="col-md-12">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" @checked($item->checklistItemResults->first()->formattedValue) disabled>
        <label class="custom-control-label">{{ $item->title }}</label>
    </div>
    <span class="invalid-feedback" role="alert"></span>
</div>
