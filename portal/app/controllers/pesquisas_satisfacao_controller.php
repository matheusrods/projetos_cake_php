<?php
class PesquisasSatisfacaoController extends AppController {
	public $name = 'PesquisasSatisfacao';
	public $uses = array('PesquisaSatisfacao', 'StatusPesquisaSatisfacao');
    public $helpers = array('Highcharts');

    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->BAuth->allow('pre_listagem_pesquisa_satisfacao_analitico');
    }

	function index() {
		$this->pageTitle = 'Pesquisa de satisfação';
		$this->data['PesquisaSatisfacao']['status_pesquisa'] = 1;
		$this->data['PesquisaSatisfacao'] = $this->Filtros->controla_sessao($this->data, 'PesquisaSatisfacao');
		$this->data['PesquisaSatisfacao']['data_inicial'] = date("01/m/Y");
		$this->data['PesquisaSatisfacao']['data_final'] = date("d/m/Y");		
		$status_pesquisa = $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa'));
	}
	
	private function validaDataReagendamento( $data, $hora ){		
		$data_atual         =  new DateTime( date("Ymd H:i") );
		$data = AppModel::dateToDbDate2( $data );
		$data_reagendamento = $data.' '.$hora;
		$data_reagendamento = new DateTime( AppModel::dateToDbDate2( $data_reagendamento) );		
		$data_valida = ( $data_reagendamento > $data_atual );
		if( (empty($data) || empty($hora)) || ($data_valida==FALSE) ) {
			$this->PesquisaSatisfacao->invalidate( 'data_reagendamento', 'Data inválida' );
			$this->PesquisaSatisfacao->invalidate( 'hora_reagendamento', 'Hora inválida' );
			$this->BSession->setFlash(array(MSGT_ERROR, 'Reagendamento não informado.'));		
			return FALSE;
		}		
		return TRUE;
	}

	function pesquisa_satisfacao($codigo_pesquisa) {
		$this->layout = 'ajax';
		$this->loadModel("ClienteContato");
		$this->pageTitle  = false;
		if(!empty($this->data)){
			if(empty($this->data['PesquisaSatisfacao']['codigo_status_pesquisa']) ) {
				if( empty($this->data['PesquisaSatisfacao']['codigo_status_pesquisa']) )
					$this->PesquisaSatisfacao->invalidate('codigo_status_pesquisa','Informe o nível de satisfação.');
				$this->BSession->setFlash('save_error');
			} else {
				$authUsuario = $this->authUsuario;
				$permite_gravar = TRUE;
				if( $this->data['PesquisaSatisfacao']['codigo_status_pesquisa'] == 4 ){
					$permite_gravar = $this->validaDataReagendamento( $this->data['PesquisaSatisfacao']['data_reagendamento'], $this->data['PesquisaSatisfacao']['hora_reagendamento'] );
				}
				if( $permite_gravar ){
					if( empty($this->data['ClienteContato']['codigo']) ){//Tenta incluir contato
						$this->data['ClienteContato']['codigo_tipo_retorno'] = $this->data['PesquisaSatisfacao']['codigo_tipo_retorno'];
						$this->data['ClienteContato']['codigo_cliente'] = $this->data['PesquisaSatisfacao']['codigo_cliente'];
						$this->data['ClienteContato']['descricao'] = $this->data['PesquisaSatisfacao']['descricao'];
						$this->data['ClienteContato']['codigo_tipo_contato'] = $this->data['PesquisaSatisfacao']['codigo_tipo_contato'];
						$this->data['ClienteContato']['nome'] = $this->data['PesquisaSatisfacao']['nome'];
						if (in_array($this->data['ClienteContato']['codigo_tipo_retorno'], array(1,3,5))) {
							$fone = Comum::soNumero( $this->data['ClienteContato']['descricao'] );
							$this->data['ClienteContato']['ddd']       = substr($fone,0,2);
							$this->data['ClienteContato']['descricao'] = substr($fone,2);
						}
						if( !$this->ClienteContato->incluir( $this->data['ClienteContato'] )) {
							foreach($this->ClienteContato->validationErrors as $field =>  $errors ){
								$this->PesquisaSatisfacao->invalidate( $field, $this->ClienteContato->validationErrors[$field] );
							}
						} else {
							$this->data['ClienteContato']['codigo'] = $this->ClienteContato->id;	
						}
					}
					if( empty($this->data['ClienteContato']['codigo']) ){
						$this->BSession->setFlash(array(MSGT_ERROR, 'Contato não informado.'));
					} else {
						$this->data['PesquisaSatisfacao']['usuario_pesquisa'] = $authUsuario['Usuario']['codigo'];
						$this->data['PesquisaSatisfacao']['codigo_usuario_pesquisa'] = $authUsuario['Usuario']['codigo'];
						$this->data['PesquisaSatisfacao']['codigo_cliente_contato'] = $this->data['ClienteContato']['codigo'];
						if( !$this->PesquisaSatisfacao->atualizar($this->data)){
							$this->BSession->setFlash('save_error');
						}else{
							$dados = array();
							$dados_pesquisa = array();
							$dados_contatos = array();
							$dados_pesquisa = $this->PesquisaSatisfacao->carregar($this->data['PesquisaSatisfacao']['codigo']);
							$dados_contatos = $this->ClienteContato->carregar($this->data['ClienteContato']['codigo']);
							$dados = array_merge($dados, $this->data,$dados_pesquisa, $dados_contatos);
							if($this->data['PesquisaSatisfacao']['codigo_status_pesquisa'] == PesquisaSatisfacao::AGRP_STATUS_PESQUISA_INSATISFEITO) {
								$this->PesquisaSatisfacao->enviarAlertaPesquisaInsastifeita($dados);
							}
							$this->BSession->setFlash('save_success');
						}		
					}
				}
			}			
			if( !empty($this->data['PesquisaSatisfacao']['codigo_pai'])){
				$dados_pai =  $this->PesquisaSatisfacao->carregar( $this->data['PesquisaSatisfacao']['codigo_pai'] );
				$this->data['PesquisaSatisfacao']['codigo_cliente_contato'] = $dados_pai['PesquisaSatisfacao']['codigo_cliente_contato'];
			}			
		} else {
			$this->data = $this->PesquisaSatisfacao->carregar( $codigo_pesquisa );
			if( !empty($this->data['PesquisaSatisfacao']['codigo_pai'])){
				$dados_pai =  $this->PesquisaSatisfacao->carregar( $this->data['PesquisaSatisfacao']['codigo_pai'] );
				$this->data['PesquisaSatisfacao']['codigo_cliente_contato'] = $dados_pai['PesquisaSatisfacao']['codigo_cliente_contato'];
				$dados_contato =  $this->ClienteContato->carregar( $this->data['PesquisaSatisfacao']['codigo_cliente_contato'] );
				$this->data['PesquisaSatisfacao']['codigo_tipo_retorno'] = $dados_contato['TipoRetorno']['codigo'];
				$this->data['PesquisaSatisfacao']['descricao'] = $dados_contato['ClienteContato']['ddd'].$dados_contato['ClienteContato']['descricao'];
				$this->data['PesquisaSatisfacao']['nome'] = $dados_contato['ClienteContato']['nome'];
				$this->data['PesquisaSatisfacao']['codigo_tipo_contato'] = $dados_contato['ClienteContato']['codigo_tipo_contato'];
			}
		}
        $tipos_contato    = $this->ClienteContato->TipoContato->find('list');
        $tipos_retorno    = $this->ClienteContato->TipoRetorno->find('list', array( 'conditions'=> array('codigo'=> 1 )));
		$status_pesquisa  = $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa'));
		$disabled_contato = true;
		$this->set(compact('status_pesquisa','codigo_pesquisa', 'disabled_contato', 'tipos_contato', 'tipos_retorno' ));
	}

	function pesquisa_realizada( $codigo_pesquisa ) {		
		$this->layout    = 'ajax';
		$this->pageTitle = false;
		$dados_pesquisa  = $this->PesquisaSatisfacao->carregarPesquisa( $codigo_pesquisa );
		$status_pesquisa = $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa'));		
		$status_alterado = ($dados_pesquisa[0]['PesquisaSatisfacao']['codigo_status_pesquisa'] != $this->data['PesquisaSatisfacao']['codigo_status_pesquisa'] );
		if( !empty($this->data['PesquisaSatisfacao']['observacao_complementar']) || ($status_alterado == TRUE) ){
			$dados_pesquisa  = $this->PesquisaSatisfacao->carregarPesquisa( $codigo_pesquisa );
			$authUsuario = $this->authUsuario;
			$observacao_complementar = $dados_pesquisa[0]['PesquisaSatisfacao']['observacao']."\n\n";
			$observacao_complementar.= date("d/m/Y H:i:s")."\n";
			$observacao_complementar.= $authUsuario['Usuario']['apelido']."\n";
			$observacao_complementar.= ($status_alterado ? "Status Anterior (".$status_pesquisa[$dados_pesquisa[0]['PesquisaSatisfacao']['codigo_status_pesquisa']].") \n" : NULL);
			$observacao_complementar.= $this->data['PesquisaSatisfacao']['observacao_complementar']."\n";
			$this->data['PesquisaSatisfacao']['observacao'] = $observacao_complementar;
			if( empty($this->data['PesquisaSatisfacao']['codigo_status_pesquisa']) ) 
				$this->data['PesquisaSatisfacao']['codigo_status_pesquisa'] = $dados_pesquisa[0]['PesquisaSatisfacao']['codigo_status_pesquisa'];

			if( $this->PesquisaSatisfacao->atualizar_observacao( $this->data) ){
				$observacao_complementar_success = true;
			} else {
				$observacao_complementar_success = false;
			}
			$this->set(compact('observacao_complementar_success'));
		}
		$this->data 	= $dados_pesquisa[0];
		$hitorico_pesquisas = $this->PesquisaSatisfacao->find('all', array(
			'fields' => array('codigo','codigo_produto', 'data_pesquisa', 'codigo_status_pesquisa'),
			'conditions'=> array(
				'PesquisaSatisfacao.codigo_cliente'=>$this->data['PesquisaSatisfacao']['codigo_cliente'],
				'PesquisaSatisfacao.codigo_produto'=>$this->data['PesquisaSatisfacao']['codigo_produto'],
				'PesquisaSatisfacao.codigo_status_pesquisa <>' => NULL,
				'PesquisaSatisfacao.codigo <>' => $this->data['PesquisaSatisfacao']['codigo'],
			),
			'limit' => 12
		));
		$cor_status_pesquisa = array(1 => 'success',2 => 'transito',3 => 'important', 4=>'warning');
		$this->set(compact('codigo_pai','hitorico_pesquisas','status_pesquisa','cor_status_pesquisa'));
	}

	function listagem_pesquisa_satisfacao( ){
		$this->layout 	= 'ajax'; 
		$filtros 		= $this->Filtros->controla_sessao($this->data, 'PesquisaSatisfacao');
		$conditions 	= $this->PesquisaSatisfacao->converteFiltrosEmConditions( $filtros );
	    $fields = array(
			'PesquisaSatisfacao.codigo', 'PesquisaSatisfacao.codigo_pai', 'PesquisaSatisfacao.data_cadastro',
			'PesquisaSatisfacao.data_para_pesquisa', 'PesquisaSatisfacao.data_pesquisa', 'PesquisaSatisfacao.codigo_produto',
			'PesquisaSatisfacao.codigo_cliente', 'PesquisaSatisfacao.codigo_status_pesquisa',						
			'Cliente.razao_social', 'PesquisaSatisfacaoPai.codigo_status_pesquisa','PesquisaSatisfacaoPai.codigo'
		);

		$joins = array(
				array(
					"table" => "vendas.cliente",
					"alias" => "Cliente",
					"type"  => "INNER",
					"conditions" => array("Cliente.codigo = PesquisaSatisfacao.codigo_cliente")
				),
				array(
					"table" => "vendas.status_pesquisa_satisfacao",
					"alias" => "StatusPesquisaSatisfacao",
					"type"  => "LEFT",
					'conditions' => array("StatusPesquisaSatisfacao.codigo = PesquisaSatisfacao.codigo_status_pesquisa"),
				),
				array(
					"table" => "vendas.pesquisas_satisfacao",
					"alias" => "PesquisaSatisfacaoPai",
					"type"  => "LEFT",
					'conditions' => array("PesquisaSatisfacaoPai.codigo = PesquisaSatisfacao.codigo_pai"),
				),
			);
		$this->paginate['PesquisaSatisfacao'] = array(
			'joins' => array(
				array(
					"table" => "vendas.cliente",
					"alias" => "Cliente",
					"type"  => "INNER",
					"conditions" => array("Cliente.codigo = PesquisaSatisfacao.codigo_cliente")
				),
				array(
					"table" => "vendas.status_pesquisa_satisfacao",
					"alias" => "StatusPesquisaSatisfacao",
					"type"  => "LEFT",
					'conditions' => array("StatusPesquisaSatisfacao.codigo = PesquisaSatisfacao.codigo_status_pesquisa"),
				),
				array(
					"table" => "vendas.pesquisas_satisfacao",
					"alias" => "PesquisaSatisfacaoPai",
					"type"  => "LEFT",
					'conditions' => array("PesquisaSatisfacaoPai.codigo = PesquisaSatisfacao.codigo_pai"),
				),

			),
			'fields' => $fields,
			'conditions' => $conditions,
			'limit'      => 200,
			'order'      => array('PesquisaSatisfacao.data_para_pesquisa', 'Cliente.razao_social' )
		);
		$listagem = $this->paginate('PesquisaSatisfacao');
		$qtd_produto = array( 82=>0, 1=>0 );
		foreach ($listagem as $key => $value ) {			
			if( $value['PesquisaSatisfacao']['codigo_produto'] == 82 )
				$qtd_produto[82] = $qtd_produto[82] + 1;
			else
				$qtd_produto[1] = $qtd_produto[1] + 1;
		}
		$status_pesquisa 	 = $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa' ) );
		$cor_status_pesquisa = array(1 => 'success',2 => 'transito',3 => 'important', 4 =>'warning');
		$this->set(compact('listagem','status_pesquisa','cor_status_pesquisa', 'qtd_produto'));
	}

	function pesquisa_satisfacao_analitico( $tipo_view = false ) {
        if($tipo_view == 'popup')
            $this->layout = 'new_window';    	
        $this->pageTitle   = 'Pesquisa de satisfação - Analítico';
        $this->loadModel("Usuario");
		if( empty($this->data['PesquisaSatisfacao'])){
			$this->data['PesquisaSatisfacao']['data_inicial'] = date("01/m/Y");
			$this->data['PesquisaSatisfacao']['data_final'] = date("d/m/Y");
			$this->data['PesquisaSatisfacao']['status_pesquisa'] = NULL;
		}
		$this->data['PesquisaSatisfacao'] = $this->Filtros->controla_sessao($this->data, 'PesquisaSatisfacao');
        $status_pesquisa   = $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa'));
        $usuarios_pesquisa = $this->Usuario->find('list', array('conditions'=>array('codigo_cliente'=>NULL)) );
    	$this->carrega_combos_pesquisa_satisfacao();
		$this->set(compact('status_pesquisa', 'usuarios_pesquisa', 'sintetico'));
    }

    function pre_listagem_pesquisa_satisfacao_analitico() {
    	$this->data['PesquisaSatisfacao']['consulta_cliente'] = true;
    	$this->Filtros->controla_sessao($this->data, 'PesquisaSatisfacao');
    	$this->redirect(array('action' => 'listagem_pesquisa_satisfacao_analitico'));
    }

    function listagem_pesquisa_satisfacao_analitico() {
       	$this->layout 	= 'ajax';
        $consulta_cliente = false;
        $filtros = $this->Filtros->controla_sessao($this->data, 'PesquisaSatisfacao');
        if (isset($filtros['consulta_cliente'])) {
        	$consulta_cliente = true;
        	$this->Filtros->limpa_sessao('PesquisaSatisfacao');
        }
		$conditions   = $this->PesquisaSatisfacao->converteFiltrosEmConditions( $filtros );
	    $fields = array(
			'PesquisaSatisfacao.codigo', 'PesquisaSatisfacao.data_cadastro',
			'PesquisaSatisfacao.data_para_pesquisa', 'PesquisaSatisfacao.data_pesquisa','PesquisaSatisfacao.codigo_produto',
			'PesquisaSatisfacao.codigo_cliente', 'PesquisaSatisfacao.codigo_status_pesquisa',
			'Cliente.razao_social', 'Usuario.apelido'
		);
	    $conditions2 = array();
	    foreach ($conditions as $campo => $valor ) {
	    	$campo = str_replace('PesquisaSatisfacao.', '', $campo);
	    	if( $campo != 'codigo_status_pesquisa')
	    		$conditions2[$campo] = $valor;
	    }
	    $conditions = $conditions2;
		$dbo = $this->PesquisaSatisfacao->getDataSource();
		$pesquisa_filho = $dbo->buildStatement(
			array(
				'fields' => array('*'),
				'table' => $this->PesquisaSatisfacao->databaseTable.'.'.$this->PesquisaSatisfacao->tableSchema.'.'.$this->PesquisaSatisfacao->useTable,
				'alias' => 'PSF',
				'conditions' => $conditions,
			), $this->PesquisaSatisfacao
		);
		$conditions   = $this->PesquisaSatisfacao->converteFiltrosEmConditions( $filtros );
		$conditions['PesquisaSatisfacaoFilho.codigo'] = NULL;
		$this->paginate['PesquisaSatisfacao'] = array(
			'joins' => array(
				array(
					"table" => "vendas.cliente",
					"alias" => "Cliente",
					"type"  => "INNER",
					"conditions" => array("Cliente.codigo = PesquisaSatisfacao.codigo_cliente")
				),
				array(
					"table" => "vendas.status_pesquisa_satisfacao",
					"alias" => "StatusPesquisaSatisfacao",
					"type"  => "LEFT",
					'conditions' => array("StatusPesquisaSatisfacao.codigo = PesquisaSatisfacao.codigo_status_pesquisa"),
				),
				array(
					"table" => "portal.usuario",
					"alias" => "Usuario",
					"type"  => "LEFT",
					'conditions' => array("Usuario.codigo = PesquisaSatisfacao.codigo_usuario_pesquisa"),
				),				
				array(
					'table' => "({$pesquisa_filho})",
					'alias' => 'PesquisaSatisfacaoFilho',
					'type' => 'LEFT',
					'conditions' => array(
						'PesquisaSatisfacaoFilho.codigo_pai = PesquisaSatisfacao.codigo'
					),
				),
			),
			'fields'     => $fields,
			'conditions' => $conditions,
			'limit'      => 50,
			'order'      => array('PesquisaSatisfacao.data_para_pesquisa', 'Cliente.razao_social' )
		);
		$status_pesquisa = $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa'));		
		$cor_status_pesquisa = array(1 => 'success',2 => 'transito',3 => 'important', 4=>'warning');		
		$listagem = $this->paginate('PesquisaSatisfacao');
        $this->set(compact('listagem', 'filtros', 'status_pesquisa', 'cor_status_pesquisa', 'consulta_cliente'));
    }

    function pesquisa_satisfacao_sintetico() {
		$this->pageTitle = 'Pesquisa de satisfação - Sintético';
		$sintetico 	 	 = true;
		$this->loadModel("Usuario");
		if( empty($this->data['PesquisaSatisfacao'])){
			$this->data['PesquisaSatisfacao']['data_inicial'] = date("01/m/Y");
			$this->data['PesquisaSatisfacao']['data_final'] = date("d/m/Y");
			$this->data['PesquisaSatisfacao']['status_pesquisa'] = NULL;
		}
    	$this->carrega_combos_pesquisa_satisfacao();
		$this->data['PesquisaSatisfacao'] = $this->Filtros->controla_sessao($this->data, 'PesquisaSatisfacao');
		$this->data['PesquisaSatisfacao']['sintetico'] = $sintetico;
		if(empty($this->data['PesquisaSatisfacao']['agrupamento']))
    		$this->data['PesquisaSatisfacao']['agrupamento'] = 1;
		$status_pesquisa   	= $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa'));
		$usuarios_pesquisa 	= $this->Usuario->find('list', array('conditions'=>array('codigo_cliente'=>NULL)) );
    	$agrupamento 		= $this->PesquisaSatisfacao->listaAgrupamento();
		$this->set(compact('status_pesquisa', 'usuarios_pesquisa', 'sintetico', 'agrupamento'));    	
}

    function listagem_pesquisa_satisfacao_sintetico() {
    	$this->layout 	= 'ajax';
    	$this->loadModel('Gestor');
    	$filtros 		= $this->Filtros->controla_sessao($this->data, "PesquisaSatisfacao");
    	$agrupamento 	= 1;
		if( !empty($filtros['agrupamento'] ))
			$agrupamento = $filtros['agrupamento'];
		$conditions  = $this->PesquisaSatisfacao->converteFiltrosEmConditions( $filtros );
		$listagem    = $this->PesquisaSatisfacao->listagem_pesquisas_sintetica( compact('conditions', 'agrupamento' ) );
		$agrupamentos= $this->PesquisaSatisfacao->listaAgrupamento();
		$series = array();		
		$cor_status_pesquisa = array(NULL=>'#999999',1 => '#468847',2 => '#FAF267',3 => '#B94A48', 4=>'#F89406' );
		foreach ($listagem as $key => $dados ){
			if( $agrupamento == 3 ){
				$color = $cor_status_pesquisa[$dados[0]['codigo']];
				$series[] = array('name'=> "'".$dados[0]['nome']."'",'color'=> "'".$color."'",'values' => $dados[0]['total']);
			} else {
				$series[] = array('name'=> "'".$dados[0]['nome']."'",'values' => $dados[0]['total']);				
			}
		}
		$this->set(compact('listagem', 'agrupamento', 'agrupamentos','series', 'filtros'));
    }

    function carrega_combos_pesquisa_satisfacao( ) {
    	$this->loadModel('Usuario');
    	$this->loadModel('Gestor');
    	$authUsuario    = $this->authUsuario;
    	//$authUsuario['Usuario']['codigo'] = 30052;
    	$gestor_logado = FALSE;
    		if(!empty($authUsuario['Usuario']['codigo'])) {
    			$gestor = $this->Gestor->verifica_se_usuairo_gestor($authUsuario['Usuario']['codigo']);
    			if(!empty($gestor) && $gestor['Gestor']['codigo_departamento'] == Departamento::COMERCIAL){
    				$this->data['PesquisaSatisfacao']['codigo_gestor'] = $gestor['Gestor']['codigo'];
    				$gestor_logado = TRUE;
    			}elseif(!empty($gestor) && $gestor['Gestor']['codigo_departamento'] == Departamento::GESTOR_NPE) {
    				$this->data['PesquisaSatisfacao']['codigo_gestor_npe'] = $gestor['Gestor']['codigo'];
    				$gestor_logado = TRUE;
    			}
    		}
		$gestores_com 		= $this->Gestor->listarNomesGestoresAtivos();		
		$gestores_npe 	= $this->Usuario->lista_gestor_npe( (FALSE) );
		$this->set(compact('gestores_npe', 'gestores_com', 'gestor_logado'));

    }

    function visualizar_pesquisa( $codigo_pesquisa ){
		$this->layout    = 'ajax';
		$this->pageTitle = false;
		$dados_pesquisa  = $this->PesquisaSatisfacao->carregarPesquisa( $codigo_pesquisa );
		$this->data 	 = $dados_pesquisa[0];		
    }

    function pesquisa_satisfacao_anual () {
    	$filtrado = FALSE; 
    	$this->pageTitle = 'Pesquisa de Satisfação Anual';
    	$this->loadModel('Usuario');
    	$this->data['PesquisaSatisfacaoAnual'] = $this->Filtros->controla_sessao($this->data, "PesquisaSatisfacaoAnual");
    	$anos = Comum::listAnos(date('Y')-3);
		$usuarios_pesquisa = $this->Usuario->find('list', array('conditions'=>array('codigo_cliente'=>NULL)) );
		$status_pesquisa   = $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa'));
		$status_pesquisa += array(5 => 'Cancelado', 6 => 'Bloqueado', 7 => 'Sem Pesquisa');
		unset($this->data['data_inicial']);
		unset($this->data['data_final']);
		$this->data['PesquisaSatisfacaoAnual']['ano'] = (isset($this->data['PesquisaSatisfacaoAnual']['ano']) ? $this->data['PesquisaSatisfacaoAnual']['ano'] : date("Y"));
		$this->set(compact('meses', 'anos', 'usuarios_pesquisa', 'status_pesquisa'));
    }

    function listagem_pesquisa_satisfacao_anual() {
    	$this->loadModel('Cliente');
    	$this->loadModel('ClienteProduto');
    	$this->loadModel('ClienteProdutoLog');
    	$this->loadModel('MotivoBloqueio');
    	$this->loadModel('StatusPesquisaSatisfacao');
    	$this->layout 	= 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'PesquisaSatisfacaoAnual');
		$conditions = $this->PesquisaSatisfacao->converteFiltrosEmConditionsCte($filtros);

		$sub_query = "(CASE 
						WHEN %s.data_pesquisa is null AND  %s.data_para_pesquisa is not null
						THEN 0
						WHEN %s_bloqueio.codigo_motivo_bloqueio = 17 
						THEN 5 
						WHEN %s_bloqueio.codigo_motivo_bloqueio = 8 
						THEN 6 ELSE %s.codigo_status_pesquisa 
						END) AS %s";

		$fields = array(
			'base.codigo_cliente AS codigo_cliente', 
			'base.codigo_produto AS codigo_produto',
			'RTRIM(LTRIM(Cliente.razao_social)) AS razao_social',
			 sprintf($sub_query, 'janeiro', 'janeiro', 'janeiro', 'janeiro', 'janeiro', 'janeiro'), 
			 sprintf($sub_query, 'fevereiro', 'fevereiro', 'fevereiro', 'fevereiro', 'fevereiro', 'fevereiro'), 
			 sprintf($sub_query, 'marco', 'marco', 'marco', 'marco', 'marco', 'marco'), 
			 sprintf($sub_query, 'abril', 'abril', 'abril', 'abril', 'abril', 'abril'), 
			 sprintf($sub_query, 'maio', 'maio', 'maio', 'maio', 'maio', 'maio'), 
			 sprintf($sub_query, 'junho', 'junho', 'junho', 'junho', 'junho', 'junho'), 
			 sprintf($sub_query, 'julho', 'julho', 'julho', 'julho', 'julho', 'julho'), 
			 sprintf($sub_query, 'agosto', 'agosto', 'agosto', 'agosto', 'agosto', 'agosto'), 
			 sprintf($sub_query, 'setembro', 'setembro', 'setembro', 'setembro', 'setembro', 'setembro'), 
			 sprintf($sub_query, 'outubro', 'outubro', 'outubro', 'outubro', 'outubro', 'outubro'), 
			 sprintf($sub_query, 'novembro', 'novembro', 'novembro', 'novembro', 'novembro', 'novembro'), 
			 sprintf($sub_query, 'dezembro', 'dezembro', 'dezembro', 'dezembro', 'dezembro', 'dezembro'), 
		);
		$table_cliente_produto_log = $this->ClienteProdutoLog->databaseTable.'.'.$this->ClienteProdutoLog->tableSchema.'.'.$this->ClienteProdutoLog->useTable;
		$table_pesquisa_satisfacao = $this->PesquisaSatisfacao->databaseTable.'.'.$this->PesquisaSatisfacao->tableSchema.'.'.$this->PesquisaSatisfacao->useTable;
		$joins = array(
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "janeiro",
						"type"  => "LEFT",
						"conditions" => array("janeiro.codigo = base.janeiro")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "fevereiro",
						"type"  => "LEFT",
						"conditions" => array("fevereiro.codigo = base.fevereiro")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "marco",
						"type"  => "LEFT",
						"conditions" => array("marco.codigo = base.marco")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "abril",
						"type"  => "LEFT",
						"conditions" => array("abril.codigo = base.abril")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "maio",
						"type"  => "LEFT",
						"conditions" => array("maio.codigo = base.maio")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "junho",
						"type"  => "LEFT",
						"conditions" => array("junho.codigo = base.junho")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "julho",
						"type"  => "LEFT",
						"conditions" => array("julho.codigo = base.julho")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "agosto",
						"type"  => "LEFT",
						"conditions" => array("agosto.codigo = base.agosto")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "setembro",
						"type"  => "LEFT",
						"conditions" => array("setembro.codigo = base.setembro")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "outubro",
						"type"  => "LEFT",
						"conditions" => array("outubro.codigo = base.outubro")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "novembro",
						"type"  => "LEFT",
						"conditions" => array("novembro.codigo = base.novembro")

					),
					array(
						"table" => $table_pesquisa_satisfacao,
						"alias" => "dezembro",
						"type"  => "LEFT",
						"conditions" => array("dezembro.codigo = base.dezembro")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "janeiro_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("janeiro_bloqueio.codigo = base.janeiro_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "fevereiro_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("fevereiro_bloqueio.codigo = base.fevereiro_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "marco_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("marco_bloqueio.codigo = base.marco_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "abril_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("abril_bloqueio.codigo = base.abril_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "maio_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("maio_bloqueio.codigo = base.maio_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "junho_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("junho_bloqueio.codigo = base.junho_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "julho_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("julho_bloqueio.codigo = base.julho_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "agosto_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("agosto_bloqueio.codigo = base.agosto_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "setembro_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("setembro_bloqueio.codigo = base.setembro_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "outubro_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("outubro_bloqueio.codigo = base.outubro_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "novembro_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("novembro_bloqueio.codigo = base.novembro_bloqueio")

					),
					array(
						"table" => $table_cliente_produto_log,
						"alias" => "dezembro_bloqueio",
						"type"  => "LEFT",
						"conditions" => array("dezembro_bloqueio.codigo = base.dezembro_bloqueio")

					),

					array(
						"table" => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
						"alias" => "Cliente",
						"type"  => "LEFT",
						"conditions" => array("Cliente.codigo = base.codigo_cliente")

					),
				);

			// $order = array('razao_social',
			//	           'codigo_produto');
			$this->paginate['PesquisaSatisfacao'] = array(
				'joins'		 => $joins,
				'fields'     => $fields,
				'conditions' => $conditions,
				'order'		 => null,
				'limit'      => 50,
				'page'	     => 1,
				'method' 	 => 'pesquisa_anual'
			);
		$status_pesquisa = $this->StatusPesquisaSatisfacao->find('list',array('fields'=>'descricao_pesquisa'));	
		$status_pesquisa += array(5 => 'Cancelado', 6 => 'Bloqueado', 7 => 'Sem Pesquisa');	
		$cor_status_pesquisa = array(1 => 'success',2 => 'transito',3 => 'important', 4=>'warning',  5=>'ativo', 6=>'bloqueado', 7=>'sem-utilizacao', 99 => '');		
		$listagem = $this->paginate('PesquisaSatisfacao');
        $this->set(compact('listagem', 'filtros', 'status_pesquisa', 'cor_status_pesquisa'));
    }

}
?>
