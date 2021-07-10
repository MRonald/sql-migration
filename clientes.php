<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Crud Project</title>
    <script src="https://kit.fontawesome.com/407f74df21.js" crossorigin="anonymous"></script>
    <script src="scripts/menu.js" defer></script>
    <link rel="stylesheet" href="styles/global.css"/>
    <link rel="stylesheet" href="styles/tabela-dados.css"/>
</head>
<body>
    <header class="header-main">
        <nav class="menu">
            <i class="fas fa-bars" onclick="showMenu()"></i>
            <div class="menu-side" id="menu-side">
                <i class="fas fa-times" onclick="hideMenu()"></i>
                <ul>
                    <li>
                        <a href="index.html">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="pedidos.html">
                            Cliente
                        </a>
                    </li>
                    <li>
                        <a href="pedidos.html">
                            Produto
                        </a>
                    </li>
                    <li>
                        <a href="pedidos.html">
                            Pedido
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <h1>Crud Project</h1>
    </header>
    <main class="content-main" id="content-main">
        <div class="titles-data">
            <div class="name">Nome</div>
            <div class="cpf">CPF</div>
            <div class="email">Email</div>
            <div class="actions">Ações</div>
        </div>
        <?php
            function formatCpf($cpf){
                $cpf = preg_replace("/[^0-9]/", "", $cpf);

                $firstBlock = substr($cpf,0,3);
                $secondBlock = substr($cpf,3,3);
                $thirdBlock = substr($cpf,6,3);
                $lastBlock = substr($cpf,-2);
                $cpfFormated = $firstBlock.".".$secondBlock.".".$thirdBlock."-".$lastBlock;

                return $cpfFormated;
            }

            include_once('./connection.php');

            $resultsClients = $connection->query('SELECT * FROM cliente');

            foreach ($resultsClients as $result) {
                echo '
                    <div class="result-data">
                        <div class="name">'. $result['nome_cliente'] .'</div>
                        <div class="cpf">'. formatCpf($result['cpf']) .'</div>
                        <div class="email">'. $result['email'] .'</div>
                        <div class="actions">
                            <a href="./editar/cliente.php?id='. $result['id'] .'">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="./apagar/cliente.php?id='. $result['id'] .'">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </div>
                ';
            }

            unset($connection);
        ?>
    </main>
</body>
</html>