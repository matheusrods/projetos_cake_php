<?php
class ConselhoProfissional extends AppModel {
    public $name = 'ConselhoProfissional';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'conselho_profissional';
    public $primaryKey = 'codigo';
    public $actsAs = array('Containable');
	//public $recursive = -1;

    public $hasMany = array(
		'Medico' => array(
			'className'    => 'Medico',
			'foreignKey'    => 'codigo_conselho_profissional'
			)
	);

    function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['ConselhoProfissional.codigo'] = $data['codigo'];
        
        if (!empty($data['descricao'])){
            $conditions['ConselhoProfissional.descricao LIKE'] = '%' . $data['descricao'] . '%';
        }
       

        return $conditions;
    }
    
}
