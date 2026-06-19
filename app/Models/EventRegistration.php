<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = [
        'full_name',
        'mobile',
        'gender',
        'email',
        'city',
        'event_date',
        'photo_original',
        'photo_cropped',
        'generated_banner',
    ];
    protected $casts = [
        'event_date' => 'date',
    ];
    // City => [date, banner_image_filename]
    public static function cityData()
    {
        return [
            'Ghaziabad (16 June)' => [
                'date'     => '16 June',
                'banner'   => 'ghaziabad-16-june.png',
                'banner_f' => 'ghaziabad-16-june-f.png',
            ],
            'Meerut (18 June)' => [
                'date'     => '18 June',
                'banner'   => 'meerut-18-june.png',
                'banner_f' => 'meerut-18-june-f.png',
            ],
            'Bareilly (29 June)' => [
                'date'     => '29 June',
                'banner'   => 'bareilly-29-june.png',
                'banner_f' => 'bareilly-29-june-f.png',
            ],
            'Bareilly (30 June)' => [
                'date'     => '30 June',
                'banner'   => 'intro.png',
                'banner_f' => 'intro-f.png',
            ],
            'Lucknow (8 July)' => [
                'date'     => '8 July',
                'banner'   => 'lucknow-08-july.png',
                'banner_f' => 'lucknow-08-july-f.png',
            ],
            'Lucknow (9 July)' => [
                'date'     => '9 July',
                'banner'   => 'lucknow-09-july.png',
                'banner_f' => 'lucknow-09-july-f.png',
            ],
            'Unnao (10 July)' => [
                'date'     => '10 July',
                'banner'   => 'unnao-10-july.png',
                'banner_f' => 'unnao-10-july-f.png',
            ],
            'Ayodhya (17 July)' => [
                'date'     => '17 July',
                'banner'   => 'ayodhya-17-july.png',
                'banner_f' => 'ayodhya-17-july-f.png',
            ],
            'Gorakhpur (21 July)' => [
                'date'     => '21 July',
                'banner'   => 'gorakhpur-21-july.png',
                'banner_f' => 'gorakhpur-21-july-f.png',
            ],
            'Gorakhpur (22 July)' => [
                'date'     => '22 July',
                'banner'   => 'gorakhpur-22-july.png',
                'banner_f' => 'gorakhpur-22-july-f.png',
            ],
            'Lucknow (24 July)' => [
                'date'     => '24 July',
                'banner'   => 'lucknow-24-july.png',
                'banner_f' => 'lucknow-24-july-f.png',
            ],
            'Lucknow (28 July)' => [
                'date'     => '28 July',
                'banner'   => 'lucknow-24-july.png',
                'banner_f' => 'lucknow-28-july-f.png',
            ],
        ];
    }
}
