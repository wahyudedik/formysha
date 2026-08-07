<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\Child;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildController extends Controller
{
    /**
     * Display a listing of the user's children.
     */
    public function index(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->withCount('familyMembers')
            ->orderBy('date_of_birth', 'asc')
            ->get();

        return view('children.index', [
            'children' => $children,
        ]);
    }

    /**
     * Show the form for creating a new child.
     */
    public function create(): View
    {
        return view('children.create');
    }

    /**
     * Store a newly created child in storage.
     */
    public function store(StoreChildRequest $request): RedirectResponse
    {
        $child = $request->user()->children()->create($request->validated());

        return redirect()->route('children.show', $child)
            ->with('status', 'Profil anak berhasil dibuat!');
    }

    /**
     * Display the specified child.
     */
    public function show(Request $request, Child $child): View
    {
        $child->load('familyMembers');

        return view('children.show', [
            'child' => $child,
        ]);
    }

    /**
     * Show the form for editing the specified child.
     */
    public function edit(Request $request, Child $child): View
    {
        return view('children.edit', [
            'child' => $child,
        ]);
    }

    /**
     * Update the specified child in storage.
     */
    public function update(UpdateChildRequest $request, Child $child): RedirectResponse
    {
        $child->update($request->validated());

        return redirect()->route('children.show', $child)
            ->with('status', 'Profil anak berhasil diperbarui!');
    }

    /**
     * Remove the specified child from storage.
     */
    public function destroy(Request $request, Child $child): RedirectResponse
    {
        $child->delete();

        return redirect()->route('children.index')
            ->with('status', 'Profil anak berhasil dihapus.');
    }
}
