<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPartyController extends Controller
{
    public function index()
    {
        $parties = Party::withCount('users')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.parties.index', compact('parties'));
    }

    public function create()
    {
        return view('admin.parties.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'description'             => 'nullable|string|max:1000',
            'location'                => 'nullable|string|max:255',
            'starts_at'               => 'required|date',
            'registration_opens_at'   => 'nullable|date|before:starts_at',
            'registration_closes_at'  => 'nullable|date|before:starts_at',
            'cover_image'             => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('parties', 'public');
        }

        $data['created_by'] = auth()->id();
        $data['status']     = 'registration';

        Party::create($data);

        return redirect()->route('admin.parties.index')
            ->with('success', '¡Fiesta creada correctamente!');
    }

    public function edit(Party $party)
    {
        return view('admin.parties.edit', compact('party'));
    }

    public function update(Request $request, Party $party)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'description'             => 'nullable|string|max:1000',
            'location'                => 'nullable|string|max:255',
            'starts_at'               => 'required|date',
            'registration_opens_at'   => 'nullable|date|before:starts_at',
            'registration_closes_at'  => 'nullable|date|before:starts_at',
            'cover_image'             => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('cover_image')) {
            // Borrar imagen anterior si existe
            if ($party->cover_image) {
                Storage::disk('public')->delete($party->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('parties', 'public');
        }

        $party->update($data);

        return redirect()->route('admin.parties.index')
            ->with('success', 'Fiesta actualizada correctamente.');
    }

    public function updateStatus(Request $request, Party $party)
    {
        $request->validate([
            'status' => 'required|in:draft,registration,countdown,active,finished',
        ]);

        $party->update(['status' => $request->status]);

        return back()->with('success', 'Estado actualizado a "' . $request->status . '".');
    }

    public function destroy(Party $party)
    {
        if ($party->cover_image) {
            Storage::disk('public')->delete($party->cover_image);
        }

        $party->delete();

        return redirect()->route('admin.parties.index')
            ->with('success', 'Fiesta eliminada.');
    }

    public function qr(Party $party)
    {
        return view('admin.parties.qr', compact('party'));
    }
}