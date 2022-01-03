<?php

namespace Tinkeshwar\Imager;

use Illuminate\Support\Facades\Storage;

class ThumbMaker
{
    public function createThumb($originalFilePath, $newPath, $height, $width, $newName, $imageExtension)
    {
        $dx = $dy = $sx = $sy = 0;
        $source = pathinfo($originalFilePath);
        $original_image = $this->getImageType($source['extension'], $originalFilePath);
        $original_image_width = imagesx($original_image);
        $original_image_height = imagesy($original_image);
        $new_image = imagecreatetruecolor($width, $height);
        imagesavealpha($new_image, true);
        $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
        imagefill($new_image, 0, 0, $color);
        if ($original_image_height < $height || $original_image_width < $width) {
            if ($original_image_height < $height && $original_image_width < $width) {
                $dx = round(($width - $original_image_width) / 2);
                $dy = round(($height - $original_image_height) / 2);
            } else if ($original_image_height < $height) {
                $dy = round(($height - $original_image_height) / 2);
            } else {
                $dx = round(($width - $original_image_width) / 2);
            }
            imagecopy($new_image, $original_image, $dx, $dy, 0, 0, $original_image_width, $original_image_height);
        } else {
            $width_ratio = $original_image_width / $width;
            $height_ratio = $original_image_height / $height;

            if ($width_ratio > $height_ratio) {
                $sx = round(($original_image_width - $width * $height_ratio) / 2);
                $original_image_width = round($width * $height_ratio);
            } elseif ($width_ratio < $height_ratio) {
                $sy = round(($original_image_height - $height * $width_ratio) / 2);
                $original_image_height = round($height * $width_ratio);
            }
            imagecopyresampled($new_image, $original_image, $dx, $dy, $sx, $sy, $width, $height, $original_image_width, $original_image_height);
        }
        $this->outputThumb($new_image, $newName, $imageExtension);
        return $newName;
    }

    private function outputThumb($newImage, $newName, $newExtension)
    {
        Storage::disk('public')->makeDirectory('temp-cache/');
        $temp = Storage::disk('public')->path('temp-cache/');
        switch ($newExtension) {
            case '.webp' && function_exists('imagewebp'):
                imagewebp($newImage, $temp . $newName);
                break;
            case '.png' && function_exists('imagepng'):
                imagewebp($newImage, $temp . $newName);
                break;
            case '.jpg' && function_exists('imagejpeg'):
                imagejpeg($newImage, $temp . $newName);
                break;
            default:
                break;
        }
        Storage::disk(config('image.image_cache_storage'))->put('image-cache/' . $newName, Storage::disk('public')->get('temp-cache/' . $newName), 'public');
        imagedestroy($newImage);
        Storage::disk('public')->delete('temp-cache/' . $newName);
        return true;
    }

    private function getImageType($source, $originalFilePath)
    {
        switch ($source) {
            case $source === 'jpeg' or $source === 'jpg':
                return imagecreatefromjpeg($originalFilePath);
            case $source === 'png':
                return imagecreatefrompng($originalFilePath);
            case $source === 'gif':
                return imagecreatefromgif($originalFilePath);
            case $source === 'webp':
                return imagecreatefromwebp($originalFilePath);
            default:
                break;
        }
    }
}
