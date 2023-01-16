<?php
App::import('Component', array('StringView', 'Mailer.Scheduler'));
class Cotacao extends AppModel {
	public $name = 'Cotacao';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'cotacoes';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable');

	public $hasMany = array(
		'ItemCotacao' => array(
			'className' => 'ItemCotacao',
			'foreignKey' => 'codigo_cotacao',
			'dependent' => true
			)
		);

	public $belongsTo = array(
		'Cliente' => array(
			'className' => 'Cliente',
			'foreignKey' => 'codigo_cliente',
			),
		'Vendedor' => array(
			'className' => 'Vendedor',
			'foreignKey' => 'codigo_vendedor',
			),
		'FormaPagto' => array(
			'className' => 'FormaPagto',
			'foreignKey' => 'codigo_forma_pagto',
			)
		);

	public function validaEmailTelefone()
	{
		if(empty($this->data['Cotacao']['telefone']) && empty($this->data['Cotacao']['email'])) return false; 
		return true;
	}

	public function converteFiltroEmCondition($data) {
		$conditions = array();
		if (!empty($data['codigo']))
			$conditions['Cotacao.codigo'] = $data['codigo'];

		if (!empty($data['nome']))
			$conditions ['Cotacao.nome LIKE'] = '%' . $data['nome'] . '%';

		if(!empty($data['data_de']) && !empty($data['data_ate'])) {
			$data_de = DateTime::createFromFormat('d/m/Y', $data['data_de']);
			$data_ate = DateTime::createFromFormat('d/m/Y', $data['data_ate']);
			$conditions['Cotacao.data_inclusao BETWEEN ? AND ?'] = array($data_de->format('Y-m-d').' 00:00', $data_ate->format('Y-m-d').' 23:59');
		} else {
			if (!empty($data['data_de'])) {
				$date = DateTime::createFromFormat('d/m/Y', $data['data_de']);
				$conditions['Cotacao.data_inclusao >='] = $date->format('Y-m-d').' 00:00';
			}
			if (!empty($data['data_ate'])) {
				$date = DateTime::createFromFormat('d/m/Y', $data['data_ate']);
				$conditions['Cotacao.data_inclusao <='] =  $date->format('Y-m-d').' 23:59';
			}
		}
		return $conditions;
	}


	public function enviaCotacaoPorEmail($cliente, $email_cliente, $vendedor, $forma_pagto, $dados) {
   		$this->StringView = new StringViewComponent();
   		$this->Scheduler  = new SchedulerComponent();
		$this->StringView->set(compact('cliente', 'email_cliente', 'vendedor', 'forma_pagto', 'dados'));
		$content = $this->StringView->renderMail('envio_cotacao_email', 'default');
		$options = array(
			'from' => 'portal@rhhealth.com.br',
			'sent' => null,
			'to' => $email_cliente,
			'subject' => 'Cotação Online',
			);
		
		return $this->Scheduler->schedule($content, $options) ? true: false;
	}

}