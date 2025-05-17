<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {

    }
    public function index()
    {
        // Logic to fetch and display events
        return view('admin.gamification.events.index');
    }

    public function store()
    {
        // Logic to show the form for creating a new event
        return view('admin.events.create');
    }

    public function update($id)
    {
        // Logic to show the form for editing an existing event
        return view('admin.events.edit', ['eventId' => $id]);
    }

    public function destroy($id)
    {
        // Logic to delete an event
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }

    public function show($id)
    {
        // Logic to show a specific event
        return view('admin.gamification.events.show', ['eventId' => $id]);
    }

    public function participants($id)
    {
        return view('admin.gamification.events.participants', ['eventId' => $id]);
    }
}
