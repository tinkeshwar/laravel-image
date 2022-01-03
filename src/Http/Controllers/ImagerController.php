<?php

namespace Tinkeshwar\Imager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tinkeshwar\Imager\ThumbMaker;
use Tinkeshwar\Imager\Models\Image;

class ImagerController extends Controller
{

    public $imageExtension;

    function __construct()
    {
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
        header(header_string($this->imageExtension));
        header("Cache-Control: max-age=" . config('image.max_age') * 86400 . ", public");
        header('Expires: Mon, ' . date('d M Y', strtotime("+ " . config('image.max_age') . "days")) . ' 05:00:00 GMT');
        header('Last-Modified: Mon, ' . date('d M Y', strtotime("- " . config('image.cache_last_modified') . "days")) . ' 05:00:00 GMT');
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
            if (Storage::disk($image->driver)->exists($image->path . $image->name)) {
                $name = '';
                $name = Str::snake($thumb_name ? $thumb_name : $id) . '-' . $height . 'x' . $width . $this->imageExtension;
                $image_path = $image->path . $image->name;
                if (!Storage::disk(config('image.image_cache_storage'))->exists($name)) {
                    $name = $this->newThumb($image_path, $height, $width, $name, $image->driver);
                }
                $file = Storage::disk(config('image.image_storage'))->path('image-cache/' . $name);
                if (config('image.image_storage') === 's3') {
                    $file = Storage::disk(config('image.image_storage'))->url('image-cache/' . $name);
                }
            }
        }
        return $file;
    }

    private function newThumb($filename, $height, $width, $newName, $driver)
    {
        $imager = new ThumbMaker();
        Storage::disk(config('image.image_storage'))->makeDirectory('image-cache/');
        $originalFilePath = Storage::disk($driver)->url($filename);
        $newPath = Storage::disk(config('image.image_storage'))->path('image-cache/');
        return $imager->createThumb($originalFilePath, $newPath, $height, $width, $newName, $this->imageExtension);
    }
}
