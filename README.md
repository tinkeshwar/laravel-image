# Documentation

## Installation

Use the command below to install this package to your existing laravel project.

    composer require tinkeshwar/imager

After successfull installation publish the config file using one of the following commands:

> **_Install Automatically_**
>
> `php artisan imager:install`

> **_Install Manually_**
>
> `php artisan vendor:publish`
>
> and select **`Tinkeshwar\Imager\ImagerServiceProvider`** this will
> copy image.php into your app/config.
>
> Run
>
> `php artisan migrate`
>
> To migrate the image table schema into you database.

## Usage

##### Your model

    <?php
    namespace  App\Models;
    use Illuminate\Database\Eloquent\Model;
    use Tinkeshwar\Imager\Models\Image;

    class <YOUR MODEL>  extends  Model{

        /**
        * One to One Relation with <YOUR> Model
        * A <YOUR MODEL> has one image
        */
        public  function  image(){
    	    return  $this->morphOne(Image::class,'imageable');
        }

    	/**
        * One to Many Relation with <YOUR> Model
        * A <YOUR MODEL> has many images
        */
        public  function  images(){
    	    return  $this->morphMany(Image::class,'imageable');
        }
    }

##### Your controller

    <?php

    namespace  App\Http\Controllers;

    use App\Models\<YOUR MODEL>;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;
    use Tinkeshwar\Imager\Imager;

    class  <YOUR CONTROLLER>  extends  Controller {

        public function store (Request $request){
    	    $data = $request->validate([
    		    'name'=>'required',
    		    'image'=>'image|nullable'
    	    ]);
    	    $yourmodel = <YOUR MODEL>::create($data);
    	    if($request->hasFile('image')){
    		    $yourmodel->image()->create([
    			    'name'=>Imager::moveFile($request->file('image'),'public'), //second parameter is optional, `public` is default
    			    'path'=>'public/', //sample path used in above function
                    'driver' => config('image.image_storage')
    			]);
    		}
    	}


        public function destroy ($imageId){
            Imager::deleteFile($imageId);
        }
    }

##### Your view

    <form  action="<your route>"  method="POST"  enctype="multipart/form-data">
        @csrf
        <div  class="form-group">
    	    <label >Example file input</label>
    	    <input  type="file"  class="form-control-file"  name="image">
        </div>
        <button  type="submit"  class="btn btn-primary">Submit</button>
    </form>

##### Static image usage on blade

    {{thumb($source, $height, $width, $extension)}}

| Parameter  | Usage                                                     |
| ---------- | --------------------------------------------------------- |
| $source    | path/to/image/in/public/folder                            |
| $height    | number                                                    |
| $width     | number                                                    |
| $extension | optional :: `default: .webp` `allowed: .webp, .png, .jpg` |

> example
>
> `<img src={{thumb('/image/bg.png', 100, 100)}}/>`

##### Dynamic image usage on blade

Once the file has been uploaded into the system, it can be access with

    <Your Host>/thumb/{image_id}/{height}/{width}

> example
>
> `http://localhost:8000/thumb/1/100/100`

If an image exists in cache for provided `image_id` and aspect ratio it will display existing image. If not, it will generate an image with the required aspect ratio at the storage defined in `config/image.php`

### NOTE:

If an image is re-uploaded and previous copy of image still exists in cache, then the new image wouldn't be cached, you would need to clear cache of previous image for the new one to take it's place.

There are two options to clear the cache:

1.  By artisan command
    `php artisan imager:clear`
2.  By calling following facade method
    `Imager::listCache()`
