<?

class Database
{
    private $host;
    private $dbname;
    private $user;
    private $password;
    private $port;
    

    public function __construct()
    {
        $this->host = getenv('DB_HOST');
        $this->dbname = getenv('DB_NAME');
        $this->user = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');
        $this->port = getenv('DB_PORT');
    }
    private $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->dbname";
            $this->conn = new PDO($dsn, $this->user, $this->password,[
                PDO::ATTR_EMULATE_PREPARES=>false,
                PDO::ATTR_STRINGIFY_FETCHES=>false
            ]);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           
        } catch (PDOException $e) {
            echo "Error:-" . $e->getMessage();
        }
        return $this->conn;
    }
}