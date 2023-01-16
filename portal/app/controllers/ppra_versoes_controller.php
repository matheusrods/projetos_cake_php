<?php
class PpraVersoesController extends AppController {
	public $name = 'PpraVersoes';

	public $uses = array( 
		'GrupoEconomicoCliente',
		'PpraVersoes',
		'Medico'
	);


	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(); 
	}

	public function versoes_ppra(){
		//$this->autoRender = false;
		$this->pageTitle = 'Consulta Versões PGR';
		
		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$this->data['PpraVersoes']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		//$unidades	= $this->GrupoEconomicoCliente->getUnidadesFromVersoesPCMSO();
		$unidades = $this->GrupoEconomicoCliente->lista($this->data['PpraVersoes']['codigo_cliente']);
		$medicos 	= $this->Medico->getMedicosFromVersoesPpra();

		$this->set(compact('unidades', 'medicos'));
	}//FINAL FUNCTION versoes_pcmso

	public function listagem(){
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, 'PpraVersoes');
		$authUsuario = $this->BAuth->user();

		//debug($this->authUsuario['Usuario']);

		if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		$listagem = array();

		if (!empty($filtros['codigo_cliente'])) {

			$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
			
			if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
				$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
			}

			$fields = array('PpraVersoes.codigo',
				'PpraVersoes.codigo_cliente_alocacao',
				'GrupoEconomico.descricao',
				'PpraVersoes.versao',
				'Medicos.nome', 
				'PpraVersoes.inicio_vigencia_ppra', 
				'PpraVersoes.periodo_vigencia_ppra', 
				'DATEADD(MONTH, CONVERT(INT, PpraVersoes.periodo_vigencia_ppra), PpraVersoes.inicio_vigencia_ppra) as final_vigencia_ppra', 
			);

			$joins = array(
				array(
					'table' => 'grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => 'GrupoEconomicoCliente.codigo_cliente = PpraVersoes.codigo_cliente_alocacao',
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
					'conditions' => 'Medicos.codigo = PpraVersoes.codigo_medico',
				),
			);

			$conditions = $this->PpraVersoes->converteFiltrosEmConditions($filtros);

			$order = array('PpraVersoes.versao');

			$this->paginate['PpraVersoes'] = array(
				'recursive' => -1,	
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'limit' => 50,
				'order' => $order
			);

			$listagem = $this->paginate('PpraVersoes');

			$this->set(compact('listagem'));

			$this->set('codigo_grupo_economico', (isset($codigo_grupo_economico) ? $codigo_grupo_economico : ''));

			$this->Filtros->limpa_sessao($this->PpraVersoes->name);

		}//FINAL SE $filtros['codigo_cliente'] DIFERENTE DE VAZIO

	}//FINAL FUNCTION listagem

	/**
	 * [imprimir_relatorio description]
	 * envia para o jasper os dados do codigo da versão
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function imprimir_relatorio($codigo_ppra_versoes, $codigo_cliente_alocacao) 
	{
		$this->__jasperConsulta($codigo_ppra_versoes, $codigo_cliente_alocacao);
	}//fim imprimir_relatorio
	
	private function __jasperConsulta( $codigo_ppra_versoes, $codigo_cliente_alocacao ) 
	{
		
		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/relatorio_ppra_versoes', // especificar qual relatório
			'FILE_NAME'=> basename( 'relatorio_pgr_versoes.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array( 
			'CODIGO_PPRA_VERSOES' => $codigo_ppra_versoes, 
			'CODIGO_CLIENTE' => $codigo_cliente_alocacao
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
		
	} //fim __jasperconsulta

}//FINAL class PCMSOVersoesController