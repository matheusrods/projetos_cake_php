<?php
class IntegracaoContratosShell extends Shell {
	var $uses = array('ClienteProduto', 'ClienteProdutoContrato', 'Cliente');

	public function main() {
		set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 0);
        ini_set('mssql.timeout', '600');
		
		$contratos_existentes = $this->ClienteProdutoContrato->find('all', array('fields' => array('codigo_cliente_produto')));
		$contratos_existentes = Set::extract('/ClienteProdutoContrato/codigo_cliente_produto', $contratos_existentes);
		$clientes_produtos = $this->ClienteProduto->find('all', array(
			'fields' => array('codigo'),
			'conditions' => array(
					'NOT' => array('ClienteProduto.codigo' => $contratos_existentes)
			),
			'recursive' => -1
		));
		
		$clientes_produtos = Set::extract('/ClienteProduto/codigo', $clientes_produtos);
		
		foreach($clientes_produtos as $cliente_produto) {
			echo 'registrando contrato do cliente produto '.$cliente_produto.'  ';
			if($this->registrarContrato($cliente_produto))
				echo "registrado com sucesso\n";
			else
				echo "falha ao incluir contrato\n";
		}
	}
	
	function registrarContrato($codigo_cliente_produto) {
		$joins = array(
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => array('Cliente.codigo = ClienteProduto.codigo_cliente')
			)
		);
		$conditions = array(
			'ClienteProduto.codigo' => $codigo_cliente_produto
		);
		$fields = array(
			'Cliente.data_inclusao'
		);
		$recursive = -1;
		$cliente_produto = $this->ClienteProduto->find('first', compact('fields', 'conditions', 'joins', 'recursive'));
		$data_inclusao = strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3$2$1$4", $cliente_produto['Cliente']['data_inclusao']));
		$data_condicional = strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3$2$1$4", '01/03/2012 00:00:00'));
		if ($data_inclusao > $data_condicional) {
			$data_contrato = $cliente_produto['Cliente']['data_inclusao'];
			$data_envio = $data_contrato;
			$data_vigencia = date('d/m/Y', strtotime('+1 year', strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3$2$1$4", $data_contrato))));
		} else {
			$data_contrato = $cliente_produto['Cliente']['data_inclusao'];
			$data_envio = $data_contrato;
			$data_vigencia = '01/03/2013 00:00:00';
		}
		
		$cliente_produto_contrato = array(
			'ClienteProdutoContrato' => array(
				'codigo_cliente_produto' => $codigo_cliente_produto,
				'numero' => $this->gerarNumeroContrato($codigo_cliente_produto),
				'data_contrato' => $data_contrato,
				'data_envio' => $data_envio,
				'data_vigencia' => $data_vigencia,
				'rf' => 0,
				'cs' => 0,
				'observacao' => '',
				'codigo_usuario_inclusao' => '29184',
				'data_inclusao' => date('d/m/Y H:i:s')
			)
		);
		return $this->ClienteProdutoContrato->incluir($cliente_produto_contrato);
	}
	
	function gerarNumeroContrato($codigo_cliente_produto) {
		$data = date('Ymd');
		$numero_final = str_pad(mt_rand(0, 999999), 8,'0', STR_PAD_LEFT);
		$numero_contrato = str_pad($data.$codigo_cliente_produto.$numero_final, 20, '0', STR_PAD_RIGHT);
		return $numero_contrato;
	}
	
}
