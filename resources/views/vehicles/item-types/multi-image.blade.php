@foreach ($item->checklistItemResults->first()->formattedValue as $value)
    <div class="col-xxl-4 col-lg-4 col-sm-6">
        <div class="card card-bordered product-card">
            @if (!empty($item->title))
                <p class="text-center pt-3 fw-bold">{{ $item->title }}</p>
            @endif

            <div class="product-thumb">
                <a href="{{ $value['full'] }}" class="popup-image image-container">
                    <img src="{{ $value['thumbnail'] }}" onerror="_ie(this)">
                </a>
                <ul class="product-badges">
                    <li><span class="badge bg-success">{{ $item->description }}</span></li>
                </ul>
            </div>
        </div>
    </div>
@endforeach
