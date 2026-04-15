<?php
class Avaliacao {
    private PDO $conn;
    private string $table = "avaliacao";

    public function __construct(PDO $db){
        $this->conn = $db;
    }

    // Criar avaliação
    public function criar(int $idProduto, int $idCliente, float $nota, string $comentario): bool {
        try {
            if ($nota < 1 || $nota > 5) {
                throw new Exception("Nota deve ser entre 1 e 5");
            }

            $sql = "INSERT INTO {$this->table}
                    (idProduto, idCliente, nota, comentario, dataAvaliacao)
                    VALUES (:produto, :cliente, :nota, :comentario, NOW())";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ":produto" => $idProduto,
                ":cliente" => $idCliente,
                ":nota" => $nota,
                ":comentario" => htmlspecialchars($comentario)
            ]);

        } catch (Exception $e) {
            error_log("Erro ao criar avaliação: " . $e->getMessage());
            return false;
        }
    }

    // Listar avaliações de um produto
    public function listarPorProduto(int $idProduto): array {
        try {
            $sql = "SELECT a.*, c.nome 
                    FROM {$this->table} a
                    JOIN cliente c ON a.idCliente = c.idCliente
                    WHERE a.idProduto = :produto
                    ORDER BY a.dataAvaliacao DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":produto", $idProduto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Erro ao listar avaliações: " . $e->getMessage());
            return [];
        }
    }

    // Média das avaliações
    public function media(int $idProduto): array {
        try {
            $sql = "SELECT 
                        ROUND(AVG(nota), 1) as media, 
                        COUNT(*) as total
                    FROM {$this->table}
                    WHERE idProduto = :produto";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":produto", $idProduto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Erro ao calcular média: " . $e->getMessage());
            return ["media" => 0, "total" => 0];
        }
    }

    // Atualizar avaliação
    public function atualizar(int $idAvaliacao, float $nota, string $comentario): bool {
        try {
            $sql = "UPDATE {$this->table}
                    SET nota = :nota, comentario = :comentario
                    WHERE idAvaliacao = :id";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ":nota" => $nota,
                ":comentario" => htmlspecialchars($comentario),
                ":id" => $idAvaliacao
            ]);

        } catch (Exception $e) {
            error_log("Erro ao atualizar avaliação: " . $e->getMessage());
            return false;
        }
    }

    // Deletar avaliação
    public function deletar(int $idAvaliacao): bool {
        try {
            $sql = "DELETE FROM {$this->table}
                    WHERE idAvaliacao = :id";

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([":id" => $idAvaliacao]);

        } catch (Exception $e) {
            error_log("Erro ao deletar avaliação: " . $e->getMessage());
            return false;
        }
    }
}
?>