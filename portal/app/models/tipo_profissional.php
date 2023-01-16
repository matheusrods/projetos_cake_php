<?php
class TipoProfissional extends AppModel {

    var $name = 'TipoProfissional';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'profissional_tipo';
    var $displayField = 'descricao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	public function buscaDescricaoTipoProfissional($codigo_tipo){
		$condition['TipoProfissional.codigo'] = $codigo_tipo;
		$return = $this->find('first',array('conditions'=>$condition));
        return $return['TipoProfissional']['descricao'];
	}


}
