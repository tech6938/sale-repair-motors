<div class="col-md-12">
    @if (!empty($item->title))
        <h5 class="pb-3">{{ $item->title }}</h5>
    @endif

    <div class="form-group">
        <label class="form-label">{{ $item->description }}</label>
        <input type="text" class="form-control" value="{{ $item->checklistItemResults->first()->formattedValue }}" disabled>
    </div>
</div>
