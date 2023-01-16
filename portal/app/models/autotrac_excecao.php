<?php
class AutotracExcecao extends AppModel {
	var $name = 'AutotracExcecao';
	var $primaryKey = 'codigo';
	var $databaseTable = 'dbBuonny';
	var $tableSchema = 'vendas';
	var $useTable = 'autotrac_excecoes';
	var $actsAs = array('Secure');
	var $validate = array(
        'codigo_cliente' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o código do cliente',
                'required' => true
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Já existe este cliente na exceção',
                'required' => true
            )
        )
    );	

	var $belongsTo = array(
        'Cliente' => array(
            'className' => 'Cliente',
            'foreignKey' => false,
            'conditions' => array('Cliente.codigo = AutotracExcecao.codigo_cliente')
        )
    );

    function converteFiltroEmCondition($filtros){
    	$conditions = array();
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			$conditions['codigo_cliente'] = $filtros['codigo_cliente'];
		}
		return $conditions; 
    }

    function lista_codigo_guardian_cliente_excecao(){
        App::import('Component','DbbuonnyGuardian');
        $this->DbbuonnyGuardian = new DbbuonnyGuardianComponent();
        $codigo_clientes = $this->find('all',array('fields' => 'codigo_cliente'));   
        $clientes = null;
        foreach ($codigo_clientes as $codigo_cliente) {
            $cliente = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($codigo_cliente['AutotracExcecao']['codigo_cliente']);
            $clientes[] = $cliente[0];
        }
        return $clientes;
    }
}  
