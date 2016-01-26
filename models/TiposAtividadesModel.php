<?php

require_once('vendor/phpfastcache-final/phpfastcache.php');

class TiposAtividadesModel {
    
    private function populateVo($connection, $row) {
        $vo = new TiposAtividadesVo();
        
        $vo->setId($row->tpt_cditipoatividade);
        $vo->setDescricao($row->tpt_dsstipoatividade);
        $vo->setSituacao($row->tpt_opldesativado);
        
        return $vo;
    }
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $tiposAtividadesCache = $cache->get("TiposAtividadesCacheAtivos");
        
        if ($tiposAtividadesCache != null) {
            return $tiposAtividadesCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   tiposatividades
		       WHERE  tpt_opldesativado = 0 
		       ORDER  BY tpt_dsstipoatividade ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("TiposAtividadesCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT * 
		   FROM   tiposatividades
		   WHERE  tpt_cditipoatividade = :tpt_cditipoatividade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpt_cditipoatividade', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new TiposAtividadesVo();
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
        $query = " INSERT INTO tiposatividades
                     ( tpt_dsstipoatividade
                     , tpt_opldesativado
                     )
                   VALUES
                     ( :tpt_dsstipoatividade
                     , :tpt_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpt_dsstipoatividade', $vo->getDescricao());
        $stmt->bindParam(':tpt_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE tiposatividades
                   SET    tpt_dsstipoatividade = :tpt_dsstipoatividade
                   ,      tpt_opldesativado    = :tpt_opldesativado
                   WHERE  tpt_cditipoatividade = :tpt_cditipoatividade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpt_dsstipoatividade', $vo->getDescricao());
        $stmt->bindParam(':tpt_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':tpt_cditipoatividade', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM tiposatividades WHERE tpt_cditipoatividade = :tpt_cditipoatividade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpt_cditipoatividade', $vo->getId());
        
        $stmt->execute();
    }
    
}