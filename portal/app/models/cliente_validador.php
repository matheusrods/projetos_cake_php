<?php

class ClienteValidador extends AppModel {

	var $name = 'ClienteValidador';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_validador';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	public function FiltroEmCondition($data) {

        $conditions = array();

        if (!empty($data['codigo_cliente'])){
            $conditions['ClienteValidador.codigo_cliente_matriz'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_unidade'])){
            $conditions['ClienteValidador.codigo_cliente_alocacao'] = $data['codigo_unidade'];
        }

        if (isset($data['login']) && !empty($data['login'])  && trim($data['login']) != '') {
            $conditions['Usuario.apelido like'] = '%' . $data['login'] . '%';
        }

        if (isset($data['nome_usuario']) && !empty($data['nome_usuario'])  && trim($data['nome_usuario']) != '') {
            $conditions['Usuario.nome like'] = '%' . $data['nome_usuario'] . '%';
        }

        return $conditions;
    }

    public function getClienteValidadores($conditions){
		
		$fields = array(
            'ClienteValidador.codigo',
            'ClienteValidador.codigo_cliente_matriz',
            'ClienteValidador.codigo_cliente_alocacao',
            'ClienteValidador.codigo_usuario',
			'Unidade.codigo',
            'Unidade.nome_fantasia',
            'Usuario.apelido',
            'Usuario.nome',
        );

		$joins = array(
			array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'ClienteMatriz',
                'type' => 'INNER',
                'conditions' => array('ClienteValidador.codigo_cliente_matriz = ClienteMatriz.codigo')
			),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Unidade',
                'type' => 'INNER',
                'conditions' => array('ClienteValidador.codigo_cliente_alocacao = Unidade.codigo')
            ),
            array(
                'table' => 'RHHealth.dbo.usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => array('ClienteValidador.codigo_usuario = Usuario.codigo')
            ),    
        );
        // debug($conditions);exit;

        $order = 'ClienteValidador.codigo';

        $dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order
        );    

        // pr( $this->find('sql',$dados) );exit;

		return $dados;
	}//fim query

}//fim class ClienteValidador
?>