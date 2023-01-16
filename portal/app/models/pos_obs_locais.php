<?php
class PosObsLocais extends AppModel {

	public $name		   	= 'PosObsLocais';

	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_obs_locais';
	var $primaryKey = 'codigo';
    var $recursive = 2;

    function bindLocal() {
		$this->bindModel(array(
			'belongsTo' => array(
				'ClienteOpco' => array(
					'foreignKey' => 'codigo_cliente_opco'
                ),
                'ClienteBu' => array(
					'foreignKey' => 'codigo_cliente_bu'
				),
                'ClienteEnderecoLocalidade' => array(
                    'className'     => 'ClienteEndereco',
					'foreignKey' => 'codigo_localidade'
				),
                'ClienteEnderecoEmpresa' => array(
                    'className'     => 'ClienteEndereco',
					'foreignKey' => 'codigo_local_empresa'
				)
			)
		));
	}

    public function obterLocais($codigo_observacao ){

        $conditions = array();
        $conditions['codigo_pos_obs_observacao'] = $codigo_observacao;
        
        $this->bindLocal();

		return $this->find('all', array(
			//'fields' => $fields,
			'joins'=> array(),
			'conditions' => $conditions,
			'limit' => 1,
			'recursive' => 2
		));        
    }
}