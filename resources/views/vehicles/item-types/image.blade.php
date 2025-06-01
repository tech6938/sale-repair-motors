<div class="col-xxl-4 col-lg-4 col-sm-6">
    <div class="card card-bordered product-card">
        <div class="product-thumb">
            <a href="{{ $item->checklistItemResults->first()->formattedValue['full'] }}" class="popup-image image-container">
                <img src="{{ $item->checklistItemResults->first()->formattedValue['thumbnail'] }}" onerror="_ie(this)">
            </a>
            <ul class="product-badges">
                <li><span class="badge bg-success">{{ $item->title }}</span></li>
            </ul>
        </div>
    </div>
</div>
