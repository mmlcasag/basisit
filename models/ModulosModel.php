<?php

class ModulosModel {
    
    private function populateVo($connection, $row) {
        $vo = new ModulosVo();
        
        $vo->setId($row->mod_cdimodulo);
        $vo->setDescricao($row->mod_dssmodulo);
        $vo->setSituacao($row->mod_opldesativado);
        
        return $vo;
    }
    
    public function load($connection, $descricao = "", $situacao = "") {
        if (Functions::isEmpty($situacao)) {
            $situacao = 1;
        }
        
        $registros = array();

        $query = " SELECT * 
                   FROM   modulos 
                   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($descricao)) {
            $query .= " AND LOWER(mod_dssmodulo) LIKE :mod_dssmodulo ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND mod_opldesativado = :mod_opldesativado ";
        }
        
        $query .= " ORDER  BY mod_dssmodulo ";

        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($descricao)) {
            $descricao = "%" . strtolower($descricao) . "%";
            $stmt->bindParam(':mod_dssmodulo', $descricao);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':mod_opldesativado', $situacao);
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