<?php

namespace App\Http\Controllers\User;

use App\Models\Proof;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function index(){
        $pageTitle = 'All Posts';
        $user = auth()->user();
        $services = Service::with('category')->where('user_id',$user->id)->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'user.services.index',compact('pageTitle','services'));
    }

    public function create(){

        $pageTitle = 'Add Post';
        $categories = Category::where('status',1)->get();
        return view($this->activeTemplate.'user.services.create',compact('pageTitle','categories'));
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'link'=>'required|url',
            'category'=>'required|',
        ]);

        $user =  auth()->user();
        $purifier = new \HTMLPurifier();

        $service = new Service();
        $service->user_id = $user->id;
        $service->category_id = $request->category;
        $service->name = $request->name;
        $service->link = $request->link;
        $service->link_description = $purifier->purify($request->link_description);
        $service->status = gs()->auto_approved == 1 ?? 0;

        $user->credits -= gs()->deduct_credits;
        $service->save();
        $user->save();

        $notify[] = ['success', 'Service has been created successfully'];
        return back()->withNotify($notify);
    }

    public function edit($id){
        $pageTitle = 'Update Post';
        $categories = Category::where('status',1)->get();
        $service = Service::findOrFail($id);
        return view($this->activeTemplate.'user.services.edit',compact('pageTitle','categories','service'));
    }

    public function update(Request $request,$id){

        $request->validate([
            'name'=>'required',
            'link'=>'required|url',
            'category'=>'required|',
        ]);

        $user =  auth()->user();
        $purifier = new \HTMLPurifier();

        $service = Service::findOrFail($id);
        $service->user_id = $user->id;
        $service->category_id = $request->category;
        $service->name = $request->name;
        $service->link = $request->link;
        $service->link_description = $purifier->purify($request->link_description);
        $service->save();

        $notify[] = ['success', 'Service has been updated successfully'];
        return back()->withNotify($notify);
    }

    public function pending(){
        $pageTitle = 'Pending Posts';
        $user = auth()->user();
        $services = Service::with('category')->where('user_id',$user->id)->where('status',0)->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'user.services.index',compact('pageTitle','services'));
    }

    public function active(){
        $pageTitle = 'Pending Posts';
        $user = auth()->user();
        $services = Service::with('category')->where('user_id',$user->id)->where('status',1)->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'user.services.index',compact('pageTitle','services'));
    }

   
}
