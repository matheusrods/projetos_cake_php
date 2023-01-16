<?php
class RiscosImpactosTipo extends AppModel
{
    public $name = 'RiscosImpactosTipo';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'riscos_impactos_tipo';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        )
    );

    public function retornaRiscosImpactosTipo()
    {
        $conditions = array();

        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));
    }
}
