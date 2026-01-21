<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Course::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return Course::create(
            $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string',
                'content' => 'nullable|string',
                'user_id' => 'required|exists:users,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date',
                'status' => 'nullable|string|in:draft,published,archived',
                'tags' => 'nullable|string'
            ])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Course::findOrFail($id);
    }

    /**
     * Find entry by id.
     */
    public function find(string $id)
    {
        return Course::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $course = $this->find($id);

        $course->update($request->all());

        return $course;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = $this->find($id);

        $course->delete();

        return response()->noContent();
    }
}
