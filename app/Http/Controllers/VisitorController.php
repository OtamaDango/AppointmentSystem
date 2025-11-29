<?php

namespace App\Http\Controllers;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index(){
        return Visitor::all();
    }
    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobileno' => 'required|string|max:10',
            'email' => 'required|email|max:244',
        ]);

        $visitor = Visitor::create([
            'name' => $validated['name'],
            'mobileno' => $validated['mobileno'],
            'email' => $validated['email'],
            'status' => 'Active',
        ]);

        return response()->json([
            'message' => 'Visitor created successfully',
            'visitor' => $visitor], 201);
    }
    public function update(Request $request,$id){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobileno' => 'required|string|max:10',
            'email' => 'required|email|max:255',
        ]);

        $visitor = Visitor::findOrFail($id);
        $visitor->name = $validated['name'];
        $visitor->mobileno = $validated['mobileno'];
        $visitor->email = $validated['email'];
        $visitor->save();

        return response()->json([
            'message' => 'Visitor updated successfully',
            'visitor' => $visitor], 200);
    }
    public function activate($id){
        $visitor = Visitor::findOrFail($id);
        $visitor->status = 'Active';
        $visitor->save();

        $visitor->appointments()
            ->where('status','Deactivated')
            ->where('date','>=', now())
            ->whereHas('officer', function($query){
                $query->where('status','Active');
            })
            ->update(['status' => 'Active']);

        
        return response()->json([
            'message' => 'Visitor activated successfully',
            'visitor' => $visitor], 200);
    }
    public function deactivate($id){
        $visitor = Visitor::findOrFail($id);
        $visitor->status = 'Inactive';
        $visitor->save();

        $visitor->appointments()
            ->where('status','Active')
            ->where('date','>=',now())
            ->update(['status' => 'Deactivated']);

        return response()->json([
            'message' => 'Visitor deactivated successfully',
            'visitor' => $visitor], 200);
    }
    public function viewAppointments($id){
        $visitor = Visitor::findOrFail($id);
        $appointments = $visitor->appointments;
        return response()->json([
            'visitor' => $visitor,
            'appointments' => $appointments], 200);
    }
}
?>