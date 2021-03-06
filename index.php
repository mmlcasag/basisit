<?php

if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
    $redirect = "https://basisit3.websiteseguro.com".$_SERVER['REQUEST_URI'];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
}

require 'models/Databases.php';

require 'lib/Application.php';
require 'lib/Functions.php';
require 'lib/View.php';

require 'vo/ApontamentosVo.php';
require 'vo/AtividadesVo.php';
require 'vo/CategoriasVo.php';
require 'vo/ChamadosHistoricosVo.php';
require 'vo/ChamadosVo.php';
require 'vo/EmpresasVo.php';
require 'vo/ModulosVo.php';
require 'vo/ParametrosVo.php';
require 'vo/PerfisVo.php';
require 'vo/PerfisPermissoesVo.php';
require 'vo/PrioridadesVo.php';
require 'vo/SituacoesVo.php';
require 'vo/TiposAmbientesVo.php';
require 'vo/TiposApontamentosVo.php';
require 'vo/TiposAtividadesVo.php';
require 'vo/TiposAvaliacoesVo.php';
require 'vo/TiposProdutosVo.php';
require 'vo/TiposRelatoriosVo.php';
require 'vo/TiposSistemasVo.php';
require 'vo/UsuariosVo.php';

require 'models/ApontamentosModel.php';
require 'models/AtividadesModel.php';
require 'models/CategoriasModel.php';
require 'models/ChamadosHistoricosModel.php';
require 'models/ChamadosModel.php';
require 'models/EmpresasModel.php';
require 'models/ModulosModel.php';
require 'models/ParametrosModel.php';
require 'models/PerfisModel.php';
require 'models/PerfisPermissoesModel.php';
require 'models/PrioridadesModel.php';
require 'models/SituacoesModel.php';
require 'models/TiposAmbientesModel.php';
require 'models/TiposApontamentosModel.php';
require 'models/TiposAtividadesModel.php';
require 'models/TiposAvaliacoesModel.php';
require 'models/TiposProdutosModel.php';
require 'models/TiposRelatoriosModel.php';
require 'models/TiposSistemasModel.php';
require 'models/UsuariosModel.php';

require 'controllers/BaseController.php';
require 'controllers/ApontamentosController.php';
require 'controllers/AtividadesController.php';
require 'controllers/CategoriasController.php';
require 'controllers/ChamadosController.php';
require 'controllers/EmpresasController.php';
require 'controllers/IndexController.php';
require 'controllers/ModulosController.php';
require 'controllers/ParametrosController.php';
require 'controllers/PerfisController.php';
require 'controllers/PerfisPermissoesController.php';
require 'controllers/PrioridadesController.php';
require 'controllers/SituacoesController.php';
require 'controllers/TiposAmbientesController.php';
require 'controllers/TiposAtividadesController.php';
require 'controllers/TiposProdutosController.php';
require 'controllers/TiposSistemasController.php';
require 'controllers/UsuariosController.php';

require 'vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

ini_set('session.cookie_lifetime', '360000'); //100 hours
ini_set('default_charset', 'UTF-8');
ini_set('file_uploads', 'On');
ini_set('display_errors', 'Off');

error_reporting(E_ALL & ~E_STRICT);

session_start();

$application = new Application();
$application->dispatch();