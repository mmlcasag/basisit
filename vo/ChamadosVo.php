<?php

class ChamadosVo {
    
    private $codigo;
    private $usuario;
    private $requisitante;
    private $atendente;
    private $data;
    private $situacao;
    private $empresa;
    private $categoria;
    private $tipoAmbiente;
    private $tipoProduto;
    private $modulo;
    private $prioridade;
    private $impacto;
    private $usuariosAfetados;
    private $areasAfetadas;
    private $previsaoTermino;
    private $assunto;
    private $observacao;
    private $qtdAvisosEmail;
    private $anexo;
    
    function __construct() {
        
    }
    
    public function setId($codigo) {
        $this->codigo = $codigo;
    }
    
    public function getId() {
        return $this->codigo;
    }
    
    public function setData($data) {
        $this->data = $data;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function setSituacao($situacao) {
        $this->situacao = $situacao;
    }
    
    public function getSituacao() {
        return $this->situacao;
    }
    
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }
    
    public function getUsuario() {
        return $this->usuario;
    }
    
    public function setRequisitante($requisitante) {
        $this->requisitante = $requisitante;
    }
    
    public function getRequisitante() {
        return $this->requisitante;
    }
    
    public function setAtendente($atendente) {
        $this->atendente = $atendente;
    }
    
    public function getAtendente() {
        return $this->atendente;
    }
    
    public function setEmpresa($empresa) {
        $this->empresa = $empresa;
    }
    
    public function getEmpresa() {
        return $this->empresa;
    }
    
    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }
    
    public function getCategoria() {
        return $this->categoria;
    }
    
    public function setTipoAmbiente($tipoAmbiente) {
        $this->tipoAmbiente = $tipoAmbiente;
    }
    
    public function getTipoAmbiente() {
        return $this->tipoAmbiente;
    }
    
    public function setTipoProduto($tipoProduto) {
        $this->tipoProduto = $tipoProduto;
    }
    
    public function getTipoProduto() {
        return $this->tipoProduto;
    }
    
    public function setModulo($modulo) {
        $this->modulo = $modulo;
    }
    
    public function getModulo() {
        return $this->modulo;
    }
    
    public function setPrioridade($prioridade) {
        $this->prioridade = $prioridade;
    }
    
    public function getPrioridade() {
        return $this->prioridade;
    }
    
    public function setImpacto($impacto) {
        $this->impacto = $impacto;
    }
    
    public function getImpacto() {
        return $this->impacto;
    }
    
    public function getImpactoExtenso() {
        switch ($this->impacto) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function getImpactoExtensoParam($impacto) {
        switch ($impacto) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function setUsuariosAfetados($usuariosAfetados) {
        $this->usuariosAfetados = $usuariosAfetados;
    }
    
    public function getUsuariosAfetados() {
        return $this->usuariosAfetados;
    }
    
    public function getUsuariosAfetadosExtenso() {
        switch ($this->usuariosAfetados) {
            case 0: return "Nenhum";
            case 1: return "1 - 10";
            case 2: return "11 - 50";
            case 3: return "51 - 200";
            case 4: return "mais de 200";
            default: return "";
        }
    }
    
    public function getUsuariosAfetadosExtensoParam($usuariosAfetados) {
        switch ($usuariosAfetados) {
            case 0: return "Nenhum";
            case 1: return "1 - 10";
            case 2: return "11 - 50";
            case 3: return "51 - 200";
            case 4: return "mais de 200";
            default: return "";
        }
    }
    
    public function setAreasAfetadas($areasAfetadas) {
        $this->areasAfetadas = $areasAfetadas;
    }
    
    public function getAreasAfetadas() {
        return $this->areasAfetadas;
    }
    
    public function getAreasAfetadasExtenso() {
        switch ($this->areasAfetadas) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function getAreasAfetadasExtensoParam($areasAfetadas) {
        switch ($areasAfetadas) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function setPrevisaoTermino($previsaoTermino) {
        $this->previsaoTermino = $previsaoTermino;
    }
    
    public function getPrevisaoTermino() {
        return $this->previsaoTermino;
    }
    
    public function getAssunto() {
        return $this->assunto;
    }
    
    public function setAssunto($assunto) {
        $this->assunto = $assunto;
    }
    
    public function setObservacao($observacao) {
        $this->observacao = $observacao;
    }
    
    public function getObservacao() {
        return $this->observacao;
    }
    
    public function setAnexo($anexo) {
        $this->anexo = $anexo;
    }
    
    public function getAnexo() {
        return $this->anexo;
    }
    
    public function setQtdAvisosEmail($qtdAvisosEmail) {
        $this->qtdAvisosEmail = $qtdAvisosEmail;
    }
    
    public function getQtdAvisosEmail() {
        return $this->qtdAvisosEmail;
    }
    
}