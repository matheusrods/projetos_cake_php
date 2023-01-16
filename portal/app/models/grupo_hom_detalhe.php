<?php

class GrupoHomDetalhe extends AppModel {

	var $name = 'GrupoHomDetalhe';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_homogeneos_exposicao_detalhes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_homogeneos_exposicao_detalhes'));


	var $validate = array(
		'codigo_setor' => array(
			// 'rule' => 'notEmpty',
			// 'message' => 'Informe o Setor',
			// 'required' => true
		),
		'codigo_cargo' => array(
			// 'rule' => 'notEmpty',
			// 'message' => 'Informe o Cargo',
			// 'required' => true
		),
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();
        if (!empty($data['codigo']))
            $conditions['GrupoHomDetalhe.codigo'] = $data['codigo'];	

		if (!empty($data['codigo_setor']))
            $conditions['GrupoHomDetalhe.codigo_setor'] = $data['codigo_setor'];

        if (!empty($data['codigo_cargo']))
            $conditions['GrupoHomDetalhe.codigo_cargo'] = $data['codigo_cargo'];
        
        return $conditions;
    }
}

?>