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
}
