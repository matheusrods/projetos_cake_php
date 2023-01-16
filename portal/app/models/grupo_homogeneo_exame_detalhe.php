<?php

class GrupoHomogeneoExameDetalhe extends AppModel {

	var $name = 'GrupoHomogeneoExameDetalhe';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_homogeneos_exames_detalhes';
	var $primaryKey = 'codigo';
    //var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_homogeneos_exposicao_detalhes'));
    var $actsAs = array('Secure', 'Containable');

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
            $conditions['GrupoHomogeneoExameDetalhe.codigo'] = $data['codigo'];	

		if (!empty($data['codigo_setor']))
            $conditions['GrupoHomogeneoExameDetalhe.codigo_setor'] = $data['codigo_setor'];

        if (!empty($data['codigo_cargo']))
            $conditions['GrupoHomogeneoExameDetalhe.codigo_cargo'] = $data['codigo_cargo'];
        
        return $conditions;
    }
}

?>