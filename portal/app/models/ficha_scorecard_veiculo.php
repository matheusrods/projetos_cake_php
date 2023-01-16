<?php
class FichaScorecardVeiculo extends AppModel {
    var $name = 'FichaScorecardVeiculo';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_veiculo';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    const CAVALO  = 0;
    const CARRETA = 1;
    const BITREM  = 2;
    
    var $descricoes = array(self::CAVALO  => 'Cavalo', self::CARRETA => 'Carreta', self::BITREM  => 'Bitrem' );
    public function descricao( $tipo_veiculo ) {
        return ClassRegistry::init('FichaScorecardVeiculo')->descricoes[$tipo_veiculo];
    }

    public function salvar($tipo_veiculo, $veiculo_log, $proprietario_logs, $codigo_ficha_scorecard){
    	$this->deleteAll(array('tipo'=>$tipo_veiculo, 'codigo_ficha_scorecard'=>$codigo_ficha_scorecard));
    	$data = array(
    		'codigo_veiculo_log' => $veiculo_log['VeiculoLog'],
    		'tipo' => $tipo_veiculo,
    		'codigo_ficha_scorecard' => $codigo_ficha_scorecard,
    		'codigo_proprietario_log' => $proprietario_logs['ProprietarioLog'],
    		'codigo_proprietario_endereco_log' => $proprietario_logs['ProprietarioEnderecoLog'],
    	);
    	$this->create();
    	if($this->save($data, array('validate' => TRUE)))
    		return $this->id;
    	return null;
    }

    public function excluir($codigo_ficha_scorecard){
    	$codigos = $this->find('list', array('conditions'=>array('codigo_ficha_scorecard'=>$codigo_ficha_scorecard), 'fields'=>array('codigo')));    	
    	$this->FichaScVeicPropContatoLog = ClassRegistry::init("FichaScVeicPropContatoLog");
    	$this->FichaScVeicPropContatoLog->primaryKey = 'codigo_ficha_scorecard_veiculo';
    	$this->FichaScVeicPropContatoLog->delete($codigos);
    	$this->FichaScVeicPropContatoLog->primaryKey = null;    	
    	$this->delete($codigos);
    }
}