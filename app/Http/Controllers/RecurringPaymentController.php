<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorerecurringPaymentRequest;
use App\Http\Requests\UpdaterecurringPaymentRequest;
use App\Models\Admin;
use App\Models\recurringPayment;
use Illuminate\Http\Request;
use App\Models\Category;


class RecurringPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return recurringPayment::all();
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
            'type'=>'required|in:income,expense',
            'title'=>'required',
            'description'=>'required',
            'amount'=>'required',
            'currency'=>'required|in:USD,EUR,LBP',
            'category_title'=>'required',
            'start_date'=>'required|date_format:Y-m-d',
            'end_date'=>'required|date_format:Y-m-d|after_or_equal:start_date',
        
        ]);
    
        // Find the category based on the category title provided
        $category = Category::where('title', $validatedData['category_title'])->first();

        // If the category does not exist, create a new category and add it to the database
        if (!$category) {
            $category = new Category();
            $category->title = $validatedData['category_title'];
            $validatedData['admin_id'] = auth()->user()->id;
            $category->created_by = auth()->user()->username; // Set the created_by attribute to the username of the authenticated user
            $category->save();
        }

        // Add the category ID to the validated data array
         $validatedData['category_id'] = $category->id;
         $validatedData['category_title'] = $category->title;

         // Get the current authenticated admin
    $validatedData['admin_id'] = auth()->user()->id;
    $validatedData['created_by'] = auth()->user()->username;

    // Create a new payment and associate it with the current admin
    // $recurringPayment = recurringPayment::create( $validatedData);





// Create a new payment and associate it with the current admin and category
$recurringPayment = recurringPayment::create([
    'type' => $validatedData['type'],
    'title' => $validatedData['title'],
    'description' => $validatedData['description'],
    'amount' => $validatedData['amount'],
    'currency' => $validatedData['currency'],
    'category_id' => $category->id,
    'category_title' => $category->title, // set the category_title attribute on the Payment model
    'admin_id' => auth()->user()->id,
    'created_by' => auth()->user()->username,
]);



// Get a category by ID
// $category = Category::find(1); 
     // Get a admin by ID
    $admin = auth()->user();
    // $admin = Admin::find(auth()->user()->id);
    $recurringPayment = $admin->recurringPayment; // Get all payments associated with the admin

  
    return $validatedData;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return recurringPayment::find($id);   
    }

    /**
     * Show the form for editing the specified resource.
     */
  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $recurring_payment = recurringPayment::find($id);
        // $recurring_payment->update($request->all());
        // return $recurring_payment;

            $recurringPayment = recurringPayment::find($id);
            if($recurringPayment){
        
                $validatedData = $request->validate([
                    'type' => 'in:income,expense',
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

      $recurringPayment->update($request->all());
      $recurringPayment->update($validatedData);
      $updatedPayment = recurringPayment::find($id); // retrieve updated data from the database

      // Get all payments associated with the admin
    //   $admin = Admin::find(1); 
      // Get a admin by ID
      $admin = auth()->user();
      $recurringPayment = $admin->recurringPayment; // Get all payments associated with the admin

      return $updatedPayment;
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
    $recurringPayment = recurringPayment::find($id);
    if ($recurringPayment) {
       // Get the current authenticated admin
       $validatedData['deleted_by'] = auth()->user()->username;
       $recurringPayment->update($validatedData);
       $recurringPayment->delete();
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
        return recurringPayment::where('title', 'like', '%'.$title.'%')->get();
    }
}