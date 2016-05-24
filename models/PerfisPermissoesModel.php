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
    
    public function load($connection) {
        $cache = phpFastCache();
        
        $perfisPermissoesCache = $cache->get("PerfisPermissoesCacheAtivos");
        
        if ($perfisPermissoesCache != null) {
            return $perfisPermissoesCache;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   perfispermissoes, perfis, tipossistemas
		       WHERE  prp_opldesativado  = 0
                       AND    prf_cdiperfil      = prp_cdiperfil
                       AND    prf_opldesativado  = 0
                       AND    tps_cditiposistema = prp_cditiposistema
                       AND    tps_opldesativado  = 0
		       ORDER  BY prf_dssperfil, tps_dsstiposistema ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("PerfisPermissoesCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
    public function loadByPerfil($connection, $perfilCodigo) {
        $cache = phpFastCache();
        
        $perfisPermissoesCachePerfil = $cache->get("PerfisPermissoesCachePerfil".$perfilCodigo);
        
        if ($perfisPermissoesCachePerfil != null) {
            return $perfisPermissoesCachePerfil;
        } else {
            $registros = array();
            
            $query = " SELECT * 
		       FROM   perfispermissoes, perfis, tipossistemas
		       WHERE  prp_cdiperfil      = :prp_cdiperfil
                       AND    prp_opldesativado  = 0
                       AND    prf_cdiperfil      = prp_cdiperfil
                       AND    prf_opldesativado  = 0
                       AND    tps_cditiposistema = prp_cditiposistema
                       AND    tps_opldesativado  = 0
		       ORDER  BY prf_dssperfil, tps_dsstiposistema ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->bindParam(':prp_cdiperfil', $perfilCodigo);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("PerfisPermissoesCachePerfil".$perfilCodigo, $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
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