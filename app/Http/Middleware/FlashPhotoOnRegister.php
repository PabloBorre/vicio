<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FlashPhotoOnRegister
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('POST') && $request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            if ($file && $file->isValid()) {
                $tmpName = 'tmp_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('temp-photos', $tmpName, 'public');
                session()->flash('photo_preview_path', 'temp-photos/' . $tmpName);
            }
        }

        return $next($request);
    }
}
