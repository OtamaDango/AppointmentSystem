@extends('layout.app')

@section('content')
<h2>Activity Details</h2>

<p><strong>ID:</strong> {{ $activity->activity_id }}</p>
<p><strong>Officer:</strong> {{ $activity->officer->name }}</p>
<p><strong>Type:</strong> {{ $activity->type }}</p>
<p><strong>Status:</strong> {{ $activity->status }}</p>
<p><strong>Start:</strong> {{ $activity->start_date }} {{ $activity->start_time }}</p>
<p><strong>End:</strong> {{ $activity->end_date }} {{ $activity->end_time }}</p>
<p><strong>Appointment:</strong>
    @if($activity->appointment)
        {{ $activity->appointment->name }} (Visitor: {{ $activity->appointment->visitor->name }})
    @else
        -
    @endif
</p>

<a href="{{ route('activities.index') }}">Back to list</a>
@endsection
