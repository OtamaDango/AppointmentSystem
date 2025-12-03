<?php

namespace App\Http\Controllers;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index(){
        $visitors = Visitor::all();
        return view('visitors.index', compact('visitors'));
    }
    public function create() {
        return view('visitors.create');
    }
    public function edit($id){
        $visitor = Visitor::findOrFail($id);
        return view('visitors.edit', compact('visitor'));
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

        return redirect()->route('visitors.index')->with('success', 'Visitor created successfully');
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

        return redirect()->route('visitors.index')->with('success', 'Visitor updated successfully');
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

        
        return redirect()->route('visitors.index')->with('success', 'Visitor activated successfully');
    }
    public function deactivate($id){
        $visitor = Visitor::findOrFail($id);
        $visitor->status = 'Inactive';
        $visitor->save();

        $visitor->appointments()
            ->where('status','Active')
            ->where('date','>=',now())
            ->update(['status' => 'Deactivated']);

        return redirect()->route('visitors.index')->with('success', 'Visitor deactivated successfully');
    }
    public function viewAppointments($id){
        $visitor = Visitor::findOrFail($id);
        $appointments = $visitor->appointments;
        return view('visitors.appointments', compact('visitor', 'appointments'));
    }
}
?>