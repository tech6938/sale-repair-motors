<div class="col-md-12">
    @if (!empty($item->title))
        <h5>{{ $item->title }}</h5>
    @endif

    @if (!empty($item->description))
        <p class="mb-0">{{ $item->description }}</p>
    @endif

    @foreach ($item->itemOptions as $option)
        <div class="d-block pt-3">
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" @checked($option->uuid === $item->checklistItemResults->first()->formattedValue) disabled>
                <label class="custom-control-label">{{ $option->label }}</label>
            </div>
        </div>
    @endforeach
</div>
