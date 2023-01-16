<?php 

class IdentidadesGenero extends AppModel {

    public $name = 'IdentidadesGenero';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'identidades_genero';
    public $primaryKey = 'codigo';

    public $actsAs = array( 'Secure' );
    public $validate = array();

    public function obterOpcoesCombo() {

        $options = array();
        $options[0] = 'Selecione';

        $options = $this->find('list', array(
            'fields' => array(
                'codigo',
                'descricao'
            ),
            'order' => array(
                'codigo'
            )
        ));

        return $options;
    }

}