<?php
 session_start();

 ob_start();

 $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Edit Herança Mysql</title>
</head>
<nav>
    <a href="index.php">Listar</a><br>
    <a href="create.php">Cadastrar</a><br>
</nav>
<body>

   

    <h1>Editar o Usuário</h1>

    
    <?php
    
    if(isset($_SESSION["msg"])){
        echo $_SESSION["msg"];
        unset($_SESSION["msg"]);
    }

    require "./Conn.php";
    require "./User.php";


    //Receber os dados do formulário
    $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    //Verificar se o usuário clicou no botão
    if(!empty($formData['SendEditUser'])){
        $editUser = new User();
        $editUser->formData = $formData; //instancia o atributo formData de User.php no $formData
        $value = $editUser->edit();
        
       if ($value) {
        $_SESSION['msg'] =  "<p style='color: #7FFF00;'>Usuário editado com sucesso!</p>";
        header("Location: index.php");
       }else{
        echo "<p style='color: #f00;'>Erro: Usuário não editado!</p>";
       }
    }

    if(!empty($id)){
      
        $viewUser = new User();
        $viewUser->id = $id;
        $valueUser = $viewUser->view();

        extract($valueUser);

        ?>

            <form class="form" name="EditUser" method="POST" action="">
                <input type="hidden" name="id" 
                value="<?php echo $id;?>" />

                <label>Nome: </label>
                <input type="text" name="name" 
                placeholder="Nome Completo" 
                value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                required /> 

                <label>Email: </label>
                <input type="email" name="email" 
                placeholder="E-mail" 
                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                required /> 

                <input class="input" type="submit" value="Editar" name="SendEditUser">
            </form>

        <?php

        


    }else{
        $_SESSION['msg'] =  "<p style='color: #f00;'>Usuário não encontrado!</p>";
        header("Location: index.php");
    }

    ?>

</body>
</html>