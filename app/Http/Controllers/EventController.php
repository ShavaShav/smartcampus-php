<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Event;

class EventController extends Controller
{
    /**
     * Display a listing of all Events.
     *
     * @return Response
     */
    public function index()
    {
        return Event::with('author')->get();
    }

    /**
     * Store a newly created Event in the database.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        return Event::create($request->all());
    }

    /**
     * Display the specified Event's JSON.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Event $event)
    {
        return $event;
    }

    /**
     * Update the specified Event in the database.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Event $event)
    {
        $event->update($request->all());

        return response()->json($event, 200);
    }

    /**
     * Remove the specified Event from the database.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json(null, 204);
    }
}
