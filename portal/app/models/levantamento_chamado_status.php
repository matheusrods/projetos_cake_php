<?php

class LevantamentoChamadoStatus extends AppModel
{
    public $name = 'LevantamentoChamadoStatus';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'levantamentos_chamados_status';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    const NAO_INICIADO = 1;
    const ADIADO = 2;
    const EM_ANDAMENTO = 3;
    const CONCLUIDO = 4;
    const CANCELADO = 5;

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
            $conditions['LevantamentoChamadoStatus.codigo'] = $data['codigo'];
        }

        if (! empty($data['descricao'])) {
            $conditions['LevantamentoChamadoStatus.descricao LIKE'] = '%' . $data['descricao'] . '%';
        }

        if (isset($data['ativo'])) {
            if ($data['ativo'] === '0') {
                $conditions[] = '(LevantamentoChamadoStatus.ativo = ' . $data['ativo'] . ' OR LevantamentoChamadoStatus.ativo IS NULL)';
            } elseif ($data['ativo'] == '1') {
                $conditions['LevantamentoChamadoStatus.ativo'] = $data['ativo'];
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
