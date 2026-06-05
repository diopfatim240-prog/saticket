<?php
class DBRepository 
{
    private $host;
    private $dbname;
    private $user;
    private $password;

    protected $db; 

    public function __construct() 
    {
       $this->host = getenv('DB_HOST') ?: 'localhost';
       $this->dbname = getenv('DB_NAME') ?: 'saticket2';
       $this->user = getenv('DB_USER') ?: 'root';
       $this->password = getenv('DB_PASSWORD') ?: '';
       $this->getConnexion();
    }

    private function getConnexion() 
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
        try { 
            $this->db = new PDO($dsn, $this->user, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            throw new Exception("Erreur de connexion à la base de données : " . $error->getMessage());
        }
        return $this->db;
    }

    protected function getExistingColumn(string $table, array $candidateColumns): ?string
    {
        try {
            $columns = $this->db->query("SHOW COLUMNS FROM {$table}")->fetchAll(PDO::FETCH_ASSOC);
            $fields = array_column($columns, 'Field');
            foreach ($candidateColumns as $candidate) {
                if (in_array($candidate, $fields, true)) {
                    return $candidate;
                }
            }
        } catch (PDOException $error) {
            error_log("Erreur getExistingColumn({$table}) : " . $error->getMessage());
        }
        return null;
    }
}
?>