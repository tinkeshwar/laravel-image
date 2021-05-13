<?php

namespace Tinkeshwar\Imager;

use Illuminate\Support\Facades\Storage;
use Tinkeshwar\Imager\Models\Image;

class Imager
{
    public static function moveFile($file, $folder = 'public')
    {
        if (!$file->isValid()) {
            return '';
        }
        $response = Storage::disk(config('image.image_storage'))->put($folder, $file);
        Self::clearCache();
        return substr($response, strlen($folder) + 1);
    }

    public static function listCache()
    {
        return Storage::disk(config('image.image_cache_storage'))->allFiles('image-cache');
    }

    public static function clearCache()
    {
        return Storage::disk(config('image.image_cache_storage'))->deleteDirectory('image-cache');
    }

    public static function deleteFile($id)
    {
        $image = Image::find($id);
        if($image){
            Storage::disk(config('image.image_storage'))->delete($image->path.$image->name);
            Image::destroy($id);
            Self::clearCache();
            return true;
        }
        return false;
    }
}
