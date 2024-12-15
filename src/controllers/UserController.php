<?php
require __DIR__ . "/../utils/Jwt.php";
class UserController
{

    public function __construct(private UserModel $userModel) {}
    public function assign($method, $parts)
    {
        switch ($method) {
            case "Post":
                if ($parts[2] == "register") {
                    $this->register();
                } elseif ($parts[2] == "login") {
                    $this->login();
                } else {
                    $this->notFound();
                }
        }
    }

    public function register()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $username = $input["username"] ?? null;
            $password = $input["password"] ?? null;

            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "msg" => "Missing username or password"]);
                exit;
            }
            $id = $this->userModel->register($username, $password);
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "data" => [
                    "id" => $id,
                    "username" => $username,
                    "password" => $password
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }


    public function login()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $username = $input["username"];
            $password = $input["password"];

            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "msg" => "Both username and password required"]);
                exit;
            }

            $user = $this->userModel->getByUsername($username);
            if (!$user) {
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Incorrect username or password"]);
                exit;
            }

            if (password_verify($password, $user["password"])) {
                //generate token

                $payload = [
                    "id" => $user['id'],
                    "username" => $user["username"],
                    "role"=>$user["role"]
                ];

                $secretKey = getenv('SECRET_KEY');
                $JwtController = new Jwt($secretKey);

                $token = $JwtController->encode($payload);
                http_response_code(200);
                echo json_encode(["status" => "success", "token" => $token]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Incorrect username or password"]);
                exit;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }

    public function updateUser()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $id = $_REQUEST["id"];

            $allowedFields = ['username', 'fullname', 'email'];
            
            foreach ($input as $key => $value) {
                
                if (!in_array($key, $allowedFields)) {
                    // If the key is not allowed, return an error
                    http_response_code(400);
                    echo json_encode(["status" => "error", "msg" => "Invalid field: $key"]);
                    exit; 
                } 
            }
        

            $user = $this->userModel->update($id, $input);
            http_response_code(200);
            echo json_encode(["status" => "success", "data"=>$user]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }

    public function getUser()
    {
       try {
        $id = $_REQUEST["id"];
        $result = $this->userModel->getById($id);
        if ($result) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "data" => $result
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "msg" => "User not found"
            ]);
        }
       } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
       }
    }


    public function getAllUsers()
    {
      try {
        $result=$this->userModel->getAll();
        if(!$result){
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "msg" => "No users"
            ]);
        }
        else{
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "data" => $result
            ]);
        }
      } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
      }
    }
    public function notFound() {}
}
