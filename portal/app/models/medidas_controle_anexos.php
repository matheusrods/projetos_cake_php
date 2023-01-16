<?php
class MedidasControleAnexos extends AppModel
{
    public $name = 'MedidasControleAnexos';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'medidas_controle_anexos';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    public function getMedidasControleAnexos($codigo_medida_controle)
    {

        $fields = array(
            'codigo',
            'codigo_medida_controle',
            'arquivo_url',
        );


        $conditions = array('MedidasControleAnexos.codigo_medida_controle' => $codigo_medida_controle);

        $dados = $this->find('all', array(
            'conditions' => $conditions,
            'fields' => $fields,
        ));

        return $dados;
    }

}
