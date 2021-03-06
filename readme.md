### NOTE: This package is still in development, you are very welcome to contribute

# Documentation

## Installation

Use below command to install package to your existing laravel project.

    composer require tinkeshwar/imager

After successfull installation publish the config file with following command:

> **_Install Automatically_**
>
>     php artisan imager:install

> **_Install Manually_**
> php artisan vendor:publish
>
> and select **`Tinkeshwar\Imager\ImagerServiceProvider`** this will
> copy image.php into your app/config.
>
> Run
>
>     php artisan migrate
>
> to migrate the image table schema into you database.

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
    			    'path'=>'public/' //sample path used in above function
    			]);
    		}
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

Once the file is uploaded into the system, it can be access as

    <Your Host>/thumb/{image_id}/{height}/{width}

> example

    `http://localhost:8000/thumb/1/100/100`

If image in cache exist for following `image_id` and aspected ratio it will display existing image or if not exist it will generate a image with required aspect in storage defined in the `config/image.php`

### NOTE:

If image is re-uploaded it has a cache image of previous image you need delete the cache image.

There are following two options to clear cache:

1.  By artisan command
    `php artisan imager:clear`
2.  By calling following facade method
    `Imager::listCache()`
