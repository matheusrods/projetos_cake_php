<?php
class FichaStatusCriterioLog extends AppModel {
	var $name = 'FichaStatusCriterioLog';
	var $primaryKey = 'codigo';
	var $displayField = 'pontos';
	var $databaseTable = 'dbTeleconsult';
	var $tableSchema = 'informacoes';
	var $useTable = 'fichas_status_criterios_log';
	var $actsAs = array('Secure');

	public function listarRespostasFicha($codigo_ficha) {
		$PontuacaoSCProfissional = ClassRegistry::init("PontuacaoSCProfissional");
		$FichaScorecardLog          = ClassRegistry::init("FichaScorecardLog");
		$lista =  $this->find('all', array(
			'conditions'=>array('FichaStatusCriterioLog.codigo_ficha_log'=>$codigo_ficha), 
			'fields'    =>array('FichaStatusCriterioLog.codigo', 'FichaStatusCriterioLog.codigo_criterio', 'FichaStatusCriterioLog.codigo_status_criterio', 'FichaStatusCriterioLog.observacao', 'FichaStatusCriterioLog.automatico')));
		$return['FichaStatusCriterio'] = array();
		foreach($lista as $item){
			$return['FichaStatusCriterio'][$item['FichaStatusCriterioLog']['codigo_criterio']] = $item['FichaStatusCriterioLog'];
		}
		return $return;
	}

}
