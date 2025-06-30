<div class="nk-block-head nk-block-head-lg pb-0">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">{{ $checklist->title }}</h4>
        </div>
        <div class="nk-block-head-content align-self-start d-lg-none">
            <a href="javascript:void(0);" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside">
                <em class="icon ni ni-menu-alt-r"></em>
            </a>
        </div>
    </div>
</div>

<div class="nk-data data-list">
    <div class="row g-gs">
        @if ($items->count() == 0)
            @include('vehicles.item-types.empty')
        @endif

        @foreach ($items as $item)
            @if (empty($item->checklistItemResults->first()))
                @include('vehicles.item-types.empty')

                @if ($item->item_type === 'multiselect')
                    @break
                @endif

                @continue
            @endif

            @if ($item->item_type == 'image')
                @include('vehicles.item-types.image', ['item' => $item])
            @endif

            @if ($item->item_type == 'multi_image')
                @include('vehicles.item-types.multi-image', ['item' => $item])
            @endif

            @if ($item->item_type == 'video')
                @include('vehicles.item-types.video', ['item' => $item])
            @endif

            @if ($item->item_type == 'text')
                @include('vehicles.item-types.text', ['item' => $item])
            @endif

            @if ($item->item_type == 'number')
                @include('vehicles.item-types.number', ['item' => $item])
            @endif

            @if ($item->item_type == 'boolean')
                @include('vehicles.item-types.boolean', ['item' => $item])
            @endif

            @if ($item->item_type == 'select')
                @include('vehicles.item-types.select', ['item' => $item])
            @endif

            @if ($item->item_type == 'multiselect')
                @include('vehicles.item-types.multiselect', ['item' => $item])
            @endif
        @endforeach
    </div>
</div>
