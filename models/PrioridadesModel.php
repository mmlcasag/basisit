<?php

require_once('vendor/phpfastcache-final/phpfastcache.php');

class PrioridadesModel {
    
    private function populateVo($connection, $row) {
        $vo = new PrioridadesVo();
        
        $vo->setId($row->pri_cdiprioridade);
        $vo->setDescricao($row->pri_dssprioridade);
        $vo->setExibeImpacto($row->pri_oplexibeimpacto);
        $vo->setSituacao($row->pri_opldesativado);
        
        return $vo;
    }
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $prioridadesCache = $cache->get("PrioridadesCacheAtivos");
        
        if ($prioridadesCache != null) {
            return $prioridadesCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   prioridades 
		       WHERE  pri_opldesativado = 0 
		       ORDER  BY pri_cdiprioridade ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("PrioridadesCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
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