<?php

class ApontamentosModel {
    
    private function populateVo($connection, $row) {
        if (!$row) {
            return new ApontamentosVo();
        }
        
        $usuarioModel = new UsuariosModel();
        $usuarioVo = $usuarioModel->loadById($connection, $row->apo_cdiusuario);
        
        $atividadeModel = new AtividadesModel();
        $atividadeVo = $atividadeModel->loadById($connection, $row->apo_cdiatividade);
        
        $chamadoModel = new ChamadosModel();
        $chamadoVo = $chamadoModel->loadById($connection, $row->apo_cdichamado);
        
        $vo = new ApontamentosVo();
        
        $vo->setId($row->apo_cdiapontamento);
        $vo->setUsuario($usuarioVo);
        $vo->setAtividade($atividadeVo);
        $vo->setChamado($chamadoVo);
        $vo->setDataInicio(Functions::toDateTime($row->apo_dtdinicio));
        $vo->setDataFim(Functions::toDateTime($row->apo_dtdfim));
        $vo->setObservacao($row->apo_dsbobservacao);
        $vo->setAvaliacao($row->apo_cdimodofaturamento);
        $vo->setApontado(Functions::toTime($row->apo_hrsapontadas));
        $vo->setFaturado(Functions::toTime($row->apo_hrsfaturadas));
        
        return $vo;
    }
    
    public function load($connection, $atividadeCodigo = "", $chamadoCodigo = "") {
        $registros = array();
        
        $query = " SELECT *
                   FROM   apontamentos
                   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($atividadeCodigo)) {
            $query = $query . " AND apo_cdiatividade = " . $atividadeCodigo;
        } else if (!Functions::isEmpty($chamadoCodigo)) {
            $query = $query . " AND apo_cdichamado = " . $chamadoCodigo;
        } else {
            $query = $query . " AND 1 = 2 ";
        }
        
        $query = $query . " ORDER BY apo_cdiapontamento ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($atividadeCodigo)) {
            $stmt->bindParam(':apo_cdiatividade', $atividadeCodigo);
        } else if (!Functions::isEmpty($chamadoCodigo)) {
            $stmt->bindParam(':apo_cdichamado', $chamadoCodigo);
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
                   FROM   apontamentos
                   WHERE  apo_cdiapontamento = :apo_cdiapontamento ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':apo_cdiapontamento', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        return $this->populateVo($connection, $row);
    }
    
    public function loadUltimaAberta($connection, $usuarioCodigo, $tipo) {
        $query = " SELECT *
                   FROM   apontamentos
                   WHERE  apo_dtdfim     LIKE '%0000%'
                   AND    apo_cdiusuario    = " . $usuarioCodigo;
        
        if ($tipo == "A") {
            $query = $query . " AND apo_cdiatividade IS NOT NULL AND apo_cdichamado IS NULL ";
        } else if ($tipo == "C") {
            $query = $query . " AND apo_cdichamado IS NOT NULL AND apo_cdiatividade IS NULL ";
        } else {
            $query = $query . " AND 1 = 2 ";
        }
        
        $query = $query . " ORDER BY apo_cdiapontamento ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        return $this->populateVo($connection, $row);
    }
    
    public function verificaSeAberto($connection, $tipo, $codigo) {
        $query = " SELECT *
                   FROM   apontamentos
                   WHERE  apo_dtdfim LIKE '%0000%' ";
        
        if ($tipo == "A") {
            $query = $query . " AND apo_cdiatividade IS NOT NULL AND apo_cdichamado IS NULL AND apo_cdiatividade = " . $codigo;
        } else if ($tipo == "C") {
            $query = $query . " AND apo_cdichamado IS NOT NULL AND apo_cdiatividade IS NULL AND apo_cdichamado = " . $codigo;
        } else {
            $query = $query . " AND 1 = 2 ";
        }
        
        $query = $query . " ORDER BY apo_cdiapontamento ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        return $this->populateVo($connection, $row);
    }
    
    public function save($connection, $vo) {
        if (Functions::isEmpty($vo->getId())) {
            $this->insert($connection, $vo);
        } else {
            $this->update($connection, $vo);
        }
    }
    
    public function insert($connection, $vo) {
        $query = " INSERT INTO apontamentos
                      ( apo_cdiusuario
                      , apo_cdiatividade
                      , apo_cdichamado
                      , apo_dtdinicio
                      , apo_dtdfim
                      , apo_dsbobservacao
                      , apo_cdimodofaturamento
                      , apo_hrsapontadas
                      , apo_hrsfaturadas
                      )
                   VALUES
                      ( :apo_cdiusuario
                      , :apo_cdiatividade
                      , :apo_cdichamado
                      , :apo_dtdinicio
                      , :apo_dtdfim
                      , :apo_dsbobservacao
                      , :apo_cdimodofaturamento
                      , TIMEDIFF(:apo_dtdfim, :apo_dtdinicio)
                      , :apo_hrsfaturadas
                   ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':apo_cdiusuario', $vo->getUsuario()->getId());
        $stmt->bindParam(':apo_cdiatividade', $vo->getAtividade()->getId());
        $stmt->bindParam(':apo_cdichamado', $vo->getChamado()->getId());
        $stmt->bindParam(':apo_dtdinicio', Functions::toDateTimeToSql($vo->getDataInicio()));
        $stmt->bindParam(':apo_dtdfim', Functions::toDateTimeToSql($vo->getDataFim()));
        $stmt->bindParam(':apo_dsbobservacao', $vo->getObservacao());
        $stmt->bindParam(':apo_cdimodofaturamento', $vo->getAvaliacao());
        $stmt->bindParam(':apo_hrsfaturadas', $vo->getFaturado());
        
        $stmt->execute();
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE apontamentos
                   SET    apo_cdiusuario         = :apo_cdiusuario
                   ,      apo_cdiatividade       = :apo_cdiatividade
                   ,      apo_cdichamado         = :apo_cdichamado
                   ,  	  apo_dtdinicio          = :apo_dtdinicio
                   ,  	  apo_dtdfim             = :apo_dtdfim
                   ,  	  apo_dsbobservacao      = :apo_dsbobservacao
                   ,  	  apo_cdimodofaturamento = :apo_cdimodofaturamento
                   ,      apo_hrsapontadas       = TIMEDIFF(:apo_dtdfim, :apo_dtdinicio)
                   ,  	  apo_hrsfaturadas       = :apo_hrsfaturadas
                   WHERE  apo_cdiapontamento     = :apo_cdiapontamento ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':apo_cdiusuario', $vo->getUsuario()->getId());
        $stmt->bindParam(':apo_cdiatividade', $vo->getAtividade()->getId());
        $stmt->bindParam(':apo_cdichamado', $vo->getChamado()->getId());
        $stmt->bindParam(':apo_dtdinicio', Functions::toDateTimeToSql($vo->getDataInicio()));
        $stmt->bindParam(':apo_dtdfim', Functions::toDateTimeToSql($vo->getDataFim()));
        $stmt->bindParam(':apo_dsbobservacao', $vo->getObservacao());
        $stmt->bindParam(':apo_cdimodofaturamento', $vo->getAvaliacao());
        $stmt->bindParam(':apo_hrsfaturadas', $vo->getFaturado());
        $stmt->bindParam(':apo_cdiapontamento', $vo->getId());
        
        $stmt->execute();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM apontamentos WHERE apo_cdiapontamento = :apo_cdiapontamento ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':apo_cdiapontamento', $vo->getId());
        
        $stmt->execute();
    }
    
    public function loadFaturamentoPorFuncionario($connection, $periodoInicial, $periodoFinal, $usuarioCodigo = "") {
        $registros = array();
        
        $query = " SELECT a.usu_cdiusuario
                   ,      a.usu_dssnome
                   ,      SEC_TO_TIME(SUM(TIME_TO_SEC(a.apo_hrsapontadas))) apo_hrsapontadas
                   ,      SEC_TO_TIME(SUM(TIME_TO_SEC(a.apo_hrsfaturadas))) apo_hrsfaturadas
                   FROM   (
                       
                       SELECT us.usu_cdiusuario
                       ,      us.usu_dssnome
                       ,      SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(ap.apo_dtdfim, ap.apo_dtdinicio)))) apo_hrsapontadas
                       ,      SEC_TO_TIME(SUM(TIME_TO_SEC(ap.apo_hrsfaturadas))) apo_hrsfaturadas
                       FROM   apontamentos ap
                       JOIN   atividades   at ON at.ati_cdiatividade = ap.apo_cdiatividade
                       JOIN   usuarios     us ON us.usu_cdiusuario   = ap.apo_cdiusuario
                       WHERE  date_format(ap.apo_dtdinicio, '%Y-%m-%d') BETWEEN :apo_dtdinicioini AND :apo_dtdiniciofim
                       AND    date_format(ap.apo_dtdfim   , '%Y-%m-%d') BETWEEN :apo_dtdfimini    AND :apo_dtdfimfim ";
                       
                       if (!Functions::isEmpty($usuarioCodigo)) {
                           $query = $query . " AND us.usu_cdiusuario = :usu_cdiusuario ";
                       }
                       
                       $query = $query . "
                       GROUP  BY us.usu_cdiusuario, us.usu_dssnome
                       
                       UNION ALL
                       
                       SELECT us.usu_cdiusuario
                       ,      us.usu_dssnome
                       ,      SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(ap.apo_dtdfim, ap.apo_dtdinicio)))) apo_hrsapontadas
                       ,      SEC_TO_TIME(SUM(TIME_TO_SEC(ap.apo_hrsfaturadas))) apo_hrsfaturadas
                       FROM   apontamentos ap
                       JOIN   chamados     ch ON ch.cha_cdichamado   = ap.apo_cdichamado
                       JOIN   usuarios     us ON us.usu_cdiusuario   = ap.apo_cdiusuario
                       WHERE  date_format(ap.apo_dtdinicio, '%Y-%m-%d') BETWEEN :apo_dtdinicioini AND :apo_dtdiniciofim
                       AND    date_format(ap.apo_dtdfim   , '%Y-%m-%d') BETWEEN :apo_dtdfimini    AND :apo_dtdfimfim ";
                       
                       if (!Functions::isEmpty($usuarioCodigo)) {
                           $query = $query . " AND us.usu_cdiusuario = :usu_cdiusuario ";
                       }
                       
                       $query = $query . "
                       GROUP  BY us.usu_cdiusuario, us.usu_dssnome
                       
                   ) a
                   
                   GROUP BY a.usu_cdiusuario, a.usu_dssnome
                   ORDER BY 2 ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':apo_dtdinicioini', Functions::toDateToSql($periodoInicial));
        $stmt->bindParam(':apo_dtdiniciofim', Functions::toDateToSql($periodoFinal));
        $stmt->bindParam(':apo_dtdfimini', Functions::toDateToSql($periodoInicial));
        $stmt->bindParam(':apo_dtdfimfim', Functions::toDateToSql($periodoFinal));
        
        if (!Functions::isEmpty($usuarioCodigo)) {
            $stmt->bindParam(':usu_cdiusuario', $usuarioCodigo);
        }
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $registro = array('usuarioCodigo' => $row->usu_cdiusuario,
                              'usuarioNome' => $row->usu_dssnome,
                              'apontamentoDuracao' => $row->apo_hrsapontadas,
                              'apontamentoFaturado' => $row->apo_hrsfaturadas);
            
            array_push($registros, $registro);
        }
        
        return $registros;
    }
    
    public function loadFaturamentoPorEmpresa($connection, $periodoInicial, $periodoFinal, $empresaCodigo = "") {
        $registros = array();
        
        $query = " SELECT a.emp_cdiempresa
                   ,      a.emp_dssempresa
                   ,      SEC_TO_TIME(SUM(TIME_TO_SEC(a.apo_hrsapontadas))) apo_hrsapontadas
                   ,      SEC_TO_TIME(SUM(TIME_TO_SEC(a.apo_hrsfaturadas))) apo_hrsfaturadas
                   FROM   (
                       
                       SELECT ep.emp_cdiempresa
                       ,      ep.emp_dssempresa
                       ,      SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(ap.apo_dtdfim, ap.apo_dtdinicio)))) apo_hrsapontadas
                       ,      SEC_TO_TIME(SUM(TIME_TO_SEC(ap.apo_hrsfaturadas))) apo_hrsfaturadas
                       FROM   apontamentos ap
                       JOIN   atividades   at ON at.ati_cdiatividade = ap.apo_cdiatividade
                       JOIN   empresas     ep ON ep.emp_cdiempresa   = at.ati_cdiempresa
                       WHERE  date_format(ap.apo_dtdinicio, '%Y-%m-%d') BETWEEN :apo_dtdinicioini AND :apo_dtdiniciofim
                       AND    date_format(ap.apo_dtdfim   , '%Y-%m-%d') BETWEEN :apo_dtdfimini    AND :apo_dtdfimfim ";
                       
                       if (!Functions::isEmpty($empresaCodigo)) {
                           $query = $query . " AND ep.emp_cdiempresa = :emp_cdiempresa ";
                       }
                       
                       $query = $query . "
                       GROUP  BY ep.emp_cdiempresa, ep.emp_dssempresa
                       
                       UNION ALL
                       
                       SELECT ep.emp_cdiempresa
                       ,      ep.emp_dssempresa
                       ,      SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(ap.apo_dtdfim, ap.apo_dtdinicio)))) apo_hrsapontadas
                       ,      SEC_TO_TIME(SUM(TIME_TO_SEC(ap.apo_hrsfaturadas))) apo_hrsfaturadas
                       FROM   apontamentos ap
                       JOIN   chamados     ch ON ch.cha_cdichamado   = ap.apo_cdichamado
                       JOIN   empresas     ep ON ep.emp_cdiempresa   = ch.cha_cdiempresa
                       WHERE  date_format(ap.apo_dtdinicio, '%Y-%m-%d') BETWEEN :apo_dtdinicioini AND :apo_dtdiniciofim
                       AND    date_format(ap.apo_dtdfim   , '%Y-%m-%d') BETWEEN :apo_dtdfimini    AND :apo_dtdfimfim ";
                       
                       if (!Functions::isEmpty($empresaCodigo)) {
                           $query = $query . " AND ep.emp_cdiempresa = :emp_cdiempresa ";
                       }
                       
                       $query = $query . "
                       GROUP  BY ep.emp_cdiempresa, ep.emp_dssempresa
                       
                   ) a
                   
                   GROUP BY a.emp_cdiempresa, a.emp_dssempresa
                   ORDER BY 2 ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':apo_dtdinicioini', Functions::toDateToSql($periodoInicial));
        $stmt->bindParam(':apo_dtdiniciofim', Functions::toDateToSql($periodoFinal));
        $stmt->bindParam(':apo_dtdfimini', Functions::toDateToSql($periodoInicial));
        $stmt->bindParam(':apo_dtdfimfim', Functions::toDateToSql($periodoFinal));
        
        if (!Functions::isEmpty($empresaCodigo)) {
            $stmt->bindParam(':emp_cdiempresa', $empresaCodigo);
        }
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $registro = array('empresaCodigo' => $row->emp_cdiempresa,
                              'empresaDescricao' => $row->emp_dssempresa,
                              'apontamentoDuracao' => $row->apo_hrsapontadas,
                              'apontamentoFaturado' => $row->apo_hrsfaturadas);

            array_push($registros, $registro);
        }
        
        return $registros;
    }
    
    public function loadApontamentosParaAvaliacao($connection, $periodoInicial, $periodoFinal, $usuarioCodigo = "", $empresaCodigo = "", $apontamentoAvaliacao = "", $apontamentoTipo = "") {
        $registros = array();
        
        $query = "  SELECT 'Atividade' apo_dsstipoapontamento
                    ,      at.ati_cdiatividade    ,      ap.apo_cdiapontamento
                    ,      ta.tpt_cditipoatividade,      ta.tpt_dsstipoatividade	
                    ,      ep.emp_cdiempresa      ,      ep.emp_dssempresa
                    ,      st.sit_cdisituacao     ,      st.sit_dsssituacao
                    ,      us.usu_cdiusuario      ,      us.usu_dssnome
                    ,      ap.apo_dtdinicio       ,      ap.apo_dtdfim
                    ,      ap.apo_dsbobservacao   ,      ap.apo_cdimodofaturamento
                    ,      timediff(ap.apo_dtdfim, ap.apo_dtdinicio) apo_hrsapontadas, ap.apo_hrsfaturadas
                    FROM   apontamentos    ap
                    JOIN   usuarios        us ON us.usu_cdiusuario       = ap.apo_cdiusuario
                    JOIN   atividades      at ON at.ati_cdiatividade     = ap.apo_cdiatividade
                    JOIN   tiposatividades ta ON ta.tpt_cditipoatividade = at.ati_cditipoatividade
                    JOIN   empresas        ep ON ep.emp_cdiempresa       = at.ati_cdiempresa    
                    JOIN   situacoes       st ON st.sit_cdisituacao      = at.ati_cdisituacao
                    WHERE  date_format(ap.apo_dtdinicio, '%Y-%m-%d') BETWEEN :apo_dtdinicioini AND :apo_dtdiniciofim
                    AND    date_format(ap.apo_dtdfim   , '%Y-%m-%d') BETWEEN :apo_dtdfimini    AND :apo_dtdfimfim ";
        
        if (!Functions::isEmpty($usuarioCodigo)) {
            $query = $query . " AND us.usu_cdiusuario = :usu_cdiusuario ";
        }
        
        if (!Functions::isEmpty($empresaCodigo)) {
            $query = $query . " AND ep.emp_cdiempresa = :emp_cdiempresa ";
        }
        
        if ($apontamentoAvaliacao != "") {
            $query = $query . " AND ap.apo_cdimodofaturamento = :apo_cdimodofaturamento ";
        }
        
        if (!Functions::isEmpty($apontamentoTipo)) {
            if ($apontamentoTipo == "Atividade") {
                $query = $query . " AND 1 = 1 ";
            } else {
                $query = $query . " AND 1 = 2 ";
            }
        }
        
        $query = $query . " UNION ALL
                            
                            SELECT 'Chamado' apo_dsstipoapontamento
                            ,      ch.cha_cdichamado ati_cdiatividade    , ap.apo_cdiapontamento
                            ,      ch.cha_cdichamado tpt_cditipoatividade, 'Chamado' tpt_dsstipoatividade
                            ,      ep.emp_cdiempresa      ,      ep.emp_dssempresa
                            ,      st.sit_cdisituacao     ,      st.sit_dsssituacao
                            ,      us.usu_cdiusuario      ,      us.usu_dssnome
                            ,      ap.apo_dtdinicio       ,      ap.apo_dtdfim
                            ,      ap.apo_dsbobservacao   ,      ap.apo_cdimodofaturamento
                            ,      timediff(ap.apo_dtdfim, ap.apo_dtdinicio) apo_hrsapontadas, ap.apo_hrsfaturadas
                            FROM   apontamentos    ap
                            JOIN   usuarios        us ON us.usu_cdiusuario       = ap.apo_cdiusuario
                            JOIN   chamados        ch ON ch.cha_cdichamado       = ap.apo_cdichamado
                            JOIN   tiposprodutos   ta ON ta.tpp_cditipoproduto   = ch.cha_cditipoproduto
                            JOIN   empresas        ep ON ep.emp_cdiempresa       = ch.cha_cdiempresa    
                            JOIN   situacoes       st ON st.sit_cdisituacao      = ch.cha_cdisituacao
                            WHERE  date_format(ap.apo_dtdinicio, '%Y-%m-%d') BETWEEN :apo_dtdinicioini AND :apo_dtdiniciofim
                            AND    date_format(ap.apo_dtdfim   , '%Y-%m-%d') BETWEEN :apo_dtdfimini    AND :apo_dtdfimfim ";
        
        if (!Functions::isEmpty($usuarioCodigo)) {
            $query = $query . " AND us.usu_cdiusuario = :usu_cdiusuario ";
        }
        
        if (!Functions::isEmpty($empresaCodigo)) {
            $query = $query . " AND ep.emp_cdiempresa = :emp_cdiempresa ";
        }
        
        if ($apontamentoAvaliacao != "") {
            $query = $query . " AND ap.apo_cdimodofaturamento = :apo_cdimodofaturamento ";
        }
        
        if (!Functions::isEmpty($apontamentoTipo)) {
            if ($apontamentoTipo == "Chamado") {
                $query = $query . " AND 1 = 1 ";
            } else {
                $query = $query . " AND 1 = 2 ";
            }
        }
        
        $query = $query . " ORDER BY 1, 2, 3 ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':apo_dtdinicioini', Functions::toDateToSql($periodoInicial));
        $stmt->bindParam(':apo_dtdiniciofim', Functions::toDateToSql($periodoFinal));
        $stmt->bindParam(':apo_dtdfimini', Functions::toDateToSql($periodoInicial));
        $stmt->bindParam(':apo_dtdfimfim', Functions::toDateToSql($periodoFinal));
        
        if (!Functions::isEmpty($usuarioCodigo)) {
            $stmt->bindParam(':usu_cdiusuario', $usuarioCodigo);
        }
        
        if (!Functions::isEmpty($empresaCodigo)) {
            $stmt->bindParam(':emp_cdiempresa', $empresaCodigo);
        }
        
        if ($apontamentoAvaliacao != "") {
            $stmt->bindParam(':apo_cdimodofaturamento', $apontamentoAvaliacao);
        }
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $registro = array( 'atividadeCodigo' => $row->ati_cdiatividade
                             , 'apontamentoTipo' => $row->apo_dsstipoapontamento
                             , 'apontamentoCodigo' => $row->apo_cdiapontamento
                             , 'tipoAtividadeCodigo' => $row->tpt_cditipoatividade
                             , 'tipoAtividadeDescricao' => $row->tpt_dsstipoatividade
                             , 'empresaCodigo' => $row->emp_cdiempresa
                             , 'empresaDescricao' => $row->emp_dssempresa
                             , 'situacaoCodigo' => $row->sit_cdisituacao
                             , 'situacaoDescricao' => $row->sit_dsssituacao
                             , 'usuarioCodigo' => $row->usu_cdiusuario
                             , 'usuarioNome' => $row->usu_dssnome
                             , 'apontamentoInicio' => Functions::toDateTime($row->apo_dtdinicio)
                             , 'apontamentoFim' => Functions::toDateTime($row->apo_dtdfim)
                             , 'apontamentoObservacao' => $row->apo_dsbobservacao
                             , 'apontamentoAvaliacao' => $row->apo_cdimodofaturamento
                             , 'apontamentoDuracao' => Functions::toTime($row->apo_hrsapontadas)
                             , 'apontamentoFaturado' => Functions::toTime($row->apo_hrsfaturadas));

            array_push($registros, $registro);
        }
        
        return $registros;
    }
    
    public function loadApontamentosDiasDistintos($connection, $tipoApontamento) {
        $cache = phpFastCache();
        
        $apontamentosCache = $cache->get("ApontamentosDiasDistintos" . $tipoApontamento);
        
        if ($apontamentosCache != null) {
            return $apontamentosCache;
        } else {
            $registros = array();
            
            $query = " SELECT *
                       FROM   apontamentos
                       WHERE  apo_dtdinicio NOT LIKE '%0000%'
                       AND    apo_dtdfim    NOT LIKE '%0000%'
                       AND    DAY(apo_dtdinicio) <> DAY(apo_dtdfim) ";
            
            if ((!Functions::isEmpty($tipoApontamento)) && ($tipoApontamento == "A")) {
                $query .= " AND apo_cdiatividade IS NOT NULL ";
            }
            if ((!Functions::isEmpty($tipoApontamento)) && ($tipoApontamento == "C")) {
                $query .= " AND apo_cdichamado   IS NOT NULL ";
            }
            
            $query.= " ORDER  BY apo_cdiapontamento ";
            
            $stmt = $connection->prepare($query);
            
            $stmt->execute();
            
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $vo = $this->populateVo($connection, $row);
                
                array_push($registros, $vo);
            }
            
            $cache->set("ApontamentosDiasDistintos" . $tipoApontamento, $registros, 60 * Functions::getParametro('cache'));
            
            return $registros;
        }
    }
    
}
