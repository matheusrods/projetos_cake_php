<?php
class CscGhe extends AppModel
{
    public $name = 'CscGhe';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'csc_ghe';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_ghe' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo ghe',
            'required' => true
        ),

        'codigo_clientes_setores_cargos' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo clientes setores cargos',
            'required' => true
        ),
    );
    
    public function getSetoresByCodigoGhe($codigo_ghe, $codigo_cliente = null)
    {
        $setores = $this->find(
            'all',
            array(
                'fields' => array(
                    'DISTINCT setor.codigo',
                    'setor.descricao',
                ),
                'joins' => array(
                    array(
                        'table' => 'RHHealth.dbo.clientes_setores_cargos',
                        'alias' => 'ClienteSetorCargo',
                        'type' => 'INNER',
                        'conditions' => 'ClienteSetorCargo.codigo = CscGhe.codigo_clientes_setores_cargos',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.setores',
                        'alias' => 'setor',
                        'type' => 'INNER',
                        'conditions' => 'setor.codigo = ClienteSetorCargo.codigo_setor',
                    ),
                ),
                'conditions' => array('CscGhe.codigo_ghe' => $codigo_ghe),
                'order' => 'setor.descricao',
            )
        );

        foreach ($setores as $key => $setor) {
            $setores[$key]['cargos'] = $this->getCargosByCodigoClienteSetor($codigo_ghe, $codigo_cliente, $setor['setor']['codigo']);
        }

        return $setores;
    }

    public function getCargosByCodigoClienteSetor($codigo_ghe, $codigo_cliente, $codigo_setor)
    {
        $cargos = $this->find(
            'all',
            array(
                'fields' => array(
                    'cargo.codigo',
                    'cargo.descricao',
                ),
                'joins' => array(
                    array(
                        'table' => 'RHHealth.dbo.clientes_setores_cargos',
                        'alias' => 'ClienteSetorCargo',
                        'type' => 'INNER',
                        'conditions' => 'ClienteSetorCargo.codigo = CscGhe.codigo_clientes_setores_cargos',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.cargos',
                        'alias' => 'cargo',
                        'type' => 'INNER',
                        'conditions' => 'cargo.codigo = ClienteSetorCargo.codigo_cargo',
                    ),
                ),
                'conditions' => array(
                    'CscGhe.codigo_ghe' => $codigo_ghe,
					'ClienteSetorCargo.codigo_cliente' => $codigo_cliente, 
					'ClienteSetorCargo.codigo_setor' => $codigo_setor),
                'order' => 'cargo.descricao',
            )
		);
		
		foreach ($cargos as $key => $cargo) {
            $cargos[$key] = $cargo['cargo'];
		}

        return $cargos;
    }
}
