# ImagePresenter plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require xavier83ar/image-presenter
```

## Usage

Load the plugin

```php
Plugin::load('ImagePresenter', ['routes' => true]);
```

Configure your variants...

```php
'ImagePresenter' => [
    'variants' => [
        'thumbnail' => [
            'size' => [350, 250],
            'mode' => ImageInterface::THUMBNAIL_OUTBOUND,
            'filter' => ImageInterface::FILTER_LANCZOS
        ],
        'mini' => [
            'operation' => 'thumbnail',
            'size' => [120, 120],
            'mode' => ImageInterface::THUMBNAIL_INSET,
        ],
        'other' => [
            'operation' => function (ImageInterface $imagine) {
                return $imagine->resize(new Box(400, 300))->rotate(90);
            }
        ],
        'amazing' => [
            'operation' => function (ImageInterface $imagine) {
                return $imagine
                    ->resize(new Box(600, 320))
                    ->effects()->grayscale()->blur(5);
            }
        ],
    ],
]
```

Any variant which its name starts with "thumbnail" will use thumbnail operation mode. Right now there two operation modes:
thumbnail, which sets up a `ImageInterface::thumbnail()` operation, and closure mode, which allows you to pass a 
closure which receive an `ImageInterface` to play with.  

This helpers uses imagine/imagine package for image manipulation operations, see https://imagine.readthedocs.org/en/latest/ 
for more information.

### Showing up images

Finally load and use ImageHelper

```php
class AppView extends View
{
    public function initialize()
    {
        $this->loadHelper('ImagePresenter.Image');
    }
}
```

On your templates

```php
<img src="<?= $this->Image->variant($img, 'thumbnail') ?>" alt="">
```

`ImagePresenter\View\Helper\ImageHelper::variant()` method will only check if exists a file for that variant, if it does, then it will return the path to that file relative to webroot, if not, it will return the path to the `PresenterController` which takes care of generate that variant and serve the file.

This way variants are created only when needed, and only once.

