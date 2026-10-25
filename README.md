# CRUD de Usuários com PHP e MySQL

Aplicação web didática desenvolvida em **PHP puro**, com **MySQL**, **PDO**, **HTML** e **CSS**, para praticar as operações de um CRUD (*Create, Read, Update, Delete*). O projeto permite cadastrar, listar, pesquisar, visualizar, editar e excluir usuários por meio de uma interface simples, em modo escuro e responsiva.

> **Projeto de estudos e portfólio.** Esta aplicação não possui autenticação nem controle de acesso e não deve ser publicada na internet com dados reais sem melhorias adicionais de segurança.

## Tecnologias utilizadas

- **PHP 8.2** — lógica do servidor e processamento de formulários (ambiente usado no desenvolvimento; outras versões de PHP 8 podem funcionar).
- **MySQL 8** — armazenamento dos usuários.
- **PDO** — conexão com o MySQL e execução de consultas preparadas.
- **HTML5 e CSS3** — interface e layout responsivo, sem framework de front-end.
- **Composer** — gerenciamento da dependência [`vlucas/phpdotenv`](https://github.com/vlucas/phpdotenv), usada para carregar as variáveis do arquivo `.env`.

## Funcionalidades

- Cadastro de usuários com nome e email.
- Listagem dos usuários em **cards**, com até três colunas em telas grandes.
- Visualização de dados de um usuário, incluindo as datas de criação e atualização.
- Edição do nome e do email.
- Exclusão com confirmação, requisição **POST** e verificação de token **CSRF**.
- Pesquisa por nome ou email utilizando `LIKE` e parâmetros preparados.
- Paginação da listagem, com **cinco usuários por página** e links de navegação.
- Mensagens de sucesso, erro e resultados não encontrados.
- Tratamento de saída HTML com `htmlspecialchars()` nos locais revisados.
- Layout responsivo: três colunas no desktop, duas em tablets e uma em telas pequenas.

## Como o projeto funciona

O navegador envia formulários e requisições às páginas PHP. A classe `User` centraliza as operações SQL e utiliza a conexão criada pela classe abstrata `Conn`. Os registros são persistidos na tabela `users` do MySQL.

```text
Navegador (HTML + CSS)
        |
        v
index.php / create.php / view.php / edit.php / delete.php
        |
        v
User.php  (consultas e operações CRUD)
        |
        v
Conn.php  (PDO e configuração do .env)
        |
        v
MySQL  ->  tabela users
```

### Estrutura dos principais arquivos

| Arquivo | Responsabilidade |
| --- | --- |
| `index.php` | Listagem, pesquisa, paginação e ações dos cards. |
| `create.php` | Formulário e processamento do cadastro. |
| `view.php` | Detalhes de um usuário. |
| `edit.php` | Formulário e processamento da edição. |
| `delete.php` | Exclusão de registros por POST, mediante verificação CSRF. |
| `User.php` | Métodos de listagem, contagem, cadastro, consulta, edição e exclusão. |
| `Conn.php` | Carregamento do `.env` e conexão PDO. |
| `style.css` | Estilos, cards e responsividade. |
| `table.sql` | Script SQL para criar a tabela `users`. |
| `.example.env` | Exemplo das variáveis de conexão. |

## Pré-requisitos

Para executar localmente, é necessário ter:

1. PHP 8 com as extensões **PDO MySQL** (`pdo_mysql`) e **mbstring** se utilizar funções multibyte em futuras validações.
2. MySQL instalado e em execução.
3. Composer instalado.
4. Git (opcional, mas recomendado para clonar o projeto).

> **XAMPP não é obrigatório.** É possível executar o projeto com o servidor embutido do PHP. Os exemplos abaixo utilizam **Windows / PowerShell**.

## Instalação e execução local

### 1. Clonar o projeto

```powershell
git clone https://github.com/richzago/crud-form-php.git
cd crud-form-php
```

### 2. Instalar a dependência PHP

Se o repositório **já contiver** `composer.json` e `composer.lock`, execute:

```powershell
composer install
```

Se esses arquivos ainda **não estiverem presentes** na versão clonada, instale a dependência usada em `Conn.php`:

```powershell
composer require vlucas/phpdotenv
```

Esse comando cria os arquivos do Composer e a pasta `vendor/`.

> Recomenda-se versionar `composer.json` e `composer.lock` no Git. A pasta `vendor/` deve continuar ignorada, pois é recriada com `composer install`.

### 3. Configurar as variáveis de ambiente

No PowerShell:

```powershell
Copy-Item .example.env .env
```

Edite o `.env` para informar sua conexão local (substitua usuário e senha pelos valores do seu ambiente):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_form_php
DB_USERNAME=root
DB_PASSWORD=
```

Não envie o arquivo `.env` ao GitHub e nunca publique senhas reais.

### 4. Criar o banco de dados

No MySQL Workbench, phpMyAdmin ou outro cliente MySQL, execute:

```sql
CREATE DATABASE IF NOT EXISTS crud_form_php
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Selecione o banco `crud_form_php` e execute o conteúdo do arquivo `table.sql` para criar a tabela `users`.

**Atenção:** o `table.sql` original começa com `DROP TABLE IF EXISTS users;`, portanto **apaga a tabela `users` existente nesse banco**, inclusive seus dados, antes de recriá-la. Use-o somente em um banco de estudos ou vazio, e confira qual banco está selecionado.

### 5. Iniciar o servidor PHP

Na pasta do projeto:

```powershell
php -S 127.0.0.1:8001
```

Acesse no navegador: **http://127.0.0.1:8001/index.php**.

> O servidor embutido do PHP serve para desenvolvimento local e não deve ser usado como servidor público de produção.

## Banco de dados

O projeto utiliza uma tabela principal, `users`:

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `id` | `INT`, auto incremento | Identificador do usuário e chave primária. |
| `name` | `VARCHAR(220)` | Nome do usuário. |
| `email` | `VARCHAR(220)` | Email do usuário. |
| `createdAt` | `DATETIME` | Data e hora do cadastro. |
| `updatedAt` | `DATETIME NULL` | Data e hora da última atualização. |

O cadastro preenche `createdAt` com `NOW()`; a edição atualiza `updatedAt`. O script atual **não cria uma restrição `UNIQUE` para o email**.

## Pesquisa e paginação

A listagem permite pesquisar por parte do nome ou email. Por exemplo:

```text
http://127.0.0.1:8001/index.php?search=ana
```

Para navegar entre páginas, utiliza-se o parâmetro `page`:

```text
http://127.0.0.1:8001/index.php?search=ana&page=2
```

A consulta utiliza `LIKE` com valores enviados por parâmetros preparados do PDO. A paginação usa `LIMIT`, `OFFSET` e `COUNT(*)`, exibindo **cinco resultados por página**. A busca é preservada ao navegar entre as páginas.

## Segurança e validação

Este projeto introduz conceitos fundamentais de segurança em PHP:

- **Prepared statements:** os valores fornecidos pelo usuário são vinculados aos parâmetros nas consultas PDO.
- **Saída HTML escapada:** `htmlspecialchars()` é usado para evitar que nomes e emails sejam interpretados como código HTML nas páginas revisadas.
- **Validação no cadastro:** nome com tamanho mínimo e verificação de formato de email com `filter_var()`.
- **Exclusão por POST:** o usuário confirma a ação antes do envio e o PHP verifica um token CSRF da sessão.
- **Credenciais no `.env`:** configurações do banco separadas do código-fonte.
- **Erros de conexão:** detalhes técnicos podem ser enviados ao log em vez de exibidos diretamente ao visitante.

**Limitações conhecidas:** o CRUD não possui autenticação ou autorização; a proteção CSRF foi aplicada à exclusão, não necessariamente aos demais formulários; a validação da edição deve ser conferida separadamente; e não há garantia de unicidade de email no banco. Por isso, trata-se de uma aplicação para estudos, não de um sistema pronto para produção.

## Roteiro de testes manuais

Com o MySQL e o servidor PHP em execução:

1. **Cadastro:** acesse `create.php`, informe nome e email válidos e confirme a mensagem de sucesso.
2. **Validação:** tente cadastrar um nome muito curto ou email malformado e confirme que o registro não é criado.
3. **Listagem:** abra `index.php` e verifique os cards.
4. **Visualização:** clique em **Visualizar** e confira os dados e datas do usuário.
5. **Edição:** altere o nome ou email e confirme que a mudança aparece na listagem e nos detalhes.
6. **Exclusão cancelada:** clique em **Apagar** e depois em **Cancelar**; o registro deve permanecer.
7. **Exclusão confirmada:** clique em **Apagar** e confirme; o registro deve desaparecer.
8. **Requisição GET indevida:** tente acessar `/delete.php?id=1` pela barra do navegador; a operação não deve excluir nada e deve ser rejeitada.
9. **Pesquisa:** pesquise por parte de um nome, depois por parte de um email e, por fim, por algo inexistente.
10. **Paginação:** crie ao menos 12 registros de teste e confira a navegação entre três páginas.
11. **Pesquisa com paginação:** confirme que a busca permanece ativa ao navegar entre páginas.
12. **Responsividade:** reduza a largura do navegador; confira o rearranjo dos cards em duas e uma coluna.

Para verificar a sintaxe do PHP no terminal:

```powershell
php -l index.php
php -l create.php
php -l view.php
php -l edit.php
php -l delete.php
php -l User.php
php -l Conn.php
```

## Possíveis melhorias futuras

- Aplicar as mesmas regras de validação também à edição.
- Impedir emails duplicados com validação PHP e índice `UNIQUE` no MySQL.
- Adicionar autenticação e autorização, caso a aplicação precise ser acessível publicamente.
- Proteger todos os formulários que alteram dados com tokens CSRF.
- Criar testes automatizados para as operações do CRUD.

## Autor

**Richard Zago** — Desenvolvedor Full Stack com foco em Back-end.

- GitHub: [github.com/richzago](https://github.com/richzago)
- Repositório: [github.com/richzago/crud-form-php](https://github.com/richzago/crud-form-php)

---

Desenvolvido como projeto de prática de **PHP, orientação a objetos, PDO, SQL e desenvolvimento web**.
