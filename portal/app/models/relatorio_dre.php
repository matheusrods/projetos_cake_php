<?php
class RelatorioDre extends AppModel {
    var $name 			= 'RelatorioDre';
    var $useTable 		= false;

    public function consolidar($ano = '', $cliente = null, $produto = null) {
        $this->Notafis = ClassRegistry::init('Notafis');
        $this->Notafis->bindLazyNotaiteClienteProduto();
        
        $fields = array('Notafis.cancela', 'SUM(Notaite.preco * Notaite.qtde) as ReceitaOperacionalBruta'
            , 'SUM(Notaite.desconto) as DescontosIncondicionais', 'SUM(Notaite.vliss) as ISS'
            , 'SUM(Notaite.vlpis) as PIS', 'SUM(Notaite.vlcofins) as COFINS');
            
        $group = array('Notafis.cancela');
        
        $conditions = array('Notafis.empresa' => '03');
        
        if (!empty($ano)) {
            $fields[] = 'MONTH(Notafis.dtemissao) as Mes';
            $fields[] = 'YEAR(Notafis.dtemissao) as Ano';
            $group[] = 'MONTH(Notafis.dtemissao)';
            $group[] = 'YEAR(Notafis.dtemissao)';
            $conditions['YEAR(Notafis.dtemissao)'] = $ano;
        }
        
        if (!empty($cliente))
            $conditions['Notafis.cliente'] = $cliente;
        
        if (!empty($produto))
            $conditions['Notaite.produto'] = $produto;
            
        $dadosBrutos = $this->Notafis->find('all', array(
            'fields' => $fields,
            'group' => $group,
            'conditions' => $conditions,
        	'order' => 'Notafis.cancela'
        ));
        
        $dadosAgrupados = $this->agrupaVendasAnuladas($dadosBrutos);
        $dadosOrganizados = $this->organizarChavesReceitasETotalizarReceitaLiquida($dadosAgrupados);
        return $dadosOrganizados;
    }
    
    public function agrupaVendasAnuladas($dados) {
        $agrupado = array();
        foreach($dados as $dado) {
            $key = isset($dado[0]['Ano']) ? $dado[0]['Ano'].$dado[0]['Mes'] : 0;
            if($dado['Notafis']['cancela'] == 'N')
                $agrupado[$key] = $dado[0];
            else
                $agrupado[$key]['VendasAnuladas'] = $dado['0']['ReceitaOperacionalBruta'];
        }
        return $agrupado;
    }
    
    public function organizarChavesReceitasETotalizarReceitaLiquida($dados) {
        $dadosOrganizados = array();
        
        foreach($dados as $dado) {
            if(isset($dado['Ano'])) {
                $ano = $dado['Ano'];
                $mes = $dado['Mes'];
                $key = $ano.$mes;
                $valores_agrupamento = array('Ano'=>$ano, 'Mes'=>$mes);
            } else {
                $key = 0;
                $valores_agrupamento = array();
            }
            
            $receitaOperacionalLiquida = 0;
            
            foreach($dado as $tipo => $valor)
                if(!in_array($tipo, array_keys($valores_agrupamento))) {
                    $dadosOrganizados[] = array_merge($valores_agrupamento, array('Tipo' => $tipo, 'Valor' => $valor));
                    $receitaOperacionalLiquida += $tipo == 'ReceitaOperacionalBruta' ? $valor : -$valor;
                }
            
            $dadosOrganizados[] = array_merge($valores_agrupamento, array('Tipo' => 'ReceitaOperacionalLiquida', 'Valor' => round($receitaOperacionalLiquida, 2)));
        }
        
        return $dadosOrganizados;
    }
    
    public function consolidarDespesas($ano) {
        $this->Tranpag = ClassRegistry::init('Tranpag');
        $this->Tranpag->bindLazyClassificacoes();
        
        $fields = array(
              'Grflux.descricao'
            , 'Sbflux.descricao'
        	, 'Grflux.codigo'
        	, 'Sbflux.codigo'
            , 'SUM(Tranpcc.valor) AS valor'
        	, 'MONTH(Tranpag.dtemiss) AS mes'
    		, 'YEAR(Tranpag.dtemiss) AS ano'
        );
        
        $group = array('Grflux.codigo', 'Grflux.descricao', 'Sbflux.codigo', 'Sbflux.descricao' , 'MONTH(Tranpag.dtemiss)', 'YEAR(Tranpag.dtemiss)');
        
        $conditions = array('YEAR(Tranpag.dtemiss)' => $ano);
        
        $resultado = $this->Tranpag->find('all', array(
            'fields' => $fields,
            'group' => $group,
            'conditions' => $conditions,
        	'order' => 'cast(Grflux.codigo as int), cast(Sbflux.codigo as int)'
        ));
        $dadosOrganizados = $this->organizarChavesDespesas($resultado);

        return $dadosOrganizados;
    }
    
    public function organizarChavesDespesas($dados) {
    	$dadosOrganizados = array();
    	
    	foreach($dados as $dado){
    		$dadosOrganizados[] = array(
    			'Ano' => $dado[0]['ano'],
    			'Mes' => $dado[0]['mes'],
    			'Grupo' => $dado['Grflux']['codigo'].' '.$dado['Grflux']['descricao'],
    			'Subgrupo' => $dado['Grflux']['codigo'].'.'.$dado['Sbflux']['codigo'].' '.$dado['Sbflux']['descricao'],
    			'Valor' => $dado[0]['valor']
    		);
    	}
    	
    	return $dadosOrganizados;
    }

}