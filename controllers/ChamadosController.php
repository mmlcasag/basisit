<?php

class ChamadosController extends BaseController {
    
    function ChamadosController() {
        parent::__construct();
    }
    
    private function getParametroTela($parametro) {
        return filter_input(INPUT_POST, $parametro);
    }
    
    private function validarFormulario($vo) {
        if (Functions::isEmpty($vo->getData())) {
            return 'N' . 'Informe o campo "Data de Abertura"';
        } else if (!Functions::isDate($vo->getData())) {
            return 'N' . 'Valor inválido para o campo "Data de Abertura"';
        } else if (Functions::isEmpty($vo->getSituacao()->getId())) {
            return 'N' . 'Informe o campo "Situação"';
        } else if (!Functions::isInteger($vo->getSituacao()->getId())) {
            return 'N' . 'Valor inválido para o campo "Situação"';
        } else if (Functions::isEmpty($vo->getUsuario()->getId())) {
            return 'N' . 'Informe o campo "Usuário"';
        } else if (!Functions::isInteger($vo->getUsuario()->getId())) {
            return 'N' . 'Valor inválido para o campo "Usuário"';
        } else if (Functions::isEmpty($vo->getEmpresa()->getId())) {
            return 'N' . 'Informe o campo "Empresa"';
        } else if (!Functions::isInteger($vo->getEmpresa()->getId())) {
            return 'N' . 'Valor inválido para o campo "Empresa"';
        } else if (Functions::isEmpty($vo->getCategoria()->getId())) {
            return 'N' . 'Informe o campo "Categoria"';
        } else if (!Functions::isInteger($vo->getCategoria()->getId())) {
            return 'N' . 'Valor inválido para o campo "Categoria"';
        } else if (Functions::isEmpty($vo->getTipoAmbiente()->getId())) {
            return 'N' . 'Informe o campo "Tipo de Ambiente"';
        } else if (!Functions::isInteger($vo->getTipoAmbiente()->getId())) {
            return 'N' . 'Valor inválido para o campo "Tipo de Ambiente"';
        } else if (Functions::isEmpty($vo->getTipoProduto()->getId())) {
            return 'N' . 'Informe o campo "Tipo de Produto"';
        } else if (!Functions::isInteger($vo->getTipoProduto()->getId())) {
            return 'N' . 'Valor inválido para o campo "Tipo de Produto"';
        } else if (Functions::isEmpty($vo->getPrioridade()->getId())) {
            return 'N' . 'Informe o campo "Prioridade"';
        } else if (!Functions::isInteger($vo->getPrioridade()->getId())) {
            return 'N' . 'Valor inválido para o campo "Prioridade"';
        } else if (($vo->getImpacto() == 1) && ($vo->getUsuariosAfetados() == 0)) {
            return 'N' . 'Informe o campo "Usuários Afetados"';
        } else if (Functions::isEmpty($vo->getAssunto())) {
            return 'N' . 'Informe o campo "Assunto"';
        } else if ((Functions::isEmpty($vo->getId())) && (Functions::isEmpty($vo->getObservacao()))) {
            return 'N' . 'Informe o campo "Observação"';
        } else {
            return 'S' . 'Operação realizada com sucesso';
        }
    }
    
    private function salvarRegistro($connection, $vo) {
        $model = new ChamadosModel();
        return $model->save($connection, $vo);
    }
    
    public function filtrarAction($mensagem = "") {
        $connection = Databases::connect();
        $dados = $this->carregarDadosFiltrar($connection, $mensagem);
        Databases::disconnect($connection);
        
        $this->exibirTelaFiltrar($dados);
    }
    
    private function carregarDadosFiltrar($connection, $mensagem = "") {
        $usuariosModel = new UsuariosModel();
        $usuarios = $usuariosModel->load($connection, 0);
        
        $requisitantesModel = new UsuariosModel();
        $requisitantes = $requisitantesModel->loadClientes($connection, 0);
        
        $atendentesModel = new UsuariosModel();
        $atendentes = $atendentesModel->loadNaoClientes($connection, 0);
        
        $situacoesModel = new SituacoesModel();
        $situacoes = $situacoesModel->load($connection);
        
	$empresasModel = new EmpresasModel();
        $empresas = $empresasModel->load($connection);
	
	$categoriasModel = new CategoriasModel();
        $categorias = $categoriasModel->load($connection);
        
        $tiposAmbientesModel = new TiposAmbientesModel();
        $tiposAmbientes = $tiposAmbientesModel->load($connection);
        
        $tiposProdutosModel = new TiposProdutosModel();
        $tiposProdutos = $tiposProdutosModel->load($connection);
        
        $modulosModel = new ModulosModel();
        $modulos = $modulosModel->load($connection);
        
        $prioridadesModel = new PrioridadesModel();
        $prioridades = $prioridadesModel->load($connection);
        
        return $this->trabalharDadosFiltrar($usuarios, $requisitantes, $atendentes, $situacoes, $empresas, $categorias, $tiposAmbientes, $tiposProdutos, $modulos, $prioridades, $mensagem);
    }
    
    private function trabalharDadosFiltrar($usuarios = array(), $requisitantes = array(), $atendentes = array(), $situacoes = array(), $empresas = array(), $categorias = array(), $tiposAmbientes = array(), $tiposProdutos = array(), $modulos = array(), $prioridades = array(), $mensagem = "") {
        return array( 'mensagem'       => $mensagem
                    , 'usuarios'       => $usuarios 
                    , 'requisitantes'  => $requisitantes
                    , 'atendentes'     => $atendentes
                    , 'situacoes'      => $situacoes
                    , 'empresas'       => $empresas 
                    , 'categorias'     => $categorias
                    , 'tiposAmbientes' => $tiposAmbientes
                    , 'tiposProdutos'  => $tiposProdutos
                    , 'modulos'        => $modulos
                    , 'prioridades'    => $prioridades );
    }
    
    private function exibirTelaFiltrar($dados = array()) {
        $view = new View('views/filtrarChamados.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function listarAction($mensagem = "") {
        $connection = Databases::connect();
        $dados = $this->carregarDadosListar($connection, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaListar($dados);
    }
    
    private function carregarDadosListar($connection, $mensagem = "") {
        $codigo             = $this->getParametroTela('id');
        $usuario            = $this->getParametroTela('usuario');
        $requisitante       = $this->getParametroTela('requisitante');
        $atendente          = $this->getParametroTela('atendente');
	$dataIni            = $this->getParametroTela('dataIni');
        $dataFim            = $this->getParametroTela('dataFim');
        $situacao           = $this->getParametroTela('situacao');
        $empresa            = $this->getParametroTela('empresa');
        $categoria          = $this->getParametroTela('categoria');
        $tipoAmbiente       = $this->getParametroTela('tipoAmbiente');
        $tipoProduto        = $this->getParametroTela('tipoProduto');
        $modulo             = $this->getParametroTela('modulo');
        $prioridade         = $this->getParametroTela('prioridade');
        $impacto            = $this->getParametroTela('impacto');
        $previsaoTerminoIni = $this->getParametroTela('previsaoTerminoIni');
        $previsaoTerminoFim = $this->getParametroTela('previsaoTerminoFim');
        $assunto            = $this->getParametroTela('assunto');
        $observacao         = $this->getParametroTela('observacao');
        
        $model = new ChamadosModel();
        $registros = $model->loadByCriteria($connection, $codigo, $usuario, $requisitante, $atendente, $dataIni, $dataFim, $situacao, $empresa, $categoria, $tipoAmbiente, $tipoProduto, $modulo, $prioridade, $impacto, $previsaoTerminoIni, $previsaoTerminoFim, $assunto, $observacao);
        
        return $this->trabalharDadosListar($registros, $mensagem);
    }
    
    private function trabalharDadosListar($registros = array(), $mensagem = "") {
        return array( 'mensagem'  => $mensagem
                    , 'registros' => $registros );
    }
    
    private function exibirTelaListar($dados = array()) {
        $view = new View('views/listarChamados.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function listarNaoClassificadosAction($mensagem = "") {
        $connection = Databases::connect();
        $dados = $this->carregarDadosNaoClassificados($connection, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaListar($dados);
    }
    
    private function carregarDadosNaoClassificados($connection, $mensagem = "") {
        $model = new ChamadosModel();
        $registros = $model->loadNaoClassificados($connection);
        
        return $this->trabalharDadosListar($registros, $mensagem);
    }
    
    public function cadastrarAction() {
        $id = $this->getParametroTela('id');

        $connection = Databases::connect();
        $dados = $this->carregarDadosManter($connection, $id);
        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
    
    public function carregarDadosManter($connection, $id = "", $mensagem = "") {
        // parametro $id pode ser tanto ChamadoVo como chamadoCodigo
        if (is_object($id)) {
            $chamado = $id;
        } else if (!Functions::isEmpty($id)) {
            $model = new ChamadosModel();
            $chamado = $model->loadById($connection, $id);
        } else {
            $chamado = new ChamadosVo();
        }
        
        // Campos Descriçao e Anexo devem vir em branco sempre
        // Exceto quando validação do formulário encontrar algum problema
        if ((Functions::isEmpty($mensagem)) || (substr($mensagem,0,1) == 'S')) {
            $chamado->setObservacao("");
            $chamado->setAnexo("");
        }
        
        $apenasAtivos = 0;
	if (Functions::isEmpty($chamado->getId())) {
            $apenasAtivos = 1;
        }
        
        $usuariosModel = new UsuariosModel();
        $usuarios = $usuariosModel->load($connection, $apenasAtivos);
	
        $requisitantesModel = new UsuariosModel();
        $requisitantes = $requisitantesModel->loadClientes($connection, $apenasAtivos);
        
        $atendentesModel = new UsuariosModel();
        $atendentes = $atendentesModel->loadNaoClientes($connection, $apenasAtivos);
        
        $situacoesModel = new SituacoesModel();
        $situacoes = $situacoesModel->load($connection);
        
	$empresasModel = new EmpresasModel();
        $empresas = $empresasModel->load($connection);
	
	$categoriasModel = new CategoriasModel();
        $categorias = $categoriasModel->load($connection);
	
	$tiposAmbientesModel = new TiposAmbientesModel();
        $tiposAmbientes = $tiposAmbientesModel->load($connection);
	
	$tiposProdutosModel = new TiposProdutosModel();
        $tiposProdutos = $tiposProdutosModel->load($connection);
        
	$prioridadesModel = new PrioridadesModel();
        $prioridades = $prioridadesModel->load($connection);
	
        $moduloModel = new ModulosModel();
        $modulos = $moduloModel->load($connection);
        
	$historicosModel = new ChamadosHistoricosModel();
	$historicos = $historicosModel->loadByChamado($connection, $chamado->getId());
	
	$apontamentosModel = new ApontamentosModel();
	$apontamentos = $apontamentosModel->load($connection, "", $chamado->getId());
	
	$outroChamado = new ChamadosVo();
	
        $caller = "cadastrar";
	
        return $this->trabalharDadosManter($chamado, $usuarios, $requisitantes, $atendentes, $situacoes, $empresas, $categorias, $tiposAmbientes, $tiposProdutos, $modulos, $prioridades, $historicos, $apontamentos, $outroChamado, $caller, $mensagem);
    }
    
    private function trabalharDadosManter($chamado, $usuarios = array(), $requisitantes = array(), $atendentes = array(), $situacoes = array(), $empresas = array(), $categorias = array(), $tiposAmbientes = array(), $tiposProdutos = array(), $modulos = array(), $prioridades = array(), $historicos = array(), $apontamentos = array(), $outroChamado = "", $caller = "", $mensagem = "") {
        return array( 'mensagem'        => $mensagem
		    , 'chamado'         => $chamado
                    , 'usuarios'        => $usuarios 
                    , 'requisitantes'   => $requisitantes
                    , 'atendentes'      => $atendentes
                    , 'situacoes'       => $situacoes
                    , 'empresas'        => $empresas 
                    , 'categorias'      => $categorias 
                    , 'tiposAmbientes'  => $tiposAmbientes
                    , 'tiposProdutos'   => $tiposProdutos
                    , 'modulos'         => $modulos
                    , 'prioridades'     => $prioridades
                    , 'modulos'         => $modulos
                    , 'historicos'      => $historicos
                    , 'apontamentos'    => $apontamentos
                    , 'outroChamado'    => $outroChamado
                    , 'caller'          => $caller);
    }
    
    public function exibirTelaManter($dados) {
        $view = new View('views/manterChamados.phtml');
        $view->setParams($dados);
        $view->showContents();
    }
    
    public function salvarCadastrarAction() {
        $connection = Databases::connect();

        $chamadosModel = new ChamadosModel();
        $chamadoVo = $chamadosModel->loadById($connection, $this->getParametroTela('id'));

        $situacoesModel = new SituacoesModel();
        $situacaoVo = $situacoesModel->loadById($connection, $this->getParametroTela('situacao'));

        $usuariosModel = new UsuariosModel();
        $usuarioVo = $usuariosModel->loadById($connection, $this->getParametroTela('usuario'));
        $requisitanteVo = $usuariosModel->loadById($connection, $this->getParametroTela('requisitante'));
        $atendenteVo = $usuariosModel->loadById($connection, $this->getParametroTela('atendente'));

        $empresasModel = new EmpresasModel();
        $empresaVo = $empresasModel->loadById($connection, $this->getParametroTela('empresa'));

        $categoriasModel = new CategoriasModel();
        $categoriaVo = $categoriasModel->loadById($connection, $this->getParametroTela('categoria'));

        $tiposAmbientesModel = new TiposAmbientesModel();
        $tipoAmbienteVo = $tiposAmbientesModel->loadById($connection, $this->getParametroTela('tipoAmbiente'));

        $tiposProdutosModel = new TiposProdutosModel();
        $tipoProdutoVo = $tiposProdutosModel->loadById($connection, $this->getParametroTela('tipoProduto'));
        
        $modulosModel = new ModulosModel();
        $moduloVo = $modulosModel->loadById($connection, $this->getParametroTela('modulo'));
        
        $prioridadesModel = new PrioridadesModel();
        $prioridadeVo = $prioridadesModel->loadById($connection, $this->getParametroTela('prioridade'));
        
        if (Functions::isEmpty($this->getParametroTela('impacto'))) {
            $impacto = 0;
        } else {
            $impacto = $this->getParametroTela('impacto');
        }

        if (Functions::isEmpty($this->getParametroTela('id'))) {
            $modo = "I";
        } else {
            $modo = "A";
        }

        $vo = new ChamadosVo();

        $vo->setId($this->getParametroTela('id'));
        $vo->setUsuario($usuarioVo);
        $vo->setRequisitante($requisitanteVo);
        $vo->setAtendente($atendenteVo);
        $vo->setData($this->getParametroTela('data'));
        $vo->setSituacao($situacaoVo);
        $vo->setEmpresa($empresaVo);
        $vo->setCategoria($categoriaVo);
        $vo->setTipoAmbiente($tipoAmbienteVo);
        $vo->setTipoProduto($tipoProdutoVo);
        $vo->setModulo($moduloVo);
        $vo->setPrioridade($prioridadeVo);
        $vo->setImpacto($impacto);
        $vo->setUsuariosAfetados($this->getParametroTela('usuariosAfetados'));
        $vo->setAreasAfetadas($this->getParametroTela('areasAfetadas'));
        $vo->setPrevisaoTermino($this->getParametroTela('previsaoTermino'));
        $vo->setAssunto($this->getParametroTela('assunto'));
        $vo->setObservacao($this->getParametroTela('observacao'));
        $vo->setAnexo($_FILES["anexo"]);
        
        $mensagem = $this->validarFormulario($vo);
        
        if (substr($mensagem, 0, 1) == 'S') {
            $id = $this->salvarRegistro($connection, $vo);
            $vo->setId($id);
            
            $alteracoes = $this->verificarAlteracoes($chamadoVo, $vo);
            
            // Lançamento de Histórico do Chamado
            if (!Functions::isEmpty($alteracoes)) {
                $usuarioLogadoVo = $usuariosModel->loadById($connection, $_SESSION['usuarioCodigo']);
                
                $historico = new ChamadosHistoricosVo();
                $historico->setChamado($vo);
                $historico->setUsuario($usuarioLogadoVo);
                $historico->setData(date('Y-m-d H:i'));
                $historico->setObservacao($alteracoes);
                $historico->setAnexo($vo->getAnexo());
                
                $historicosModel = new ChamadosHistoricosModel();
                $historicosModel->save($connection, $historico);
            }
            
            // Disparo de E-mail Comunicando
            if (!Functions::isEmpty($alteracoes)) {
                if ($modo == "I") {
                    $subject = "BasisIT :: Inclusão de Chamado :: Número " . $vo->getId() . " :: " . $vo->getEmpresa()->getDescricao();
                    $txt = "Informamos que o chamado " . $vo->getId() . " foi aberto com sucesso.<br /><br />Solicitamos que aguarde até que o mesmo seja verificado.<br /><br />Você receberá um aviso a cada nova interação feita.";
                } else {
                    $subject = "BasisIT :: Nova Interação no Chamado :: Número " . $vo->getId() . " :: " . $vo->getEmpresa()->getDescricao();
                    $txt = "Informamos que houve uma nova interação no chamado " . $vo->getId() . ".";
                }
                
                $txt = $txt . '<br /><br />';
                $txt = $txt . '<fieldset><legend><b>Descrição da Interação:</b></legend>'. $alteracoes . '</fieldset>';
                $txt = $txt . '<br />';
                $txt = $txt . '<font color="red"><b>OBS: Este e-mail foi gerado automaticamente. Favor não responder para este endereço.</b></font>';
                
                // E-mail para o usuário que abriu o chamado vai sempre
                if (!Functions::isEmpty($vo->getUsuario()->getEmail())) {
                    $to = $vo->getUsuario()->getEmail();
                    Functions::email($to, $subject, $txt);
                }
                
                // Se for inclusão de chamado, vai para os usuários definidos no parâmetro
                if ($modo == "I") {
                    $destinatarios = explode(';', Functions::getParametro('destinatários'));
                    foreach ($destinatarios as $destinatario) {
                        Functions::email($destinatario, $subject, $txt);
                    }
                // Se for alteração de chamado, vai para os envolvidos que estiverem envolvidos no chamado (analista e requisitante)
                } else {
                    if (!Functions::isEmpty($vo->getRequisitante()->getEmail())) {
                        $to = $vo->getRequisitante()->getEmail();
                        Functions::email($to, $subject, $txt);
                    }
                    if (!Functions::isEmpty($vo->getAtendente()->getEmail())) {
                        $to = $vo->getAtendente()->getEmail();
                        Functions::email($to, $subject, $txt);
                    }
                }
            }
            
            // Lançamento de Apontamento Automático
            /*
            if ( ($modo == "I") && ($vo->getAtendente()->getId() == $vo->getUsuario()->getId()) ) {
                $controller = new ApontamentosController();
                $mensagem = $controller->iniciar($connection, "C", $vo->getId(), "Iniciado automaticamente");
            }
            */
        }
        
        $dados = $this->carregarDadosManter($connection, $vo, $mensagem);
        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
    
    public function verificarDispararEmail($chamadoAntes, $chamadoDepois) {
        if (Functions::isEmpty($chamadoAntes->getId())) {
            return true;
        } else {
            if ($chamadoAntes->getAtendente()->getId() != $chamadoDepois->getAtendente()->getId()) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function verificarAlteracoes($chamadoAntes, $chamadoDepois) {
        $mensagem = "";
        
        if (Functions::isEmpty($chamadoAntes->getId())) {
            $mensagem = $mensagem . '* Chamado incluído. <br />* Descrição adicionada: "' . $chamadoDepois->getObservacao() . '"<br />';
        } else {
            $chamadoAnexoAntes = $chamadoAntes->getAnexo();
            $chamadoAnexoDepois = $chamadoDepois->getAnexo();
            
            if ($chamadoAntes->getId() != $chamadoDepois->getId()) {
                $mensagem = $mensagem . '* ID alterado de "' . $chamadoAntes->getId() . '" para "' . $chamadoDepois->getId() . '"<br />';
            }
            if ($chamadoAntes->getData() != $chamadoDepois->getData()) {
                $mensagem = $mensagem . '* Data alterada de "' . $chamadoAntes->getData() . '" para "' . $chamadoDepois->getData() . '"<br />';
            }
            if ($chamadoAntes->getSituacao()->getId() != $chamadoDepois->getSituacao()->getId()) {
                $mensagem = $mensagem . '* Situação alterada de "' . $chamadoAntes->getSituacao()->getDescricao() . '" para "' . $chamadoDepois->getSituacao()->getDescricao() . '"<br />';
            }
            if ($chamadoAntes->getUsuario()->getId() != $chamadoDepois->getUsuario()->getId()) {
                $mensagem = $mensagem . '* Usuário alterado de "' . $chamadoAntes->getUsuario()->getNome() . '" para "' . $chamadoDepois->getUsuario()->getNome() . '"<br />';
            }
            if ($chamadoAntes->getRequisitante()->getId() != $chamadoDepois->getRequisitante()->getId()) {
                $mensagem = $mensagem . '* Requisitante alterado de "' . $chamadoAntes->getRequisitante()->getNome() . '" para "' . $chamadoDepois->getRequisitante()->getNome() . '"<br />';
            }
            if ($chamadoAntes->getAtendente()->getId() != $chamadoDepois->getAtendente()->getId()) {
                $mensagem = $mensagem . '* Atendente alterado de "' . $chamadoAntes->getAtendente()->getNome() . '" para "' . $chamadoDepois->getAtendente()->getNome() . '"<br />';
            }
            if ($chamadoAntes->getEmpresa()->getId() != $chamadoDepois->getEmpresa()->getId()) {
                $mensagem = $mensagem . '* Empresa alterada de "' . $chamadoAntes->getEmpresa()->getDescricao() . '" para "' . $chamadoDepois->getEmpresa()->getDescricao() . '"<br />';
            }
            if ($chamadoAntes->getCategoria()->getId() != $chamadoDepois->getCategoria()->getId()) {
                $mensagem = $mensagem . '* Categoria alterada de "' . $chamadoAntes->getCategoria()->getDescricao() . '" para "' . $chamadoDepois->getCategoria()->getDescricao() . '"<br />';
            }
            if ($chamadoAntes->getTipoAmbiente()->getId() != $chamadoDepois->getTipoAmbiente()->getId()) {
                $mensagem = $mensagem . '* Tipo de Ambiente alterado de "' . $chamadoAntes->getTipoAmbiente()->getDescricao() . '" para "' . $chamadoDepois->getTipoAmbiente()->getDescricao() . '"<br />';
            }
            if ($chamadoAntes->getTipoProduto()->getId() != $chamadoDepois->getTipoProduto()->getId()) {
                $mensagem = $mensagem . '* Tipo de Produto alterado de "' . $chamadoAntes->getTipoProduto()->getDescricao() . '" para "' . $chamadoDepois->getTipoProduto()->getDescricao() . '"<br />';
            }
            if ($chamadoAntes->getModulo()->getId() != $chamadoDepois->getModulo()->getId()) {
                $mensagem = $mensagem . '* Módulo alterado de "' . $chamadoAntes->getModulo()->getDescricao() . '" para "' . $chamadoDepois->getModulo()->getDescricao() . '"<br />';
            }
            if ($chamadoAntes->getPrioridade()->getId() != $chamadoDepois->getPrioridade()->getId()) {
                $mensagem = $mensagem . '* Prioridade alterada de "' . $chamadoAntes->getPrioridade()->getDescricao() . '" para "' . $chamadoDepois->getPrioridade()->getDescricao() . '"<br />';
            }
            if ($chamadoAntes->getImpacto() != $chamadoDepois->getImpacto()) {
                $mensagem = $mensagem . '* Impacto alterado de "' . $chamadoAntes->getImpactoExtenso() . '" para "' . $chamadoDepois->getImpactoExtenso() . '"<br />';
            }
            if ($chamadoAntes->getUsuariosAfetados() != $chamadoDepois->getUsuariosAfetados()) {
                $mensagem = $mensagem . '* Usuários Afetados alterado de "' . $chamadoAntes->getUsuariosAfetadosExtenso() . '" para "' . $chamadoDepois->getUsuariosAfetadosExtenso() . '"<br />';
            }
            if ($chamadoAntes->getAreasAfetadas() != $chamadoDepois->getAreasAfetadas()) {
                $mensagem = $mensagem . '* Áreas Afetadas alterado de "' . $chamadoAntes->getAreasAfetadasExtenso() . '" para "' . $chamadoDepois->getAreasAfetadasExtenso() . '"<br />';
            }
            if ($chamadoAntes->getPrevisaoTermino() != $chamadoDepois->getPrevisaoTermino()) {
                $mensagem = $mensagem . '* Previsão Término alterado de "' . $chamadoAntes->getPrevisaoTermino() . '" para "' . $chamadoDepois->getPrevisaoTermino() . '"<br />';
            }
            if ($chamadoAntes->getAssunto() != $chamadoDepois->getAssunto()) {
                $mensagem = $mensagem . '* Assunto alterado de "' . $chamadoAntes->getAssunto() . '" para "' . $chamadoDepois->getAssunto() . '"<br />';
            }
            if (!Functions::isEmpty(Functions::clean($chamadoDepois->getObservacao()))) {
                $mensagem = $mensagem . '* Descrição adicionada: "' . Functions::clean($chamadoDepois->getObservacao()) . '"<br />';
            }
            if ($chamadoAnexoAntes["name"] != $chamadoAnexoDepois["name"]) {
                $mensagem = $mensagem . '* Anexado o arquivo "' . $chamadoAnexoDepois["name"] . '"<br />';
            }
        }
        
        return $mensagem;
    }
    
    public function baixarAction() {
        $nomeOriginal = $this->getParametroTela('nomeOriginal');
        $nomeSistema  = $this->getParametroTela('nomeSistema');
        
        header('Content-Disposition: attachment; filename="' . $nomeOriginal . '"');
        
        readfile($nomeSistema);
    }
    
    public function finalizarAction($mensagem = "") {
        $connection = Databases::connect();
        
        $id = $this->getParametroTela('id');
        
        $chamadoModel = new ChamadosModel();
        $chamadoVo = $chamadoModel->loadById($connection, $id);
        
        $apontamentoModel = new ApontamentosModel();
        $apontamento = $apontamentoModel->verificaSeAberto($connection, "C", $id);
        
        $apontamentoController = new ApontamentosController();
        $mensagem = $apontamentoController->validarIniciarApontamento(new AtividadesVo(), $chamadoVo, $apontamento, "C", $id);
        
        if (substr($mensagem, 0, 1) == 'S') {
            $situacaoModel = new SituacoesModel();
            $situacaoVo = $situacaoModel->loadById($connection, $_SESSION['situacaoFinalizada']); // Finalizado
            
            $chamadoVo->setSituacao($situacaoVo);
            $chamadoModel->save($connection, $chamadoVo);
            
            $usuarioModel = new UsuariosModel();
            $usuarioVo = $usuarioModel->loadById($connection, $_SESSION['usuarioCodigo']);
            
            $chamadoHistoricoVo = new ChamadosHistoricosVo();
            $chamadoHistoricoVo->setChamado($chamadoVo);
            $chamadoHistoricoVo->setData(date('Y-m-d H:i'));
            $chamadoHistoricoVo->setUsuario($usuarioVo);
            $chamadoHistoricoVo->setObservacao("* Chamado finalizado");
            
            $chamadoHistoricoModel = new ChamadosHistoricosModel();
            $chamadoHistoricoModel->save($connection, $chamadoHistoricoVo);
            
            // Disparo de E-mail Comunicando
            $subject = "BasisIT :: Finalização de Chamado :: Número " . $chamadoVo->getId() . " :: " . $chamadoVo->getEmpresa()->getDescricao();
            $txt = "Informamos que houve uma nova interação no chamado " . $chamadoVo->getId() . ".";
            
            $txt = $txt . '<br /><br />';
            $txt = $txt . '<fieldset><legend><b>Descrição da Interação:</b></legend>* O chamado foi finalizado</fieldset>';
            $txt = $txt . '<br />';
            $txt = $txt . '<font color="red"><b>OBS: Este e-mail foi gerado automaticamente. Favor não responder para este endereço.</b></font>';
            
            // E-mail para o usuário que abriu o chamado vai sempre
            if (!Functions::isEmpty($chamadoVo->getUsuario()->getEmail())) {
                $to = $chamadoVo->getUsuario()->getEmail();
                Functions::email($to, $subject, $txt);
            }
            if (!Functions::isEmpty($chamadoVo->getRequisitante()->getEmail())) {
                $to = $chamadoVo->getRequisitante()->getEmail();
                Functions::email($to, $subject, $txt);
            }
            if (!Functions::isEmpty($chamadoVo->getAtendente()->getEmail())) {
                $to = $chamadoVo->getAtendente()->getEmail();
                Functions::email($to, $subject, $txt);
            }
        }
        
        $dados = $this->carregarDadosManter($connection, $id, $mensagem);
        
        Databases::disconnect($connection);
        
        $this->exibirTelaManter($dados);
    }
    
    public function cancelarAction($mensagem = "") {
        $connection = Databases::connect();

        $id = $this->getParametroTela('id');

        $chamadoModel = new ChamadosModel();
        $chamadoVo = $chamadoModel->loadById($connection, $id);

        $apontamentoModel = new ApontamentosModel();
        $apontamento = $apontamentoModel->verificaSeAberto($connection, "C", $id);

        $apontamentoController = new ApontamentosController();
        $mensagem = $apontamentoController->validarIniciarApontamento(new AtividadesVo(), $chamadoVo, $apontamento, "C", $id);

        if (substr($mensagem, 0, 1) == 'S') {
            $situacaoModel = new SituacoesModel();
            $situacaoVo = $situacaoModel->loadById($connection, $_SESSION['situacaoCancelada']); // Cancelado

            $chamadoVo->setSituacao($situacaoVo);
            $chamadoModel->save($connection, $chamadoVo);

            $usuarioModel = new UsuariosModel();
            $usuarioVo = $usuarioModel->loadById($connection, $_SESSION['usuarioCodigo']);

            $chamadoHistoricoVo = new ChamadosHistoricosVo();
            $chamadoHistoricoVo->setChamado($chamadoVo);
            $chamadoHistoricoVo->setData(date('Y-m-d H:i'));
            $chamadoHistoricoVo->setUsuario($usuarioVo);
            $chamadoHistoricoVo->setObservacao("* Chamado cancelado");

            $chamadoHistoricoModel = new ChamadosHistoricosModel();
            $chamadoHistoricoModel->save($connection, $chamadoHistoricoVo);
            
            // Disparo de E-mail Comunicando
            $subject = "BasisIT :: Cancelamento de Chamado :: Número " . $chamadoVo->getId() . " :: " . $chamadoVo->getEmpresa()->getDescricao();
            $txt = "Informamos que houve uma nova interação no chamado " . $chamadoVo->getId() . ".";
            
            $txt = $txt . '<br /><br />';
            $txt = $txt . '<fieldset><legend><b>Descrição da Interação:</b></legend>* O chamado foi cancelado</fieldset>';
            $txt = $txt . '<br />';
            $txt = $txt . '<font color="red"><b>OBS: Este e-mail foi gerado automaticamente. Favor não responder para este endereço.</b></font>';
            
            // E-mail para o usuário que abriu o chamado vai sempre
            if (!Functions::isEmpty($chamadoVo->getUsuario()->getEmail())) {
                $to = $chamadoVo->getUsuario()->getEmail();
                Functions::email($to, $subject, $txt);
            }
            if (!Functions::isEmpty($chamadoVo->getRequisitante()->getEmail())) {
                $to = $chamadoVo->getRequisitante()->getEmail();
                Functions::email($to, $subject, $txt);
            }
            if (!Functions::isEmpty($chamadoVo->getAtendente()->getEmail())) {
                $to = $chamadoVo->getAtendente()->getEmail();
                Functions::email($to, $subject, $txt);
            }
        }
        
        $dados = $this->carregarDadosManter($connection, $id, $mensagem);

        Databases::disconnect($connection);

        $this->exibirTelaManter($dados);
    }
    
    public function ajaxBuscaEmpresaDoRequisitanteAction() {
        $requisitante = $this->getParametroTela('requisitante');

        $connection = Databases::connect();
        $usuarioModel = new UsuariosModel();
        $usuarioVo = $usuarioModel->loadById($connection, $requisitante);
        Databases::disconnect($connection);

        echo $usuarioVo->getEmpresa()->getId();
    }
    
    public function ajaxUsuariosAfetadosAction() {
        $impacto = $this->getParametroTela('impacto');
        $usuariosAfetados = $this->getParametroTela('usuariosAfetados');
        $exibeAberto = $this->getParametroTela('exibeAberto');

        $chamadoVo = new ChamadosVo();

        if ($impacto == 1) {
            echo '<div class="form-group">
                    <label class="control-label col-sm-2" for="usuariosAfetados">Usuários Afetados:</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="usuariosAfetados" name="usuariosAfetados" ' . ( ($exibeAberto == 1) ? '' : ' disabled="disabled" ' ) . '>
                            <option value="0" ' . ( ($usuariosAfetados == 0) ? ' selected ' : '' ) . '>' . $chamadoVo->getUsuariosAfetadosExtensoParam(0) . '</option>
                            <option value="1" ' . ( ($usuariosAfetados == 1) ? ' selected ' : '' ) . '>' . $chamadoVo->getUsuariosAfetadosExtensoParam(1) . '</option>
                            <option value="2" ' . ( ($usuariosAfetados == 2) ? ' selected ' : '' ) . '>' . $chamadoVo->getUsuariosAfetadosExtensoParam(2) . '</option>
                            <option value="3" ' . ( ($usuariosAfetados == 3) ? ' selected ' : '' ) . '>' . $chamadoVo->getUsuariosAfetadosExtensoParam(3) . '</option>
                            <option value="4" ' . ( ($usuariosAfetados == 4) ? ' selected ' : '' ) . '>' . $chamadoVo->getUsuariosAfetadosExtensoParam(4) . '</option>
                        </select>
                    </div>
                  </div>';
        } else {
            echo '<input type="hidden" id="usuariosAfetados" name="usuariosAfetados" value="0" />';
        }
    }
    
    public function ajaxAreasAfetadasAction() {
        $impacto = $this->getParametroTela('impacto');
        $areasAfetadas = $this->getParametroTela('areasAfetadas');
        $exibeAberto = $this->getParametroTela('exibeAberto');

        $chamadoVo = new ChamadosVo();
        
        if ($impacto == 1) {
            echo '<div class="form-group">
                    <label class="control-label col-sm-2" for="areasAfetadas">Todas áreas afetadas?</label>
                    <div class="col-sm-10">
                        <label class="radio-inline"><input type="radio" id="areasAfetadas" name="areasAfetadas" value="0"' . ( ($exibeAberto == 1) ? '' : ' disabled="disabled" ' ) . ( ( $areasAfetadas == 0 ) ? ' checked="checked" ' : '' ) . '>' . $chamadoVo->getAreasAfetadasExtensoParam(0) . '</label>
                        <label class="radio-inline"><input type="radio" id="areasAfetadas" name="areasAfetadas" value="1"' . ( ($exibeAberto == 1) ? '' : ' disabled="disabled" ' ) . ( ( $areasAfetadas == 1 ) ? ' checked="checked" ' : '' ) . '>' . $chamadoVo->getAreasAfetadasExtensoParam(1) . '</label>
                    </div>
                  </div>';
        } else {
            echo '<input type="hidden" id="areasAfetadas" name="areasAfetadas" value="0" />';
        }
    }
    
}