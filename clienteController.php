<?php
session_start();
require_once "conexaoBanco.php";
require_once "cliente.php";

$conn = (new Database())->conectar();
$cliente = new Cliente($conn);

$acao = $_POST["acao"] ?? "";

// CADASTRO
if($acao === "cadastrar"){

    $cliente->nome = $_POST["nome"] ?? "";
    $cliente->telefone = $_POST["telefone"] ?? "";
    $cliente->email = $_POST["email"] ?? "";
    $cliente->senha = $_POST["senha"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if(empty($cliente->nome) || empty($cliente->email) || empty($cliente->senha)){
        echo "Preencha todos os campos";
        exit;
    }

    if($cliente->senha !== $confirmar){
        echo "Senhas não coincidem";
        exit;
    }

    // verificar email existente
    $existe = $cliente->buscarPorEmail($cliente->email);
    if($existe){
        echo "Email já cadastrado";
        exit;
    }

    if($cliente->criar()){
        echo "Cadastro realizado com sucesso";
    } else {
        echo "Erro ao cadastrar";
    }
}

// LOGIN
if($acao === "login"){

    $email = $_POST["email"] ?? "";
    $senha = $_POST["senha"] ?? "";

    if(empty($email) || empty($senha)){
        echo "Preencha tudo";
        exit;
    }

    $usuario = $cliente->buscarPorEmail($email);

    if(!$usuario){
        echo "Usuário não encontrado";
        exit;
    }

    if(!password_verify($senha, $usuario["senha"])){
        echo "Senha incorreta";
        exit;
    }

    // cria sessão
    $_SESSION["usuario_id"] = $usuario["idCliente"];
    $_SESSION["usuario_nome"] = $usuario["nome"];

    echo "Login realizado com sucesso";
}

// LOGOUT
if($acao === "logout"){
    session_destroy();
    echo "Logout realizado";
}
?>