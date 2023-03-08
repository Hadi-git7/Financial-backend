<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
        ]);

         // Get the current authenticated admin
         $validatedData['admin_id'] = auth()->user()->id;
         $validatedData['created_by'] = auth()->user()->username;

        $category = Category::create([
            'title' =>  $validatedData['title'],
            'admin_id' => auth()->user()->id,
            'created_by' => auth()->user()->username,
        ]);

        return $validatedData;
    }



    /**
     * Display the specified resource.
     */
    public function show(Category $id)
    {
        $category = Category::find($id);
        return response()->json([
            'message' => $category,
        ]);
    }

  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if($category){
          
            $validatedData = $request->validate([
                'title',
            ]);


         // Get the current authenticated admin
         $validatedData['admin_id'] = auth()->user()->id;
         $validatedData['updated_by'] = auth()->user()->username;

         $mergedData = array_merge($request->all(), $validatedData);

         $category->update($mergedData);
        //  $category->update($validatedData);
         $updatedCategory = Category::find($id); // retrieve updated data from the database

         return $updatedCategory;
    }else{
        return [
            'message' => "Category not found"
        ];
    }
   }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category =Category::find($id);
        if($category){

         // Get the current authenticated admin
         $validatedData['deleted_by'] = auth()->user()->username;
            $category->update($validatedData);
            $category->delete();
            return [
                'message' => "Deleted successfuly"
            ];
        }else{
        return [
            'message' => "Category not found"
        ];}
    }

    public function search($title)
    {
     return   Category::where('title','like', '%'.$title.'%')->get();
    }
}
