<?php 

class ReenviarKitsShell extends Shell {

	var $uses = array(
		'Importar',
		'Exame',
		'Servico', 
		'Esocial',
		'GrupoEconomicoCliente',
		'Atestados',
		'PedidoExame',
		'Cliente',
		'ImportacaoEstrutura',
		'GrupoEconomico',
		'RegistroImportacao'
	);
	var $arquivo;

	public function initialize() {}

	public function main()
	{
		
		echo "cake\console\cake -app ./app reenviar_kits {data_inicio 'YYYY-mm-dd'} {data_fim 'YYYY-mm-dd'} {codigo_cliente (opcional)} \n";
		if(empty($this->args)) {
			exit;
		}

		$params = $this->args;

		$data_inicio = $params[0];
		$data_fim = (isset($params[1])) ? $params[1] : null;
		$codigo_cliente = (isset($params[2])) ? $params[2] : null;

		$this->reenviar_kits($data_inicio,$data_fim,$codigo_cliente);
	}


	public function reenviar_kits ($data_inicio, $data_fim = null, $codigo_cliente = null) 
	{
		
		$this->autoRender = false;

		echo "INICIANDO O PROCESSAMENTO DO REENVIO DOS KITS\n";

		echo "data_inicio:" . $data_inicio . " - codigo_cliente:" . $codigo_cliente."\n";

		//todos os pedidos a partir da data de inicio
		// $query = "";



		
		exit;
	}

}