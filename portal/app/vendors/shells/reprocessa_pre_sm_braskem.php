<?php
App::import('Component');
App::import('Component', 'SmSoap');
class ReprocessaPreSMBraskemShell extends Shell {
	var $uses = array(
		'TViagViagem',
		'TPviaPreViagem',
		'LogIntegracao'
	);

	function main() {
		echo "==================================================\n\n";
	}

	function run() {
		if (!$this->im_running('reprocessaPreSMs'))
        	$this->reprocessaPreSMs();
    }
    
	private function im_running($tipo) {
		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep '{$tipo}'");
			// 1 execução é a execução atual
			return substr_count($cmd, 'cake.php -working') > 1;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"{$tipo}"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;			
		}
	}

	private function reprocessaPreSMs() {
		App::import('Vendor', 'xml'.DS.'xml2_array');
		
		$this->SmSoap = new SmSoapComponent();

		$conditions = Array(
			'codigo_cliente' => 39929,
			'sistema_origem' => 'Pamcary',
			'data_inclusao >=' => '20150810 00:00:00',
			'status' => 1,
			'descricao' => ''
		);
		$pre_sms = $this->LogIntegracao->find('all',compact('conditions'));

		foreach ($pre_sms as $key => $dados) {
			$xml = $dados['LogIntegracao']['conteudo'];
			//$tag_inicio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://portal.localhost/portal/wsdl/PlanWebService"><soapenv:Header/><soapenv:Body><criarPlanoViagem>';
			//$tag_fim = '</criarPlanoViagem></soapenv:Body></soapenv:Envelope>';
			//$xml = str_replace('<sm>', '', $xml);
			//$xml = str_replace('</sm>', '', $xml);

			$objXml = XML2Array::createArray($xml);
			$objXml2 = $objXml['sm'];
			//debug($objXml);
			$result = $this->SmSoap->criarPlanoViagem(Comum::arrayToObject($objXml2));

			if ($result['Resultado']['Codigo']=='00') {
				$dados_atu = Array(
					'codigo' => $dados['LogIntegracao']['codigo'],
					'descricao' => '.'
				);
				//debug($dados_atu);
				$this->LogIntegracao->save($dados_atu);
			} else {
				debug($result);
			}

		}
	}

	/*
    private function verifica_clientes_pagadores_monitora(){
    	$clientes = $this->Recebsm->find('all',array(
    		'fields' => array(
				'cliente_pagador_faturado',
				'SM'
			),
			'conditions' => array(
				array('cliente_pagador_faturado <>' => null),
				array('cliente_pagador_faturado <>' => 0),
				array('Dta_Receb between ? and ?' =>  array('2015-01-01 00:00:00','2015-01-31 23:59:59'))
			),
			'group' => array(
				'cliente_pagador_faturado',
				'SM'
			),
		));
		return $clientes;   
    }

    private function verifica_seguradora_corretora(){
    	$clientes = $this->verifica_clientes_pagadores_monitora();
   
    	foreach ($clientes as $key => $cliente) {
    		$sm = $cliente['Recebsm']['SM'];
	    	$seguradora_corretora[] = $this->Cliente->find('all',array(
	    		'recursive' => -1,
	    		'fields' => array(
	    			'codigo',
	    			'codigo_seguradora',
	    			'codigo_corretora',
	    			"$sm AS sm"
	    		),
	    		'conditions' => array('codigo' => $cliente['Recebsm']['cliente_pagador_faturado']),
	    	));
    	}    
    	return $seguradora_corretora; 

    }

    private function converter_seguradora_corretora(){
    	$seguradoras_corretoras_portal = $this->verifica_seguradora_corretora();

    	foreach ($seguradoras_corretoras_portal as $key => $seguradora_corretora) {
			
    		$seguradora_portal = $this->Seguradora->find('all',array(
    			'conditions' => array(
    				array('codigo' => $seguradora_corretora[0]['Cliente']['codigo_seguradora']),
    				array('codigo_documento <>' => NULL)
    			),
    		));
    		$corretora_portal = $this->Corretora->find('all',array(
    			'conditions' => array(
	    			array('codigo' => $seguradora_corretora[0]['Cliente']['codigo_corretora']),
	    			array('codigo_documento <>' => NULL)
	    		),
    		));

    		if(isset($seguradora_portal[0])){
	    		$seguradora = $this->TPjurPessoaJuridica->find('all',array(
	    			'conditions' => array('pjur_cnpj' => $seguradora_portal[0]['Seguradora']['codigo_documento']),
	    			'fields' => 'pjur_pess_oras_codigo',
	    		));

	    		if(empty($seguradora)){
	    			$this->TPessPessoa->incluirSeguradoraCorretora(array(
						'pjur_cnpj' 				=> $seguradora_portal[0]['Seguradora']['codigo_documento'],
						'pjur_razao_social'			=> $seguradora_portal[0]['Seguradora']['nome']
					),TRUE);

				$seguradora[0] 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($seguradora_portal[0]['Seguradora']['codigo_documento']);
	    		}
	    	}
    		if(isset($corretora_portal[0])){
	    		$corretora = $this->TPjurPessoaJuridica->find('all',array(
	    			'conditions' => array('pjur_cnpj' => $corretora_portal[0]['Corretora']['codigo_documento']),
	    			'fields' => 'pjur_pess_oras_codigo',
	    		));

	    		if(empty($corretora)){
	    			$this->TPessPessoa->incluirSeguradoraCorretora(array(
						'pjur_cnpj' 				=> $corretora_portal[0]['Corretora']['codigo_documento'],
						'pjur_razao_social'			=> $corretora_portal[0]['Corretora']['nome']
					),TRUE);

				$corretora[0] 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($corretora_portal[0]['Corretora']['codigo_documento']);
	    		}
	    	}

	    	$seguradoras['seguradora'] =  $seguradora[0]['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];  		
	    	$corretoras['corretora'] =  $corretora[0]['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];  		
    	
    		$seguradora_corretora_guardian = array_merge($seguradoras,$corretoras);
    		$seguradora_corretora_guardian['sm'] = $seguradora_corretora[0][0]['sm'];
 			
 			$seguradoras_corretoras[] = $seguradora_corretora_guardian;
    	}
       	return $seguradoras_corretoras;
    }

    public function atualizar_seguradora_corretora(){
    	$seguradoras_corretoras = $this->converter_seguradora_corretora();
    
    	foreach ($seguradoras_corretoras as $key => $seguradora_corretora) {
	    	$viagens = $this->TViagViagem->find('all',array(
	    		'conditions' => array(
	    			'viag_codigo_sm' => $seguradora_corretora['sm'],
	    		),    
	    		'fields' => array('viag_codigo')
	    	));
	
	    	if(isset($viagens[0]['TViagViagem']['viag_codigo']) && !empty($viagens[0]['TViagViagem']['viag_codigo'])){
		    	$viagens[0]['TViagViagem']['viag_segu_pjur_pess_oras_codigo'] = $seguradora_corretora['seguradora'];
		    	$viagens[0]['TViagViagem']['viag_corr_pjur_pess_oras_codigo'] = $seguradora_corretora['corretora'];
	    		$viagem = $viagens;
		    	foreach ($viagem as $dado) {
	    			$this->TViagViagem->atualizar($dado);
		    	}
		    }
    	}	    
    
    }
    */

}