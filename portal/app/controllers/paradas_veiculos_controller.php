<?php
class ParadasVeiculosController extends appController {
	var $name = 'ParadasVeiculos';
	var $uses = array('TPveiParadaVeiculo', 'Cliente', 'TRefeReferencia');
	var $components = array('DbbuonnyGuardian');

	function mapa() {
		$this->pageTitle = 'Mapa de Paradas';
		$filtrado = false;
		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$this->data['TPveiParadaVeiculo']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
		if ($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			$filtrado = $this->validateMapa();
			if ($filtrado) {
				$this->loadModel('TPjurPessoaJuridica');
				$this->converteBuonnyGuardian();
				$pess_oras_codigo = $this->TPjurPessoaJuridica->buscaClienteCentralizador($this->data['TPveiParadaVeiculo']['codigo_cliente']);
    			$pess_oras_codigo = $pess_oras_codigo['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
				$referencias = $this->TRefeReferencia->find('all', array('conditions' => array('refe_codigo' => array_keys($this->data['TPveiParadaVeiculo']['cd_id']))));
				$conditions = $this->TPveiParadaVeiculo->converteFiltrosEmConditions($this->data['TPveiParadaVeiculo'], $pess_oras_codigo);
				$dados = $this->TPveiParadaVeiculo->listar($conditions, $pess_oras_codigo);
				$cliente = $this->Cliente->carregar($this->data['TPveiParadaVeiculo']['codigo_cliente']);
				$this->set(compact('dados', 'cliente', 'referencias'));
				if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export') {
					$this->exportarMapa($dados);
				}
			}
		} else {
			$this->data['TPveiParadaVeiculo']['data_inicial'] = date('d/m/Y', strtotime('-1 day'));
			$this->data['TPveiParadaVeiculo']['data_final'] = date('d/m/Y', strtotime('-1 day'));
			$this->data['TPveiParadaVeiculo']['status_alvo'] = TPveiParadaVeiculo::STATUS_ALVO_FORA;
			$this->data['TPveiParadaVeiculo']['minutos_parado'] = 10;			
		}
		$cds = array();
		
		$this->set(compact('cds', 'filtrado'));
	}

	private function exportarMapa($dados) {
		header('Content-type: application/vnd.ms-excel');
		header(sprintf('Content-Disposition: attachment; filename="%s"', basename('mapa_de_paradas.csv')));
	    header('Pragma: no-cache');
   		echo iconv('UTF-8', 'ISO-8859-1', 'Placa;Posição;Data Inicial;Data Final;Alvo;Tempo;SM;CD Origem;Transportadora;Motorista;')."\n";
   		foreach ($dados as $dado) {
   			echo iconv('UTF-8', 'ISO-8859-1', $dado['0']['veic_placa'].";".
   			$dado['0']['pvei_descricao'].";".
   			$dado['0']['pvei_data_inicial'].";".
   			$dado['0']['pvei_data_final'].";".
   			$dado['0']['refe_descricao_alvo'].";".
   			Comum::convertToHoursMins($dado['0']['minutos_parado']).";".
   			$dado['0']['viag_codigo_sm'].";".
   			$dado['0']['refe_descricao_origem'].";".
   			$dado['0']['tran_pjur_razao_social'].";".
   			$dado['0']['moto_pess_nome']."\n");
   		}
		exit;
	}

	private function validateMapa() {
		if (empty($this->data['TPveiParadaVeiculo']['codigo_cliente'])) {
			$this->TPveiParadaVeiculo->invalidate('codigo_cliente', 'Informe o cliente');
		}
		if (empty($this->data['TPveiParadaVeiculo']['cd_id'])) {
			$this->TPveiParadaVeiculo->invalidate('codigo_cliente', 'Informe pelo menos 1 alvo');
		}
		if (empty($this->data['TPveiParadaVeiculo']['data_inicial'])) {
			$this->TPveiParadaVeiculo->invalidate('data_inicial', 'Informe a data');
		}
		if (empty($this->data['TPveiParadaVeiculo']['data_final'])) {
			$this->TPveiParadaVeiculo->invalidate('data_final', 'Informe a data');
		}
		return count($this->TPveiParadaVeiculo->invalidFields()) == 0;
	}

	private function converteBuonnyGuardian() {
		$codigos_guardian = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($this->data['TPveiParadaVeiculo']['codigo_cliente'], $this->data['TPveiParadaVeiculo']['base_cnpj']);
		if ($codigos_guardian) {
			$this->data['TPveiParadaVeiculo']['pess_oras_codigo'] = $codigos_guardian;
		} else {
			$this->data['TPveiParadaVeiculo']['pess_oras_codigo'] = -1;
		}
	}
}
