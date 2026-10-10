<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Jobs\SendUserActivatedEmail;
use App\Models\Pin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class UserController
 *
 * This controller handles user management in the admin panel, including CRUD operations,
 * activation/deactivation, and updating user attributes such as area and group.
 */
class UserController extends Controller
{
    /**
     * Retrieve and return a paginated list of users.
     *
     * This method fetches users from the database with optional search functionality
     * and caches the results for 10 minutes.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing pagination and search parameters.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the paginated list of users.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default to 10 items per page
        $search = $request->input('search');
        $area = $request->input('area');

        $query = User::select('id', 'name', 'email', 'is_admin', 'is_activated', 'area', 'group', 'cfo')
            ->withCount('pins') // 👈 adds pins_count field
            ->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($area && $area !== 'all') {
            $query->where('area', $area);
        }

        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Display the user management view.
     *
     * This method returns the view for managing users in the admin panel.
     *
     * @return \Illuminate\View\View The user management view.
     */
    public function manage()
    {
        return view('admin.users.index');
    }

    /**
     * Update the admin status of a specific user.
     *
     * This method updates the `is_admin` attribute of the specified user and clears the cache.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing the new admin status.
     * @param  \App\Models\User  $user  The user whose admin status is to be updated.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function updateAdminStatus(Request $request, User $user)
    {
        $user->is_admin = $request->boolean('is_admin');
        $user->save();

        return response()->json(['message' => 'Admin status updated.']);
    }

    /**
     * Update the activation status of a specific user.
     *
     * This method updates the `is_activated` attribute of the specified user and clears the cache.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing the new activation status.
     * @param  \App\Models\User  $user  The user whose activation status is to be updated.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function updateActivationStatus(Request $request, User $user)
    {
        $user->is_activated = $request->boolean('is_activated');
        $user->save();

        if ($user->is_activated) { // Only send email if activated
            SendUserActivatedEmail::dispatch($user);
        }

        return response()->json(['message' => 'Activation status updated.']);
    }

    /**
     * Activate all non-admin users.
     *
     * This method sets the `is_activated` attribute to true for all non-admin users
     * and clears the cache.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function activateAll()
    {
        // Get all non-admin users who are NOT yet activated
        $users = User::where('is_admin', false)
            ->where('is_activated', false)
            ->get();

        foreach ($users as $user) {
            // Activate the user
            $user->is_activated = true;
            $user->save();

            // Send activation email
            SendUserActivatedEmail::dispatch($user);
        }

        return response()->json(['message' => 'All users activated and notified.']);
    }

    /**
     * Deactivate all non-admin users.
     *
     * This method sets the `is_activated` attribute to false for all non-admin users
     * and clears the cache.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function deactivateAll()
    {
        User::query()->where('is_admin', false)->update(['is_activated' => false]);

        return response()->json(['message' => 'All users deactivated.']);
    }

    /**
     * Update the area and group of a specific user.
     *
     * This method updates the `area` and `group` attributes of the specified user
     * and clears the cache.
     *
     * @param  \App\Http\Requests\UpdateProfileRequest  $request  The HTTP request containing the new area and group data.
     * @param  \App\Models\User  $user  The user whose area and group are to be updated.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function updateAreaGroup(UpdateProfileRequest $request, User $user)
    {
        $user->area = $request->input('area');
        $user->group = $request->input('group');
        $user->save();

        return response()->json(['message' => 'User area and group updated.']);
    }

    public function update(UpdateProfileRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $user,
        ]);
    }

    /**
     * Per-area count of users who have / have not pinned, optionally scoped to the
     * dashboard's date range and campaign. "No pins" is the area's remaining users,
     * so the two columns always add up to the area's total.
     */
    public function userParticipationByArea(Request $request)
    {
        $filters = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'campaign_id' => 'nullable|integer',
        ]);

        // Cache key must include the filters, otherwise every filter combination shares one result.
        $cacheKey = 'user_participation_by_area:'.md5(json_encode([
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null,
            $filters['campaign_id'] ?? null,
        ]));

        // Short TTL: the dashboard is watched live while campaigns run.
        $participation = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filters) {
            $areas = range(1, 6);

            $totals = User::whereIn('area', $areas)
                ->selectRaw('area, count(*) as total')
                ->groupBy('area')
                ->pluck('total', 'area');

            $withPinsQuery = Pin::query()
                ->join('users', 'users.id', '=', 'pins.user_id')
                ->whereIn('users.area', $areas);

            if (! empty($filters['campaign_id'])) {
                $withPinsQuery->where('pins.campaign_id', $filters['campaign_id']);
            }

            // Range comparison (end day inclusive) rather than casting created_at, so the index stays usable.
            if (! empty($filters['date_from'])) {
                $withPinsQuery->where('pins.created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
            }

            if (! empty($filters['date_to'])) {
                $withPinsQuery->where('pins.created_at', '<', Carbon::parse($filters['date_to'])->addDay()->startOfDay());
            }

            $withPins = $withPinsQuery
                ->selectRaw('users.area as area, count(distinct pins.user_id) as with_pins')
                ->groupBy('users.area')
                ->pluck('with_pins', 'area');

            return collect($areas)->map(function ($areaId) use ($totals, $withPins) {
                $with = (int) ($withPins[$areaId] ?? 0);

                return [
                    'area' => $areaId,
                    'with_pins' => $with,
                    'no_pins' => max(0, (int) ($totals[$areaId] ?? 0) - $with),
                ];
            })->all();
        });

        return response()->json([
            'participation' => $participation,
        ]);
    }
}
