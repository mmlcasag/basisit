<?php

class AtividadesVo {
    
    private $codigo;
    private $usuario;
    private $data;
    private $empresa;
    private $tipoAtividade;
    private $situacao;
    private $observacao;
    
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
    
    public function setEmpresa($empresa) {
        $this->empresa = $empresa;
    }
    
    public function getEmpresa() {
        return $this->empresa;
    }
    
    public function setTipoAtividade($tipoAtividade) {
        $this->tipoAtividade = $tipoAtividade;
    }
    
    public function getTipoAtividade() {
        return $this->tipoAtividade;
    }
    
    public function setObservacao($observacao) {
        $this->observacao = $observacao;
    }
    
    public function getObservacao() {
        return $this->observacao;
    }
    
}