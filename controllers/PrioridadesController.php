<?php

class PrioridadesController extends BaseController {
    
    function PrioridadesController() {
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
        } else if ((!Functions::isEmpty($vo->getExibeImpacto())) && (!is_numeric($vo->getExibeImpacto()))) {
            return 'N' . 'Valor para "Exibe Impacto" é inválido';
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
        $model = new PrioridadesModel();
        $model->save($connection, $vo);
    }
    
    private function excluirRegistro($connection, $id) {
        $model = new PrioridadesModel();
        $vo = $model->loadById($connection, $id);
        $model->delete($connection, $vo);
    }
    
    private function carregarDadosListar($connection, $mensagem = "") {
        $model = new PrioridadesModel();
        $registros = $model->load($connection);
        return $this->trabalharDadosListar($registros, $mensagem);
    }
    
    private function trabalharDadosListar($registros = array(), $mensagem = "") {
        return array( 'mensagem'  => $mensagem
                    , 'registros' => $registros );
    }
    
    private function exibirTelaListar($dados = array()) {
        $view = new View('views/listarPrioridades.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    private function carregarDadosManter($connection, $id = "", $mensagem = "") {
        if (!Functions::isEmpty($id)) {
            $model = new PrioridadesModel();
            $prioridades = $model->loadById($connection, $id);
        } else {
            $prioridades = new PrioridadesVo();
        }
        return $this->trabalharDadosManter($prioridades, $mensagem);
    }
    
    private function trabalharDadosManter($prioridades, $mensagem = "") {
        return array( 'mensagem' => $mensagem
                    , 'registro' => $prioridades );
    }
    
    private function exibirTelaManter($dados = array()) {
        $view = new View('views/manterPrioridades.phtml');
        $view->setParams($dados);
        $view->showContents();
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
    
    public function salvarAction() {
        $vo = new PrioridadesVo();

        $vo->setId($this->getParametroTela('id'));
        $vo->setDescricao($this->getParametroTela('descricao'));
        $vo->setExibeImpacto($this->getParametroTela('exibeImpacto'));
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
    
    public function ajaxExibeImpactoAction() {
        $prioridade = $this->getParametroTela('prioridade');
        $impacto = $this->getParametroTela('impacto');
        $exibeAberto = $this->getParametroTela('exibeAberto');
        $usuariosAfetados = $this->getParametroTela('usuariosAfetados');
        $areasAfetadas = $this->getParametroTela('areasAfetadas');

        $connection = Databases::connect();
        $prioridadeModel = new PrioridadesModel();
        $prioridadeVo = $prioridadeModel->loadById($connection, $prioridade);
        Databases::disconnect($connection);

        if ($prioridadeVo->getExibeImpacto() == 1) {
            echo '<label class="control-label col-sm-2" for="impacto">Impacto:</label>
                  <div class="col-sm-3">
                    <label class="radio-inline"><input type="radio" id="impactoNao" name="impacto" value="0"' . ( ($exibeAberto == 1) ? '' : ' disabled="disabled" ' ) . (( $impacto == 0 ) ? ' checked="checked" ' : '' ) . ' onchange="alteraImpacto(' . $exibeAberto . ',' . $usuariosAfetados . ',' . $areasAfetadas . ');">' . $prioridadeVo->getExibeImpactoExtensoParam(0) . '</label>
                    <label class="radio-inline"><input type="radio" id="impactoSim" name="impacto" value="1"' . ( ($exibeAberto == 1) ? '' : ' disabled="disabled" ' ) . (( $impacto == 1 ) ? ' checked="checked" ' : '' ) . ' onchange="alteraImpacto(' . $exibeAberto . ',' . $usuariosAfetados . ',' . $areasAfetadas . ');">' . $prioridadeVo->getExibeImpactoExtensoParam(1) . '</label>
                  </div>';
        } else {
            echo '<input type="hidden" id="impacto" name="impacto" value="0" />';
        }
    }
    
    public function ajaxExibePrevisaoTerminoAction() {
        $abertura = date('Y-m-d');

        // Verifica em qual dia da semana caiu
        $diadasemana = date('w', strtotime($abertura));

        switch ($diadasemana) {
            case  4: $termino = 4; break; // Quando dia de abertura do chamdo cair numa quinta
            case  5: $termino = 4; break; // Quando dia de abertura do chamdo cair numa sexta
            case  6: $termino = 3; break; // Quando dia de abertura do chamdo cair num  sábado
            default: $termino = 2; break; // Dois dias é o prazo padrão para atendimento do chamado
        }

        // Adiciona os dias
        $termino = date('Y-m-d', strtotime("+". $termino ." days"));

        echo Functions::toDate($termino);
    }
}