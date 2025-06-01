<div class="col-md-12">
    <div class="form-group">
        <label class="form-label">{{ $item->title }}</label>
        <textarea class="form-control" disabled rows="9">{{ $item->checklistItemResults->first()->formattedValue }}</textarea>
    </div>
</div>
