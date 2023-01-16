<?php
class PCMSOVersoesController extends AppController {
	public $name = 'PcmsoVersoes';
	public $uses = array( 
		// 'Cliente', 
		// 'ClienteImplantacao',
		// 'Cargo', 
		// 'Setor', 
		// 'Funcionario',
		// 'GrupoEconomico',
		'GrupoEconomicoCliente',
		// 'Fornecedor',
		// 'VEndereco',
		// 'FornecedorEndereco',
		// 'OrdemServico',
		// 'OrdemServicoItem',
		// 'StatusOrdemServico',
		// 'ClienteEndereco',
		// 'Endereco',
		// 'EnderecoTipo',
		// 'EnderecoBairro',
		// 'EnderecoCidade',
		// 'EnderecoEstado',
		// 'Servico',
		'PcmsoVersoes',
		// 'ClienteFuncionario',
		// 'GrupoHomogeneo',
		// 'GrupoExposicao',
		// 'AplicacaoExame',
		'AplicacaoExameVersoes',
		// 'Alerta',
		// 'AlertaTipo',
		// 'PrevencaoRiscoAmbiental',
		// 'Gpra',
		'Medico'
	);


	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow('versoes_pcmso', 'listagem', 'imprimir_relatorio'); 
	}

	public function versoes_pcmso(){
		//$this->autoRender = false;
		$this->pageTitle = 'Consulta Versões PCMSO';
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['PcmsoVersoes']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		//$unidades	= $this->GrupoEconomicoCliente->getUnidadesFromVersoesPCMSO();
		$unidades = $this->GrupoEconomicoCliente->lista($this->data['PcmsoVersoes']['codigo_cliente']);
		$medicos 	= $this->Medico->getMedicosFromVersoesPCMSO();

		$this->set(compact('unidades', 'medicos'));
	}//FINAL FUNCTION versoes_pcmso

	public function listagem(){
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, 'PcmsoVersoes');
		$authUsuario = $this->BAuth->user();

		//debug($this->authUsuario['Usuario']);

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		$listagem = array();

		if (!empty($filtros['codigo_cliente'])) {

			$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', 
				array('conditions' => 
					array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']
				), 
					'recursive' => '-1', 
					'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico')
			);
			
			if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
				$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
			}

			$fields = array('PcmsoVersoes.codigo',
							'PcmsoVersoes.codigo_cliente_alocacao',
							'GrupoEconomico.descricao',
							'PcmsoVersoes.versao',
							'Medicos.nome', 
							'PcmsoVersoes.inicio_vigencia_pcmso', 
							'PcmsoVersoes.periodo_vigencia_pcmso', 
							'DATEADD(MONTH, CONVERT(INT, PcmsoVersoes.periodo_vigencia_pcmso), PcmsoVersoes.inicio_vigencia_pcmso) as final_vigencia_pcmso', 
						);

			$joins = array(
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => 'GrupoEconomicoCliente.codigo_cliente = PcmsoVersoes.codigo_cliente_alocacao',
				),
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
				),
				array(
					'table' => 'medicos',
					'alias' => 'Medicos',
					'type' => 'LEFT',
					'conditions' => 'Medicos.codigo = PcmsoVersoes.codigo_medico',
				),
			);

			$conditions = $this->PcmsoVersoes->converteFiltrosEmConditions($filtros);

			$order = array('PcmsoVersoes.versao');

			$this->paginate['PcmsoVersoes'] = array(
				'recursive' => -1,	
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'limit' => 50,
				'order' => $order
			);

			//pr(  $this->PcmsoVersoes->find('sql', $this->paginate['PcmsoVersoes'] ));

			$listagem = $this->paginate('PcmsoVersoes');

			$this->set(compact('listagem'));

			$this->set('codigo_grupo_economico', (isset($codigo_grupo_economico) ? $codigo_grupo_economico : ''));

			$this->Filtros->limpa_sessao($this->PcmsoVersoes->name);
		}//FINAL SE $filtros['codigo_cliente'] DIFERENTE DE VAZIO
	}//FINAL FUNCTION listagem

	public function imprimir_relatorio($codigo_cliente, $codigo_pcmso_versao) {
		$this->__jasperConsulta( $codigo_cliente, $codigo_pcmso_versao );
	}//FINAL FUNCTION imprimir_relatorio
	
	private function __jasperConsulta( $codigo_cliente, $codigo_pcmso_versao ) {
		
		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/relatorio_pcmso_versao', // especificar qual relatório
			'FILE_NAME'=> basename( 'relatorio_pcmso_versao.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array( 
			'CODIGO_CLIENTE' => $codigo_cliente, 
			'CODIGO_PCMSO_VERSAO' => $codigo_pcmso_versao 
		);

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		$this->loadModel('MultiEmpresa');
		//codigo empresa emulada
		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
		//url logo da multiempresa
		$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);	

		try {
		
			// envia dados ao componente para gerar
			$url = $this->Jasper->generate( $parametros, $opcoes );	

			if($url){
				// se obter retorno apresenta usando cabeçalho apropriado
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header('Content-type: application/pdf');
				echo $url; exit;
			}

		} catch (Exception $e) {
			// se ocorreu erro
			debug($e); exit;
		}		

		exit;

	}//FINAL FUNCTION __jasperConsulta
	
}//FINAL CLASS PCMSOVersoesController