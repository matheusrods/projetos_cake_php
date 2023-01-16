<?php
class Glosas extends AppModel {

	public $name		   	= 'Glosas';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'glosas';
	public $primaryKey	   	= 'codigo';
	public $actsAs          = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_glosas'));


	/**
	 * [converteFiltroEmConditiON description]
	 * 
	 * monta as conditions
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function converteFiltroEmConditiON($data) 
	{
		$conditions = array();

		if (!empty($data['codigo_fornecedor'])) {
			$conditions['Glosas.codigo_fornecedor'] = $data['codigo_fornecedor'];
		}

		if (!empty($data['numero_nfs'])) {
			$conditions['NotaFiscalServico.numero_nota_fiscal'] = $data['numero_nfs'];
		}

		if (!empty($data['status'])) {
			$conditions['Glosas.codigo_status_glosa'] = $data['status'];
		}

		if (!empty($data['codigo_nota_fiscal_servico'])) {
			$conditions['NotaFiscalServico.codigo'] = $data['codigo_nota_fiscal_servico'];
		}

		return $conditions;
	}

	/**
	 * [get_glosas description]
	 * 
	 * metodo para pegar os dados do tabela de glosas
	 * 
	 * @param  [type] $filtros [description]
	 * @return [type]          [description]
	 */
	public function get_glosas($filtros)
	{

		//monta as conditions
		$conditions = $this->converteFiltroEmCondition($filtros);
		
		//monta o que irá retornar do select
		$fields = array(
			'Glosas.codigo_fornecedor AS codigo_fornecedor',
			'NotaFiscalServico.numero_nota_fiscal AS numero_nota_fiscal',
			'Glosas.codigo as codigo_glosa',
			'Glosas.codigo_pedidos_exames as codigo_pedido_exame',
			'Exame.descricao as exame',
			'Glosas.valor AS valor',
			'Glosas.data_glosa AS data_glosa',
			'Glosas.data_vencimento AS data_vencimento',
			'Glosas.data_pagamento AS data_pagamento',
			'GlosasStatus.descricao as status_glosa',
			'Glosas.motivo_glosa AS motivo_glosa',
		);

		//monta o join da query
		$joins = array(			
			array(
				'table' => 'RHHealth.dbo.glosas_status',
				'alias' => 'GlosasStatus',
				'type' => 'INNER',
				'conditions' => 'GlosasStatus.codigo = Glosas.codigo_status_glosa',
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = Glosas.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.nota_fiscal_servico',
				'alias' => 'NotaFiscalServico',
				'type' => 'INNER',
				'conditions' => 'Glosas.codigo_nota_fiscal_servico = NotaFiscalServico.codigo',
			),
		);

		//retorna o array para executar
		return array('fields' => $fields,'conditions' => $conditions,'joins' => $joins);

	}//fim getDadosNfs

	/**
	 * Obter Glosas para Notificacao do Prestador
	 *
	 * 	// modelo resultado
	 *	$dados['nome_prestador']= null;
	 *	$dados['nota_fiscal_numero']=null;
	 * 	$dados['nota_fiscal_valor']=null;
	 *	$dados['valor_glosa']=null;
	 *	$dados['valor_glosa_divergencia']=null;
	 *	$dados['exame']=null;
	 *	$dados['previsao_pagamento']=null;
	 * 	$dados['motivo']=null;
	 *	$dados['detalhamento']= array(
	 *		'exame'=> null,
	 *		'quantidade'=> null,
	 *		'valor_cobrado'=> null,
	 *		'valor_tabela'=> null,
	 *		'diferenca_unitaria'=> null,
	 *		'diferenca_total'=> null,
	 *	);
	 *	$dados['procedimentos']= array(
	 *		'empresa'=> null,
	 *		'pedido'=> null,
	 *		'colaborador'=> null,
	 *		'data'=> null,
	 *		'tipo_exame'=> null,
	 *		'procedimento'=> null,
	 *		'motivo'=> null,
	 *		'valor'=> null,
	 *	);
	 * 
	 * 
	 * @param array $filtros
	 * @return void
	 */
	public function obterGlosasParaNotificarPrestador($filtros)
	{
		$dados = array();

		//monta as conditions
		$conditions = $this->converteFiltroEmConditiON($filtros);

		//monta o que irá retornar do select
		$fields = array(
			'Glosas.codigo as codigo_glosa',
			'Glosas.codigo_fornecedor AS codigo_fornecedor',
			'Glosas.codigo_pedidos_exames as codigo_pedido_exame',
			'Glosas.valor as valor_glosa',
			'Glosas.valor as valor_glosa_divergencia', // FIXME:
			'Glosas.data_glosa AS data_glosa',
			'Glosas.data_pagamento AS glosa_data_pagamento',
			'Glosas.motivo_glosa AS motivo',
			'GlosasStatus.descricao as status_glosa',

			'Fornecedor.nome as nome_prestador',
			
			'NotaFiscalServico.codigo AS codigo_nota_fiscal_servico',
			'NotaFiscalServico.numero_nota_fiscal AS nota_fiscal_numero',
			'NotaFiscalServico.valor AS nota_fiscal_valor',
			'NotaFiscalServico.data_pagamento AS previsao_pagamento',

			'Exame.descricao as exame',	
		);

		//monta o join da query
		$joins = array(			
			array(
				'table' => 'RHHealth.dbo.glosas_status',
				'alias' => 'GlosasStatus',
				'type' => 'INNER',
				'conditions' => 'GlosasStatus.codigo = Glosas.codigo_status_glosa',
			),
			array(
				'table' => 'RHHealth.dbo.nota_fiscal_servico',
				'alias' => 'NotaFiscalServico',
				'type' => 'INNER',
				'conditions' => 'Glosas.codigo_nota_fiscal_servico = NotaFiscalServico.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = Glosas.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'RHHealth.dbo.exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo',
			),
		);
	
		$options = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'recursive' => -1
		);
		
		$query = $this->find('all', $options); // pr($query); exit;
		
		$url_website = isset($_SERVER['SERVER_NAME']) ?  $_SERVER['SERVER_NAME'] : 'https://www.rhhealth.com.br';

		foreach ($query as $key => $value) {
			
			
			$dados[$value[0]['nota_fiscal_numero']] = array(
				'nota_fiscal_numero' =>  $value[0]['nota_fiscal_numero'],
				'nota_fiscal_valor' => Comum::formataMoeda($value[0]['nota_fiscal_valor']),
				'nome_prestador' => $value[0]['nome_prestador'],
				'valor_glosa' => Comum::formataMoeda($value[0]['valor_glosa']),
				'valor_glosa_divergencia' => Comum::formataMoeda($value[0]['valor_glosa_divergencia']),
				'exame' => $value[0]['exame'],
				'previsao_pagamento' => $value[0]['previsao_pagamento'],
				'motivo' => $value[0]['motivo'],
				'url_website' => $url_website, // url do site que fica no rodapé do email
				'email_contato' => 'coreexpress@gmail.com' // FIXME: email fornecedor
			);
			
		}

		return $dados;
	}


	public function enviarNotificacaoGlosaAoPrestador($codigo_nota_fiscal_servico = null){

        set_time_limit(0);
		
        $filtros = array();
		// $codigo_nota_fiscal_servico = 317;
		if(!empty($codigo_nota_fiscal_servico)){
			$filtros = array('codigo_nota_fiscal_servico'=>$codigo_nota_fiscal_servico);
		}

		$dados = $this->obterGlosasParaNotificarPrestador($filtros);
		
		// TODO: buscar
		// 1) dados de fornecedor e glosas em uma NF onde as consolidações foram realizadas
		// fornecedor, fornecedores_contato
		
		foreach($dados as $key => $dado){
			
			try{
				//Se não possui e-mail de contato
				// if(!isset($dado['FornecedoresContato']['descricao'])) throw new Exception("Fornecedor não possui e-mail de contato", 1);
				$to = $dado['email_contato'];
				$assunto = 'Informativo de Glosa - NF '.$dado['nota_fiscal_numero'];
				$template = 'notificacao_glosa_ao_prestador';
				
				// trava o envio de multiplos emails para uma mesma nota
				if(!$this->travarEnvio($assunto, $to)){
					if( $this->disparaEmail($dado, $assunto , $template, $to) ){
						echo("enviando e-mail ".$assunto." \n");
						$this->log('Enviando email, O "'.$assunto. '" foi incluido na fila de disparo.', 'debug');
					}
				} else {
					echo("e-mail ".$assunto." não enviado \n");
					$this->log('Email não enviado, O "'.$assunto. '" já foi incluido na fila de disparo.', 'debug');
				}

			}catch (Exception $ex) {
				$this->log($ex->getMessage(), 'debug');
			}
		}

        return true;
	}

	/**
	 * Bloqueia envios de duplo clique
	 *
	 * @param string $assunto
	 * @return boolean
	 */
	private function travarEnvio($assunto)
	{
		$data_inicio = date('Y-m-d')." 00:00:00";
		$data_fim = date('Y-m-d')." 23:59:59";

		$where_email = " AND created >= '".$data_inicio."'";
		$where_email .= " AND created <= '".$data_fim."'";

		$sql_emails = "SELECT [to], subject,attachments,content 
						FROM mailer_outbox 
						WHERE [subject] like '" . $assunto ."'
							AND attachments is not null " . $where_email . " order by [created] ";

		$dados_email = $this->query($sql_emails);

		return !empty($dados_email);
	}
	
	/**
	 * Inclui o email na fila de disparo
	 *
	 * @param array $dados
	 * @param string $assunto
	 * @param string $template
	 * @param string $to
	 * @param boolean $comlink
	 * @return boolean
	 */
    private function disparaEmail($dados = null, $assunto, $template, $to = null, $comlink = false ) {

        if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
			$to = 'tid@ithealth.com.br';
            $cc = null;
        }

        $cc = null;

        App::import('Component', array('StringView', 'Mailer.Scheduler'));

        $this->stringView = new StringViewComponent();
        $this->scheduler = new SchedulerComponent();
        $this->stringView->reset();

        $this->stringView->set('dados', $dados);

        $content = $this->stringView->renderMail($template);

        return $this->scheduler->schedule($content, array (
            'from' => 'portal@rhhealth.com.br',
            'to' => $to,
            'cc' => $cc,
            'subject' => $assunto
        ));
	}
	
	public function conditionsGlosas($data){
		$conditions = array();

        if (!empty($data['numero_nota_fiscal'])) {
            $conditions['NotaFiscalServico.numero_nota_fiscal'] = $data['numero_nota_fiscal'];
        }

        if (!empty($data['numero_nota_fiscal'])) {
            $conditions['NotaFiscalServico.numero_nota_fiscal'] = $data['numero_nota_fiscal'];
        }

        if(!empty($data["data_inicio"])) {
            $data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
            $data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');
            $conditions [] = "(NotaFiscalServico.data_inclusao >= '". $data_inicio . "'";
        }//fim if

        if(!empty($data["data_fim"])) {
            $conditions [] = "NotaFiscalServico.data_inclusao <= '" . $data_fim . "')";
        }

        if (!empty($data['razao_social'])) {
            $conditions['Fornecedor.razao_social LIKE'] = '%'.$data['razao_social'].'%';
        }

        if (!empty($data['nome_fantasia'])) {
            $conditions['Fornecedor.nome LIKE'] = '%'.$data['nome_fantasia'].'%';
        }

        if (!empty($data['codigo_documento'])) {
            $conditions['Fornecedor.codigo_documento like'] = $data['codigo_documento'] . '%';
        }

        if (isset($data['ativo'])){
            if($data['ativo'] == '0') {
                $conditions ['NotaFiscalServico.ativo'] = $data['ativo'];
            } else if ($data['ativo'] == '1') {
                $conditions ['NotaFiscalServico.ativo'] = $data['ativo'];
            }
        }

        if (!empty($data['tipo_glosa'])) {
            $conditions['TipoGlosas.codigo'] = $data['tipo_glosa'];
        }
		
		$conditions[] = 'Glosas.valor IS NOT NULL'; // não trazer registros de glosas com valores zerados
		$conditions[] = 'Glosas.ativo = 1'; // trazer apenas glosas ativas

        return $conditions;
	}

	public function buscaGlosas($conditions){
		//fields
		$fields = array(
			'DISTINCT NotaFiscalServico.codigo',
			'NotaFiscalServico.numero_nota_fiscal',
			'NotaFiscalServico.valor',
			'(SELECT COUNT(*) FROM glosas where codigo_nota_fiscal_servico = NotaFiscalServico.codigo AND ativo = 1) as qtd_glosas',
			'(SELECT SUM(valor) FROM glosas where codigo_nota_fiscal_servico = NotaFiscalServico.codigo AND ativo = 1) as total_glosado',
			'Fornecedor.codigo',
			'Fornecedor.razao_social',
			'Fornecedor.nome',
			'Fornecedor.codigo_documento',
		);
		//joins
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.glosas',
				'alias' => 'Glosas',
				'type' => 'LEFT',
				'conditions' => 'NotaFiscalServico.codigo = Glosas.codigo_nota_fiscal_servico',
			),
			array(
				'table' => 'RHHealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'NotaFiscalServico.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'RHHealth.dbo.tipo_glosas',
				'alias' => 'TipoGlosas',
				'type' => 'LEFT',
				'conditions' => 'Glosas.codigo_tipo_glosa = TipoGlosas.codigo',
			),
			
		);

		$dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields       
        );

		return $dados;

	}

	public function trataDados($dados){

        foreach ($dados as $key => $dado) {
            $dados[$key]['DadosGlosa']['codigo'] = $dado['Glosas']['codigo'];
            $dados[$key]['DadosGlosa']['codigo_pedidos_exames'] = $dado['Glosas']['codigo_pedidos_exames'];
            $dados[$key]['DadosGlosa']['valor'] = Comum::moeda($dado['Glosas']['valor']);
            $dados[$key]['DadosGlosa']['data_glosa'] = $dado['Glosas']['data_glosa'];
            $dados[$key]['DadosGlosa']['data_pagamento'] = $dado['Glosas']['data_pagamento'];
            $dados[$key]['DadosGlosa']['codigo_status_glosa'] = $dado['Glosas']['codigo_status_glosa'];
            $dados[$key]['DadosGlosa']['data_vencimento'] = $dado['Glosas']['data_vencimento'];
            $dados[$key]['DadosGlosa']['motivo_glosa'] = $dado['Glosas']['motivo_glosa'];
            $dados[$key]['DadosGlosa']['exame'] = $dado['Exame']['descricao'];
            $dados[$key]['DadosGlosa']['status'] = $dado['GlosasStatus']['descricao'];       
	        
            switch ($dado['Glosas']['ativo']) {
                case 0:
                    $dados[$key]['DadosGlosa']['ativo'] = 'Inativa';
                    break;
                case 1:
                    $dados[$key]['DadosGlosa']['ativo'] = 'Ativa';
                    break;
            }

	        unset($dados[$key]['Glosas']);
	        unset($dados[$key]['Exame']);
	        unset($dados[$key]['GlosasStatus']);
        }//fim foreach

        foreach ($dados as $key1 => $dadoGlosa) {    
            foreach ($dadoGlosa['DadosGlosa'] as $key2 => $value) {
                if(empty($value))
                    $dados[$key1]['DadosGlosa'][$key2] = '';
            }
        }
        

        return $dados;
    }
}