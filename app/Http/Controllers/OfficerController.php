<?php

namespace App\Http\Controllers;
use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
class OfficerController extends Controller
{
    public function index(){
        $officers = Officer::all();
        return view('officers.index', compact('officers'));
    }
    public function create() {
        $posts = Post::all();
        return view('officers.create', compact('posts'));
    }
    public function edit($id){
        $officer = Officer::findOrFail($id);
        $posts = Post::all();
        return view('officers.edit', compact('officer','posts'));
    }
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        if (Auth::guard('officer')->attempt($credentials)) {
            $officer = Auth::guard('officer')->user();
            return response()->json([
                'message' => 'Login successful',
                'officer' => $officer
            ], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
    
    public function logout(Request $request){
        Auth::guard('officer')->logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }
    
    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'post_id' => 'required|exists:posts,post_id',
            'WorkStartTime' => 'required|date_format:H:i',
            'WorkEndTime' => 'required|date_format:H:i|after:WorkStartTime',
            'workdays' => 'required|array|min:1',
            'workdays.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        $officer = Officer::create([
            'name' => $validated['name'],
            'post_id' => $validated['post_id'],
            'WorkStartTime' => $validated['WorkStartTime'],
            'WorkEndTime' => $validated['WorkEndTime'],
            'status' => 'Active',
        ]);

        foreach($validated['workdays']as $day){
            $officer->workDays()->create([
                'day_of_week' => $day,
            ]);
        }

        return redirect()->route('officers.index')->with('success', 'Officer created successfully');
    }
    public function update(Request $request,$id){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'post_id' => 'required|exists:posts,post_id',
            'WorkStartTime' => 'required|date_format:H:i',
            'WorkEndTime' => 'required|date_format:H:i|after:WorkStartTime',
            'workdays' => 'required|array|min:1',
            'workdays.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);
    
        $officer = Officer::findOrFail($id);
        $officer->name = $validated['name'];
        $officer->post_id = $validated['post_id'];
        $officer->WorkStartTime = $validated['WorkStartTime'];
        $officer->WorkEndTime = $validated['WorkEndTime'];
        $officer->save();
    
        // Update workdays
        $officer->workDays()->delete();
        foreach($validated['workdays'] as $day){
            $officer->workDays()->create([
                'day_of_week' => $day,
            ]);
        }
    
        // Deactivate activities outside new schedule
        $startTime = $validated['WorkStartTime'] . ':00';
        $endTime   = $validated['WorkEndTime'] . ':00';
    
        $officer->activities()
            ->where('start_date','>=',now())
            ->where(function($query) use ($startTime, $endTime){
                $query->whereTime('start_time','<',$startTime)
                      ->orWhereTime('end_time','>',$endTime);
            })
            ->update(['status' => 'Deactivated']);
    
        return redirect()->route('officers.index')->with('success', 'Officer updated successfully');
    }
    public function activate($id){
        $officer = Officer::findOrFail($id);
        // check if post is active
        if($officer->post->status !== 'Active'){
            return response()->json(['error' => 'Cannot activate officer with inactive post'], 400);
        }
        $officer->status = 'Active';
        $officer->save();
        //reactivate future activities if visitor is active
        $activities = $officer->activities()
            ->where('status','Deactivated')
            ->where('start_date','>=', now())
            ->get();

        foreach($activities as $activity){
            if($activity->appointment && $activity->appointment->visitor && $activity->appointment->visitor->status === 'Active'){
            $activity->update(['status' => 'Active']);
        }
}

    
        return redirect()->route('officers.index')->with('success', 'Officer activated successfully');
    }
    public function deactivate($id){
        $officer = Officer::findOrFail($id);
        $officer->status = 'Inactive';
        $officer->save();
        //deactivate future activities
        $officer->activities()
            ->where('status','Active')
            ->where('start_date','>=',now())
            ->update(['status' => 'Deactivated']);
        
        return redirect()->route('officers.index')->with('success', 'Officer deactivated successfully');
    }
}
?>