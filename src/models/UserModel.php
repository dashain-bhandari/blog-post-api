<?php

class UserModel
{
    private $conn;

    private $table = "users";

    public $id;

    public $username;

    public $password;

    public $email;

    public $role;

    public $fullname;

    public $created_at;


    public function __construct($db)
    {
        $this->conn = $db;
        $this->createTable();
    }

    private function createTable()
    {
        $query1 = "CREATE TYPE user_role AS ENUM ('super-admin', 'user')";
        $query = "
        CREATE TABLE IF NOT EXISTS {$this->table} (
              id SERIAL PRIMARY KEY,
            username VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(100),
            fullname VARCHAR(255),
            email VARCHAR(255),
            role user_role NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        try {
            $stmt = $this->conn->prepare($query1);
            $stmt->execute();
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Table creation error: " . $e->getMessage());
        }
    }


    public function getByUsername($username)
    {
        try {
            $query = "SELECT * FROM {$this->table} WHERE (username = :username) LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return null;
            }

            return $data;
        } catch (PDOException $e) {
            throw new Exception("Database error:" . $e->getMessage());
        }
    }
    public function register($username, $password)
    {
        try {
            $query = "INSERT INTO {$this->table} (username,password) VALUES (:username,:password)";
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $hash);

            if ($stmt->execute()) {

                return $this->conn->lastInsertId();
            } else {

                throw new Exception("Failed to create post");
            }
        } catch (PDOException $e) {

            throw new Exception("Database error:" . $e->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $query = "SELECT * FROM {$this->table} WHERE (id = :id) LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return null;
            }
            return $data;
        } catch (PDOException $e) {
            echo json_encode(["eror", $e->getMessage()]);
            throw new Exception("Database error:" . $e->getMessage());
        }
    }

    public  function getAll()
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
        echo json_encode(["eror", $e->getMessage()]);
        throw new Exception("Database error:" . $e->getMessage());
    }
    }
    public function update($id, $data)
    {

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
}
