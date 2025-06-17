<div class="col-md-12">
    @if (!empty($item->title))
        <h5>{{ $item->title }}</h5>
    @endif

    @if (!empty($item->description))
        <p class="mb-0">{{ $item->description }}</p>
    @endif

    @foreach ($item->itemOptions as $option)
        @php $value = $item->checklistItemResults->first()?->formattedValue @endphp

        <div class="d-block pt-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" @checked($value && in_array($option->uuid, $value)) disabled>
                <label class="custom-control-label">{{ $option->label }}</label>
            </div>
        </div>
    @endforeach
</div>
