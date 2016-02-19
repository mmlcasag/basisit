<?php

class TiposRelatoriosModel {
    
    private function populateVo($connection, $row) {
        $vo = new TiposRelatoriosVo();
        
        $vo->setId($row['id']);
        $vo->setDescricao($row['descricao']);
        
        return $vo;
    }
    
    public function load($connection) {
        $registros = array();
        
        $row = array('id' => 'S', 'descricao' => 'Sintético');
        $vo = $this->populateVo($connection, $row);
        array_push($registros, $vo);
        
        $row = array('id' => 'A', 'descricao' => 'Analítico');
        $vo = $this->populateVo($connection, $row);
        array_push($registros, $vo);
        
        return $registros;
    }
    
}