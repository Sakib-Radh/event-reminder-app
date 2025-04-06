<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $events = $user->events;

        return response()->json([
            'data' => $events,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_time' => 'required|date',
        ]);

        $event = Event::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'event_time' => $request->event_time,
            'unique_id' => uniqid('event_'),
        ]);

        return response()->json($event, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $upcomingEvent)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user_id = $request->user()->id;
        $currentTime = Carbon::now();
        $thirtyMinutesLater = Carbon::now()->addMinutes(30);

        // Get events that will occur within the next 30 minutes
        $events = Event::where('user_id', $user_id)
                        ->where('event_time', '>', $currentTime)
                       ->where('event_time', '<=', $thirtyMinutesLater)
                       ->get();

        return response()->json([
            'data' => $events
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);

        $event->update($request->only('title', 'description', 'event_time'));

        return response()->json($event, 201);;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        $event->delete();
    
        return response()->json(['message' => 'Event deleted successfully.']);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_map('trim', $data[0]));

        $events = [];

        foreach (array_slice($data, 1) as $row) {
            $row = array_combine($header, $row);

            // Validate each row
            $validator = Validator::make($row, [
                'title' => 'required|string|max:255',
                'event_time' => 'required|date',
            ]);

            if ($validator->fails()) {
                continue;
            }

            $events[] = [
                'user_id' => $request->user()->id,
                'title' => $row['title'],
                'description' => $row['description'] ?? null,
                'event_time' => $row['event_time'],
                'unique_id' => uniqid('event_'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($events)) {
            Event::insert($events);
        }

        return response()->json(['message' => 'CSV uploaded successfully.', 'imported' => count($events)]);
    }
}
