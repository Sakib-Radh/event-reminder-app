<!DOCTYPE html>
<html>
<head>
    <title>Event Reminder</title>
</head>
<body>
    <h2>Reminder: {{ $event->title }}</h2>
    <p>{{ $event->description }}</p>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y h:i A') }}</p>
    <p>Thank you!</p>
</body>
</html>
