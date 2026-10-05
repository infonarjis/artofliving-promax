@if (Session::has('error'))
    <div class="alert alert-danger alert-dismissible mt-2 auto-dismiss-alert" role="alert">
        Error! {{ Session::get('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@error('og_image')
    <div class="alert alert-danger alert-dismissible mt-2 auto-dismiss-alert" role="alert">
        Error! {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@enderror

@error('image')
    <div class="alert alert-danger alert-dismissible mt-2 auto-dismiss-alert" role="alert">
        Error! {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@enderror

@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible mt-2 auto-dismiss-alert" role="alert">
        Success! {{ Session::get('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script>
    setTimeout(function () {
        $('.auto-dismiss-alert').fadeOut('slow', function () {
            $(this).remove();
        });
    }, 3000);
</script>