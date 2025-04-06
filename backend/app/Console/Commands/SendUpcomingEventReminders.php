<?php

namespace App\Console\Commands;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Traits\SendsEventReminders;

class SendUpcomingEventReminders extends Command
{
    use SendsEventReminders;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-upcoming-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for events happening in 30 minutes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $thirtyMinsLater = $now->copy()->addMinutes(30);

        // Get events happening within next 30 mins and not notified yet
        $events = Event::where('event_time', '>', $now)
                        ->where('event_time', '<=', $thirtyMinsLater)
                        ->where('notified', false)
                        ->get();

        foreach ($events as $event) {
            $this->sendReminderToAllUsers($event);

            $event->notified = true;
            $event->save();
        }

        $this->info("Reminder emails sent to all users for upcoming events.");
    }
}
