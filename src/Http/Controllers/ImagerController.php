<?php

namespace Tinkeshwar\Imager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tinkeshwar\Imager\Models\Image;

class ImagerController extends Controller
{

    public $imageExtension;

    function __construct()
    {
        dd(config('image'));
        $this->imageExtension = config('image.image_extension') ?? '.png';
    }

    /**
     * Get dynamic image
     *
     * @return image url
     */
    public function index($id, $height, $width, $thumb_name = NULL)
    {
        $file = $this->getFile($id, $height, $width, $thumb_name);
        if (function_exists('imagewebp') && $this->imageExtension == '.webp') {
            header('Content-type:image/webp');
        } else {
            header('Content-type:image/png');
        }
        header('Cache-Control: max-age=691200, public');
        header('Expires: Mon, ' . date('d M Y', strtotime("+ 8days")) . ' 05:00:00 GMT');
        header('Last-Modified: Mon, ' . date('d M Y', strtotime("- 1days")) . ' 05:00:00 GMT');
        readfile($file);
        exit();
    }

    /**
     * Get Image
     *
     *
     */
    protected function getFile($id, $height, $width, $thumb_name = NULL)
    {
        $image = Image::find($id);
        $file = 'https://dummyimage.com/' . $width . 'x' . $height . '&text=no-image';
        if (isset($image->id) && $image->path && $image->name) {
            if (Storage::disk(config('image.image_storage'))->exists($image->path . $image->name)) {
                $name = '';
                $name = Str::snake($thumb_name ? $thumb_name : $id) . '-' . $height . 'x' . $width . $this->imageExtension;
                $image_path = $image->path . $image->name;
                if (!Storage::disk(config('image.image_cache_storage'))->exists($name)) {
                    $name = $this->newThumb($image_path, $height, $width, $name);
                }
                $file = Storage::disk(config('image.image_storage'))->path('image-cache/' . $name);
            }
        }
        return $file;
    }

    private function newThumb($filename, $height, $width, $newName)
    {
        $dx = $dy = $sx = $sy = 0;
        Storage::disk(config('image.image_storage'))->makeDirectory('image-cache/');
        $originalFilePath = Storage::disk(config('image.image_storage'))->path($filename);
        $newPath = Storage::disk(config('image.image_storage'))->path('image-cache/');
        $source = pathinfo($originalFilePath);
        $original_image = $this->getImageType($source, $originalFilePath);
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
        if (function_exists('imagewebp') && $this->imageExtension == '.webp') {
            imagewebp($new_image, $newPath . $newName);
        } else {
            imagepng($new_image, $newPath . $newName);
        }
        imagedestroy($new_image);
        return $newName;
    }

    private function getImageType($source, $originalFilePath)
    {
        if ($source['extension'] === 'jpeg' or $source['extension'] === 'jpg') {
            return imagecreatefromjpeg($originalFilePath);
        }
        if ($source['extension'] === 'png') {
            return imagecreatefrompng($originalFilePath);
        }
        if ($source['extension'] === 'gif') {
            return imagecreatefromgif($originalFilePath);
        }
    }
}
