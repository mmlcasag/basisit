<?php

require_once('vendor/phpfastcache-final/phpfastcache.php');

class UsuariosModel {
    
    private function populateVo($connection, $row) {
        $perfilModel = new PerfisModel();
        $perfilVo = $perfilModel->loadById($connection, $row->usu_cdiperfil);
        
        $empresaModel = new EmpresasModel();
        $empresaVo = $empresaModel->loadById($connection, $row->usu_cdiempresa);
        
        $vo = new UsuariosVo();
        
        $vo->setId($row->usu_cdiusuario);
        $vo->setNome($row->usu_dssnome);
        $vo->setEmpresa($empresaVo);
        $vo->setSetor($row->usu_dsssetor);
        $vo->setPerfil($perfilVo);
        $vo->setFoneComercial($row->usu_dssfonecomercial);
        $vo->setFoneCelular($row->usu_dssfonecelular);
        $vo->setEmail($row->usu_dssemail);
        $vo->setSenha(Functions::decrypt($row->usu_dsssenha));
        $vo->setSituacao($row->usu_opldesativado);
        $vo->setObservacao($row->usu_dsbobservacao);
        
        return $vo;
    }
    
    public function load($connection, $apenasAtivos) {
        $cache = phpFastCache();
        
        if ($apenasAtivos == 1) {
            $usuariosCache = $cache->get("UsuariosCacheAtivos");
        } else {
            $usuariosCache = $cache->get("UsuariosCacheGeral");
        }
        
        if ($usuariosCache != null) {
            return $usuariosCache;
        } else {
            $registros = array();
            
            $query = " SELECT * FROM usuarios WHERE 1 = 1 ";
            if ($apenasAtivos == 1) {
                $query = $query . " AND usu_opldesativado = 0 ";
            }
            $query = $query . " ORDER BY usu_dssnome ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            if ($apenasAtivos == 1) {
                $cache->set("UsuariosCacheAtivos", $registros, 60 * Functions::getParametro('cache'));
            } else {
                $cache->set("UsuariosCacheGeral", $registros, 60 * Functions::getParametro('cache'));
            }
            
            return $registros;
        }
    }
    
    public function loadClientes($connection, $apenasAtivos) {
        $cache = phpFastCache();
        
        if ($apenasAtivos == 1) {
            $usuariosCache = $cache->get("UsuariosCacheClientesAtivos");
        } else {
            $usuariosCache = $cache->get("UsuariosCacheClientesGeral");
        }
        
        if ($usuariosCache != null) {
            return $usuariosCache;
        } else {
            $registros = array();
            
            $query = " SELECT *
                       FROM   usuarios, perfis
                       WHERE  prf_cdiperfil  = usu_cdiperfil
                       AND    prf_oplcliente = 1 ";
            
            if ($apenasAtivos == 1) {
                $query = $query . " AND usu_opldesativado = 0 ";
            }
            
            $query = $query . " ORDER BY usu_dssnome ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            if ($apenasAtivos == 1) {
                $cache->set("UsuariosCacheClientesAtivos", $registros, 60 * Functions::getParametro('cache'));
            } else {
                $cache->set("UsuariosCacheClientesGeral", $registros, 60 * Functions::getParametro('cache'));
            }
            
            return $registros;
        }
    }
    
    public function loadNaoClientes($connection, $apenasAtivos) {
        $cache = phpFastCache();
        
        if ($apenasAtivos == 1) {
            $usuariosCache = $cache->get("UsuariosCacheNaoClientesAtivos");
        } else {
            $usuariosCache = $cache->get("UsuariosCacheNaoClientesGeral");
        }
        
        if ($usuariosCache != null) {
            return $usuariosCache;
        } else {
            $registros = array();
            
            $query = " SELECT *
                       FROM   usuarios, perfis
                       WHERE  prf_cdiperfil  = usu_cdiperfil
                       AND    prf_oplcliente = 0 ";
            
            if ($apenasAtivos == 1) {
                $query = $query . " AND usu_opldesativado = 0 ";
            }
            
            $query = $query . " ORDER BY usu_dssnome ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            if ($apenasAtivos == 1) {
                $cache->set("UsuariosCacheNaoClientesAtivos", $registros, 60 * Functions::getParametro('cache'));
            } else {
                $cache->set("UsuariosCacheNaoClientesGeral", $registros, 60 * Functions::getParametro('cache'));
            }
            
            return $registros;
        }
    }
    
    public function loadClientesDeUmaEmpresa($connection, $apenasAtivos, $empresaCodigo) {
        $registros = array();
        
        $query = " SELECT *
                   FROM   usuarios, perfis
                   WHERE  prf_cdiperfil  = usu_cdiperfil
                   AND    prf_oplcliente = 1 ";

        if ($apenasAtivos == 1) {
            $query = $query . " AND usu_opldesativado = 0 ";
        }
        if (!Functions::isEmpty($empresaCodigo)) {
            $query = $query . " AND usu_cdiempresa = :usu_cdiempresa ";
        }
        
        $query = $query . " ORDER BY usu_dssnome ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($empresaCodigo)) {
            $stmt->bindParam(':usu_cdiempresa', $empresaCodigo);
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
                   FROM   usuarios 
                   WHERE  usu_cdiusuario = :usu_cdiusuario ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':usu_cdiusuario', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new UsuariosVo();
        } else {
            return $this->populateVo($connection, $row);
        }
    }
    
    public function loadByUserAndPassword($connection, $user, $pass) {
	$query = " SELECT * 
                   FROM   usuarios 
                   WHERE  usu_opldesativado = 0
                   AND    usu_dssemail = :usu_dssemail
                   AND    usu_dsssenha = :usu_dsssenha ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':usu_dssemail', $user);
        $stmt->bindParam(':usu_dsssenha', Functions::encrypt($pass));
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new UsuariosVo();
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
        $query = " INSERT INTO usuarios
                      ( usu_dssnome
                      , usu_cdiempresa
                      , usu_dsssetor
                      , usu_cdiperfil
                      , usu_dssfonecomercial
                      , usu_dssfonecelular
                      , usu_dssemail
                      , usu_dsssenha
                      , usu_opldesativado
                      , usu_dsbobservacao
                      )
                   VALUES
                      ( :usu_dssnome
                      , :usu_cdiempresa
                      , :usu_dsssetor
                      , :usu_cdiperfil
                      , :usu_dssfonecomercial
                      , :usu_dssfonecelular
                      , :usu_dssemail
                      , :usu_dsssenha                      
                      , :usu_opldesativado
                      , :usu_dsbobservacao
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':usu_dssnome', $vo->getNome());
        $stmt->bindParam(':usu_cdiempresa', $vo->getEmpresa()->getId());
        $stmt->bindParam(':usu_dsssetor', $vo->getSetor());
        $stmt->bindParam(':usu_cdiperfil', $vo->getPerfil()->getId());
        $stmt->bindParam(':usu_dssfonecomercial', $vo->getFoneComercial());
        $stmt->bindParam(':usu_dssfonecelular', $vo->getFoneCelular());
        $stmt->bindParam(':usu_dssemail', $vo->getEmail());
        $stmt->bindParam(':usu_dsssenha', Functions::encrypt($vo->getSenha()));
        $stmt->bindParam(':usu_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':usu_dsbobservacao', $vo->getObservacao());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE usuarios
                   SET    usu_dssnome          = :usu_dssnome
                   ,      usu_cdiempresa       = :usu_cdiempresa
                   ,      usu_dsssetor         = :usu_dsssetor
                   ,  	  usu_cdiperfil        = :usu_cdiperfil
                   ,  	  usu_dssfonecomercial = :usu_dssfonecomercial
                   ,  	  usu_dssfonecelular   = :usu_dssfonecelular
                   ,  	  usu_dssemail         = :usu_dssemail
                   ,      usu_dsssenha         = :usu_dsssenha
                   ,  	  usu_opldesativado    = :usu_opldesativado
                   ,  	  usu_dsbobservacao    = :usu_dsbobservacao
                   WHERE  usu_cdiusuario       = :usu_cdiusuario ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':usu_dssnome', $vo->getNome());
        $stmt->bindParam(':usu_cdiempresa', $vo->getEmpresa()->getId());
        $stmt->bindParam(':usu_dsssetor', $vo->getSetor());
        $stmt->bindParam(':usu_cdiperfil', $vo->getPerfil()->getId());
        $stmt->bindParam(':usu_dssfonecomercial', $vo->getFoneComercial());
        $stmt->bindParam(':usu_dssfonecelular', $vo->getFoneCelular());
        $stmt->bindParam(':usu_dssemail', $vo->getEmail());
        $stmt->bindParam(':usu_dsssenha', Functions::encrypt($vo->getSenha()));
        $stmt->bindParam(':usu_opldesativado', $vo->getSituacao());
        $stmt->bindParam(':usu_dsbobservacao', $vo->getObservacao());
        $stmt->bindParam(':usu_cdiusuario', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM usuarios WHERE usu_cdiusuario = :usu_cdiusuario ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':usu_cdiusuario', $vo->getId());
        
        $stmt->execute();
    }
    
    public function loadMeusRegistrosPerfilCliente($connection, $usuarioCodigo) {
        $registros = array();
        
        $query = " SELECT situacoes.sit_cdisituacao
                   ,      situacoes.sit_dsssituacao
                   ,      COUNT(distinct chamados.cha_cdichamado) sit_qtichamados
                   FROM   situacoes
                   LEFT   JOIN chamados ON situacoes.sit_cdisituacao = chamados.cha_cdisituacao AND chamados.cha_cdiusuario = :usuarioCodigo
                   WHERE  situacoes.sit_opldesativado = 0
                   GROUP  BY situacoes.sit_cdisituacao, situacoes.sit_dsssituacao
                   ORDER  BY situacoes.sit_cdisituacao, situacoes.sit_dsssituacao ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':usuarioCodigo', $usuarioCodigo);
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $registro = array('situacaoCodigo' => $row->sit_cdisituacao,
                              'situacaoDescricao' => $row->sit_dsssituacao,
                              'situacaoQuantidadeChamados' => $row->sit_qtichamados,
                              'situacaoQuantidadeAtividades' => 0);
            
            array_push($registros, $registro);
        }
        
        return $registros;
    }
    
    public function loadMeusRegistrosDemaisPerfis($connection, $usuarioCodigo) {
        $registros = array();
        
        $query = " SELECT situacoes.sit_cdisituacao
                   ,      situacoes.sit_dsssituacao
                   ,      COUNT(distinct chamados.cha_cdichamado) sit_qtichamados
                   ,      COUNT(distinct atividades.ati_cdiatividade) sit_qtiatividades
                   FROM   situacoes
                   LEFT   JOIN chamados ON situacoes.sit_cdisituacao = chamados.cha_cdisituacao AND chamados.cha_cdiusuario_atendente = :usuarioCodigo
                   LEFT   JOIN atividades ON situacoes.sit_cdisituacao = atividades.ati_cdisituacao AND atividades.ati_cdiusuario = :usuarioCodigo
                   WHERE  situacoes.sit_opldesativado = 0
                   GROUP  BY situacoes.sit_cdisituacao, situacoes.sit_dsssituacao
                   ORDER  BY situacoes.sit_cdisituacao, situacoes.sit_dsssituacao ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':usuarioCodigo', $usuarioCodigo);
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $registro = array('situacaoCodigo' => $row->sit_cdisituacao,
                              'situacaoDescricao' => $row->sit_dsssituacao,
                              'situacaoQuantidadeChamados' => $row->sit_qtichamados,
                              'situacaoQuantidadeAtividades' => $row->sit_qtiatividades);
            
            array_push($registros, $registro);
        }
        
        return $registros;
    }
    
    public function loadResumoGeralPerfilCliente($connection, $usuarioCodigo) {
        $registros = array();
        
        $query = " SELECT us.usu_cdiusuario
                   ,      us.usu_dssnome
                   ,      COUNT(distinct ch.cha_cdichamado) sit_qtichamados
                   FROM   chamados ch
                   JOIN   usuarios us ON us.usu_cdiusuario = ch.cha_cdiusuario AND us.usu_cdiempresa = ch.cha_cdiempresa
                   WHERE  ch.cha_cdiempresa      IN (select usu_cdiempresa from usuarios where usu_cdiusuario = :usuarioCodigo)
                   AND    ch.cha_cdisituacao NOT IN (" . $_SESSION['situacaoFinalizada'] . "," . $_SESSION['situacaoCancelada'] . ")
                   GROUP  BY us.usu_cdiusuario, us.usu_dssnome
                   ORDER  BY us.usu_dssnome ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':usuarioCodigo', $usuarioCodigo);
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $registro = array('usuarioCodigo' => $row->usu_cdiusuario,
                              'usuarioNome' => $row->usu_dssnome,
                              'usuarioQuantidadeChamados' => $row->sit_qtichamados,
                              'usuarioQuantidadeAtividades' => 0);
            
            array_push($registros, $registro);
        }
        
        return $registros;
    }
    
    public function loadResumoGeralDemaisPerfis($connection) {
        $registros = array();
        
        $query = " SELECT usuarios.usu_cdiusuario
                   ,      usuarios.usu_dssnome
                   ,      COUNT(distinct chamados.cha_cdichamado) sit_qtichamados
                   ,      COUNT(distinct atividades.ati_cdiatividade) sit_qtiatividades
                   FROM   usuarios
                   JOIN   perfis          ON perfis.prf_cdiperfil    = usuarios.usu_cdiperfil
                   LEFT   JOIN chamados   ON usuarios.usu_cdiusuario = chamados.cha_cdiusuario_atendente AND chamados.cha_cdisituacao   NOT IN (" . $_SESSION['situacaoFinalizada'] . "," . $_SESSION['situacaoCancelada'] . ")
                   LEFT   JOIN atividades ON usuarios.usu_cdiusuario = atividades.ati_cdiusuario         AND atividades.ati_cdisituacao NOT IN (" . $_SESSION['situacaoFinalizada'] . "," . $_SESSION['situacaoCancelada'] . ")
                   WHERE  perfis.prf_oplcliente      = 0
                   GROUP  BY usuarios.usu_cdiusuario, usuarios.usu_dssnome
                   HAVING COUNT(distinct chamados.cha_cdichamado) > 0 OR COUNT(distinct atividades.ati_cdiatividade) > 0
                   ORDER  BY usuarios.usu_cdiusuario, usuarios.usu_dssnome ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $registro = array('usuarioCodigo' => $row->usu_cdiusuario,
                              'usuarioNome' => $row->usu_dssnome,
                              'usuarioQuantidadeChamados' => $row->sit_qtichamados,
                              'usuarioQuantidadeAtividades' => $row->sit_qtiatividades);
            
            array_push($registros, $registro);
        }
        
        return $registros;
    }
    
}