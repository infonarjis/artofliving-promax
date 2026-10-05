<title>{{ $seoManagement['title'] }}</title>

<meta name="description" content="{{ $seoManagement['description'] }}">
<meta name="keywords" content="{{ $seoManagement['keywords'] }}">
<meta name="robots" content="{{ $seoManagement['robots'] }}">

<link rel="canonical" href="{{ $seoManagement['canonical'] }}" />

{{-- Open Graph --}}
<meta property="og:title" content="{{ $seoManagement['og_title'] ?? $seoManagement['title'] }}">
<meta property="og:description" content="{{ $seoManagement['og_description'] ?? $seoManagement['description'] }}">
<meta property="og:image" content="{{ $seoManagement['og_image'] }}">
<meta property="og:type" content="website">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoManagement['twitter_title'] ?? $seoManagement['title'] }}">
<meta name="twitter:description" content="{{ $seoManagement['twitter_description'] ?? $seoManagement['description'] }}">
<meta name="twitter:image" content="{{ $seoManagement['twitter_image'] ?? $seoManagement['og_image'] }}">

{{-- Schema --}}
@if (!empty($seoManagement['schema']))
    <script type="application/ld+json">
{!! json_encode($seoManagement['schema'], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
</script>
@endif
