<?php

require_once('vendor/phpfastcache-final/phpfastcache.php');

class EmpresasModel {
    
    private function populateVo($connection, $row) {
        $vo = new EmpresasVo();
        
        $vo->setId($row->emp_cdiempresa);
        $vo->setDescricao($row->emp_dssempresa);
        $vo->setSituacao($row->emp_opldesativado);
        
        return $vo;
    }
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $empresasCache = $cache->get("EmpresasCacheAtivos");
        
        if ($empresasCache != null) {
            return $empresasCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   empresas 
		       WHERE  emp_opldesativado = 0 
		       ORDER  BY emp_dssempresa ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("EmpresasCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT * 
		   FROM   empresas
		   WHERE  emp_cdiempresa = :emp_cdiempresa ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':emp_cdiempresa', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new EmpresasVo();
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
        $query = " INSERT INTO empresas
                     ( emp_dssempresa
                     , emp_opldesativado
                     )
                   VALUES
                     ( :emp_dssempresa
                     , :emp_opldesativado
                   ) ";

        $stmt = $connection->prepare($query);

        $stmt->bindParam(':emp_dssempresa', $vo->getDescricao());
        $stmt->bindParam(':emp_opldesativado', $vo->getSituacao());

        $stmt->execute();
    }

    public function update($connection, $vo) {
        $query = " UPDATE empresas
                   SET    emp_dssempresa    = :emp_dssempresa
                   ,      emp_opldesativado = :emp_opldesativado
                   WHERE  emp_cdiempresa    = :emp_cdiempresa ";

        $stmt = $connection->prepare($query);

        $stmt->bindParam(':emp_dssempresa', $vo->getDescricao());
        $stmt->bindParam(':emp_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':emp_cdiempresa', $vo->getId());

        $stmt->execute();
    }

    public function delete($connection, $vo) {
        $query = " DELETE FROM empresas WHERE emp_cdiempresa = :emp_cdiempresa ";

        $stmt = $connection->prepare($query);

        $stmt->bindParam(':emp_cdiempresa', $vo->getId());

        $stmt->execute();
    }

}