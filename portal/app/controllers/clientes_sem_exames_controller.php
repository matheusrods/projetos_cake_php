<?php

class ClientesSemExamesController extends AppController {
	public $name = 'ClientesSemExames';
	public $uses = array(
		'Cliente', 
		'AplicacaoExame',
		'Exame'
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('index', 'listagem'));
	}	

	function index() {
		$this->pageTitle = 'Clientes sem Exames Contratados (necessário no PCMSO)';
		
		$this->Filtros->limpa_sessao('AplicacaoExame');
		$this->data['AplicacaoExame'] = $this->Filtros->controla_sessao($this->data, 'AplicacaoExame');
		
		$exames = $this->Exame->find('list', array('conditions' => array('ativo' => '1'), 'fields' => array('codigo', 'descricao')));
		$this->set(compact('exames'));
	}

	/**
	 * [listagem description]
	 * 
	 * metodo para buscar a listagem dos dados de exames para a empresa que não tem contrato
	 * 
	 * @return [type] [description]
	 */
	public function listagem() {
		
		$this->layout = 'ajax';

		//variavel auxiliar
		$exames_sem_assinatura = array();

		//seta os filtros
		$filtros = $this->Filtros->controla_sessao($this->data, 'AplicacaoExame');

		//verifica se o usuario é um cliente
       	if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
       		//seta o filtro do usuario cliente
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		//verifica se existe dados em data
		$codigo_cliente = "";
		if(!empty($filtros['codigo_cliente'])) {
			$codigo_cliente = $filtros['codigo_cliente'];

			// $options['conditions']['AplicacaoExame.codigo_cliente'] = $codigo_cliente;

		}//fim filtros

		//pega os dados para listar qual exame que não tem uma assinatura
		$dadosServico = $this->AplicacaoExame->getExamesSemAssinatura($codigo_cliente,$filtros['codigo_exame']);

		
				
		//verifica se existe exames que não tem assinaturas
		if(!empty($dadosServico)) {
			//campos para apresentação
			$options['fields'] = array(
					// 'AplicacaoExame.data_inclusao',
					'Cliente.codigo',
					'Cliente.razao_social',
					'Cliente.nome_fantasia',
					'Servico.codigo',
					'Servico.descricao'
			);
			
			//campos de relacionamento
			$options['joins'] = array(
				array(
					'table' => 'RHHealth.dbo.exames',
					'alias' => 'Exame',
					'type' => 'INNER',
					'conditions' => array('Exame.codigo = AplicacaoExame.codigo_exame')
				),
				array(
					'table' => 'RHHealth.dbo.servico',
					'alias' => 'Servico',
					'type' => 'INNER',
					'conditions' => array('Servico.codigo = Exame.codigo_servico')
				),
				array(
					'table' => 'RHHealth.dbo.cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array('Cliente.codigo = AplicacaoExame.codigo_cliente')
				)
			);
			
			$options['recursive'] = '-1';

			//varre dados servicos
			foreach($dadosServico as $cod_cliente => $servico){
				//formata os para filtrar
				$servicos = implode(',',$servico);
				//verifica os servicos
				if(!empty($servicos)) {
					//acrescenta na query
					$exames_sem_assinatura_query = $this->AplicacaoExame->find('sql', $options);
					$exames_sem_assinatura_query .= " AND Cliente.ativo = 1 AND AplicacaoExame.codigo_cliente = ".$cod_cliente." AND Servico.codigo IN (".$servicos.")";
					$exames_sem_assinatura_query .= " GROUP BY Cliente.codigo,Cliente.razao_social,Cliente.nome_fantasia,Servico.codigo,Servico.descricao";
					//executa
					$exames_sem_assinatura[] = $this->AplicacaoExame->query($exames_sem_assinatura_query);
				}//fim if servicos
			}//fim foreach
			// pr($exames_sem_assinatura);exit;			
		}//fim empty de dadosServico
		
		
		// $clientes = $this->paginate('ClienteImplantacao');
		$this->set(compact('exames_sem_assinatura'));
	}//fim listagem

}