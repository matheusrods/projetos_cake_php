<?php
class PosFerramenta extends AppModel
{
    public $name = 'PosFerramenta';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'pos_ferramenta';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array();

    public function retornaPosFerramenta($data)
    {

        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
            $codigo_cliente = array(
                'PosFerramenta.codigo_cliente' => $data['codigo_cliente']
            );
        } else {
            $codigo_cliente = array();
        }

        $conditions = array(
            $codigo_cliente,
            'PosFerramenta.ativo' => 1
        );

        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));
    }
}
