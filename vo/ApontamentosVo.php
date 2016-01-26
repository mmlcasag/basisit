<?php

class ApontamentosVo {
    
    private $codigo;
    private $usuario;
    private $atividade;
    private $chamado;
    private $dataInicio;
    private $dataFim;
    private $observacao;
    private $avaliacao;
    private $apontado;
    private $faturado;
    
    function __construct() {
        
    }
    
    public function setId($codigo) {
        $this->codigo = $codigo;
    }
    
    public function getId() {
        return $this->codigo;
    }
    
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }
    
    public function getUsuario() {
        return $this->usuario;
    }
    
    public function setAtividade($atividade) {
        $this->atividade = $atividade;
    }
    
    public function getAtividade() {
        return $this->atividade;
    }
    
    public function setChamado($chamado) {
        $this->chamado = $chamado;
    }
    
    public function getChamado() {
        return $this->chamado;
    }
    
    public function setDataInicio($dataInicio) {
        $this->dataInicio = $dataInicio;
    }
    
    public function getDataInicio() {
        return $this->dataInicio;
    }
    
    public function setDataFim($dataFim) {
        $this->dataFim = $dataFim;
    }
    
    public function getDataFim() {
        return $this->dataFim;
    }
    
    public function setObservacao($observacao) {
        $this->observacao = $observacao;
    }
    
    public function getObservacao() {
        return $this->observacao;
    }
    
    public function setAvaliacao($avaliacao) {
        $this->avaliacao = $avaliacao;
    }
    
    public function getAvaliacao() {
        return $this->avaliacao;
    }
    
    public function setApontado($apontado) {
        $this->apontado = $apontado;
    }
    
    public function getApontado() {
        return $this->apontado;
    }
    
    public function setFaturado($faturado) {
        $this->faturado = $faturado;
    }
    
    public function getFaturado() {
        return $this->faturado;
    }
    
}
