<?php

class CommentController
{
    public function __construct(private CommentModel $commentModel, private UserModel $userModel,private PostModel $postModel) {}


    public function getAllCommentsOfPost($id)
    {
        try {
            $data = $this->commentModel->getAll($id);
            if($data){
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "data" => $data
                ]);
            }
            else{
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Comments not found"]);
            }
         
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }

    }



    public function createComment($id)
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            $postId = $id ;
            $comment = $input['comment'] ?? null;
            $userId = $_REQUEST["id"];

            if (empty($postId) || empty($comment)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "msg" => "Both postId and comment are required."]);
                exit;
            }
    
            $post=$this->postModel->getById($id);
            if(!$post){
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Post with {$id} doesn't exist"]);
                return;
            }
                
                $id = $this->commentModel->createComment($postId, $comment, $userId);

                http_response_code(201);
                echo json_encode([
                    "status" => "success",
                    "data" => [
                        "id" => $id,
                        "comment" => $comment
                    ]

                ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }
}
