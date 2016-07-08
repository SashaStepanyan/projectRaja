<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin/person';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }
    public function myreset(Request $request){
        return view('auth.passwords.email');
    }
    public function emailsend(Request $request){
        if($request->ajax()){

            $email = $_POST['email'];
            $user = User::where('email', '=', $email)->first();
            
            if ($user === null) {
//                $request->session()->put('status', 'Sending error');
                return array('status'=>'danger', 'message'=>'This email was not found in our system');

            }else{

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers = 'Content-type: text/html; charset=utf-8' . "\r\n";

                $token = $_POST['token'];
                $link = url('password/reset', $token).'?email='.urlencode($email);
                $subject="Your  Password Reset Link";
                $body="Click here to reset your password: <a href='".$link."'> ".$link." </a>";

               mail($email,$subject,$body,$headers);
                return array('status'=>'success', 'message'=>'Check your email address and reset instructions');
            }


        }

    }
    
    public function resetpassword($token){
        $email=$_GET['email'];
        return view('auth.passwords.reset',['token'=>$token,'email'=>$email]);
    }
    public function postReset(Request $request)
    {

        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
        $userID=DB::table('users')->where('email', $request->email)->first()->id;
        $user=User::find($userID);

        $user->password = bcrypt($request->password);

        $user->save();
        return redirect('login');

    }
   

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
