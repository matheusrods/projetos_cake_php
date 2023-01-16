<?php
class UnidadesMedicao extends AppModel
{
    public $name = 'UnidadesMedicao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'unidades_medicao';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'inteiro' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o inteiro.',
            'required' => true
        )
    );

    public function getListaUnidadesMedicao($filtros = null)
    {

        $fields = array(
            'UnidadesMedicao.codigo',
            'UnidadesMedicao.descricao',
            'UnidadesMedicao.inteiro',
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $unidades_medicao = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'UnidadesMedicao.codigo desc',
        );

        return $unidades_medicao;
    }

    function getByCodigo($codigo) {
        $fields = array(
            'UnidadesMedicao.codigo',
            'UnidadesMedicao.descricao',
            'UnidadesMedicao.inteiro'
        );

        $conditions = array('UnidadesMedicao.codigo' => $codigo);

        $unidades_medicao = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($unidades_medicao)){
            return array();
        }

        return $unidades_medicao;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['UnidadesMedicao.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['UnidadesMedicao.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (!empty($data['inteiro'])) {
            $conditions['UnidadesMedicao.inteiro'] = $data['inteiro'];
        }

//        if (isset($data ['ativo'])) {
//            if ($data ['ativo'] === '0') {
//                $conditions [] = '(UnidadesMedicao.ativo = ' . $data ['ativo'] . ' OR UnidadesMedicao.ativo IS NULL)';
//            } elseif ($data ['ativo'] == '1') {
//                $conditions ['UnidadesMedicao.ativo'] = $data ['ativo'];
//            }
//        }

        return $conditions;
    }
}
