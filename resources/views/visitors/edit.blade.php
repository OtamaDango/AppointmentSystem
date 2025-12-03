@extends('layout.app')

@section('content')
<h2>Edit Visitor</h2>
<form method="POST" action="{{ route('visitors.update', $visitor->visitor_id) }}">
    @csrf
    @method('PUT')
    Name: <input type="text" name="name" value="{{ $visitor->name }}"><br>
    Mobile: <input type="text" name="mobileno" value="{{ $visitor->mobileno }}"><br>
    Email: <input type="email" name="email" value="{{ $visitor->email }}"><br>
    <button type="submit">Update</button>
</form>
@endsection
