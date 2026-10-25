
<?php

session_start();

// Receber e validar o ID enviado pela URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Caso o ID seja inválido, voltar para a listagem
if (!$id || $id < 1) {
    $_SESSION['msg'] = "<p class='message error'>ID inválido!</p>";

    header("Location: index.php");
    exit;
}

// Incluir os arquivos
require "./Conn.php";
require "./User.php";

// Buscar o usuário no banco
$viewUser = new User();
$viewUser->id = $id;
$valueUser = $viewUser->view();

// Verificar se encontrou o usuário
if (!$valueUser) {
    $_SESSION['msg'] = "<p class='message error'>Usuário não encontrado!</p>";

    header("Location: index.php");
    exit;
}

// Receber os dados
$name = htmlspecialchars($valueUser['name'], ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($valueUser['email'], ENT_QUOTES, 'UTF-8');

// Formatar a data de cadastro
$createdAt = date('d/m/Y H:i:s', strtotime($valueUser['createdAt']));

// Verificar se existe uma data de atualização
if (!empty($valueUser['updatedAt'])) {
    $updatedAt = date('d/m/Y H:i:s', strtotime($valueUser['updatedAt']));
} else {
    $updatedAt = 'Sem atualização';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Visualizar Usuário | CRUD PHP</title>
</head>
<body>

    <!-- Menu -->
    <nav>
        <a href="index.php">Listar</a>
        <a href="create.php">Cadastrar</a>
    </nav>

    <h1>Detalhes do Usuário</h1>

    <!-- Card com os dados do usuário -->
    <div class="user-info-view">

        <p><strong>ID:</strong> <?= $id ?></p>

        <p><strong>Nome:</strong> <?= $name ?></p>

        <p><strong>E-mail:</strong> <?= $email ?></p>

        <p><strong>Usuário criado em:</strong> <?= $createdAt ?></p>

        <p><strong>Última atualização:</strong> <?= $updatedAt ?></p>

        <a class="a-user-info" href="edit.php?id=<?= $id ?>">
            Editar usuário
        </a>

        <a class="a-user-info" href="index.php">
            Voltar para listagem
        </a>

    </div>

</body>
</html>
