<div class="col-xxl-4 col-lg-4 col-sm-6">
    <div class="card card-bordered product-card">
        @if (!empty($item->title))
            <p class="text-center pt-3 fw-bold">{{ $item->title }}</p>
        @endif

        <div class="product-thumb">
            <video controls width="100%" height="auto">
                <source src="{{ $item->checklistItemResults->first()->formattedValue }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <ul class="product-badges">
                <li><span class="badge bg-success">{{ $item->description }}</span></li>
            </ul>
        </div>
    </div>
</div>
