<?php

class IndexController {
    
    public function indexAction() {
        $index = new IndexController();
        
        if (!$index->verificarSessaoAtiva()) {
            $index->expirarSessao();
        } else {
            $index->atualizarSessao();
            
            $connection = Databases::connect();
            $dados = $this->carregarDadosListar($connection);
            Databases::disconnect($connection);

            $this->exibirTelaListar($dados);
        }
    }
    
    public function loginAction() {
        $connection = Databases::connect();
        
        $email = $this->getParametroTela('email');
        $senha = $this->getParametroTela('senha');
        
        $mensagem = $this->validarFormulario($connection, $email, $senha);
        
        if (substr($mensagem, 0, 1) == 'S') {
            $usuario = $this->efetuarLogin($connection, $email, $senha);
            $this->iniciarSessaoUsuario($usuario);
            $dados = $this->carregarDadosListar($connection);
            Databases::disconnect($connection);
            $this->exibirTelaListar($dados);
        } else if (substr($mensagem, 0, 1) == 'N') {
            Databases::disconnect($connection);
            $dados = $this->trabalharDadosLogin($mensagem);
            $this->exibirTelaLogin($dados);
        }
    }
    
    public function logoutAction() {
        $this->finalizarSessaoUsuario();
        $this->exibirTelaLogin();
    }
    
    private function carregarDadosListar($connection) {
        $model = new TiposSistemasModel();
        $registros = $model->loadByPerfil($connection, $_SESSION['perfilCodigo']);
        
        $chamadosModel = new ChamadosModel();
        $naoClassificados = $chamadosModel->loadNaoClassificados($connection);
        
        $usuariosModel = new UsuariosModel();
        $meusRegistros = $usuariosModel->loadMeusRegistros($connection, $_SESSION['usuarioCodigo']);
        $resumoGeral = $usuariosModel->loadResumoGeral($connection);
        
        return $this->trabalharDadosListar($registros, count($naoClassificados), $meusRegistros, $resumoGeral);
    }
    
    private function trabalharDadosListar($registros = array(), $naoClassificados = 0, $meusRegistros = array(), $resumoGeral = array()) {
        return array('registros' => $registros,
                     'naoClassificados' => $naoClassificados,
                     'meusRegistros' => $meusRegistros,
                     'resumoGeral' => $resumoGeral);
    }
    
    private function exibirTelaListar($dados = array()) {
        $view = new View('views/showFeatures.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function trabalharDadosLogin($mensagem = "") {
        return array('mensagem' => $mensagem);
    }
    
    public function exibirTelaLogin($dados = array()) {
        $view = new View('views/login.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function efetuarLogin($connection, $email, $senha) {
        $model = new UsuariosModel();
        return $model->loadByUserAndPassword($connection, $email, $senha);
    }
    
    private function validarFormulario($connection, $email, $senha) {
        if (Functions::isEmpty($email)) {
            return 'N' . 'Informe o campo "E-mail"';
        } else if ((!Functions::isEmpty($email)) && (!Functions::isEmail($email))) {
            return 'N' . 'Valor para "E-mail" é inválido';
        } else if (Functions::isEmpty($senha)) {
            return 'N' . 'Informe o campo "Senha"';
        } else {
            $vo = $this->efetuarLogin($connection, $email, $senha);
            if (Functions::isEmpty($vo->getId())) {
                return 'N' . 'Credenciais de acesso inválidas';
            } else {
                return 'S' . 'Operação realizada com sucesso';
            }
        }
    }
    
    private function iniciarSessaoUsuario($usuario) {
        $_SESSION['last_activity']       = time();
        $_SESSION['life_expectancy']     = Functions::getParametro('sessão');
        
        $_SESSION['usuarioCodigo']       = $usuario->getId();
        $_SESSION['usuarioNome']         = $usuario->getNome();
        
        $_SESSION['empresaCodigo']       = $usuario->getEmpresa()->getId();
        $_SESSION['empresaDescricao']    = $usuario->getEmpresa()->getDescricao();
        
        $_SESSION['perfilCodigo']        = $usuario->getPerfil()->getId();
        $_SESSION['perfilDescricao']     = $usuario->getPerfil()->getDescricao();
        $_SESSION['perfilAdministrador'] = $usuario->getPerfil()->getAdministrador();
        $_SESSION['perfilFuncionario']   = $usuario->getPerfil()->getFuncionario();
        $_SESSION['perfilCliente']       = $usuario->getPerfil()->getCliente();
        
        $_SESSION['situacaoEmAndamento'] = 2;
        $_SESSION['situacaoFinalizada']  = 3;
        $_SESSION['situacaoCancelada']   = 9;
    }
    
    public function verificarSessaoAtiva() {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > 60 * Functions::getParametro('sessão')) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    
    public function atualizarSessao() {
        $_SESSION['last_activity'] = time(); // update last activity time stamp
    }
    
    public function expirarSessao() {
        $this->logoutAction();
    }
    
    private function finalizarSessaoUsuario() {
        session_unset();
        session_destroy();
    }
    
    private function getParametroTela($parametro) {
        return filter_input(INPUT_POST, $parametro);
    }
    
}