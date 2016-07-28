<?php

class PerfisPermissoesModel {
    
    private function populateVo($connection, $row) {
        $perfisModel = new PerfisModel();
        $perfilVo = $perfisModel->loadById($connection, $row->prp_cdiperfil);
        
        $tiposSistemasModel = new TiposSistemasModel();
        $tipoSistemaVo = $tiposSistemasModel->loadById($connection, $row->prp_cditiposistema);
        
        $vo = new PerfisPermissoesVo();
        
        $vo->setId($row->prp_cdiperfilpermissao);
        $vo->setPerfil($perfilVo);
        $vo->setTipoSistema($tipoSistemaVo);
        $vo->setSituacao($row->prp_opldesativado);
        
        return $vo;
    }
    
    public function loadByPerfil($connection, $perfilCodigo, $descricao = "", $situacao = "") {
        if (Functions::isEmpty($situacao)) {
            $situacao = 1;
        }
        
        $registros = array();
        
        $query = " SELECT * 
                   FROM   perfispermissoes, perfis, tipossistemas
                   WHERE  prf_cdiperfil      = prp_cdiperfil
                   AND    prf_opldesativado  = 1
                   AND    tps_cditiposistema = prp_cditiposistema
                   AND    tps_opldesativado  = 1
                   AND    prp_cdiperfil      = :prp_cdiperfil ";
        
        if (!Functions::isEmpty($descricao)) {
            $query .= " AND LOWER(tps_dsstiposistema) LIKE :tps_dsstiposistema ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND prp_opldesativado = :prp_opldesativado ";
        }
        
        $query .= " ORDER  BY prf_dssperfil, tps_dsstiposistema ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($descricao)) {
            $descricao = "%" . strtolower($descricao) . "%";
            $stmt->bindParam(':tps_dsstiposistema', $descricao);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':prp_opldesativado', $situacao);
        }
        
        $stmt->bindParam(':prp_cdiperfil', $perfilCodigo);
        
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
		           FROM   perfispermissoes
		           WHERE  prp_cdiperfilpermissao = :prp_cdiperfilpermissao ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':prp_cdiperfilpermissao', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new PerfisPermissoesVo();
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
        $query = " INSERT INTO perfispermissoes
                     ( prp_cdiperfil
                     , prp_cditiposistema
                     , prp_opldesativado
                     )
                   VALUES
                     ( :prp_cdiperfil
                     , :prp_cditiposistema
                     , :prp_opldesativado
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':prp_cdiperfil', $vo->getPerfil()->getId());
        $stmt->bindParam(':prp_cditiposistema', $vo->getTipoSistema()->getId());
        $stmt->bindParam(':prp_opldesativado', $vo->getSituacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE perfispermissoes
                   SET    prp_cdiperfil          = :prp_cdiperfil
                   ,      prp_cditiposistema     = :prp_cditiposistema
                   ,      prp_opldesativado      = :prp_opldesativado
                   WHERE  prp_cdiperfilpermissao = :prp_cdiperfilpermissao ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':prp_cdiperfil', $vo->getPerfil()->getId());
        $stmt->bindParam(':prp_cditiposistema', $vo->getTipoSistema()->getId());
        $stmt->bindParam(':prp_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':prp_cdiperfilpermissao', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM perfispermissoes WHERE prp_cdiperfilpermissao = :prp_cdiperfilpermissao ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':prp_cdiperfilpermissao', $vo->getId());
        
        $stmt->execute();
    }
	
}