<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\NewEventRequest;
use App\Http\Controllers\Controller;

use App\Event;
use JWTAuth;

class EventController extends Controller
{

    /**
     * Display a listing of all Events, most recent first.
     *
     * @return Response
     */
    public function index()
    {
        $events = Event::with('author')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json(compact('events'));
    }

    /**
     * Store a newly created Event in the database.
     *
     * @param  NewEventRequest  $request
     * @return Response
     */
    public function store(NewEventRequest $request)
    {
        $eventData = $request->get('event');

        // Add user to event fields from token
        $token = JWTAuth::getToken();
        $eventData['author_id'] = JWTAuth::toUser($token)->id;

        // Create in database.
        $event = Event::create($eventData);

        // Embed author for response
        $event->load('author');

        return response()->json(compact('event'));
    }

    /**
     * Display the specified Event's JSON.
     *
     * @param  Event  $event
     * @return Response
     */
    public function show(Event $event)
    {
        // Embed author for response
        $event->load('author');

        return response()->json(compact('event'));
    }

    /**
     * Update the specified Event in the database.
     *
     * @param  Request  $request
     * @param  Event  $event
     * @return Response
     */
    public function update(Request $request, Event $event)
    {
        // TODO: v2
        // $event->update($request->all());

        // return response()->json($event, 200);
    }

    /**
     * Remove the specified Event from the database.
     *
     * @param  Event  $event
     * @return Response
     */
    public function destroy(Event $event)
    {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        if ($event->author_id == $user->id) {
            // Event belongs to user
            $event->delete();

            return response()->json(['message' => 'delete_successful'], 200);
        } else {
            return response()->json(['error' => 'delete_forbidden'], 403);
        }
    }
}
