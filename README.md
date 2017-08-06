# DynamicImage
Create dynamically sized images on the fly so that they don't need to be resized when added or uploaded.

The goal of this script is to allow images to be generated on the fly to any size. This is done by using PHPs imagick libraries to creating images in a fraction of a second.

This script has a fully functioning file based cache system which renews when original images are updated. Images are generated to scale and generated to high resolution. There is also support for images that are hosted on seperate endpoints.

At the moment, only `JPG` and `PNG` images are supported.

### Params

The parameters are expected as a `key`, `value` array and passed when the object is created.

`filename` - `required` This is the original image filename including it's extension, for example, `snorkel.jpg`. This image can be of any size any will be rendered in near perferct resolution.

`width` - `required` This is the desired width of the image.

`height` - `required` This is the desired height of the image.

`imageDirectory` - This is the directory that all of your original images are held in. This cannot move 'up' through your filesystem to keep the script as a whole, more secure. This MUST also end with a trailing slash.

`debug` - If you wish for errors to be turned on, then you can set this to `true` to display any errors that may be occuring.

`image_missing` - If the requested image isn't found then this image is returned instead. This means that you never get a blank response and there's always something that you can display.

`exceptions` - Parameter to set whether or not you wish for exceptions to be thrown by the script. The default value is `false` so if you want exceptions to be thrown then you must set this to `true` when creating the object.

`validate_image` - Flag to indicate whether or not you want to check for the requested images' existance before looking to see if the image exists in cache. This means that if the image gets updated after it's been saved to cache then the image wont be regenerated. By default this is set to `true`.

### Example AJAX request to load an image from a seperate domain

Request URL

`http://yousite.com/loadimage.php?filename=snorkel.jpg&width=500&height=500`

loadimage.php
```PHP
require_once 'DynamicImage.php';

$settings = [
    'filename' => $_GET['filename'],
    'width' => $_GET['width'],
    'height' => $_GET['height']
];

$DI = new DynamicImage($settings);
echo json_encode($DI->file);
exit
?>
```

HTML
```HTML
<img src="<?=$DI->file?>">
```

### Potential Issues

Make sure that your cache folder is writable and that the apache user has permission to write on your machine. Usually user `www-data`.
