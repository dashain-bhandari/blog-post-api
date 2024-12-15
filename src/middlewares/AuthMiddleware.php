<?php
class AuthMiddleware
{
   

    // Dependency Injection via Constructor
    public function __construct(private Jwt $jwt,private UserModel $userModel)
    {
    }

    public function handle(): bool
    {
        if ($this->authenticateJWTToken()) {
            return true;
        }
        return false;
    }

    

    public function authenticateJWTToken(): bool
{
    $headers = $this->getAuthorizationHeader();
    
   if((!empty($headers))){
    if (!preg_match("/^Bearer\s+(.*)$/", $headers, $matches)) {
        http_response_code(400);
        echo json_encode(["message" => "Incomplete authorization header"]);
        return false;
    }

    try {
        $data = $this->jwt->decode($matches[1]); 
    //check if user exist
    $user=$this->userModel->getByUsername($data["username"]);
    if(!$user){
        http_response_code(404);
        echo json_encode(["status"=>"error","message" => "User with above token doesn't exist"]);
        return false;
    }
        $_REQUEST["id"]=$data["id"];
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["message" => $e->getMessage()]);
        return false;
    }

    return true;
   }

    else{
        http_response_code(401);
        echo json_encode(["message" => "Authorization header missing"]);
        return false;
    }

}

private function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
       
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
       
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

   
}
