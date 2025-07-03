<?php

namespace App\Http\Controllers\User;

use App\Models\Form;
use App\Models\Plan;
use App\Models\Proof;
use App\Models\Report;
use App\Models\Deposit;
use App\Models\Service;
use App\Models\Category;
use App\Lib\FormProcessor;
use App\Models\ServiceView;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Lib\GoogleAuthenticator;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $user = auth()->user();

    // Monthly Deposit  Report Graph
     $deposits = Deposit::selectRaw("SUM(amount) as amount, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
        ->whereYear('created_at', date('Y'))
        ->whereStatus(1)
        ->where('user_id',$user->id)
        ->groupBy('month_name', 'month_num')
        ->orderBy('month_num')
        ->get();

      $depositsChart['labels'] = $deposits->pluck('month_name');
      $depositsChart['values'] = $deposits->pluck('amount');

      $widget['total_services'] = Service::where('user_id',$user->id)->count();
      $widget['pending_services'] = Service::where('user_id',$user->id)->where('status',0)->count();

    return view($this->activeTemplate . 'user.dashboard', compact('pageTitle','depositsChart','widget'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits();
        if ($request->search) {
            $deposits = $deposits->where('trx',$request->search);
        }
        $deposits = $deposits->with(['gateway'])->orderBy('id','desc')->paginate(getPaginate());
        return view($this->activeTemplate.'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate.'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions(Request $request)
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id',auth()->id());

        if ($request->search) {
            $transactions = $transactions->where('trx',$request->search);
        }

        if ($request->type) {
            $transactions = $transactions->where('trx_type',$request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark',$request->remark);
        }

        $transactions = $transactions->orderBy('id','desc')->paginate(getPaginate());
        return view($this->activeTemplate.'user.transactions', compact('pageTitle','transactions','remarks'));
    }


    public function attachmentDownload($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name).'- attachments.'.$extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate.'user.user_data', compact('pageTitle','user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            'country'=>@$user->address->country,
            'address'=>$request->address,
            'state'=>$request->state,
            'zip'=>$request->zip,
            'city'=>$request->city,
        ];
        $user->reg_step = 1;
        $user->save();

        $notify[] = ['success','Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);

    }


    public function plan(){
        $pageTitle = 'By Credits';
        $plans = Plan::where('status',1)->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'user.plan',compact('pageTitle','plans'));
    }

    public function fetchPost(){
        $pageTitle = 'Post Lists';
        $services = Service::with('category')->where('status',1)->where('user_id','!=',auth()->user()->id)->inRandomOrder()->latest()->limit(100)->get();
        $viewed  = ServiceView::where('user_id',auth()->user()->id)->pluck('service_id')->toArray();
        return view($this->activeTemplate.'user.services.all_posts',compact('pageTitle','services','viewed'));
    }

    public function postDetails($slug,$id){
        $service = Service::findOrFail($id);
        $user = auth()->user();
        $pageTitle = $service->name;
        $categories = Category::where('status',1)->latest()->get();
        return view($this->activeTemplate.'user.services.details',compact('pageTitle','service','categories','user'));
    }

    public function confirm(Request $request){


        $user = auth()->user();
        $serviceView = ServiceView::where('user_id',$user->id)->where('service_id',$request->service_id)->first();

        if($serviceView){
            $notify[] = ['error', 'You have already completed the task for this post.'];
            return back()->withNotify($notify);
        }

        $serviceView = new ServiceView();
        $serviceView->user_id = $user->id;
        $serviceView->service_id = $request->service_id;
        $serviceView->view_date = Date('Y-m-d');
        $serviceView->credits = gs()->add_credits;


        $user->credits += gs()->add_credits;

        $user->save();
        $serviceView->save();

        $notify[] = ['success','Screenshort submitted'];
        return back()->withNotify($notify);
    }

    public function postReport(Request $request){

        $request->validate([
            'report' => 'required'
        ]);

        $user = auth()->user();
        $exist = Report::where('user_id',$user->id)->where('service_id',$request->service_id)->first();
        $existServiceView = ServiceView::where('user_id',$user->id)->where('service_id',$request->service_id)->first();

        if($exist){
            $notify[] = ['error', 'You have already submitted the report for this post.'];
            return back()->withNotify($notify);
        }

        if(!$existServiceView){
            $notify[] = ['error', 'Please complete the task before submitting a report.'];
            return back()->withNotify($notify);
        }

        $report =  new Report();
        $report->user_id = $user->id;
        $report->service_id = $request->service_id;
        $report->report = $request->report;
        $report->save();

        $notify[] = ['success','Report submitted'];
        return back()->withNotify($notify);
    }



}
