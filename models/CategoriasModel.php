<?php

require_once('vendor/phpfastcache-final/phpfastcache.php');

class CategoriasModel {
    
    private function populateVo($connection, $row) {
        $vo = new CategoriasVo();
        
        $vo->setId($row->cat_cdicategoria);
        $vo->setDescricao($row->cat_dsscategoria);
        $vo->setSituacao($row->cat_opldesativado);
        
        return $vo;
    }
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $categoriasCache = $cache->get("CategoriasCacheAtivos");
        
        if ($categoriasCache != null) {
            return $categoriasCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   categorias 
		       WHERE  cat_opldesativado = 0 
		       ORDER  BY cat_dsscategoria ";
            
            $stmt = $connection->prepare($query);

            $stmt->execute();

            $rows = $stmt->fetchAll();

            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("CategoriasCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT * 
		   FROM   categorias
		   WHERE  cat_cdicategoria = :cat_cdicategoria ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':cat_cdicategoria', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new CategoriasVo();
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
        $query = " INSERT INTO categorias
                     ( cat_dsscategoria
                     , cat_opldesativado
                     )
                   VALUES
                     ( :cat_dsscategoria
                     , :cat_opldesativado
                   ) ";

        $stmt = $connection->prepare($query);

        $stmt->bindParam(':cat_dsscategoria', $vo->getDescricao());
        $stmt->bindParam(':cat_opldesativado', $vo->getSituacao());

        $stmt->execute();
    }

    public function update($connection, $vo) {
        $query = " UPDATE categorias
                   SET    cat_dsscategoria  = :cat_dsscategoria
                   ,      cat_opldesativado = :cat_opldesativado
                   WHERE  cat_cdicategoria  = :cat_cdicategoria ";

        $stmt = $connection->prepare($query);

        $stmt->bindParam(':cat_dsscategoria', $vo->getDescricao());
        $stmt->bindParam(':cat_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':cat_cdicategoria', $vo->getId());

        $stmt->execute();
    }

    public function delete($connection, $vo) {
        $query = " DELETE FROM categorias WHERE cat_cdicategoria = :cat_cdicategoria ";

        $stmt = $connection->prepare($query);

        $stmt->bindParam(':cat_cdicategoria', $vo->getId());

        $stmt->execute();
    }

}