<?php

class ChamadosHistoricosModel {
    
    private function populateVo($connection, $row) {
        $chamadoModel = new ChamadosModel();
        $chamadoVo = $chamadoModel->loadById($connection, $row->chh_cdichamado);
        
        $usuarioModel = new UsuariosModel();
        $usuarioVo = $usuarioModel->loadById($connection, $row->chh_cdiusuario);
        
        $vo = new ChamadosHistoricosVo();
        
        $vo->setId($row->chh_cdichamadohistorico);
        $vo->setChamado($chamadoVo);
        $vo->setUsuario($usuarioVo);
        $vo->setData(Functions::toDateTime($row->chh_dtddata));
        $vo->setObservacao($row->chh_dsbobservacao);
        $vo->setAnexo($row->chh_dsbanexo);
        
        return $vo;
    }
    
    public function loadByChamado($connection, $codigo) {
        $registros = array();
        
        $query = " SELECT ch.*
                   FROM   chamadoshistoricos ch
                   WHERE  ch.chh_cdichamado = :chh_cdichamado
                   ORDER  BY ch.chh_cdichamadohistorico ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':chh_cdichamado', $codigo);
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $vo = $this->populateVo($connection, $row);
            
            array_push($registros, $vo);
        }
        
        return $registros;
    }
    
    public function loadById($connection, $codigo) {
        $query = " SELECT ch.* 
		           FROM   chamadoshistoricos ch 
		           WHERE  ch.chh_cdichamadohistorico = :chh_cdichamadohistorico ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':chh_cdichamadohistorico', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new ChamadosHistoricosVo();
        } else {
            return $this->populateVo($connection, $row);
        }
    }
    
    public function save($connection, $vo) {
        if (!Functions::isEmpty($vo->getObservacao())) {
            if (Functions::isEmpty($vo->getId())) {
                $vo->setId($this->insert($connection, $vo));
            } else {
                $vo->setId($this->update($connection, $vo));
            }
        }
        
        $arquivo = $vo->getAnexo();
        if (!Functions::isEmpty($arquivo["name"])) {
            $this->uploadFile($vo);
        }
    }
    
    public function insert($connection, $vo) {
        $query = " INSERT INTO chamadoshistoricos
                     ( chh_cdichamado
                     , chh_cdiusuario
                     , chh_dtddata
                     , chh_dsbobservacao
                     , chh_dsbanexo
                     )
                   VALUES
                     ( :chh_cdichamado
                     , :chh_cdiusuario
                     , :chh_dtddata
                     , :chh_dsbobservacao
                     , :chh_dsbanexo
                     ) ";
        
        $stmt = $connection->prepare($query);
        
        $chamadoAnexo = $vo->getAnexo();
        
        $stmt->bindParam(':chh_cdichamado', $vo->getChamado()->getId());
        $stmt->bindParam(':chh_cdiusuario', $vo->getUsuario()->getId());
        $stmt->bindParam(':chh_dtddata', $vo->getData());
        $stmt->bindParam(':chh_dsbobservacao', $vo->getObservacao());
        $stmt->bindParam(':chh_dsbanexo', $chamadoAnexo["name"]);
        
        $stmt->execute();
        
        $sequence = " SELECT last_insert_id() AS id ";
        
        foreach ($connection->query($sequence) as $row) {
            return $row->id;
        }
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE chamadoshistoricos
                   SET    chh_cdichamado          = :chh_cdichamado
                   ,      chh_cdiusuario          = :chh_cdiusuario
                   ,      chh_dtddata             = :chh_dtddata
                   ,      chh_dsbobservacao       = :chh_dsbobservacao
                   ,  	  chh_dsbanexo            = :chh_dsbanexo
                   WHERE  chh_cdichamadohistorico = :chh_cdichamadohistorico ";
        
        $stmt = $connection->prepare($query);
        
        $chamadoAnexo = $vo->getAnexo();
        
        $stmt->bindParam(':chh_cdichamado', $vo->getChamado()->getId());
        $stmt->bindParam(':chh_cdiusuario', $vo->getUsuario()->getId());
        $stmt->bindParam(':chh_dtddata', $vo->getEnderecoManter());
        $stmt->bindParam(':chh_dsbobservacao', $vo->getObservacao());
        $stmt->bindParam(':chh_dsbanexo', $chamadoAnexo["name"]);
        $stmt->bindParam(':chh_cdichamadohistorico', $vo->getId());
        
        $stmt->execute();
        
        return $vo->getId();
    }

    public function deleteByChamado($connection, $vo) {
        $query = " DELETE FROM chamadoshistoricos WHERE chh_cdichamado = :chh_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':chh_cdichamado', $vo->getChamado()->getId());
        
        $stmt->execute();
    }
    
    public function deleteById($connection, $vo) {
        $query = " DELETE FROM chamadoshistoricos WHERE chh_cdichamadohistorico = :chh_cdichamadohistorico ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':chh_cdichamadohistorico', $vo->getId());
        
        $stmt->execute();
    }
    
    public function uploadFile($vo) {
        $arquivo = $vo->getAnexo();
        
        $nome = "uploads/" . "CHA" . str_pad($vo->getChamado()->getId(), 11, "0", STR_PAD_LEFT) . "." . str_pad($vo->getId(), 11, "0", STR_PAD_LEFT) . "." . $arquivo['name'];
        if (file_exists($nome)) {
            $this->deleteUploadedFile($nome);
        }
        
        if (!move_uploaded_file($arquivo["tmp_name"], $nome)) {
            throw new Exception("Erro não tratado tentando fazer o upload do arquivo");
        }
    }
    
    public function deleteUploadedFile($nome) {
        unlink($nome);
    }
    
}