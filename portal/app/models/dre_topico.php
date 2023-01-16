<?php
class DreTopico extends AppModel {

    var $name = 'DreTopico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'dre_topicos';
    var $primaryKey = 'codigo'	;
    var $displayField = 'descricao';
    
    var $hasMany = array(
        'DreTopicoRegra' => array(
			'class' => 'DreTopicoRegra',
			'foreignKey' => 'codigo_topico'
		)
    );
    
    public function consolidarDespesas($ano, $tipo = 'dre1') {
    	$this->Tranpccrat = ClassRegistry::init('Tranpccrat');
    	$dados = array();
    	
    	$topicos = $this->topicosOrdenadosParaVisualizacao();
    	    	
    	foreach($topicos as $topico){
    		$numero = $topico['DreTopico']['numero'];

    		if($topico['DreTopico']['tipo_topico'] == 1){
	    		$conditions = $this->DreTopicoRegra->montarConditions($topico['DreTopicoRegra'], $ano, $tipo);
	    		if(empty($conditions))
	    			continue;
	    		
                $totais = $this->Tranpccrat->sumarizaAnoMes($conditions, $tipo);
	    		foreach($totais as $total){
	    			$total = $total[0];
	    			
	    		    foreach($topicos as $topico_interno)
        		        if (!isset($dados[$total['Ano']][$total['Mes']][$topico_interno['DreTopico']['numero']]))
        		            $dados[$total['Ano']][$total['Mes']][$topico_interno['DreTopico']['numero']] = array(
        		                'Ano' => $total['Ano'],
                                'Mes' => $total['Mes'],
        		                'Numero' => $topico_interno['DreTopico']['numero'],
        		                'Topico' => '',
        		                'Valor' => 0
        		            );
        		    
		    		$dados[$total['Ano']][$total['Mes']][$numero] = array(
		    				'Ano' => $total['Ano'],
		    				'Mes' => $total['Mes'],
				            'Numero' => $numero,
		    				'Topico' => "{$numero} {$topico['DreTopico']['descricao']}",
		    				'Valor' => $total['Valor']
		    		);
	    		}
    		}
    	}
        
    	foreach($topicos as $topico)
    		if($topico['DreTopico']['tipo_topico'] == 2)
    			for($mes = 1; $mes <= 12; $mes++)
    				if(isset($dados[$ano][$mes]))
    					$dados[$ano][$mes][$topico['DreTopico']['numero']] = $this->montaDadosFormula($topico['DreTopico'], $ano, $mes, $dados);
    	
    	$dados_relatorio = array();
    	foreach($dados as $dados_ano)
	    	foreach($dados_ano as $dados_mes)
		    	foreach($dados_mes as $dado_topico)
    				$dados_relatorio[] = $dado_topico;
    	
    	return $dados_relatorio;
    }
    
    public function topicosOrdenadosParaVisualizacao() {
        return $this->find('all', array('order'=>'ordenacao'));
    }
    
    public function montaDadosFormula($topico, $ano, $mes, $valores) {
    	$valores_mes = Set::combine(array_values($valores[$ano][$mes]), '{n}.Numero', '{n}.Valor');
        
        $resultado = array(
            'Ano' => $ano,
            'Mes' => $mes,
            'Numero' => $topico['numero'],
            'Topico' => $topico['numero'] . ' ' . $topico['descricao'],
            'Valor' => $this->aplicaFormula($topico['formula'], $valores_mes)
        );
        return $resultado;
    }
    
    public function aplicaFormula($formula, $valores) {
        $this->valores = $valores;
        
        $topicosComColchete = array();
        foreach (array_keys($valores) as $topico)
            $topicosComColchete[] = "[{$topico}]";
        $formulaFinal = str_replace($topicosComColchete, $valores, $formula);
        
        return $this->computaFormula($formulaFinal);
    }
    
    public function computaFormula($formula) {
        App::import('Vendor', 'ipsum', array('file' => 'ipsum'.DS.'Parser.class.php'));
        $oldDebug = Configure::read('debug');
        Configure::write('debug', 0);
        $oldErrorReporting = error_reporting(E_ALL ^ E_NOTICE);
        try {
        	$parser = new Parser($formula);
            $result = $parser->run();
        } catch (Exception $e) {
            $result = $e->getMessage();
        }
        error_reporting($oldErrorReporting);
        Configure::write('debug', $oldDebug);
        
        return $result;
    }
    
    public function atualizaOrdenacao($dre_topico_codigo, $posicao_nova = null){
    	$posicao_atual = $this->field('ordenacao', array('codigo'=>$dre_topico_codigo));
    	$posicao_maxima = $this->find('count');
    	
    	if(empty($posicao_atual))
    		$posicao_atual = $posicao_maxima;
    	
    	if(empty($posicao_nova) || $posicao_nova > $posicao_maxima)
    		$posicao_nova = $posicao_maxima;
    	
    	if($posicao_atual > $posicao_nova){
    		$this->updateAll(
    				array('ordenacao'=>'ordenacao+1'),
    				array(
    						array('ordenacao >='=>$posicao_nova),
    						array('ordenacao <'=>$posicao_atual)
    				)
    		);
    	} else {
    		$this->updateAll(
    				array('ordenacao'=>'ordenacao-1'),
    				array(
    						array('ordenacao <='=>$posicao_nova),
    						array('ordenacao >'=>$posicao_atual)
    				)
    		);
    	}
    	$this->updateAll(array('ordenacao'=>$posicao_nova), array('codigo'=>$dre_topico_codigo));
    }
    
}