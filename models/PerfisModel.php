<?php

class PerfisModel {
    
    private function populateVo($connection, $row) {
        $vo = new PerfisVo();
        
        $vo->setId($row->prf_cdiperfil);
        $vo->setDescricao($row->prf_dssperfil);
        $vo->setAdministrador($row->prf_opladministrador);
        $vo->setFuncionario($row->prf_oplfuncionario);
        $vo->setCliente($row->prf_oplcliente);
        $vo->setSituacao($row->prf_opldesativado);
        
        return $vo;
    }
    
    public function load($connection, $descricao = "", $situacao = "") {
        if (Functions::isEmpty($situacao)) {
            $situacao = 1;
        }
        
        $registros = array();
        
        $query = " SELECT * 
                   FROM   perfis 
                   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($descricao)) {
            $query .= " AND LOWER(prf_dssperfil) LIKE :prf_dssperfil ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND prf_opldesativado = :prf_opldesativado ";
        }
        
        $query .= " ORDER  BY prf_dssperfil ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($descricao)) {
            $descricao = "%" . strtolower($descricao) . "%";
            $stmt->bindParam(':prf_dssperfil', $descricao);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':prf_opldesativado', $situacao);
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
		           FROM   perfis
		           WHERE  prf_cdiperfil = :prf_cdiperfil ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':prf_cdiperfil', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new PerfisVo();
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
        $query = " INSERT INTO perfis
                     ( prf_dssperfil
                     , prf_opladministrador
                     , prf_oplfuncionario
                     , prf_oplcliente
                     , prf_opldesativado
                     )
                   VALUES
                     ( :prf_dssperfil
                     , :prf_opladministrador
                     , :prf_oplfuncionario
                     , :prf_oplcliente
                     , :prf_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':prf_dssperfil', $vo->getDescricao());
        $stmt->bindParam(':prf_opladministrador', $vo->getAdministrador());
        $stmt->bindParam(':prf_oplfuncionario', $vo->getFuncionario());
        $stmt->bindParam(':prf_oplcliente', $vo->getCliente());
        $stmt->bindParam(':prf_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE perfis
                   SET    prf_dssperfil        = :prf_dssperfil
                   ,      prf_opladministrador = :prf_opladministrador
                   ,      prf_oplfuncionario   = :prf_oplfuncionario
                   ,      prf_oplcliente       = :prf_oplcliente
                   ,      prf_opldesativado    = :prf_opldesativado
                   WHERE  prf_cdiperfil        = :prf_cdiperfil ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':prf_dssperfil', $vo->getDescricao());
        $stmt->bindParam(':prf_opladministrador', $vo->getAdministrador());
        $stmt->bindParam(':prf_oplfuncionario', $vo->getFuncionario());
        $stmt->bindParam(':prf_oplcliente', $vo->getCliente());
        $stmt->bindParam(':prf_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':prf_cdiperfil', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM perfis WHERE prf_cdiperfil = :prf_cdiperfil ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':prf_cdiperfil', $vo->getId());
        
        $stmt->execute();
    }
    
}