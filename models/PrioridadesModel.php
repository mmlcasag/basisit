<?php

class PrioridadesModel {
    
    private function populateVo($connection, $row) {
        $vo = new PrioridadesVo();
        
        $vo->setId($row->pri_cdiprioridade);
        $vo->setDescricao($row->pri_dssprioridade);
        $vo->setExibeImpacto($row->pri_oplexibeimpacto);
        $vo->setSituacao($row->pri_opldesativado);
        
        return $vo;
    }
    
    public function load($connection, $descricao = "", $situacao = "") {
        if (Functions::isEmpty($situacao)) {
            $situacao = 1;
        }
        
        $registros = array();
        
        $query = " SELECT * 
                   FROM   prioridades 
                   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($descricao)) {
            $query .= " AND LOWER(pri_dssprioridade) LIKE :pri_dssprioridade ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND pri_opldesativado = :pri_opldesativado ";
        }
        
        $query .= " ORDER  BY pri_dssprioridade ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($descricao)) {
            $descricao = "%" . strtolower($descricao) . "%";
            $stmt->bindParam(':pri_dssprioridade', $descricao);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':pri_opldesativado', $situacao);
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
		           FROM   prioridades
		           WHERE  pri_cdiprioridade = :pri_cdiprioridade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':pri_cdiprioridade', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new PrioridadesVo();
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
        $query = " INSERT INTO prioridades
                     ( pri_dssprioridade
                     , pri_oplexibeimpacto
                     , pri_opldesativado
                     )
                   VALUES
                     ( :pri_dssprioridade
                     , :pri_oplexibeimpacto
                     , :pri_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':pri_dssprioridade', $vo->getDescricao());
        $stmt->bindParam(':pri_oplexibeimpacto', $vo->getExibeImpacto());
        $stmt->bindParam(':pri_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE prioridades
                   SET    pri_dssprioridade   = :pri_dssprioridade
                   ,      pri_oplexibeimpacto = :pri_oplexibeimpacto
                   ,      pri_opldesativado   = :pri_opldesativado
                   WHERE  pri_cdiprioridade   = :pri_cdiprioridade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':pri_dssprioridade', $vo->getDescricao());
        $stmt->bindParam(':pri_oplexibeimpacto', $vo->getExibeImpacto());
        $stmt->bindParam(':pri_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':pri_cdiprioridade', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM prioridades WHERE pri_cdiprioridade = :pri_cdiprioridade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':pri_cdiprioridade', $vo->getId());
        
        $stmt->execute();
    }
    
}