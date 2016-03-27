<?php

class ApontamentosController extends BaseController {
    
    function ApontamentosController() {
        parent::__construct();
    }
    
    private function getParametroTela($parametro) {
        return filter_input(INPUT_POST, $parametro);
    }
    
    private function validarFormulario($vo) {
        if ((!Functions::isEmpty($vo->getId())) && (!is_numeric($vo->getId()))) {
            return 'N' . 'Valor para "ID" é inválido';
        } else if (Functions::isEmpty($vo->getUsuario()->getId())) {
            return 'N' . 'Informe o campo "Usuário"';
        } else if (Functions::isEmpty($vo->getDataInicio())) {
            return 'N' . 'Informe o campo "Data/Hora Início"';
        } else if (!Functions::isDateTime($vo->getDataInicio())) {
            return 'N' . 'Valor inválido para o campo "Data/Hora Início"';
        } else if ((!Functions::isEmpty($vo->getDataFim())) && (!Functions::isDateTime($vo->getDataFim()))) {
            return 'N' . 'Valor inválido para o campo "Data/Hora Final"';
        } else if (($vo->getAvaliacao() == "") && ($vo->getFaturado() != "00:00:00")) {
            return 'N' . 'Quando o "Tipo de Avaliação" é "Não Avaliado", "Valor Faturado" deve ser "00:00:00"';
        } else if (($vo->getAvaliacao() == "3") && ($vo->getFaturado() != "00:00:00")) {
            return 'N' . 'Quando o "Tipo de Avaliação" é "Não Faturado", "Valor Faturado" deve ser "00:00:00"';
        } else if (($vo->getAvaliacao() == "1") && ($vo->getFaturado() != $vo->getApontado())) {
            return 'N' . 'Quando o "Tipo de Avaliação" é "Faturado Integral", "Valor Faturado" deve ser igual a "Valor Apontado"' . $vo->getApontado();
        } else if (($vo->getAvaliacao() == "2") && (($vo->getFaturado() == "00:00:00") || ($vo->getFaturado() == $vo->getApontado()))) {
            return 'N' . 'Quando o "Tipo de Avaliação" é "Faturado Parcial", "Valor Faturado" deve ser diferente de "00:00:00" e de "Valor Apontado"' . $vo->getApontado();
        } else {
            return 'S' . 'Operação realizada com sucesso';
        }
    }
    
    private function validarExclusao($id = "") {
        if (Functions::isEmpty($id)) {
            return 'N' . 'Informe o campo "ID"';
        } else if (!is_numeric($id)) {
            return 'N' . 'Valor para "ID" é inválido';
        } else {
            return 'S' . 'Operação realizada com sucesso';
        }
    }
    
    private function salvarRegistro($connection, $vo) {
        $model = new ApontamentosModel();
        $model->save($connection, $vo);
    }
    
    private function excluirRegistro($connection, $vo) {
        $model = new ApontamentosModel();
        $model->delete($connection, $vo);
    }
    
    public function cadastrarAction() {
        $id = $this->getParametroTela('id');
        $atividade = $this->getParametroTela('atividade');
        $chamado = $this->getParametroTela('chamado');

        $connection = Databases::connect();
        $dados = $this->carregarDadosManter($connection, $id, $atividade, $chamado);
        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
    
    private function carregarDadosManter($connection, $id = "", $atividade = "", $chamado = "", $mensagem = "") {
        // parametro $id pode ser tanto ApontamentoVo como apontamentoCodigo
        if (is_object($id)) {
            $apontamento = $id;
        } else if (!Functions::isEmpty($id)) {
            $model = new ApontamentosModel();
            $apontamento = $model->loadById($connection, $id);
        } else {
            $apontamento = new ApontamentosVo();
        }
        
        $usuariosModel = new UsuariosModel();
        $usuarios = $usuariosModel->load($connection, 0);
        
        return $this->trabalharDadosManter($apontamento, $usuarios, $atividade, $chamado, $mensagem);
    }
    
    private function trabalharDadosManter($apontamento, $usuarios = array(), $atividade = "", $chamado = "", $mensagem = "") {
        return array( 'mensagem' => $mensagem
                    , 'apontamento' => $apontamento
                    , 'usuarios' => $usuarios
                    , 'atividade' => $atividade
                    , 'chamado' => $chamado);
    }
    
    private function exibirTelaManter($dados) {
        $view = new View('views/manterApontamentos.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function salvarCadastrarAction() {
        $connection = Databases::connect();

        $usuariosModel = new UsuariosModel();
        $usuarioVo = $usuariosModel->loadById($connection, $this->getParametroTela('usuario'));

        $atividadesModel = new AtividadesModel();
        $atividadeVo = $atividadesModel->loadById($connection, $this->getParametroTela('atividade'));

        $chamadosModel = new ChamadosModel();
        $chamadoVo = $chamadosModel->loadById($connection, $this->getParametroTela('chamado'));

        $vo = new ApontamentosVo();

        $vo->setId($this->getParametroTela('id'));
        $vo->setUsuario($usuarioVo);
        $vo->setAtividade($atividadeVo);
        $vo->setChamado($chamadoVo);
        $vo->setDataInicio($this->getParametroTela('dataInicio'));
        $vo->setDataFim($this->getParametroTela('dataFim'));
        $vo->setObservacao($this->getParametroTela('observacao'));
        $vo->setAvaliacao($this->getParametroTela('avaliacao'));
        $vo->setFaturado(Functions::toTime($this->getParametroTela('faturado')));

        // Por padrão grava sempre o apontamento como Não Avaliado
        if (Functions::isEmpty($vo->getAvaliacao())) {
            $vo->setAvaliacao(9);
        }

        $mensagem = $this->validarFormulario($vo);

        if (substr($mensagem, 0, 1) == 'S') {
            $this->salvarRegistro($connection, $vo);

            if (!Functions::isEmpty($vo->getAtividade()->getId())) {
                $controller = new AtividadesController();
                $codigo = $vo->getAtividade()->getId();
            }
            if (!Functions::isEmpty($vo->getChamado()->getId())) {
                $controller = new ChamadosController();
                $codigo = $vo->getChamado()->getId();
            }

            $dados = $controller->carregarDadosManter($connection, $codigo, $mensagem);
            Databases::disconnect($connection);
            $controller->exibirTelaManter($dados);
        }

        if (substr($mensagem, 0, 1) == 'N') {
            $dados = $this->carregarDadosManter($connection, $vo, $atividadeVo->getId(), $chamadoVo->getId(), $mensagem);
            Databases::disconnect($connection);
            $this->exibirTelaManter($dados);
        }
    }
    
    public function excluirAction() {
        $connection = Databases::connect();

        $id = $this->getParametroTela('id');
        $model = new ApontamentosModel();
        $apontamento = $model->loadById($connection, $id);

        $mensagem = $this->validarExclusao($id);

        if (substr($mensagem, 0, 1) == 'S') {
            $this->excluirRegistro($connection, $apontamento);
        }

        if (!Functions::isEmpty($apontamento->getAtividade()->getId())) {
            $controller = new AtividadesController();
            $codigo = $apontamento->getAtividade()->getId();
        }
        if (!Functions::isEmpty($apontamento->getChamado()->getId())) {
            $controller = new ChamadosController();
            $codigo = $apontamento->getChamado()->getId();
        }

        $dados = $controller->carregarDadosManter($connection, $codigo, $mensagem);
        Databases::disconnect($connection);
        $controller->exibirTelaManter($dados);
    }
    
    private function validarRelatorio($periodoInicial = "", $periodoFinal = "") {
        if (Functions::isEmpty($periodoInicial)) {
            return 'N' . 'Informe o campo Período Inicial';
        } else if (!Functions::isDate($periodoInicial)) {
            return 'N' . 'Valor inválido para o campo Período Inicial';
        } else if (Functions::isEmpty($periodoFinal)) {
            return 'N' . 'Informe o campo Período Final';
        } else if (!Functions::isDate($periodoFinal)) {
            return 'N' . 'Valor inválido para o campo Período Final';
        } else {
            return 'S' . 'Operação realizada com sucesso';
        }
    }
    
    public function relatorioFaturamentoPorFuncionarioAction() {
        $connection = Databases::connect();

        if (Functions::isEmpty($this->getParametroTela('periodoInicial'))) {
            $periodoInicial = date('d/m/Y');
        } else {
            $periodoInicial = $this->getParametroTela('periodoInicial');
        }

        if (Functions::isEmpty($this->getParametroTela('periodoFinal'))) {
            $periodoFinal = date('d/m/Y');
        } else {
            $periodoFinal = $this->getParametroTela('periodoFinal');
        }

        if (Functions::isEmpty($this->getParametroTela('usuarioCodigo'))) {
            $usuario = new UsuariosVo();
        } else {
            $usuarioModel = new UsuariosModel();
            $usuario = $usuarioModel->loadById($connection, $this->getParametroTela('usuarioCodigo'));
        }
        
        $submit = $this->getParametroTela('submit');
        
        $mensagem = $this->validarRelatorio($periodoInicial, $periodoFinal);

        if (substr($mensagem, 0, 1) == 'S') {
            $mensagem = "";
        }

        $registros = $this->carregarFaturamentoPorFuncionario($connection, $periodoInicial, $periodoFinal, $usuario, $mensagem, $submit);
        Databases::disconnect($connection);

        $this->exibirFaturamentoPorFuncionario($registros);
    }
    
    private function carregarFaturamentoPorFuncionario($connection, $periodoInicial, $periodoFinal, $usuario, $mensagem = "", $submit = "") {
        if ($submit == "Consultar") {
            $model = new ApontamentosModel();
            $registros = $model->loadFaturamentoPorFuncionario($connection, $periodoInicial, $periodoFinal, $usuario->getId());
        } else {
            $registros = array();
        }
        
        $usuariosModel = new UsuariosModel();
        $usuarios = $usuariosModel->loadNaoClientes($connection,0);
        
        return $this->trabalharFaturamentoPorFuncionario($periodoInicial, $periodoFinal, $usuario, $usuarios, $registros, $mensagem);
    }
    
    private function trabalharFaturamentoPorFuncionario($periodoInicial, $periodoFinal, $usuario, $usuarios = array(), $registros = array(), $mensagem = "") {
        return array( 'mensagem'       => $mensagem
                    , 'periodoInicial' => $periodoInicial
                    , 'periodoFinal'   => $periodoFinal
                    , 'usuario'        => $usuario
                    , 'usuarios'       => $usuarios
                    , 'registros'      => $registros);
    }
    
    private function exibirFaturamentoPorFuncionario($dados = array()) {
        $view = new View('views/relatorioFaturamentoPorFuncionario.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function relatorioFaturamentoPorEmpresaAction() {
        $connection = Databases::connect();

        if (Functions::isEmpty($this->getParametroTela('periodoInicial'))) {
            $periodoInicial = date('d/m/Y');
        } else {
            $periodoInicial = $this->getParametroTela('periodoInicial');
        }

        if (Functions::isEmpty($this->getParametroTela('periodoFinal'))) {
            $periodoFinal = date('d/m/Y');
        } else {
            $periodoFinal = $this->getParametroTela('periodoFinal');
        }

        if (Functions::isEmpty($this->getParametroTela('empresaCodigo'))) {
            $empresa = new EmpresasVo();
        } else {
            $empresaModel = new EmpresasModel();
            $empresa = $empresaModel->loadById($connection, $this->getParametroTela('empresaCodigo'));
        }
        
        $submit = $this->getParametroTela('submit');
        
        $mensagem = $this->validarRelatorio($periodoInicial, $periodoFinal);

        if (substr($mensagem, 0, 1) == 'S') {
            $mensagem = "";
        }

        $registros = $this->carregarFaturamentoPorEmpresa($connection, $periodoInicial, $periodoFinal, $empresa, $mensagem, $submit);
        Databases::disconnect($connection);

        $this->exibirFaturamentoPorEmpresa($registros);
    }
    
    private function carregarFaturamentoPorEmpresa($connection, $periodoInicial, $periodoFinal, $empresa, $mensagem = "", $submit = "") {
        if ($submit == "Consultar") {
            $model = new ApontamentosModel();
            $registros = $model->loadFaturamentoPorEmpresa($connection, $periodoInicial, $periodoFinal, $empresa->getId());
        } else {
            $registros = array();
        }
        
        $empresasModel = new EmpresasModel();
        $empresas = $empresasModel->load($connection);
        
        return $this->trabalharFaturamentoPorEmpresa($periodoInicial, $periodoFinal, $empresa, $empresas, $registros, $mensagem);
    }
    
    private function trabalharFaturamentoPorEmpresa($periodoInicial, $periodoFinal, $empresa, $empresas = array(), $registros = array(), $mensagem = "") {
        return array( 'mensagem'       => $mensagem
                    , 'periodoInicial' => $periodoInicial
                    , 'periodoFinal'   => $periodoFinal
                    , 'empresa'        => $empresa
                    , 'empresas'       => $empresas
                    , 'registros'      => $registros);
    }
    
    private function exibirFaturamentoPorEmpresa($dados = array()) {
        $view = new View('views/relatorioFaturamentoPorEmpresa.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function relatorioApontamentosParaAvaliacaoAction() {
        $connection = Databases::connect();

        if (Functions::isEmpty($this->getParametroTela('periodoInicial'))) {
            $periodoInicial = date('d/m/Y');
        } else {
            $periodoInicial = $this->getParametroTela('periodoInicial');
        }

        if (Functions::isEmpty($this->getParametroTela('periodoFinal'))) {
            $periodoFinal = date('d/m/Y');
        } else {
            $periodoFinal = $this->getParametroTela('periodoFinal');
        }

        if (Functions::isEmpty($this->getParametroTela('usuarioCodigo'))) {
            $usuario = new UsuariosVo();
        } else {
            $usuarioModel = new UsuariosModel();
            $usuario = $usuarioModel->loadById($connection, $this->getParametroTela('usuarioCodigo'));
        }

        if (Functions::isEmpty($this->getParametroTela('empresaCodigo'))) {
            $empresa = new EmpresasVo();
        } else {
            $empresaModel = new EmpresasModel();
            $empresa = $empresaModel->loadById($connection, $this->getParametroTela('empresaCodigo'));
        }

        $apontamentoAvaliacao = $this->getParametroTela('apontamentoAvaliacao');

        if (Functions::isEmpty($this->getParametroTela('apontamentoTipo'))) {
            $apontamentoTipo = "";
        } else {
            $apontamentoTipo = $this->getParametroTela('apontamentoTipo');
        }
        
        $submit = $this->getParametroTela('submit');
        
        $caller = $this->getParametroTela('caller');
        
        $mensagem = $this->validarRelatorio($periodoInicial, $periodoFinal);

        if (substr($mensagem, 0, 1) == 'S') {
            $mensagem = "";
        }
        
        $registros = $this->carregarApontamentosParaAvaliacao($connection, $periodoInicial, $periodoFinal, $usuario, $empresa, $apontamentoAvaliacao, $apontamentoTipo, $mensagem, $submit, $caller);
        
        Databases::disconnect($connection);

        $this->exibirApontamentosParaAvaliacao($registros);
    }
    
    private function carregarApontamentosParaAvaliacao($connection, $periodoInicial, $periodoFinal, $usuario, $empresa, $apontamentoAvaliacao, $apontamentoTipo, $mensagem = "", $submit = "", $caller = "") {
        if (($submit == "Consultar") || (!Functions::isEmpty($caller))) {
            $model = new ApontamentosModel();
            $registros = $model->loadApontamentosParaAvaliacao($connection, $periodoInicial, $periodoFinal, $usuario->getId(), $empresa->getId(), $apontamentoAvaliacao, $apontamentoTipo);
        } else {
            $registros = array();
        }
        
        $usuariosModel = new UsuariosModel();
        $usuarios = $usuariosModel->loadNaoClientes($connection, 0);
        
        $empresasModel = new EmpresasModel();
        $empresas = $empresasModel->load($connection);
        
        return $this->trabalharApontamentosParaAvaliacao($periodoInicial, $periodoFinal, $usuario, $empresa, $usuarios, $empresas, $apontamentoAvaliacao, $apontamentoTipo, $registros, $mensagem);
    }
    
    private function trabalharApontamentosParaAvaliacao($periodoInicial, $periodoFinal, $usuario, $empresa, $usuarios = array(), $empresas = array(), $apontamentoAvaliacao, $apontamentoTipo, $registros = array(), $mensagem = "") {
        return array( 'mensagem'              => $mensagem
                    , 'periodoInicial'        => $periodoInicial
                    , 'periodoFinal'          => $periodoFinal
                    , 'usuario'               => $usuario
                    , 'empresa'               => $empresa
                    , 'usuarios'              => $usuarios
                    , 'empresas'              => $empresas
                    , 'apontamentoAvaliacao'  => $apontamentoAvaliacao
                    , 'apontamentoTipo'       => $apontamentoTipo
                    , 'registros'             => $registros);
    }
    
    private function exibirApontamentosParaAvaliacao($dados = array()) {
        $view = new View('views/relatorioApontamentosParaAvaliacao.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function validarApontamentoParaAvaliacao($connection, $apontamentosCodigos = array(), $apontamentosDuracoes = array(), $apontamentosAvaliacoes = array(), $apontamentosFaturados = array()) {
        for ($i = 0; $i < count($apontamentosCodigos); $i++) {
            if (!Functions::isTime($apontamentosDuracoes[$i])) {
                return 'N' . 'Valor inválido de "Valor Apontado" na linha '. ($i + 1) . ': ' . $apontamentosDuracoes[$i];
            } else if (!Functions::isTime($apontamentosFaturados[$i])) {
                return 'N' . 'Valor inválido de "Valor Faturado" na linha '. ($i + 1) . ': ' . $apontamentosFaturados[$i];
            } else if (Functions::isEmpty($apontamentosAvaliacoes[$i])) {
                return 'N' . 'Informe o campo "Tipo de Avaliação" na linha '. ($i + 1);
            } else if (($apontamentosAvaliacoes[$i] == "3") && ($apontamentosFaturados[$i] != "00:00:00")) {
                return 'N' . 'Na linha ' . ($i + 1) . ', quando o "Tipo de Avaliação" é "Não Faturado", "Valor Faturado" deve ser "00:00:00"';
            } else if (($apontamentosAvaliacoes[$i] == "1") && ($apontamentosFaturados[$i] != $apontamentosDuracoes[$i])) {
                return 'N' . 'Na linha ' . ($i + 1) . ', quando o "Tipo de Avaliação" é "Faturado Integral", "Valor Faturado" deve ser igual a "Valor Apontado"';
            } else if (($apontamentosAvaliacoes[$i] == "2") && (($apontamentosFaturados[$i] == "00:00:00") || ($apontamentosFaturados[$i] == $apontamentosDuracoes[$i]))) {
                return 'N' . 'Na linha ' . ($i + 1) . ', quando o "Tipo de Avaliação" é "Faturado Parcial", "Valor Faturado" deve ser diferente de "00:00:00" e de "Valor Apontado"';
            } else {
                $model = new ApontamentosModel();
                $vo = $model->loadById($connection, $apontamentosCodigos[$i]);
                $vo->setAvaliacao($apontamentosAvaliacoes[$i]);
                $vo->setFaturado($apontamentosFaturados[$i]);
                $model->save($connection, $vo);
            }
        }
        return 'S' . 'Operação realizada com sucesso';
    }
    
    public function salvarApontamentoParaAvaliacaoAction() {
        $connection = Databases::connect();
        
        if (Functions::isEmpty($this->getParametroTela('periodoInicial'))) {
            $periodoInicial = date('d/m/Y');
        } else {
            $periodoInicial = $this->getParametroTela('periodoInicial');
        }
        
        if (Functions::isEmpty($this->getParametroTela('periodoFinal'))) {
            $periodoFinal = date('d/m/Y');
        } else {
            $periodoFinal = $this->getParametroTela('periodoFinal');
        }
        
        if (Functions::isEmpty($this->getParametroTela('usuarioCodigo'))) {
            $usuario = new UsuariosVo();
        } else {
            $usuarioModel = new UsuariosModel();
            $usuario = $usuarioModel->loadById($connection, $this->getParametroTela('usuarioCodigo'));
        }
        
        if (Functions::isEmpty($this->getParametroTela('empresaCodigo'))) {
            $empresa = new EmpresasVo();
        } else {
            $empresaModel = new EmpresasModel();
            $empresa = $empresaModel->loadById($connection, $this->getParametroTela('empresaCodigo'));
        }

        if (Functions::isEmpty($this->getParametroTela('apontamentoAvaliacao'))) {
            $apontamentoAvaliacao = "";
        } else {
            $apontamentoAvaliacao = $this->getParametroTela('apontamentoAvaliacao');
        }

        if (Functions::isEmpty($this->getParametroTela('apontamentoTipo'))) {
            $apontamentoTipo = "";
        } else {
            $apontamentoTipo = $this->getParametroTela('apontamentoTipo');
        }

        $apontamentosCodigos    = $_REQUEST['apontamentoCodigo'];
        $apontamentosDuracoes   = $_REQUEST['apontamentoDuracao'];
        $apontamentosAvaliacoes = $_REQUEST['apontamentoAvaliacao'];
        $apontamentosFaturados  = $_REQUEST['apontamentoFaturado'];

        $mensagem = $this->validarApontamentoParaAvaliacao($connection, $apontamentosCodigos, $apontamentosDuracoes, $apontamentosAvaliacoes, $apontamentosFaturados);

        if (substr($mensagem, 0, 1) == 'S') {
            $mensagem = "";
        }

        $registros = $this->carregarApontamentosParaAvaliacao($connection, $periodoInicial, $periodoFinal, $usuario, $empresa, $apontamentoAvaliacao, $apontamentoTipo, $mensagem);
        Databases::disconnect($connection);

        $this->exibirApontamentosParaAvaliacao($registros);
    }
    
    public function validarIniciarApontamento($atividadeVo, $chamadoVo, $apontamento, $tipoApontamento, $codigo) {
        if (!Functions::isEmpty($chamadoVo->getId())) {
            if ($_SESSION['perfilCliente'] == 1) {
                if (!Functions::isEmpty($chamadoVo->getAtendente()->getId())) {
                    return 'N' . 'Chamado em atendimento, favor entrar em contato com a BasisIT';
                }
            } else {
                if (Functions::isEmpty($chamadoVo->getAtendente()->getId())) {
                    return 'N' . 'Informe o campo "Analista" antes de iniciar essa operação';
                }
            }
        }
        if ($apontamento->getAtividade() != "") {
            if (!Functions::isEmpty($apontamento->getAtividade()->getId())) {
                if ($_SESSION['perfilCliente'] == 1) {
                    return 'N' . 'Chamado em atendimento, favor entrar em contato com a BasisIT';
                } else {
                    return 'N' . 'A atividade de número ' . $apontamento->getAtividade()->getId() . ' está em andamento para este funcionário! É necessário encerrá-la primeiro! Clique <a href="javascript:parar2(' . $apontamento->getAtividade()->getId() . ',' . $codigo . ');">aqui</a> para encerrá-la!';
                }
            }
        }
        if ($apontamento->getChamado() != "") {
            if (!Functions::isEmpty($apontamento->getChamado()->getId())) {
                if ($_SESSION['perfilCliente'] == 1) {
                    return 'N' . 'Chamado em atendimento, favor entrar em contato com a BasisIT';
                } else {
                    return 'N' . 'O chamado de número ' . $apontamento->getChamado()->getId() . ' está em andamento para este funcionário! É necessário encerrá-lo primeiro! Clique <a href="javascript:parar2(' . $apontamento->getChamado()->getId() . ',' . $codigo . ');">aqui</a> para encerrá-lo!';
                }
            }
        }
        return 'S' . 'Operação realizada com sucesso';
    }
    
    public function iniciarAction() {
        $connection = Databases::connect();

        $atividade = $this->getParametroTela('atividade');
        $chamado = $this->getParametroTela('chamado');
        $observacao = $this->getParametroTela('observacao');
        $visualizar = $this->getParametroTela('visualizar');

        if (!Functions::isEmpty($atividade)) {
            $tipoApontamento = "A";
            $controller = new AtividadesController();
            $codigo = $atividade;
        }
        if (!Functions::isEmpty($chamado)) {
            $tipoApontamento = "C";
            $controller = new ChamadosController();
            $codigo = $chamado;
        }

        $mensagem = $this->iniciar($connection, $tipoApontamento, $codigo, $observacao);

        if (!Functions::isEmpty($visualizar)) {
            $codigo = $visualizar;
        }

        $dados = $controller->carregarDadosManter($connection, $codigo, $mensagem);
        Databases::disconnect($connection);
        $controller->exibirTelaManter($dados);
    }
    
    public function iniciar($connection, $tipoApontamento, $codigo, $observacao) {
        if ($tipoApontamento == "A") {
            $atividadeModel = new AtividadesModel();
            $atividadeVo = $atividadeModel->loadById($connection, $codigo);
            $chamadoVo = new ChamadosVo();
        }
        if ($tipoApontamento == "C") {
            $chamadoModel = new ChamadosModel();
            $chamadoVo = $chamadoModel->loadById($connection, $codigo);
            $atividadeVo = new AtividadesVo();
        }
        
        $usuarioModel = new UsuariosModel();
        $usuarioVo = $usuarioModel->loadById($connection, $_SESSION['usuarioCodigo']);
        
        $apontamentoModel = new ApontamentosModel();
        $apontamento = $apontamentoModel->loadUltimaAberta($connection, $usuarioVo->getId(), $tipoApontamento);
        
        $mensagem = $this->validarIniciarApontamento($atividadeVo, $chamadoVo, $apontamento, $tipoApontamento, $codigo);
        
        if (substr($mensagem, 0, 1) == 'S') {
            $apontamento->setAtividade($atividadeVo);
            $apontamento->setChamado($chamadoVo);
            $apontamento->setUsuario($usuarioVo);
            $apontamento->setDataInicio(date('d/m/Y H:i'));
            $apontamento->setDataFim("");
            $apontamento->setObservacao($observacao);
            $apontamento->setAvaliacao(9);
            
            $this->salvarRegistro($connection, $apontamento);
        }
        
        return $mensagem;
    }
    
    private function validarPararApontamento($apontamento, $tipoApontamento, $codigo) {
        if ($apontamento->getId() == "") {
            return 'N' . 'Não existe nenhum apontamento em andamento para este funcionário! É necessário iniciar um primeiro!';
        } else if (($tipoApontamento == "A") && ($apontamento->getAtividade()->getId() != $codigo)) {
            return 'N' . 'A atividade de número ' . $apontamento->getAtividade()->getId() . ' está em andamento para este funcionário! É necessário encerrá-la primeiro! Clique <a href="javascript:parar2(' . $apontamento->getAtividade()->getId() . ',' . $codigo . ');">aqui</a> para encerrá-la!';
        } else if (($tipoApontamento == "C") && ($apontamento->getChamado()->getId() != $codigo)) {
            return 'N' . 'O chamado de número ' . $apontamento->getChamado()->getId() . ' está em andamento para este funcionário! É necessário encerrá-lo primeiro! Clique <a href="javascript:parar2(' . $apontamento->getChamado()->getId() . ',' . $codigo . ');">aqui</a> para encerrá-lo';
        } else {
            return 'S' . 'Operação realizada com sucesso';
        }
    }
    
    public function pararAction() {
        $connection = Databases::connect();

        $atividade = $this->getParametroTela('atividade');
        $chamado = $this->getParametroTela('chamado');

        if (!Functions::isEmpty($atividade)) {
            $tipoApontamento = "A";
            $controller = new AtividadesController();
            $codigo = $atividade;
        }
        if (!Functions::isEmpty($chamado)) {
            $tipoApontamento = "C";
            $controller = new ChamadosController();
            $codigo = $chamado;
        }

        $apontamentoModel = new ApontamentosModel();
        $apontamento = $apontamentoModel->loadUltimaAberta($connection, $_SESSION['usuarioCodigo'], $tipoApontamento);

        $mensagem = $this->validarPararApontamento($apontamento, $tipoApontamento, $codigo);

        if (substr($mensagem, 0, 1) == 'S') {
            $apontamento->setDataFim(date('d/m/Y H:i'));
            $apontamento->setObservacao($apontamento->getObservacao() . '<br />-<br />' . $this->getParametroTela('observacao'));
            $apontamento->setAvaliacao(9);

            $this->salvarRegistro($connection, $apontamento);
        }

        if (!Functions::isEmpty($this->getParametroTela('visualizar'))) {
            $codigo = $this->getParametroTela('visualizar');
        }

        $dados = $controller->carregarDadosManter($connection, $codigo, $mensagem);
        Databases::disconnect($connection);
        $controller->exibirTelaManter($dados);
    }
    
    public function ajaxAtualizarSessaoAction() {
        $index = new IndexController();
        $index->atualizarSessao();
    }
    
}