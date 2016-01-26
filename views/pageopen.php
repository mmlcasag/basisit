<?php

$connection = Databases::connect();
    $model = new TiposSistemasModel();
    $items = $model->loadByPerfil($connection, $_SESSION['perfilCodigo']);
Databases::disconnect($connection);

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
    
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    
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

<body>
    
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
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#" onclick="profile(<?php echo $_SESSION['usuarioCodigo']; ?>);"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;&nbsp;Meus Dados</a></li>
                        <li><a href="#" onclick="redirect('Index','logout');"><span class="glyphicon glyphicon-off"></span>&nbsp;&nbsp;&nbsp;Sair</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>