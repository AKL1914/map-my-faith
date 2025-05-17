<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {

    }
    public function manage()
    {
        // Logic to fetch and display events
        return view('admin.gamification.events.index');
    }

    public function index()
    {
        return Event::orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        return Event::create($validated);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $event->update($validated);

        return response()->json(['message' => 'Event updated successfully.']);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully.']);
    }
    public function participants(Event $event)
    {
        return view('admin.gamification.events.participants', compact('event'));
    }


    public function eventParticipants($id, Request $request)
    {
        // Ensure the event exists
        $event = Event::findOrFail($id);

        // Join through game_pins to filter participants for this event
        $participants = \DB::table('game_pins_participants')
            ->join('game_pins', 'game_pins_participants.game_pin_id', '=', 'game_pins.id')
            ->join('users', 'game_pins_participants.user_id', '=', 'users.id')
            ->where('game_pins.event_id', $id)
            ->select(
                'game_pins_participants.id',
                'users.name as user_name',
                'game_pins_participants.distance',
                'game_pins_participants.created_at'
            )
            ->orderByDesc('game_pins_participants.id')
            ->paginate(10);

        return response()->json([
            'event_name' => $event->name,
            'data' => $participants->items(),
            'meta' => [
                'current_page' => $participants->currentPage(),
                'last_page' => $participants->lastPage(),
                'per_page' => $participants->perPage(),
                'total' => $participants->total(),
            ],
        ]);
    }
}
