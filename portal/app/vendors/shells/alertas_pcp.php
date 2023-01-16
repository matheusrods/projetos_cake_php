<?php
class AlertasPcpShell extends Shell {
	var $uses = array('TIpcpInformacaoPcp', 'TRefeReferencia','Alerta','AlertaTipo');

	function main() {
		echo "==================================================\n\n";
		echo "=> verificaViagemAtrasada => Verifica Viagens Atrasadas e em provavel atraso \n\n";
	}

	function run() {
		if (!$this->im_running('alertas_pcp'))
        	$this->insereAlerta();
    }
    
	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep '{$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

	function verificaViagemAtrasada(){
		$this->TIpcpInformacaoPcp->bindModel(array(
			'hasOne' => array(
				'TRefeReferencia' => array(
					'foreignKey' => false,
					'conditions' => array("TRefeReferencia.refe_codigo = TIpcpInformacaoPcp.ipcp_refe_codigo"),
					'type' => 'LEFT'
				),
				'TStemStatusTempo' => array(
					'foreignKey' => false,
					'conditions' => array("TStemStatusTempo.stem_codigo = TIpcpInformacaoPcp.ipcp_stem_codigo"),
					'type' => 'LEFT'
				),
				'TMatrMotivoAtraso' => array(
					'foreignKey' => false,
					'conditions' => array("TMatrMotivoAtraso.matr_codigo = TIpcpInformacaoPcp.ipcp_matr_codigo"),
					'type' => 'LEFT'
				),
				'TViagViagem' => array(
					'foreignKey' => false,
					'conditions' => array("TViagViagem.viag_pedido_cliente = CONCAT(LPAD(ipcp_cd,4,'0'),TIpcpInformacaoPcp.ipcp_rota)"),
					'type' => 'LEFT'
				),
				'TVveiViagemVeiculo' => array(
					'foreignKey' => false,
					'conditions' => array("TVveiViagemVeiculo.vvei_viag_codigo = TIpcpInformacaoPcp.ipcp_viag_codigo"),
					'type' => 'LEFT'
				),
				'TVeicVeiculo' => array(
					'foreignKey' => false,
					'conditions' => array("TVeicVeiculo.veic_oras_codigo = TVveiViagemVeiculo.vvei_veic_oras_codigo"),
					'type' => 'LEFT'
				),
			),
		));

		$conditions['ipcp_stem_codigo'] = array(2,3);
		$conditions['ipcp_data_notificacao'] = NULL;
		$lista = $this->TIpcpInformacaoPcp->find('all',array(
				'conditions' => $conditions,
				'fields' => array(
					'ipcp_codigo',
					'ipcp_janela_inicial',
					'ipcp_janela_final',
					'ipcp_loja',
					'ipcp_viag_codigo',
					'ipcp_refe_codigo',
					'ipcp_stem_codigo',
					'ipcp_matr_codigo',
					'ipcp_pjur_pess_oras_codigo',
					'TRefeReferencia.refe_descricao',
					'INITCAP(TStemStatusTempo.stem_descricao) AS stem_descricao',
					'INITCAP(TMatrMotivoAtraso.matr_descricao) AS matr_descricao',
					'TViagViagem.viag_codigo_sm',
					'TVeicVeiculo.veic_placa',
					'ipcp_rota',
				),
				'limit' => 1,
			)
		);

		return $lista;		
	}

	function insereAlerta(){
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		App::Import('Component',array('DbbuonnyGuardian'));
		$dados = $this->verificaViagemAtrasada();
		foreach ($dados as $key => $dado) {
			$this->StringView 	= new StringViewComponent();
			$this->Scheduler  	= new SchedulerComponent();	
			$codigo_cliente = DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny( $dado['TIpcpInformacaoPcp']['ipcp_pjur_pess_oras_codigo'] );
			if(!empty($dado['TRefeReferencia']['refe_descricao'])){
				$descricao =  substr($dado['TRefeReferencia']['refe_descricao'],0,30);
			}else{
				$descricao = substr($dado['TIpcpInformacaoPcp']['ipcp_loja'],0,30);
			}
			if(!empty($dado['TVeicVeiculo']['veic_placa'])){
				$placa = '-Placa: '.$dado['TVeicVeiculo']['veic_placa'].'-';
			}else{
				$placa = '-';
			}
			if(!empty($dado['TViagViagem']['viag_codigo_sm'])){
				$sm = 'Sm: '.$dado['TViagViagem']['viag_codigo_sm'].'-';
			}else{
				$sm = '';
			}
			if($dado[0]['stem_descricao'] == 'Possível Atraso'){
            	$dado[0]['stem_descricao'] = 'Possivel Atraso';
        	}
			$alerta_tipo = $this->AlertaTipo->retornaAlertaTipo($dado['TIpcpInformacaoPcp']['ipcp_stem_codigo'],$dado['TIpcpInformacaoPcp']['ipcp_matr_codigo']);
			$this->StringView->set(compact('dado'));
			$content = $this->StringView->renderMail('email_alerta_pcp');
			$alerta = array(
				'Alerta' => array(
					'codigo_cliente' => $codigo_cliente,
					'descricao' => "PCP - {$dado[0]['stem_descricao']} - {$dado[0]['matr_descricao']} -Rota: {$dado['TIpcpInformacaoPcp']['ipcp_rota']}{$placa}{$sm}- Alvo: {$descricao} - Janela Inicial : {$dado['TIpcpInformacaoPcp']['ipcp_janela_inicial']} - Janela Final : {$dado['TIpcpInformacaoPcp']['ipcp_janela_final']}",
					'descricao_email' => $content,
					'codigo_alerta_tipo' => $alerta_tipo,
					'model' => 'TIpcpInformacaoPcp',
					'foreign_key' => $dado['TIpcpInformacaoPcp']['ipcp_codigo'],
				),
			);

			$this->Alerta->query('begin transaction');
			$this->TIpcpInformacaoPcp->query('begin transaction');
			if($this->Alerta->incluir($alerta)){
				$data_pcp = array(
					'TIpcpInformacaoPcp' => array(
						'ipcp_codigo' => $dado['TIpcpInformacaoPcp']['ipcp_codigo'],
						'ipcp_data_notificacao' => 'NOW()',
					)
				);
				if($this->TIpcpInformacaoPcp->atualizar($data_pcp)){
					$this->Alerta->commit();
					$this->TIpcpInformacaoPcp->commit();
				}else{
					$this->Alerta->rollback();
					$this->TIpcpInformacaoPcp->rollback();
					return FALSE;
				}
			}
		}
	}
}