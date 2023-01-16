<?php
class AlterarMotivoBloqueioClienteProdutoShell extends Shell {

		function main() {
			echo "==================================================\n";
			echo "* Incluir \n";
			echo "* \n";
			echo "* \n";
			echo "==================================================\n\n";

			echo "=> incluir_sm_basica: Realiza a inserção de SM no modo básico conforme a necessidade do cliente \n\n";
		}

		function alterar_motivo_bloqueio(){

			$this->Cliente = ClassRegistry::init('Cliente');
			$this->ClienteProduto = ClassRegistry::init('ClienteProduto');

			// $codigos = $this->ClienteProduto->find('all', array('conditions' => array('codigo_motivo_bloqueio_bkp2' => 3),'fields' => array('codigo')));
			// $qtd_total = count($codigos);
			// $atual = 1;
			// foreach ($codigos as $codigo) {
			// 	$query = 'UPDATE dbBuonny.vendas.cliente_produto SET codigo_motivo_cancelamento = 2 WHERE codigo = '.$codigo['ClienteProduto']['codigo'].'';
				
			// 	if(!$this->ClienteProduto->query($query)){
			// 		file_put_contents('nao_cadastrados', $codigo['Cliente']['codigo']."\n", FILE_APPEND);
			// 		echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))."%]: Nao foi possivel atualizar o cliente: ".$codigo['ClienteProduto']['codigo']."\n";
			// 	}else{
			// 		echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))."%]: Cliente: ".$codigo['ClienteProduto']['codigo']." atualizado\n";
			// 	}
			// 	$atual++;
			// }
			// $codigos = $this->ClienteProduto->find('all', array('conditions' => array('codigo_motivo_bloqueio_bkp2' => 4),'fields' => array('codigo')));
			// $qtd_total = count($codigos);
			// $atual = 1;
			// foreach ($codigos as $codigo) {
			// 	$query = 'UPDATE dbBuonny.vendas.cliente_produto SET  codigo_motivo_cancelamento = 3 WHERE codigo = '.$codigo['ClienteProduto']['codigo'].'';
				
			// 	if(!$this->ClienteProduto->query($query)){
			// 		file_put_contents('nao_cadastrados', $codigo['ClienteProduto']['codigo']."\n", FILE_APPEND);
			// 		echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))."%]: Nao foi possivel atualizar o cliente: ".$codigo['ClienteProduto']['codigo']."\n";
			// 	}else{
			// 		echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))."%]: Cliente: ".$codigo['ClienteProduto']['codigo']." atualizado\n";
			// 	}
			// 	$atual++;
			// }
			
			$codigos = $this->ClienteProduto->find('all', array('conditions' => array('codigo_motivo_bloqueio_bkp2' => 5),'fields' => array('codigo')));
			$qtd_total = count($codigos);
			$atual = 1;
			foreach ($codigos as $codigo) {
				$query = 'UPDATE dbBuonny.vendas.cliente_produto SET codigo_motivo_cancelamento = 4 WHERE codigo = '.$codigo['ClienteProduto']['codigo'].'';
				
				if(!$this->ClienteProduto->query($query)){
					file_put_contents('nao_cadastrados', $codigo['ClienteProduto']['codigo']."\n", FILE_APPEND);
					echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))."%]: Nao foi possivel atualizar o cliente: ".$codigo['ClienteProduto']['codigo']."\n";
				}else{
					echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))."%]: Cliente: ".$codigo['ClienteProduto']['codigo']." atualizado\n";
				}
				$atual++;
			}

		}	
}