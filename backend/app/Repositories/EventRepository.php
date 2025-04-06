<?php

namespace App\Repositories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class EventRepository
{
    /**
     * Get events for the authenticated user.
     *
     * @param  int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEventsByUserId($userId)
    {
        return Event::where('user_id', $userId)->get();
    }

    /**
     * Get upcoming events within 30 minutes for the authenticated user.
     *
     * @param  int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingEvents($userId)
    {
        $currentTime = Carbon::now();
        $thirtyMinutesLater = Carbon::now()->addMinutes(30);

        return Event::where('user_id', $userId)
            ->where('event_time', '>', $currentTime)
            ->where('event_time', '<=', $thirtyMinutesLater)
            ->get();
    }

    /**
     * Create a new event.
     *
     * @param  array $data
     * @return \App\Models\Event
     */
    public function createEvent(array $data)
    {
        return Event::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'event_time' => $data['event_time'],
            'unique_id' => uniqid('event_'),
        ]);
    }

    /**
     * Update an event.
     *
     * @param  int $eventId
     * @param  array $data
     * @return \App\Models\Event
     */
    public function updateEvent($eventId, array $data)
    {
        $event = Event::where('user_id', $data['user_id'])->findOrFail($eventId);
        $event->update($data);
        return $event;
    }

    /**
     * Delete an event.
     *
     * @param  int $eventId
     * @return bool
     */
    public function deleteEvent($eventId, $userId)
    {
        $event = Event::where('user_id', $userId)->findOrFail($eventId);
        return $event->delete();
    }

    /**
     * Import events from CSV file.
     *
     * @param  \Illuminate\Http\Request $request
     * @return int Number of successfully imported events.
     */
    public function importCsv($request)
    {
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

        return count($events);
    }
}
