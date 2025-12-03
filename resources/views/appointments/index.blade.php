@extends('layout.app')

@section('content')
<h2>Appointments</h2>
<a href="{{ route('appointments.create') }}">Add Appointment</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Officer</th>
        <th>Visitor</th>
        <th>Date</th>
        <th>Start</th>
        <th>End</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    @foreach($appointments as $appointment)
    <tr>
        <td>{{ $appointment->appointment_id }}</td>
        <td>{{ $appointment->name }}</td>
        <td>{{ $appointment->officer->name }}</td>
        <td>{{ $appointment->visitor->name }}</td>
        <td>{{ $appointment->date }}</td>
        <td>{{ $appointment->StartTime }}</td>
        <td>{{ $appointment->EndTime }}</td>
        <td>{{ $appointment->status }}</td>
        <td>
            <a href="{{ route('appointments.edit', $appointment->appointment_id) }}">Edit</a> |
            <form action="{{ route('appointments.cancel', $appointment->appointment_id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" >Cancel</button>
            </form>
            
        </td>
    </tr>
    @endforeach
</table>
@endsection
