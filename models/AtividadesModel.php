<?php

class AtividadesModel {
    
    private function populateVo($connection, $row) {
        $situacaoModel = new SituacoesModel();
        $situacaoVo = $situacaoModel->loadById($connection, $row->ati_cdisituacao);
        
        $usuarioModel = new UsuariosModel();
        $usuarioVo = $usuarioModel->loadById($connection, $row->ati_cdiusuario);
        
        $empresaModel = new EmpresasModel();
        $empresaVo = $empresaModel->loadById($connection, $row->ati_cdiempresa);
        
        $tipoAtividadeModel = new TiposAtividadesModel();
        $tipoAtividadeVo = $tipoAtividadeModel->loadById($connection, $row->ati_cditipoatividade);
        
        $vo = new AtividadesVo();
        
        $vo->setId($row->ati_cdiatividade);
        $vo->setSituacao($situacaoVo);
        $vo->setData(Functions::toDate($row->ati_dtdcriacao));
        $vo->setUsuario($usuarioVo);
        $vo->setEmpresa($empresaVo);
        $vo->setTipoAtividade($tipoAtividadeVo);
        $vo->setAssunto($row->ati_dssassunto);
        $vo->setObservacao($row->ati_dsbobservacao);
        
        return $vo;
    }
    
    public function load($connection) {
        $registros = array();
        
        $query = " SELECT *
                   FROM   atividades
                   WHERE  1 = 1 
                   ORDER  BY ati_cdiatividade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $vo = $this->populateVo($connection, $row);
            
            array_push($registros, $vo);
        }
        
        return $registros;
    }
    
    public function loadByCriteria($connection, $codigo = "", $usuario = "", $dataIni = "", $dataFim = "", $empresa = "", $tipoAtividade = "", $situacao = "", $assunto = "", $especial = "") {
        $registros = array();
        
        $query = " SELECT *
                   FROM   atividades
                   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($codigo)) {
            $query = $query . " AND ati_cdiatividade = :ati_cdiatividade ";
        }
        if (!Functions::isEmpty($usuario)) {
            $query = $query . " AND ati_cdiusuario = :ati_cdiusuario ";
        }
        if (!Functions::isEmpty($dataIni)) {
            $query = $query . " AND ati_dtdcriacao >= :ati_dtdcriacaoini ";
        }
        if (!Functions::isEmpty($dataFim)) {
            $query = $query . " AND ati_dtdcriacao <= :ati_dtdcriacaofim ";
        }
        if (!Functions::isEmpty($empresa)) {
            $query = $query . " AND ati_cdiempresa = :ati_cdiempresa ";
        }
        if (!Functions::isEmpty($tipoAtividade)) {
            $query = $query . " AND ati_cditipoatividade = :ati_cditipoatividade ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query = $query . " AND ati_cdisituacao = :ati_cdisituacao ";
        }
        if (!Functions::isEmpty($assunto)) {
            $query = $query . " AND LOWER(ati_dssassunto) LIKE :ati_dssassunto ";
        }
        if (!Functions::isEmpty($especial)) {
            $query = $query . " AND ati_cdisituacao NOT IN (" . $_SESSION['situacaoFinalizada'] . "," . $_SESSION['situacaoCancelada'] . ") ";
        }
        
        $query = $query . " ORDER  BY ati_cdiatividade ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($codigo)) {
            $stmt->bindParam(':ati_cdiatividade', $codigo);
        }
        if (!Functions::isEmpty($usuario)) {
            $stmt->bindParam(':ati_cdiusuario', $usuario);
        }
        if (!Functions::isEmpty($dataIni)) {
            $stmt->bindParam(':ati_dtdcriacaoini', Functions::toDateToSql($dataIni));
        }
        if (!Functions::isEmpty($dataFim)) {
            $stmt->bindParam(':ati_dtdcriacaofim', Functions::toDateToSql($dataFim));
        }
        if (!Functions::isEmpty($empresa)) {
            $stmt->bindParam(':ati_cdiempresa', $empresa);
        }
        if (!Functions::isEmpty($tipoAtividade)) {
            $stmt->bindParam(':ati_cditipoatividade', $tipoAtividade);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':ati_cdisituacao', $situacao);
        }
        if (!Functions::isEmpty($assunto)) {
            $assunto = "%" . strtolower($assunto) . "%";
            $stmt->bindParam(':ati_dssassunto', $assunto);
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
		   FROM   atividades 
		   WHERE  ati_cdiatividade = :ati_cdiatividade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':ati_cdiatividade', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new AtividadesVo();
        } else {
            return $this->populateVo($connection, $row);
        }
    }
    
    public function save($connection, $vo) {
        if (Functions::isEmpty($vo->getId())) {
            return $this->insert($connection, $vo);
        } else {
            return $this->update($connection, $vo);
        }
    }
    
    public function insert($connection, $vo) {
        $query = " INSERT INTO atividades
                      ( ati_cdisituacao
                      , ati_dtdcriacao
                      , ati_cdiusuario
                      , ati_cdiempresa
                      , ati_cditipoatividade
                      , ati_dssassunto
                      , ati_dsbobservacao
                      )
                   VALUES
                      ( :ati_cdisituacao
                      , :ati_dtdcriacao
                      , :ati_cdiusuario
                      , :ati_cdiempresa
                      , :ati_cditipoatividade
                      , :ati_dssassunto
                      , :ati_dsbobservacao
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':ati_cdisituacao', $vo->getSituacao()->getId());
        $stmt->bindParam(':ati_dtdcriacao', Functions::toDateToSql($vo->getData()));
        $stmt->bindParam(':ati_cdiusuario', $vo->getUsuario()->getId());
        $stmt->bindParam(':ati_cdiempresa', $vo->getEmpresa()->getId());
        $stmt->bindParam(':ati_cditipoatividade', $vo->getTipoAtividade()->getId());
        $stmt->bindParam(':ati_dssassunto', $vo->getAssunto());
        $stmt->bindParam(':ati_dsbobservacao', $vo->getObservacao());
        
        $stmt->execute();
        
        $sequence = " SELECT last_insert_id() AS id ";
        
        foreach ($connection->query($sequence) as $row) {
            return $row->id;
        }
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE atividades
                   SET    ati_cdisituacao        = :ati_cdisituacao
                   ,      ati_dtdcriacao         = :ati_dtdcriacao
                   ,      ati_cdiusuario         = :ati_cdiusuario
                   ,  	  ati_cdiempresa         = :ati_cdiempresa
                   ,  	  ati_cditipoatividade   = :ati_cditipoatividade
                   ,      ati_dssassunto         = :ati_dssassunto
                   ,  	  ati_dsbobservacao      = :ati_dsbobservacao
                   WHERE  ati_cdiatividade       = :ati_cdiatividade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':ati_cdisituacao', $vo->getSituacao()->getId());
        $stmt->bindParam(':ati_dtdcriacao', Functions::toDateToSql($vo->getData()));
        $stmt->bindParam(':ati_cdiusuario', $vo->getUsuario()->getId());
        $stmt->bindParam(':ati_cdiempresa', $vo->getEmpresa()->getId());
        $stmt->bindParam(':ati_cditipoatividade', $vo->getTipoAtividade()->getId());
        $stmt->bindParam(':ati_dssassunto', $vo->getAssunto());
        $stmt->bindParam(':ati_dsbobservacao', $vo->getObservacao());
        $stmt->bindParam(':ati_cdiatividade', $vo->getId());
        
        $stmt->execute();
        
        return $vo->getId();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM atividades WHERE ati_cdiatividade = :ati_cdiatividade ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':ati_cdiatividade', $vo->getId());
        
        $stmt->execute();
    }
    
}