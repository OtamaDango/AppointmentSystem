@extends('layout.app')

@section('content')
<h2>Add Visitor</h2>
<form method="POST" action="{{ route('visitors.store') }}">
    @csrf
    Name: <input type="text" name="name"><br>
    Mobile: <input type="text" name="mobileno"><br>
    Email: <input type="email" name="email"><br>
    <button type="submit">Save</button>
</form>
@endsection
