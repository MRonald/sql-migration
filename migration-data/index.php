<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Crud Project</title>
    <script src="https://kit.fontawesome.com/407f74df21.js" crossorigin="anonymous"></script>
    <script src="../scripts/menu.js" defer></script>
    <script src="../scripts/masks.js" defer></script>
    <link rel="stylesheet" href="../styles/global.css"/>
    <link rel="stylesheet" href="../styles/migration.css"/>
</head>
<body>
    <header class="header-main">
        <nav class="menu">
            <i class="fas fa-bars" onclick="showMenu()"></i>
            <div class="menu-side" id="menu-side">
                <i class="fas fa-times" onclick="hideMenu()"></i>
                <ul>
                    <li>
                        <a href="../index.html">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="../clientes.php">
                            Cliente
                        </a>
                    </li>
                    <li>
                        <a href="../produtos.php">
                            Produto
                        </a>
                    </li>
                    <li>
                        <a href="../pedidos.php">
                            Pedido
                        </a>
                    </li>
                    <li>
                        <a href="../migration-data">
                            Migrar dados
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <h1>Crud Project</h1>
    </header>
    <main class="content-main" id="content-main">
        <div class="informations">
        <?php
            if ($_POST) {
                ini_set('display_errors',1);
                ini_set('display_startup_erros',1);
                error_reporting(E_ALL);

                $hostname = $_POST['hostname'] ?? 'localhost';
                $oldDatabaseName = $_POST['oldDatabase'] ?? 'projeto_php';
                $newDatabaseName = $_POST['newDatabase'] ?? 'projeto_php_estruturado';
                $user = $_POST['user'] ?? 'root';
                $password = $_POST['password'] ?? null;

                echo "<div class='message-migration'>";
                try {
                    // Conexão com o banco antigo
                    $oldDatabaseConn = new PDO("mysql:host=$hostname;dbname=$oldDatabaseName", $user, $password);
                    $oldDatabaseConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Conexão com o novo banco
                    $newDatabaseConn = new PDO("mysql:host=$hostname;dbname=$newDatabaseName", $user, $password);
                    $newDatabaseConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Pegando os registros do banco antigo
                    $oldRegisters = $oldDatabaseConn->query("select * from pedido");

                    foreach ($oldRegisters as $register) {
                        // Inserindo dado do cliente
                        $clientInsert = $newDatabaseConn->prepare("INSERT INTO cliente (nome_cliente, cpf, email) VALUES (:nome, :cpf, :email)");
                        $clientInsert->execute(array(
                            ":nome" => $register["nome_cliente"],
                            ":cpf" => $register["cpf"],
                            ":email" => $register["email"]
                        ));

                        // Inserindo dado do produto
                        $productInsert = $newDatabaseConn->prepare(
                            "INSERT INTO produto (cod_barras, nome_produto, valor_unitario) VALUES (:codBarras, :nomeProduto, :valorUnitario)"
                        );
                        $productInsert->execute(array(
                            ":codBarras" => $register["cod_barras"],
                            ":nomeProduto" => $register["nome_produto"],
                            ":valorUnitario" => $register["valor_unitario"]
                        ));
                    }

                } catch (PDOException $e) {
                    echo "";
                } finally {
                    $oldDatabaseConn = null;
                    $newDatabaseConn = null;
                }

                // Inserindo dados do pedido
                try {
                    // Conexão com o banco antigo
                    $oldDatabaseConn = new PDO("mysql:host=$hostname;dbname=$oldDatabaseName", $user, $password);
                    $oldDatabaseConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Conexão com o novo banco
                    $newDatabaseConn = new PDO("mysql:host=$hostname;dbname=$newDatabaseName", $user, $password);
                    $newDatabaseConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Pegando os registros do banco antigo
                    $oldRegisters = $oldDatabaseConn->query("select * from pedido");


                    foreach ($oldRegisters as $register) {
                        // Inserindo dado do pedido
                        $orderInsert = $newDatabaseConn->prepare(
                            "INSERT INTO pedido (data_pedido, id_cliente, id_produto, quantidade) VALUES (:dataPedido, :idCliente, :idProduto, :quantidade)"
                        );

                        // Recuperando id do cliente através do cpf
                        $resultQuery = $newDatabaseConn->query("select id from cliente where cpf = " . $register["cpf"]);
                        $idCliente;
                        foreach ($resultQuery as $result) {
                            $idCliente = $result["id"];
                        }

                        // Recuperando id do produto através do código de barras
                        $resultQuery = $newDatabaseConn->query("select id from produto where cod_barras = " . $register["cod_barras"]);
                        $idProduto;
                        foreach ($resultQuery as $result) {
                            $idProduto = $result["id"];
                        }

                        $orderInsert->execute(array(
                            ":dataPedido" => $register["dt_pedido"],
                            ":idCliente" => $idCliente,
                            ":idProduto" => $idProduto,
                            ":quantidade" => $register["quantidade"]
                        ));
                    }

                    // Testando conexão com o novo banco
                    $newData = $newDatabaseConn->query("
                        select p.numero_pedido, p.data_pedido, c.nome_cliente, pr.nome_produto, pr.valor_unitario, p.quantidade
                        from pedido p
                        join cliente c on p.id_cliente = c.id
                        join produto pr on p.id_produto = pr.id;
                    ");

                    if (!empty($newData->fetchAll())) {
                        echo "<p class='success'>Migração concluída com sucesso!</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='error'>Falha no erro :C - " . $e->getMessage() . "</p>";
                } finally {
                    $oldDatabaseConn = null;
                    $newDatabaseConn = null;
                }
                echo "</div>";
            }
        ?>
            <p>Este formulário irá usar os dados informados sobre a conexão e os bancos de dados para rodar um script que irá migrar todos os dados do banco não estruturado para o novo banco estruturado. Esse script só reconhece um padrão definido de modelagem dos dados.</p>

            <form method="POST" action="../migration-data/index.php" class="form-standard" style="width: 60%;">
                <label for="hostname">Local do servidor:</label>
                <input type="text" value="localhost" name="hostname" id="hostname" required/>

                <label for="oldDatabase">Nome do banco antigo:</label>
                <input type="text" value="php_migration_old" name="oldDatabase" id="oldDatabase" required/>

                <label for="newDatabase">Nome do novo banco:</label>
                <input type="text" value="php_crud_structured" name="newDatabase" id="newDatabase" required/>

                <label for="user">Usuário do banco:</label>
                <input type="text" value="root" name="user" id="user" required/>

                <label for="password">Senha do banco*:</label>
                <input type="password" placeholder="password" name="password" id="password"/>

                <input type="submit" value="Fazer migração"/>

                <p class="detail">
                    *Caso esteja usando o XAMPP, deixe vazio.
                </p>
            </form>

            <p>Para testar a migração dos dados baixe e execute os dois dumps num arquivo zip clicando no link abaixo.</p>
            <p class="plink">
                <a href="../_dumps/dumps-project.zip" download>
                    <i class="fas fa-file-archive"></i>
                    Download zip
                </a>
            </p>
        </div>
    </main>
</body>
</html>
