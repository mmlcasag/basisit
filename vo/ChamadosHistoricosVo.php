<?php

class ChamadosHistoricosVo {
    
    private $codigo;
    private $chamado;
    private $usuario;
    private $data;
    private $observacao;
    private $anexo;
    
    function __construct() {
        
    }
    
    public function setId($codigo) {
        $this->codigo = $codigo;
    }
    
    public function getId() {
        return $this->codigo;
    }
    
    public function setChamado($chamado) {
        $this->chamado = $chamado;
    }
    
    public function getChamado() {
        return $this->chamado;
    }
    
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }
    
    public function getUsuario() {
        return $this->usuario;
    }
    
    public function setData($data) {
        $this->data = $data;
    }
    
    public function getData() {
        return $this->data;
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
    
}