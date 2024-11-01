<?php

namespace App\Controller;

use Carbon\Carbon;
use App\Model\Cart;
use App\Model\User;
use App\Model\AuthModel;
use App\Repository\Email;
use App\Helper\Validation;
use Illuminate\Support\Str;
use Pimple\Psr11\Container;
use App\Helper\JsonResponse;
use App\Model\PasswordReset;
use App\Notifications\ForgotNotification;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AuthController
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container        = $container;
        $this->authModel        = new AuthModel($this->container->get('db'), ['admin', 'super', 'user', 'copywritter', 'store_ops']);
    }

    public function login(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();
        $email 			        = isset($post["email"]) ? $post["email"] : '';
        $password 			    = isset( $post["password"]) ?  $post["password"] : '';

        $validation             = new Validation($this->container , $request, [
            'email'             => 'required',
            'password'          => 'required',
        ]);

        $validation->validate();

        if(!$email && !$password){
            $result = [ 
                'status' => false, 
                'message' => 'Please fill in your sign in details',
            ];

            return JsonResponse::withJson($response, $result, 200);
        }

        if(!$email){
            $result = [ 
                'status' => false, 
                'message' => 'Please fill in the email',
            ];

            return JsonResponse::withJson($response, $result, 200);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = [ 
                'status' => false, 
                'message' => 'Please adjust the following: Incorrect email or password',
            ];

            return JsonResponse::withJson($response, $result, 200);
        }

        if(!$password){
            $result = [ 
                'status' => false, 
                'message' => 'Please fill in the password',
            ];

            return JsonResponse::withJson($response, $result, 200);
        }

        $data                   = $this->authModel->processLogin($email, $password, true);

        $result                 = [
            'status'            => $data['status'],
            'message'           => $data['message'],
            'data'              => (isset($data['user']) ? $data['user'] : [])
        ];

        return JsonResponse::withJson($response, $result, 200);
    }

    public function register(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();
        $firstname 			    = isset($post["firstname"]) ? $post["firstname"] : '';
        $lastname 			    = isset($post["lastname"]) ? $post["lastname"] : '';
        $email 			        = isset($post["email"]) ? $post["email"] : '';
        $password 			    = isset( $post["password"]) ?  $post["password"] : '';

        $validation             = new Validation($this->container , $request, [
            'firstname'         => 'required',
            'lastname'          => 'required',
            'email'             => 'required|email',
            'password'          => 'required',
        ]);

        $validation->validate();

        $check                  = User::where('email', $email)->where('role_id', 2)->first();

        if($check){
            $result                 = [
                'status'            => false,
                'message'           => 'email already taken',
            ];

            return JsonResponse::withJson($response, $result, 200);
        }

        $name                   = $firstname.' '.$lastname;

        $data = User::create([
            'name'              => $name,
            'email'             => $email,
            'password'          => password_hash($password, PASSWORD_DEFAULT),
            'role_id'           => 2,
            'image'             => 'https://ui-avatars.com/api/?name='.(str_replace(' ', '+', $name))
        ]);

        if($data){

            $token              = $this->authModel->generateRandomString();
            
            PasswordReset::create([
                'email'         => $post['email'],
                'token'         => $token
            ]);

            $result                 = [
                'status'            => true,
                'message'           => 'successfully',
            ];
        } else {
            $result                 = [
                'status'            => false,
                'message'           => 'failed',
            ];
        }


        return JsonResponse::withJson($response, $result, 200);
    }

    public function validateUser(Request $request, Response $response): Response
    {
        $result               = $this->authModel->validateToken();

        return JsonResponse::withJson($response, $result, 200);
    }

    public function verify(Request $request, Response $response): Response
    {
        $data                   = $request->getParsedBody();
        $user                   = User::whereEmail($data['email'])->where('role_id', 2)->first();
        $token                  = '';
        if($user){
            $check              = PasswordReset::where('email', $user->email)->where('token', $data['token'])->first();

            if($check){
                $user->update([
                    'email_verified_at' => Carbon::now('Asia/Jakarta')
                ]);
                $check->delete();

                $data                   = $this->authModel->processLogin($user->email, 'test', false);
                $auth                   = (isset($data['user']) ? $data['user'] : []);
                $token                  = "login=manual&token=".$auth['key']."&email=".$auth['email']."&name=".$auth['name'];
            }

            $result                 = [
                'status'            => true,
                'message'           => 'Successfully',
                'data'              => $token
            ];
    
            return JsonResponse::withJson($response, $result, 200);
        } else {
            $result                 = [
                'status'            => false,
                'message'           => 'Failed',
            ];
    
            return JsonResponse::withJson($response, $result, 200);
        }
    }

    public function social(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();
        $type 			        = isset($post["type"]) ? $post["type"] : '';
        $token 			        = isset( $post["token"]) ?  $post["token"] : '';

        $validation             = new Validation($this->container , $request, [
            'type'              => 'required',
            'token'             => 'required',
        ]);

        $validation->validate();

        if ($type == 'facebook') {
            $url = 'https://graph.facebook.com/me';
        } else {
            $url = 'https://www.googleapis.com/oauth2/v3/userinfo';
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$token
        ),
        ));

        $tampil = json_decode(curl_exec($curl), true);

        curl_close($curl);

        if (isset($tampil['id']) or isset($tampil['sub'])) {
            $email = isset($tampil['email']) ? $tampil['email'] : null;

            $socialId = isset($tampil['id']) ? $tampil['id'] : (isset($tampil['sub']) ? $tampil['sub'] : null);
            
            if($type == 'facebook'){
                $check = User::where('social_id', $socialId)->where('role_id', 2)->first();
            } else {
                $check = User::where('social_id', $socialId)->where('role_id', 2)->orWhere('email', $email)->where('role_id', 2)->first();
            }
            
            if ($check) {
                
                $check->update([
                    'social_id'         => $socialId,
                    'email_verified_at' => Carbon::now('Asia/Jakarta'),
                    'social_type'       => $post['type']
                ]);

                $data                   = $this->authModel->processLoginGoogle($check->social_id, 'test',  'social');
                
                $result                 = [
                    'status'            => true,
                    'message'           => $data['message'],
                    'data'              => (isset($data['user']) ? $data['user'] : [])
                ];
    
                return JsonResponse::withJson($response, $result, 200);
            } else {

                $name               = isset($tampil['name']) ? $tampil['name'] : null;

                $user = User::create([
                    'social_id'         => $socialId,
                    'role_id'           => 2, 
                    'email'             => $email,
                    'name'              => $name,
                    'email_verified_at' => Carbon::now('Asia/Jakarta'),
                    'password'          => password_hash('test', PASSWORD_DEFAULT),
                    'social_type'       => $post['type'],
                    'image'             => 'https://ui-avatars.com/api/?name='.(str_replace(' ', '+', $name))
                ]);

                $data                   = $this->authModel->processLoginGoogle($user->social_id, 'test', 'social');

                $result                 = [
                    'status'            => $data['status'],
                    'message'           => $data['message'],
                    'data'              => (isset($data['user']) ? $data['user'] : [])
                ];
            }
        } else {
            $result                 = [
                'status'            => false,
                'message'           => 'failed',
                'data'              => $tampil
            ];
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function forgot(Request $request, Response $response): Response
    {
        $validation             = new Validation($this->container , $request, [
            'email'         => 'required',
        ]);

        $validation->validate();

        $post               = $request->getParsedBody();
        $email 			    = isset($post["email"]) ? $post["email"] : '';
        $input              = User::where('email', $email)->where('role_id', 2)->first();

        if ($input) {
            $token = Str::random(90);
            PasswordReset::insert(
            ['email' => $email, 'token' => $token, 'created_at' => Carbon::now()]
            );

            $mail           = new ForgotNotification();

            $mail->sendMail($email, $input->name, $token);

            $result['status']     = true;
            $result['message']    = 'Email already sent';
        } else {
            $result['status']     = false;
            $result['message']    = 'Data Not Found';
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function resetPassword(Request $request, Response $response): Response
    {
        $validation                     = new Validation($this->container , $request, [
            'email'                     => 'required|email',
            'token'                     => 'required',
            'password'                  => 'required',
            'password_confirmation'     => 'required',
        ]);

        $validation->validate();

        $post                       = $request->getParsedBody();
        $email 			            = isset($post["email"]) ? $post["email"] : '';
        $token 			            = isset($post["token"]) ? $post["token"] : '';
        $password 			        = isset($post["password"]) ? $post["password"] : '';
        $password_confirmation 	    = isset($post["password_confirmation"]) ? $post["password_confirmation"] : '';
        
        if (!empty($email) or !empty($token) or !empty($password)) {

            if(strcmp($password, $password_confirmation) !== 0) {
                
                $result['status']     = false;
                $result['message']    = 'Password tidak sama';
                $result['data']       = [
                    'password'        => 'Password tidak sama'
                ];

                return JsonResponse::withJson($response, $result, 200);
            }

            $updatePassword = PasswordReset::where(['email' => $post['email'], 'token' => $post['token']])->first();

            if(!$updatePassword) {
                $result['status']     = false;
                $result['message']    = 'Gagal';
                $result['data']       = [
                    'token'           => 'Invalid token'
                ];

                return JsonResponse::withJson($response, $result, 200);
            }

            User::where('email', $post['email'])->where('role_id', 2)->update(['password' => password_hash($post['password'], PASSWORD_DEFAULT)]);

            PasswordReset::where(['email'=> $post['email']])->delete();

            $result['status']     = true;
            $result['message']    = 'Berhasil update profile';
        } else {
            $result['status']     = false;
            $result['message']    = 'Email wajib diisi';
        }

        return JsonResponse::withJson($response, $result, 200);
    }
}