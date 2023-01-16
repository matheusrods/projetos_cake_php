<?php
App::import('Component', 'Soap');

class WsOutboxCase extends CakeTestCase {
	
	var $fixtures = array('app.ws_configuracao', 'app.ws_outbox');

	public function startTest($method){
	    $this->WsConfiguracao = ClassRegistry::init('WsConfiguracao');
	    $this->WsOutbox = ClassRegistry::init('WsOutbox');
	}

	function testCriacaoEnvelope() {
	    $outboxData = $this->WsOutbox->find('first');
	    
	    $expected = new Envelope();
	    $expected->EVENTO->NU_SMP = '508596';
	    $expected->EVENTO->NU_ANO_SMP = '2012';
	    $expected->EVENTO->DS_DOC_CONTROLE = '19650494886';
	    $expected->EVENTO->DT_EVENTO = '2012-03-15T05:05:18';
	    $expected->EVENTO->CD_EVENTO = '2';
	    $expected->EVENTO->DS_EVENTO = 'Chegada no Ponto de Entrega';
	    $expected->EVENTO->NU_LAT = '-23.58416748';
	    $expected->EVENTO->NU_LONG = '-46.67916489';
	    $expected->EVENTO->DS_PLACA_CAVALO = 'EMT7848';
	    $expected->EVENTO->DS_PLACA_CARRETA = '';
	    $expected->EVENTO->NM_PONTO = 'CLODOMIRO AMAZONAS';
	    $expected->EVENTO->NM_APELIDO_PONTO = '2005';
	    $expected->EVENTO->DS_DOCUMENTO_NF = '19650513407';
	    $expected->EVENTO->TEMPERATURA = '0';
	    
	    $actual = $this->WsOutbox->asEnvelope($outboxData);
	    
	    $this->assertEqual($expected, $actual);
	}
	
	function testProximaRequisicaoNaoEnviada() {
			$eventos = $this->WsOutbox->proximosNaoEnviados(1);
			$proximo = $this->WsOutbox->asEnvelope($eventos[0]);
			$this->assertEqual('508597', $proximo->EVENTO->NU_SMP);
	}

	function testMarcarRequisicaoEnviada() {
    	$totalAntesProcessar = count($this->WsOutbox->proximosNaoEnviados(null));
		$this->WsOutbox->marcarEnviado(2);
		$this->assertEqual($totalAntesProcessar - 1, count($this->WsOutbox->proximosNaoEnviados(null)));
	}

	function testaConverteMensagemEmEnvelope(){
		$data_RMA 	 = $this->WsOutbox->find('first', array('conditions'=>array('tipo_mensagem'=>'rma') ));
		$objEnvelope = $this->WsOutbox->converteMensagemEmEnvelope( $data_RMA );
		$this->assertEqual( 'Atraso no reinicio da viagem', $objEnvelope->EVENTO->tipo_ocorrencia);

		$data_RMA 	 = $this->WsOutbox->find('first', array('conditions'=>array('tipo_mensagem'=>'saida_alvo') ));	
		$objEnvelope = $this->WsOutbox->converteMensagemEmEnvelope( $data_RMA );
		$this->assertEqual($objEnvelope->EVENTO->evento, 'saida_alvo');
		
	}

}
