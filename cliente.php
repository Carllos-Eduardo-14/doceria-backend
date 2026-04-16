<?php
class Cliente {
    private $conn;
    private $table = "cliente";

    public $idCliente;
    public $nome;
    public $telefone;
    public $email;
    public $senha;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar cliente
    public function criar() {
        $sql = "INSERT INTO {$this->table}
                (nome, telefone, email, senha)
                VALUES (:nome, :telefone, :email, :senha)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":nome" => $this->nome,
            ":telefone" => $this->telefone,
            ":email" => $this->email,
            ":senha" => password_hash($this->senha, PASSWORD_DEFAULT)
        ]);
    }

    // Buscar cliente por email (login)
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":email" => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar clientes
    public function listar() {
        $sql = "SELECT idCliente, nome, email, telefone FROM {$this->table}";
        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>