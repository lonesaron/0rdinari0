<?php

namespace App\Http\Controllers;

use App\Models\chirp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

use function PHPUnit\Framework\returnCallback;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : View
    {
        return view('chirps.index', [
            'chirps' => Chirp::with('user')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'video' => 'nullable|file|mimetypes:video/mp4'
        ]);
 
        if ($request->has('video')){
            $file=$request->file('video');
            $extension = $file->getClientOriginalExtension();

            $filename = time().'.'.$extension;
            $file->move('uploads/category/', $filename);
        }

        $request->user()->chirps()->create($validated);
 
        return redirect(route('chirps.index'));
    }

    public function search(Request $request){
        $search = $request->search;

        $chirps =Chirp::where(function($query) use ($search){
            $query->where('message', 'like', "%$search%");

        })
        ->get();

        return view('chirps.index', compact('chirps', 'search'));
    }

    /**
     * Display the specified resource.
     */
    public function show(chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(chirp $chirp): View
    {
        Gate::authorize('update', $chirp);
 
        return view('chirps.edit', [
            'chirp' => $chirp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        Gate::authorize('update', $chirp);
 
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
 
        $chirp->update($validated);
 
        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(Chirp $chirp): RedirectResponse
    {
        Gate::authorize('delete', $chirp);
 
        $chirp->delete();
 
        return redirect(route('chirps.index'));
    }
}
