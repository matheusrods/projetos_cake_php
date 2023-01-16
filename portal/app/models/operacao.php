<?php

class Operacao extends AppModel {

    var $name = 'Operacao';
    var $tableSchema = 'vendas';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'operacao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $displayField = 'descricao';
    var $validate = array(
        'descricao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a descrição',
                'required' => true
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Já existe esta operação',
                'required' => true
            )
        )
    );

    /**
     * @var ClienteOperacao
     */
    public $ClienteOperacao;

    function naoPossuiClientes($codigo) {
        $this->ClienteOperacao = & ClassRegistry::init('ClienteOperacao');

        $qtde = $this->ClienteOperacao->find('count', array(
            'conditions' => array(
                'codigo_operacao' => $codigo
            )
                ));

        if ($qtde > 0) {
            $this->invalidate('codigo', 'Esta operação possui clientes');
            return false;
        }

        return true;
    }

    function beforeDelete() {
        if ($this->naoPossuiClientes($this->id)) {
            return true;
        }

        return false;
    }
    
    function converteFiltroEmConditions($filtros = null) {
        $conditions = null;
        if (isset($filtros['Operacao']['codigo']) && !empty($filtros['Operacao']['codigo']))
            $conditions['Operacao.codigo'] = $filtros['Operacao']['codigo'];
        if (isset($filtros['Operacao']['descricao']) && !empty($filtros['Operacao']['descricao']))
            $conditions['Operacao.descricao like'] = '%' . $filtros['Operacao']['descricao'] . '%';
        return $conditions;
    }
    
    function listar($conditions = null) {
        $operacoes = $this->find('all', array('conditions' => $conditions));
        return $operacoes;
    }

}

?>