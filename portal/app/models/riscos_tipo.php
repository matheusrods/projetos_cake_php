<?php
class RiscosTipo extends AppModel
{
    public $name = 'RiscosTipo';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'riscos_tipo';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'cor' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a cor',
            'required' => true
        ),
        'icone' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o icone',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código cliente',
            'required' => true
        )
    );

    public function getListaRiscosTipo($filtros = null)
    {
        $fields = array(
            'RiscosTipo.codigo',
            'RiscosTipo.descricao',
            'RiscosTipo.cor',
            'RiscosTipo.icone',
            'RiscosTipo.ativo',
            'RiscosTipo.codigo_cliente'
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $riscos_tipo = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'RiscosTipo.codigo desc',
        );

        return $riscos_tipo;
    }

    public function retornaRiscoTipo($data = null)
    {

        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
            $codigo_cliente = array(
                'RiscosTipo.codigo_cliente' => $data['codigo_cliente']
            );
        } else {
            $codigo_cliente = array();
        }

        $conditions = array(
            $codigo_cliente,
            'RiscosTipo.ativo' => 1
        );

        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));;
    }

    function getByCodigo($codigo) {
        $fields = array(
            'RiscosTipo.codigo',
            'RiscosTipo.descricao',
            'RiscosTipo.cor',
            'RiscosTipo.icone',
            'RiscosTipo.ativo',
            'RiscosTipo.codigo_cliente'
        );

        $conditions = array('RiscosTipo.codigo' => $codigo);

        $riscos_tipo = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($riscos_tipo)){
            return array();
        }

        return $riscos_tipo;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['RiscosTipo.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['RiscosTipo.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (!empty($data ['cor'])) {
            $conditions ['RiscosTipo.cor LIKE'] = '%' . $data ['cor'] . '%';
        }

        if (!empty($data ['icone'])) {
            $conditions ['RiscosTipo.icone LIKE'] = '%' . $data ['icone'] . '%';
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(RiscosTipo.ativo = ' . $data ['ativo'] . ' OR RiscosTipo.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['RiscosTipo.ativo'] = $data ['ativo'];
            }
        }

        if (isset($data ['codigo_cliente'])) {
            $conditions ['RiscosTipo.codigo_cliente'] = $data ['codigo_cliente'];
        }

        return $conditions;
    }
}
