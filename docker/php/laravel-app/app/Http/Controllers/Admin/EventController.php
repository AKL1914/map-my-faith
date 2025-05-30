<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Notifications\GlobalAnnouncementNotification;
use Illuminate\Http\Request;

/**
 * Class EventController
 *
 * This controller handles CRUD operations and additional functionalities for managing events
 * in the admin panel, including participants and event-related pins.
 */
class EventController extends Controller
{
    /**
     * EventController constructor.
     *
     * This constructor can be used to initialize middleware or other dependencies.
     */
    public function __construct()
    {
        // Constructor logic (if any)
    }

    /**
     * Display the event management view.
     *
     * This method returns the view for managing events in the admin panel.
     *
     * @return \Illuminate\View\View The event management view.
     */
    public function manage()
    {
        // Logic to fetch and display events
        return view('admin.gamification.events.index');
    }

    /**
     * Retrieve and return all events.
     *
     * This method fetches all events from the database, ordered by ID in descending order.
     *
     * @return \Illuminate\Database\Eloquent\Collection A collection of all events.
     */
    public function index()
    {
        return Event::orderBy('id', 'desc')->get();
    }

    /**
     * Store a new event in the database.
     *
     * This method validates the request data and creates a new event.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing event data.
     * @return \App\Models\Event The newly created event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        return Event::create($validated);
    }

    /**
     * Update an existing event in the database.
     *
     * This method validates the request data and updates the specified event.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing updated event data.
     * @param int $id The ID of the event to update.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $statusChanged = $validated['is_active'] !== $event->is_active;

        $event->update($validated);

        // Send notification if status changed
        if ($statusChanged) {
            $title = $validated['is_active']
                ? '📢 Event Activated'
                : '🛑 Event Ended';

            $body = "The event \"{$event->name}\" has been " . ($validated['is_active'] ? 'activated' : 'deactivated') . ".";

            $level = $validated['is_active'] ? 'success' : 'error';

            // Notify all users with push subscriptions
            User::whereHas('pushSubscriptions')->get()->each(function ($user) use ($title, $body, $level) {
                $user->notify(new GlobalAnnouncementNotification($title, $body, $level));
            });
        }

        return response()->json(['message' => 'Event updated successfully.']);
    }

    /**
     * Delete a specific event from the database.
     *
     * This method deletes the specified event by its ID.
     *
     * @param int $id The ID of the event to delete.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully.']);
    }

    /**
     * Display the participants view for a specific event.
     *
     * This method returns the view displaying participants of the specified event.
     *
     * @param \App\Models\Event $event The event whose participants are to be displayed.
     * @return \Illuminate\View\View The participants view.
     */
    public function participants(Event $event)
    {
        return view('admin.gamification.events.participants', compact('event'));
    }

    /**
     * Display the details of a specific event.
     *
     * This method returns the view displaying the details of the specified event.
     *
     * @param \App\Models\Event $event The event to display.
     * @return \Illuminate\View\View The event details view.
     */
    public function show(Event $event)
    {
        return view('admin.gamification.events.show', compact('event'));
    }

    /**
     * Retrieve and return all pins associated with a specific event.
     *
     * This method fetches all game pins for the specified event, including associated user data.
     *
     * @param \App\Models\Event $event The event whose pins are to be retrieved.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the event name and pins data.
     */
    public function eventPins(Event $event)
    {
        $gamePins = $event->gamePins()->with(['user'])->get();
        return response()->json([
            'event_name' => $event->name,
            'data' => $gamePins,
        ]);
    }

    /**
     * Retrieve and return all participants of a specific event.
     *
     * This method fetches participants for the specified event by joining through game pins.
     * The results are paginated.
     *
     * @param int $id The ID of the event.
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     * @return \Illuminate\Http\JsonResponse A JSON response containing participants data and pagination metadata.
     */
    public function eventParticipants($id, Request $request)
    {
        $event = Event::findOrFail($id);

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
