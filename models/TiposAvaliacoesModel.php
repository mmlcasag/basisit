<?php

class TiposAvaliacoesModel {
    
    private function populateVo($connection, $row) {
        $vo = new TiposAvaliacoesVo();
        
        $vo->setId($row['id']);
        $vo->setDescricao($row['descricao']);
        
        return $vo;
    }
    
    public function load($connection) {
        $registros = array();
        
        $row = array('id' => '1', 'descricao' => 'Faturados e Não Faturados');
        $vo = $this->populateVo($connection, $row);
        array_push($registros, $vo);
        
        $row = array('id' => '2', 'descricao' => 'Apenas Faturados');
        $vo = $this->populateVo($connection, $row);
        array_push($registros, $vo);
        
        $row = array('id' => '3', 'descricao' => 'Apenas Não Faturados');
        $vo = $this->populateVo($connection, $row);
        array_push($registros, $vo);
        
        return $registros;
    }
    
}