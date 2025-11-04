<nav class="navbar navbar-dark px-3" style="background: #C49A6C; position: fixed; top: 0; width: 100%; z-index: 1000;">
    <a class="navbar-brand" href="{{ route('dashboard.index') }}">
        <img src="{{ asset('img/logo-anda.png') }}" alt="Hotel Logo" class="brand-logo-img-only">
    </a>
</nav>

<style>
    .brand-logo-img-only {
        max-height: 88px;  
        width: auto;       
        display: block;
    }

    /* Tambahkan padding-top pada konten agar tidak tertutup navbar */
    body {
        padding-top: 88px; /* sesuaikan dengan tinggi navbar */
    }
</style>
