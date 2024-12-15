<?php

class CommentModel
{
    private $conn;

    private $table = "comments";

    public $id;

    public $comment;

    public $postId;

    public $userId;

    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->createTable();
    }

    private function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS comments (
    id SERIAL PRIMARY KEY,
    comment TEXT NOT NULL,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Table creation error: " . $e->getMessage());
        }
    }



    public function getAll($id)
    {
   try {
    $query = "SELECT  c.id, 
    c.comment, 
    p.title AS post_title,
    u.username AS user_name,  
    c.created_at
    FROM {$this->table} c
     
    LEFT JOIN  posts p ON p.id = c.post_id
    LEFT JOIN users u ON u.id = c.user_id
    WHERE p.id=$id
        
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


    public function createComment(int $postId, string $comment, string $userId)
    {
        try {
            $query = "INSERT INTO {$this->table} (user_id,post_id,comment) VALUES (:user_id, :post_id,:comment)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":post_id", $postId);
            $stmt->bindParam(":comment", $comment);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            } else {
                throw new Exception("Failed to create post");
              
            }
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}
