<?php

class PerfisPermissoesVo {
    
    private $codigo;
    private $perfil;
    private $tipoSistema;
    private $situacao;
    
    function __construct() {
        
    }
    
    public function setId($codigo) {
        $this->codigo = $codigo;
    }
    
    public function getId() {
        return $this->codigo;
    }
    
    public function setPerfil($perfil) {
        $this->perfil = $perfil;
    }
    
    public function getPerfil() {
        return $this->perfil;
    }
    
    public function setTipoSistema($tipoSistema) {
        $this->tipoSistema = $tipoSistema;
    }
    
    public function getTipoSistema() {
        return $this->tipoSistema;
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