<?php

require_once('vendor/phpfastcache-final/phpfastcache.php');

class TiposProdutosModel {
    
    private function populateVo($connection, $row) {
        $vo = new TiposProdutosVo();
        
        $vo->setId($row->tpp_cditipoproduto);
        $vo->setDescricao($row->tpp_dsstipoproduto);
        $vo->setSituacao($row->tpp_opldesativado);
        
        return $vo;
    }
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $tiposProdutosCache = $cache->get("TiposProdutosCacheAtivos");
        
        if ($tiposProdutosCache != null) {
            return $tiposProdutosCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   tiposprodutos 
		       WHERE  tpp_opldesativado = 0 
		       ORDER  BY tpp_dsstipoproduto ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("TiposProdutosCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT * 
		   FROM   tiposprodutos
		   WHERE  tpp_cditipoproduto = :tpp_cditipoproduto ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpp_cditipoproduto', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new TiposProdutosVo();
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
        $query = " INSERT INTO tiposprodutos
                     ( tpp_dsstipoproduto
                     , tpp_opldesativado
                     )
                   VALUES
                     ( :tpp_dsstipoproduto
                     , :tpp_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpp_dsstipoproduto', $vo->getDescricao());
        $stmt->bindParam(':tpp_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE tiposprodutos
                   SET    tpp_dsstipoproduto = :tpp_dsstipoproduto
                   ,      tpp_opldesativado  = :tpp_opldesativado
                   WHERE  tpp_cditipoproduto = :tpp_cditipoproduto ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpp_dsstipoproduto', $vo->getDescricao());
        $stmt->bindParam(':tpp_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':tpp_cditipoproduto', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM tiposprodutos WHERE tpp_cditipoproduto = :tpp_cditipoproduto ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tpp_cditipoproduto', $vo->getId());
        
        $stmt->execute();
    }
    
}