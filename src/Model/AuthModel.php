<?php
declare(strict_types=1);

namespace App\Model;

use Exception;
use App\Model\User;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

/**
 * AuthModel class
 */
final class AuthModel
{
    protected $database;
    protected $secret_key;
    protected $status;

    public function __construct(\PDO $database, $status = true)
    {
        $this->database = $database;
        $this->status = $status;
        $this->secret_key = "aQD7e4s25RpeoFtLes@g8toHMDZx&&h#Yg%75PYN";
        $this->secretPassword = "yytrS6j3zXd7KZ6WMAVitQ7ssie8k7qsED5KaqGZWN";
    }

    public function processLogin($email, $password, $status = '')
    {
        $result['status']           = false;
        $result['message']          = '';
        
        $loginstatus                = false;

        $user                       = User::where('email', $email)->first();

        if (!$user) { 
            $result['message'] = "The password or email you entered is incorrect!";
            return $result;
        }

        // check if using newer hash (general user)
        if (password_verify($password, $user->password)) {
            $loginstatus = true;
        }
        
        // sorry, please recheck your password.
        if($status){
            if (!$loginstatus) {
                $result['message'] = "The password or email you entered is incorrect!";
                return $result;
            }

        } else {
            $loginstatus = true;
        }

        if ($loginstatus) {

            $result["user"]	    = [
                'id'            => $user->id,
                'name'          => $user->name,
                'role'          => ($user->role ? $user->role->name : 'user'),
                'email'         => $user->email,
                'key'           => $this->generateToken($user->id, $email, $user->name)
            ];
            $result["status"]	= true;
            $result["message"]	= "Login successfully!";
        }

        return $result;
    }

    public function processLoginGoogle($email, $password, $status = '')
    {
        $result['status']           = false;
        $result['message']          = '';
        
        $loginstatus                = false;
        $social_id                  = Admin::where('social_id', $email)->where('role_id', 2)->first();

        if(!$social_id){
            $result['message'] = "The password or email you entered is incorrect!";
            return $result;
        }

        $user                  = $social_id;

        if($user){
            $loginstatus = true;
        }
        
        if ($loginstatus) {
            $result["user"]	    = [
                'id'            => $user->id,
                'name'          => $user->name,
                'role'          => ($user->role ? $user->role->name : 'user'),
                'email'         => $user->email,
                'key'           => $this->generateToken($user->id, $email, $user->name)
            ];
            $result["status"]	= true;
            $result["message"]	= "Login successfully!";
        }

        return $result;
    }

    public function generateToken($id, $email, $name)
    {
        $configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->secret_key)
        );

        $now   = new \DateTimeImmutable();
        $token = $configuration->builder()
                ->identifiedBy('4f1g23a12aa')
                ->issuedAt($now)
                ->withClaim('id', $id)
                ->withClaim('email', $email)
                ->withClaim('name', $name)
                ->getToken($configuration->signer(), $configuration->signingKey());
        
        return $token->toString();
    }

    public function validateToken($status = true)
    {
        $result                 = array();
        $result['status']       = false;
        $result['message']      = 'Please sign-in first';
        try {
            if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
                $jwt = $_SERVER['HTTP_AUTHORIZATION'];
                $configuration = Configuration::forSymmetricSigner(
                    new Sha256(),
                    InMemory::plainText($this->secret_key)
                );
                if (preg_match('/Bearer\s(\S+)/', $jwt, $matches)) {
                    $jwt = $matches[1];
                }
                $parser = $configuration->parser()->parse($jwt);
                
                assert($parser instanceof Plain);
    
                $parsed_data    = $parser->claims()->all();
                
                if (!empty($parsed_data['id'])) {
                    $user               = User::whereId($parsed_data['id'])->select('id','name', 'email','phone', 'date_of_birth', 'identify as country', 'image', 'order_reminder', 'update_offer')->first();
                    $user['order_reminder'] = $user->order_reminder == '1' ? true : false;
                    $user['update_offer'] = $user->update_offer == '1' ? true : false;
                    if($user){
                        $result['status']   = true;
                        $result['message']  = "Success";
                        $result['data']     = $user;
                    } else {
                        $result['status']   = false;
                        $result['message']  = "Please sign-in first";
                        $result['data']         = [
                            'auth'              => false
                        ];
                    }
                }

            } else {
                if($status == true){

                    $result['status']   = false;
                    $result['message']  = "Token or authorization incomplete.";
                    $result['data']         = [
                        'auth'              => false
                    ];
                } else {
                    if($status == true){
                        $result['status']   = false;
                        $result['message']  = "Please sign-in first";
                        $result['data']         = [
                            'auth'              => false
                        ];
                    } else {
                        $result['status']   = true;
                        $result['message']  = "Success.";
                    }
                }
            }
        } catch(Exception $e) {
            if($status == true){
                $result['status']   = false;
                $result['message']  = "Please sign-in first";
                $result['data']         = [
                    'auth'              => false
                ];
            } else {
                $result['status']   = true;
                $result['message']  = "Success.";
            }
        }


        if (!$result['status']) {
            header('Content-Type: application/json');
            echo json_encode($result);
            die();
        }
        
        return $result;
        
    }

    public function generateRandomString($length = 64) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function generateRandom($length = 64) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-%#$@!_=+';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}