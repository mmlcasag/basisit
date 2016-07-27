<?php

class PerfisPermissoesController extends BaseController {
    
    function PerfisPermissoesController() {
        parent::__construct();
    }
    
    private function getParametroTela($parametro) {
        return filter_input(INPUT_POST, $parametro);
    }
    
    private function validarFormulario($vo) {
        if ((!Functions::isEmpty($vo->getId())) && (!is_numeric($vo->getId()))) {
            return 'N' . 'Valor para "ID" é inválido';
        } else if (Functions::isEmpty($vo->getPerfil()->getId())) {
            return 'N' . 'Informe o campo "Perfil"';
        } else if (!Functions::isInteger($vo->getPerfil()->getId())) {
            return 'N' . 'Valor para "Perfil" é inválido';
        } else if (Functions::isEmpty($vo->getTipoSistema()->getId())) {
            return 'N' . 'Informe o campo "Tipo de Sistema"';
        } else if (!Functions::isInteger($vo->getTipoSistema()->getId())) {
            return 'N' . 'Valor para "Tipo de Sistema" é inválido';
        } else if ((!Functions::isEmpty($vo->getSituacao())) && (!is_numeric($vo->getSituacao()))) {
            return 'N' . 'Valor para "Situação" é inválido';
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
        $model = new PerfisPermissoesModel();
        $model->save($connection, $vo);
    }
    
    private function excluirRegistro($connection, $id) {
        $model = new PerfisPermissoesModel();
        $vo = $model->loadById($connection, $id);
        $model->delete($connection, $vo);
    }
    
    private function carregarDadosListar($connection, $id, $mensagem = "", $descricao = "", $situacao = "") {
        $controller = new PerfisController();
        return $controller->carregarDadosManter($connection, $id, $mensagem, $descricao, $situacao);
    }
    
    private function exibirTelaListar($dados = array()) {
        $controller = new PerfisController();
        $controller->exibirTelaManter($dados);
    }
    
    private function carregarDadosManter($connection, $perfil, $id = "", $mensagem = "") {
        if (is_object($id)) {
            $vo = $id;
        } else if (!Functions::isEmpty($id)) {
            $model = new PerfisPermissoesModel();
            $vo = $model->loadById($connection, $id);
        } else {
            $vo = new PerfisPermissoesVo();
        }
        
        $perfisModel = new PerfisModel();
        $perfis = $perfisModel->load($connection);
        
        $tiposSistemasModel = new TiposSistemasModel();
        $tiposSistemas = $tiposSistemasModel->load($connection);
        
        return $this->trabalharDadosManter($perfil, $vo, $perfis, $tiposSistemas, $mensagem);
    }
    
    private function trabalharDadosManter($perfil, $vo, $perfis = array(), $tiposSistemas = array(), $mensagem = "") {
        return array( 'mensagem'      => $mensagem
                    , 'perfil'        => $perfil
                    , 'registro'      => $vo
                    , 'perfis'        => $perfis
                    , 'tiposSistemas' => $tiposSistemas );
    }
    
    private function exibirTelaManter($dados = array()) {
        $view = new View('views/manterPerfisPermissoes.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function listarAction() {
        $id        = $this->getParametroTela('id');
        $descricao = $this->getParametroTela('descricao');
        $situacao  = $this->getParametroTela('situacao');
        
        $connection = Databases::connect();
        $dados = $this->carregarDadosListar($connection, $id, "", $descricao, $situacao);
        Databases::disconnect($connection);
        
        $this->exibirTelaListar($dados);
    }
    
    public function cadastrarAction() {
        $id = $this->getParametroTela('id');

        $connection = Databases::connect();

        $perfilModel = new PerfisModel();
        $perfilVo = $perfilModel->loadById($connection, $this->getParametroTela('perfilCodigo'));

        $dados = $this->carregarDadosManter($connection, $perfilVo, $id);

        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
    
    public function salvarAction() {
        $connection = Databases::connect();

        $perfilModel = new PerfisModel();
        $perfilVo = $perfilModel->loadById($connection, $this->getParametroTela('perfil'));

        $tiposSistemasModel = new TiposSistemasModel();
        $tipoSistemaVo = $tiposSistemasModel->loadById($connection, $this->getParametroTela('tipoSistema'));

        $vo = new PerfisPermissoesVo();

        $vo->setId($this->getParametroTela('id'));
        $vo->setPerfil($perfilVo);
        $vo->setTipoSistema($tipoSistemaVo);
        $vo->setSituacao($this->getParametroTela('situacao'));

        $mensagem = $this->validarFormulario($vo);

        if (substr($mensagem, 0, 1) == 'S') {
            $connection = Databases::connect();
            $this->salvarRegistro($connection, $vo);
            $dados = $this->carregarDadosListar($connection, $perfilVo->getId(), $mensagem);
            Databases::disconnect($connection);

            $this->exibirTelaListar($dados);
        } else if (substr($mensagem, 0, 1) == 'N') {
            $dados = $this->carregarDadosManter($connection, $perfilVo, $vo, $mensagem);
            $this->exibirTelaManter($dados);
        }
    }
    
    public function excluirAction() {
        $id = $this->getParametroTela('id');

        $connection = Databases::connect();

        $mensagem = $this->validarExclusao($id);
        if (substr($mensagem, 0, 1) == 'S') {
            $this->excluirRegistro($connection, $id);
        }

        $dados = $this->carregarDadosListar($connection, $id, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaListar($dados);
    }
}