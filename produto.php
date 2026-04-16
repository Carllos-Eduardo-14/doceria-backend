<?php
class Produto {
    private $conn;
    private $table = "produto";

    public function __construct($db){
        $this->conn = $db;
    }

    // Listar produtos com média de avaliação
    public function listar(){
        try {
            $sql = "SELECT 
                        p.idProduto,
                        p.nome,
                        p.descricao,
                        p.preco,
                        p.estoque,
                        COALESCE(AVG(a.nota), 0) AS media
                    FROM produto p
                    LEFT JOIN avaliacao a 
                        ON p.idProduto = a.idProduto
                    GROUP BY 
                        p.idProduto, p.nome, p.descricao, p.preco, p.estoque";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }

    // Criar produto
    public function criar($nome, $descricao, $preco, $estoque){
        try {
            $sql = "INSERT INTO {$this->table}
                    (nome, descricao, preco, estoque)
                    VALUES (:nome, :descricao, :preco, :estoque)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ":nome" => $nome,
                ":descricao" => $descricao,
                ":preco" => $preco,
                ":estoque" => $estoque
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }

    // Buscar produtos por nome
    public function buscar($termo = ''){
        try {
            $sql = "SELECT 
                        p.idProduto,
                        p.nome,
                        p.descricao,
                        p.preco,
                        p.estoque,
                        COALESCE(AVG(a.nota), 0) AS media
                    FROM produto p
                    LEFT JOIN avaliacao a 
                        ON p.idProduto = a.idProduto
                    WHERE p.nome LIKE :termo
                    GROUP BY 
                        p.idProduto, p.nome, p.descricao, p.preco, p.estoque";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":termo" => "%$termo%"
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }
}
?>