<?php
include 'conexaoBanco.php';

header("Content-Type: application/json");

$acao = $_GET['acao'] ?? '';

switch ($acao) {

    case 'listar':
        $sql = "SELECT * FROM produtos";
        $result = $conn->query($sql);

        $produtos = [];

        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        echo json_encode($produtos);
        break;
    
    case 'excluir':
        $id = $_GET['id'] ?? 0;

        $stmt = $conn->prepare("DELETE FROM produtos WHERE id = $idProduto = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "excluído"]);
        } else {
            echo json_encode(["status" => "erro"]);
        }
        break;

    case 'atualizar':
        $sid = $_POST['id'] ?? 0;
        $nome = $_POST['nome'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $preco = $_POST['preco'] ?? 0;
        
        $stmt = $conn->prepare("
            UPDATE produtos
            SET nome = ?, descricao = ?, preco = ?
            WHERE idProduto = ?");

            $stmt->bind_param("ssdi", $nome, $descricao, $preco, $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "atualizado"]);
        } else {
            echo json_encode(["status" => "erro"]);
        }
        break;

    default:
        echo json_encode(["status" => "ação inválida"]);
}
?>
