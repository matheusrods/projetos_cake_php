<?php

class GrupoHomogeneoExameExterno extends AppModel {

	var $name = 'GrupoHomogeneoExameExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'grupos_homogeneos_exames_externo';
	var $primaryKey = 'codigo';

	var $validate = array();

	var $hasOne = array(
        'GrupoHomogeneoExame' => array(
            'className'    => 'GrupoHomogeneoExame',
            'conditions' => 'GrupoHomogeneoExameExterno.codigo_grupo_homogeneo_exame = GrupoHomogeneoExame.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo_cliente']))
			$conditions['GrupoHomogeneoExame.codigo_cliente'] = $data ['codigo_cliente'];

		if (!empty($data['codigo']))
			$conditions['GrupoHomogeneoExame.codigo'] = $data['codigo'];
		
        if (! empty ( $data ['descricao'] ))
			$conditions ['GrupoHomogeneoExame.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(GrupoHomogeneoExame.ativo = ' . $data ['ativo'] . ' OR GrupoHomogeneoExame.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['GrupoHomogeneoExame.ativo'] = $data ['ativo'];
	    }

	    if (isset ($data ['codigo_externo']) && !empty($data ['codigo_externo']) ){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }

        return $conditions;
    }

}

?>