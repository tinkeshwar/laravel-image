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
        $response = Storage::disk(config('image.image_storage'))->put($folder, $file, 'public');
        Self::clearCache();
        return substr($response, strlen($folder) + 1);
    }

    public static function moveWithFileRatio($file, $folder = 'public')
    {
        if (!$file->isValid()) {
            return '';
        }
        list($width, $height, $type, $attr) = getimagesize($file);
        $divisor = gmp_intval( gmp_gcd( $width, $height ) );
        $aspectRatio = $width / $divisor . ':' . $height / $divisor;
        $response = Storage::disk(config('image.image_storage'))->put($folder, $file, 'public');
        Self::clearCache();
        return [
            'name' => substr($response, strlen($folder) + 1),
            'ratio' => $aspectRatio
        ];
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
        if ($image) {
            Storage::disk(config('image.image_storage'))->delete($image->path . $image->name);
            Image::destroy($id);
            Self::clearCache();
            return true;
        }
        return false;
    }

    public static function removeFile($id)
    {
        $image = Image::find($id);
        if ($image) {
            Image::destroy($id);
            Self::clearCache();
            return true;
        }
        return false;
    }

    public static function changePosition($id, $position)
    {
        $image = Image::find($id);
        if ($image) {
            $imageList = Image::where('id', '!=', $id)->where('imageable_type', $image->imageable_type)->where('imageable_id', $image->imageable_id)->orderBy('sort_order', 'ASC')->get();
            foreach ($imageList as $imageToChange) {
                if ($imageToChange->sort_order < $position) {
                    continue;
                }
                Image::where('id', $imageToChange->id)->increment('sort_order', 1);
            }
            $image->update(['sort_order' => $position]);
            return true;
        }
        return false;
    }
}
