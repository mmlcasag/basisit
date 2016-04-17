<?php

class ChamadosModel {
    
    private function populateVo($connection, $row) {
        $usuarioModel = new UsuariosModel();
        $usuarioVo = $usuarioModel->loadById($connection, $row->cha_cdiusuario);
        $requisitanteVo = $usuarioModel->loadById($connection, $row->cha_cdiusuario_requisitante);
        $atendenteVo = $usuarioModel->loadById($connection, $row->cha_cdiusuario_atendente);
        
        $situacaoModel = new SituacoesModel();
        $situacaoVo = $situacaoModel->loadById($connection, $row->cha_cdisituacao);
        
        $empresaModel = new EmpresasModel();
        $empresaVo = $empresaModel->loadById($connection, $row->cha_cdiempresa);
        
        $categoriaModel = new CategoriasModel();
        $categoriaVo = $categoriaModel->loadById($connection, $row->cha_cdicategoria);
        
        $tipoAmbienteModel = new TiposAmbientesModel();
        $tipoAmbienteVo = $tipoAmbienteModel->loadById($connection, $row->cha_cditipoambiente);
        
        $tipoProdutoModel = new TiposProdutosModel();
        $tipoProdutoVo = $tipoProdutoModel->loadById($connection, $row->cha_cditipoproduto);
        
        $modulosModel = new ModulosModel();
        $moduloVo = $modulosModel->loadById($connection, $row->cha_cdimodulo);
        
        $prioridadeModel = new PrioridadesModel();
        $prioridadeVo = $prioridadeModel->loadById($connection, $row->cha_cdiprioridade);
        
        $vo = new ChamadosVo();
        
        $vo->setId($row->cha_cdichamado);
        $vo->setUsuario($usuarioVo);
        $vo->setRequisitante($requisitanteVo);
        $vo->setAtendente($atendenteVo);
        $vo->setData(Functions::toDate($row->cha_dtdcriacao));
        $vo->setSituacao($situacaoVo);
        $vo->setEmpresa($empresaVo);
        $vo->setCategoria($categoriaVo);
        $vo->setTipoAmbiente($tipoAmbienteVo);
        $vo->setTipoProduto($tipoProdutoVo);
        $vo->setModulo($moduloVo);
        $vo->setPrioridade($prioridadeVo);
        $vo->setImpacto($row->cha_oplimpacto);
        $vo->setUsuariosAfetados($row->cha_nuiusuariosafetados);
        $vo->setAreasAfetadas($row->cha_nuiareasafetadas);
        $vo->setPrevisaoTermino(Functions::toDate($row->cha_dtdprevisaotermino));
        $vo->setAssunto($row->cha_dssassunto);
        $vo->setObservacao($row->cha_dsbobservacao);
        
        return $vo;
    }
    
    public function load($connection) {
        $registros = array();
        
        $query = " SELECT *
		   FROM   chamados
                   WHERE  1 = 1
		   ORDER  BY cha_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $vo = $this->populateVo($connection, $row);
            
            array_push($registros, $vo);
        }
        
        return $registros;
    }
    
    public function loadByCriteria($connection, $codigo = null, $usuario = null, $requisitante = null, $atendente = null, $dataIni = null, $dataFim = null, $situacao = null, $empresa = null, $categoria = null, $tipoAmbiente = null, $tipoProduto = null, $modulo = null, $prioridade = null, $impacto = null, $previsaoTerminoIni = null, $previsaoTerminoFim = null, $assunto = null, $observacao = null, $especial = null, $especialCliente = null) {
        $registros = array();
        
        $query = " SELECT *
		   FROM   chamados
		   WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($codigo)) {
            $query = $query . " AND cha_cdichamado = :cha_cdichamado ";
        }
        if (!Functions::isEmpty($usuario)) {
            $query = $query . " AND cha_cdiusuario = :cha_cdiusuario ";
        }
        if (!Functions::isEmpty($requisitante)) {
            $query = $query . " AND cha_cdiusuario_requisitante = :cha_cdiusuario_requisitante ";
        }
        if (!Functions::isEmpty($atendente)) {
            $query = $query . " AND cha_cdiusuario_atendente = :cha_cdiusuario_atendente ";
        }
        if (!Functions::isEmpty($dataIni)) {
            $query = $query . " AND cha_dtdcriacao >= :cha_dtdcricaoini ";
        }
        if (!Functions::isEmpty($dataFim)) {
            $query = $query . " AND cha_dtdcriacao <= :cha_dtdcricaofim ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query = $query . " AND cha_cdisituacao = :cha_cdisituacao ";
        }
        if (!Functions::isEmpty($empresa)) {
            $query = $query . " AND cha_cdiempresa = :cha_cdiempresa ";
        }
        if (!Functions::isEmpty($categoria)) {
            $query = $query . " AND cha_cdicategoria = :cha_cdicategoria ";
        }
        if (!Functions::isEmpty($tipoAmbiente)) {
            $query = $query . " AND cha_cditipoambiente = :cha_cditipoambiente ";
        }
        if (!Functions::isEmpty($tipoProduto)) {
            $query = $query . " AND cha_cditipoproduto = :cha_cditipoproduto ";
        }
        if (!Functions::isEmpty($modulo)) {
            $query = $query . " AND cha_cdimodulo = :cha_cdimodulo ";
        }
        if (!Functions::isEmpty($prioridade)) {
            $query = $query . " AND cha_cdiprioridade = :cha_cdiprioridade ";
        }
        if (!Functions::isEmpty($impacto)) {
            $query = $query . " AND cha_oplimpacto = :cha_oplimpacto ";
        }
        if (!Functions::isEmpty($previsaoTerminoIni)) {
            $query = $query . " AND cha_dtdprevisaotermino >= :cha_dtdprevisaoterminoini ";
        }
        if (!Functions::isEmpty($previsaoTerminoFim)) {
            $query = $query . " AND cha_dtdprevisaotermino <= :cha_dtdprevisaoterminofim ";
        }
        if (!Functions::isEmpty($assunto)) {
            $query = $query . " AND lower(cha_dssassunto) LIKE :cha_dssassunto ";
        }
        if (!Functions::isEmpty($observacao)) {
            $query = $query . " AND lower(cha_dsbobservacao) LIKE :cha_dsbobservacao ";
        }
        if (!Functions::isEmpty($especial)) {
            $query = $query . " AND cha_cdisituacao NOT IN (" . $_SESSION['situacaoFinalizada'] . "," . $_SESSION['situacaoCancelada'] . ") ";
        }
        if (!Functions::isEmpty($especialCliente)) {
            $query = $query . " AND cha_cdiusuario_atendente IN ( SELECT usu_cdiusuario FROM usuarios, perfis WHERE prf_cdiperfil = usu_cdiperfil AND prf_oplcliente = 0 ) ";
        }
        
        $query = $query . " ORDER  BY cha_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($codigo)) {
            $stmt->bindParam(':cha_cdichamado', $codigo);
        }
        if (!Functions::isEmpty($usuario)) {
            $stmt->bindParam(':cha_cdiusuario', $usuario);
        }
        if (!Functions::isEmpty($requisitante)) {
            $stmt->bindParam(':cha_cdiusuario_requisitante', $requisitante);
        }
        if (!Functions::isEmpty($atendente)) {
            $stmt->bindParam(':cha_cdiusuario_atendente', $atendente);
        }
        if (!Functions::isEmpty($dataIni)) {
            $stmt->bindParam(':cha_dtdcricaoini', Functions::toDateToSql($dataIni));
        }
        if (!Functions::isEmpty($dataFim)) {
            $stmt->bindParam(':cha_dtdcricaofim', Functions::toDateToSql($dataFim));
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':cha_cdisituacao', $situacao);
        }
        if (!Functions::isEmpty($empresa)) {
            $stmt->bindParam(':cha_cdiempresa', $empresa);
        }
        if (!Functions::isEmpty($categoria)) {
            $stmt->bindParam(':cha_cdicategoria', $categoria);
        }
        if (!Functions::isEmpty($tipoAmbiente)) {
            $stmt->bindParam(':cha_cditipoambiente', $tipoAmbiente);
        }
        if (!Functions::isEmpty($tipoProduto)) {
            $stmt->bindParam(':cha_cditipoproduto', $tipoProduto);
        }
        if (!Functions::isEmpty($modulo)) {
            $stmt->bindParam(':cha_cdimodulo', $modulo);
        }
        if (!Functions::isEmpty($prioridade)) {
            $stmt->bindParam(':cha_cdiprioridade', $prioridade);
        }
        if (!Functions::isEmpty($impacto)) {
            $stmt->bindParam(':cha_oplimpacto', $impacto);
        }
        if (!Functions::isEmpty($previsaoTerminoIni)) {
            $stmt->bindParam(':cha_dtdprevisaoterminoini', Functions::toDateToSql($previsaoTerminoIni));
        }
        if (!Functions::isEmpty($previsaoTerminoFim)) {
            $stmt->bindParam(':cha_dtdprevisaoterminofim', Functions::toDateToSql($previsaoTerminoFim));
        }
        if (!Functions::isEmpty($assunto)) {
            $stmt->bindParam(':cha_dssassunto', "%".strtolower($assunto)."%");
        }
        if (!Functions::isEmpty($observacao)) {
            $stmt->bindParam(':cha_dsbobservacao', "%".strtolower($observacao)."%");
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
		   FROM   chamados 
		   WHERE  cha_cdichamado = :cha_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':cha_cdichamado', $codigo);
        
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if (!$row) {
            return new ChamadosVo();
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
        $query = " INSERT INTO chamados
                     ( cha_cdiusuario
                     , cha_cdiusuario_requisitante
                     , cha_cdiusuario_atendente
                     , cha_dtdcriacao
                     , cha_cdisituacao
                     , cha_cdiempresa
                     , cha_cdicategoria
		     , cha_cditipoambiente
                     , cha_cditipoproduto
                     , cha_cdimodulo
                     , cha_cdiprioridade
                     , cha_oplimpacto
                     , cha_nuiusuariosafetados
                     , cha_nuiareasafetadas
		     , cha_dtdprevisaotermino
                     , cha_dssassunto
                     , cha_dsbobservacao
                     )
		   VALUES
                     ( :cha_cdiusuario
                     , :cha_cdiusuario_requisitante
                     , :cha_cdiusuario_atendente
                     , :cha_dtdcriacao
                     , :cha_cdisituacao
                     , :cha_cdiempresa
                     , :cha_cdicategoria
		     , :cha_cditipoambiente
                     , :cha_cditipoproduto
                     , :cha_cdimodulo
                     , :cha_cdiprioridade
                     , :cha_oplimpacto
		     , :cha_nuiusuariosafetados
                     , :cha_nuiareasafetadas
                     , :cha_dtdprevisaotermino
                     , :cha_dssassunto
                     , :cha_dsbobservacao
                     ) ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':cha_cdiusuario', $vo->getUsuario()->getId());
        $stmt->bindParam(':cha_cdiusuario_requisitante', $vo->getRequisitante()->getId());
        $stmt->bindParam(':cha_cdiusuario_atendente', $vo->getAtendente()->getId());
        $stmt->bindParam(':cha_dtdcriacao', Functions::toDateToSql($vo->getData()));
        $stmt->bindParam(':cha_cdisituacao', $vo->getSituacao()->getId());
        $stmt->bindParam(':cha_cdiempresa', $vo->getEmpresa()->getId());
        $stmt->bindParam(':cha_cdicategoria', $vo->getCategoria()->getId());
        $stmt->bindParam(':cha_cditipoambiente', $vo->getTipoAmbiente()->getId());
        $stmt->bindParam(':cha_cditipoproduto', $vo->getTipoProduto()->getId());
        $stmt->bindParam(':cha_cdimodulo', $vo->getModulo()->getId());
        $stmt->bindParam(':cha_cdiprioridade', $vo->getPrioridade()->getId());
        $stmt->bindParam(':cha_oplimpacto', $vo->getImpacto());
        $stmt->bindParam(':cha_nuiusuariosafetados', $vo->getUsuariosAfetados());
        $stmt->bindParam(':cha_nuiareasafetadas', $vo->getAreasAfetadas());
        $stmt->bindParam(':cha_dtdprevisaotermino', Functions::toDateToSql($vo->getPrevisaoTermino()));
        $stmt->bindParam(':cha_dssassunto', $vo->getAssunto());
        $stmt->bindParam(':cha_dsbobservacao', $vo->getObservacao());
        
        $stmt->execute();
        
        $sequence = " SELECT last_insert_id() AS id ";
        
        foreach ($connection->query($sequence) as $row) {
            return $row->id;
        }
    }
    
    public function update($connection, $vo) {
        $query = " UPDATE chamados
                   SET    cha_cdiusuario              = :cha_cdiusuario
                   ,      cha_cdiusuario_requisitante = :cha_cdiusuario_requisitante
                   ,      cha_cdiusuario_atendente    = :cha_cdiusuario_atendente
                   ,      cha_dtdcriacao              = :cha_dtdcriacao
                   ,  	  cha_cdisituacao             = :cha_cdisituacao
                   ,      cha_cdiempresa              = :cha_cdiempresa
                   ,      cha_cdicategoria            = :cha_cdicategoria
                   ,      cha_cditipoambiente         = :cha_cditipoambiente
                   ,      cha_cditipoproduto          = :cha_cditipoproduto
                   ,      cha_cdimodulo               = :cha_cdimodulo
                   ,      cha_cdiprioridade           = :cha_cdiprioridade
                   ,      cha_oplimpacto              = :cha_oplimpacto
                   ,      cha_nuiusuariosafetados     = :cha_nuiusuariosafetados
                   ,      cha_nuiareasafetadas        = :cha_nuiareasafetadas
                   ,      cha_dtdprevisaotermino      = :cha_dtdprevisaotermino
                   ,      cha_dssassunto              = :cha_dssassunto
                   ,      cha_dsbobservacao           = :cha_dsbobservacao
                   WHERE  cha_cdichamado              = :cha_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':cha_cdiusuario', $vo->getUsuario()->getId());
        $stmt->bindParam(':cha_cdiusuario_requisitante', $vo->getRequisitante()->getId());
        $stmt->bindParam(':cha_cdiusuario_atendente', $vo->getAtendente()->getId());
        $stmt->bindParam(':cha_dtdcriacao', Functions::toDateToSql($vo->getData()));
        $stmt->bindParam(':cha_cdisituacao', $vo->getSituacao()->getId());
        $stmt->bindParam(':cha_cdiempresa', $vo->getEmpresa()->getId());
        $stmt->bindParam(':cha_cdicategoria', $vo->getCategoria()->getId());
        $stmt->bindParam(':cha_cditipoambiente', $vo->getTipoAmbiente()->getId());
        $stmt->bindParam(':cha_cditipoproduto', $vo->getTipoProduto()->getId());
        $stmt->bindParam(':cha_cdimodulo', $vo->getModulo()->getId());
        $stmt->bindParam(':cha_cdiprioridade', $vo->getPrioridade()->getId());
        $stmt->bindParam(':cha_oplimpacto', $vo->getImpacto());
        $stmt->bindParam(':cha_nuiusuariosafetados', $vo->getUsuariosAfetados());
        $stmt->bindParam(':cha_nuiareasafetadas', $vo->getAreasAfetadas());
        $stmt->bindParam(':cha_dtdprevisaotermino', Functions::toDateToSql($vo->getPrevisaoTermino()));
        $stmt->bindParam(':cha_dssassunto', $vo->getAssunto());
        $stmt->bindParam(':cha_dsbobservacao', $vo->getObservacao());
        $stmt->bindParam(':cha_cdichamado', $vo->getId());
        
        $stmt->execute();
        
        return $vo->getId();
    }
    
    public function delete($connection, $vo) {
        $query = " DELETE FROM chamados WHERE cha_cdichamado = :cha_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->bindParam(':cha_cdichamado', $vo->getId());
        
        $stmt->execute();
    }
    
    public function loadNaoClassificados($connection) {
        $registros = array();
        
        $query = " SELECT *
		   FROM   chamados
		   WHERE  cha_cdiusuario_atendente IS NULL
                   AND    cha_cdisituacao NOT IN (" . $_SESSION['situacaoCancelada'] . ")
                   ORDER  BY cha_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $vo = $this->populateVo($connection, $row);
            
            array_push($registros, $vo);
        }
        
        return $registros;
    }
    
    public function loadRelatorioAtendimentosSintetico($connection, $periodoInicial, $periodoFinal, $empresa, $situacao, $tipoAvaliacao) {
        $registros = array();
        
        $query = "  SELECT ch.cha_cdichamado, ch.cha_dtdcriacao
                    ,      us.usu_dssnome, ep.emp_dssempresa
                    ,      st.sit_dsssituacao, cg.cat_dsscategoria
                    ,      ta.tpa_dsstipoambiente, tp.tpp_dsstipoproduto
                    ,      md.mod_dssmodulo, ch.cha_dssassunto
                    ,      SEC_TO_TIME(SUM(TIME_TO_SEC(ap.apo_hrsfaturadas))) apo_hrsfaturadas
                    FROM   chamados            ch
                    INNER  JOIN apontamentos   ap ON ap.apo_cdichamado      = ch.cha_cdichamado
                    LEFT   JOIN usuarios       us ON us.usu_cdiusuario      = ch.cha_cdiusuario
                    LEFT   JOIN empresas       ep ON ep.emp_cdiempresa      = ch.cha_cdiempresa
                    LEFT   JOIN situacoes      st ON st.sit_cdisituacao     = ch.cha_cdisituacao
                    LEFT   JOIN categorias     cg ON cg.cat_cdicategoria    = ch.cha_cdicategoria
                    LEFT   JOIN tiposambientes ta ON ta.tpa_cditipoambiente = ch.cha_cditipoambiente
                    LEFT   JOIN tiposprodutos  tp ON tp.tpp_cditipoproduto  = ch.cha_cditipoproduto
                    LEFT   JOIN modulos        md ON md.mod_cdimodulo       = ch.cha_cdimodulo
                    WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($periodoInicial)) {
            $query .= " AND date_format(ch.cha_dtdcriacao, '%Y-%m-%d') >= :periodoInicial ";
        }
        if (!Functions::isEmpty($periodoFinal)) {
            $query .= " AND date_format(ch.cha_dtdcriacao, '%Y-%m-%d') <= :periodoFinal ";
        }
        if (!Functions::isEmpty($empresa)) {
            $query .= " AND ep.emp_cdiempresa = :empresa ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND st.sit_cdisituacao = :situacao ";
        }
        if (!Functions::isEmpty($tipoAvaliacao)) {
            if ($tipoAvaliacao == 1) {
                $query .= " AND ap.apo_cdimodofaturamento in (1,2,3) ";
            } else if ($tipoAvaliacao == 2) {
                $query .= " AND ap.apo_cdimodofaturamento in (1,2) ";
            } else if ($tipoAvaliacao == 3) {
                $query .= " AND ap.apo_cdimodofaturamento in (3) ";
            }
        }
        
        $query .= " GROUP BY ch.cha_cdichamado, ch.cha_dtdcriacao
                    ,        us.usu_dssnome, ep.emp_dssempresa
                    ,        st.sit_dsssituacao, cg.cat_dsscategoria
                    ,        ta.tpa_dsstipoambiente, tp.tpp_dsstipoproduto
                    ,        md.mod_dssmodulo, ch.cha_dssassunto
                    ORDER BY ch.cha_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($periodoInicial)) {
            $stmt->bindParam(':periodoInicial', Functions::toDateToSql($periodoInicial));
        }
        if (!Functions::isEmpty($periodoFinal)) {
            $stmt->bindParam(':periodoFinal', Functions::toDateToSql($periodoFinal));
        }
        if (!Functions::isEmpty($empresa)) {
            $stmt->bindParam(':empresa', $empresa);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':situacao', $situacao);
        }
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            if (Functions::isEmpty($row->apo_hrsfaturadas)) {
                $horasFaturadas = '00:00:00';
            } else {
                $horasFaturadas = $row->apo_hrsfaturadas;
            }
            
            $registro = array( 'chamadoCodigo' => $row->cha_cdichamado
                             , 'chamadoData' => Functions::toDate($row->cha_dtdcriacao)
                             , 'usuarioNome' => $row->usu_dssnome
                             , 'empresaDescricao' => $row->emp_dssempresa
                             , 'situacaoDescricao' => $row->sit_dsssituacao
                             , 'categoriaDescricao' => $row->cat_dsscategoria
                             , 'tipoAmbienteDescricao' => $row->tpa_dsstipoambiente
                             , 'tipoProdutoDescricao' => $row->tpp_dsstipoproduto
                             , 'moduloDescricao' => $row->mod_dssmodulo
                             , 'chamadoAssunto' => $row->cha_dssassunto
                             , 'horasFaturadas' => $horasFaturadas
                             ) ;
            
            array_push($registros, $registro);
        }
        
        return $registros;
        
    }
    
    public function loadRelatorioAtendimentosAnalitico($connection, $periodoInicial, $periodoFinal, $empresa, $situacao, $tipoAvaliacao) {
        $registros = array();
        
        $query = "  SELECT ch.cha_cdichamado, ch.cha_dtdcriacao
                    ,      us.usu_dssnome, ep.emp_dssempresa
                    ,      st.sit_dsssituacao, cg.cat_dsscategoria
                    ,      ta.tpa_dsstipoambiente, tp.tpp_dsstipoproduto
                    ,      md.mod_dssmodulo, ch.cha_dssassunto
                    ,      SEC_TO_TIME(SUM(TIME_TO_SEC(ap.apo_hrsfaturadas))) apo_hrsfaturadas
                    FROM   chamados            ch
                    INNER  JOIN apontamentos   ap ON ap.apo_cdichamado      = ch.cha_cdichamado
                    LEFT   JOIN usuarios       us ON us.usu_cdiusuario      = ch.cha_cdiusuario
                    LEFT   JOIN empresas       ep ON ep.emp_cdiempresa      = ch.cha_cdiempresa
                    LEFT   JOIN situacoes      st ON st.sit_cdisituacao     = ch.cha_cdisituacao
                    LEFT   JOIN categorias     cg ON cg.cat_cdicategoria    = ch.cha_cdicategoria
                    LEFT   JOIN tiposambientes ta ON ta.tpa_cditipoambiente = ch.cha_cditipoambiente
                    LEFT   JOIN tiposprodutos  tp ON tp.tpp_cditipoproduto  = ch.cha_cditipoproduto
                    LEFT   JOIN modulos        md ON md.mod_cdimodulo       = ch.cha_cdimodulo
                    WHERE  1 = 1 ";
        
        if (!Functions::isEmpty($periodoInicial)) {
            $query .= " AND date_format(ch.cha_dtdcriacao, '%Y-%m-%d') >= :periodoInicial ";
        }
        if (!Functions::isEmpty($periodoFinal)) {
            $query .= " AND date_format(ch.cha_dtdcriacao, '%Y-%m-%d') <= :periodoFinal ";
        }
        if (!Functions::isEmpty($empresa)) {
            $query .= " AND ep.emp_cdiempresa = :empresa ";
        }
        if (!Functions::isEmpty($situacao)) {
            $query .= " AND st.sit_cdisituacao = :situacao ";
        }
        if (!Functions::isEmpty($tipoAvaliacao)) {
            if ($tipoAvaliacao == 1) {
                $query .= " AND ap.apo_cdimodofaturamento in (1,2,3) ";
            } else if ($tipoAvaliacao == 2) {
                $query .= " AND ap.apo_cdimodofaturamento in (1,2) ";
            } else if ($tipoAvaliacao == 3) {
                $query .= " AND ap.apo_cdimodofaturamento in (3) ";
            }
        }
        
        $query .= " GROUP BY ch.cha_cdichamado, ch.cha_dtdcriacao
                    ,        us.usu_dssnome, ep.emp_dssempresa
                    ,        st.sit_dsssituacao, cg.cat_dsscategoria
                    ,        ta.tpa_dsstipoambiente, tp.tpp_dsstipoproduto
                    ,        md.mod_dssmodulo, ch.cha_dssassunto
                    ORDER BY ch.cha_cdichamado ";
        
        $stmt = $connection->prepare($query);
        
        if (!Functions::isEmpty($periodoInicial)) {
            $stmt->bindParam(':periodoInicial', Functions::toDateToSql($periodoInicial));
        }
        if (!Functions::isEmpty($periodoFinal)) {
            $stmt->bindParam(':periodoFinal', Functions::toDateToSql($periodoFinal));
        }
        if (!Functions::isEmpty($empresa)) {
            $stmt->bindParam(':empresa', $empresa);
        }
        if (!Functions::isEmpty($situacao)) {
            $stmt->bindParam(':situacao', $situacao);
        }
        
        $stmt->execute();
        
        $rows = $stmt->fetchAll();
        
        foreach ($rows as $row) {
            $chamadosHistoricosModel = new ChamadosHistoricosModel();
            $chamadosHistoricosArray = $chamadosHistoricosModel->loadByChamado($connection, $row->cha_cdichamado);
            
            if (Functions::isEmpty($row->apo_hrsfaturadas)) {
                $horasFaturadas = '00:00:00';
            } else {
                $horasFaturadas = $row->apo_hrsfaturadas;
            }
            
            $registro = array( 'chamadoCodigo' => $row->cha_cdichamado
                             , 'chamadoData' => Functions::toDate($row->cha_dtdcriacao)
                             , 'usuarioNome' => $row->usu_dssnome
                             , 'empresaDescricao' => $row->emp_dssempresa
                             , 'situacaoDescricao' => $row->sit_dsssituacao
                             , 'categoriaDescricao' => $row->cat_dsscategoria
                             , 'tipoAmbienteDescricao' => $row->tpa_dsstipoambiente
                             , 'tipoProdutoDescricao' => $row->tpp_dsstipoproduto
                             , 'moduloDescricao' => $row->mod_dssmodulo
                             , 'chamadoAssunto' => $row->cha_dssassunto
                             , 'horasFaturadas' => $horasFaturadas
                             , 'chamadoHistoricoArray' => $chamadosHistoricosArray
                             ) ;
            
            array_push($registros, $registro);
        }
        
        return $registros;
        
    }
    
}
