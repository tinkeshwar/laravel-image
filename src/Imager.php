<?php

namespace Tinkeshwar\Imager;

use Illuminate\Support\Facades\Storage;

class Imager
{
    public static function moveFile($file, $folder = 'public')
    {
        if (!$file->isValid()) {
            return '';
        }
        $response = Storage::disk(config('image.image_storage'))->put($folder, $file);
        return substr($response, strlen($folder) + 1);
    }

    public static function listCache()
    {
        return Storage::disk(config('image.image_cache_storage'))->deleteDirectory('image-cache');
    }

    public static function clearCache()
    {
        return Storage::disk(config('image.image_cache_storage'))->deleteDirectory('image-cache');
    }
}
