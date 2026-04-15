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

    public function criar() {

        // validação básica
        if(empty($this->nome) || empty($this->email) || empty($this->senha)){
            return ["erro" => "Dados incompletos"];
        }

        // verifica email duplicado
        $check = $this->conn->prepare("SELECT idCliente FROM {$this->table} WHERE email = :email");
        $check->execute([":email" => $this->email]);

        if($check->rowCount() > 0){
            return ["erro" => "Email já cadastrado"];
        }

        $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO {$this->table}
                (nome, telefone, email, senha)
                VALUES (:nome, :telefone, :email, :senha)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ":nome" => $this->nome,
            ":telefone" => $this->telefone,
            ":email" => $this->email,
            ":senha" => $senhaHash
        ]);

        return ["status" => "ok", "mensagem" => "Cliente cadastrado"];
    }

    public function listar() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // LOGIN 
    public function login($email, $senha){

        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":email" => $email]);

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if($cliente && password_verify($senha, $cliente['senha'])){
            return [
                "status" => "ok",
                "cliente" => $cliente
            ];
        }

        return ["erro" => "Email ou senha inválidos"];
    }
}
?>