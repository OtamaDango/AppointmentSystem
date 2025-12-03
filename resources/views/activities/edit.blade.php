@extends('layout.app')

@section('content')
<h2>Edit Activity</h2>

<form method="POST" action="{{ route('activities.update', $activity->activity_id) }}">
    @csrf
    @method('PUT') <!-- Important: tells Laravel this is a PUT request -->

    <label>Type:</label>
    <select name="type" required>
        <option value="Break" {{ $activity->type=='Break' ? 'selected' : '' }}>Break</option>
        <option value="Leave" {{ $activity->type=='Leave' ? 'selected' : '' }}>Leave</option>
        <option value="Busy" {{ $activity->type=='Busy' ? 'selected' : '' }}>Busy</option>
    </select><br>

    <label>Start Date:</label>
    <input type="date" name="start_date" value="{{ $activity->start_date }}" required><br>

    <label>End Date:</label>
    <input type="date" name="end_date" value="{{ $activity->end_date }}" required><br>

    <label>Start Time:</label>
    <input type="time" name="start_time" value="{{ $activity->start_time }}" required><br>

    <label>End Time:</label>
    <input type="time" name="end_time" value="{{ $activity->end_time }}" required><br>

    <button type="submit">Update</button>
</form>

@endsection
