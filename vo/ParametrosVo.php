<?php

class ParametrosVo {
    
    private $codigo;
    private $nome;
    private $valor;
    private $situacao;
    
    function __construct() {
        
    }
    
    public function setId($codigo) {
        $this->codigo = $codigo;
    }
    
    public function getId() {
        return $this->codigo;
    }
    
    public function setNome($nome) {
        $this->nome = $nome;
    }
    
    public function getNome() {
        return $this->nome;
    }
    
    public function setValor($valor) {
        $this->valor = $valor;
    }
    
    public function getValor() {
        return $this->valor;
    }
    
    public function setSituacao($situacao) {
        $this->situacao = $situacao;
    }
    
    public function getSituacao() {
        return $this->situacao;
    }
    
    public function getSituacaoExtenso() {
        switch ($this->situacao) {
            case 0: return "Ativo";
            case 1: return "Inativo";
            default: return "";
        }
    }
    
    public function getSituacaoExtensoParam($situacao) {
        switch ($situacao) {
            case 0: return "Ativo";
            case 1: return "Inativo";
            default: return "";
        }
    }
    
}
