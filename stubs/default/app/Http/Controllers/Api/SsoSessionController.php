<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SSOSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoSessionController extends Controller
{
    protected mixed $authToken;

    public function __construct()
    {
        $this->authToken = config('sso-session.SSO_AUTH_TOKEN');
    }

    public function auth(Request $request)
    {
        $headers = $request->header();
        $username = $request->input('username');
        $password = $request->input('password');

        if(isset($headers['auth'])) {
            $authToken = $headers['auth'][0];
            $authToken = explode(' ', $authToken)[1];
            if ($authToken != $this->authToken) {
                echo json_encode(array("accessGranted" => false, "username" => htmlentities($username), "message"=>"Invalid Auth Token"));
                return true;
            }

            if (!$username || !$password) {
                echo json_encode(array("accessGranted" => false, "username" => htmlentities($username), "message"=>"Username & Password is Required"));
            }

            $isVerified = false;
            $user = null;
            $internal = 0;
            if (Auth::attempt(['username' => $username, 'password' => $password]))
            {
                $user = auth()->user();
                $isVerified = true;
            }

            if ($isVerified && $user) {
                $user_data = array(
                    'USERS_USERNAME'=>$user->username,
                    'USERS_NAME'=>$user->name,
                    'USERS_EMAIL'=>$user->npk.'@pupukkaltim.com',
                    'USERS_NPK'=>$user->npk,
                    'USERS_IS_INTERNAL'=>$internal
                );
                echo json_encode(array("accessGranted" => true,"data"=>$user_data, "username" => htmlentities($username)));
            } else {
                echo json_encode(array("accessGranted" => false, "username" => htmlentities($username),  "message"=>"Incorrect username or password"));
            }
        } else {
            echo json_encode(array("accessGranted" => false, "username" => htmlentities($username), "message"=>"Auth Token is Required"));
        }
        return true;
    }

    public function set(Request $request)
    {
        $headers = $request->header('Auth');

        if (isset($headers)) {
            $authToken = $headers;
            $authToken = explode(' ', $authToken)[1];
            if ($authToken !== $this->authToken) {
                return response()->json(array('status' => false, 'error' => true, 'message' => 'Data failed to create'));
            }

            $data = array(
                'SESSION_ID' => $request->SESSION_ID,
                'TOKEN' => $request->TOKEN,
                'USER_ID' => $request->USER_ID,
                'USER_ALIASES' => $request->USER_ALIASES
            );
            $resp = SSOSession::create($data);

            if ($resp) {
                return response()->json(array('status' => true, 'error' => false, 'message' => 'Data successfully created'));
            } else {
                return response()->json(array('status' => false, 'error' => true, 'message' => 'Data failed to create'));
            }
        } else {
            return response()->json(array('status' => false, 'error' => true, 'message' => 'Data failed to create 3'));
        }
    }

    public function destroy(Request $request){
        $headers = $request->header('Auth');

        if(isset($headers)){
            $authToken = $headers;
            $authToken = explode(' ', $authToken)[1];
            if ($authToken !== $this->authToken) {
                return response()->json(array('status' => false, 'error' => true, 'message' => 'Data failed to create'));
            }

            $resp = SSOSession::where(array(
                'SESSION_ID'=>$request->SESSION_ID
            ))->delete();
            $request->session()->flush();

            if ($resp) {
                return response()->json(array('status' => true, 'error' => false, 'message' => 'Data successfully created'));
            } else {
                return response()->json(array('status' => false, 'error' => true, 'message' => 'Data failed to create'));
            }
        } else{
            return response()->json(array('status' => false, 'error' => true, 'message' => 'Data failed to create'));
        }
    }

    public function syncUsers(Request $request)
    {
        $lsnpk = $request->data;


        $fail = 0;
        $success = 0;

        $userlist = [];

        if(count($lsnpk) > 0){

            foreach($lsnpk as $item){
                $leader = User::find($item->username);

                if($leader){

                    $arr = [
                        'USERS_NPK' => $leader->NPK,
                        'USERS_PASSWORD' => $leader->PASSWORD,
                        'USERS_EMAIL' => $leader->EMAIL,
                        'USERS_NAME' => $leader->NAME,
                        'USERS_USERNAME' => $leader->USERNAME,
                        'USERS_COST_CENTER_DESCRIPTION' => NULL,
                        'USERS_POSITION' => NULL,
                        'USERS_IS_INTERNAL' => 0,
                    ];

                    $userlist[] = $arr;
                }
            }
        } else {

            $user = User::all();

            foreach($user as $leader){

                $arr = [
                    'USERS_NPK' => $leader->NPK,
                    'USERS_PASSWORD' => $leader->PASSWORD,
                    'USERS_EMAIL' => $leader->EMAIL,
                    'USERS_NAME' => $leader->NAME,
                    'USERS_USERNAME' => $leader->USERNAME,
                    'USERS_COST_CENTER_DESCRIPTION' => NULL,
                    'USERS_POSITION' => NULL,
                    'USERS_IS_INTERNAL' => 0,
                ];

                $userlist[] = $arr;

            }

        }
    }


}
