<?php
class ArrtpaRi extends AppModel
{
    public $name = 'ArrtpaRi';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'arrtpa_ri';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    public function getDadosAgentesRiscos($codigo_arrtpa_ri)
    {
        $fields = array(
            'ArrtpaRi.codigo',
            'MedidasControle.codigo',
            'MedidasControle.codigo_arrtpa_ri',
            'MedidasControle.codigo_medida_controle_hierarquia_tipo',
            'MedidasControle.titulo',
            'MedidasControle.descricao',

            'RiscosImpactosAnexos.codigo',
            'RiscosImpactosAnexos.arquivo_url',

        );

        $joins = array(
            array(
                'table' => 'medidas_controle',
                'alias' => 'MedidasControle',
                'type' => 'LEFT',
                'conditions' => array('ArrtpaRi.codigo = MedidasControle.codigo_arrtpa_ri')
            ),
            array(
                'table' => 'riscos_impactos_selecionados_anexos',
                'alias' => 'RiscosImpactosAnexos',
                'type' => 'LEFT',
                'conditions' => array('ArrtpaRi.codigo = RiscosImpactosAnexos.codigo_arrtpa_ri and RiscosImpactosAnexos.data_remocao is null')
            ),

        );

        $conditions = array('ArrtpaRi.codigo' => $codigo_arrtpa_ri);

        $dados = $this->find('all', array(
            'conditions' => $conditions,
            'fields' => $fields,
            'joins' => $joins
        ));

//        debug($dados);exit;
        return $dados;
    }
}
