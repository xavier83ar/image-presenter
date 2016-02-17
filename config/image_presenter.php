<?php
/**
 * Created by javier
 * Date: 17/02/16
 * Time: 08:26
 */
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

return [
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
];
