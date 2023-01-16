<?php
class ValorPadraoPjurShell extends Shell {
	var $uses = array('TVppjValorPadraoPjur', 'TPjurPessoaJuridica', 'Cliente');
	
	function main() {
		echo "Carregar valores padrão de Cliente e Pjur para Vppj\n";
	}

	function carregar(){
		echo "Carregando Clientes\n";
		$clientes = $this->Cliente->find('all',array(
			'conditions' => array(
				'OR' => array('temperatura_de IS NOT NULL','temperatura_ate IS NOT NULL','monitorar_retorno' => true),
				'ativo' => true,
			),
		));
		$qtd_total = count($clientes);
		$atual = 0;
		foreach($clientes as $cliente){
			$atual++;
			echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))." %]";
			$cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);

			if($cliente_pjur){
				$vppj = $this->TVppjValorPadraoPjur->carregarPorPjur($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
				$data = array(
					'TVppjValorPadraoPjur' => array(
						'vppj_pjur_oras_codigo' => $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
						'vppj_temperatura_de' => $cliente['Cliente']['temperatura_de'],
						'vppj_temperatura_ate' => $cliente['Cliente']['temperatura_ate'],
						'vppj_monitorar_retorno' => ($cliente['Cliente']['monitorar_retorno']?'1':'0'),
						'vppj_monitorar_isca' => ($cliente_pjur['TPjurPessoaJuridica']['pjur_monitora_isca']?'1':'0'),
					),
				);

				if($vppj){
					echo "Atualizando {$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']} ";
					$data['TVppjValorPadraoPjur']['vppj_codigo'] = $vppj['TVppjValorPadraoPjur']['vppj_codigo'];
					if($this->TVppjValorPadraoPjur->atualizar($data))
						echo "- OK\n";
					else{
						echo "- ERRO\n";die;
					}
				}else{
					echo "Criando {$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']} ";
					if($this->TVppjValorPadraoPjur->incluir($data))
						echo "- OK\n";
					else{
						echo "- ERRO\n";die;
					}
				}
			}
		}
	}

	function carregarPjur(){
		echo "Carregando Pjur\n";
		$pjurs = $this->TPjurPessoaJuridica->find('all',array(
			'conditions' => array(
				'OR' => array('pjur_monitora_isca' => true,'pjur_checklist_validade <> 0'),
			),
		));
		$qtd_total = count($pjurs);
		$atual = 0;
		foreach($pjurs as $cliente_pjur){
			$atual++;
			echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))." %]";
			$cliente = $this->Cliente->carregarPorDocumento($cliente_pjur['TPjurPessoaJuridica']['pjur_cnpj']);

			if($cliente){
				$vppj = $this->TVppjValorPadraoPjur->carregarPorPjur($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
				$data = array(
					'TVppjValorPadraoPjur' => array(
						'vppj_pjur_oras_codigo' => $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
						'vppj_temperatura_de' => $cliente['Cliente']['temperatura_de'],
						'vppj_temperatura_ate' => $cliente['Cliente']['temperatura_ate'],
						'vppj_monitorar_retorno' => ($cliente['Cliente']['monitorar_retorno']?'1':'0'),
						'vppj_monitorar_isca' => ($cliente_pjur['TPjurPessoaJuridica']['pjur_monitora_isca']?'1':'0'),
					),
				);

				if($vppj){
					echo "Atualizando {$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']} ";
					$data['TVppjValorPadraoPjur']['vppj_codigo'] = $vppj['TVppjValorPadraoPjur']['vppj_codigo'];
					if($this->TVppjValorPadraoPjur->atualizar($data))
						echo "- OK";
					else{
						echo "- ERRO";die;
					}
				}else{
					echo "Criando {$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']} ";
					if($this->TVppjValorPadraoPjur->incluir($data))
						echo "- OK";
					else{
						echo "- ERRO";die;
					}
				}
			}
			echo "\n";
		}
	}

}
?>
