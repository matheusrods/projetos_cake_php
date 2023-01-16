<?php
class TarefaDesenvolvimento extends AppModel {
	var $name = 'TarefaDesenvolvimento';
	var $useDbConfig = 'dbProducao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tarefas_desenvolvimento';
	var $primaryKey = 'codigo';
	var $displayField = 'codigo_usuario_inclusao';
	var $actsAs = array('Secure');
	var $validate = array(
		'titulo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o titulo da tarefa',
		),
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe uma descrição',
		),
		'codigo_usuario_inclusao' => array(
			'rule' => 'notEmpty',
			'message' => 'O usuário da tarefa não foi informado',
		),
		'status' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o status da tarefa',
		),
		'tipo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o tipo da tarefa',
		),
	);

	public function listarTarefasDesenvolvimento($filtros) {

		$Usuarios               	= ClassRegistry::init('Usuario');
		$TarefaDesenvolvimentoTipo  = ClassRegistry::init('TarefaDesenvolvimentoTipo');

        $joins = array(
            array(
                'table' => "{$Usuarios->databaseTable}.{$Usuarios->tableSchema}.{$Usuarios->useTable}",
                'alias' => 'Usuario',
                'conditions' => 'usuario.codigo = TarefaDesenvolvimento.codigo_usuario_inclusao',
            	'type' => 'INNER'
            ),
            array(
                'table' => "{$TarefaDesenvolvimentoTipo->databaseTable}.{$TarefaDesenvolvimentoTipo->tableSchema}.{$TarefaDesenvolvimentoTipo->useTable}",
                'alias' => 'TarefaDesenvolvimentoTipo',
                'conditions' => 'TarefaDesenvolvimentoTipo.codigo = TarefaDesenvolvimento.tipo',
            	'type' => 'INNER'
            ),
       	);
		  
		$fields = array(
			'Usuario.codigo' ,
			'Usuario.apelido',
			'Usuario.nome',
			'TarefaDesenvolvimento.codigo',	
			'TarefaDesenvolvimento.titulo',
			'TarefaDesenvolvimento.data_inclusao',
			'TarefaDesenvolvimento.data_alteracao',
			'TarefaDesenvolvimento.data_publicacao',
			'TarefaDesenvolvimento.status',
			'TarefaDesenvolvimento.descricao',
			'TarefaDesenvolvimentoTipo.codigo',
			'TarefaDesenvolvimentoTipo.descricao'
		);

		$conditions = array();
		if (isset($filtros['codigo_usuario_inclusao']) && !empty($filtros['codigo_usuario_inclusao']))
			$conditions[$this->name.'.codigo_usuario_inclusao'] = $filtros['codigo_usuario_inclusao'];
		
		if (isset($filtros['status']) && !empty($filtros['status']))
			$conditions[$this->name.'.status'] = $filtros['status'];

		if ($filtros['data_inicial']){
			$filtros['data_inicial'] 	= AppModel::dateToDbDate2($filtros['data_inicial']).' 00:00:00';
			$conditions[$this->name.'.data_inclusao >='] = $filtros['data_inicial'];
		}

		if ($filtros['data_final']){
			$filtros['data_final'] 		= AppModel::dateToDbDate2($filtros['data_final']).' 23:59:59';
			$conditions[$this->name.'.data_inclusao <='] = $filtros['data_final'];
		}

		if (isset($filtros['tipo']) && !empty($filtros['tipo']))
			$conditions[$this->name.'.tipo'] = $filtros['tipo'];
		
		$limit 		= 50;	
		$order		= array('TarefaDesenvolvimento.data_inclusao DESC');

		if ($this->useDbConfig == 'test_suite') {
            return $this->find('all',compact('conditions','joins','fields'));
        }

		return  compact('conditions','joins', 'fields','limit','order');	
	}

	public function contarTarefaDesenvolvimento($filtros){
		$Usuarios               = ClassRegistry::init('Usuario');  

        $joins = array(
            array(
                'table' => "{$Usuarios->databaseTable}.{$Usuarios->tableSchema}.{$Usuarios->useTable}",
                'alias' => 'Usuario',
                'conditions' => 'usuario.codigo = TarefaDesenvolvimento.codigo_usuario_inclusao',
            	'type' => 'INNER'
            ),
       	);
		 
		$conditions = array();
		if (isset($filtros['codigo_usuario_inclusao']) && !empty($filtros['codigo_usuario_inclusao']))
			$conditions[$this->name.'.codigo_usuario_inclusao'] = $filtros['codigo_usuario_inclusao'];
		
		if (isset($filtros['status']) && !empty($filtros['status']))
			$conditions[$this->name.'.status'] = $filtros['status'];

		if ($filtros['data_inicial'] && $filtros['data_final']){
			$conditions[$this->name.'.data_inclusao BETWEEN ? AND ?'] = array(
				$filtros['data_inicial'],
				$filtros['data_final']
			);
		}

		if (isset($filtros['tipo']) && !empty($filtros['tipo']))
			$conditions[$this->name.'.tipo'] = $filtros['tipo'];

		return $this->find('count',compact('conditions','joins'));
	}

	public function listarTarefasRelatorio() {

		$Usuarios               = ClassRegistry::init('Usuario');  

        $joins = array(
            array(
                'table' => "{$Usuarios->databaseTable}.{$Usuarios->tableSchema}.{$Usuarios->useTable}",
                'alias' => 'Usuario',
                'conditions' => 'usuario.codigo = TarefaDesenvolvimento.codigo_usuario_inclusao',
            	'type' => 'INNER'
            ),
       	);

       	$fields = array(
			'Usuario.codigo' ,
			'Usuario.apelido',
			'Usuario.nome',
			'TarefaDesenvolvimento.codigo',	
			'TarefaDesenvolvimento.titulo',
			'TarefaDesenvolvimento.data_inclusao',
			'TarefaDesenvolvimento.data_alteracao',
			'TarefaDesenvolvimento.data_publicacao',
			'TarefaDesenvolvimento.status',
			'TarefaDesenvolvimento.descricao'
		);

		$conditions = array($this->name.'.status' => 2, $this->name.'.tipo' => 1);
		$order 		= array($this->name.'.codigo_usuario_inclusao');

		return $this->find('all', compact('conditions','joins', 'order','fields'));	
	}
}

?>