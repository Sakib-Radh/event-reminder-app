<?php

namespace App\Traits;

use App\Mail\UpcomingEventReminder;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

trait SendsEventReminders
{
    public function sendReminderToAllUsers($event)
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->email) {
                Mail::to($user->email)->send(new UpcomingEventReminder($event));
            }
        }
    }
}
