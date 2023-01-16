<?php
class EstatisticaCliente extends AppModel {

	var $name = 'EstatisticaCliente';
	var $tableSchema = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'estatistica_cliente';
	var $primaryKey = 'codigo';
	var $displayField = 'ano_mes';
	var $actsAs = array('Secure');

	function estatistica($filtros) {
		$dados = $this->find('all', array('conditions' => array(
			'or' => array(
				"ano_mes like '".$filtros['Cliente']['ano']."%'",
				"ano_mes like '".--$filtros['Cliente']['ano']."%'"
			)
		)));
		
		return $dados;
	}
	
	function atualizarUltimoMes() {
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->Notafis = ClassRegistry::init('Notafis');
		$dbo = $this->getDataSource();
		$ano_mes = $this->Cliente->find('sql', array('fields' => array('LEFT(CONVERT(VARCHAR, data_inclusao, 120),7) AS mes'), 'recursive' => -1, 'group' => array('LEFT(CONVERT(VARCHAR, data_inclusao, 120),7)')));
		
		$base_select = array(
            'fields' => array(
				"mes"
				, "CONVERT(DATETIME, mes+'-01') AS inicio"
				, "DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0, CONVERT(DATETIME, mes+'-01') )+1,0)) AS fim"
            ),
            'table' => "({$ano_mes})",
            'alias' => 'meses',
            'limit' =>  null,
            'offset' => null,
            'joins' =>  array(),
            'conditions' => null,
            'order' => null,
            'group' => null
        );

        $select_meses = $dbo->buildStatement($base_select, $this->Cliente);

        $with_notas = "WITH CTE_Meses AS (".$select_meses.")";
		
		$cadastros = array(
            'fields' => array(
				"mes"
				, "codigo"
		        , "CASE WHEN LEFT( CONVERT(VARCHAR, ISNULL(data_inativacao,CONVERT(DATETIME, '1950-01-01')), 120) ,7) = meses.mes THEN 1 ELSE 0 END AS nova_inativacao"
		        , "CASE WHEN LEFT( CONVERT(VARCHAR, ISNULL(data_inclusao,CONVERT(DATETIME, '1950-01-01')), 120) ,7) = meses.mes THEN 1 ELSE 0 END AS novo_cadastro"
			    , "data_inclusao"
		        , "data_inativacao"
            ),
            'table' => 'CTE_Meses',
            'alias' => 'meses',
            'limit' =>  null,
            'offset' => null,
            'joins' =>  array(
                array(
                    'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                    'alias' => 'Cliente',
                    'type' => 'LEFT',
                    'conditions' => array('OR' => array(
							'Cliente.data_inclusao BETWEEN meses.inicio AND meses.fim',
							'Cliente.data_inativacao BETWEEN meses.inicio AND meses.fim'
						)
					)
                )
            ),
            'conditions' => null,
            'order' => null,
            'group' => null
        );

		$mes = array(
			'fields' => array(
				'LEFT(CONVERT(VARCHAR, data_inclusao, 120), 7) AS mes'
			),
			'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
            'conditions' => null,
			'alias' => 'cliente',
            'limit' =>  null,
			'order' => null,
			'group' => array("LEFT(CONVERT(VARCHAR, data_inclusao, 120), 7)")
		);
			
		$fiscal = array(
			'fields' => array(
				'mes'
				, 'COUNT(DISTINCT(cliente)) AS ativos'
			),
			'table' => "({$dbo->buildStatement($mes, $this->Cliente)})",
            'conditions' => null,
			'alias' => 'Meses',
			'joins' =>  array(
                array(
                    'table' => "{$this->Notafis->databaseTable}.{$this->Notafis->tableSchema}.{$this->Notafis->useTable}",
                    'alias' => 'Notafis',
                    'type' => 'LEFT',
                    'conditions' => array('Meses.mes = CONVERT(VARCHAR(7), Notafis.dtemissao, 120)')
                )
            ),
            'limit' =>  null,
			'order' => null,
			'group' => array("mes")
		);
					
		$principal = array(
			'fields' => array(
				'cadastros.mes AS mes_corrente'
				, 'SUM(novo_cadastro) AS total_cadastros'
				, 'SUM(nova_inativacao) AS total_inativacoes'
				, 'Fiscal.ativos AS total_ativos'
			),
			'table' => "({$dbo->buildStatement($cadastros, $this->Cliente)})",
            'conditions' => array('cadastros.mes = CONVERT(VARCHAR(7), getdate(), 120)'),
			'alias' => 'cadastros',
			'joins' =>  array(
                array(
                    'table' => "({$dbo->buildStatement($fiscal, $this->Cliente)})",
                    'alias' => 'Fiscal',
                    'type' => 'LEFT',
                    'conditions' => array('Fiscal.mes = cadastros.mes')
                )
            ),
            'limit' =>  null,
			'order' =>  null,
			'group' => array(
				'cadastros.mes'
				, 'Fiscal.ativos'
			)
		);
						
		$ano_mes_atual = date('Y-m');
		$existe_registro = $this->find('first', array('conditions' => array('ano_mes' => $ano_mes_atual)));
		if ($existe_registro)
			return $this->query($with_notas." UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} SET novos_cadastros = total_cadastros, novas_inativacoes = total_inativacoes, saldo = total_ativos FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} INNER JOIN (".$dbo->buildStatement($principal, $this->Cliente).") AS Tmp ON Tmp.mes_corrente = ano_mes WHERE ano_mes = CONVERT(VARCHAR(7), getdate(), 120)");
		else
			return $this->query($with_notas." INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (ano_mes, novos_cadastros, novas_inativacoes, saldo) ".$dbo->buildStatement($principal, $this->Cliente));
	}

}