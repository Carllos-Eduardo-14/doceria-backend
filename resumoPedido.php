<?php
class ResumoPedido {
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function buscar($idPedido){

        // PEDIDO + CLIENTE
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
            return ["erro" => "Pedido não encontrado"];
        }

        // formatar total
        $pedido["valorTotal"] = number_format($pedido["valorTotal"], 2, ',', '.');

        // ITENS
        $sqlItens = "
            SELECT i.*, pr.nome as nomeProduto
            FROM itemPedido i
            JOIN produto pr ON i.idProduto = pr.idProduto
            WHERE i.idPedido = :id
        ";

        $stmtItens = $this->conn->prepare($sqlItens);
        $stmtItens->execute([":id" => $idPedido]);
        $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

        // formatar subtotais
        foreach($itens as &$item){
            $item["subtotal"] = number_format($item["subtotal"], 2, ',', '.');
        }

        // PAGAMENTO (opcional)
        $sqlPagamento = "
            SELECT * FROM pagamento WHERE idPedido = :id
        ";

        $stmtPag = $this->conn->prepare($sqlPagamento);
        $stmtPag->execute([":id" => $idPedido]);
        $pagamento = $stmtPag->fetch(PDO::FETCH_ASSOC);

        if(!$pagamento){
            $pagamento = null;
        }

        return [
            "status" => "ok",
            "pedido" => $pedido,
            "itens" => $itens,
            "pagamento" => $pagamento
        ];
    }
}
?>