<?php
class ArrtpariGhe extends AppModel
{
    public $name = 'ArrtpariGhe';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'arrtpari_ghe';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_ghe' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo ghe',
            'required' => true
        ),

        'codigo_arrtpa_ri' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo arrtpa_ri',
            'required' => true
        ),
    );
    
    public function getRiscosImpactosByCodigoGhe($codigo_ghe, $codigo_cliente = null)
    {
        $riscosImpactos = $this->find(
            'all',
            array(
                'fields' => array(
                    // 'ArrtpariGhe.codigo',
                    // 'ArrtpariGhe.codigo_ghe',
                    'ArrtpariGhe.codigo_arrtpa_ri',
                ),
                'conditions' => array('ArrtpariGhe.codigo_ghe' => $codigo_ghe),
            )
        );

        foreach ($riscosImpactos as $key => $risco) {
            $riscosImpactos[$key] = $risco['ArrtpariGhe'];
		}

        return $riscosImpactos;
    }
}
