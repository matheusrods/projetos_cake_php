<?php


App::import('Component', 'StringView');

class PreFaturamento extends AppModel {

	public $name		   	= 'PreFaturamento';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'pre_faturamento';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');
	
	/**
	 * Lista os pedidos de exames do funcionário
	 */
	public function listar($filtros) {
		
		$where = "";

		if (!empty($filtros['codigo_cliente'])) {
			$where .= " codigo_cliente = '{$filtros['codigo_cliente']}' ";
		}else{
			if (!empty($filtros['status'])) {
				$where .= " status = '{$filtros['status']}' ";
			}
		}

		if (!empty($filtros['codigo_unidade'])) {
			$where .= " AND codigo_unidade = '{$filtros['codigo_unidade']}' ";
		}

		if (!empty($filtros['codigo_pagador'])) {
			$where .= " AND codigo_pagador = '{$filtros['codigo_pagador']}' ";
		}

		if (!empty($filtros['forma_de_cobranca'])) {
			$where .= " AND forma_de_cobranca = '{$filtros['forma_de_cobranca']}' ";
		}
		
		if (!empty($filtros['mes'])) {
			$where .= " AND (MONTH(data_baixa_exame) = '{$filtros['mes']}') ";
		}

		if (!empty($filtros['ano'])) {
			$where .= " AND (YEAR(data_baixa_exame) = '{$filtros['ano']}') ";
		}
		/*
		if (!empty($filtros['mes_realizacao'])) {
			$where .= " AND (MONTH(data_realizacao_do_exame) = '{$filtros['mes_realizacao']}') ";
		}

		if (!empty($filtros['ano_realizacao'])) {
			$where .= " AND (YEAR(data_realizacao_do_exame) = '{$filtros['ano_realizacao']}') ";
		}
		*/
		if (!empty($filtros['status'])) {
			$where .= " AND status = '{$filtros['status']}' ";
		}

		$rst = $this->find('all',array('conditions' => $where));

		//debug($this->find('sql',array('conditions' => $where)));

		// resultado válido
		$saida = (isset($rst)) ? $rst: false;
		
		return $saida;
	}

	public function scheduleMailPreFaturamento($email) {
    
		//monta o email para ser enviado
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();
		$this->Scheduler  = new SchedulerComponent();

		//seta os dados para o email
		$this->StringView->reset();

		$content = $this->StringView->renderMail('emails_validacao_faturamento');
		
		$this->Scheduler->schedule($content, array(
			'from' => 'nfe@rhhealth.com.br',
			'to' => $email,
			'subject' => 'Lembrete - Validação de Pré-Faturamento RH Health'
		),
            null,
            null
	    );
		
	}//FINAL FUNCTION scheduleMailFaturamento
}
