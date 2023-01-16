<?php
class MetodosTipo extends AppModel
{
    public $name = 'MetodosTipo';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'metodos_tipo';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a descrição.',
            'required' => true
        )
    );

    public function retornaMetodosTipo()
    {
        $conditions = array();

        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));
    }

    public function converteFiltroEmCondition($data) {
        $conditions = array();

        //verifica se tem o codigo cliente
        if(!empty($data['codigo_cliente'])){
            $conditions['MetodosTipo.codigo_cliente'] = $data['codigo_cliente'];     
        }

        if (! empty ( $data ['descricao'] ))
            $conditions ['MetodosTipo.nome LIKE'] = '%' . $data ['descricao'] . '%';

        return $conditions;
    }

    public function getMetodosTipo(array $conditions = array(), $pagination = false){
        $fields = array(
            'MetodosTipo.codigo',
            'MetodosTipo.descricao',
            'MetodosTipo.codigo_cliente'
        );

        if($pagination){
            $paginate = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => "MetodosTipo.descricao"
            );
            return $paginate;
        } else {
            $this->MetodosTipo =& ClassRegistry::init("MetodosTipo");
            return $this->MetodosTipo->find('sql', array('fields' => $fields, 'conditions' => $conditions));
        }
    }

    public function carregar($codigo) {
        $dados = $this->find ( 'first', array (
                'conditions' => array (
                        $this->name . '.codigo' => $codigo 
                ) 
        ) );
        return $dados;
    }
}
