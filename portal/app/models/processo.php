<?php
class Processo extends AppModel
{
    public $name = 'Processo';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'processos';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_levantamento_chamado' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o levantamento do chamado',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o cliente',
            'required' => true
        ),
    );

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['Processo.codigo'] = $data['codigo'];
        }

        if (!empty($data['codigo_levantamento_chamado'])) {
            $conditions['Processo.codigo_levantamento_chamado'] = $data['codigo_levantamento_chamado'];
        }

        if (!empty($data['codigo_cliente'])) {
            $conditions['Processo.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data ['titulo'])) {
            $conditions ['Processo.titulo LIKE'] = '%' . $data ['titulo'] . '%';
        }

        if (!empty($data ['descricao'])) {
            $conditions ['Processo.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (!empty($data['codigo_usuario_inclusao'])) {
            $conditions['Processo.codigo_usuario_inclusao'] = $data['codigo_usuario_inclusao'];
        }

        if (!empty($data['codigo_usuario_alteracao'])) {
            $conditions['Processo.codigo_usuario_alteracao'] = $data['codigo_usuario_alteracao'];
        }

        if (!empty($data['data_alteracao'])) {
            $conditions['Processo.data_alteracao'] = $data['data_alteracao'];
        }

        if (!empty($data['codigo_processo_tipo'])) {
            $conditions['Processo.codigo_processo_tipo'] = $data['codigo_processo_tipo'];
        }

        return $conditions;
    }

    public function getListaProcessos($filtros = null)
    {
        $fields = array(
            'Processo.codigo',
            'Processo.codigo_levantamento_chamado',
            'Processo.codigo_cliente',
            'Processo.titulo',
            'Processo.descricao',
            'Processo.codigo_processo_tipo',
            'ProcessoTipo.codigo',
            'ProcessoTipo.descricao'
        );

        $conditions = $this->converteFiltroEmCondition($filtros);
        
        $joins  = array(
            array(
              'table' => 'RHHealth.dbo.processos_tipo',
              'alias' => 'ProcessoTipo',
              'type' => 'LEFT',
              'conditions' => 'ProcessoTipo.codigo = Processo.codigo_processo_tipo',
            )
        );

        $processos = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => 'Processo.codigo desc',
        );

        return $processos;
    }
}
