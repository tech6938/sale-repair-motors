<div class="col-md-12">
    <div class="form-group">
        <label class="form-label">{{ $item->title }}</label>
        <input type="text" class="form-control" value="{{ $item->checklistItemResults->first()->formattedValue }}" disabled>
    </div>
</div>
