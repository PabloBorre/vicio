<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
   public function index(Request $request)
{
    $query = User::with('currentParty')
        ->orderByDesc('created_at');

    // Filtro por fiesta
    $party = null;
    if ($partyId = $request->get('party')) {
        $query->whereHas('parties', fn($q) => $q->where('parties.id', $partyId));
        $party = \App\Models\Party::find($partyId);
    }

    // Búsqueda
    if ($search = $request->get('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Filtro rol/estado
    match ($request->get('filter')) {
        'admins'  => $query->where('is_admin', true),
        'banned'  => $query->where('is_banned', true),
        default   => null,
    };

    $users = $query->paginate(20)->withQueryString();

    return view('admin.users.index', compact('users', 'party'));
}
    public function edit(User $user)
    {
        $user->load('currentParty');
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'username'           => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users')->ignore($user->id)],
            'email'              => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'age'                => ['nullable', 'integer', 'min:18', 'max:99'],
            'gender_identity'    => ['nullable', 'in:man,woman'],
            'sexual_preference'  => ['nullable', 'in:man,woman,bi'],
            'bio'                => ['nullable', 'string', 'max:500'],
            'is_admin'           => ['boolean'],
            'is_banned'          => ['boolean'],
            'profile_photo'      => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
        ]);

        // No permitir que el admin se quite permisos a sí mismo
        if ($user->id === auth()->id() && isset($data['is_admin']) && !$data['is_admin']) {
            return back()->withErrors(['is_admin' => 'No puedes quitarte el rol de admin a ti mismo.']);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Checkbox sin marcar = false
        $data['is_admin']  = $request->boolean('is_admin');
        $data['is_banned'] = $request->boolean('is_banned');

        unset($data['profile_photo']);
        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario {$user->username} actualizado correctamente.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminarte a ti mismo.']);
        }

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

public function toggleBan(User $user)
{
    if ($user->id === auth()->id()) {
        return back()->with('error', 'No puedes banearte a ti mismo.');
    }

    $user->update(['is_banned' => !$user->is_banned]);

    if ($user->is_banned) {
        event(new \App\Events\UserBanned($user));
    }

    $msg = $user->is_banned ? "Usuario {$user->username} baneado." : "Usuario {$user->username} desbaneado.";

    return back()->with('success', $msg);
}

    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes modificar tu propio rol de admin.');
        }

        $user->update(['is_admin' => !$user->is_admin]);

        $msg = $user->is_admin ? "Se ha dado rol admin a {$user->username}." : "Se ha retirado rol admin a {$user->username}.";

        return back()->with('success', $msg);
    }
}