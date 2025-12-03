<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Activity;
use App\Models\Officer;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // Display all appointments
    public function index()
    {
        $appointments = Appointment::with(['officer', 'visitor', 'activities'])->get();
        return view('appointments.index', compact('appointments'));
    }

    // Show form to create a new appointment
    public function create()
    {
        $officers = Officer::where('status', 'Active')->get();
        $visitors = Visitor::where('status', 'Active')->get();
        return view('appointments.create', compact('officers', 'visitors'));
    }

    // Show form to edit an existing appointment
    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        $officers = Officer::where('status', 'Active')->get();
        $visitors = Visitor::where('status', 'Active')->get();
        return view('appointments.edit', compact('appointment', 'officers', 'visitors'));
    }

    // Store a new appointment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'officer_id' => 'required|exists:officers,officer_id',
            'visitor_id' => 'required|exists:visitors,visitor_id',
            'name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'StartTime' => 'required|date_format:H:i',
            'EndTime' => 'required|date_format:H:i|after:StartTime',
        ]);

        $officer = Officer::findOrFail($validated['officer_id']);
        $visitor = Visitor::findOrFail($validated['visitor_id']);

        // Officer and visitor must be active
        if ($officer->status !== 'Active') {
            return redirect()->back()->with('error', 'Officer is inactive.');
        }

        if ($visitor->status !== 'Active') {
            return redirect()->back()->with('error', 'Visitor is inactive.');
        }

        // Check if appointment date is a workday for the officer
        $dayOfWeek = Carbon::parse($validated['date'])->format('D');
        $workdays = $officer->workDays->pluck('day_of_week')->toArray();
        if (!in_array($dayOfWeek, $workdays)) {
            return redirect()->back()->with('error', 'Officer does not work on this day.');
        }

        // Check if appointment time is within officer's working hours
        $start = Carbon::parse($validated['StartTime']);
        $end = Carbon::parse($validated['EndTime']);
        $workStart = Carbon::parse($officer->WorkStartTime);
        $workEnd = Carbon::parse($officer->WorkEndTime);
        if ($start->lt($workStart) || $end->gt($workEnd)) {
            return redirect()->back()->with('error', 'Time is outside officer working hours.');
        }

        // Cancel any deactivated overlapping activity
        $deactivatedActivity = $officer->activities()
            ->where('status', 'Deactivated')
            ->where('start_date', $validated['date'])
            ->where('start_time', '<', $validated['EndTime'])
            ->where('end_time', '>', $validated['StartTime'])
            ->first();

        if ($deactivatedActivity) {
            $deactivatedActivity->status = 'Cancelled';
            $deactivatedActivity->save();
        }

        // Check for officer conflicting activities (Active only)
        $conflict = $officer->activities()
            ->where('status', 'Active')
            ->where('start_date', $validated['date'])
            ->where('start_time', '<', $validated['EndTime'])
            ->where('end_time', '>', $validated['StartTime'])
            ->exists();
        if ($conflict) {
            return redirect()->back()->with('error', 'Officer has a conflicting activity.');
        }

        // Check for visitor conflicting appointments
        $visitorConflict = $visitor->appointments()
            ->where('status', 'Active')
            ->where('date', $validated['date'])
            ->where('StartTime', '<', $validated['EndTime'])
            ->where('EndTime', '>', $validated['StartTime'])
            ->exists();
        if ($visitorConflict) {
            return redirect()->back()->with('error', 'Visitor has another appointment.');
        }

        // Create the appointment
        $appointment = Appointment::create([
            'officer_id' => $validated['officer_id'],
            'visitor_id' => $validated['visitor_id'],
            'name' => $validated['name'],
            'date' => $validated['date'],
            'StartTime' => $validated['StartTime'],
            'EndTime' => $validated['EndTime'],
            'status' => 'Active',
            'AddedOn' => now(),
        ]);

        // Create linked activity for the appointment
        Activity::create([
            'officer_id' => $validated['officer_id'],
            'type' => 'Appointment',
            'start_date' => $validated['date'],
            'end_date' => $validated['date'],
            'start_time' => $validated['StartTime'],
            'end_time' => $validated['EndTime'],
            'status' => 'Active',
            'appointment_id' => $appointment->appointment_id,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    }

    // Update an existing appointment
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'StartTime' => 'required|date_format:H:i',
            'EndTime' => 'required|date_format:H:i|after:StartTime',
        ]);

        $officer = $appointment->officer;
        $visitor = $appointment->visitor;

        // Officer & visitor must be active
        if ($officer->status !== 'Active') {
            return redirect()->back()->with('error', 'Cannot update appointment. Officer is inactive.');
        }

        if ($visitor->status !== 'Active') {
            return redirect()->back()->with('error', 'Cannot update appointment. Visitor is inactive.');
        }

        // Check if appointment date is a workday for the officer
        $dayOfWeek = Carbon::parse($validated['date'])->format('D');
        $workdays = $officer->workDays->pluck('day_of_week')->toArray();
        if (!in_array($dayOfWeek, $workdays)) {
            return redirect()->back()->with('error', 'Appointment date is not on officer\'s workday.');
        }

        // Check if appointment time is within officer's working hours
        $start = Carbon::parse($validated['StartTime']);
        $end = Carbon::parse($validated['EndTime']);
        $workStart = Carbon::parse($officer->WorkStartTime);
        $workEnd = Carbon::parse($officer->WorkEndTime);
        if ($start->lt($workStart) || $end->gt($workEnd)) {
            return redirect()->back()->with('error', 'Appointment time is outside officer\'s working hours.');
        }

        // Check for officer conflicting activities
        $conflict = $officer->activities()
            ->where('status', 'Active')
            ->where('start_date', $validated['date'])
            ->where('activity_id', '!=', optional($appointment->activity)->activity_id)
            ->where('start_time', '<', $validated['EndTime'])
            ->where('end_time', '>', $validated['StartTime'])
            ->exists();
        if ($conflict) {
            return redirect()->back()->with('error', 'Officer has a conflicting activity.');
        }

        // Check for visitor conflicting appointments
        $visitorConflict = $visitor->appointments()
            ->where('status', 'Active')
            ->where('date', $validated['date'])
            ->where('appointment_id', '!=', $appointment->appointment_id)
            ->where('StartTime', '<', $validated['EndTime'])
            ->where('EndTime', '>', $validated['StartTime'])
            ->exists();
        if ($visitorConflict) {
            return redirect()->back()->with('error', 'Visitor has another appointment.');
        }

        // Update the appointment
        $appointment->update($validated);

        // Update linked activity
        if ($appointment->activity) {
            $appointment->activity->update([
                'start_date' => $validated['date'],
                'end_date' => $validated['date'],
                'start_time' => $validated['StartTime'],
                'end_time' => $validated['EndTime'],
            ]);
        }

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    // Cancel an appointment
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'Cancelled';
        $appointment->save();

        // Cancel linked activity
        if ($appointment->activity) {
            $appointment->activity->update(['status' => 'Cancelled']);
        }

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully.');
    }

    // Show a single appointment with related officer, visitor, and activity
    public function show($id)
    {
        $appointment = Appointment::with(['officer', 'visitor', 'activity'])->findOrFail($id);
        return view('appointments.show', compact('appointment'));
    }
}
?>