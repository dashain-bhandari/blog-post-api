<?php

class PostController
{
    public function __construct(private PostModel $postModel) {}

   

    public function getOnePost($id)
    {
        try {
            $result =  $this->postModel->getById($id);
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Post not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }

    public function getAllPosts()
    {
        try {
            $result = $this->postModel->getAll();
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Posts not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }



    public function createPost()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $title = $input['title'] ?? null;
            $content = $input['content'] ?? null;
            $author = $input['author'] ?? null;

            if (empty($title) || empty($author) || empty($content)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "msg" => "Missing title or author or content"]);
                exit;
            }
           $id= $this->postModel->createPost($title, $content, $author);
           
           http_response_code(201);
           echo json_encode(["status" => "sucess", "msg" => "Post of {$id} created"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }

    public function updatePost($id) {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
          
            $allowedFields = ['title', 'content', 'author'];
          
            foreach ($input as $key => $value) {
                
                if (!in_array($key, $allowedFields)) {
                    // If the key is not allowed, return an error
                    http_response_code(400);
                    echo json_encode(["status" => "error", "msg" => "Invalid field: $key"]);
                    exit; 
                } 
            }
        

            //check if post exists
            $post=$this->postModel->getById($id);
            if(!$post){
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Post with {$id} doesn't exist"]);
                return;
            }


            $post = $this->postModel->updatePost($id, $input);
            http_response_code(200);
            echo json_encode(["status" => "success", "data"=>$post]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }

    public function deletePost($id) {
        try {
            //check if post exists
            $post=$this->postModel->getById($id);
            if(!$post){
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Post with {$id} doesn't exist"]);
                return;
            }
           $result=$this->postModel->deletePost($id);
           http_response_code(200);
           echo json_encode(["status" => "success", "msg"=>"Deleted post with id:{$id}"]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }
}
