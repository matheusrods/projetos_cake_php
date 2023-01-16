<?php
class Cnae extends AppModel {

    var $name = 'Cnae';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cnae';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_cnae'));
    
    var $hasOne = array(
        'CnaeSecao' => array(
            'className'    => 'CnaeSecao',
            'conditions' => 'Cnae.secao = CnaeSecao.secao',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

    var $validate = array(
        'cnae' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o CNAE',
                'required' => true
             ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Cnae já existe',
            ),
            'numeric' => array(
                'rule' => 'numeric',
                'message' => 'Apenas números são permitidos',
            ),
            'maxlength' => array(
                'rule' => array('maxlength', '7'),
                'message' => 'Máximo de 7 caracteres'
            ),
            'minlength' => array(
                'rule' => array('minlength', '7'),
                'message' => 'Mínimo de 7 caracteres'
            ),
        ),
        'descricao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a descrição',
                'required' => true
             )
        ),
        'secao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Seção',
            'required' => true
        ),
        'grau_risco' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o Grau de Risco',
                'required' => true
             ),
            'numeric' => array(
                'rule' => 'numeric',
                'message' => 'Apenas números são permitidos',
                )
        )
    );
    
    function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['cnae']))
            $conditions['Cnae.cnae'] = $data['cnae'];

        if (! empty ( $data ['descricao'] ))
            $conditions ['Cnae.descricao LIKE'] = '%' . $data ['descricao'] . '%'; 

        if (! empty ( $data ['secao'] ))
            $conditions ['Cnae.secao LIKE'] =  $data ['secao'] ;

        if (!empty($data['grau_risco']))
            $conditions['Cnae.grau_risco'] = $data['grau_risco'];

        return $conditions;
    }

    function carregar($codigo) {
        $dados = $this->find ( 'first', array (
                'conditions' => array (
                        $this->name . '.codigo' => $codigo 
                ) 
        ) );
        return $dados;
    }

    function incluir($dados){

        if (!parent::incluir($dados)){
            return false;
        }
        else{
            return true;
        }
    }
    
    function atualizar($dados){

        if (!parent::atualizar($dados)){
            return false;
        }
        else{
            return true;
        }
    }

    function excluir($codigo) {
        return $this->delete($codigo);
    }

    /**
     * Busca um Cnae pelo..Cnae
     * 
     * @return array
     */
    public function buscarCnae($cnae) {        
        $result = $this->find('first', array(
            'conditions' => array(
                'Cnae.cnae ' => $cnae 
            ),
            'fields' => array(
                'Cnae.descricao as descricao',
                'Cnae.secao as secao',
                'CnaeSecao.descricao as secao_descricao',   
             )
        ));
        
        return $result[0];
    }
}
