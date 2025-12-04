<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Officer;
use App\Models\Visitor;
use Carbon\Carbon;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    // Show form to create a new activity (Leave/Break)
    public function create()
    {
        $officers = Officer::where('status', 'Active')->get();
        return view('activities.create', compact('officers'));
    }

    // Show a single activity
    public function show($id)
    {
        $activity = Activity::with(['officer', 'appointment', 'appointment.visitor'])->findOrFail($id);
        return view('activities.show', compact('activity'));
    }

    // Show edit form
    public function edit($id)
    {
        $activity = Activity::findOrFail($id);

        // Appointment type activities cannot be edited manually
        if ($activity->type === 'Appointment') {
            return redirect()->back()->with('error', 'Appointment activities cannot be updated manually.');
        }

        return view('activities.edit', compact('activity'));
    }

    // List all activities with optional filters
    public function index(Request $request)
    {
        $query = Activity::with(['officer', 'appointment', 'appointment.visitor']);

        // Filter by type (Leave/Break/Appointment/Busy) or skip if "All"
        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('type', $request->type);
        }

        // Filter by status (Active/Completed/Cancelled) or skip if "All"
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        // Filter by officer
        if ($request->filled('officer_id')) {
            $query->where('officer_id', $request->officer_id);
        }

        // Filter by visitor (through appointment)
        if ($request->filled('visitor_id')) {
            $query->whereHas('appointment', function ($q) use ($request) {
                $q->where('visitor_id', $request->visitor_id);
            });
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }

        // Filter by time range
        if ($request->filled('start_time') && $request->filled('end_time')) {
            $query->whereBetween('start_time', [$request->start_time, $request->end_time]);
        }

        $officers = Officer::all();
        $visitors = Visitor::all();
        $activities = $query->get();

        // Auto-update past activities
        foreach ($activities as $activity) {
            $activityEnd = Carbon::parse($activity->end_date . ' ' . $activity->end_time);
            if ($activityEnd->isPast()) {
                if ($activity->status === 'Active') {
                    $activity->status = 'Completed';
                    $activity->save();
                }
                if ($activity->status === 'Deactivated' || $activity->status === 'Cancelled') {
                    $activity->status = 'Cancelled';
                    $activity->save();
                }
            }
        }

        return view('activities.index', compact('activities', 'officers', 'visitors'));
    }

    // Store new activity
    public function store(Request $request)
    {
        $validated = $request->validate([
            'officer_id' => 'required|exists:officers,officer_id',
            'type' => 'required|string|in:Leave,Break',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $officer = Officer::findOrFail($validated['officer_id']);

        // Officer must be active
        if ($officer->status !== 'Active') {
            return redirect()->back()->with('error', 'Officer is inactive.');
        }

        // Check if activity falls within officer's workdays
        $dayOfWeek = Carbon::parse($validated['start_date'])->format('D');
        $officerWorkDays = $officer->workDays->pluck('day_of_week')->toArray();
        if (!in_array($dayOfWeek, $officerWorkDays)) {
            return redirect()->back()->with('error', 'Activity date is outside officer workdays.');
        }

        // Check if activity falls within officer's working hours
        $workStart = Carbon::parse($officer->WorkStartTime);
        $workEnd = Carbon::parse($officer->WorkEndTime);
        $activityStart = Carbon::parse($validated['start_time']);
        $activityEnd = Carbon::parse($validated['end_time']);
        if ($activityStart < $workStart || $activityEnd > $workEnd) {
            return redirect()->back()->with('error', 'Activity time is outside working hours.');
        }

        // Check for conflicting activities
        $conflict = $officer->activities()
            ->where('status', 'Active')
            ->where(function ($q) use ($validated) {
                $q->where('start_date', '<=', $validated['end_date'])
                  ->where('end_date', '>=', $validated['start_date'])
                  ->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()->with('error', 'Time conflict with existing activity.');
        }

        $activity = Activity::create($validated);

        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    // Update an existing activity (Leave/Break only)
    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
    
        if ($activity->type === 'Appointment') {
            return back()->with('error', 'Appointment activities cannot be updated manually.');
        }
    
        $validated = $request->validate([
            'type' => 'required|string|in:Leave,Break',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
    
        $officer = $activity->officer;
    
        if ($officer->status !== 'Active') {
            return back()->with('error', 'Cannot update. Officer is inactive.');
        }
    
        // Workday check
        $dayOfWeek = Carbon::parse($validated['start_date'])->format('D');
        $officerWorkDays = $officer->workDays->pluck('day_of_week')->toArray();
    
        if (!in_array($dayOfWeek, $officerWorkDays)) {
            return back()->with('error', 'Activity date is outside officer workdays.');
        }
    
        // Working hours check
        $workStart = Carbon::parse($officer->WorkStartTime);
        $workEnd = Carbon::parse($officer->WorkEndTime);
        $activityStart = Carbon::parse($validated['start_time']);
        $activityEnd = Carbon::parse($validated['end_time']);
    
        if ($activityStart < $workStart || $activityEnd > $workEnd) {
            return back()->with('error', 'Activity time is outside working hours.');
        }
    
        // Conflict check
        $conflict = $officer->activities()
            ->where('activity_id', '!=', $activity->activity_id)
            ->where('status', 'Active')
            ->where(function ($q) use ($validated) {
                $q->where('start_date', '<=', $validated['end_date'])
                  ->where('end_date', '>=', $validated['start_date'])
                  ->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();
    
        if ($conflict) {
            return back()->with('error', 'Time conflict with existing activity.');
        }
    
        $activity->update($validated);
    
        return redirect()->route('activities.index')->with('success', 'Activity updated successfully.');
    }
    
}
