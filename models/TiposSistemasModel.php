<?php

class TiposSistemasModel {
    
    private function populateVo($connection, $row) {
        $vo = new TiposSistemasVo();
        
        $vo->setId($row->tps_cditiposistema);
        $vo->setDescricao($row->tps_dsstiposistema);
        $vo->setNomeMenu($row->tps_dssnomemenu);
        $vo->setEnderecoListar($row->tps_dssenderecolistar);
        $vo->setSituacao($row->tps_opldesativado);
        
        return $vo;
    }
    
    public function load($connection, $descricao = "", $situacao = "") {
        if (Functions::isEmpty($situacao)) {
            $situacao = 1;
        }
        
        $registros = array();
        
        $query = " SELECT * 
                   FROM   tipossistemas
                   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($descricao)) {
            $query .= " AND LOWER(tps_dsstiposistema) LIKE :tps_dsstiposistema ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND tps_opldesativado = :tps_opldesativado ";
        }
        
        $query .= " ORDER  BY tps_dsstiposistema ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($descricao)) {
            $descricao = "%" . strtolower($descricao) . "%";
            $stmt->bindParam(':tps_dsstiposistema', $descricao);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':tps_opldesativado', $situacao);
        }
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $vo = $this->populateVo($connection, $row);
            
            array_push($registros, $vo);
        }
        
        return $registros;
    }
    
    public function loadByPerfil($connection, $perfilCodigo) {
        $registros = array();
        
        $query = " SELECT ts.*
                   FROM   perfis           pr
                   JOIN   perfispermissoes pp ON pp.prp_cdiperfil      = pr.prf_cdiperfil
                   JOIN   tipossistemas    ts ON ts.tps_cditiposistema = pp.prp_cditiposistema
                   WHERE  pr.prf_cdiperfil     = :prf_cdiperfil
                   AND    pr.prf_opldesativado = 1
                   AND    pp.prp_opldesativado = 1
                   AND    ts.tps_opldesativado = 1
                   ORDER  BY ts.tps_dsstiposistema ";
        
        $stmt = $connection->prepare($query);

        $stmt->bindParam(':prf_cdiperfil', $perfilCodigo);

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
                   FROM   tipossistemas 
                   WHERE  tps_cditiposistema = :tps_cditiposistema ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tps_cditiposistema', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new TiposSistemasVo();
        } else {
            $vo = $this->populateVo($connection, $row);
            return $vo;
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
        $query = " INSERT INTO tipossistemas
                     ( tps_dsstiposistema
                     , tps_dssnomemenu
                     , tps_dssenderecolistar
                     , tps_opldesativado
                     )
                   VALUES
                     ( :tps_dsstiposistema
                     , :tps_dssnomemenu
                     , :tps_dssenderecolistar
                     , :tps_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tps_dsstiposistema', $vo->getDescricao());
        $stmt->bindParam(':tps_dssnomemenu', $vo->getNomeMenu());
        $stmt->bindParam(':tps_dssenderecolistar', $vo->getEnderecoListar());
        $stmt->bindParam(':tps_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE tipossistemas
                   SET    tps_dsstiposistema    = :tps_dsstiposistema
                   ,      tps_dssnomemenu       = :tps_dssnomemenu
                   ,      tps_dssenderecolistar = :tps_dssenderecolistar
                   ,  	  tps_opldesativado     = :tps_opldesativado
                   WHERE  tps_cditiposistema    = :tps_cditiposistema ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tps_dsstiposistema', $vo->getDescricao());
        $stmt->bindParam(':tps_dssnomemenu', $vo->getNomeMenu());
        $stmt->bindParam(':tps_dssenderecolistar', $vo->getEnderecoListar());
        $stmt->bindParam(':tps_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':tps_cditiposistema', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM tipossistemas WHERE tps_cditiposistema = :tps_cditiposistema ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':tps_cditiposistema', $vo->getId());
        
        $stmt->execute();
    }
    
}