<?php

$connection = Databases::connect();
    $model = new TiposSistemasModel();
    $items = $model->loadByPerfil($connection, $_SESSION['perfilCodigo']);
Databases::disconnect($connection);

$quantidadeCadastros = 0;
foreach($items as $item) {
    if (substr($item->getDescricao(),0,1) == "C") {
        $quantidadeCadastros++;
    }
}

$quantidadeLancamentos = 0;
foreach($items as $item) {
    if (substr($item->getDescricao(),0,1) == "L") {
        $quantidadeLancamentos++;
    }
}

$quantidadeRelatorios = 0;
foreach($items as $item) {
    if (substr($item->getDescricao(),0,1) == "R") {
        $quantidadeRelatorios++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="template/images/favicon.ico">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>BasisIT</title>

    <!-- Bootstrap -->
    <link href="lib/bootstrap-3.3.4-dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="lib/jquery/jquery.js"></script>
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="lib/bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
    
    <link href="template/css/template.css" rel="stylesheet">
    
    <script type="text/javascript" src="template/js/default.js"></script>
    
    <style type="text/css">
        table {
            table-layout: fixed;
            word-wrap: break-word;
        }
    </style>
    
    <script type="text/javascript">
        function redirect(controle, acao) {
            postViaJS('', {controle: controle, acao: acao});
        }
        function voltar() {
            postViaJS('', {controle: 'Index', acao: 'index'});
        }
        function profile(usuarioCodigo) {
            postViaJS('', {controle: 'Usuarios', acao: 'atualizar', id: usuarioCodigo});
        }
    </script>
</head>

<body id="app-layout">
    
    <div id="header">
        <img src="template/images/bg-header.jpg" alt="" width="100%" />
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span> 
                    </button>
                    <a class="navbar-brand" href="#" onclick="redirect('Index','index')">BasisIT</a>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav">
                        <li><a href="#" onclick="redirect('Index','index')"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;&nbsp;Home</a></li>
                        <?php if ($quantidadeCadastros > 0) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Cadastros
                            <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <?php
                                foreach($items as $item) {
                                    if (substr($item->getDescricao(),0,1) == "C") {
                                        echo "<li><a href='#' onclick=javascript:redirect('" . $item->getNomeMenu() . "','" . $item->getEnderecoListar() . "')>" . $item->getDescricao() . "</a></li>";
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php if ($quantidadeLancamentos > 0) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Lançamentos
                            <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <?php
                                foreach($items as $item) {
                                    if (substr($item->getDescricao(),0,1) == "L") {
                                        echo "<li><a href='#' onclick=javascript:redirect('" . $item->getNomeMenu() . "','" . $item->getEnderecoListar() . "')>" . $item->getDescricao() . "</a></li>";
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php if ($quantidadeRelatorios > 0) { ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Relatórios
                            <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <?php
                                foreach($items as $item) {
                                    if (substr($item->getDescricao(),0,1) == "R") {
                                        echo "<li><a href='#' onclick=javascript:redirect('" . $item->getNomeMenu() . "','" . $item->getEnderecoListar() . "')>" . $item->getDescricao() . "</a></li>";
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                        <?php } ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#" onclick="profile(<?php echo $_SESSION['usuarioCodigo']; ?>);"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;&nbsp;Meus Dados</a></li>
                        <li><a href="#" onclick="redirect('Index','logout');"><span class="glyphicon glyphicon-off"></span>&nbsp;&nbsp;&nbsp;Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>