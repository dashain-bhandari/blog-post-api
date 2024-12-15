<?php

class PostModel
{
    private $conn;

    private $table = "posts";

    public $id;

    public $title;

    public $content;
    public $author;
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
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            author VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Table creation error: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                return $data;
            } else {
                null;
            }
        } catch (PDOException $e) {
            throw new Exception("Database error:" . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $query = "SELECT * FROM {$this->table}";
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
            throw new Exception("Database error:" . $e->getMessage());
        }
    }


    public function createPost(string $title, string $content, string $author)
    {
        try {
            $query = "INSERT INTO {$this->table} (title, content,author) VALUES (:title, :content,:author)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":content", $content);
            $stmt->bindParam(":author", $author);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            } 
            else {
                throw new Exception("Failed to create post");
            }

        } catch (PDOException $e) {
            throw new Exception("Database error:" . $e->getMessage());
        }
    }

    public function updatePost($id,$data){
        $setPart = [];
        foreach ($data as $column => $value) {
            $setPart[] = "{$column} = :{$column}";
        }
        $setString = implode(", ", $setPart);

        $query = "UPDATE {$this->table}
                  SET {$setString}
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);


        foreach ($data as $column => &$value) {
            $stmt->bindParam(":{$column}", $value);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return $this->getById($id);
            } else {
                throw new Exception("Failed to update the record");
            }
        } catch (Exception $e) {

            throw new Exception("Database error:" . $e->getMessage());
        }
    }

    public function deletePost($id){
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);

            if ($stmt->execute()) {
               return true;
            } else {
                throw new Exception("Failed to delete post record from database.");
            }
        } catch (PDOException $e) {
            throw new Exception("Database error:".$e->getMessage());
        }
    }
}
