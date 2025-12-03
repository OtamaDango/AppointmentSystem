@extends('layout.app')

@section('content')
<h2>Create Activity</h2>

<form method="POST" action="{{ route('activities.store') }}">
    @csrf

    <label>Officer:</label>
    <select name="officer_id" required>
        @foreach($officers as $officer)
            <option value="{{ $officer->officer_id }}">{{ $officer->name }}</option>
        @endforeach
    </select><br>

    <label>Type:</label>
    <select name="type" required>
        <option value="Break">Break</option>
        <option value="Leave">Leave</option>
        <option value="Busy">Busy</option>
    </select><br>

    <label>Start Date:</label>
    <input type="date" name="start_date" required><br>

    <label>End Date:</label>
    <input type="date" name="end_date" required><br>

    <label>Start Time:</label>
    <input type="time" name="start_time" required><br>

    <label>End Time:</label>
    <input type="time" name="end_time" required><br>

    <button type="submit">Create</button>
</form>
@endsection
