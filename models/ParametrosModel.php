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
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $parametrosCache = $cache->get("ParametrosCacheAtivos");
        
        if ($parametrosCache != null) {
            return $parametrosCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   parametros 
		       ORDER  BY par_cdiparametro ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("ParametrosCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
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
		   WHERE  par_opldesativado = 0
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