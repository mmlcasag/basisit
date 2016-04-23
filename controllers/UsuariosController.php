<?php

class UsuariosController extends BaseController {
    
    function UsuariosController() {
        parent::__construct();
    }

    private function getParametroTela($parametro) {
        return filter_input(INPUT_POST, $parametro);
    }
    
    private function validarFormulario($vo) {
        if ((!Functions::isEmpty($vo->getId())) && (!is_numeric($vo->getId()))) {
            return 'N' . 'Valor para "ID" é inválido';
        } else if (Functions::isEmpty($vo->getNome())) {
            return 'N' . 'Informe o campo "Nome"';
        } else if (Functions::isEmpty($vo->getEmail())) {
            return 'N' . 'Informe o campo "E-mail"';
        } else if (!Functions::isEmail($vo->getEmail())) {
            return 'N' . 'Valor para "E-mail" é inválido';
        } else if (Functions::isEmpty($vo->getPerfil()->getId())) {
            return 'N' . 'Informe o campo "Perfil"';
        } else if (!Functions::isInteger($vo->getPerfil()->getId())) {
            return 'N' . 'Valor para "Perfil" é inválido';
        } else if ((!Functions::isEmpty($vo->getSituacao())) && (!is_numeric($vo->getSituacao()))) {
            return 'N' . 'Valor para "Situação" é inválido';
        } else {
            return 'S' . 'Operação realizada com sucesso';
        }
    }
    
    private function validarAlteracaoSenha($vo, $atual, $nova1, $nova2) {
        if ((Functions::isEmpty($atual)) && (Functions::isEmpty($nova1)) && (Functions::isEmpty($nova2))) {
            return 'S' . $vo->getSenha();
        } else if (Functions::isEmpty($atual)) {
            return 'N' . 'Informe o campo "Senha Atual"';
        } else if ($atual != $vo->getSenha()) {
            return 'N' . 'Valor informado em "Senha Atual" não coincide com senha atual';
        } else if (Functions::isEmpty($nova1)) {
            return 'N' . 'Informe o campo "Nova Senha"';
        } else if (Functions::isEmpty($nova2)) {
            return 'N' . 'Informe o campo "Repita a Nova Senha"';
        } else if ($nova1 != $nova2) {
            return 'N' . 'Valor informado em "Repita a Nova Senha" é diferente de "Nova Senha"';
        } else {
            return 'S' . $nova1;
        }
    }
    
    private function validarExclusao($id = "") {
        if (Functions::isEmpty($id)) {
            return 'N' . 'Informe o campo "ID"';
        } else if (!Functions::isInteger($id)) {
            return 'N' . 'Valor para "ID" é inválido';
        } else {
            return 'S' . 'Operação realizada com sucesso';
        }
    }
    
    private function salvarRegistro($connection, $vo) {
        $model = new UsuariosModel();
        $model->save($connection, $vo);
    }
    
    private function excluirRegistro($connection, $id) {
        $model = new UsuariosModel();
        $vo = $model->loadById($connection, $id);
        $model->delete($connection, $vo);
    }
    
    private function carregarDadosListar($connection, $mensagem = "") {
        $model = new UsuariosModel();
        $registros = $model->load($connection, 0);
        return $this->trabalharDadosListar($registros, $mensagem);
    }
    
    private function trabalharDadosListar($registros = array(), $mensagem = "") {
        return array( 'mensagem'  => $mensagem
                    , 'registros' => $registros );
    }
    
    private function exibirTelaListar($dados) {
        $view = new View('views/listarUsuarios.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function carregarDadosManter($connection, $id = "", $mensagem = "") {
        // parametro $id pode ser tanto UsuarioVo como usuarioCodigo
        if (is_object($id)) {
            $usuario = $id;
        } else if (!Functions::isEmpty($id)) {
            $model = new UsuariosModel();
            $usuario = $model->loadById($connection, $id);
        } else {
            $usuario = new UsuariosVo();
        }
        
        $perfisModel = new PerfisModel();
        $perfis = $perfisModel->load($connection);
        
        $empresasModel = new EmpresasModel();
        $empresas = $empresasModel->load($connection);
        
        return $this->trabalharDadosManter($usuario, $perfis, $empresas, $mensagem);
    }
    
    private function trabalharDadosManter($usuario, $perfis = array(), $empresas = array(), $mensagem = "") {
        return array( 'mensagem' => $mensagem
                    , 'usuario'  => $usuario 
                    , 'perfis'   => $perfis
                    , 'empresas' => $empresas );
    }
    
    private function exibirTelaManter($dados) {
        $view = new View('views/manterUsuarios.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function trabalharDadosAtualizar($usuario, $mensagem = "") {
        return array( 'mensagem' => $mensagem
                    , 'usuario'  => $usuario );
    }
    
    private function exibirTelaAtualizar($dados) {
        $view = new View('views/manterUsuarioProprio.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function exibirTelaInicial() {
        $login = new IndexController();
        $login->indexAction();
    }
    
    public function listarAction($mensagem = "") {
        $connection = Databases::connect();
        $dados = $this->carregarDadosListar($connection, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaListar($dados);
    }
    
    public function cadastrarAction() {
        $id = $this->getParametroTela('id');

        $connection = Databases::connect();
        $dados = $this->carregarDadosManter($connection, $id);
        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
    
    public function salvarCadastrarAction() {
        $connection = Databases::connect();
        
        $empresaModel = new EmpresasModel();
        $empresaVo = $empresaModel->loadById($connection, $this->getParametroTela('empresa'));
        
        $perfilModel = new PerfisModel();
        $perfilVo = $perfilModel->loadById($connection, $this->getParametroTela('perfil'));
        
        $vo = new UsuariosVo();
        
        $vo->setId($this->getParametroTela('id'));
        $vo->setNome($this->getParametroTela('nome'));
        $vo->setEmpresa($empresaVo);
        $vo->setSetor($this->getParametroTela('setor'));
        $vo->setPerfil($perfilVo);
        $vo->setFoneComercial($this->getParametroTela('foneComercial'));
        $vo->setFoneCelular($this->getParametroTela('foneCelular'));
        $vo->setEmail($this->getParametroTela('email'));
        $vo->setSenha($this->getParametroTela('senha'));
        $vo->setSituacao($this->getParametroTela('situacao'));
        $vo->setObservacao($this->getParametroTela('observacao'));
        
        // Se campo senha não for preenchido, mantém senha atual do usuário
        if (Functions::isEmpty($this->getParametroTela('senha'))) {
            $model = new UsuariosModel();
            $oldVo = $model->loadById($connection, $this->getParametroTela('id'));
            
            $vo->setSenha($oldVo->getSenha());
        }
        
        $mensagem = $this->validarFormulario($vo);
        if (substr($mensagem, 0, 1) == 'S') {
            $this->salvarRegistro($connection, $vo);
            $dados = $this->carregarDadosListar($connection, $mensagem);
            Databases::disconnect($connection);

            $this->exibirTelaListar($dados);
        } else if (substr($mensagem, 0, 1) == 'N') {
            $dados = $this->carregarDadosManter($connection, $vo, $mensagem);
            Databases::disconnect($connection);

            $this->exibirTelaManter($dados);
        }
    }
    
    public function atualizarAction() {
        $id = $this->getParametroTela('id');

        $connection = Databases::connect();
        $dados = $this->carregarDadosManter($connection, $id);
        Databases::disconnect($connection);

        $this->exibirTelaAtualizar($dados);
    }
    
    public function salvarAtualizarAction() {
        $connection = Databases::connect();

        $usuario = new UsuariosModel();
        $vo = $usuario->loadById($connection, $this->getParametroTela('id'));

        $vo->setNome($this->getParametroTela('nome'));
        $vo->setFoneComercial($this->getParametroTela('foneComercial'));
        $vo->setFoneCelular($this->getParametroTela('foneCelular'));
        $vo->setEmail($this->getParametroTela('email'));

        $mensagem = $this->validarAlteracaoSenha($vo, $this->getParametroTela('senhaAtual'), $this->getParametroTela('senhaNova1'), $this->getParametroTela('senhaNova2'));
        if (substr($mensagem, 0, 1) == 'S') {
            $vo->setSenha(substr($mensagem, 1, strlen($mensagem) - 1));

            $mensagem = $this->validarFormulario($vo);
            if (substr($mensagem, 0, 1) == 'S') {
                $this->salvarRegistro($connection, $vo);
                Databases::disconnect($connection);

                $this->exibirTelaInicial();
            } else if (substr($mensagem, 0, 1) == 'N') {
                Databases::disconnect($connection);
                $dados = $this->trabalharDadosAtualizar($vo, $mensagem);
                $this->exibirTelaAtualizar($dados);
            }
        } else if (substr($mensagem, 0, 1) == 'N') {
            Databases::disconnect($connection);
            $dados = $this->trabalharDadosAtualizar($vo, $mensagem);
            $this->exibirTelaAtualizar($dados);
        }
    }
    
    public function excluirAction() {
        $id = $this->getParametroTela('id');

        $connection = Databases::connect();

        $mensagem = $this->validarExclusao($id);
        if (substr($mensagem, 0, 1) == 'S') {
            $this->excluirRegistro($connection, $id);
        }

        $dados = $this->carregarDadosListar($connection, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaListar($dados);
    }
}