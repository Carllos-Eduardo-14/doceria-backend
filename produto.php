<?php
class Produto {
    private $conn;
    private $table = "produto";

    public function __construct($db){
        $this->conn = $db;
    }

    // LISTAR PRODUTOS
    public function listar(){
        try {
            $sql = "SELECT 
                        p.idProduto,
                        p.nome,
                        p.descricao,
                        p.preco,
                        p.estoque,
                        c.nome AS categoria,
                        ROUND(COALESCE(AVG(a.nota), 0), 1) AS media
                    FROM produto p
                    LEFT JOIN categoria c 
                        ON p.idCategoria = c.idCategoria
                    LEFT JOIN avaliacao a 
                        ON p.idProduto = a.idProduto
                    GROUP BY p.idProduto";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // formatar preço
            foreach($produtos as &$p){
                $p["preco"] = number_format($p["preco"], 2, ',', '.');
            }

            return [
                "status" => "ok",
                "produtos" => $produtos
            ];

        } catch (PDOException $e) {
            return ["erro" => "Erro ao listar produtos"];
        }
    }

    // CRIAR PRODUTO
    public function criar($nome, $descricao, $preco, $idCategoria, $estoque){
        try {

            if(!is_numeric($preco) || $preco <= 0){
                return ["erro" => "Preço inválido"];
            }

            $sql = "INSERT INTO {$this->table}
                    (nome, descricao, preco, idCategoria, estoque)
                    VALUES (:nome, :descricao, :preco, :categoria, :estoque)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ":nome" => $nome,
                ":descricao" => $descricao,
                ":preco" => $preco,
                ":categoria" => $idCategoria,
                ":estoque" => $estoque
            ]);

            return ["status" => "ok"];

        } catch (PDOException $e) {
            return ["erro" => "Erro ao criar produto"];
        }
    }

    // BUSCAR PRODUTOS
    public function buscar($termo = '', $idCategoria = ''){
        try {
            $sql = "SELECT 
                        p.idProduto,
                        p.nome,
                        p.descricao,
                        p.preco,
                        p.estoque,
                        ROUND(COALESCE(AVG(a.nota), 0), 1) AS media
                    FROM produto p
                    LEFT JOIN avaliacao a 
                        ON p.idProduto = a.idProduto
                    WHERE 1=1";

            $params = [];

            if(!empty($termo)){
                $sql .= " AND p.nome LIKE :termo";
                $params[":termo"] = "%$termo%";
            }

            if(!empty($idCategoria)){
                $sql .= " AND p.idCategoria = :categoria";
                $params[":categoria"] = $idCategoria;
            }

            $sql .= " GROUP BY p.idProduto";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // formatar preço
            foreach($produtos as &$p){
                $p["preco"] = number_format($p["preco"], 2, ',', '.');
            }

            return [
                "status" => "ok",
                "produtos" => $produtos
            ];

        } catch (PDOException $e) {
            return ["erro" => "Erro na busca"];
        }
    }
}
?>