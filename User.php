<?php

class User extends Conn
{
    public object $conn;
    public array $formData;
    public int $id;

 
public function list(
    string $search = '',
    int $limit = 5,
    int $offset = 0
): array {

    // Conectar ao banco de dados
    $this->conn = $this->connectDb();

    // Consulta inicial
    $sql = "SELECT id, name, email FROM users";

    // Adicionar filtro somente quando houver pesquisa
    if ($search !== '') {
        $sql .= " WHERE name LIKE :name OR email LIKE :email";
    }

    // Ordenar e limitar os resultados
    $sql .= " ORDER BY id ASC LIMIT :limit OFFSET :offset";

    // Preparar a consulta
    $stmt = $this->conn->prepare($sql);

    // Enviar o termo pesquisado com segurança
    if ($search !== '') {
        $term = "%{$search}%";

        $stmt->bindValue(':name', $term, PDO::PARAM_STR);
        $stmt->bindValue(':email', $term, PDO::PARAM_STR);
    }

    // Informar quantos registros buscar e de onde começar
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    return $stmt->fetchAll();
}


public function countUsers(string $search = ''): int
{
    // Conectar ao banco
    $this->conn = $this->connectDb();

    // Contar todos os usuários
    $sql = "SELECT COUNT(*) FROM users";

    // Se houver pesquisa, contar somente os resultados filtrados
    if ($search !== '') {
        $sql .= " WHERE name LIKE :name OR email LIKE :email";
    }

    $stmt = $this->conn->prepare($sql);

    // Aplicar o mesmo filtro usado na listagem
    if ($search !== '') {
        $term = "%{$search}%";

        $stmt->bindValue(':name', $term, PDO::PARAM_STR);
        $stmt->bindValue(':email', $term, PDO::PARAM_STR);
    }

    $stmt->execute();

    // fetchColumn() recupera o resultado do COUNT(*)
    return (int) $stmt->fetchColumn();
}



    public function create(): bool
    {
        $this->conn = $this->connectDb();
        $query_user = "INSERT INTO users 
        (name, email, createdAt) 
        VALUES (:name, :email, NOW())
        ";
        $add_user = $this->conn->prepare($query_user);
        $add_user->bindParam(':name', $this->formData["name"]);
        $add_user->bindParam(':email', $this->formData["email"]);
        $add_user->execute();

        if($add_user->rowCount()) {
            return true;
        }else{
            return false;
        }
    }

    public function view()
    {
        $this->conn = $this->connectDb();
        $query_user = "SELECT id, name, email, createdAt, updatedAt
                FROM users
                    WHERE id =:id
                        LIMIT 1";
        $result_user = $this->conn->prepare($query_user);
        $result_user->bindParam(":id", $this->id);
        $result_user->execute();
        $value = $result_user->fetch();

        return $value;

    }

    public function edit(): bool
    {
        $this->conn = $this->connectDb();
        $query_user = "UPDATE users SET name=:name, 
        email=:email, updatedAt=NOW()
            WHERE id=:id";
        $edit_user = $this->conn->prepare($query_user);
        $edit_user->bindParam(":name", $this->formData["name"]);
        $edit_user->bindParam(":email", $this->formData["email"]);
        $edit_user->bindParam(":id", $this->formData["id"]);

        $edit_user->execute();

        if ($edit_user->rowCount()) {
            return true;
        }else{
            return false;
        }
    }

    public function delete(): bool
    {
        $this->conn = $this->connectDb();
        $query_user = "DELETE FROM users WHERE id=:id LIMIT 1";
        $delete_user = $this->conn->prepare($query_user);
        $delete_user->bindParam(":id", $this->id);
        $value = $delete_user->execute();

        return $value;

    }
}