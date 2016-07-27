<?php

class ModulosVo {
    
    private $codigo;
    private $descricao;
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
