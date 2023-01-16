<?php
class Tpecas extends AppModel {
	var $name = 'Tpecas';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'tpecas';
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
    	'numero_peca' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Número da Peça',
            'required' => true
    	),
    	'dn' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o DN',
            'required' => true
    	),
    	'tipo_caixa' => array(
            'rule' => 'notEmpty',
            'message' => 'Tipo da caixa',
            'required' => true
    	),
    	'tipo_caixa_avaria' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo de Avaria da Caixa',
            'required' => true
    	),
    	'tipo_peca' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo da Peça',
            'required' => true
    	),
    	'tipo_peca_avaria' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo de Avaria da Peça',
            'required' => true
    	),
    	'destino' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Destino',
            'required' => true
    	),
    	'data' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Data',
            'required' => true
    	),
	);


	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {		
		if( isset($extra['extra']['tpecas_analitico']) && $extra['extra']['tpecas_analitico'] )
			$dados = $this->listagem_analitico($conditions, $limit, $page, $order);
	    return $dados;
	}
	public function importar($pasta, $arquivo){
		$conteudo = file_get_contents($pasta.DS.$arquivo,'r');		

		$codigo_cliente = substr(str_replace(array('Pecas','Peca'), '', $arquivo),0,-16);
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
				if(is_array($colunas) && count($colunas)==11){	
					$data = null;
					if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', trim($colunas[10])))			
						$data = !empty($colunas[10]) ? implode('',array_reverse(explode("/", trim($colunas[10])))) : date('Ymd H:m:s');
						$dados = array('Tpecas' => array(
								'filename'          => $arquivo,
								'local'             => trim($colunas[0]),
								'dn'                => trim($colunas[1]),
								'transportador'     => trim($colunas[2]),
								'numero_peca'       => trim($colunas[3]),
								'tipo_caixa'        => trim($colunas[4]),
								'tipo_caixa_avaria' => trim($colunas[5]),
								'tipo_peca'         => trim($colunas[6]),
								'tipo_peca_avaria'  => trim($colunas[7]),
								'destino'           => trim($colunas[8]),
								'filename_pic'      => trim($colunas[9]),
								'data'              => $data,
								'codigo_cliente'    => $codigo_cliente,
								'data_inclusao'     => date('Ymd H:m:s')
							)
						);
					if($this->incluir($dados))					
						$this->mover_arquivo($pasta, $colunas[9]);
					// else
					// 	echo "erro incluir peca ".$arquivo." linha: ".$numero."\n";
				}
				// else{
				// 	echo "erro incluir peca ".$arquivo." linha: ".$numero."\n";
				// }
			}
		}
		return true;		
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
				'local'            => 'Local da vistoria', 
				'tipo_peca'        => 'Tipo de peça', 
				'tipo_peca_avaria' => 'Tipo de avaria',
				'transportador'    => 'Transportadora',
				'peca_avaria'      => 'Peça / Avaria',
				'peca_total'       => 'Peça / Total'
			);
	}

	public function pesquisaTpecas($filtros){
		$query = "SELECT 
				   {$filtros['agrupamento']} as agrupamento,
				    SUM(com_avaria) as com_avaria,
				    (SUM(com_avaria)*100 / COUNT(1)) as p_com_avaria,
				    SUM(sem_avaria) as sem_avaria,
				    (SUM(sem_avaria)*100 / COUNT(1)) as p_sem_avaria,
				    COUNT(1) as total
				  FROM (SELECT
				    	codigo, filename, local, dn, transportador, numero_peca
				    	tipo_caixa, tipo_caixa_avaria, tipo_peca, tipo_peca_avaria,
				    	destino, filename_pic, data, codigo_cliente, data_inclusao,
				    CASE WHEN tipo_peca_avaria like '%Sem Av%' THEN 0 ELSE 1 END as com_avaria,
				    CASE WHEN tipo_peca_avaria not like '%Sem Av%' THEN 0 ELSE 1 END as sem_avaria
				    FROM tpecas) as pecas
				   WHERE 1=1 ";		

		$conditions = array();

		if(!empty($filtros['codigo_cliente']))
			$query .= ' AND codigo_cliente = ' . $filtros['codigo_cliente'];
		if(!empty($filtros['local']))
			$query .= ' AND local = \'' . $filtros['local'].'\'';
		if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial']))
        	$query .= ' AND data >= \'' . date('Ymd 00:00:00', Comum::dateToTimestamp($filtros['data_inicial'])).'\'';      
      	if(isset($filtros['data_final']) && !empty($filtros['data_final']))
       	 	$query .= ' AND data <= \'' . date('Ymd 23:59:59', Comum::dateToTimestamp($filtros['data_final'])).'\'';      

       	if($filtros['agrupamento']=='tipo_peca_avaria'){
       	 	$query .= ' AND tipo_peca_avaria not like \'%Sem Av%\'';
       	}

       	$query .= " GROUP BY {$filtros['agrupamento']}";
       	
		$resultado = $this->query($query);		
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
	public function pesquisaPecaAvaria($tipo, $filtros, $grafico=true){		
		$conditions = array();
		if(!empty($filtros['codigo_cliente']))
			$conditions['Tpecas.codigo_cliente'] = $filtros['codigo_cliente'];
		if(!empty($filtros['local']))
			$conditions['Tpecas.local'] = $filtros['local'];
		if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial']))
        	$conditions['Tpecas.data >='] = date('Ymd 00:00:00', Comum::dateToTimestamp($filtros['data_inicial']));      
      	if(isset($filtros['data_final']) && !empty($filtros['data_final']))
       	 	$conditions['Tpecas.data <='] =  date('Ymd 23:59:59', Comum::dateToTimestamp($filtros['data_final']));      
       	
       	if(!empty($filtros['agrupamento']) && $filtros['agrupamento'] == 'peca_avaria'){
       		$conditions['Tpecas.tipo_peca_avaria not like'] = '%Sem Av%';
       	}

		$busca = 'Tpecas.tipo_peca_avaria';

       	$group  = array(
			   		'Tpecas.tipo_peca', 
			   		$busca
				);
       	$fields = array(
       				'Tpecas.tipo_peca as peca',
       				$busca.' as avaria' ,
       				'SUM(1) as total'
       			);
       	$order  = array('Tpecas.tipo_peca');       	
		$linhas = $this->find('all', 
				compact('conditions', 'fields', 'order', 'group')
			);

		$retorno = array();		
		$total = 0;
		if($tipo=='total'){
			foreach($linhas as $key => $linha){
				$linha = $linha[0];
				if(!isset($retorno[$linha['peca']]))
					$retorno[$linha['peca']] = array(
							'Sem Avaria'   => 0,
							'Sem Avaria %' => 0, 
							'Com Avaria'   => 0,
							'Avaria %'     => 0, 	
						);
				if((strpos($linha['avaria'], 'Sem Av')!==false) || $linha['avaria']== ' ')					
					$retorno[$linha['peca']]['Sem Avaria'] += $linha['total'];
				else
					$retorno[$linha['peca']]['Com Avaria'] += $linha['total'];
				$total += $linha['total'];
			}		
		}else{
			foreach($linhas as $linha){
				$linha = $linha[0];
				$retorno[$linha['peca']][$linha['avaria']] = $linha['total'];
				$total += $linha['total'];
			}		
		}
		if(!$grafico){
			foreach($retorno as $peca => $valor){
				if($tipo=='total'){
					$total_sem = $retorno[$peca]['Sem Avaria'];
					$total_com = $retorno[$peca]['Com Avaria'];
					$retorno[$peca]['Sem Avaria %'] = round((100*$total_sem) / ($total_sem + $total_com));
					$retorno[$peca]['Avaria %']     = round((100*$total_com) / ($total_sem + $total_com));
				}
			}				
		}
		$retorno['total'] = $total;
		return $retorno;
	}	
}
?>