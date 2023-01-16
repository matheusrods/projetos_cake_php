<?php
class PerigosAspectos extends AppModel
{
    public $name = 'PerigosAspectos';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'perigos_aspectos';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'codigo_risco_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código risco tipo',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe código cliente',
            'required' => true
        ),
    );

    public function getListaPerigosAspectos($filtros = null)
    {
        $fields = array(
            'PerigosAspectos.codigo',
            'PerigosAspectos.descricao',
            'PerigosAspectos.codigo_risco_tipo',
            'PerigosAspectos.ativo',
            'RiscosTipo.descricao',
            'PerigosAspectos.codigo_cliente',
        );

        $joins = array(
            array(
                'table' => 'riscos_tipo',
                'alias' => 'RiscosTipo',
                'type' => 'INNER',
                'conditions' => 'PerigosAspectos.codigo_risco_tipo = RiscosTipo.codigo'
            )
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $perigos_aspectos = array(
            'fields' => $fields,
            'joins'  => $joins,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'PerigosAspectos.codigo desc',
        );

        return $perigos_aspectos;
    }

    public function getByCodigo($codigo) {
        $fields = array(
            'PerigosAspectos.codigo',
            'PerigosAspectos.descricao',
            'PerigosAspectos.codigo_risco_tipo',
            'PerigosAspectos.codigo_risco_tipo',
            'PerigosAspectos.codigo_cliente',
        );

        $conditions = array('PerigosAspectos.codigo' => $codigo);

        $perigos_aspectos = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($perigos_aspectos)){
            return array();
        }

        return $perigos_aspectos;
    }

    public function retornaPerigosAspectos($data)
    {

        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
            $codigo_cliente = array(
                'PerigosAspectos.codigo_cliente' => $data['codigo_cliente']
            );
        } else {
            $codigo_cliente = array();
        }

        $conditions = array(
            $codigo_cliente,
            'PerigosAspectos.ativo' => 1
        );

        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['PerigosAspectos.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['PerigosAspectos.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (!empty($data ['codigo_risco_tipo'])) {
            $conditions ['PerigosAspectos.codigo_risco_tipo'] = $data ['codigo_risco_tipo'];
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(PerigosAspectos.ativo = ' . $data ['ativo'] . ' OR PerigosAspectos.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['PerigosAspectos.ativo'] = $data ['ativo'];
            }
        }

        if (!empty($data ['codigo_cliente'])) {
            $conditions ['PerigosAspectos.codigo_cliente'] = $data ['codigo_cliente'];
        }

        return $conditions;
    }
}
