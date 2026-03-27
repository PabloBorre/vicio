<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FlashPhotoOnRegister
{
    public function handle(Request $request, Closure $next)
    {
        // Solo en POST al endpoint de registro
        if ($request->isMethod('POST') && $request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            if ($file && $file->isValid()) {
                $photoData = base64_encode(file_get_contents($file->getRealPath()));
                $photoMime = $file->getMimeType();
                session()->flash('photo_preview', 'data:' . $photoMime . ';base64,' . $photoData);
            }
        }

        return $next($request);
    }
}