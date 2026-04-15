<?php
session_start();

class Pedido {
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function criar($idCliente, $carrinho, $dataEntrega, $horaEntrega, $obs, $tipoEntrega, $endereco, $taxaEntrega, $metodoPagamento){

        if(empty($carrinho)){
            return ["erro" => "Carrinho vazio"];
        }

        if(empty($dataEntrega) || empty($horaEntrega)){
            return ["erro" => "Escolha data e horário"];
        }

        $horaMin = "13:00";
        $horaMax = "22:00";

        if($horaEntrega < $horaMin || $horaEntrega > $horaMax){
            return ["erro" => "Horário inválido"];
        }

        $this->conn->beginTransaction();

        try {

            $total = 0;
            foreach($carrinho as $item){
                $total += $item['preco'] * $item['quantidade'];
            }

            $sql = "INSERT INTO pedido 
            (idCliente, dataPedido, dataEntrega, horaEntrega, status, valorTotal, observacao, tipoEntrega, enderecoEntrega, taxaEntrega, metodoPagamento)
            VALUES (:cliente, NOW(), :dataEntrega, :horaEntrega, 'PENDENTE', :total, :obs, :tipo, :endereco, :taxa, :pagamento)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":cliente" => $idCliente,
                ":dataEntrega" => $dataEntrega,
                ":horaEntrega" => $horaEntrega,
                ":total" => $total,
                ":obs" => $obs,
                ":tipo" => $tipoEntrega,
                ":endereco" => $endereco,
                ":taxa" => $taxaEntrega,
                ":pagamento" => $metodoPagamento
            ]);

            $idPedido = $this->conn->lastInsertId();

            foreach($carrinho as $item){
                $sqlItem = "INSERT INTO itemPedido 
                (idPedido, idProduto, quantidade, subtotal)
                VALUES (:pedido, :produto, :qtd, :subtotal)";

                $this->conn->prepare($sqlItem)->execute([
                    ":pedido" => $idPedido,
                    ":produto" => $item['idProduto'],
                    ":qtd" => $item['quantidade'],
                    ":subtotal" => $item['preco'] * $item['quantidade']
                ]);
            }

            $this->conn->commit();

            unset($_SESSION["carrinho"]);

            return [
                "status" => "ok",
                "pedido" => $idPedido,
                "total" => $total
            ];

        } catch(Exception $e){
            $this->conn->rollBack();
            return ["erro" => $e->getMessage()];
        }
    }
}
?>