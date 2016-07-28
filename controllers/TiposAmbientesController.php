<?php

class TiposAmbientesController extends BaseController {
    
    function TiposAmbientesController() {
        parent::__construct();
    }
    
    private function getParametroTela($parametro) {
        return filter_input(INPUT_POST, $parametro);
    }
    
    private function validarFormulario($vo) {
        if ((!Functions::isEmpty($vo->getId())) && (!is_numeric($vo->getId()))) {
            return 'N' . 'Valor para "ID" é inválido';
        } else if (Functions::isEmpty($vo->getDescricao())) {
            return 'N' . 'Informe o campo "Descrição"';
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
        $model = new TiposAmbientesModel();
        $model->save($connection, $vo);
    }
    
    private function excluirRegistro($connection, $id) {
        $model = new TiposAmbientesModel();
        $vo = $model->loadById($connection, $id);
        $model->delete($connection, $vo);
    }
    
    private function carregarDadosListar($connection, $mensagem = "", $descricao = "", $situacao = "") {
        $model = new TiposAmbientesModel();
        $registros = $model->load($connection, $descricao, $situacao);
        
        return $this->trabalharDadosListar($registros, $mensagem, $descricao, $situacao);
    }
    
    private function trabalharDadosListar($registros = array(), $mensagem = "", $descricao = "", $situacao = "") {
        return array( 'mensagem'  => $mensagem
                    , 'descricao' => $descricao
                    , 'situacao'  => $situacao
                    , 'registros' => $registros );
    }
    
    private function exibirTelaListar($dados = array()) {
        $view = new View('views/listarTiposAmbientes.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function carregarDadosManter($connection, $id = "", $mensagem = "") {
        if (!Functions::isEmpty($id)) {
            $model = new TiposAmbientesModel();
            $tiposAmbientes = $model->loadById($connection, $id);
        } else {
            $tiposAmbientes = new TiposAmbientesVo();
        }
        return $this->trabalharDadosManter($tiposAmbientes, $mensagem);
    }
    
    private function trabalharDadosManter($tiposAmbientes, $mensagem = "") {
        return array( 'mensagem' => $mensagem
                    , 'registro' => $tiposAmbientes );
    }
    
    private function exibirTelaManter($dados = array()) {
        $view = new View('views/manterTiposAmbientes.phtml');
        $view->setParams($dados);
        $view->showContents();
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
    
    public function salvarAction() {
        $vo = new TiposAmbientesVo();

        $vo->setId($this->getParametroTela('id'));
        $vo->setDescricao($this->getParametroTela('descricao'));
        $vo->setSituacao($this->getParametroTela('situacao'));

        $mensagem = $this->validarFormulario($vo);

        if (substr($mensagem, 0, 1) == 'S') {
            $connection = Databases::connect();
            $this->salvarRegistro($connection, $vo);
            $dados = $this->carregarDadosListar($connection, $mensagem);
            Databases::disconnect($connection);

            $this->exibirTelaListar($dados);
        } else if (substr($mensagem, 0, 1) == 'N') {
            $dados = $this->trabalharDadosManter($vo, $mensagem);
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

        $dados = $this->carregarDadosListar($connection, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaListar($dados);
    }
}