<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use App\Models\Activity;
use App\Models\Officer;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Notifications\Action;

use function PHPSTORM_META\map;

class AppointmentController extends Controller
{
    public function index(){
        return Appointment::all();
    }
    public function store(Request $request){
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
        //officer and visitor must be active
        if($officer->status !== 'Active'){
            return response()->json(['error' => 'Officer is Inactive'],400);
        }
        if($visitor->status !== 'Active'){
            return response()->json(['error','Visitor is Inactive'],400);
        }

        // if date falls on officer workday
        $dayOfWeek = Carbon::parse($validated['date'])->format('D');
        $workdays = $officer->workDays->pluck('day_of_week')->toArray();

        if(!in_array($dayOfWeek,$workdays)){
            return response()->json(['error','Officer does not work on this day.'],400);
        }
        // check working hours

        $start = Carbon::parse($validated['StartTime']);
        $end = Carbon::parse($validated['EndTime']);
        $workStart = Carbon::parse($officer->WorkStartTime);
        $workEnd = Carbon::parse($officer->WorkEndTime);

        if($start->lt($workStart) || $end->gt($workEnd)){
            return response()->json(['error','Time is outisde officer working hours.'],400);
        }
        // check officer conflicting activities
        $conflict = $officer->activities()
            ->where('StartDate',$validated['date'])
            ->where('status','Active')
            ->where(function($q) use ($validated){
                $q->where(function ($sub) use ($validated){
                    $sub->where('StartTime','<',$validated['EndTime'])
                        ->where('EndTime','>',$validated['StartTime']);
                });
            })
            ->exists();

        if($conflict){
            return response()->json(['error','Officer has conflicting activity'],400);
        }
        // check visitor conflicting appointment

        $visitorConflict = $visitor->appointments()
            ->where('date', $validated['date'])
            ->where('status', 'Active')
            ->where(function ($q) use ($validated) {
                $q->where('StartTime', '<', $validated['EndTime'])
                    ->where('EndTime', '>', $validated['StartTime']);
            })
            ->exists();

        if ($visitorConflict) {
            return response()->json(['error' => 'Visitor has another appointment'], 400);
        }

        // Cancel any deactivated activities that this appointment overrides
        $officer->activities()
            ->where('status', 'Deactivated')
            ->where('start_date', '<=', $validated['date'])
            ->where('end_date', '>=', $validated['date'])
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['EndTime'])
                  ->where('end_time', '>', $validated['StartTime']);
            })
            ->update(['status' => 'Cancelled']);

        //create appointment
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

        // creating activity of appointment
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

        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment' => $appointment,
        ],201);
    }
    public function update(Request $request,$id){
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
        if($officer->status !== 'Active'){
        return response()->json(['error' => 'Cannot update appointment. Officer is inactive.'], 400);
        }
        if($visitor->status !== 'Active'){
            return response()->json(['error' => 'Cannot update appointment. Visitor is inactive.'], 400);
        }

        // Check if date is officer's workday
        $dayOfWeek = Carbon::parse($validated['date'])->format('D');
        $workdays = $officer->workDays->pluck('day_of_week')->toArray();
        if(!in_array($dayOfWeek, $workdays)){
        return response()->json(['error' => 'Appointment date is not on officers workday.'], 400);
        }

        // Check working hours
        $start = Carbon::parse($validated['StartTime']);
        $end = Carbon::parse($validated['EndTime']);
        $workStart = Carbon::parse($officer->WorkStartTime);
        $workEnd = Carbon::parse($officer->WorkEndTime);
        if($start->lt($workStart) || $end->gt($workEnd)){
            return response()->json(['error' => 'Appointment time is outside officers working hours.'], 400);
        }

        // Check for officer conflicting activities
        $conflict = $officer->activities()
            ->where('status','Active')
            ->where('StartDate', $validated['date'])
            ->where('activity_id', '!=', optional($appointment->activity)->activity_id)
            ->where(function($q) use ($validated){
                $q->where(function ($sub) use ($validated){
                    $sub->where('StartTime', '<', $validated['EndTime'])
                        ->where('EndTime', '>', $validated['StartTime']);
                });
            })
            ->exists();
        if($conflict){
            return response()->json(['error' => 'Officer has a conflicting activity.'], 400);
        }

        // Check for visitor conflicting appointments
        $visitorConflict = $visitor->appointments()
            ->where('status','Active')
            ->where('date', $validated['date'])
            ->where('appointment_id', '!=', $appointment->appointment_id)
            ->where(function ($q) use ($validated) {
                $q->where('StartTime', '<', $validated['EndTime'])
                    ->where('EndTime', '>', $validated['StartTime']);
            })
            ->exists();
        if($visitorConflict){
            return response()->json(['error' => 'Visitor has another appointment.'], 400);
        }
        
        $appointment->update($validated);
        //update corresponding activity
        $appointment->activities()->update([
            'start_date' =>$validated['date'],
            'end_date' =>$validated['date'],
            'start_time' => $validated['StartTime'],
            'end_time' => $validated['EndTime'],
        ]);

        return response()->json([
            'message' =>'Appointment Updated'
        ],200);
    }
    public function cancel($id){
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'Cancelled';
        $appointment->save();

        //cancel linked activity
        $appointment->activities()->update((['status'=>'Cancelled']));

        return response()->json(['message'=>'Appointment Cancelled'],200);
    }
    public function show($id){
        return Appointment::with(['officer','visitor','activity'])->findOrFail($id);
    }
}
?>
