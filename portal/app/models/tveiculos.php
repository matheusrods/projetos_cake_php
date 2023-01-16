<?php
class Tveiculos extends AppModel {
	var $name = 'Tveiculos';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'tveiculos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
        'codigo_cliente' => array(            
            'rule' => 'notEmpty',
            'message' => 'Informe o Cliente', 
            'required' => true
    	),
    	'local' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Local',
            'required' => true
    	),
    	'entrada_saida' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Entrada/Saída',
            'required' => true
    	),
    	'transportador' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Transportador',
            'required' => true
    	),
    	'chassi' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Chassi',
            'required' => true
    	),
    	'veiculo_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo do Veículo',
            'required' => true
    	),
    	'veiculo_cor' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Cor do Veículo',
            'required' => true
    	),
    	'avaria_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo da Avaria',
            'required' => true
    	),
    	'data' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Data',
            'required' => true
    	),
	);

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {				
		if( !empty($extra['extra']['tveiculos_analitico']) && $extra['extra']['tveiculos_analitico'] ){
			$dados = $this->listagem_analitico($conditions, $limit, $page, $order);					
		}
	    return $dados;
	}

	public function importar($pasta, $arquivo){

		$conteudo = file_get_contents($pasta.DS.$arquivo,'r');
		$codigo_cliente = substr(str_replace(array('Veiculos','Veiculo'), '', $arquivo),0,-16);

		if(empty($codigo_cliente)){
			$codigo_cliente = 29289;
		}

		$linhas = explode("\n", $conteudo);	
		array_shift($linhas);							
		foreach ($linhas as $numero => $linha) {		
			if(!empty($linha)){		
				$colunas = explode(";", $linha);
				if(count($colunas) == 1){
					$colunas = explode(",", $linha);
				}
				if(is_array($colunas) && count($colunas)==12){
					$data = null;
					
					if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', trim($colunas[11]))){						
						$data = !empty($colunas[11]) ? implode('',array_reverse(explode("/", trim($colunas[11])))) : date('Ymd H:m:s');
					}

					$dados = array('Tveiculos' => 
							array(
								'filename'       => $arquivo,
								'local'          => trim($colunas[0]),
								'entrada_saida'  => trim($colunas[1]),
								'transportador'  => trim($colunas[2]),
								'chassi'         => trim($colunas[3]),
								'veiculo_tipo'   => trim($colunas[4]),
								'veiculo_cor'    => trim($colunas[5]),
								'avaria_tipo'    => trim($colunas[6]),
								'avaria_local'   => trim($colunas[7]),
								'fronte'         => trim($colunas[8]),
								'lateral'        => trim($colunas[9]),
								'filename_pic'   => trim($colunas[10]),
								'data'           => $data,
								'codigo_cliente' => $codigo_cliente,
								'data_inclusao'  => date('Ymd H:m:s')
							)
						);					
					if($this->verifica_existe($dados['Tveiculos']['chassi'], $dados['Tveiculos']['local']) == 0)
						if($this->incluir($dados))
							$this->mover_arquivo($pasta, $colunas[10]);				
				}
			};
		}		
		return true;
	}

	private function verifica_existe($chassi, $local){
		return $this->find('count', array('conditions' => array(
					'Tveiculos.chassi' => $chassi, 
					'Tveiculos.local' => $local
				)));
	}

	public function mover_arquivo($pasta,$arquivo){		
		if(!empty($arquivo) && $arquivo!='Sem Foto'){
			$origem = $pasta.DS.$arquivo;				
			$destino = $pasta.DS.'..'.DS.'processado'.DS.$arquivo;		
			if(file_exists($origem)){
				if(copy($origem, $destino)){
					unlink($origem);		
					return true;
				}
			}
			// else
			// 	echo "erro ao copiar arquivo\n";
		}
		return false;
	}

	public function listaAgrupamento(){
		return array(
				'local'          => 'Local da Vistoria', 
				'transportador'  => 'Transportadora', 
				'veiculo_tipo'   => 'Tipo de Veículo', 
				'avaria_tipo'    => 'Tipo de Avaria', 
				'avaria_local'   => 'Local Avaria',
				'veiculo_avaria' => 'Veic. / Avaria',
	            'veiculo_local'  => 'Veic. / Local Avar.',
	            'veiculo_total'  => 'Veic. / Total'
			);
	}

	public function pesquisaTveiculos($filtros, $limit = null, $page = null, $order = null, $total = false, $grafico = false){

		$dbo = $this->getDataSource();
		$fields = array(
				'codigo','filename','local','entrada_saida','transportador','chassi',
			    'veiculo_tipo','veiculo_cor','fronte','lateral','filename_pic',
			    'data','codigo_cliente','data_inclusao',
			    "CASE WHEN avaria_tipo like '%Sem Av%' THEN 0 ELSE 1 END as com_avaria",
			    "CASE WHEN avaria_tipo not like '%Sem Av%' THEN 0 ELSE 1 END as sem_avaria",
			    'avaria_tipo',
			    'avaria_local'
			);

		$subselect = $dbo->buildStatement(
   			array(
   				'table'      => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
				'alias'      => 'Tveiculos',
				'fields'     => $fields,
				'conditions' => null,
				'order'      => null,
				'limit'      => null,
				'group'      => null,
				'page'       => $page
				), 
   			$this);


		$fields = array( 
					"{$filtros['agrupamento']} as agrupamento",
					"SUM(com_avaria) as com_avaria",
					"(SUM(com_avaria)*100 / COUNT(1)) as p_com_avaria",
					"SUM(sem_avaria) as sem_avaria",
					"(SUM(sem_avaria)*100 / COUNT(1)) as p_sem_avaria",
					"COUNT(1) as total",
					"count(distinct(chassi)) as veiculos"
					);		

		$conditions = array();
		if(!empty($filtros['codigo_cliente']))
			$conditions['codigo_cliente'] = $filtros['codigo_cliente'];
		if(!empty($filtros['chassi']))
			$conditions['chassi'] = $filtros['chassi'];
		if(!empty($filtros['local']))
			$conditions['local'] = $filtros['local'];
		if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial']))
        	$conditions['data >='] = date('Ymd 00:00:00', Comum::dateToTimestamp($filtros['data_inicial']));
      	if(isset($filtros['data_final']) && !empty($filtros['data_final']))
       	 	$conditions['data <='] = date('Ymd 23:59:59', Comum::dateToTimestamp($filtros['data_final']));
      	if(($filtros['agrupamento'] == 'avaria_tipo' || $filtros['agrupamento'] == 'avaria_local') && !$total)
      		$conditions['avaria_tipo not like'] = '%Sem Av%';
      	if(($filtros['agrupamento'] == 'local' || $filtros['agrupamento'] == 'transportador' || $filtros['agrupamento'] == 'veiculo_tipo') && $grafico)
      		$conditions['avaria_tipo not like'] = '%Sem Av%';

		$query = $dbo->buildStatement(
			array(
				'table'      => '('.$subselect.')',
				'alias'      => 'veiculos',
				'fields'     => $fields,
				'conditions' => $conditions,
				'order'      => $order,
				'limit'      => null,
				'group'      => array($filtros['agrupamento']) 
				),
			$this);

       	$resultado = $this->query($query); 

       	if($filtros['agrupamento'] == 'avaria_tipo' || $filtros['agrupamento'] == 'avaria_local'){
       		$total = 0;
       		foreach($resultado as $result){
       			$result = $result[0];
       			$total += $result['com_avaria'];
       		}
       		foreach($resultado as $key => $result){
       			$resultado[$key][0]['percentual'] = number_format((100*$resultado[$key][0]['com_avaria'] / $total), 0,',','.');
       		}
       	}
		return $resultado;
	}

	public function listagem_analitico($conditions, $limit = null, $page = null, $order = null){		
		$retorno = $this->find('all',
				array(
					'conditions' => $conditions,
					'order'      => $order,
					'limit'      => $limit,
					'page'       => $page,
				)
			);					
		return $retorno;
	}

	public function pesquisaVeiculoAvaria($tipo, $filtros, $grafico = false){		
		$conditions = array();
		$conditions_sub = array();

		$dbo = $this->getDataSource();

		if(!empty($filtros['codigo_cliente'])){
			$conditions['Tveiculos.codigo_cliente'] = $filtros['codigo_cliente'];
			$conditions_sub['veic.codigo_cliente'] = $filtros['codigo_cliente'];
		}
		if(!empty($filtros['chassi'])){
			$conditions['Tveiculos.chassi'] = $filtros['chassi'];
			$conditions_sub['veic.chassi'] = $filtros['chassi'];
		}
		if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])){
        	$conditions['Tveiculos.data >='] = date('Ymd 00:00:00', Comum::dateToTimestamp($filtros['data_inicial']));
        	$conditions_sub['veic.data >='] = date('Ymd 00:00:00', Comum::dateToTimestamp($filtros['data_inicial']));
		}
      	if(isset($filtros['data_final']) && !empty($filtros['data_final'])){
       	 	$conditions['Tveiculos.data <='] =  date('Ymd 23:59:59', Comum::dateToTimestamp($filtros['data_final']));
       	 	$conditions_sub['veic.data <='] = date('Ymd 23:59:59', Comum::dateToTimestamp($filtros['data_final']));
      	}
       	
		if(!empty($filtros['local'])){
			$conditions['Tveiculos.local'] = $filtros['local'];
			$conditions_sub['veic.local'] = $filtros['local'];
		}

		if($tipo == 'tipo'){
			$busca = 'Tveiculos.avaria_tipo';
			$conditions['Tveiculos.avaria_tipo not like'] = '%Sem Av%';
			$conditions_sub['veic.avaria_tipo not like'] = '%Sem Av%';
		}else if($tipo == 'local'){
			$busca = 'Tveiculos.avaria_local';
			$conditions['Tveiculos.avaria_tipo not like'] = '%Sem Av%';
			$conditions_sub['veic.avaria_tipo not like'] = '%Sem Av%';
		}else{			
			$busca = 'Tveiculos.avaria_tipo';
		}    

		$group  = array(
		   		'Tveiculos.veiculo_tipo', 
		   		$busca
			);
		$order  = array('Tveiculos.veiculo_tipo');
		$fields = array('Tveiculos.veiculo_tipo AS veiculo');
		$conditions_sub[] = 'veic.veiculo_tipo = Tveiculos.veiculo_tipo';

       	$subselect = $dbo->buildStatement(
   			array(
				'table'      => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
				'alias'      => 'veic',
				'fields'     => array('COUNT(distinct(veic.chassi)) as total'),
				'conditions' => $conditions_sub,
				'order'      => null,
				'limit'      => null,
				'group'      => null
			), $this
   		);

       	$fields[] = $busca.' AS avaria';
   		$fields[] = 'SUM(1) AS total';
   		$fields[] = '('.$subselect.') AS veiculos';       	
       	       	
		$linhas = $this->find('all', 
				compact('conditions', 'fields', 'order', 'group')
			);
		
		$retorno = array();
		$total = 0;
		if($tipo=='total' || $tipo == 'local_vistoria' || $tipo == 'transportadora'){
			foreach($linhas as $key => $linha){
				$linha = $linha[0];
				if(!isset($retorno[$linha['veiculo']]))
					$retorno[$linha['veiculo']] = array(
							'Avaria'       => 0, 
							'Avaria %'     => 0, 				
							'Sem Avaria'   => 0, 
							'Sem Avaria %' => 0, 
							'veiculos'     => 0,
						);
				
				if((strpos($linha['avaria'], 'Sem Av')!==false) || $linha['avaria']== ' ')					
					$retorno[$linha['veiculo']]['Sem Avaria'] += $linha['total'];
				else
					$retorno[$linha['veiculo']]['Avaria'] += $linha['total'];
				
				if(!$grafico)
					$retorno[$linha['veiculo']]['veiculos'] = $linha['veiculos'];
				
				$total += $linha['total'];
			}			
		}else{
			foreach($linhas as $linha){
				$linha = $linha[0];
				$retorno[$linha['veiculo']][$linha['avaria']] = $linha['total'];
				if(!$grafico)
					$retorno[$linha['veiculo']]['veiculos'] = $linha['veiculos'];
				$total += $linha['total'];
			}
		}
		if(!$grafico){
			foreach($retorno as $veiculo => $valor){
				if($tipo=='total'){
					$total_sem = $retorno[$veiculo]['Sem Avaria'];
					$total_com = $retorno[$veiculo]['Avaria'];
					$retorno[$veiculo]['Sem Avaria %'] = round((100*$total_sem) / ($total_sem + $total_com));
					$retorno[$veiculo]['Avaria %']     = round((100*$total_com) / ($total_sem + $total_com));
				}
			}				
		}
		$retorno['total'] = $total;
		return $retorno;
	}
}
?>