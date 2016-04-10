<?php

class TiposApontamentosModel {
    
    private function populateVo($connection, $row) {
        $vo = new TiposApontamentosVo();
        
        $vo->setId($row['id']);
        $vo->setDescricao($row['descricao']);
        
        return $vo;
    }
    
    public function load($connection) {
        $registros = array();
        
        $row = array('id' => '1', 'descricao' => 'Atividades e Chamados');
        $vo = $this->populateVo($connection, $row);
        array_push($registros, $vo);
        
        $row = array('id' => '2', 'descricao' => 'Apenas Chamados');
        $vo = $this->populateVo($connection, $row);
        array_push($registros, $vo);
        
        $row = array('id' => '3', 'descricao' => 'Apenas Atividades');
        $vo = $this->populateVo($connection, $row);
        array_push($registros, $vo);
        
        return $registros;
    }
    
}