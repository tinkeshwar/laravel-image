<?php

use Illuminate\Support\Facades\Storage;
use Tinkeshwar\Imager\ThumbMaker;

if (!function_exists('thumb')) {
    function thumb($source, $height, $width, $extension = '.webp')
    {
        if (file_exists(public_path($source))) {
            Storage::disk(config('image.image_storage'))->makeDirectory('image-cache/');
            $info = pathinfo($source);
            $imager = new ThumbMaker();
            $newPath = Storage::disk(config('image.image_storage'))->path('image-cache/');
            $newName = $height . 'x' . $width . '-' . $info['filename'] . $extension;
            if (!Storage::disk(config('image.image_cache_storage'))->exists($newName)) {
                $newName = $imager->createThumb(public_path($source), $newPath, $height, $width, $newName, $extension);
            }
            $file = Storage::disk(config('image.image_storage'))->path('image-cache/' . $newName);
            header(header_string($extension));
            header("Cache-Control: max-age=" . config('image.max_age') * 86400 . ", public");
            header('Expires: Mon, ' . date('d M Y', strtotime("+ " . config('image.max_age') . "days")) . ' 05:00:00 GMT');
            header('Last-Modified: Mon, ' . date('d M Y', strtotime("- " . config('image.cache_last_modified') . "days")) . ' 05:00:00 GMT');
            $type = pathinfo($file, PATHINFO_EXTENSION);
            $data = file_get_contents($file);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }
}

if (!function_exists('thumbCDN')) {
    function thumbCDN($source, $height, $width, $extension = '.webp')
    {
        if (@getimagesize($source)) {
            $urlImage = 'temp.jpg';
            Storage::disk(config('image.image_storage'))->makeDirectory('image-cache/');
            file_put_contents($urlImage, file_get_contents($source));
            $info = pathinfo($urlImage);
            $imager = new ThumbMaker();
            $newPath = Storage::disk(config('image.image_storage'))->path('image-cache/');
            $newName = $height . 'x' . $width . '-' . $info['filename'] . $extension;
            if (!Storage::disk(config('image.image_cache_storage'))->exists($newName)) {
                $newName = $imager->createThumb(public_path($urlImage), $newPath, $height, $width, $newName, $extension);
            }
            $file = Storage::disk(config('image.image_storage'))->path('image-cache/' . $newName);
            header(header_string($extension));
            header("Cache-Control: max-age=" . config('image.max_age') * 86400 . ", public");
            header('Expires: Mon, ' . date('d M Y', strtotime("+ " . config('image.max_age') . "days")) . ' 05:00:00 GMT');
            header('Last-Modified: Mon, ' . date('d M Y', strtotime("- " . config('image.cache_last_modified') . "days")) . ' 05:00:00 GMT');
            $type = pathinfo($file, PATHINFO_EXTENSION);
            $data = file_get_contents($file);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            return 'https://dummyimage.com/' . $width . 'x' . $height . '&text=no-image';
        }
    }
}

if (!function_exists('header_string')) {
    function header_string($imageExtension)
    {
        switch ($imageExtension) {
            case '.webp':
                return 'Content-type:image/webp';
            case '.png':
                return 'Content-type:image/png';
            case '.jpg':
                return 'Content-type:image/jpg';
            default:
                return 'Content-type:image/png';
        }
    }
}
