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
    
    private function carregarDadosListar($connection, $mensagem = "", $descricao = "", $situacao = "") {
        $model = new PrioridadesModel();
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
        $prioridade   = $this->getParametroTela('prioridade');
        $tipoAmbiente = $this->getParametroTela('tipoAmbiente');
        
        // Desenvolvimento
        if ($tipoAmbiente == Functions::getParametro('AmbienteDesenvolvimentoID')) {
            // Baixa
            if ($prioridade == Functions::getParametro('PrioridadeBaixaID')) {
                $prazo = 5;
            // Média
            } else if ($prioridade == Functions::getParametro('PrioridadeMediaID')) {
                $prazo = 4;
            // Alta
            } else if ($prioridade == Functions::getParametro('PrioridadeAltaID')) {
                $prazo = 2;
            // Urgente
            } else if ($prioridade == Functions::getParametro('PrioridadeUrgenteID')) {
                $prazo = 1;
            } else {
                $prazo = 7;
            }
        // Qualidade
        } else if ($tipoAmbiente == Functions::getParametro('AmbienteQualidadeID')) {
            // Baixa
            if ($prioridade == Functions::getParametro('PrioridadeBaixaID')) {
                $prazo = 5;
            // Média
            } else if ($prioridade == Functions::getParametro('PrioridadeMediaID')) {
                $prazo = 4;
            // Alta
            } else if ($prioridade == Functions::getParametro('PrioridadeAltaID')) {
                $prazo = 2;
            // Urgente
            } else if ($prioridade == Functions::getParametro('PrioridadeUrgenteID')) {
                $prazo = 1;
            } else {
                $prazo = 7;
            }
        // Produção
        } else if ($tipoAmbiente == Functions::getParametro('AmbienteProducaoID')) {
            // Baixa
            if ($prioridade == Functions::getParametro('PrioridadeBaixaID')) {
                $prazo = 4;
            // Média
            } else if ($prioridade == Functions::getParametro('PrioridadeMediaID')) {
                $prazo = 3;
            // Alta
            } else if ($prioridade == Functions::getParametro('PrioridadeAltaID')) {
                $prazo = 2;
            // Urgente
            } else if ($prioridade == Functions::getParametro('PrioridadeUrgenteID')) {
                $prazo = 1;
            } else {
                $prazo = 7;
            }
        } else {
            $prazo = 10;
        }
        
        $abertura = date('Y-m-d', strtotime("+". $prazo ." days"));
        
        // Verifica em qual dia da semana caiu
        $diadasemana = date('w', strtotime($abertura));
        
        switch ($diadasemana) {
            case  0: $prazo = $prazo + 1; break; // Quando dia de abertura do chamdo cair num  domingo
            case  6: $prazo = $prazo + 2; break; // Quando dia de abertura do chamdo cair num  sábado
            default: $prazo = $prazo + 0; break; // Dois dias é o prazo padrão para atendimento do chamado
        }
	
	// Adiciona os dias
        $termino = date('Y-m-d', strtotime("+". $prazo ." days"));
        
        echo Functions::toDate($termino);
    }
}