<?php
class WsOutbox extends AppModel {
    var $name = 'WsOutbox';
    var $tableSchema = 'portal';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'ws_outbox';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    public function asEnvelope($data) {
    	$dataExploded = explode('|', $data['WsOutbox']['mensagem']);
    	$envelope = new Envelope();
    	$envelope->EVENTO->NU_SMP = $dataExploded[0];
    	$envelope->EVENTO->NU_ANO_SMP = $dataExploded[1];
    	$envelope->EVENTO->DS_DOC_CONTROLE = $dataExploded[2];
    	$envelope->EVENTO->DT_EVENTO = str_replace(' ', 'T', $dataExploded[3]);
    	$envelope->EVENTO->CD_EVENTO = $dataExploded[4];
    	$envelope->EVENTO->DS_EVENTO = $dataExploded[5];
    	$envelope->EVENTO->NU_LAT = number_format(str_replace(',', '.', $dataExploded[6]), 8);
    	$envelope->EVENTO->NU_LONG = number_format(str_replace(',', '.', $dataExploded[7]), 8);
    	$envelope->EVENTO->DS_PLACA_CAVALO = $dataExploded[8];
    	$envelope->EVENTO->DS_PLACA_CARRETA = $dataExploded[9];
    	$envelope->EVENTO->NM_PONTO = $dataExploded[10];
    	$envelope->EVENTO->NM_APELIDO_PONTO = $dataExploded[11];
    	$envelope->EVENTO->DS_DOCUMENTO_NF = $dataExploded[12];
    	$envelope->EVENTO->TEMPERATURA = $dataExploded[13];
    	return $envelope;
    }
		
	public function proximosNaoEnviados($limit) {
		return $this->find('all', array(
			'conditions'=>array('data_processado'=>null),
			'order'=>array('codigo'),
			'limit'=>$limit
		));
	}
	
	public function marcarEnviado($id) {
		$this->id = $id;
        $data = array(
            'data_processado'=>date('Y-m-d H:i:s'),
            'enviado'=>'S'
        );
		return $this->save($data);
	}

    public function marcarNaoEnviado($id) {
        $this->id = $id;
        $data = array(
            'data_processado'=>date('Y-m-d H:i:s'),
            'enviado'=>'N'
        );
        return $this->save($data);
    }

    public function adicionarTentativa($id) {
        $dados = $this->find('first',array('conditions'=>array('codigo'=>$id)));
        $tentativas = (!empty($dados['WsOutbox']['tentativas'])?$dados['WsOutbox']['tentativas']:0);
        $this->id = $id;
        if ($this->saveField('tentativas', ++$tentativas)) {
            return $tentativas;
        } else return --$tentativas;
    }    
	
	public function limpaHistorico() {
        $data_limite = date('Y-m-d H:i:s', strtotime('-1 month'));
        $this->query("DELETE FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} WHERE data_processado < '{$data_limite}'");
	}

    public function asEnvelopeEventos($data, $atributos_maiusculo = false) {
        $dataExploded = explode('|', $data['WsOutbox']['mensagem']);
        $envelope = new Envelope();
        if ($atributos_maiusculo) {
            $envelope->EVENTO->SM        = $dataExploded[0];
            $envelope->EVENTO->LATITUDE  = $dataExploded[6];
            $envelope->EVENTO->LONGITUDE = $dataExploded[7];
            $envelope->EVENTO->EVENTO    = $data['WsOutbox']['tipo_mensagem'];
            $envelope->EVENTO->ALVO      = $dataExploded[10];
            $envelope->EVENTO->DATA_HORA = str_replace(' ', 'T', $dataExploded[3]);
            $envelope->EVENTO->CODIGO_EXTERNO = $dataExploded[14];
            $envelope->EVENTO->PEDIDO = $dataExploded[2];
        } else {
            $envelope->EVENTO->sm        = $dataExploded[0];
            $envelope->EVENTO->latitude  = $dataExploded[6];
            $envelope->EVENTO->longitude = $dataExploded[7];
            $envelope->EVENTO->evento    = $data['WsOutbox']['tipo_mensagem'];
            $envelope->EVENTO->alvo      = $dataExploded[10];
            $envelope->EVENTO->data_hora = str_replace(' ', 'T', $dataExploded[3]);
            $envelope->EVENTO->codigo_externo = $dataExploded[14];
            $envelope->EVENTO->pedido = $dataExploded[2];
        }
        return $envelope;
    }
    
    public function asEnvelopeRMA($data, $atributos_maiusculo = false) {    
        $dataExploded = explode('|', $data['WsOutbox']['mensagem']);
        $envelope = new Envelope();
        if ($atributos_maiusculo) {
            $envelope->EVENTO->SM               = $dataExploded[0];
            $envelope->EVENTO->PLACA            = $dataExploded[1];
            $envelope->EVENTO->LOCAL            = $dataExploded[2];
            $envelope->EVENTO->FATO_GERADOR     = $dataExploded[3];
            $envelope->EVENTO->TIPO_OCORRENCIA  = $dataExploded[4];
            $envelope->EVENTO->OPERADOR         = $dataExploded[5];
            $envelope->EVENTO->DATA_HORA        = str_replace(' ', 'T', $dataExploded[6]);
        } else {
            $envelope->EVENTO->sm               = $dataExploded[0];
            $envelope->EVENTO->placa            = $dataExploded[1];
            $envelope->EVENTO->local            = $dataExploded[2];
            $envelope->EVENTO->fato_gerador     = $dataExploded[3];
            $envelope->EVENTO->tipo_ocorrencia  = $dataExploded[4];
            $envelope->EVENTO->operador         = $dataExploded[5];
            $envelope->EVENTO->data_hora        = str_replace(' ', 'T', $dataExploded[6]);
        }
        return $envelope;
    }

    public function converteMensagemEmEnvelope($data, $atributos_maiusculo = false){
        if($data['WsOutbox']['tipo_mensagem'] == 'rma'){
            return $this->asEnvelopeRMA($data, $atributos_maiusculo);
        }else{
            return $this->asEnvelopeEventos($data, $atributos_maiusculo);
        }
    }

}
?>