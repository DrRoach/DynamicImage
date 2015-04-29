# DynamicImage
Create dynamically sized images on the fly so that they don't need to be resized when added or uploaded.

The aim of this script is to create images of any size as quickly and efficiently as possible so that different sized images don't need to be created whenever you want to add a image to your site or a user uploads a image to your site. The advantage of this is that you will always have the image size that you want even if you need a new required image size half way through a project.

This script has a fully functioning file based cache system which renews when original images are updated. It is also 100% secure as it doesn't make use of `eval()` or any other potentially evil functions.

At the moment, only `JPG` and `PNG` images are supported.

###Params
`filename` - `required` This is the original image filename including it's extension, for example, `snorkel.jpg`.

`width` - `required` This is the desired width of the image.

`height` - `required` This is the desired height of the image.

`imageDirectory` - This is the directory that all of your original images are held in. This cannot move 'up' through your filesystem to keep the script as a whole more secure. This MUST also end with a trailing slash.

###Example AJAX request to load an image from a seperate domain

Request URL

`http://yousite.com/loadimage.php?filename=snorkel.jpg&width=500&height=500`

loadimage.php
```PHP
require_once 'DynamicImage.php';
$DI = new DynamicImage();
echo json_encode($DI->file);
exit
?>
```

HTML
```HTML
<img src="<?=$DI->file?>">
```
