<?php

//namespace App\Core;

use Firebase\JWT\JWT as JWTFIREBASE;
/*
    The 'iss' (issuer) =>  claim matches the identifier of your Okta Authorization Server
    The 'aud' (audience) =>  claim should match the Client ID used to request the ID Token
    The 'iat' (issued at time) =>  claim indicates when this ID token was issued
    The 'exp' (expiry time) =>  claim is the time at which this token will expire
    The 'nonce'  => claim value should match whatever was passed when requesting the ID token
    */

class Jwt{
    protected $issuedAt;
    protected $jwt_secrect;
    protected $expire;
    protected $token;
    protected $jwt;

    public function __construct()
    {
        // date_default_timezone_set('America/Lima');
        $this->issuedAt = time();

        // Token Validity (3600 second = 1hr or 60 * 60)
        $this->expire = $this->issuedAt + 120 ;
        
        // Set your secret or signature
        $this->jwt_secrect = "MYSECREYKEY";
    }

    // ENCODING THE TOKEN
    public function generate($data, $iss = ""){
        $this->token = [
            // Adding the identifier to the token (who issue the token)
            'iss' => self::Aud(), //$iss
            'aud' => self::Aud(), //$iss
            // Adding the current timestamp to the token, for identifying that when the token was issued.
            "iat" => $this->issuedAt,
            // Token expiration
            "exp" => $this->expire,
            // Payload
            "data"=> $data
        ];
        $this->jwt = JWTFIREBASE::encode($this->token,$this->jwt_secrect);
        return $this->jwt;
    }

    public function refresh(){}

    public function checkToken($jwt_token){
        try {
            $decode = JWTFIREBASE::decode($jwt_token,$this->jwt_secrect, array('HS256'));
            return [
                'success' => true,
                '_token' => $decode->data
            ];
        } 
        catch(\Firebase\JWT\ExpiredException $e){
            return $this->msg_res($e->getMessage(),false);
        }
        catch(\Firebase\JWT\SignatureInvalidException $e){
            return $this->msg_res($e->getMessage(),false);
        }
        catch(\Firebase\JWT\BeforeValidException $e){
            return $this->msg_res($e->getMessage(),false);
        }
        catch(\DomainException $e){
            return $this->msg_res($e->getMessage(),false);
        }
        catch(\InvalidArgumentException $e){
            return $this->msg_res($e->getMessage(),false);
        }
        catch(\UnexpectedValueException $e){
            return $this->msg_res($e->getMessage(),false);
        }
    }

    protected function msg_res($msg,$state = true){
        return [
            'success' => $state,
            'message' => $msg,
        ];
    }

    static public function destroyToken(){
         
    }
 
    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
