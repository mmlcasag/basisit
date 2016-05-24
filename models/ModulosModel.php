<?php

class ModulosModel {
    
    private function populateVo($connection, $row) {
        $vo = new ModulosVo();
        
        $vo->setId($row->mod_cdimodulo);
        $vo->setDescricao($row->mod_dssmodulo);
        $vo->setSituacao($row->mod_opldesativado);
        
        return $vo;
    }
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $modulosCache = $cache->get("ModulosCacheAtivos");
        
        if ($modulosCache != null) {
            return $modulosCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   modulos 
		       WHERE  mod_opldesativado = 0 
		       ORDER  BY mod_dssmodulo ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("ModulosCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT * 
		   FROM   modulos
		   WHERE  mod_cdimodulo = :mod_cdimodulo ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':mod_cdimodulo', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new ModulosVo();
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
        $query = " INSERT INTO modulos
                     ( mod_dssmodulo
                     , mod_opldesativado
                     )
                   VALUES
                     ( :mod_dssmodulo
                     , :mod_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':mod_dssmodulo', $vo->getDescricao());
        $stmt->bindParam(':mod_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE modulos
                   SET    mod_dssmodulo     = :mod_dssmodulo
                   ,      mod_opldesativado = :mod_opldesativado
                   WHERE  mod_cdimodulo     = :mod_cdimodulo ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':mod_dssmodulo', $vo->getDescricao());
        $stmt->bindParam(':mod_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':mod_cdimodulo', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM modulos WHERE mod_cdimodulo = :mod_cdimodulo ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':mod_cdimodulo', $vo->getId());
        
        $stmt->execute();
    }
    
}