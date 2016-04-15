<?php

class AtividadesController extends BaseController {
    
    function AtividadesController() {
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
        } else if (Functions::isEmpty($vo->getData())) {
            return 'N' . 'Informe o campo "Data"';
        } else if (Functions::isEmpty($vo->getSituacao()->getId())) {
            return 'N' . 'Informe o campo "Situação"';
        } else if (Functions::isEmpty($vo->getEmpresa()->getId())) {
            return 'N' . 'Informe o campo "Empresa"';
        } else if (Functions::isEmpty($vo->getTipoAtividade()->getId())) {
            return 'N' . 'Informe o campo "Tipo de Atividade"';
        } else if (Functions::isEmpty($vo->getAssunto())) {
            return 'N' . 'Informe o campo "Assunto"';
        } else if (Functions::isEmpty($vo->getObservacao())) {
            return 'N' . 'Informe o campo "Observação"';
        } else {
            return 'S' . 'Operação realizada com sucesso';
        }
    }
    
    private function salvarRegistro($connection, $vo) {
        $model = new AtividadesModel();
        return $model->save($connection, $vo);
    }
    
    public function filtrarAction($mensagem = "") {
        $connection = Databases::connect();
        $dados = $this->carregarDadosFiltrar($connection, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaFiltrar($dados);
    }
    
    private function carregarDadosFiltrar($connection, $mensagem = "") {
        $situacoesModel = new SituacoesModel();
        $situacoes = $situacoesModel->load($connection);
        
        $usuariosModel = new UsuariosModel();
        $usuarios = $usuariosModel->loadNaoClientes($connection, 0);
        
        $empresasModel = new EmpresasModel();
        $empresas = $empresasModel->load($connection);
        
        $tiposAtividadesModel = new TiposAtividadesModel();
        $tiposAtividades = $tiposAtividadesModel->load($connection);
        
        return $this->trabalharDadosFiltrar($situacoes, $usuarios, $empresas, $tiposAtividades, $mensagem);
    }
    
    private function trabalharDadosFiltrar($situacoes = array(), $usuarios = array(), $empresas = array(), $tiposAtividades = array(), $mensagem = "") {
        return array( 'mensagem' => $mensagem
                    , 'situacoes' => $situacoes
                    , 'usuarios' => $usuarios
                    , 'empresas' => $empresas
                    , 'tiposAtividades' => $tiposAtividades);
    }
    
    private function exibirTelaFiltrar($dados) {
        $view = new View('views/filtrarAtividades.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function listarAction($mensagem = "") {
        $connection = Databases::connect();
        $dados = $this->carregarDadosListar($connection, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaListar($dados);
    }
    
    private function carregarDadosListar($connection, $mensagem = "") {
        $codigo          = $this->getParametroTela('id');
        $usuario         = $this->getParametroTela('usuario');
        $dataIni         = $this->getParametroTela('dataIni');
        $dataFim         = $this->getParametroTela('dataFim');
        $empresa         = $this->getParametroTela('empresa');
        $tipoAtividade   = $this->getParametroTela('tipoAtividade');
        $situacao        = $this->getParametroTela('situacao');
        $assunto         = $this->getParametroTela('assunto');
        $especial        = $this->getParametroTela('especial');
        $especialCliente = $this->getParametroTela('especialCliente');
        
        $model = new AtividadesModel();
        $registros = $model->loadByCriteria($connection, $codigo, $usuario, $dataIni, $dataFim, $empresa, $tipoAtividade, $situacao, $assunto, $especial, $especialCliente);
        
        return $this->trabalharDadosListar($registros, $mensagem);
    }
    
    private function trabalharDadosListar($registros = array(), $mensagem = "") {
        return array( 'mensagem' => $mensagem
                    , 'registros' => $registros);
    }
    
    private function exibirTelaListar($dados) {
        $view = new View('views/listarAtividades.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function cadastrarAction() {
        $id = $this->getParametroTela('id');

        $connection = Databases::connect();
        $dados = $this->carregarDadosManter($connection, $id);
        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
    
    public function carregarDadosManter($connection, $id = "", $mensagem = "") {
        // parametro $id pode ser tanto AtividadeVo como atividadeCodigo
        if (is_object($id)) {
            $atividade = $id;
        } else if (!Functions::isEmpty($id)) {
            $model = new AtividadesModel();
            $atividade = $model->loadById($connection, $id);
        } else {
            $atividade = new AtividadesVo();
        }
        
        $situacoesModel = new SituacoesModel();
        $situacoes = $situacoesModel->load($connection);
        
        $apenasAtivos = 0;
        $usuariosModel = new UsuariosModel();
        if (Functions::isEmpty($atividade->getId())) {
            $apenasAtivos = 1;
        }
        $usuarios = $usuariosModel->loadNaoClientes($connection, $apenasAtivos);
        
        $empresasModel = new EmpresasModel();
        $empresas = $empresasModel->load($connection);
        
        $tiposAtividadesModel = new TiposAtividadesModel();
        $tiposAtividades = $tiposAtividadesModel->load($connection);
        
        $apontamentosModel = new ApontamentosModel();
        $apontamentos = $apontamentosModel->load($connection, $atividade->getId());
        
        $outraAtividade = new AtividadesVo();
        
        $caller = "cadastrar";
        
        return $this->trabalharDadosManter($atividade, $situacoes, $usuarios, $empresas, $tiposAtividades, $apontamentos, $outraAtividade, $caller, $mensagem);
    }
    
    private function trabalharDadosManter($atividade, $situacoes = array(), $usuarios = array(), $empresas = array(), $tiposAtividades = array(), $apontamentos = array(), $outraAtividade = "", $caller = "", $mensagem = "") {
        return array( 'mensagem'        => $mensagem
                    , 'atividade'       => $atividade
                    , 'situacoes'       => $situacoes
                    , 'usuarios'        => $usuarios
                    , 'empresas'        => $empresas
                    , 'tiposAtividades' => $tiposAtividades
                    , 'apontamentos'    => $apontamentos
                    , 'outraAtividade'  => $outraAtividade
                    , 'caller'          => $caller);
    }
    
    public function exibirTelaManter($dados) {
        $view = new View('views/manterAtividades.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function salvarCadastrarAction() {
        $connection = Databases::connect();

        $situacoesModel = new SituacoesModel();
        $situacaoVo = $situacoesModel->loadById($connection, $this->getParametroTela('situacao'));

        $usuariosModel = new UsuariosModel();
        $usuarioVo = $usuariosModel->loadById($connection, $this->getParametroTela('usuario'));

        $empresasModel = new EmpresasModel();
        $empresaVo = $empresasModel->loadById($connection, $this->getParametroTela('empresa'));

        $tiposAtividadesModel = new TiposAtividadesModel();
        $tipoAtividadeVo = $tiposAtividadesModel->loadById($connection, $this->getParametroTela('tipoAtividade'));

        if (Functions::isEmpty($this->getParametroTela('id'))) {
            $modo = "I";
        } else {
            $modo = "A";
        }

        $vo = new AtividadesVo();

        $vo->setId($this->getParametroTela('id'));
        $vo->setData($this->getParametroTela('data'));
        $vo->setSituacao($situacaoVo);
        $vo->setUsuario($usuarioVo);
        $vo->setEmpresa($empresaVo);
        $vo->setTipoAtividade($tipoAtividadeVo);
        $vo->setAssunto($this->getParametroTela('assunto'));
        $vo->setObservacao($this->getParametroTela('observacao'));

        $mensagem = $this->validarFormulario($vo);

        if (substr($mensagem, 0, 1) == 'S') {
            $id = $this->salvarRegistro($connection, $vo);
            $vo->setId($id);

            if ($modo == "I") {
                $controller = new ApontamentosController();
                $mensagem = $controller->iniciar($connection, "A", $vo->getId(), "Iniciado automaticamente");
            }
        }

        $dados = $this->carregarDadosManter($connection, $vo, $mensagem);
        Databases::disconnect($connection);
        $this->exibirTelaManter($dados);
    }
    
    public function finalizarAction($mensagem = "") {
        $connection = Databases::connect();

        $id = $this->getParametroTela('id');

        $atividadeModel = new AtividadesModel();
        $atividadeVo = $atividadeModel->loadById($connection, $id);

        $usuarioModel = new UsuariosModel();
        $usuarioVo = $usuarioModel->loadById($connection, $_SESSION['usuarioCodigo']);

        $apontamentoModel = new ApontamentosModel();
        $apontamento = $apontamentoModel->verificaSeAberto($connection, "A", $id);

        $apontamentoController = new ApontamentosController();
        $mensagem = $apontamentoController->validarIniciarApontamento($atividadeVo, new ChamadosVo(), $apontamento, "A", $id);

        if (substr($mensagem, 0, 1) == 'S') {
            $situacaoModel = new SituacoesModel();
            $situacaoVo = $situacaoModel->loadById($connection, $_SESSION['situacaoFinalizada']); // Finalizado

            $atividadeVo->setSituacao($situacaoVo);
            $atividadeModel->save($connection, $atividadeVo);
        }

        $dados = $this->carregarDadosManter($connection, $id, $mensagem);

        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
    
    public function cancelarAction($mensagem = "") {
        $connection = Databases::connect();

        $id = $this->getParametroTela('id');

        $atividadeModel = new AtividadesModel();
        $atividadeVo = $atividadeModel->loadById($connection, $id);

        $usuarioModel = new UsuariosModel();
        $usuarioVo = $usuarioModel->loadById($connection, $_SESSION['usuarioCodigo']);

        $apontamentoModel = new ApontamentosModel();
        $apontamento = $apontamentoModel->verificaSeAberto($connection, "A", $id);
        
        $apontamentoController = new ApontamentosController();
        $mensagem = $apontamentoController->validarIniciarApontamento($atividadeVo, new ChamadosVo(), $apontamento, "A", $id);
        
        if (substr($mensagem, 0, 1) == 'S') {
            $situacaoModel = new SituacoesModel();
            $situacaoVo = $situacaoModel->loadById($connection, $_SESSION['situacaoCancelada']); // Cancelado

            $atividadeVo->setSituacao($situacaoVo);
            $atividadeModel->save($connection, $atividadeVo);
        }

        $dados = $this->carregarDadosManter($connection, $id, $mensagem);

        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
}