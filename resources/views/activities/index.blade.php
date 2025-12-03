@extends('layout.app')

@section('content')
<h2>Activities</h2>

<!-- Filters -->
<form method="GET" action="{{ route('activities.index') }}">
    <label>Type:</label>
    <select name="type">
        <option value="">All</option>
        <option value="Break" {{ request('type')=='Break' ? 'selected' : '' }}>Break</option>
        <option value="Leave" {{ request('type')=='Leave' ? 'selected' : '' }}>Leave</option>
        <option value="Appointment" {{ request('type')=='Appointment' ? 'selected' : '' }}>Appointment</option>
        <option value="Busy" {{ request('type')=='Busy' ? 'selected' : '' }}>Busy</option>
    </select>

    <label>Status:</label>
    <select name="status">
        <option value="">All</option>
        <option value="Active" {{ request('status')=='Active' ? 'selected' : '' }}>Active</option>
        <option value="Completed" {{ request('status')=='Completed' ? 'selected' : '' }}>Completed</option>
        <option value="Cancelled" {{ request('status')=='Cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>

    <button type="submit">Filter</button>
</form>

<!-- Activities Table -->
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Officer</th>
            <th>Type</th>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
            <th>Appointment</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($activities as $activity)
        <tr>
            <td>{{ $activity->activity_id }}</td>
            <td>{{ $activity->officer->name }}</td>
            <td>{{ $activity->type }}</td>
            <td>{{ $activity->start_date }} {{ $activity->start_time }}</td>
            <td>{{ $activity->end_date }} {{ $activity->end_time }}</td>
            <td>{{ $activity->status }}</td>
            <td>
                @if($activity->appointment)
                    {{ $activity->appointment->name }}
                @else
                    -
                @endif
            </td>
            <td>
                @if($activity->type != 'Appointment')
                    <a href="{{ route('activities.edit', $activity->activity_id) }}">Edit</a>
                @endif
                <a href="{{ route('activities.show', $activity->activity_id) }}">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
