@if ($errors->any())
    <div class="alert alert-danger alert-dismissible mb-4">
        <button class="close" data-bs-dismiss="alert"></button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@session('error')
    <div class="alert alert-danger alert-icon alert-dismissible mb-4 alert-auto-hide">
        <em class="icon ni ni-cross-circle"></em> <strong>Oops</strong>! {{ session('error') }} <button class="close" data-bs-dismiss="alert"></button>
    </div>
@endsession

@session('success')
    <div class="alert alert-success alert-icon alert-dismissible mb-4 alert-auto-hide">
        <em class="icon ni ni-check-circle"></em> <strong>Hurray</strong>! {{ session('success') }} <button class="close" data-bs-dismiss="alert"></button>
    </div>
@endsession
