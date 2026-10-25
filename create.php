
<?php
    session_start();

    ob_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta charset="UTF-8">
    <title>Cadastrar | CRUD PHP</title>
</head>
<body>
    <nav>
        <a href="index.php">Listar</a><br>
        <a href="create.php">Cadastrar</a><br>
    </nav>

    <h1>Cadastrar Usuário</h1>

    <?php

    require "./Conn.php";
    require "./User.php";

    $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (!empty($formData['SendAddUser'])) {

        // 1. Remover espaços extras dos dados recebidos
        $name = trim($formData['name'] ?? '');
        $email = trim($formData['email'] ?? '');

        // 2. Validar o nome
        if (strlen($name) < 3) {
            echo "<p style='color: red;'>O nome deve ter pelo menos 3 caracteres.</p>";

        // 3. Validar o email
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p style='color: red;'>Informe um email válido.</p>";

        // 4. Se os dados forem válidos, cadastrar
        } else {

            $createUser = new User();

            $createUser->formData = [
                'name' => $name,
                'email' => $email
            ];

            $value = $createUser->create();

            if ($value) {
                $_SESSION['msg'] = "<p style='color: #7FFF00;'>Usuário cadastrado com sucesso!</p>";
                header("Location: index.php");
                exit;
            } else {
                echo "<p style='color: red;'>Erro ao cadastrar o usuário.</p>";
            }
        }
    }

    ?>

    <form class="form" name="CreateUser" method="POST" action="">
        <label>Nome: </label>
        <input
            type="text"
            name="name"
            placeholder="Nome Completo"
            required
        />

        <label>Email: </label>
        <input
            type="email"
            name="email"
            placeholder="E-mail"
            required
        />

        <input
            class="input"
            type="submit"
            value="Cadastrar"
            name="SendAddUser"
        />
    </form>
</body>
</html>
