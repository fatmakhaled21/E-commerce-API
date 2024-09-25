<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categries;
use Exception;

use Illuminate\Http\Request;
use Psy\Readline\Hoa\FileException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        $categries = Categries::paginate(10);
        return response()->json($categries,200);
    }

    public function show($id)
    {
        $categries = Categries::find($id);
        if($categries){
            return response()->json($categries,200);
        }else return response()->json('category not found');
    }

    public function store(Request $request)
    {
        try {
            // Validate the inputs
            $validated = $request->validate([
                'name' => 'required|unique:category,name', // Use "category" for the table name
                'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048' // Validate the image
            ]);
    
            // Create a new instance of the Categories model
            $categories = new Categries();
            $categories->name = $request->name;
    
            // Check if the request has an image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension(); // Get the file extension
                $filename = time() . '.' . $ext; // Create a unique name for the image
                $file->move('assets/uploads/category', $filename); // Move the image to the specified path
    
                $categories->image = $filename; // Save the image name in the database
            }
    
            // Save the data to the database
            $categories->save();
    
            return response()->json('Category added', 201);
        } catch (Exception $e) {
            // Return the error message if an exception occurs
            return response()->json($e->getMessage(), 500); 
        }
    }

    public function update_category($id,Request $request)
    {
        try{
            $validated = $request->validate([
                'name'=>'required|unique:category,name',
                'image'=>'required'
            ]);

            $categries = Categries::find($id);
            if($request->hasfile('image')){
                $path = 'assets/uploads/category' . $categries->image;
               if (File::exists($path)){
                File::delete($path);
               }
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;
                try{
                    $file->move('assets/uploads/category',$filename);
                }catch(FileException $e){
                    dd($e);
                }
                $categries->image = $filename;
            }
            Categries::where('id',$id)->update(['name'=>$request->name]);
            $categries->name=$request->name;
            $categries->update();
            return response()->json('category update',200);
        }catch(Exception $e){
            return response()->json($e,500);
        }
    }

    public function delete_category($id)
    {
        $categries = Categries::find($id);
        if($categries){
            $categries->delete();
        }
        else return response()->json('category not found');
    }
}
