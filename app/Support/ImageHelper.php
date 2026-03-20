<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageHelper
{
    /**
     * Comprime y convierte una imagen a WebP, la guarda en disco 'public'
     * y devuelve el path relativo (ej: profile-photos/abc123.webp).
     */
    public static function storeAsWebP(
        UploadedFile $file,
        string $directory = 'profile-photos',
        int $maxWidth = 800,
        int $quality = 80
    ): string {
        $filename = Str::uuid() . '.webp';
        $path     = $directory . '/' . $filename;

        $image = Image::read($file->getRealPath())
            ->scaleDown(width: $maxWidth)   // solo reduce, nunca agranda
            ->toWebp(quality: $quality);

        Storage::disk('public')->put($path, $image);

        return $path;
    }
}