<?php

namespace App\Actions\Fortify;

use App\Models\User;
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
            'gender_identity'    => ['required', 'string', 'in:man,woman,non_binary,other'],
            'sexual_preference'  => ['required', 'string', 'in:hetero,homo,bi,pan'],
            'bio'                => ['required', 'string', 'min:10', 'max:500'],
            'profile_photo'      => ['required', 'file', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'name.required'              => 'El nombre es obligatorio.',
            'username.required'          => 'El nombre de usuario es obligatorio.',
            'username.min'               => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.unique'            => 'Este nombre de usuario ya está en uso.',
            'username.regex'             => 'El nombre de usuario solo puede contener letras, números y guiones bajos.',
            'email.required'             => 'El email es obligatorio.',
            'email.unique'               => 'Este email ya está registrado.',
            'password.required'          => 'La contraseña es obligatoria.',
            'password.confirmed'         => 'Las contraseñas no coinciden.',
            'password.min'               => 'La contraseña debe tener al menos 8 caracteres.',
            'age.required'               => 'La edad es obligatoria.',
            'age.min'                    => 'Debes tener al menos 18 años.',
            'age.max'                    => 'La edad no puede superar 99 años.',
            'gender_identity.required'   => 'Selecciona con qué te identificas.',
            'gender_identity.in'         => 'Selecciona una opción de género válida.',
            'sexual_preference.required' => 'Selecciona tu preferencia.',
            'sexual_preference.in'       => 'Selecciona una preferencia válida.',
            'bio.required'               => 'Cuéntanos algo sobre ti.',
            'bio.min'                    => 'La bio debe tener al menos 10 caracteres.',
            'bio.max'                    => 'La bio no puede superar 500 caracteres.',
            'profile_photo.required'     => 'La foto de perfil es obligatoria.',
            'profile_photo.image'        => 'El archivo debe ser una imagen.',
            'profile_photo.max'          => 'La imagen no puede superar 5 MB.',
            'profile_photo.mimes'        => 'La imagen debe ser JPG, PNG o WebP.',
        ])->validate();

        /** @var UploadedFile $photo */
        $photo = $input['profile_photo'];
        $photoPath = $photo->store('profile-photos', 'public');

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