<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;
use Config;

use Zoha\Metable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, Metable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function notes(){
        return $this->morphMany('App\Models\Note', 'noteable');
    }

    public function roles(){
        return $this->belongsToMany('App\Models\Role')->withTimestamps();
    }

    public function plan(){
        return $this->hasOne('App\Models\SubscribeMember','user_id','id')->where('status','Active');
    }

    public function hasAnyRole($roles){
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }
    public function hasRole($role)
    {
        if ($this->roles()->where('name', $role)->first()) {
            return true;
        }
        return false;
    }

    public function getCustomer($request,$status=''){
        $query = $this->newQuery();
        $query->whereHas('roles',function($q){
            $q->where('name',config('constant.role.customer'));
        });

        if(!empty($status)){
            $query->where('status',$status);
        }

        $query->orderBy('name','asc');
        return $query;
    }

    public function chatCount(){
        return $this->hasOne('App\Models\ChatCount','user_id','id');
    }

    public function getAdminUserList($request){
        DB::statement("SET SQL_MODE=false");
        $query = $this->newQuery();
        $query->select('users.*','subscribe_members.subscription_plan_id','subscribe_members.name as plan_name','subscribe_members.activated_date','subscribe_members.renewal_date','subscribe_members.square_payment_subscription_id','subscribe_members.device');
        $query->whereHas('roles',function($q){
            $q->where('name',config('constant.role.customer'));
        });

        /*$query->leftJoin('subscribe_members',function($join){
            $join->on('subscribe_members.user_id','=','users.id');
            $join->where('subscribe_members.status','Active');
        });*/

        $query->leftJoin(DB::raw("(select subscribe_members.status,subscribe_members.square_payment_subscription_id,subscribe_members.activated_date,subscribe_members.renewal_date,subscription_plans.name,subscribe_members.user_id,subscribe_members.subscription_plan_id,subscribe_members.device from subscribe_members inner join subscription_plans on subscribe_members.subscription_plan_id=subscription_plans.id where subscribe_members.status='Active' ) as subscribe_members"),'users.id','=','subscribe_members.user_id');

        if($request->filled('n')){
            $query->where('users.name','like','%'.$request->n.'%')->orWhere('users.email','like','%'.$request->n.'%');
        }
        if($request->filled('s')){
            $query->where('users.status',$request->s);
        }
        if($request->filled('p')){
            $query->where('subscribe_members.subscription_plan_id',$request->p);
        }

        # code by sanjay...
        if($request->filled('nm')){
            $query->whereNotIn('users.id', function($query) use ($request){
                $query->select(DB::raw('user_id'))
                    ->from('subscribe_members')
                    ->whereIN('status', ['Active','Cancelled','Upgraded','Pending']);
            });
        }

        /*if($request->filled('f')){
            $query->whereDate('status',$request->s);
        }*/
        if($request->filled('export_to')){
            $data = $query->orderBy('users.id','desc')->groupBy('users.id')->get();
        } else {
            $data = $query->orderBy('users.id','desc')->groupBy('users.id')->paginate(config('constant.adminPerPage'));
        }        
        return $data;
    }

    public function getChatCountUserList($request){
        $query = $this->newQuery();
        $query->join('subscribe_members','subscribe_members.user_id','users.id');
        $query->leftJoin('chat_counts','users.id','=','chat_counts.user_id');

        $query->select('users.*','chat_counts.admin_id',DB::raw('ifnull(chat_counts.admin_total,0) as total'));
        $query->whereHas('roles',function($q){
            $q->where('name',config('constant.role.customer'));
        });
        
        // $query->whereIn('subscribe_members.subscription_plan_id',[config('constant.elite_member_id'),config('constant.yearly_elite_member_id')]);
        $query->where('subscribe_members.status','Active');

        $query->orderBy('chat_counts.admin_total','desc')->orderBy('chat_counts.id','desc');
        $data = $query->get();
        return $data;
    }

    public function getActivePlan($user_id){
        $plan = DB::table('subscribe_members')->join('subscription_plans','subscribe_members.subscription_plan_id','=','subscription_plans.id')->where(['subscribe_members.status'=>'Active','subscribe_members.user_id'=>$user_id])->select('subscription_plans.name','subscribe_members.square_payment_subscription_id as square_payment_subscription_id','subscribe_members.device','subscription_plans.id',DB::raw('ifnull(subscription_plans.price,"Free") as price'))->first();
        return $plan;
    }


    /**
     * code by sanjay
    */
    public function getNonMemberCustomer($request,$status=''){
        $query = $this->newQuery();
        $query->whereHas('roles',function($q){
            $q->where('name',config('constant.role.customer'));
        });

        if(!empty($status)){
            $query->where('status',$status);
        }

        $query->whereNotIn('id', function($query) use ($request){
            $query->select(DB::raw('user_id'))
                ->from('subscribe_members')
                ->whereIN('status', ['Active','Cancelled','Upgraded','Pending']);
        });
        
        $query->orderBy('name','asc');
        return $query;
    }


    public function getAllCustomer($request,$status=''){
        $query = $this->newQuery();
        $query->whereHas('roles',function($q){
            $q->where('name',config('constant.role.customer'));
        });

        if(!empty($status)){
            $query->where('status',$status);
        }

        // $query->whereIn('id', function($query) use ($request){
        //     $query->select(DB::raw('user_id'))
        //         ->from('role_user')
        //         ->whereIN('role_id', [1,2]);
        // });

        $query->orderBy('name','asc');
        return $query;
    }
}
