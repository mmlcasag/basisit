<?php

require_once('vendor/phpfastcache-final/phpfastcache.php');

class TiposAmbientesModel {
    
    private function populateVo($connection, $row) {
        $vo = new TiposAmbientesVo();
        
        $vo->setId($row->tpa_cditipoambiente);
        $vo->setDescricao($row->tpa_dsstipoambiente);
        $vo->setSituacao($row->tpa_opldesativado);
        
        return $vo;
    }
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $tiposAmbientesCache = $cache->get("TiposAmbientesCacheAtivos");
        
        if ($tiposAmbientesCache != null) {
            return $tiposAmbientesCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   tiposambientes
		       WHERE  tpa_opldesativado = 0 
		       ORDER  BY tpa_dsstipoambiente ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("TiposAmbientesCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT * 
		   FROM   tiposambientes
		   WHERE  tpa_cditipoambiente = :tpa_cditipoambiente ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpa_cditipoambiente', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new TiposAmbientesVo();
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
        $query = " INSERT INTO tiposambientes
                     ( tpa_dsstipoambiente
                     , tpa_opldesativado
                     )
                   VALUES
                     ( :tpa_dsstipoambiente
                     , :tpa_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpa_dsstipoambiente', $vo->getDescricao());
        $stmt->bindParam(':tpa_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE tiposambientes
                   SET    tpa_dsstipoambiente = :tpa_dsstipoambiente
                   ,      tpa_opldesativado   = :tpa_opldesativado
                   WHERE  tpa_cditipoambiente = :tpa_cditipoambiente ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpa_dsstipoambiente', $vo->getDescricao());
        $stmt->bindParam(':tpa_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':tpa_cditipoambiente', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM tiposambientes WHERE tpa_cditipoambiente = :tpa_cditipoambiente ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpa_cditipoambiente', $vo->getId());
        
        $stmt->execute();
    }
    
}