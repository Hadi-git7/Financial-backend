<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\fixedPayment;
use App\Models\Admin;
use App\Models\Category;

class FixedPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return fixedPayment::all();
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'type' => 'required|in:income,expense',
            'amount' => 'required',
            'currency' => 'required',
            'category_title' => 'required',
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

        // Create a new payment and associate it with the current admin and category
        $fixedPayment = fixedPayment::create([
            'title' => $validatedData['title'],
            'type' => $validatedData['type'],
            'description' => $validatedData['description'],
            'amount' => $validatedData['amount'],
            'currency' => $validatedData['currency'],
            'category_id' => $category->id,
            'category_title' => $category->title,  
            'admin_id' => auth()->user()->id,           
            'created_by' => auth()->user()->username,
        ]);
        

        //  // Get all payments associated with the admin
        // $admin = Admin::find(1); // Get a admin by ID
        // $fixedPayment = $admin->fixedPayment; // Get all payments associated with the admin
            
        // // Get all payments associated with the category
        //  $category = Category::find(1); // Get a category by ID
        //  $fixedPayment = $category->fixedPayment; // Get all payments associated with the category

       return $validatedData;
    }
        
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return fixedPayment::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $fixedPayment = fixedPayment::find($id);
        if($fixedPayment){

            $validatedData = $request->validate([
                'title',
                'description',
                'type' => 'in:income,expense',
                'amount',
                'currency',
                'category_id',
            ]);

        // Update the category title if it is provided in the request
        if ($request->has('category_title')) {
            $category = Category::where('title', $request->category_title)->first();
            if ($category) {
                $validatedData['category_id'] = $category->id;
                
            } else {
                return ['message' => 'Category not found'];
            }
        }


         // Get the current authenticated admin
         $validatedData['admin_id'] = auth()->user()->id;
         $validatedData['updated_by'] = auth()->user()->username;

         $fixedPayment->update($request->all());
         $fixedPayment->update($validatedData);
         $updatedPayment = fixedPayment::find($id); // retrieve updated data from the database

        // Get all payments associated with the admin
        $admin = Admin::find(1); // Get a admin by ID
        $fixedPayment = $admin->fixedPayment; // Get all payments associated with the admin

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
    public function destroy(string $id)
    {
        $fixedPayment =fixedPayment::find($id);
        if($fixedPayment){

         // Get the current authenticated admin
         $validatedData['deleted_by'] = auth()->user()->username;
            $fixedPayment->update($validatedData);
            $fixedPayment->delete();
            return [
                'message' => "Deleted successfuly"
            ];
        }else{
        return [
            'message' => "Payment not found"
        ];}
    }

      /**
     * Search for a fixedPayment title
     *
     * @param  str  $id
     * @return \Illuminate\Http\Response
     */
    public function search($title)
    {
     return   fixedPayment::where('title','like', '%'.$title.'%')->get();
    }
}