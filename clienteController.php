<?php
session_start();

include "conexaoBanco.php";

header("Content-Type: application/json");

$acao = $_POST["acao"] ?? "";

//  CADASTRO 
if($acao == "cadastrar"){

    $nome = $_POST["nome"] ?? "";
    $email = $_POST["email"] ?? "";
    $senha = $_POST["senha"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if(empty($nome) || empty($email) || empty($senha)){
        echo json_encode(["erro" => "Preencha todos os campos"]);
        exit;
    }

    if($senha != $confirmar){
        echo json_encode(["erro" => "Senhas não coincidem"]);
        exit;
    }

    // verificar email
    $stmt = $conn->prepare("SELECT idCliente FROM cliente WHERE email = :email");
    $stmt->execute([":email" => $email]);

    if($stmt->rowCount() > 0){
        echo json_encode(["erro" => "Email já cadastrado"]);
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO cliente (nome, email, senha)
        VALUES (:nome, :email, :senha)
    ");

    $stmt->execute([
        ":nome" => $nome,
        ":email" => $email,
        ":senha" => $senhaHash
    ]);

    echo json_encode([
        "status" => "ok",
        "mensagem" => "Cadastro realizado com sucesso"
    ]);
    exit;
}

//  LOGIN 
if($acao == "login"){

    $email = $_POST["email"] ?? "";
    $senha = $_POST["senha"] ?? "";

    if(empty($email) || empty($senha)){
        echo json_encode(["erro" => "Preencha tudo"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM cliente WHERE email = :email");
    $stmt->execute([":email" => $email]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$usuario){
        echo json_encode(["erro" => "Usuário não encontrado"]);
        exit;
    }

    if(!password_verify($senha, $usuario["senha"])){
        echo json_encode(["erro" => "Senha incorreta"]);
        exit;
    }

    $_SESSION["usuario_id"] = $usuario["idCliente"];
    $_SESSION["usuario_nome"] = $usuario["nome"];

    echo json_encode([
        "status" => "ok",
        "mensagem" => "Login realizado com sucesso"
    ]);
    exit;
}

//  LOGOUT 
if($acao == "logout"){
    session_destroy();

    echo json_encode([
        "status" => "ok",
        "mensagem" => "Logout realizado"
    ]);
    exit;
}
?>

<form action="clienteController.php" method="POST">
    <input type="hidden" name="acao" value="cadastrar">

    <input type="text" name="nome" placeholder="Nome" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="senha" placeholder="Senha" required>
    <input type="password" name="confirmar" placeholder="Confirmar senha" required>
    
    <button type="submit">Cadastrar</button>
</form>

<form action="clienteController.php" method="POST">
    <input type="hidden" name="acao" value="login">

    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="senha" placeholder="Senha" required>

    <button type="submit">Entrar</button>
</form>