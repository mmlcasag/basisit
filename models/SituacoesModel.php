<?php

require_once('vendor/phpfastcache-final/phpfastcache.php');

class SituacoesModel {
    
    private function populateVo($connection, $row) {
        $vo = new SituacoesVo();
        
        $vo->setId($row->sit_cdisituacao);
        $vo->setDescricao($row->sit_dsssituacao);
        $vo->setSituacao($row->sit_opldesativado);
        
        return $vo;
    }
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $situacoesCache = $cache->get("SituacoesCacheAtivos");
        
        if ($situacoesCache != null) {
            return $situacoesCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   situacoes 
		       WHERE  sit_opldesativado = 0 
		       ORDER  BY sit_cdisituacao ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("SituacoesCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT * 
		   FROM   situacoes
		   WHERE  sit_cdisituacao = :sit_cdisituacao ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':sit_cdisituacao', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new SituacoesVo();
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
        $query = " INSERT INTO situacoes
                     ( sit_dsssituacao
                     , sit_opldesativado
                     )
                   VALUES
                     ( :sit_dsssituacao
                     , :sit_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':sit_dsssituacao', $vo->getDescricao());
        $stmt->bindParam(':sit_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE situacoes
                   SET    sit_dsssituacao   = :sit_dsssituacao
                   ,      sit_opldesativado = :sit_opldesativado
                   WHERE  sit_cdisituacao   = :sit_cdisituacao ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':sit_dsssituacao', $vo->getDescricao());
        $stmt->bindParam(':sit_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':sit_cdisituacao', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM situacoes WHERE sit_cdisituacao = :sit_cdisituacao ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':sit_cdisituacao', $vo->getId());
        
        $stmt->execute();
    }
    
}