<?php

class ProcessoTipo extends AppModel
{
    public $name = 'ProcessoTipo';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'processos_tipo';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

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
            $conditions['ProcessoTipo.codigo'] = $data['codigo'];
        }

        if (! empty($data ['descricao'])) {
            $conditions ['ProcessoTipo.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(ProcessoTipo.ativo = ' . $data ['ativo'] . ' OR ProcessoTipo.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['ProcessoTipo.ativo'] = $data ['ativo'];
            }
        }
        
        return $conditions;
    }
    
    public function retorna_tipo($codigo_tipo = null)
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
