<?php

class UsuariosVo {
    
    private $codigo;
    private $nome;
    private $empresa;
    private $setor;
    private $perfil;
    private $foneComercial;
    private $foneCelular;
    private $email;
    private $senha;
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
    
    public function setNome($nome) {
        $this->nome = $nome;
    }
    
    public function getNome() {
        return $this->nome;
    }
    
    public function setEmpresa($empresa) {
        $this->empresa = $empresa;
    }
    
    public function getEmpresa() {
        return $this->empresa;
    }
    
    public function setSetor($setor) {
        $this->setor = $setor;
    }
    
    public function getSetor() {
        return $this->setor;
    }
    
    public function setPerfil($perfil) {
        $this->perfil = $perfil;
    }
    
    public function getPerfil() {
        return $this->perfil;
    }
    
    public function setFoneComercial($foneComercial) {
        $this->foneComercial = $foneComercial;
    }
    
    public function getFoneComercial() {
        return $this->foneComercial;
    }
    
    public function setFoneCelular($foneCelular) {
        $this->foneCelular = $foneCelular;
    }
    
    public function getFoneCelular() {
        return $this->foneCelular;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setSenha($senha) {
        $this->senha = $senha;
    }
    
    public function getSenha() {
        return $this->senha;
    }
    
    public function setSituacao($situacao) {
        $this->situacao = $situacao;
    }
    
    public function getSituacao() {
        return $this->situacao;
    }
    
    public function setObservacao($observacao) {
        $this->observacao = $observacao;
    }
    
    public function getObservacao() {
        return $this->observacao;
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
