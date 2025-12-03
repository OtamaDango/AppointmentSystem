@extends('layout.app')

@section('content')
<h2>Add Officer</h2>
<form method="POST" action="{{ route('officers.store') }}">
    @csrf

    <label>Name:</label>
    <input type="text" name="name" value="{{ old('name') }}" required><br><br>

    <label>Post:</label>
    <select name="post_id" required>
        <option value="">-- Select Post --</option>
        @foreach($posts as $post)
            <option value="{{ $post->post_id }}" {{ old('post_id') == $post->post_id ? 'selected' : '' }}>
                {{ $post->name }}
            </option>
        @endforeach
    </select><br><br>

    <label>Work Start Time:</label>
    <input type="time" name="WorkStartTime" value="{{ old('WorkStartTime') }}" required><br><br>

    <label>Work End Time:</label>
    <input type="time" name="WorkEndTime" value="{{ old('WorkEndTime') }}" required><br><br>

    <label>Workdays:</label><br>
    @php
        $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    @endphp
    @foreach($days as $day)
        <input type="checkbox" name="workdays[]" value="{{ $day }}" 
            {{ is_array(old('workdays')) && in_array($day, old('workdays')) ? 'checked' : '' }}>
        {{ $day }}
    @endforeach
    <br><br>

    <button type="submit">Save</button>
</form>
@endsection
