<div class="col-md-12">
    @if (!empty($item->title))
        <h5 class="pb-3">{{ $item->title }}</h5>
    @endif

    <div class="form-group">
        <label class="form-label">{{ $item->description }}</label>
        <textarea class="form-control" disabled rows="9">{{ $item->checklistItemResults->first()->formattedValue }}</textarea>
    </div>
</div>
