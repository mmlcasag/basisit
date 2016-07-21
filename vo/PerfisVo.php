<?php

class PerfisVo {
    
    private $codigo;
    private $descricao;
    private $administrador;
    private $funcionario;
    private $cliente;
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
    
    public function setAdministrador($administrador) {
        $this->administrador = $administrador;
    }
    
    public function getAdministrador() {
        return $this->administrador;
    }
    
    public function getAdministradorExtenso() {
        switch ($this->administrador) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function getAdministradorExtensoParam($administrador) {
        switch ($administrador) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function setFuncionario($funcionario) {
        $this->funcionario = $funcionario;
    }
    
    public function getFuncionario() {
        return $this->funcionario;
    }
    
    public function getFuncionarioExtenso() {
        switch ($this->funcionario) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function getFuncionarioExtensoParam($funcionario) {
        switch ($funcionario) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function setCliente($cliente) {
        $this->cliente = $cliente;
    }
    
    public function getCliente() {
        return $this->cliente;
    }
    
    public function getClienteExtenso() {
        switch ($this->cliente) {
            case 0: return "Não";
            case 1: return "Sim";
            default: return "";
        }
    }
    
    public function getClienteExtensoParam($cliente) {
        switch ($cliente) {
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