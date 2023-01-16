<?php
class ChecklistsController extends AppController {
	var $name = 'Checklists';
	var $helpers = array('Highcharts');
	var $components = array('DbbuonnyMonitora','DbbuonnyGuardian');
	var $uses = array('TCveiChecklistVeiculo', 'TPessPessoa', 'TVeicVeiculo', 'TUcveUltimoChecklistVeiculo');

	function analitico() {
		$this->layout = 'new_window';
		$this->loadModel("TTveiTipoVeiculo");
		$this->pageTitle = 'Checklists Analítico';
		$this->data['TCveiChecklistVeiculo'] = $this->Filtros->controla_sessao($this->data, 'TCveiChecklistVeiculo');
		$status = $this->TCveiChecklistVeiculo->listStatus();
		$veiculos_tipos = $this->TTveiTipoVeiculo->lista();
		$veiculos_tipos[99] = 'TODOS DIFERENTES DE CARRETA';
		$this->set(compact('status','veiculos_tipos'));
	}

	function analitico_listagem() {
		$this->data['TCveiChecklistVeiculo'] = $this->Filtros->controla_sessao($this->data, 'TCveiChecklistVeiculo');
		$bindType = null;
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$this->data['TCveiChecklistVeiculo']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		if (isset($this->data['TCveiChecklistVeiculo']['codigo_cliente']) && !empty($this->data['TCveiChecklistVeiculo']['codigo_cliente'])) {
			$pjur_pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($this->data['TCveiChecklistVeiculo']['codigo_cliente'], false);
			$pess_pessoa = $this->TPessPessoa->carregar($pjur_pess_oras_codigo);
			$bindType = $pess_pessoa['TPessPessoa']['pess_tipo'];
		}
		$conditions = $this->TCveiChecklistVeiculo->converteFiltrosEmConditions($this->data['TCveiChecklistVeiculo']);
		$fields = array(
			'TCveiChecklistVeiculo.cvei_codigo',
			'TCveiChecklistVeiculo.cvei_data_cadastro',
			'TCveiChecklistVeiculo.cvei_data_cancelamento',
			'TCveiChecklistVeiculo.cvei_veic_oras_codigo',
			'TCveiChecklistVeiculo.cvei_usuario_adicionou',
			'TCveiChecklistVeiculo.cvei_status',
			'TVeicVeiculo.veic_placa',
			'TVeicVeiculo.veic_pess_oras_codigo_propri',
			'Transportador.pjur_razao_social',
		);
		if ($bindType) {
			$fields = array_merge($fields, array(
				'TRefeReferencia.refe_codigo',
				'TRefeReferencia.refe_descricao',
			));
		}
		$this->paginate['TCveiChecklistVeiculo'] 	= array(
			'conditions' => $conditions,
			'limit' => 50,
			'bindType' => $bindType,
			'bind' => true,
			'fields' => $fields,
		);
		$checklists = $this->Paginate('TCveiChecklistVeiculo');
		$this->set(compact('checklists'));
	}

	function sintetico() {
		$this->loadModel("TTveiTipoVeiculo");
		$this->pageTitle = 'Checklists Sintético';
		$this->data['TCveiChecklistVeiculo'] = $this->Filtros->controla_sessao($this->data, 'TCveiChecklistVeiculo');
		$agrupamentos = $this->TCveiChecklistVeiculo->agrupamentos();
		$status = $this->TCveiChecklistVeiculo->listStatus();
		$veiculos_tipos = $this->TTveiTipoVeiculo->lista();
		$veiculos_tipos[99] = 'TODOS DIFERENTES DE CARRETA';
		$this->set(compact('agrupamentos', 'status','veiculos_tipos'));
	}

	function sintetico_listagem() {
		
		$this->data['TCveiChecklistVeiculo'] = $this->Filtros->controla_sessao($this->data, 'TCveiChecklistVeiculo');
		$bindType = null;
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$this->data['TCveiChecklistVeiculo']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		if (isset($this->data['TCveiChecklistVeiculo']['codigo_cliente']) && !empty($this->data['TCveiChecklistVeiculo']['codigo_cliente'])) {
			$pjur_pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($this->data['TCveiChecklistVeiculo']['codigo_cliente'], false);
			$pess_pessoa = $this->TPessPessoa->carregar($pjur_pess_oras_codigo);
			$bindType = $pess_pessoa['TPessPessoa']['pess_tipo'];
		}
		$conditions = $this->TCveiChecklistVeiculo->converteFiltrosEmConditions($this->data['TCveiChecklistVeiculo']);
		$checklists = $this->TCveiChecklistVeiculo->sintetico($conditions, $bindType, $this->data['TCveiChecklistVeiculo']['agrupamento']);
		$this->sintetico_grafico($checklists, $this->data['TCveiChecklistVeiculo']['agrupamento']);
				
		$this->set(compact('checklists'));
	}

	function sintetico_grafico(&$checklists, $agrupamento) {
		$series = array();
		foreach ($checklists as $key =>$checklist) {
			$mensagem = ($agrupamento == TCveiChecklistVeiculo::AGRP_ALVO_ORIGEM || $agrupamento == TCveiChecklistVeiculo::AGRP_ALVO_CHECKLIST ) ? 'Não localizado' : 'Não informado';
			$total = ($checklist['0']['qtd_aprovados'] + $checklist['0']['qtd_reprovados'] + $checklist['0']['qtd_recusados']);
			$name = (empty($checklist[0]['descricao']) ? $mensagem : trim($checklist[0]['descricao']));
			$checklists[$key][0]['descricao'] = $name;
			$series[] = array(
				'name' => "'{$name}'", 
				'values' => (int)$total,
			);
		}
		$this->set(compact('series'));
	}
	//function listar_checklist_por_placa($placa, $data_ini, $data_fim){
	function listar_checklist_por_placa($placa){
		$placa = str_replace("-", '', strtoupper($placa));

		//$data_ini = (date('Ymd 00:00:00', $data_ini));
		//$data_fim = (date('Ymd 23:59:59', $data_fim));
		$fields = array(
				'TVeicVeiculo.veic_placa',
				'TCveiChecklistVeiculo.cvei_codigo',
				'TCveiChecklistVeiculo.cvei_data_cancelamento',
				'TCveiChecklistVeiculo.cvei_data_cadastro',				
				'TCveiChecklistVeiculo.cvei_status',
				'TCveiChecklistVeiculo.cvei_pess_oras_codigo',
				"1+(EXTRACT(EPOCH FROM NOW() - cvei_data_cadastro) / 3600 / 24)::int AS dias_checklist"
			);
		$joins = array(
					array(
						'table'      => "{$this->TVeicVeiculo->databaseTable}.{$this->TVeicVeiculo->tableSchema}.{$this->TVeicVeiculo->useTable}",
						'alias'      => 'TVeicVeiculo',
						'type'       => 'INNER',
						'conditions' => 'TCveiChecklistVeiculo.cvei_veic_oras_codigo = TVeicVeiculo.veic_oras_codigo AND TVeicVeiculo.veic_placa = \''.$placa.'\''
					)												
				);
		$results = $this->TCveiChecklistVeiculo->find('all', array(
			'fields'     => $fields, 
			'joins'      => $joins,
			'conditions' => array(
					'TCveiChecklistVeiculo.cvei_principal = true',
					//'TCveiChecklistVeiculo.cvei_data_cadastro between \''.$data_ini.'\' and \''.$data_fim.'\'',
				),
			'order'      => 'TCveiChecklistVeiculo.cvei_data_cadastro',
			'group'      => array(
				'TVeicVeiculo.veic_placa',
				'TCveiChecklistVeiculo.cvei_codigo',
				'TCveiChecklistVeiculo.cvei_data_cadastro',				
				'TCveiChecklistVeiculo.cvei_status',
				'TCveiChecklistVeiculo.cvei_pess_oras_codigo',
				'dias_checklist')
			));

		$status = $this->TCveiChecklistVeiculo->listStatus();

		$this->set(compact('results','status'));
		
	}
}
?>