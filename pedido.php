<?php
class Pedido {
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function criar($idCliente, $carrinho, $dataEntrega, $horaEntrega, $obs, $tipoEntrega, $endereco, $taxaEntrega){

        try {
            $this->conn->beginTransaction();

            // calcular total dos produtos
            $total = 0;

            foreach($carrinho as $item){
                $total += $item["preco"] * $item["quantidade"];
            }

            // soma frete
            $total += $taxaEntrega;

            // inserir pedido
            $sql = "INSERT INTO pedido 
                    (idCliente, dataEntrega, horaEntrega, observacao, tipoEntrega, endereco, taxaEntrega, valorTotal, status)
                    VALUES 
                    (:cliente, :data, :hora, :obs, :tipo, :endereco, :taxa, :total, 'PENDENTE')";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ":cliente" => $idCliente,
                ":data" => $dataEntrega,
                ":hora" => $horaEntrega,
                ":obs" => $obs,
                ":tipo" => $tipoEntrega,
                ":endereco" => $endereco,
                ":taxa" => $taxaEntrega,
                ":total" => $total
            ]);

            $idPedido = $this->conn->lastInsertId();

            // inserir itens do pedido
            foreach($carrinho as $item){
                $sqlItem = "INSERT INTO itemPedido 
                            (idPedido, idProduto, quantidade, preco)
                            VALUES (:pedido, :produto, :qtd, :preco)";

                $stmtItem = $this->conn->prepare($sqlItem);

                $stmtItem->execute([
                    ":pedido" => $idPedido,
                    ":produto" => $item["idProduto"],
                    ":qtd" => $item["quantidade"],
                    ":preco" => $item["preco"]
                ]);
            }

            $this->conn->commit();

            return [
                "status" => "sucesso",
                "idPedido" => $idPedido,
                "total" => $total
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();

            return [
                "status" => "erro",
                "mensagem" => $e->getMessage()
            ];
        }
    }
}
?>