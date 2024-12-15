<?php

class PostImageModel
{
    private $conn;

    private $table = "images";

    public $postId;
    public $id;

    public $filename;


    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->createTable();
    }

    private function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id SERIAL PRIMARY KEY,

            filename TEXT NOT NULL,

            post_id INT NOT NULL,
          
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
        )";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Table creation error: " . $e->getMessage());
        }
    }


    public function createPostImage(string $filename, int $post_id)
    {
        try {
            $query = "INSERT INTO {$this->table} (post_id,filename) VALUES (:post_id, :filename)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":filename", $filename);
            $stmt->bindParam(":post_id", $post_id);
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            } else {
                throw new Exception("Something went wrong.");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error:".$e->getMessage());
        }
    }

    public function deletePostImage(int $postId, string $filename)
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE post_id = :postId AND filename = :filename";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":postId", $postId);
            $stmt->bindParam(":filename", $filename);

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return true; // Record was successfully deleted
                } else {
                    return false;
                }
            
            } else {
                throw new Exception("Failed to delete image record from database.");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error:".$e->getMessage());
        }
    }

    public function getFileName(int $fileId)
    {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :fileId LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":fileId", $fileId);


            if ($stmt->execute()) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($data == false) {
                    return null;
                   
                }
                return $data["filename"];
            } else {
                throw new Exception("Something went wrong");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error:".$e->getMessage());
        }
    }

    public function getAll(int $postId)
    {
        try {
            $query = "SELECT  i.id, 
            i.filename,  
            i.created_at
            FROM {$this->table} i
             
            LEFT JOIN  posts p ON p.id = i.post_id
            WHERE p.id=$postId 
                 ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $data = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            if (count($data) == 0) {
               return null;
            }

          return $data;

        } catch (PDOException $e) {
            throw new Exception("Database error:".$e->getMessage());
        }
    }
}
