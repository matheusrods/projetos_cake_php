<?php

class ChamadoStatus extends AppModel
{
    public $name = 'ChamadoStatus';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'chamados_status';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    const ABERTO = 1;
    const EM_ANDAMENTO = 2;
    const CONCLUIDO = 3;
    const CANCELADO = 4;

    public $validate = array(
        'descricao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a descrição.',
                'required' => true
             ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Descrição já existe.',
            ),
        ),
        'ativo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Status',
            'required' => true
        )

    );

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['ChamadoStatus.codigo'] = $data['codigo'];
        }

        if (! empty($data ['descricao'])) {
            $conditions ['ChamadoStatus.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(ChamadoStatus.ativo = ' . $data ['ativo'] . ' OR ChamadoStatus.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['ChamadoStatus.ativo'] = $data ['ativo'];
            }
        }
        
        return $conditions;
    }
    
    public function retorna_status($codigo_tipo = null)
    {
        $conditions = array();
        if (!is_null($codigo_tipo)) {
            $conditions['OR'] = array('ativo' => 1, 'codigo' => $codigo_tipo);
        } else {
            $conditions = array('ativo' => 1);
        }
        
        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));
    }
}
