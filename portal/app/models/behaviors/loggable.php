<?php
class LoggableBehavior extends ModelBehavior {
	var $foreign_key;
	var $indices;

	function setup(&$model, $config = array()) {
		$this->foreign_key = $config['foreign_key'];
		$this->indices[$model->name] = $config;
	}

	function afterSave(&$model, $created) {
		$acao_sistema = ($created ? 0 : 1);
		$log = $model->carregar($model->id);

		if($this->indices[$model->name]) {
			$this->foreign_key = $this->indices[$model->name]['foreign_key'];
		}

		if($model->name=='ConfiguracaoComissaoCorre'){
			$ModelLogger = ClassRegistry::init('ConfigComissaoCorreLog');
			$log = array($ModelLogger->name => $log[$model->name]); 
		}else{
			$ModelLogger = ClassRegistry::init($model->name.'Log');
			$log = array($ModelLogger->name => $log[$model->name]);
		}		

		if(isset($ModelLogger->foreignKeyLog)) {
			$log[$ModelLogger->name][$ModelLogger->foreignKeyLog] = $model->id;
		}else{
			if($model->name == "ClienteFuncionario"){
				$log[$ModelLogger->name]['codigo_cliente_funcionario'] = $model->id;
			}elseif($model->name == "Cnae"){
				$log[$ModelLogger->name]['codigo_cnae'] = $model->id;
			}else{
				$log[$ModelLogger->name][$this->foreign_key] = $model->id;
			}
		}

		$log[$ModelLogger->name]['acao_sistema'] = $acao_sistema;
		
		unset($log[$ModelLogger->name][(isset($ModelLogger->primaryKey) ? $ModelLogger->primaryKey : 'id')]);
		if(!$ModelLogger->incluir($log))
			throw new Exception();
		if($model->name == 'FichaScorecard')
			$this->salvarDadosRelacionadosFichaScorecard($model->id, $ModelLogger->id);
		if($model->name == 'PedidoExame')
			$this->salvarDadosRelacionadosPedidoExame($model->id, $ModelLogger->id);
	}
	
	function beforeDelete(&$model) {
		$acao_sistema = 2;
		$model->read(null, $model->id);
		if($this->indices[$model->name]) {
			$this->foreign_key = $this->indices[$model->name]['foreign_key'];
		}

		if($model->name=='ConfiguracaoComissaoCorre'){
			$ModelLogger = ClassRegistry::init('ConfigComissaoCorreLog');
		}else{
			$ModelLogger = ClassRegistry::init($model->name.'Log');
		}
		$log = array($ModelLogger->name => $model->data[$model->name]);

		if (isset($ModelLogger->foreignKeyLog)) {
			$log[$ModelLogger->name][$ModelLogger->foreignKeyLog] = $model->id;
		} else {
			$log[$ModelLogger->name][$this->foreign_key] = $model->id;
		}

		$log[$ModelLogger->name]['acao_sistema'] = $acao_sistema;
		
		if(isset($_SESSION['Auth']['Usuario']['codigo'])) {
			$log[$ModelLogger->name]['codigo_usuario_alteracao'] = $_SESSION['Auth']['Usuario']['codigo'];
		}
		
		unset($log[$ModelLogger->name][(isset($model->primaryKey) ? $model->primaryKey : 'id')]);
		if (!$ModelLogger->incluir($log)){
			throw new Exception();
		}
	}

	function salvarDadosRelacionadosFichaScorecard($codigo_ficha, $codigo_ficha_log) {
		$statusCriterios = ClassRegistry::init('FichaStatusCriterio')->buscarPorFicha($codigo_ficha);
		$data = array();
		foreach ($statusCriterios as $statusCriterio) {
			$statusCriterio = $statusCriterio['FichaStatusCriterio'];
			unset($statusCriterio['codigo']);
			unset($statusCriterio['codigo_ficha']);
			$statusCriterio['codigo_ficha_log'] = $codigo_ficha_log;
			$data[] = $statusCriterio;
		}
		ClassRegistry::init('FichaStatusCriterioLog')->saveAll($data);
	}

	public function salvarDadosRelacionadosPedidoExame($codigo_pedido_exame, $codigo_pedido_exame_log) 
	{
		$pedidoNotificacao = ClassRegistry::init('PedidoExameNotificacao')->buscarNotificacao($codigo_pedido_exame,$codigo_pedido_exame_log);
	}//fim salvarDadosRelacionadosPedidoExame($model->id, $ModelLogger->id);
}
?>