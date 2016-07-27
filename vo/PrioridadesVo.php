<?php

class PrioridadesVo {
    
    private $codigo;
    private $descricao;
    private $exibeImpacto;
    private $situacao;
    
    function __construct() {
        
    }
    
    public function setId($codigo) {
        $this->codigo = $codigo;
    }
    
    public function getId() {
        return $this->codigo;
    }
    
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }
    
    public function getDescricao() {
        return $this->descricao;
    }
    
    public function setExibeImpacto($exibeImpacto) {
        $this->exibeImpacto = $exibeImpacto;
    }
    
    public function getExibeImpacto() {
        return $this->exibeImpacto;
    }
    
    public function getExibeImpactoExtenso() {
        switch ($this->exibeImpacto) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function getExibeImpactoExtensoParam($exibeImpacto) {
        switch ($exibeImpacto) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function setSituacao($situacao) {
        $this->situacao = $situacao;
    }
    
    public function getSituacao() {
        return $this->situacao;
    }
    
    public function getSituacaoExtenso() {
        switch ($this->situacao) {
            case 1: return "Ativo";
            case 9: return "Inativo";
            default: return "";
        }
    }
    
    public function getSituacaoExtensoParam($situacao) {
        switch ($situacao) {
            case 1: return "Ativo";
            case 9: return "Inativo";
            default: return "";
        }
    }
    
}
