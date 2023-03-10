<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use Illuminate\Http\Request;


class GoalController extends Controller
{
    
    public function index()
    {
        return Goal::all();
    }

    
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'profit' => 'required',
            'year' =>'required',
        ]);
    
        $validatedData['admin_id'] = auth()->user()->id;
        $validatedData['created_by'] = auth()->user()->username;
    
        
        $goal = Goal::create([
            'profit' => $validatedData['profit'],
            'year' => $validatedData['year'],
            'admin_id' => $validatedData['admin_id'],
            'created_by' => $validatedData['created_by'],
        ]);
    
        return $validatedData;
    }
    


    
    public function show(Goal $id)
    {
        $goal = Goal::find($id);
        return response()->json([
            'message' => $goal,
        ]);
    }

   
    public function update(Request $request, $id)
    {
        
        $goal = Goal::find($id);
        if($goal){
          
            $validatedData = $request->validate([
                'profit',
                'year' ,
            ]);


         // Get the current authenticated admin
         $validatedData['admin_id'] = auth()->user()->id;
         $validatedData['updated_by'] = auth()->user()->username;

         $mergedData = array_merge($request->all(), $validatedData);

         $goal->update($mergedData);
         $updatedGoal = Goal::find($id); // retrieve updated data from the database

         return $updatedGoal;
    }else{
        return [
            'message' => "Goal not found"
        ];
    }
    }

    
   
    public function destroy($id)
    {
        $goal =Goal::find($id);
        if($goal){

         // Get the current authenticated admin
         $validatedData['deleted_by'] = auth()->user()->username;
            $goal->update($validatedData);
            $goal->delete();
            return [
                'message' => "Deleted successfuly"
            ];
        }else{
        return [
            'message' => "Goal not found"
        ];}
    }
}