<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Support\ImageHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'name'               => ['required', 'string', 'max:255'],
            'username'           => ['required', 'string', 'min:3', 'max:30', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email'              => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'           => ['required', 'confirmed', 'min:8'],
            'age'                => ['required', 'integer', 'min:18', 'max:99'],
            'gender_identity'    => ['required', 'string', 'in:man,woman'],
            'sexual_preference'  => ['required', 'string', 'in:man,woman,both'],
            'bio'                => ['required', 'string', 'min:10', 'max:500'],
            'profile_photo'      => ['required', 'file', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ], [
            // ... tus mensajes de validación existentes ...
        ])->validate();

        /** @var UploadedFile $photo */
        $photo     = $input['profile_photo'];
        $photoPath = ImageHelper::storeAsWebP($photo);   // ← CAMBIO

        return User::create([
            'name'               => $input['name'],
            'username'           => $input['username'],
            'email'              => $input['email'],
            'password'           => Hash::make($input['password']),
            'age'                => $input['age'],
            'gender_identity'    => $input['gender_identity'],
            'sexual_preference'  => $input['sexual_preference'],
            'bio'                => $input['bio'],
            'profile_photo_path' => $photoPath,
        ]);
    }
}