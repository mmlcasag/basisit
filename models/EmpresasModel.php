<?php

class EmpresasModel {
    
    private function populateVo($connection, $row) {
        $vo = new EmpresasVo();
        
        $vo->setId($row->emp_cdiempresa);
        $vo->setDescricao($row->emp_dssempresa);
        $vo->setSituacao($row->emp_opldesativado);
        
        return $vo;
    }
    
    public function load($connection, $descricao = "", $situacao = "") {
        if (Functions::isEmpty($situacao)) {
            $situacao = 1;
        }
        
        $registros = array();
        
        $query = " SELECT * 
                   FROM   empresas 
                   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($descricao)) {
            $query .= " AND LOWER(emp_dssempresa) LIKE :emp_dssempresa ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND emp_opldesativado = :emp_opldesativado ";
        }
        
        $query .= " ORDER  BY emp_dssempresa ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($descricao)) {
            $descricao = "%" . strtolower($descricao) . "%";
            $stmt->bindParam(':emp_dssempresa', $descricao);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':emp_opldesativado', $situacao);
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