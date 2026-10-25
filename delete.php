
<?php

session_start();

// 1. Receber o ID enviado pelo formulário POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// 2. Receber o token de segurança
$token = $_POST['csrf_token'] ?? '';

// 3. Verificar se a requisição é POST e se o token é válido
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SESSION['csrf_token']) ||
    !is_string($token) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit('Operação não autorizada.');
}

// 4. Verificar se o ID é válido
if (!$id || $id < 1) {
    $_SESSION['msg'] = "<p style='color: red;'>ID inválido!</p>";

    header("Location: index.php");
    exit;
}

// 5. Incluir os arquivos necessários
require "./Conn.php";
require "./User.php";

// 6. Instanciar a classe User
$deleteUser = new User();

// 7. Definir o ID do usuário que será excluído
$deleteUser->id = $id;

// 8. Executar a exclusão
$value = $deleteUser->delete();

// 9. Verificar o resultado
if ($value) {
    $_SESSION['msg'] = "<p style='color: green;'>Usuário apagado com sucesso!</p>";
} else {
    $_SESSION['msg'] = "<p style='color: red;'>Usuário não encontrado!</p>";
}

// 10. Redirecionar para a listagem
header("Location: index.php");
exit;
