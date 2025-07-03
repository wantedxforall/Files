<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(){
        $pageTitle = 'Categories';
        $categories = Category::latest()->paginate(getPaginate());
        return view('admin.category.index',compact('pageTitle','categories'));
    }

    public function store(Request $request){

        $request->validate([
            'name'=>'required',
            'image' => ['required','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);

        $category = new Category();
        $category->name =  $request->name;
        $category->status = 1;

        if ($request->hasFile('image')) {
            try {
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $category->save();

        $notify[] = ['success', 'Category has been created successfully'];
        return back()->withNotify($notify);

    }

    public function update(Request $request){

        $request->validate([
            'name'=>'required',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);

        $category = Category::findOrFail($request->id);
        $category->name =  $request->name;
        $category->status = $request->status == 1 ? 1 :0;

        if ($request->hasFile('image')) {
            try {

                $old = $category->image;
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $category->save();

        $notify[] = ['success', 'Category has been created successfully'];
        return back()->withNotify($notify);

    }
}
