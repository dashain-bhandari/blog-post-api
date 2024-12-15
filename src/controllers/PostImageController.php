<?php

class PostImageController
{
    public function __construct(private PostImageModel $postImageModel, private PostModel $postModel) {}




    public function createPostImage($id)
    {
        try {
            //check if post exists
            $post = $this->postModel->getById($id);
            if (!$post) {
                http_response_code(400);
                echo json_encode(["status" => "error", "msg" => "Post with id {$id} doesn't exist."]);
                return;
            }

            if (isset($_FILES['fileToUpload'])) {

                $target_dir = "uploads/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $fileName = basename($_FILES["fileToUpload"]["name"]);
                $target_file = $target_dir . $fileName;


                $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


                $allowedTypes = ['jpeg', 'png'];

                // Check if the file type is valid
                if (!in_array($fileType, $allowedTypes)) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "msg" => "Only JPEG, and PNG files are allowed."]);
                    return;
                }
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    try {
                        $result = $this->postImageModel->createPostImage($target_file, $id);
                        http_response_code(201);
                        echo json_encode(["status" => "success", "msg" => "The file has been uploaded successfully.", "file" => $target_file]);
                    } catch (Exception $e) {
                        unlink($target_file);
                        http_response_code(500);
                        echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "msg" => "Sorry, there was an error uploading your file."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "msg" => "File is required."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }

    public function deletePostImage($postId, $fileId)
    {
        try {
            $filename = $this->postImageModel->getFileName($fileId);

            if (!$filename) {
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Image not found"]);
                return;
            }

            if (is_file($filename)) {
                if (unlink($filename)) {
                    $result = $this->postImageModel->deletePostImage($postId, $filename);
                    if ($result) {
                        http_response_code(200);
                        echo json_encode(["status" => "success", "msg" => "File deleted."]);
                    } else {
                        http_response_code(404);
                        echo json_encode(["status" => "error", "msg" => "Image associated with the post not found"]);
                        return;
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "msg" => "Failed to delete the file."]);
                    return;
                }
            } else {
                $result = $this->postImageModel->deletePostImage($postId, $filename);
                if ($result) {
                    http_response_code(200);
                    echo json_encode(["status" => "success", "msg" => "File deleted."]);
                } else {
                    http_response_code(404);
                    echo json_encode(["status" => "error", "msg" => "Image associated with the post not found"]);
                    return;
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }

    public function getAllImages($postId)
    {
        try {
            $result = $this->postImageModel->getAll($postId);
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "msg" => "Images not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
        }
    }
}
