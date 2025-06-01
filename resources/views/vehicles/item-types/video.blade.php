<div class="col-xxl-4 col-lg-4 col-sm-6">
    <div class="card card-bordered product-card">
        <div class="product-thumb">
            <video controls width="100%" height="auto">
                <source src="{{ $item->checklistItemResults->first()->formattedValue }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <ul class="product-badges">
                <li><span class="badge bg-success">{{ $item->title }}</span></li>
            </ul>
        </div>
    </div>
</div>
