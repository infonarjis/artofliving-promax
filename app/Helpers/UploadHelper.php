<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use WebPConvert\WebPConvert;
use App\Jobs\ProcessBlurImage;

class UploadHelper
{
    public static function uploadFile($file, $folder, $oldValue = '', $filename = '', $isWebpConvert = 1, $blurPath = null, $disk = 'public')
    {
        if (blank($folder)) {
            return null;
        }

        $folder = trim($folder, '/') . '/';

        if ($blurPath) {
            $blurPath = trim($blurPath, '/') . '/';
        }

        // Create directories
        if (!Storage::disk($disk)->exists($folder)) {
            Storage::disk($disk)->makeDirectory($folder);
        }

        if ($blurPath && !Storage::disk($disk)->exists($blurPath)) {
            Storage::disk($disk)->makeDirectory($blurPath);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (blank($filename)) {
            $filename = uniqid() . '_' . time() . '.' . $extension;
        }

        // Delete old files
        if (!blank($oldValue)) {
            self::deleteFile($folder, $oldValue, $disk, $blurPath);
        }

        // Store original image
        $path = $file->storeAs($folder, $filename, $disk);

        if (!$path) {
            return null;
        }

        ## Convert Original To WebP :
        $finalFilename = $filename;
        if ($isWebpConvert == 1 && $extension !== 'webp') {
            $originalPath = Storage::disk($disk)->path($path);
            $webpFilename = pathinfo($filename,PATHINFO_FILENAME) . '.webp';
            $webpPath = Storage::disk($disk)->path($folder . $webpFilename);
            WebPConvert::convert($originalPath,$webpPath,[    'quality' => 90]);
            // Delete original
            Storage::disk($disk)->delete($path);
            $finalFilename = $webpFilename;
        }

        ## Process Blur Image :
        if ($blurPath) {
            ProcessBlurImage::dispatch($folder . $finalFilename,$blurPath,$finalFilename,$disk);
        }

        return $finalFilename;
    }

    public static function deleteFile($folder,$filename,$disk = 'public',$blurPath = null) {
        if (blank($filename) || blank($folder)) {
            return;
        }

        $folder = trim($folder, '/') . '/';

        $filePath = $folder . $filename;

        // Delete main file
        if (Storage::disk($disk)->exists($filePath)) {
            Storage::disk($disk)->delete($filePath);
        }

        // Delete blur file
        if ($blurPath) {
            $blurPath = trim($blurPath, '/') . '/';
            $blurFilePath = $blurPath . $filename;
            if (Storage::disk($disk)->exists($blurFilePath)) {
                Storage::disk($disk)->delete($blurFilePath);
            }
        }
    }
}
