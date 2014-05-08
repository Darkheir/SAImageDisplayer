SAImageDisplayer
===========

SAImageDisplayer is a Yii Widget providing a better display of images in the views.

##Description 
This extension resizes images and thumbnail only when they are called and if resized image or his thumbnail not exists. This means that you can upload images without take care of dimensions.

This extension is based on justintimeimageresizer extension adding some improvements :

* We can Specify an unlimited number of sizes, we are no more limited to "Big" or "Thumbs"
* We can specify a default image to display if the original image doesn't exists
* we can specify some groups: if we don't want to mix multiple type of image, by specifying the type it will store each image type in a separate folder
* Add the possiblity to define the class, id and style in image tag
* We can define where we would like to store the images (inside the webroot folder)
* We can choose the name of the folder storing the originals images
* If the folder that should contain the resized image doesn't exist then the widget try to create it before throwing an error
* Since V1.1: We can display the original file (with the original size)
* Since V1.2: We can generate the newly sized image whitout displaying it on the screen

(For others updates see below)

##Requirements 

* Yii 1.1 or above
* PHP 5.3
* image (yii extension)

##Installation

To install this extension it's really easy, all you have to do is extract the `SAImageDisplayer.php` file in your ``extension` folder!


## Main Configuration

In this part we are going to configure the extension. This part is optional but I encourage you to do it because later you won't have to put all the variables defined in the config in each of your call to the widget!


There are some params that won't change from one call to the widget to another. This is why I encourage you to use the ability that yii offers to globally configure some widgets option!
To do it edit your configuration main file (in general `config/main.php`) and add the following code:
```php
return array(
    ...
    'components'=>array(
        'widgetFactory'=>array(
            'widgets'=>array(
                ...
                'SAImageDisplayer'=>array(
                    'theOption'=>theValue,
                    'otherOption'=>otherValue,
                ),
            ),
        ),
    ),
);
```

### Params you should put in this configuration file
The options that I encourage you to put in this configuration are:

* The path to the dir that will contain originals and resized folders. The root of this param will be `webroot`. Default to `images`:

```php
'baseDir' => 'images', 
```
* The name of the folder holding the originals images. default to `originals`:

```php
'originalFolderName' => 'originals', 
```

* The different available sizes for the plugin (when you are not using `groups`):

```php
'sizes' =>array(
    'nameOfTheSize' => array('width' => width, 'height' => height),
   ),
```

* The groups and the sizes going with it :

```php
'groups' => array(
    'nameOfTheGroup' => array(
        'nameOfTheSize' => array('width' => width, 'height' => height),
      ),
 ),
 ```
### Example of params

#### For the param `sizes`
The following example:

```php
'sizes' =>array(
    'tiny' => array('width' => 40, 'height' => 30),
    'big' => array('width' => 640, 'height' => 480),
    'thumb' => array('width' => 400, 'height' => 300),
   ),
```
 will give the following structure (if `images` is defined as `baseDir` and `originals` as `originalFolderName`): 

    images/ 
      big/
      originals/
      thumb/
      tiny/


#### For the param `groups`
The following example:
```php
'groups' => array(
    'news' => array(
        'tiny' => array('width' => 40, 'height' => 30),
        'big' => array('width' => 640, 'height' => 480),
      ),
    'reviews' => array(
        'thumb' => array('width' => 400, 'height' => 300),
     ), 
 ),
 ```
will give the following structure (if `images` is defined as `baseDir` and `originals` as `originalFolderName`):

     images/ 
        news/
           big/
           originals/
           tiny/
        reviews/
           originals/
           thumb/ 

#### Complete configuration example

Here is a complete configuration example that you could use:

```php
return array(
    ...
    'components'=>array(
        'widgetFactory'=>array(
            'widgets'=>array(
                ...
                'SAImageDisplayer'=>array(
                    'baseDir' => 'images',
                    'originalFolderName'=> 'originals',
                    'sizes' =>array(
                        'tiny' => array('width' => 40, 'height' => 30),
                        'big' => array('width' => 640, 'height' => 480),
                        'thumb' => array('width' => 400, 'height' => 300),
                    ),
                    'groups' => array(
                        'news' => array(
                            'tiny' => array('width' => 40, 'height' => 30),
                            'big' => array('width' => 640, 'height' => 480),
                          ),
                        'reviews' => array(
                            'thumb' => array('width' => 400, 'height' => 300),
                         ), 
                    ),
                ),
            ),
        ),
    ),
);
```

## Usage

For the usage, I'll assume that you have followed the configuration step above. If not, each params defined before need to be put in each widget call in your views.

All the originals images have to be in the folder defined by `originalFolderName`. If you are using the option `group` then the original image has to be in the folder `groupName/originalFolderName`

### Simplest example
To simply display an image with a given size here is the syntax:
```php
<?php $this->widget('ext.SAImageDisplayer', array(
    'image' => 'yourImage.png',
    'size' => 'thumb',
)); ?>
```
### More info in your image tag
Now you can simply add a title to the image:
```php
<?php $this->widget('ext.SAImageDisplayer', array(
    'image' => 'yourImage.png',
    'size' => 'thumb',
    'title' => 'My super title',
)); ?>
```
This way you can set:

* The title by using the param `title`
* The alternative text by setting the param `alt`
* The class using `class`
* The id => `id`
* The style => `style`

### Display the original sized file (since V1.1)
If you want to display the original file with its original size (for a gallery for example) you can do it by not setting `size` or setting it to `original`:
```php
<?php $this->widget('ext.SAImageDisplayer', array(
    'image' => 'yourImage.png',
)); ?>
```

### And if my image could not exists?

Not sure your image really exists? No need to check it, the plugin do it for you and allow you to define a default image!

This image as to be in your original folder and will be resized to match the `size` you set.
```php
<?php $this->widget('ext.SAImageDisplayer', array(
    'image' => 'yourImage.png',
    'size' => 'thumb',
    'defaultImage' => 'default.png',
)); ?>
```

### And if I don't want to mix severals types of images?

Let's say you have some images representing the news on your site and some others the reviews. You don't want to mix them because after it's a mess to now what image belongs to what type. I agree with you, everything need to be clean!

This is why you can declare a group for each image you are displaying! The plugin will work in the folder corresponding to the group name.

So to display a new image I'll use:
```php
<?php $this->widget('ext.SAImageDisplayer', array(
    'image' => 'yourImage.png',
    'size' => 'thumb',
    'defaultImage' => 'default.png',
    'group' => 'news',
)); ?>
```
And for a review:
```php
<?php $this->widget('ext.SAImageDisplayer', array(
    'image' => 'yourImage.png',
    'size' => 'thumb',
    'defaultImage' => 'default.png',
    'group' => 'reviews',
)); ?>
```

And in your image folder you'll have something clean like:

    images/ 
        news/
           big/
           originals/
           thumb/
        reviews/
           originals/
           thumb/

Be careful, since you are using the option `group`, the original and the default images have to be in the folder `groupName/originalFolderName`!

### If I want to group some images and not some others?

Of course we can use `group` and `size` for some images and just specify `size` for others! 
This way you'll endup with a structure looking like:

     images/
        big/
        originals/
        news/
           big/
           originals/
           tiny/
        reviews/
           originals/
           thumb/ 
        thumb/
        tiny/

### If I want generate a new image size whitout displaying it?

Since v1.2 we can generate a new image size whitout displaying the image on the screen!
All we have to do is set the option `displayImage` to false in the widget. All the other operations will be performed except displaying the image tag.

```php
<?php $this->widget('ext.SAImageDisplayer', array(
    'image' => 'yourImage.png',
    'size' => 'thumb',
    'defaultImage' => 'default.png',
    'displayImage' => false,
)); ?>
```
  

## List of all the available options

* `image` : Name of the image to display (with the extension)
* `size` : Name of the size to display. The size has to be declared in `sizes` or `groups` if the option `group` is used
* `defaultImage` : Name of the default image to display if `image` doesn't exist
* `sizes` : Array containing all the possible sizes than can be called when `group` is not defined. The array has to be with the following structure:

```php
'sizes' =>array(
    'nameOfTheSize' => array('width' => width, 'height' => height),
   ),
```
* `groups` : Array containing all the groups and the associated sizes that the widget may use. The array has to be with the following structure:

```php
'groups' => array(
    'nameOfTheGroup' => array(
        'nameOfTheSize' => array('width' => width, 'height' => height),
      ),
 ),
 ```
* `baseDir` : The path to the dir that will contain originals and resized folders. The root of this param will be `webroot`. Default to `images`
* `originalFolderName` : The name of the folder holding the originals images. default to `originals`
* `title` : Title of the image tag
* `alt` : Alternative name of the image
* `style` : Style of the image tag
* `id` : Id of the image tag
* `class` : Class of the image tag
* `group` : Name of the group the image belongs to
* `displayImage` : Weither or not we should display the image on the screen. Default to true.
* `resizeMode` : How images should be resize by the image extension. Available options are SAImageDisplayer::NONE, SAImageDisplayer::AUTO, SAImageDisplayer::HEIGHT, SAImageDisplayer::WIDTH. Default to AUTO.
* `othersAttributes` : An array `array('attributeName' => 'value')` keeping all the additional attribute we wish to add to the image tag.

## Please Note

Don't hesitate to ask for help or tell me any issues you could meet while using the plugin!
If you see some typo errors in this extension page please tell me, English is not my mother tongue so there might have plenty of them.

Feel free to provide some ideas of improvment and if you provide some pull request on github I'll most definitely examinate your code and add it to the widget if I think it adds something!

## Links

* [The Github page](https://github.com/Darkheir/SAImageDisplayer)

##Updates
* **1.1:** Add the ability to display the image with its original size
* **1.2:** Add the ability to not display the generated image (it will only resize the original one in the right folder)
* **1.3:** Add the base url to the image path when displaying it
* **1.4:** Adding "width" and "height" tags to the generated image since it's a good practice: [PageSpeed best practices](https://developers.google.com/speed/docs/best-practices/rendering?hl=fr#SpecifyImageDimensions) 
* **1.5** Now Image extension can use ImageMagick or GD library to resize images. (Before it was only GD no matter what was the user configuration).
* **1.6:** Add the ability to choose which resize mode to use (NONE, AUTO, WIDTH, HEIGHT), add the ability to add other attributes to the image tag.
* **1.7** Thanks to Oreolek the exceptions are now handled internally if not in debug mode.

