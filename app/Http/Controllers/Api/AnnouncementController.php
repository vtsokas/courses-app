<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Announcement::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return Announcement::create(
            $request->validate([
                'title' => 'required',
                'body' => 'required',
                'date' => 'required|date',
                'tags' => 'nullable|string'
            ])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Announcement::findOrFail($id);
    }

    /**
     * Find entry by id.
     */
    public function find(string $id)
    {
        return Announcement::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $announcement = $this->find($id);

        $announcement->update($request->all());

        return $announcement;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $announcement = Announcement::findOrFail($id);

        $announcement->delete();
        
        return response()->json(['message'=> 'Record deleted']);
    }
}
