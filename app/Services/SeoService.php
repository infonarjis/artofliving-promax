<?php

namespace App\Services;

use App\Models\MatrimonyData;
use App\Models\SeoPageData;

class SeoService
{
    public static function getPageSeo(string $slug, array $dynamic = [], string $lang = 'en')
    {
        $seo = SeoPageData::active()
            ->where('page_slug', $slug)
            ->where('lang_code', $lang)
            ->first();

        if (!$seo) {
            return self::defaultSeo($dynamic);
        }

        return self::mergeSeo($seo, $dynamic);
    }

    private static function mergeSeo($seo, $dynamic)
    {
        $ogImage = _assetUrl('upload_path.OG_BANNER_IMAGE_URL') . $seo->og_image;
        $twitterImage = _assetUrl('upload_path.OG_BANNER_IMAGE_URL') . $seo->twitter_image;

        $schema = $seo->schema_json
            ? self::replaceSchemaPlaceholders($seo->schema_json, $dynamic)
            : self::defaultSchema($dynamic);

        return [
            'title' => $dynamic['title'] ?? $seo->seo_title,
            'description' => $dynamic['description'] ?? $seo->seo_description,
            'keywords' => $dynamic['keywords'] ?? $seo->seo_keywords,

            'og_title' => $dynamic['title'] ?? $seo->og_title,
            'og_description' => $dynamic['description'] ?? $seo->og_description,
            'og_image' => $dynamic['image'] ?? $ogImage,

            'twitter_title' => $dynamic['title'] ?? $seo->twitter_title,
            'twitter_description' => $dynamic['description'] ?? $seo->twitter_description,
            'twitter_image' => $dynamic['image'] ?? $twitterImage,

            'canonical' => $seo->canonical_url ?? url()->current(),
            'robots' => $seo->meta_robots,
            'schema' => $schema,
        ];
    }

    private static function replaceSchemaPlaceholders($schema, $dynamic)
    {
        $json = json_encode($schema);

        foreach ($dynamic as $key => $value) {
            $json = str_replace("{{{$key}}}", $value, $json);
        }

        return json_decode($json, true);
    }

    private static function defaultSchema($dynamic = [])
    {
        $configArr = _getSiteSetting();
        $logoUrl = _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'];

        return [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => $configArr['web_name'],
            "url" => url('/'),
            "logo" => $logoUrl,
        ];
    }

    public static function defaultSeo($dynamic = [])
    {
        $configArr = _getSiteSetting();
        $defaultOgImage = _assetUrl('upload_path.OG_BANNER_IMAGE_URL') . $configArr['seo_default_og_image'];

        return [
            'title' => $dynamic['title'] ?? $configArr['web_name'],
            'description' => $dynamic['description'] ?? $configArr['website_description'],
            'keywords' => $dynamic['keywords'] ?? $configArr['website_keywords'],
            'og_image' => $dynamic['image'] ?? $defaultOgImage,
            'canonical' => url()->current(),
            'robots' => 'index,follow',
            'schema' => null,
        ];
    }

    public static function getDynamicMatrimonySeo($slug, array $dynamic = [])
    {
        $lang = app()->getLocale() ?: 'en';

        $matrimonyData = MatrimonyData::active()
            ->where('slug', $slug)
            ->where('lang_code', $lang)
            ->first();

        if ($matrimonyData) {

            $bannerImage = _assetUrl('upload_path.OG_BANNER_IMAGE_URL') . $matrimonyData->banner_img;

            $schema = $matrimonyData->schema_json ?? null; // remove if column truly doesn't exist
            $schema = $schema
                ? self::replaceSchemaPlaceholders($schema, $dynamic)
                : self::defaultSchema($dynamic);

            return [
                'title' => $dynamic['title'] ?? $matrimonyData->meta_title,
                'description' => $dynamic['description'] ?? $matrimonyData->meta_description,
                'keywords' => $dynamic['keywords'] ?? $matrimonyData->meta_keyword,

                'og_title' => $dynamic['title'] ?? $matrimonyData->meta_title,
                'og_description' => $dynamic['description'] ?? $matrimonyData->meta_description,
                'og_image' => $dynamic['image'] ?? $bannerImage,

                'twitter_title' => $dynamic['title'] ?? $matrimonyData->meta_title,
                'twitter_description' => $dynamic['description'] ?? $matrimonyData->meta_description,
                'twitter_image' => $dynamic['image'] ?? $bannerImage,

                'canonical' => url()->current(),
                'robots' => 'index, follow',
                'schema' => $schema,
            ];

        } else {
            $name = ucwords(str_replace('-', ' ', $slug));
            $url  = url()->current();

            // placeholders passed into normal flow
            $dynamic = [
                'name' => $name,
                'url'  => $url,
                'title' => "$name Matrimony – Find Verified $name Bride & Groom Profiles",
                'description' => "Join $name Matrimony to search verified $name bride and groom profiles by city, profession, education and more.",
                'keywords' => "$name matrimony, $name bride, $name groom",
            ];

            // Let existing engine do the heavy lifting
            return self::getPageSeo('matrimony-dynamic', $dynamic);
        }
    }
}
