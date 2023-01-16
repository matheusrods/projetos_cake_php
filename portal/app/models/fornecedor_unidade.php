<?php
class FornecedorUnidade extends AppModel {
    var $name = 'FornecedorUnidade';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'fornecedores_unidades';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    var $validate = array(
        'codigo_fornecedor_matriz' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Matriz'
        ),
        'codigo_fornecedor_unidade' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a Unidade'
             ),
            'validaUnidade' => array(
                'rule' => 'validaUnidade',
                'message' => 'Unidade inválida',
            ),
            'validaUnidadeUnica' => array(
                'rule' => 'validaUnidadeUnica',
                'message' => 'Unidade já existe!',
            )
        )
    );
       
    function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo_fornecedor_unidade']))
            $conditions['FornecedorUnidade.codigo_fornecedor_unidade'] = $data['codigo_fornecedor_unidade'];
        
        return $conditions;
    }

    function validaUnidade(){
        $this->Fornecedor = & ClassRegistry::init('Fornecedor');
        $valida_unidade = $this->Fornecedor->find('first', array('conditions' => array('codigo' => $this->data['FornecedorUnidade']['codigo_fornecedor_unidade'])));

        if(empty($valida_unidade)){
            return false;
        }
        else{
            return true;
        }
    }
    function validaUnidadeUnica(){
        
        $conditions = array(
            'codigo_fornecedor_matriz' => $this->data['FornecedorUnidade']['codigo_fornecedor_matriz'],
            'codigo_fornecedor_unidade' => $this->data['FornecedorUnidade']['codigo_fornecedor_unidade'],
        );
        
        if(isset($this->data['FornecedorUnidade']['codigo']) && !empty($this->data['FornecedorUnidade']['codigo'])){
            $conditions['codigo <>'] = $this->data['FornecedorUnidade']['codigo'];
        }

        $valida_unidade = $this->find('first', array( 
            'conditions' => $conditions
            )
        );

        if(empty($valida_unidade)){
            return true;
        }
        else{
            return false;
        }

    }
}
?>