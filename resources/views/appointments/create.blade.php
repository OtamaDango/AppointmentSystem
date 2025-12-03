@extends('layout.app')

@section('content')
<h2>Create Appointment</h2>
<form method="POST" action="{{ route('appointments.store') }}">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" required><br>

    <label>Officer:</label>
    <select name="officer_id" required>
        @foreach($officers as $officer)
            <option value="{{ $officer->officer_id }}">{{ $officer->name }}</option>
        @endforeach
    </select><br>

    <label>Visitor:</label>
    <select name="visitor_id" required>
        @foreach($visitors as $visitor)
            <option value="{{ $visitor->visitor_id }}">{{ $visitor->name }}</option>
        @endforeach
    </select><br>

    <label>Date:</label>
    <input type="date" name="date" required><br>

    <label>Start Time:</label>
    <input type="time" name="StartTime" required><br>

    <label>End Time:</label>
    <input type="time" name="EndTime" required><br>

    <button type="submit">Create</button>
</form>
@endsection
