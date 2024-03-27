# THIS PACKAGE CAN CONVERT YOUR IMAGE IN TO WEBP AND DESIRED RESOLUTION

## HOW TO USE 

*add this line in the composer json file*

**composer require tantra-gyan/image-convert:v1.0.0 -w**

*run this command to install the package*

## Now, go through these steps for converting image into webp and desired resolution using our library :

*firstly you need to use the helper function on the top of the file*
**use TantraGyan\ImageConvert\helper\ImageHelper;**

## Now let's see the available functions
- saveImage() 
- resizeSaveImage()
- saveWebpImage()
- resizeSaveWebpImage()

## Save Image Function

*This function accepts 3 param*
- *path ('uploads/directory')* 
- *image*
- *objectStore(optional, true/false , 0/1)*

*Here is an example code snippet*
```php

use TantraGyan\ImageConvert\helper\ImageHelper;

Route::post('image-upload' , function(Request $request){
    return ImageHelper::saveImage("uploads/post" , $request->image);
});

```
*In above code we are saving normal jpg images with object store disable by default*

## Resize & Save Image

*This function accepts 5 param*
- *path ('uploads/directory')* 
- *image*
- *hight*
- *width*
- *objectStore(optional, true/false , 0/1)*

*Here is an example code snippet*
```php

use TantraGyan\ImageConvert\helper\ImageHelper;

Route::post('image-upload' , function(Request $request){
    return ImageHelper::resizeSaveImage("uploads/post" , $request->image , 320 , 320);
});

```
*In above code we are saving normal jpg/png images with object store disable by default*

## saveWebpImage function

*This function works same as `saveImage` but it can convert you png/jpg image into webp formate `webpImage`.*

*This function accepts 3 param*
- *path ('uploads/directory')* 
- *image*
- *objectStore(optional, true/false , 0/1)*

*Here is an example code snippet*
```php

use TantraGyan\ImageConvert\helper\ImageHelper;

Route::post('image-upload' , function(Request $request){
    return ImageHelper::saveWebpImage("uploads/post" , $request->image);
});

```
*In above code we are saving normal jpg images with object store disable by default*

## Resize & Save Image

*This function works same as `resizeSaveImage` but it can convert you png/jpg image into webp formate `resizeSaveWebpImage`.*

*This function accepts 5 param*
- *path ('uploads/directory')* 
- *image*
- *hight*
- *width*
- *objectStore(optional, true/false , 0/1)*

*Here is an example code snippet*
```php

use TantraGyan\ImageConvert\helper\ImageHelper;

Route::post('image-upload' , function(Request $request){
    return ImageHelper::resizeSaveWebpImage("uploads/post" , $request->image , 320 , 320);
});

```
*In above code we are saving normal jpg/png images with object store disable by default*


# HOW WE CAN USE THE OBJECT STORAGE FUNCTIONALITY 

*To save you Image into Object storage like digital ocean*

## **Create Config File in you `app/config` folder with name `ImageConvert.php`**

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
     */

    'do_spaces'    => [
        'driver'         => 's3',
        'key'            => env('DO_KEY'),
        'secret'         => env('DO_SECRET'),
        'region'         => env('DO_REGION'),
        'bucket'         => env('DO_BUCKET'),
        "endpoint"       => env("DO_ENPOINT"),
        "originendpoint" => env("ORIGIN_ENDPOINT"), //full end point of the do spaces
    ],

    
];
```

*specify these things to getting started*

## To save store you image into Object store you need to pass `true` or `1` at the end of the function.

