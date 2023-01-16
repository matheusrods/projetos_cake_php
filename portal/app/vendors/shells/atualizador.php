<?php
class AtualizadorShell extends Shell {

	var $uses = array('TVterViagemTerminal', 'ClienteProduto', 'Cliente');

	function main() {
		echo "==================================================\n";
		echo "* Atualizador \n";
		echo "* \n";
		echo "* Atualiza dados \n";
		echo "==================================================\n\n";

		echo "=> terminal_viagem: verifica se o termianl da viagem é o mesmo terminal do veículo, e em caso de diferença, atualiza o terminal da viagem, apenas se o terminal da viagem não for uma ISCA\n\n";
	}

	function terminal_viagem(){
		$terminais = $this->TVterViagemTerminal->listarTerminaisDesatualizado();
		
		if($terminais){
			foreach ($terminais as $terminal) {
				$terminal['TVterViagemTerminal']['vter_term_codigo'] = $terminal['TOrteObjetoRastreadoTermina']['orte_term_codigo'];
				$this->TVterViagemTerminal->save($terminal);
			}
		}
		
	}

	function log_cliente_produto() {
		$registros = $this->ClienteProduto->query("SELECT codigo, data_inclusao_log
			FROM (	SELECT cliente_produto.codigo,
						cliente_produto.codigo_cliente,
						cliente_produto.codigo_produto,
						cliente_produto.codigo_motivo_bloqueio,
						(	SELECT top 1 codigo_motivo_bloqueio 
							FROM dbBuonny.vendas.cliente_produto_log 
							WHERE codigo_cliente_produto = cliente_produto.codigo 
							ORDER BY cliente_produto_log.codigo DESC) AS codigo_motivo_bloqueio_log,
						(	SELECT top 1 CONVERT(VARCHAR, data_inclusao, 120) AS data_inclusao 
							FROM dbBuonny.vendas.cliente_produto_log 
							WHERE codigo_cliente_produto = cliente_produto.codigo 
							ORDER BY cliente_produto_log.codigo DESC) AS data_inclusao_log
					FROM dbBuonny.vendas.cliente_produto ) x 
			WHERE codigo_motivo_bloqueio <> codigo_motivo_bloqueio_log-- and codigo = 2601
			ORDER BY codigo");
		$_SESSION['Auth']['Usuario']['codigo'] = 2;
		$this->ClienteProduto->query("SET DATEFORMAT YMD");
		foreach ($registros as $key => $registro) {
			$cliente_produto = $this->ClienteProduto->carregar($registro[0]['codigo']);
			$cliente_produto['ClienteProduto']['data_alteracao_manual'] = $registro['0']['data_inclusao_log'];
			echo $cliente_produto['ClienteProduto']['codigo'].' '.$cliente_produto['ClienteProduto']['codigo_cliente'].' '.$cliente_produto['ClienteProduto']['codigo_produto']."\n";
			if (!$this->ClienteProduto->atualizar($cliente_produto)) {
				$invalid_fields = $this->ClienteProduto->invalidFields();
				if (in_array('Esse cliente está inativo, antes ele deve ser reativado', $invalid_fields)) {
					if (!$this->ClienteProduto->inativarProdutos($cliente_produto['ClienteProduto']['codigo_cliente'])) {
						$invalid_fields = $this->ClienteProduto->invalidFields();
						print_r($invalid_fields);
					}
				} elseif (in_array('O status selecionado para o produto não confere com a pendência selecionada', $invalid_fields)) {
					if ($cliente_produto['ClienteProduto']['codigo_motivo_bloqueio'] == 1) {
						$cliente_produto['ClienteProduto']['pendencia_financeira'] = 0;
						$cliente_produto['ClienteProduto']['pendencia_comercial'] = 0;
						$cliente_produto['ClienteProduto']['pendencia_juridica'] = 0;
					} else {
						$cliente_produto['ClienteProduto']['pendencia_comercial'] = 1;
					}
					$cliente_produto['ClienteProduto']['data_faturamento'] = AppModel::dateToDbDate($cliente_produto['ClienteProduto']['data_faturamento']);
					$cliente_produto['ClienteProduto']['data_inclusao'] = AppModel::dateToDbDate($cliente_produto['ClienteProduto']['data_inclusao']);
					$cliente_produto['ClienteProduto']['data_alteracao'] = AppModel::dateToDbDate($cliente_produto['ClienteProduto']['data_alteracao']);
					if (!$this->ClienteProduto->atualizar($cliente_produto)) {
						$invalid_fields = $this->ClienteProduto->invalidFields();
						print_r($invalid_fields);
					}
				} else {
					print_r($invalid_fields);
				}
				echo "\n";
			}
		}
		$hoje = date('Ymd');
		$this->ClienteProduto->query("UPDATE dbBuonny.vendas.cliente_produto_log SET data_inclusao = data_alteracao WHERE data_inclusao>='{$hoje}' AND codigo_usuario_inclusao = {$_SESSION['Auth']['Usuario']['codigo']}");
	}

	function log_cliente() {
		$_SESSION['Auth']['Usuario']['codigo'] = 2;
		$registros = $this->Cliente->query("select * from (
						select codigo, ativo, data_ativacao, data_inativacao
						, (select top 1 ativo from dbBuonny.vendas.cliente_log where cliente_log.codigo_cliente = cliente.codigo order by cliente_log.codigo desc) as status_log
						, (select top 1 CONVERT(VARCHAR, data_inclusao, 120) from dbBuonny.vendas.cliente_log where cliente_log.codigo_cliente = cliente.codigo order by cliente_log.codigo desc) as data_inclusao_log
						from dbBuonny.vendas.cliente where codigo_documento != '00000000000000'
						) as x where ativo <> status_log ORDER BY codigo");
		foreach ($registros as $key => $registro) {
			$cliente = $this->Cliente->carregar($registro['0']['codigo']);
			$cliente['Cliente']['data_inclusao'] = AppModel::dateToDbDate($cliente['Cliente']['data_inclusao']);
			$cliente['Cliente']['data_inativacao'] = AppModel::dateToDbDate($cliente['Cliente']['data_inativacao']);
			$cliente['Cliente']['data_ativacao'] = AppModel::dateToDbDate($cliente['Cliente']['data_ativacao']);
			$cliente['Cliente']['data_alteracao'] = AppModel::dateToDbDate($cliente['Cliente']['data_alteracao']);
			$cliente['Cliente']['data_alteracao_manual'] = $registro['0']['data_inclusao_log'];
			echo $cliente['Cliente']['codigo']."\n";
			if (!$this->Cliente->atualizar($cliente, true)) {
				$invalid_fields = $this->Cliente->invalidFields();
				if (in_array('Informe a Inscrição Municipal', $invalid_fields)) {
					$cliente['Cliente']['ccm'] = '0';
				} 
				if (in_array('Informe o Gestor', $invalid_fields)) {
					$cliente['Cliente']['codigo_gestor'] = 25754;
				}
				if (!$this->Cliente->atualizar($cliente, true)) {
					$invalid_fields = $this->Cliente->invalidFields();
					print_r($invalid_fields);
				}
			}
		}
		$hoje = date('Ymd');
		$this->Cliente->query("UPDATE dbBuonny.vendas.cliente_log SET data_inclusao = data_alteracao WHERE data_inclusao>='{$hoje}' AND codigo_usuario_inclusao = {$_SESSION['Auth']['Usuario']['codigo']}");
	}

}
?>
