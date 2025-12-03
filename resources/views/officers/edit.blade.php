@extends('layout.app')

@section('content')
<h2>Edit Officer</h2>
<form method="POST" action="{{ route('officers.update', $officer->officer_id) }}">
    @csrf
    @method('PUT')

    <!-- Officer Name -->
    <label>Name:</label>
    <input type="text" name="name" value="{{ $officer->name }}" required><br>

    <!-- Officer Post -->
    <label>Post:</label>
    <select name="post_id" required>
        @foreach($posts as $post)
            <option value="{{ $post->post_id }}" {{ $officer->post_id == $post->post_id ? 'selected' : '' }}>
                {{ $post->name }}
            </option>
        @endforeach
    </select><br>
    
    <!-- Officer Work Hours -->
    <label>Work Start Time:</label>
    <input type="time" name="WorkStartTime" value="{{ $officer->WorkStartTime }}" required><br>

    <label>Work End Time:</label>
    <input type="time" name="WorkEndTime" value="{{ $officer->WorkEndTime }}" required><br>

    <!-- Officer Workdays -->
    <label>Workdays:</label><br>
    @php
        $officerDays = $officer->workDays->pluck('day_of_week')->toArray();
    @endphp
    @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
        <label>
            <input type="checkbox" name="workdays[]" value="{{ $day }}" 
                {{ in_array($day, $officerDays) ? 'checked' : '' }}>
            {{ $day }}
        </label><br>
    @endforeach

    <button type="submit">Update Officer</button>
</form>
@endsection
