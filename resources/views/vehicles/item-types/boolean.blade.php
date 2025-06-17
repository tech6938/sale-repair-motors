<div class="col-md-12">
    @if (!empty($item->title))
        <h5 class="pb-3">{{ $item->title }}</h5>
    @endif

    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" @checked($item->checklistItemResults->first()->formattedValue) disabled>
        <label class="custom-control-label">{{ $item->description }}</label>
    </div>
</div>
