<?php

class TiposSistemasVo {
    
    private $codigo;
    private $descricao;
    private $nomeMenu;
    private $enderecoListar;
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
    
    public function setNomeMenu($nomeMenu) {
        $this->nomeMenu = $nomeMenu;
    }
    
    public function getNomeMenu() {
        return $this->nomeMenu;
    }
    
    public function setEnderecoListar($enderecoListar) {
        $this->enderecoListar = $enderecoListar;
    }
    
    public function getEnderecoListar() {
        return $this->enderecoListar;
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
