<?php
class ResumoPedido {
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function buscar($idPedido){

        // dados do pedido + cliente
        $sqlPedido = "
            SELECT p.*, c.nome, c.email
            FROM pedido p
            JOIN cliente c ON p.idCliente = c.idCliente
            WHERE p.idPedido = :id
        ";

        $stmt = $this->conn->prepare($sqlPedido);
        $stmt->execute([":id" => $idPedido]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$pedido){
            return ["status"=>"erro","msg"=>"Pedido não encontrado"];
        }

        // itens do pedido
        $sqlItens = "
            SELECT i.*, pr.nome as produto
            FROM itemPedido i
            JOIN produto pr ON i.idProduto = pr.idProduto
            WHERE i.idPedido = :id
        ";

        $stmtItens = $this->conn->prepare($sqlItens);
        $stmtItens->execute([":id" => $idPedido]);
        $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

        // pagamento
        $sqlPagamento = "
            SELECT * FROM pagamento WHERE idPedido = :id
        ";

        $stmtPag = $this->conn->prepare($sqlPagamento);
        $stmtPag->execute([":id" => $idPedido]);
        $pagamento = $stmtPag->fetch(PDO::FETCH_ASSOC);

        return [
            "status" => "sucesso",
            "pedido" => $pedido,
            "itens" => $itens,
            "pagamento" => $pagamento
        ];
    }
}
?>