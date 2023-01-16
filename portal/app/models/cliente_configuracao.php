<?php

class ClienteConfiguracao extends AppModel {

	public $name = 'ClienteConfiguracao';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'clientes_configuracoes';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable');
    public $validate = array(
        'codigo_cliente_matricula' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

    /**
     * [converteFiltroEmCondition description]
     * 
     * metodo para converter os dados em filtros na tabela
     * 
     * @return [type] [description]
     */
    public function converteFiltroEmCondition($data)
    {

		$conditions = array();

		if (!empty($data['codigo_cliente_matricula'])) {
			$conditions ['ClienteConfiguracao.codigo_cliente_matricula'] = $data ['codigo_cliente_matricula'];
		}

		if (!empty($data['finaliza_setor_cargo'])) {
			$conditions['ClienteConfiguracao.finaliza_setor_cargo'] = $data['finaliza_setor_cargo'];
		}

		// pr($conditions);exit;
		
		return $conditions;


    }//fim converteFiltroEmCondition

}