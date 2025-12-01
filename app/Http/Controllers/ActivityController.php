<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Officer;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request){
        $query = Activity::with(['officer','appointment','appointment.visitor']);
        // filter
        if($request->has('type')){
            $query->where('type',$request->type);
        }
        if($request->has('status')){
            $query->where('status',$request->status);
        }
        if($request->has('officer_id')){
            $query->where('officer_id',$request->officer_id);
        }
        if($request->has('visitor_id')){
            $query->whereHas('appointment',function($q) use ($request){
                $q->where('visitor_id',$request->visitor_id);
            });
        }
        if($request->has('start_date') && $request->has('end_date')){
            $query->whereBetween('start_date',[$request->start_date,$request->end_date]);
        }
        if($request->has('start_time') && $request->has('end_time')){
            $query->whereBetween('start_time',[$request->start_time,$request->end_time]);
        }
        $activities = $query->orderby('start_date','desc')->get();
        // auto update past activities 
        foreach($activities as $activity){
            $activityEnd = Carbon::parse($activity->end_date.' '.$activity->end_time);
            if($activityEnd->isPast()){
                // Active => Completed
                if($activity->status == 'Active'){
                    $activity->status = 'Completed';
                    $activity->save();
                }
                // Deactivated/Cancelled => Cancelled
                if($activity->status == 'Deactivated' || $activity->status == 'Cancelled'){
                    $activity->status = 'Cancelled';
                    $activity->save();
            }
        }
    }
    return $activities;
    }
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

        // must be active officer
        if ($officer->status !== 'Active') {
            return response()->json(['error' => 'Officer is inactive'], 400);
        }
        // Check if activity falls within officer's workdays
        $daysOfWeek = collect($validated['start_date'])->map(fn($date) => Carbon::parse($date)->format('D'));
        $officerWorkDays = $officer->workDays->pluck('day_of_week')->toArray();
        foreach($daysOfWeek as $day) {
            if(!in_array($day, $officerWorkDays)) {
                return response()->json(['error' => 'Activity date is outside officer workdays'], 400);
            }
        }
        // Check if activity falls within officer's work time
        $workStart = Carbon::parse($officer->WorkStartTime);
        $workEnd = Carbon::parse($officer->WorkEndTime);
        $activityStart = Carbon::parse($validated['start_time']);
        $activityEnd = Carbon::parse($validated['end_time']);

        if ($activityStart < $workStart || $activityEnd > $workEnd) {
            return response()->json(['error' => 'Activity time is outside officer working hours'], 400);
        }

        // check conflicts
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
            return response()->json(['error' => 'Officer has conflicting activity'], 400);
        }

        $activity = Activity::create($validated);

        return response()->json([
            'message' => 'Activity created successfully',
            'activity' => $activity
        ], 201);
    }
    public function update(Request $request,$id){
        $activity = Activity::findOrFail($id);
        
        // Appointment type activities cannot be updated manually
        if($activity->type == 'Appointment') {
            return response()->json(['error' => 'Appointment activities cannot be updated manually'], 400);
        }
        $validated = $request->validate([
            'type' => 'required|string|in:Leave,Break',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
        $officer = $activity->officer;
        // Check if activity falls within officer's workdays
        $daysOfWeek = collect($validated['start_date'])->map(fn($date) => Carbon::parse($date)->format('D'));
        $officerWorkDays = $officer->workDays->pluck('day_of_week')->toArray();
        foreach($daysOfWeek as $day) {
            if(!in_array($day, $officerWorkDays)) {
                return response()->json(['error' => 'Activity date is outside officer workdays'], 400);
            }
        }
        // Check if activity falls within officer's work time
        $workStart = Carbon::parse($officer->WorkStartTime);
        $workEnd = Carbon::parse($officer->WorkEndTime);
        $activityStart = Carbon::parse($validated['start_time']);
        $activityEnd = Carbon::parse($validated['end_time']);

        if ($activityStart < $workStart || $activityEnd > $workEnd) {
            return response()->json(['error' => 'Activity time is outside officer working hours'], 400);
        }
        // Officer must be active
        if($officer->status !== 'Active'){
            return response()->json(['error' => 'Cannot create activity. Officer is inactive.'], 400);
        }
        // Check conflict 
        $conflict = $officer->activities()
            ->where('activity_id','!=',$activity->activity_id)
            ->where('status','Active')
            ->where(function($q) use ($validated){
                $q->where('start_date','<=',$validated['end_date'])
                  ->where('end_date','>=',$validated['start_date'])
                  ->where('start_time','<',$validated['end_time'])
                  ->where('end_time','>',$validated['start_time']);
            })
            ->exists();
        if($conflict){
            return response()->json(['error' => 'Cannot create activity. Time conflict with existing activities.'], 400);
        }
        $activity->update($validated);
        return response()->json([
            'message' => 'Activity updated successfully',
            'activity' => $activity,
        ],200); 
    }
    public function show($id){
        $activity = Activity::with(['officer','appointment','appointment.visitor'])->findOrFail($id);
        return $activity;
    }
}
?>