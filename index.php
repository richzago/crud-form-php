
<?php

session_start();

// Gerar o token de segurança para exclusão
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Incluir os arquivos necessários
require "./Conn.php";
require "./User.php";

// ==========================================
// PESQUISA E PAGINAÇÃO
// ==========================================

// Receber o texto pesquisado
$search = $_GET['search'] ?? '';
$search = is_string($search) ? trim($search) : '';

// Receber a página atual
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$page = max(1, $page);

// Quantidade de usuários por página
$perPage = 5;

// Instanciar a classe User
$listUsers = new User();

// Contar quantos usuários existem
$totalUsers = $listUsers->countUsers($search);

// Calcular o número total de páginas
$totalPages = max(1, (int) ceil($totalUsers / $perPage));

// Impedir acessar uma página inexistente
$page = min($page, $totalPages);

// Calcular quantos registros devem ser pulados
$offset = ($page - 1) * $perPage;

// Buscar os usuários desta página
$result_users = $listUsers->list($search, $perPage, $offset);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Listar Usuários | CRUD PHP</title>
</head>
<body>

    <!-- Menu -->
    <nav>
        <a class="a-header" href="index.php">Listar</a>
        <a class="a-header" href="create.php">Cadastrar</a>
    </nav>

    <h1>Listar Usuários</h1>

    <?php

    // Exibir mensagens de sucesso ou erro
    if (isset($_SESSION['msg'])) {
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    }

    ?>

    <!-- Formulário de pesquisa -->
    <form method="GET" action="index.php" class="search-form">

        <input
            type="search"
            name="search"
            placeholder="Pesquisar nome ou email..."
            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
        >

        <button type="submit">Pesquisar</button>

        <a href="index.php">Limpar</a>
    </form>

    <?php

    // Mensagem caso não existam resultados
    if (empty($result_users)) {
        echo "<p class='message error'>Nenhum usuário encontrado.</p>";
    }

    ?>

    <!-- Container que organiza os cards -->
    <div class="users-grid">

        <?php foreach ($result_users as $row_user): ?>

            <?php
                // Receber os dados do usuário
                $id = (int) $row_user['id'];
                $name = htmlspecialchars($row_user['name'], ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars($row_user['email'], ENT_QUOTES, 'UTF-8');
            ?>

            <!-- Card individual -->
            <div class="user-info">

                <p><strong>ID:</strong> <?= $id ?></p>

                <p><strong>Nome:</strong> <?= $name ?></p>

                <p><strong>E-mail:</strong> <?= $email ?></p>

                <!-- Botão visualizar -->
                <a class="a-user-info" href="view.php?id=<?= $id ?>">
                    Visualizar
                </a>

                <!-- Botão editar -->
                <a class="a-user-info" href="edit.php?id=<?= $id ?>">
                    Editar
                </a>

                <!-- Exclusão segura -->
                <form
                    method="POST"
                    action="delete.php"
                    onsubmit="return confirm('Deseja realmente excluir este usuário?');"
                >
                    <input
                        type="hidden"
                        name="id"
                        value="<?= $id ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <button type="submit">Apagar</button>
                </form>

            </div> <!-- Fecha o card -->

        <?php endforeach; ?>

    </div> <!-- Fecha o grid -->

    <!-- Paginação -->
    <?php if ($totalPages > 1): ?>

        <?php $encodedSearch = urlencode($search); ?>

        <div class="pagination">

            <?php if ($page > 1): ?>
                <a href="index.php?search=<?= $encodedSearch ?>&page=<?= $page - 1 ?>">
                    Anterior
                </a>
            <?php endif; ?>

            <span>Página <?= $page ?> de <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="index.php?search=<?= $encodedSearch ?>&page=<?= $page + 1 ?>">
                    Próximo
                </a>
            <?php endif; ?>

        </div>

    <?php endif; ?>

</body>
</html>
