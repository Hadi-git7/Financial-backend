<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorerecurringPaymentRequest;
use App\Http\Requests\UpdaterecurringPaymentRequest;
use App\Models\Admin;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\Category;


class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Expense::all();
    }

    /**
     * Show the form for creating a new resource.
     */
   

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'type' => 'required|in:fixed,recurring',
            'amount' => 'required',
            'currency'=>'required|in:USD,EUR,LBP',
            'category_title' => 'required',
            'start_date'=>'required|date_format:Y-m-d',
            'end_date'=>'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);
    
        // Find the category based on the category title provided
        $category = Category::where('title', $validatedData['category_title'])->first();
    
        // If the category does not exist, create a new category and add it to the database
        if (!$category) {
            $category = new Category();
            $category->title = $validatedData['category_title'];
            $category->admin_id = auth()->user()->id; // Set the created_by attribute to the username of the authenticated user
            $category->created_by = auth()->user()->username; // Set the created_by attribute to the username of the authenticated user
            $category->save();
        }
    
        // Add the category ID to the validated data array
        $validatedData['category_id'] = $category->id;
        $validatedData['category_title'] = $category->title;
    
        // Get the current authenticated admin
        $validatedData['admin_id'] = auth()->user()->id;
        $validatedData['created_by'] = auth()->user()->username;
    
        // Check if a payment with the same title, category and admin already exists in the database
        $expense = Expense::where('title', $validatedData['title'])
                        ->where('category_id', $validatedData['category_id'])
                        ->where('admin_id', $validatedData['admin_id'])
                        ->first();
    
        // If the payment already exists, update its details instead of creating a new one
        if ($expense) {
            $expense->update([
                'type' => $validatedData['type'],
                'description' => $validatedData['description'],
                'amount' => $validatedData['amount'],
                'currency' => $validatedData['currency'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
            ]);
        } else {
            // Create a new payment and associate it with the current admin and category
            $expense = Expense::create([
                'title' => $validatedData['title'],
                'type' => $validatedData['type'],
                'description' => $validatedData['description'],
                'amount' => $validatedData['amount'],
                'currency' => $validatedData['currency'],
                'category_id' => $category->id,
                'category_title' => $category->title,  
                'admin_id' => auth()->user()->id,           
                'created_by' => auth()->user()->username,
                'start_date'=>$validatedData['start_date'],
                'end_date'=> $validatedData['end_date'],
            ]);
        }
    
        // Get all payments associated with the admin
        $admin = auth()->user();
        $income = $admin->expense;
    
        return $validatedData;
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Expense::find($id);   
    }

    /**
     * Show the form for editing the specified resource.
     */
  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
      
            $expense = Expense::find($id);
            if($expense){
        
                $validatedData = $request->validate([
                    'type' => 'required|in:fixed,recurring',
                    'title',
                    'description', 
                    'created_by', 
                    'amount',
                    'currency'=>'in:USD,LBP,EUR',
                    'start_date' => 'date_format:Y-m-d',
                    'end_date' => 'date_format:Y-m-d|after_or_equal:start_date',
                    'category_id'
                ]);


 // Update the category title if it is provided in the request
 if ($request->has('category_title')) {
    $category = Category::where('title', $request->category_title)->first();
    if ($category) {
        $validatedData['category_id'] = $category->id;
        $category->updated_by = auth()->user()->username;
    } else {
        return ['message' => 'Category not found'];
    }
}


      // Get the current authenticated admin
      $validatedData['admin_id'] = auth()->user()->id;
      $validatedData['updated_by'] = auth()->user()->username;

      $expense->update($request->all());
      $expense->update($validatedData);
      $updatedExpense = Expense::find($id); // retrieve updated data from the database

      // Get all payments associated with the admin
    //   $admin = Admin::find(1); 
      // Get a admin by ID
      $admin = auth()->user();
      $expense = $admin->expense; // Get all payments associated with the admin

      return $updatedExpense;
  }else{
      return [
            'message' => "Payment not found"
        ];
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $expense = Expense::find($id);
    if ($expense) {
       // Get the current authenticated admin
       $validatedData['deleted_by'] = auth()->user()->username;
       $expense->update($validatedData);
       $expense->delete();
       return [
           'message' => "Deleted successfuly"
       ];
   }else{
   return [
       'message' => "Payment not found"
   ];}
}

    public function search($title)
    {
        return Expense::where('title', 'like', '%'.$title.'%')->get();
    }
}