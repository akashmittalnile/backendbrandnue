<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplement;
use App\Models\Exercise;
use App\Models\Category;
use App\Models\SubscribeMemberTransaction;
use App\Models\SubscribeMember;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Input;
use Auth;
use DB;

use Square\SquareClient;
use Square\LocationsApi;

class AdminController extends Controller
{
    public $supplement,$exercise,$category,$plan;

    public function __construct(Supplement $supplement,Exercise $exercise,Category $category,SubscriptionPlan $plan){
        $this->middleware('auth');
        $this->supplement = $supplement;
        $this->exercise = $exercise;
        $this->category = $category;
        $this->plan = $plan;
    }

    public function supplementList(){
        $list = $this->supplement->getSupplementList();
        return view('admin.supplement.list',compact('list'));
    }

    public function supplementCreate(){
        return view('admin.supplement.create');
    }

    public function supplementStore(Request $request){
        $this->validate($request,[
            'supplement_name'=>'required|max:191|unique:supplements,name',
            'status'=>'required'
        ]);
        $supplement = new $this->supplement;
        $supplement->name = $request->supplement_name;
        $supplement->status = $request->status;
        $supplement->created_by = Auth::id();
        $supplement->save();
        \Session::flash('success','Record created successfully');
        return redirect(route('admin.supplement.list'));
    }

    public function supplementEdit(Supplement $supplement){
        return view('admin.supplement.edit',compact('supplement'));
    }

    public function supplementUpdate(Request $request,Supplement $supplement){
        $this->validate($request,[
            'supplement_name'=>'required|max:191|unique:supplements,name,'.$supplement->id,
            'status'=>'required'
        ]);
        $supplement->name = $request->supplement_name;
        $supplement->status = $request->status;
        $supplement->created_by = Auth::id();
        $supplement->save();
        \Session::flash('success','Record updated successfully');
        return redirect(route('admin.supplement.list'));
    }

    public function supplementDelete(Supplement $supplement){
        $supplement->delete();
        \Session::flash('success','Record deleted successfully');
        return successMsgResponse('Record deleted successfully');
    }

    public function exerciseList(){
        $list = $this->exercise->getExerciseList();
        return view('admin.exercise.list',compact('list'));
    }

    public function exerciseCreate(){
        return view('admin.exercise.create');
    }

    public function exerciseStore(Request $request){
        $this->validate($request,[
            'exercise_name'=>'required|max:191|unique:exercises,name',
            'status'=>'required'
        ]);
        $exercise = new $this->exercise;
        $exercise->name = $request->exercise_name;
        $exercise->status = $request->status;
        $exercise->created_by = Auth::id();
        $exercise->save();
        \Session::flash('success','Record created successfully');
        return redirect(route('admin.exercise.list'));
    }

    public function exerciseEdit(Exercise $exercise){
        return view('admin.exercise.edit',compact('exercise'));
    }

    public function exerciseUpdate(Request $request,Exercise $exercise){
        $this->validate($request,[
            'exercise_name'=>'required|max:191|unique:exercises,name,'.$exercise->id,
            'status'=>'required'
        ]);
        $exercise->name = $request->exercise_name;
        $exercise->status = $request->status;
        $exercise->created_by = Auth::id();
        $exercise->save();
        \Session::flash('success','Record updated successfully');
        return redirect(route('admin.exercise.list'));
    }

    public function exerciseDelete(Exercise $exercise){
        $exercise->delete();
        \Session::flash('success','Record deleted successfully');
        return successMsgResponse('Record deleted successfully');
    }


    public function categoryList(Request $request){
        $list = $this->category->getCategoryList($request);
        return view('admin.category.list',compact('list'));
    }

    public function categoryCreate(){
        return view('admin.category.create');
    }

    public function categoryStore(Request $request){
        $this->validate($request,[
            'category_name'=>'required|max:191|unique:categories,name',
            'status'=>'required'
        ]);
        $category = new $this->category;
        $category->name = $request->category_name;
        $category->status = $request->status;
        $category->save();
        \Session::flash('success','Record created successfully');
        return redirect(route('admin.category.list'));
    }

    public function categoryEdit(Category $category){
        return view('admin.category.edit',compact('category'));
    }

    public function categoryUpdate(Request $request,Category $category){
        $this->validate($request,[
            'category_name'=>'required|max:191|unique:categories,name,'.$category->id,
            'status'=>'required'
        ]);
        $category->name = $request->category_name;
        $category->status = $request->status;
        $category->save();
        \Session::flash('success','Record updated successfully');
        return redirect(route('admin.category.list'));
    }

    public function categoryDelete(Category $category){
        $category->delete();
        \Session::flash('success','Record deleted successfully');
        return successMsgResponse('Record deleted successfully');
    }

    # 05-dec-22
    public function transactionList(Request $request){
        $plans = $this->plan->getPlan();

        $get_order = (new SubscribeMember)->newQuery();
        $get_order->join('users AS p2', 'subscribe_members.user_id', '=', 'p2.id');
        $get_order->select('subscribe_members.*', 'p2.id as uid');

        if($request->filled('n')){
            $get_order->where('p2.name','like','%'.$request->n.'%')->orWhere('p2.email','like','%'.$request->n.'%');
        }
        if($request->filled('s')){
            $get_order->where('subscribe_members.device',$request->s);
        }

        if($request->filled('p')){
            $get_order->where('subscribe_members.subscription_plan_id',$request->p);
        }

        if($request->has('f') && $request->filled('f')){
            $fd = date('Y/m/d', strtotime($request->f));
            $get_order->where(DB::raw("(DATE_FORMAT(p2.created_at,'%Y/%m/%d'))"),">=",$fd);
        }

        if($request->has('t') && $request->filled('t')){
            $td = date('Y/m/d', strtotime($request->t));
            $get_order->where(DB::raw("(DATE_FORMAT(p2.created_at,'%Y/%m/%d'))"),"<=",$td);
        }

        $subscribe_members = $get_order->whereNotNull('subscribe_members.square_payment_subscription_id')->orderBy('subscribe_members.id', 'desc')->paginate(25);


        $total_members = SubscribeMember::whereNotNull('square_payment_subscription_id')->orderBy('id', 'desc')->get();
        return view('admin.transaction-list',compact('subscribe_members','total_members', 'plans'));
    }

    # 06 june 23
    public function showLogFile(){
        $data = $info_logs = $error_log = array();
      
        $file_path = storage_path('logs/laravel.log');
        $handle = fopen($file_path, "r");    
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . 'INFO' . ': (.*?)( in .*?:[0-9]+)?$/i', $line, $matches);
                if (!isset($matches[4])) {
                    continue;
                }
                $log = [
                  'date' => $matches[1],
                  'text' => $matches[4],
                ];
                array_push($info_logs, $log);
            }

            fclose($handle);
        }

        $err_file_path = storage_path('logs/laravel.log');
        $err_handle = fopen($err_file_path, "r");    
        if ($err_handle) {
            while (($line = fgets($err_handle)) !== false) {
                preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . 'ERROR' . ': (.*?)( in .*?:[0-9]+)?$/i', $line, $matches);
                if (!isset($matches[4])) {
                    continue;
                }
                $erlog = [
                  'date' => $matches[1],
                  'text' => $matches[4],
                ];
                array_push($error_log, $erlog);
            }

            fclose($err_handle);
        }

        $data['info_logs'] = $info_logs;
        $data['error_log'] = $error_log;
        // dd($data);
        return view('admin.show-logs', compact('data'));
    }

    public function cleanLogFile($id){
        if ($id != '') {
            # code...
            $file = 'logs/'.$id.'.log';
            $file_path = storage_path($file);
            $file = fopen($file_path,"w");
            fwrite($file,"");
            fclose($file);
            return redirect()->back();
        }    
    }
}
