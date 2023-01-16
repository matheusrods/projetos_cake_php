<?php
class MotivoRecusaExame extends AppModel {

	public $name		   	= 'MotivoRecusaExame';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'motivo_recusa_exame';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');

	public $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição',
				'required' => true
			 ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Descrição já existe'
			)
		),
        'ativo' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Status',
				'required' => true
			 ),
		),
	);
	
	public function getAll(array $FILTROS = array(), $pagination = false){
	    $fields = array(
	        'MotivoRecusaExame.codigo as codigo',
            'MotivoRecusaExame.descricao as descricao',
            'MotivoRecusaExame.ativo as ativo'
        );
        $order = 'MotivoRecusaExame.descricao ASC';
        $where = array();

        if(!empty($FILTROS['descricao']))
            $where[] = "MotivoRecusaExame.descricao LIKE '%{$FILTROS['descricao']}%'";
        if(isset($FILTROS['ativo']) && in_array($FILTROS['ativo'], array('0', '1')))
            $where[] = "MotivoRecusaExame.ativo = {$FILTROS['ativo']}";

        if($pagination){
            $paginate = array(
                'fields' => $fields,
                'limit' => (!empty($FILTROS['limit']) ? $FILTROS['limit'] : 50),
                'order' => $order
            );

            if(count($where) > 0)
                $paginate['conditions'] = $where;

            return $paginate;
        }
        else{
            $filters = array('fields' => $fields, 'order' => $order);

            if(count($where) > 0)
                $filters['conditions'] = $where;

            return $this->find('all', $filters);
        }
    }

    public function getAllListActive(){
	    $fields = array('MotivoRecusaExame.codigo', 'MotivoRecusaExame.descricao');
	    $where = array('MotivoRecusaExame.ativo' => 1);
	    $order = 'MotivoRecusaExame.descricao ASC';
	    return $this->find('list', array('fields' => $fields, 'conditions' => $where, 'order' => $order));
    }

    public function incluir(array $data){
	    try{
            return parent::incluir($data['MotivoRecusaExame']);
        }catch(Exception $ex){
	        $this->log('ERROR - Não foi possivel inserir o motivo de recusa exame: ' . $ex->getMessage());
	        return false;
        }
    }

    public function atualizar(array $data){
	    try{
            return $this->save($data);
        }catch(Exception $ex){
            $this->log('ERROR - não foi possivel atualizar o motivo da recusa exame: ' . $ex->getMessage());
            return false;
        }
    }

    public function setStatus($codigo, $status){
	    try{
	        $fields = array('MotivoRecusaExame.*');
	        $where = array('MotivoRecusaExame.codigo' => $codigo);
	        $r = $this->find('first', array('fields' => $fields, 'conditions' => $where));
	        $r['MotivoRecusaExame']['ativo'] = $status;
	        return parent::atualizar($r);
        }catch(Exception $ex){
	        $this->log('ERROR - ao tentar alterar o status do motivo recusa exame: ' . $ex->getMessage());
	        return false;
        }
    }

}