<?php
class Avaliacao {
    private $conn;
    private $table = "avaliacao";

    public function __construct($db){
        $this->conn = $db;
    }

    // Criar avaliação
    public function criar($idProduto, $idCliente, $nota, $comentario){
        $sql = "INSERT INTO {$this->table}
                (idProduto, idCliente, nota, comentario)
                VALUES (:produto, :cliente, :nota, :comentario)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":produto" => $idProduto,
            ":cliente" => $idCliente,
            ":nota" => $nota,
            ":comentario" => $comentario
        ]);
    }

    // Listar avaliações de um produto
    public function listarPorProduto($idProduto){
        $sql = "SELECT a.*, c.nome 
                FROM {$this->table} a
                JOIN cliente c ON a.idCliente = c.idCliente
                WHERE a.idProduto = :produto
                ORDER BY a.idAvaliacao DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":produto" => $idProduto]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Média
    public function media($idProduto){
        $sql = "SELECT AVG(nota) as media, COUNT(*) as total
                FROM {$this->table}
                WHERE idProduto = :produto";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":produto" => $idProduto]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>