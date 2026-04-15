<?php
class Pagamento {
    private $conn;
    private $table = "pagamento";

    public $idPedido;
    public $valorPago;
    public $metodoPagamento;
    public $status;
    public $codigoTransacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function criar() {

        try {

            // Inicia transação (garante que tudo aconteça junto)
            $this->conn->beginTransaction();

            // Inserir pagamento
            $sql = "INSERT INTO {$this->table}
                    (idPedido, valorPago, metodoPagamento, dataPagamento, status, codigoTransacao)
                    VALUES
                    (:idPedido, :valorPago, :metodoPagamento, NOW(), :status, :codigoTransacao)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(":idPedido", $this->idPedido);
            $stmt->bindParam(":valorPago", $this->valorPago);
            $stmt->bindParam(":metodoPagamento", $this->metodoPagamento);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":codigoTransacao", $this->codigoTransacao);

            $stmt->execute();

            // Atualizar status do pedido automaticamente
            $stmtUpdate = $this->conn->prepare("
                UPDATE pedido 
                SET status = 'PAGO' 
                WHERE idPedido = :id
            ");

            $stmtUpdate->execute([
                ":id" => $this->idPedido
            ]);

            // Confirma tudo
            $this->conn->commit();

            return [
                "status" => "ok",
                "mensagem" => "Pagamento realizado e pedido atualizado"
            ];

        } catch(Exception $e){

            // Se der erro, desfaz tudo
            $this->conn->rollBack();

            return [
                "erro" => "Erro ao processar pagamento"
            ];
        }
    }
}
?>