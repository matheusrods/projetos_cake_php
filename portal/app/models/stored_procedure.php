<?php
class StoredProcedure extends AppModel {
	var $name = 'StoredProcedure';
  	var $tableSchema = false;
	var $databaseTable = false;
	var $useTable = false;

	public function consulta_motorista($codigos_clientes, &$data) {
		$produtos = array(Produto::TELECONSULT_STANDARD,Produto::TELECONSULT_PLUS);
		foreach($produtos as $ordem_produto => $codigo_produto){
			foreach ($codigos_clientes as $key => $codigo_cliente) {
				$data['codigo_produto'] = $codigo_produto;				
				$retorno = $this->sp_consulta_motorista($codigo_cliente, $data);		
				if($retorno)
					return TRUE;
				
			}
		}		
	}
	
	public function sp_consulta_motorista($codigo_cliente, &$data){
		$placa 			= isset($data['placa_caminhao']) 		? str_replace('-', '', $data['placa_caminhao']) 			: NULL;
		$placa_carreta 	= isset($data['placa_carreta']) ? str_replace('-', '', $data['placa_carreta']) 	: NULL;		
		$cpf_motorista = isset($data['codigo_documento']) ? $data['codigo_documento'] : $data['motorista_cpf'];
		$sql = "exec dbteleconsult.informacoes.usp_consulta_status_motorista
			@codigo_cliente = ".$codigo_cliente.",
			@codigo_documento = '".$cpf_motorista."',
			@placa = '".$placa."',
			@codigo_produto = '".$data['codigo_produto']."',
			@departamento = 'M',
			@codigo_corporacao = 1,
			@gera_cobranca = 1,
			@codigo_usuario_inclusao = 1,
			@senha = '',
			@placa_carreta = '".$placa_carreta."',
			@codigo_carga_tipo = NULL,
			@codigo_endereco_cidade_origem = NULL,
			@codigo_endereco_cidade_destino = NULL,
			@codigo_carga_valor = NULL,
			@consulta_web = 1
		;";
		//debug($sql);
		$retorno = $this->query($sql);
		if($retorno){
			if($retorno[0][0]['codigo_log_faturamento'])
				$data['codigo_log_faturamento'] = $retorno[0][0]['codigo_log_faturamento'];
			if(strpos($retorno[0][0]['mensagem'],'PERFIL ADEQUADO AO RISCO')!==false) {
				$data['informacao'] = $retorno[0][0]['mensagem'];
				return TRUE;
			}
		}		
		return FALSE;
	}
	

}
?>