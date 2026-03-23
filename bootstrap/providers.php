<?php
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    \Intervention\Image\Laravel\InterventionImageServiceProvider::class,
];