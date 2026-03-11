<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'name'               => ['required', 'string', 'max:255'],
            'username'           => ['required', 'string', 'min:3', 'max:30', 'unique:users,username'],
            'email'              => ['required', 'email', 'unique:users,email'],
            'password'           => ['required', 'confirmed', 'min:8'],
            'age'                => ['required', 'integer', 'min:18', 'max:99'],
            'gender_identity'    => ['required', 'string'],
            'sexual_preference'  => ['required', 'string'],
            'bio'                => ['required', 'string', 'min:10', 'max:500'],
            'profile_photo'      => ['required', 'image', 'max:5120'],
        ])->validate();

        $photoPath = $input['profile_photo']->store('profile-photos', 'public');

        return User::create([
            'name'               => $input['name'],
            'username'           => $input['username'],
            'email'              => $input['email'],
            'password'           => $input['password'],
            'age'                => $input['age'],
            'gender_identity'    => $input['gender_identity'],
            'sexual_preference'  => $input['sexual_preference'],
            'bio'                => $input['bio'],
            'profile_photo_path' => $photoPath,
        ]);
    }
}