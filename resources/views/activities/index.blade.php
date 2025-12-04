@extends('layout.app')

@section('content')
<h2>Activities</h2>
<a href="{{ route('activities.create') }}">Add Activity</a>
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

    <label>Officer:</label>
    <select name="officer_id">
        <option value="">All</option>
        @foreach($officers as $officer)
            <option value="{{ $officer->officer_id }}" {{ request('officer_id') == $officer->officer_id ? 'selected' : '' }}>
                {{ $officer->name }}
            </option>
        @endforeach
    </select>

    <label>Visitor:</label>
    <select name="visitor_id">
        <option value="">All</option>
        @foreach($visitors as $visitor)
            <option value="{{ $visitor->visitor_id }}" {{ request('visitor_id') == $visitor->visitor_id ? 'selected' : '' }}>
                {{ $visitor->name }}
            </option>
        @endforeach
    </select>
    <br>
    <label>Start Date:</label>
    <input type="date" name="start_date" value="{{ request('start_date') }}">

    <label>End Date:</label>
    <input type="date" name="end_date" value="{{ request('end_date') }}">

    <label>Start Time:</label>
    <input type="time" name="start_time" value="{{ request('start_time') }}">

    <label>End Time:</label>
    <input type="time" name="end_time" value="{{ request('end_time') }}">

    <button type="submit">Filter</button>
</form>

<!-- Activities Table -->
<table border="1" cellspacing="0" cellpadding="2">
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
        @php
            $endDateTime = \Carbon\Carbon::parse($activity->end_date . ' ' . $activity->end_time);
            $statusDisplay = $activity->status;

            if ($endDateTime->lt(now())) {
                $statusDisplay = $activity->status == 'Active' ? 'Completed' : 'Cancelled';
            }
        @endphp
        <tr>
            <td>{{ $activity->activity_id }}</td>
            <td>{{ $activity->officer->name }}</td>
            <td>{{ $activity->type }}</td>
            <td>{{ $activity->start_date }} {{ $activity->start_time }}</td>
            <td>{{ $activity->end_date }} {{ $activity->end_time }}</td>
            <td>{{ $activity->display_status }}</td>
            <td>
                @if($activity->appointment)
                    {{ $activity->appointment->appointment_id }}
                @else
                    -
                @endif
            </td>
            <td>
                <a href="{{ route('activities.show', $activity->activity_id) }}">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
