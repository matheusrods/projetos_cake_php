<?php

class TipoDigitalizacao extends AppModel {

	var $name = 'TipoDigitalizacao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipo_digitalizacao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição',
			'required' => true
		)
	);

	/**
	 * [converteFiltroEmCondition description]
	 * 
	 * metodo para montar o where realizando o filtro da tela
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function converteFiltroEmCondition($data) 
	{

        $conditions = array();
        if (!empty($data['codigo'])){
            $conditions['TipoDigitalizacao.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])){
            $conditions ['TipoDigitalizacao.descricao LIKE'] = '%' . $data['descricao']. '%'; 
        }        

		if (isset($data['ativo']) && $data['ativo'] != "") {
			$conditions ['TipoDigitalizacao.ativo'] = $data['ativo'];
        } 

        return $conditions;
    }



	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo' => $codigo 
				) 
		) );
		return $dados;
	}	
	
}//fim class TipoDigitalizacao
?>