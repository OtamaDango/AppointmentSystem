<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment System</title>
</head>
<body>
    <header>
        <h1>Appointment System</h1>
        <nav>
            <a href="{{ route('dashboard') }}">Dashboard</a> |
            <a href="{{ route('officers.index') }}">Officers</a> |
            <a href="{{ route('posts.index') }}">Posts</a> |
            <a href="{{ route('visitors.index') }}">Visitors</a> |
            <a href="{{ route('appointments.index') }}">Appointments</a> |
            <a href="{{ route('activities.index') }}">Activities</a>
        </nav>
        <hr>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
