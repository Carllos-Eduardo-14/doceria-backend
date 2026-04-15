<?php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: " . $redirect);
    exit();
}

$host = "localhost";
$db = "doceria";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);    
} catch (\PDOException $e) {
    die("Erro de conexão");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $erros = [];

    if(empty($nome)) {
        $erros[] = "Nome é obrigatório.";
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido.";
    }

    if(!preg_match('/^[0-9]{10,11}$/', $telefone)) {
        $erros[] = "Telefone inválido.";
    }

    if(strlen($senha) < 4){
        $erros[] = "Senha muito curta.";
    }

    if(!empty($erros)) {
        foreach ($erros as $erro) {
            echo "<p style='color:red;'>$erro</p>";
        }
    } else {

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Cliente (nome, email, telefone, senha) 
                VALUES (:nome, :email, :telefone, :senha)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':senha' => $senhaHash
        ]);

        echo "<p style='color:green;'>Cliente cadastrado com sucesso!</p>";
    }
}
?>

<form method="POST">
    <input type="text" name="nome" placeholder="Nome"><br><br>
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="text" name="telefone" placeholder="Telefone"><br><br>
    <input type="password" name="senha" placeholder="Senha"><br><br>
    <button type="submit">Cadastrar</button>
</form>