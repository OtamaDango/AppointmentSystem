@extends('layout.app')

@section('content')
<h2>Edit Appointment</h2>
<form method="POST" action="{{ route('appointments.update', $appointment->appointment_id) }}">
    @csrf
    @method('PUT')

    Name: <input type="text" name="name" value="{{ $appointment->name }}"><br>
    Date: <input type="date" name="date" value="{{ $appointment->date }}"><br>
    Start Time: <input type="time" name="StartTime" value="{{ $appointment->StartTime }}"><br>
    End Time: <input type="time" name="EndTime" value="{{ $appointment->EndTime }}"><br>
    <button type="submit">Update</button>
</form>
@endsection
