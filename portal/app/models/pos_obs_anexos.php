<?php
class PosObsAnexos extends AppModel {

	public $name		   	= 'PosObsAnexos';

	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_obs_anexos';
	var $primaryKey = 'codigo';
    var $recursive = 2;

    function bindAnexos() {
		$this->bindModel(array(
			'belongsTo' => array(
				'PosAnexos' => array(
					'foreignKey' => 'codigo_pos_anexo'
                )
			)
		));
	}

    public function obterAnexos($codigo_observacao ){

        $conditions = array();
        $conditions['codigo_pos_obs_observacao'] = $codigo_observacao;
        
        $this->bindAnexos();

		return $this->find('all', array(
			//'fields' => $fields,
			'joins'=> array(),
			'conditions' => $conditions,
			'limit' => 1,
			'recursive' => 2
		));        
    }
}