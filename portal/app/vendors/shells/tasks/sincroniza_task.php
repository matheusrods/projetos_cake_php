<?php
class SincronizaTaskTask extends Shell {
	var $uses =  array(
        	'Cliente'
		);
	var $validationErrors;

    public function matrizFilial($data) {
    	$this->MatrizFilial 			=& ClassRegistry::init('MatrizFilial');

        if($data['codigo_documento'] > $data['documento_pagador']){
			$conditions = array(
				'codigo_cliente_matriz' => $data['codigo_pagador'],
				'codigo_cliente_filial' => $data['codigo'],
			);
		} else {
			$conditions = array(
				'codigo_cliente_matriz' => $data['codigo'],
				'codigo_cliente_filial' => $data['codigo_pagador'],
			);
		}

		if(!$this->MatrizFilial->find('count',compact('conditions'))){
			$matriz_filial = array('MatrizFilial' => $conditions);
			$matriz_filial['MatrizFilial']['codigo_usuario_inclusao'] = 1;
			
			if(!$this->MatrizFilial->incluir($matriz_filial)){
				$this->validationErrors[0] = "ERRO NA INCLUSAO DA MATRIZ FILIAL";
				return FALSE;
			}
		} else {
			echo " \n - O RELACIONAMENTO MATRIZ/FILIAL JA EXISTE";

		}
		return TRUE;
        
    }

    public function clienteProdutoPagador($data){
    	$this->ClienteProduto 			=& ClassRegistry::init('ClienteProduto');
		$this->EmbarcadorTransportador	=& ClassRegistry::init('EmbarcadorTransportador');
		$this->ClienteProdutoPagador 	=& ClassRegistry::init('ClienteProdutoPagador');

    	if($data['subtipo'] == $data['subtipo_p'] || ($data['subtipo'] != 'TRANSPORTADORA' && $data['subtipo_p'] != 'TRANSPORTADORA')){
			$this->validationErrors[0] = "CLIENTE \"{$data['subtipo']}\" | PAGADOR \"{$data['subtipo_p']}\"";
			return FALSE;
		}

		$conditions = array(
			'OR' => array(
				array(
					'codigo_cliente_embarcador' 	=> $data['codigo_pagador'],
					'codigo_cliente_transportador'	=> $data['codigo'],
				),
				array(
					'codigo_cliente_embarcador' 	=> $data['codigo'],
					'codigo_cliente_transportador'	=> $data['codigo_pagador'],
				),
			),
		);

		$embaTran = $this->EmbarcadorTransportador->find('first',compact('conditions'));
		if(!$embaTran){
			if($data['subtipo'] == 'TRANSPORDADORA'){
				$embaTran = array(
					'EmbarcadorTransportador' => array(
						'codigo_cliente_embarcador' 	=> $data['codigo_pagador'],
						'codigo_cliente_transportador'	=> $data['codigo'],
					),
				);
			} else {
				$embaTran = array(
					'EmbarcadorTransportador' => array(
						'codigo_cliente_embarcador' 	=> $data['codigo'],
						'codigo_cliente_transportador'	=> $data['codigo_pagador'],
					),
				);
			}
			$embaTran['EmbarcadorTransportador']['codigo_usuario_inclusao'] = 1;
			$embaTran['EmbarcadorTransportador']['codigo_cliente_pagador'] = $data['codigo_pagador'];

			if(!$this->EmbarcadorTransportador->incluir($embaTran)){
				$this->validationErrors[0] = "ERRO NA INCLUSAO DO EMBARCADOR TRANSPORTADOR";
				return FALSE;
			}

			$embaTran['EmbarcadorTransportador']['codigo'] = $this->EmbarcadorTransportador->id;
		}

		$conditions = array(
			'ClienteProdutoPagador.codigo_embarcador_transportador' => $embaTran['EmbarcadorTransportador']['codigo'],
			'ClienteProdutoPagador.codigo_produto' 					=> $data['codigo_produto']
		);

		if(!$this->ClienteProdutoPagador->find('count',compact('conditions'))){

			$clienteProdutoPagador = array(
				'codigo_embarcador_transportador' => $embaTran['EmbarcadorTransportador']['codigo'],
				'codigo_produto'				  => $data['codigo_produto'],
				'codigo_cliente_pagador'		  => $data['codigo_pagador'],
				'codigo_usuario_inclusao' 		  => 1,
			);

			if(!$this->ClienteProdutoPagador->incluir($clienteProdutoPagador)){
				$this->validationErrors[0] = "ERRO NA INCLUSAO DO PAGADOR ";
				return FALSE;
			}
		} else {
			echo " \n - JA EXISTE UM PAGADOR CADASTRADO PARA O PRODUTO";

		}

		return TRUE;
    }
}
?>
 