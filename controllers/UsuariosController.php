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
    
    private function carregarDadosListar($connection, $mensagem = "", $descricao = "", $situacao = "") {
        $model = new UsuariosModel();
        $registros = $model->load($connection, 0, $descricao, $situacao);
        
        return $this->trabalharDadosListar($registros, $mensagem, $descricao, $situacao);
    }
    
    private function trabalharDadosListar($registros = array(), $mensagem = "", $descricao = "", $situacao = "") {
        return array( 'mensagem'  => $mensagem
                    , 'descricao' => $descricao
                    , 'situacao'  => $situacao
                    , 'registros' => $registros );
    }
    
    private function exibirTelaListar($dados = array()) {
        $view = new View('views/listarUsuarios.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function carregarDadosManter($connection, $id = "", $mensagem = "") {
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
        $descricao = $this->getParametroTela('descricao');
        $situacao  = $this->getParametroTela('situacao');
        
        $connection = Databases::connect();
            $dados = $this->carregarDadosListar($connection, $mensagem, $descricao, $situacao);
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
    
    public function ajaxLoadClientesDeUmaEmpresaAction() {
        $empresaCodigo = $this->getParametroTela('empresaCodigo');
        $exibeUsuarioAberto = $this->getParametroTela('exibeUsuarioAberto');
        $usuarioCodigo = $this->getParametroTela('usuarioCodigo');
        
        $connection = Databases::connect();
        $model = new UsuariosModel();
        $usuarioNome = $model->loadById($connection, $usuarioCodigo);
        $clientes = $model->loadClientesDeUmaEmpresa($connection, 0, $empresaCodigo);
        Databases::disconnect($connection);
        
        $resultado = "";
        
        if ($exibeUsuarioAberto == 1) {
            $resultado = '
                <label class="control-label col-sm-2" for="usuario">Usuário:</label>
                <div class="col-sm-3">
                    <div class="input-group">   
                        <div class="input-group-addon" data-toggle="modal" data-target="#usuarioModal">
                            <span class="glyphicon glyphicon-info-sign"></span> 
                        </div>
                        <select class="form-control" id="usuario" name="usuario" onchange="atualizarUsuarioModal()">
                            <option value="">Selecione</option>';
            foreach($clientes as $cliente) {
                $resultado .= '<option value="' . $cliente->getId() . '">' . $cliente->getNome() . '</option>';
            }
            $resultado .= '</select></div></div>';
        } else {
            $resultado = '
                <label class="control-label col-sm-2" for="usuario">Usuário:</label>
                <div class="col-sm-3">
                    <div class="input-group">   
                        <div class="input-group-addon" data-toggle="modal" data-target="#usuarioModal">
                            <span class="glyphicon glyphicon-info-sign"></span> 
                        </div>
                        <input type="hidden" name="usuario" value="' . $usuarioCodigo . '" />
                        <input type="text" class="form-control" id="usuario" name="usuario" value="' . $usuarioNome . '" disabled="disabled" />
                    </div>
                </div>';
        }
        
        echo $resultado;
    }
    
    public function ajaxExibeContatosUsuarioAction() {
        $usuarioCodigo = $this->getParametroTela('usuarioCodigo');
        
        $connection = Databases::connect();
        $usuarioModel = new UsuariosModel();
        $usuario = $usuarioModel->loadById($connection, $usuarioCodigo);
        Databases::disconnect($connection);
        
        if (Functions::isEmpty($usuarioCodigo)) {
            $resultado = '
            <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Contatos de Usuário</h4>
                  </div>
                  <div class="modal-body">
                    <p>
                      Nenhum usuário foi selecionado.
                    <br />
                      Selecione um usuário para visualizar seus contatos.
                    </p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                  </div>
                </div>
            </div>';
        } else {
            $resultado = '
            <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Contatos de ' . $usuario->getNome() . '</h4>
                  </div>
                  <div class="modal-body">
                    <p>
                      <span class="glyphicon glyphicon-envelope"></span>&nbsp;&nbsp;' . $usuario->getEmail() . '<br />
                      <span class="glyphicon glyphicon-phone-alt"></span>&nbsp;&nbsp;' . $usuario->getFoneComercial() . '<br />
                      <span class="glyphicon glyphicon-phone"></span>&nbsp;&nbsp;' . $usuario->getFoneCelular() . '<br />
                    </p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                  </div>
                </div>
            </div>';
        }
        
        echo $resultado;
    }
}