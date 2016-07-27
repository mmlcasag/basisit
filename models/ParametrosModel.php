<?php

class ParametrosModel {
    
    private function populateVo($connection, $row) {
        $vo = new ParametrosVo();
        
        $vo->setId($row->par_cdiparametro);
        $vo->setNome($row->par_dssnome);
        $vo->setValor($row->par_dssvalor);
        $vo->setSituacao($row->par_opldesativado);
        
        return $vo;
    }
    
    public function load($connection, $descricao = "", $situacao = "") {
        if (Functions::isEmpty($situacao)) {
            $situacao = 1;
        }
        
        $registros = array();
        
        $query = " SELECT * 
                   FROM   parametros 
                   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($descricao)) {
            $query .= " AND LOWER(par_dssnome) LIKE :par_dssnome ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND par_opldesativado = :par_opldesativado ";
        }
        
        $query .= " ORDER  BY par_dssnome ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($descricao)) {
            $descricao = "%" . strtolower($descricao) . "%";
            $stmt->bindParam(':par_dssnome', $descricao);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':par_opldesativado', $situacao);
        }
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $vo = $this->populateVo($connection, $row);
            
            array_push($registros, $vo);
        }
        
        return $registros;
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT * 
		           FROM   parametros
		           WHERE  par_cdiparametro = :par_cdiparametro ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':par_cdiparametro', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new ParametrosVo();
        } else {
            return $this->populateVo($connection, $row);
        }
    }
    
    public function loadByName($connection, $nome) {
        $query = " SELECT * 
		           FROM   parametros
		           WHERE  par_opldesativado = 1
                   AND    lower(par_dssnome) LIKE '%".strtolower($nome)."%'";
        
        $stmt = $connection->prepare($query);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new ParametrosVo();
        } else {
            return $this->populateVo($connection, $row);
        }
    }
    
    public function save($connection, $vo) {
        if (Functions::isEmpty($vo->getId())) {
            $this->insert($connection, $vo);
        } else {
            $this->update($connection, $vo);
        }
    }
    
    public function insert($connection, $vo) {
        $query = " INSERT INTO parametros
                     ( par_dssnome
                     , par_dssvalor
                     , par_opldesativado
                     )
                   VALUES
                     ( :par_dssnome
                     , :par_dssvalor
                     , :par_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':par_dssnome', $vo->getNome());
        $stmt->bindParam(':par_dssvalor', $vo->getValor());
        $stmt->bindParam(':par_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE parametros
                   SET    par_dssnome       = :par_dssnome
                   ,      par_dssvalor      = :par_dssvalor
                   ,      par_opldesativado = :par_opldesativado
                   WHERE  par_cdiparametro  = :par_cdiparametro ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':par_dssnome', $vo->getNome());
        $stmt->bindParam(':par_dssvalor', $vo->getValor());
        $stmt->bindParam(':par_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':par_cdiparametro', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM parametros WHERE par_cdiparametro = :par_cdiparametro ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':par_cdiparametro', $vo->getId());
        
        $stmt->execute();
    }
    
}