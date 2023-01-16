<?php
class Notaite extends AppModel {

    var $name = 'Notaite';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'notaite';
    var $primaryKey = 'nnotafis';
    var $actsAs = array('Secure');
    var $belongsTo = array(
        'Notafis' => array(
            'className' => 'Notafis',
            'foreignKey' => 'nnotafis',
            'conditions' => array("Notafis.empresa = Notaite.empresa AND Notafis.seq = Notaite.seq AND Notafis.serie = Notaite.serie")
        ),
    );

    function bindLazy() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Notafis' => array(
                    'className' => 'Notafis',
                    'foreignKey' => 'nnotafis',
                    'conditions' => array("Notafis.empresa = Notaite.empresa AND Notafis.seq = Notaite.seq AND Notafis.serie = Notaite.serie AND Notafis.cancela = 'N'")
                ),
                'Tranrec' => array(
                    'classname' => 'Tranrec',
                    'foreignKey' => 'nnotafis'
                )
                ))
        );
    }

    function unbind() {
        $this->unbindModel(array(
            'BelongsTo' => array(
                'Notafis', 'Tranrec',
            )
        ));
    }

    function buscaNotasFiscaisNaveg($anoMes, $cliente, $produto) {
        $this->Produto = & ClassRegistry::init('Produto');
        $this->bindLazy();
        $mes = substr($anoMes, 4, 2);
        $ano = substr($anoMes, 0, 4);
        $produtoNaveg = $this->Produto->produtoBuonnyNaveg($produto);

        $condicoes = array('month(Notafis.dtemissao)' => $mes,
            'year(Notafis.dtemissao)' => $ano,
            'convert(int,Notaite.cliente)' => Comum::StrZero($cliente, 10),
            'Notaite.produto' => $produtoNaveg);

        return $this->find('first', array('fields' => array('Notaite.cliente',
                        'Notaite.produto',
                        'Notafis.vlnota',
                        'Notafis.numero',
                        'Notafis.dtemissao'),
                    'conditions' => $condicoes));
    }

    function itensPorNotaFiscal($nota_fiscal,$codigo_empresa_naveg) {
        return $this->find('all', array('conditions' => array('nnotafis' => str_pad($nota_fiscal, 6, "0", STR_PAD_LEFT), 'Notaite.empresa' => $codigo_empresa_naveg)));
    }

    function _total($conditions, $joins = null) {

        $options = array(
            'conditions' => $conditions,
            'fields' => array('sum(Notaite.preco * Notaite.qtde) as total'),
        );

        if (!empty($joins)) {
            $options['joins'] = $joins;
        }

        $total = $this->find('first', $options);
        $total = isset($total['0']['total']) ? $total['0']['total'] : 0;
        return $total;
    }

    function produtoPorCorretora( $filtros ) {
        $this->recursive = -1;
        $conditions = $this->converteFiltroEmConditions($filtros['Notaite']);
        if (isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora']))
            $conditions['Cliente.codigo_corretora'] = $filtros['codigo_corretora'];

        $this->Notafis = & ClassRegistry::init('Notafis');
        $this->NProduto = & ClassRegistry::init('NProduto');
        $this->Cliente = & ClassRegistry::init('Cliente');
        $this->Corretora =  ClassRegistry::init('Corretora');

        if ($this->useDbConfig == 'test_suite') {
            $this->NProduto->useTable = 'n_produto';
        }

        $joins = array(
            array(
                'table' => "{$this->Notafis->databaseTable}.{$this->Notafis->tableSchema}.{$this->Notafis->useTable}",
                'alias' => 'Notafis',
                'conditions' => 'Notafis.numero = Notaite.nnotafis',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                'alias' => 'Cliente',
                'conditions' => 'Cliente.codigo = Notafis.cliente',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->Corretora->databaseTable}.{$this->Corretora->tableSchema}.{$this->Corretora->useTable}",
                'alias' => 'Corretora',
                'conditions' => 'Corretora.codigo = Cliente.codigo_corretora',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->NProduto->databaseTable}.{$this->NProduto->tableSchema}.{$this->NProduto->useTable}",
                'alias' => 'NProduto',
                'conditions' => 'Notaite.produto = NProduto.codigo',
                'type'  => 'left',
            )
        );

        $total = $this->_total($conditions, $joins);

        $result = $this->find( 'all', array(
            'recursive' => -1,
            'fields' => array(
                'NProduto.descricao',
                'Notaite.produto',
                'SUM(Notaite.preco * Notaite.qtde) AS total',
                'ROW_NUMBER() OVER( ORDER BY SUM(Notafis.vlmerc) DESC ) AS registro',
                "(ROUND(SUM( (Notaite.preco * Notaite.qtde) / ({$total} * 100) ),4)) AS participacao"
            ),
            'conditions' => $conditions,
            'joins' => $joins,
            'group' => array('NProduto.descricao', 'Notaite.produto')
        ));
        return $result;
    }

	function comparaFaturamento($filtros = null) {
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->Notafis = ClassRegistry::init('Notafis');
		$dbo = $this->getDataSource();

		$conditions = $this->converteFiltroEmConditions($filtros);
		$fields = array(
			'Cliente.codigo AS codigo'
			, 'Cliente.razao_social AS razao_social'
			, 'Cliente.codigo_gestor AS codigo_gestor'
			, 'Notaite.produto AS produto'
			, 'Notaite.preco * qtde AS total_nota'
			, 'LEFT(CONVERT(VARCHAR, Notaite.dtemissao, 120), 7) AS ano_mes'
		);
		$joins = array(
            array(
                'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                'alias' => 'Cliente',
                'conditions' => 'Cliente.codigo = cliente',
                'type'  => 'inner',
            ),
            array(
                'table' => "{$this->Notafis->databaseTable}.{$this->Notafis->tableSchema}.{$this->Notafis->useTable}",
                'alias' => 'Notafis',
                'conditions' => array('Notafis.numero = Notaite.nnotafis', 'cancela' => 'N'),
                'type'  => 'inner',
            )
		);

		$recursive = -1;
		$base_select = $this->find('sql', compact('conditions', 'joins', 'fields', 'recursive'));
		$fields = array(
			"codigo"
			, "razao_social"
            , "SUM(
					CASE ano_mes
						WHEN LEFT('{$conditions['OR'][0]['Notaite.dtemissao BETWEEN ? AND ?'][0]}', 7)
						THEN total_nota
						ELSE 0
					END
				) AS ano_mes_um"
            , "SUM(
					CASE ano_mes
						WHEN LEFT('{$conditions['OR'][1]['Notaite.dtemissao BETWEEN ? AND ?'][0]}', 7)
						THEN total_nota
						ELSE 0
                    END
				) AS ano_mes_dois"
		);

		$sub_select = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => "({$base_select})",
                'alias' => "Sub",
                'limit' => null,
                'offset' => null,
                'joins' => array(),
                'conditions' => array(),
                'order' => array(),
                'group' => array('Sub.codigo','Sub.razao_social'),
			), $this
        );

		$fields = array(
			'codigo'
			,'razao_social'
			,'ano_mes_um'
			,'ano_mes_dois'
			,'(ano_mes_um - ano_mes_dois) AS diferenca'
			,'(
				CASE ano_mes_um
					WHEN 0
					THEN 100
					ELSE CONVERT(INT, (((ano_mes_dois - ano_mes_um) / isnull(nullif(ano_mes_um, 0), 1) * 100)))
				END
			) AS variacao'
		);

		$order = $filtros['sinal_variacao'] == 1 ? array('diferenca ASC'): array('diferenca DESC');
		$condicional_de_variacao = $filtros['sinal_variacao'] == 1 ? "> {$filtros['variacao']}": "< ({$filtros['variacao']} * -1)";
		$conditions = array("((ano_mes_dois - ano_mes_um) / isnull(nullif(ano_mes_um, 0), 1) * 100) {$condicional_de_variacao}");

		$select = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => "({$sub_select})",
                'alias' => "Tmp",
                'limit' => null,
                'offset' => null,
                'joins' => array(),
                'conditions' => $conditions,
                'order' => $order,
                'group' => array(),
			), $this
        );

		return $this->query($select);
	}

    function produtoPorSeguradora( $filtros ) {
        $this->recursive = -1;
        $conditions = $this->converteFiltroEmConditions($filtros['Notaite']);
        if (isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora']))
            $conditions['Cliente.codigo_seguradora'] = $filtros['codigo_seguradora'];

        $this->Notafis = & ClassRegistry::init('Notafis');
        $this->NProduto = & ClassRegistry::init('NProduto');
        $this->Cliente = & ClassRegistry::init('Cliente');
        $this->Seguradora  = & ClassRegistry::init('Seguradora');

        if ($this->useDbConfig == 'test_suite') {
            $this->NProduto->useTable = 'n_produto';
        }

        $joins = array(
            array(
                'table' => "{$this->Notafis->databaseTable}.{$this->Notafis->tableSchema}.{$this->Notafis->useTable}",
                'alias' => 'Notafis',
                'conditions' => 'Notafis.numero = Notaite.nnotafis',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                'alias' => 'Cliente',
                'conditions' => 'Cliente.codigo = Notafis.cliente',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->Seguradora->databaseTable}.{$this->Seguradora->tableSchema}.{$this->Seguradora->useTable}",
                'alias' => 'Seguradora',
                'conditions' => 'Seguradora.codigo = Cliente.codigo_seguradora',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->NProduto->databaseTable}.{$this->NProduto->tableSchema}.{$this->NProduto->useTable}",
                'alias' => 'NProduto',
                'conditions' => 'Notaite.produto = NProduto.codigo',
                'type'  => 'left',
            )
        );

        $total = $this->_total($conditions, $joins);

        $result = $this->find( 'all', array(
            'recursive' => -1,
            'fields' => array(
                'NProduto.descricao',
                'Notaite.produto',
                'SUM(Notaite.preco * Notaite.qtde) AS total',
                'ROW_NUMBER() OVER( ORDER BY SUM(Notafis.vlmerc) DESC ) AS registro',
                "(SUM(Notaite.preco * Notaite.qtde) / {$total} * 100) AS participacao"
            ),
            'conditions' => $conditions,
            'joins' => $joins,
            'group' => array('NProduto.descricao', 'Notaite.produto')
        ));

        return $result;
    }

    function produtoPorGestor( $filtros ) {
        $this->recursive = -1;
        $conditions = $this->converteFiltroEmConditions($filtros['Notaite']);
        if (isset($filtros['codigo_gestor']) && !empty($filtros['codigo_gestor']))
            $conditions['Cliente.codigo_gestor'] = $filtros['codigo_gestor'];

        $this->Notafis = & ClassRegistry::init('Notafis');
        $this->NProduto = & ClassRegistry::init('NProduto');
        $this->Cliente = & ClassRegistry::init('Cliente');
        $this->Gestor  = & ClassRegistry::init('Gestor');

        if ($this->useDbConfig == 'test_suite') {
            $this->NProduto->useTable = 'n_produto';
        }

        $joins = array(
            array(
                'table' => "{$this->Notafis->databaseTable}.{$this->Notafis->tableSchema}.{$this->Notafis->useTable}",
                'alias' => 'Notafis',
                'conditions' => 'Notafis.numero = Notaite.nnotafis',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                'alias' => 'Cliente',
                'conditions' => 'Cliente.codigo = Notafis.cliente',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->Gestor->databaseTable}.{$this->Gestor->tableSchema}.{$this->Gestor->useTable}",
                'alias' => 'Gestor',
                'conditions' => 'Gestor.codigo = Cliente.codigo_gestor',
                'type'  => 'left',
            ),
            array(
                'table' => "{$this->NProduto->databaseTable}.{$this->NProduto->tableSchema}.{$this->NProduto->useTable}",
                'alias' => 'NProduto',
                'conditions' => 'Notaite.produto = NProduto.codigo',
                'type'  => 'left',
            )
        );

        $total = $this->_total($conditions, $joins);

        $result = $this->find( 'all', array(
            'recursive' => -1,
            'fields' => array(
                'NProduto.descricao',
                'Notaite.produto',
                'SUM(Notaite.preco * Notaite.qtde) AS total',
                'ROW_NUMBER() OVER( ORDER BY SUM(Notafis.vlmerc) DESC ) AS registro',
                "(SUM(Notaite.preco * Notaite.qtde) / {$total} * 100) AS participacao"
            ),
            'conditions' => $conditions,
            'joins' => $joins,
            'group' => array('NProduto.descricao', 'Notaite.produto')
        ));

        return $result;
    }


    function porProduto($filtros) {
        $conditions = $this->converteFiltroEmConditions($filtros['Notaite']);
        if ($this->useDbConfig != 'test_suite') {
            if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
            }
            if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
            }
            if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_SOLEN) {
                $this->databaseTable = 'dbNavegarqSolen';
                $this->Notafis->databaseTable = 'dbNavegarqSolen';
            }
        }

        $total = $this->_total($conditions);
        $this->bindModel(array('belongsTo' => array(
            'NProduto' => array('className' => 'NProduto', 'foreignKey' => 'produto'),
        )));
        if ($this->useDbConfig == 'test_suite') {
            $this->NProduto->setSource('n_produto');
        } elseif ($this->useDbConfig != 'test_suite') {
            if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->NProduto->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
            }
            if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->NProduto->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
            }
            if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_SOLEN) {
                $this->databaseTable = 'dbNavegarqSolen';
                $this->NProduto->databaseTable = 'dbNavegarqSolen';
                $this->Notafis->databaseTable = 'dbNavegarqSolen';
            }
        }
        $group = array('Notaite.produto', 'NProduto.descricao');
        $fields = array_merge($group, array('ROW_NUMBER() OVER (order by sum(Notaite.preco * Notaite.qtde) desc) as registro', 'sum(Notaite.preco * Notaite.qtde) as total', "(sum(Notaite.preco * Notaite.qtde) / {$total} * 100) as participacao"));
        $order = array('sum(Notaite.preco * Notaite.qtde) desc');
        $dados = $this->find('all', compact('conditions', 'fields', 'group', 'order'));
        if ($this->useDbConfig != 'test_suite') {
            $this->databaseTable = 'dbNavegarqNatec';
            $this->NProduto->databaseTable = 'dbNavegarqNatec';
            $this->Notafis->databaseTable = 'dbNavegarqNatec';
        }
        return $dados;
    }

    function merge_ano_mes($meses, $faturamento) {
        foreach($meses as $mes){
            if($faturamento[0]['preco'] != 0){
                $mes = (int)substr($faturamento[0]['ano_mes'],0,2);
                $mes--;
                $meses[$mes][0]['preco'] = $faturamento[0]['preco'];
            }
        }
        return $meses;
    }

    function faturamentoAnual($filtros) {
        $conditions = $this->converteFiltroEmConditions($filtros['Notaite']);

        $fields = array('substring(convert(varchar,Notaite.dtemissao, 103), 4, 7) as ano_mes', 'SUM(Notaite.preco * Notaite.qtde) as preco',);
        $group = array('substring(convert(varchar,Notaite.dtemissao, 103), 4, 7)');
        $order = array('substring(convert(varchar,Notaite.dtemissao, 103), 4, 7)');
        $this->bindModel(array('belongsTo' => array(
                'NProduto' => array('className' => 'NProduto', 'foreignKey' => 'produto'),
                'Notafis' => array('foreignKey' => 'nnotafis', 'conditions' => 'Notafis.empresa = Notaite.empresa AND Notafis.seq = Notaite.seq AND Notafis.serie = Notaite.serie'),
            )));
        if ($this->useDbConfig == 'test_suite')
            $this->NProduto->setSource('n_produto');
        elseif ($this->useDbConfig != 'test_suite') {
            if (isset($filtros['Notaite']['grupo_empresa'])) {
                if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                    $this->databaseTable = 'dbNavegarqLider';
                    $this->NProduto->databaseTable = 'dbNavegarqLider';
                    $this->Notafis->databaseTable = 'dbNavegarqLider';
                }
                if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                    $this->databaseTable = 'dbNavegarqNatec';
                    $this->NProduto->databaseTable = 'dbNavegarqNatec';
                    $this->Notafis->databaseTable = 'dbNavegarqNatec';
                }
                if ($filtros['Notaite']['grupo_empresa'] == LojaNaveg::GRUPO_SOLEN) {
                    $this->databaseTable = 'dbNavegarqSolen';
                    $this->NProduto->databaseTable = 'dbNavegarqSolen';
                    $this->Notafis->databaseTable = 'dbNavegarqSolen';
                }
            }
        }

        $faturamentos = $this->find('all', compact('conditions', 'fields', 'group', 'order'));

        if ($this->useDbConfig != 'test_suite') {
            $this->databaseTable = 'dbNavegarqNatec';
            $this->NProduto->databaseTable = 'dbNavegarqNatec';
            $this->Notafis->databaseTable = 'dbNavegarqNatec';
        }
        $meses = array();
        for($i=1; $i<=12; $i++) {
            array_push($meses, array(array('ano_mes' => $i.'/'.$filtros['Notaite']['ano'],'preco' => 0)));
        }

        foreach($faturamentos as $faturamento)
            $meses = $this->merge_ano_mes($meses, $faturamento);

        return $meses;
    }

    function converteFiltroEmConditions($filtros) {
		if (isset($filtros['variacao'])) {

			$dt_inicial = strtotime($filtros['ano_inicial'].'-'.$filtros['mes_inicial'].'-01');
			$dt_final   = strtotime($filtros['ano_final'].'-'.$filtros['mes_final'].'-01');

			if ($dt_inicial > $dt_final) {
				$filtros['data_inicial'] = Comum::periodoMensal($filtros['ano_final'].'-'.$filtros['mes_final']);
				$filtros['data_final']   = Comum::periodoMensal($filtros['ano_inicial'].'-'.$filtros['mes_inicial']);
			} else {
				$filtros['data_inicial'] = Comum::periodoMensal($filtros['ano_inicial'].'-'.$filtros['mes_inicial']);
				$filtros['data_final']   = Comum::periodoMensal($filtros['ano_final'].'-'.$filtros['mes_final']);
			}

			$conditions = array(
				'OR' => array(
					array('Notaite.dtemissao BETWEEN ? AND ?' => array($filtros['data_inicial']['inicio'], $filtros['data_inicial']['fim'])),
					array('Notaite.dtemissao BETWEEN ? AND ?' => array($filtros['data_final']['inicio'], $filtros['data_final']['fim']))
				),
			);

			if (isset($filtros['codigo_gestor']) && !empty($filtros['codigo_gestor']))
				$conditions['Cliente.codigo_gestor'] = $filtros['codigo_gestor'];
			if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
				$conditions['Notaite.cliente'] = str_pad($filtros['codigo_cliente'], 10,'0', STR_PAD_LEFT);
			if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto']))
				$conditions['Notaite.produto'] = $filtros['codigo_produto'];

			return $conditions;
		}

        if (isset($filtros['data_inicial'])) {
            $filtros['data_inicial'] = AppModel::dateToDbDate2($filtros['data_inicial']).' 00:00:00';
            $filtros['data_final'] = AppModel::dateToDbDate2($filtros['data_final']).' 23:59:59';
        } else {
            if (isset($filtros['mes'])) {
                $filtros['data_inicial'] = date("Y-m-d 00:00:00", strtotime("{$filtros['ano']}-{$filtros['mes']}-01 00:00:00"));
                $filtros['data_final'] = date("Y-m-t 23:59:59", strtotime("{$filtros['ano']}-{$filtros['mes']}-01 23:59:59"));
            } else {
                $filtros['data_inicial'] = $filtros['ano'].'-01-01 00:00:00';
                $filtros['data_final'] = $filtros['ano'].'-12-31 23:59:59';
            }
        }
        $conditions = array('Notafis.cancela' => 'N', 'Notaite.dtemissao BETWEEN ? AND ?' => array($filtros['data_inicial'], $filtros['data_final']));
        if (isset($filtros['empresa']) && !empty($filtros['empresa'])) {
            $conditions['Notaite.empresa'] = $filtros['empresa'];
        } else {
            $conditions['Notaite.empresa'] = array('17','18','19','20','21','22');
        }
        if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto']))
            $conditions['Notaite.produto'] = $filtros['codigo_produto'];
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
            if (is_array($filtros['codigo_cliente'])) {
                foreach ($filtros['codigo_cliente'] as $key => $codigo_cliente) {
                    $filtros['codigo_cliente'][$key] = str_pad($codigo_cliente, 10,'0', STR_PAD_LEFT);
                }
            } else {
                $filtros['codigo_cliente'] = str_pad($filtros['codigo_cliente'], 10,'0', STR_PAD_LEFT);
            }
            $conditions['Notaite.cliente'] = $filtros['codigo_cliente'];
        }

        if( isset($filtros['codigo_gestor']) && !empty($filtros['codigo_gestor']) ||
            isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora']) ||
            isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora']) ) {

            if (isset($filtros['codigo_gestor'])) {
                $conditions['Cliente.codigo_gestor'] = $filtros['codigo_gestor'];
            } else if (isset($filtros['codigo_seguradora'])) {
                $conditions['Cliente.codigo_seguradora'] = $filtros['codigo_seguradora'];
            } else if (isset($filtros['codigo_corretora'])) {
                $conditions['Cliente.codigo_corretora'] = $filtros['codigo_corretora'];
            }

            $this->bindModel(array('belongsTo' => array(
                'Cliente' => array('className' => 'Cliente', 'foreignKey' => 'cliente'),
            )));
        }
        return $conditions;
    }

    function porLoja($filtros, $grupo_empresa = 1) {
        $conditions = $this->converteFiltroEmConditions($filtros['Notaite']);

        if ($this->useDbConfig != 'test_suite') {
            if ($grupo_empresa == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
            }
            if ($grupo_empresa == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
            }
            if ($grupo_empresa == LojaNaveg::GRUPO_SOLEN) {
                $this->databaseTable = 'dbNavegarqSolen';
                $this->Notafis->databaseTable = 'dbNavegarqSolen';
            }
        }
        $total = $this->_total($conditions);

        $this->bindModel(array('belongsTo' => array('LojaNaveg' => array('className' => 'LojaNaveg', 'foreignKey' => 'empresa'))));
        if ($this->useDbConfig != 'test_suite') {
            if ($grupo_empresa == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->LojaNaveg->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
            }
            if ($grupo_empresa == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->LojaNaveg->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
            }
            if ($grupo_empresa == LojaNaveg::GRUPO_SOLEN) {
                $this->databaseTable = 'dbNavegarqSolen';
                $this->LojaNaveg->databaseTable = 'dbNavegarqSolen';
                $this->Notafis->databaseTable = 'dbNavegarqSolen';
            }

        }
        $group = array('Notaite.empresa', 'LojaNaveg.razaosocia');
        $fields = array_merge($group, array('ROW_NUMBER() OVER (order by sum(Notaite.preco * Notaite.qtde) desc) as registro', 'sum(Notaite.preco * Notaite.qtde) as total', "(sum(Notaite.preco * Notaite.qtde) / {$total} * 100) as participacao"));
        $order = array('sum(Notaite.preco * Notaite.qtde) desc');
        $conditions['Notafis.tipocli']='C';
        $dados = $this->find('all', compact('conditions', 'fields', 'group', 'order'));
        if ($this->useDbConfig != 'test_suite') {
            $this->databaseTable = 'dbNavegarqNatec';
            $this->LojaNaveg->databaseTable = 'dbNavegarqNatec';
            $this->Notafis->databaseTable = 'dbNavegarqNatec';
        }
        return $dados;
    }

    function produtosFaturadosPorCliente($codigo_cliente) {
        $conditions = null;
        if (!empty($codigo_cliente)) {
            if (is_array($codigo_cliente)) {
                foreach ($codigo_cliente as $key => $valor) {
                    $codigo_cliente[$key] = str_pad($valor, 10,'0', STR_PAD_LEFT);
                }
            } else {
                $codigo_cliente = str_pad($codigo_cliente, 10,'0', STR_PAD_LEFT);
            }
            $conditions = array('Notaite.cliente' => $codigo_cliente);
        }
        $result = $this->find('all', array('fields' => 'produto', 'group' => 'produto', 'conditions' => $conditions));
        if ($result) {
            $result = Set::extract('/Notaite/produto', $result);
            foreach ($result as $key => $valor)
                $result[$key] = (string)$valor;
            return $result;
        } else
            return false;
    }

    function produtosFaturadosPorSeguradoraCorretora($codigo_seguradora, $codigo_corretora) {
        $conditions = array();
        if (!empty($codigo_seguradora)) {
            $conditions['Cliente.codigo_seguradora'] = $codigo_seguradora;
        }
        if (!empty($codigo_corretora)) {
            $conditions['Cliente.codigo_corretora'] = $codigo_corretora;
        }
        $this->bindModel(array('belongsTo' => array(
            'Cliente' => array('className' => 'Cliente', 'foreignKey' => 'cliente'),
        )));
        $result = $this->find('all', array('fields' => 'produto', 'group' => 'produto', 'conditions' => $conditions));
        if ($result) {
            $result = Set::extract('/Notaite/produto', $result);
            foreach ($result as $key => $valor)
                $result[$key] = (string)$valor;
            return $result;
        } else
            return false;
    }

    function totalRankingFaturamento($conditions) {
        $this->bindRanking();
        $fields = array("SUM(Notaite.preco * Notaite.qtde) AS total");
        $conditions['Notafis.tipocli']='C';
        $result = $this->find('first', compact('conditions', 'fields'));
        if ($result) {
            return $result[0]['total'];
        }
        return 0;
    }

    function rankingFaturamentoConditions($filtros) {

        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->LojaNaveg->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
        }
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->LojaNaveg->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
        }
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_SOLEN) {
                $this->databaseTable = 'dbNavegarqSolen';
                $this->LojaNaveg->databaseTable = 'dbNavegarqSolen';
                $this->Notafis->databaseTable = 'dbNavegarqSolen';
        }
        $conditions = array(
            'Notafis.cancela' => 'N'
        );

        if (isset($filtros['data_inicial']) and isset($filtros['data_final'])){
            $data_ini = explode('/',$filtros['data_inicial']);
            $data_fim = explode('/',$filtros['data_final']);
            $conditions['Notafis.dtemissao >='] = date("Y-m-d 00:00:00", strtotime("{$data_ini[2]}-{$data_ini[1]}-01 00:00:00"));
          $conditions['Notafis.dtemissao <='] = date("Y-m-t 23:59:59", strtotime("{$filtros['ano']}-{$filtros['mes']}-01 23:59:59"));
        }else{
          $conditions['Notafis.dtemissao >='] = date("Y-m-d 00:00:00", strtotime("{$filtros['ano']}-{$filtros['mes']}-01 00:00:00"));
          $conditions['Notafis.dtemissao <='] = date("Y-m-t 23:59:59", strtotime("{$filtros['ano']}-{$filtros['mes']}-01 23:59:59"));
        }
        if (isset($filtros['level']) && $filtros['level'] > 0) {
            if (isset($filtros['empresa']) && !empty($filtros['empresa'])) {
                $conditions["Notafis.empresa"] = $filtros['empresa'];
            }
            if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
                $conditions["Cliente.codigo"] = $filtros['codigo_cliente'];
            }
            if (isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])) {
                $conditions["Cliente.codigo_seguradora"] = $filtros['codigo_seguradora'];
            }
            if (isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora'])) {
                $conditions["Cliente.codigo_corretora"] = $filtros['codigo_corretora'];
            }
            if (isset($filtros['codigo_filial']) && !empty($filtros['codigo_filial'])) {
                $conditions["Cliente.codigo_endereco_regiao"] = $filtros['codigo_filial'];
            }
            if (isset($filtros['codigo_gestor']) && !empty($filtros['codigo_gestor'])) {
                $conditions["Cliente.codigo_gestor"] = $filtros['codigo_gestor'];
            }
            if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto'])) {
                $conditions["Notaite.produto"] = $filtros['codigo_produto'];
            }
            if (isset($filtros['codigo_grupo_economico']) && !empty($filtros['codigo_grupo_economico'])) {
                $conditions["GrupoEconomico.codigo"] = $filtros['codigo_grupo_economico'];
            }
        }
        return $conditions;
    }

    function rankingFaturamentoFilterColumn($filtros) {
        $filterColumn = "CASE WHEN 1=1";
        if (isset($filtros['empresa']) && !empty($filtros['empresa'])) {
            $filterColumn .= " AND Notafis.empresa IN (".(is_array($filtros['empresa']) ? implode(',', $filtros['empresa']) : $filtros['empresa']).")";
        }
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
            $filterColumn .= " AND Cliente.codigo IN (".(is_array($filtros['codigo_cliente']) ? implode(',', $filtros['codigo_cliente']) : $filtros['codigo_cliente']).")";
        }
        if (isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])) {
            $filterColumn .= " AND Cliente.codigo_seguradora IN (".(is_array($filtros['codigo_seguradora']) ? implode(',', $filtros['codigo_seguradora']) : $filtros['codigo_seguradora']).")";
        }
        if (isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora'])) {
            $filterColumn .= " AND Cliente.codigo_corretora IN (".(is_array($filtros['codigo_corretora']) ? implode(',', $filtros['codigo_corretora']) : $filtros['codigo_corretora']).")";
        }
        if (isset($filtros['codigo_filial']) && !empty($filtros['codigo_filial'])) {
            $filterColumn .= " AND Cliente.codigo_endereco_regiao IN (".(is_array($filtros['codigo_filial']) ? implode(',', $filtros['codigo_filial']) : $filtros['codigo_filial']).")";
        }
        if (isset($filtros['codigo_gestor']) && !empty($filtros['codigo_gestor'])) {
            $filterColumn .= " AND Cliente.codigo_gestor IN (".(is_array($filtros['codigo_gestor']) ? implode(',', $filtros['codigo_gestor']) : $filtros['codigo_gestor']).")";
        }
        if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto'])) {
            $filterColumn .= " AND Notaite.produto IN ('".(is_array($filtros['codigo_produto']) ? implode(',', $filtros['codigo_produto']) : $filtros['codigo_produto'])."')";
        }
        if (isset($filtros['codigo_grupo_economico']) && !empty($filtros['codigo_grupo_economico'])) {
            $filterColumn .= " AND GrupoEconomico.codigo IN (".(is_array($filtros['codigo_grupo_economico']) ? implode(',', $filtros['codigo_grupo_economico']) : $filtros['codigo_grupo_economico']).")";
        }
        $filterColumn .= " THEN 1 ELSE 0 END";
        return $filterColumn;
    }

    const AGRP_CLIENTES = 1;
    const AGRP_CORRETORAS = 2;
    const AGRP_GRUPOS_ECONOMICOS = 3;
    const AGRP_PRODUTOS = 4;
    const AGRP_SEGURADORAS = 5;
    const AGRP_GESTORES = 6;

    function listarAgrupamentos() {
        return array(self::AGRP_CLIENTES => 'Clientes', self::AGRP_CORRETORAS => 'Corretoras', self::AGRP_GESTORES => 'Gestores', self::AGRP_GRUPOS_ECONOMICOS => 'Grp.Econômicos', self::AGRP_PRODUTOS => 'Produtos', self::AGRP_SEGURADORAS => 'Seguradoras');
    }

    function rankingFaturamento($type, $options) {
        extract($options);
        $filtros = $conditions;

        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->LojaNaveg->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
                $this->NProduto->databaseTable = 'dbNavegarqLider';
        }
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->LojaNaveg->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
                $this->NProduto->databaseTable = 'dbNavegarqNatec';
        }
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_SOLEN) {
                $this->databaseTable = 'dbNavegarqSolen';
                $this->Notafis->databaseTable = 'dbNavegarqSolen';
                $this->LojaNaveg->databaseTable = 'dbNavegarqSolen';
                $this->NProduto->databaseTable = 'dbNavegarqSolen';

        }
        $conditions = $this->rankingFaturamentoConditions($filtros);
        $totalRankingFaturamento = $this->totalRankingFaturamento($conditions);
        $this->bindRanking();
        if (!empty($totalRankingFaturamento)) {
            $filterColumn = $this->rankingFaturamentoFilterColumn($filtros);
            $fields = array(
                "ROW_NUMBER() OVER ( ORDER BY SUM(Notaite.preco * Notaite.qtde) DESC) AS registro",
                "SUM(Notaite.preco * Notaite.qtde) AS vlmerc",
                "ROUND(SUM(Notaite.preco * Notaite.qtde) / {$totalRankingFaturamento} * 100, 4) AS participacao",
                "({$filterColumn}) AS filtro",
            );
            if (strlen($filterColumn) > 31) {
                $group = array($filterColumn);
            } else {
                $group = array();
            }
            if ($filtros['agrupamento'] == self::AGRP_CLIENTES) {
                $group = array_merge($group, array('Cliente.codigo', 'Cliente.razao_social'));
                $fields = array_merge($fields,
                    array('Cliente.codigo AS codigo', 'Cliente.razao_social AS descricao')
                );
            }
            if ($filtros['agrupamento'] == self::AGRP_CORRETORAS) {
                $group = array_merge($group, array('Corretora.codigo', 'Corretora.nome'));
                $fields = array_merge($fields,
                    array('Corretora.codigo AS codigo', 'Corretora.nome AS descricao')
                );
            }
            if ($filtros['agrupamento'] == self::AGRP_GRUPOS_ECONOMICOS) {
                $group = array_merge($group, array("ISNULL(CONVERT(VARCHAR, GrupoEconomico.codigo), 'C'+CONVERT(VARCHAR, Cliente.codigo))", 'ISNULL(GrupoEconomico.descricao, Cliente.razao_social)'));
                $fields = array_merge($fields,
                    array("ISNULL(CONVERT(VARCHAR, GrupoEconomico.codigo), 'C'+CONVERT(VARCHAR,Cliente.codigo)) AS codigo", 'ISNULL(GrupoEconomico.descricao, Cliente.razao_social) AS descricao')
                );
            }
            if ($filtros['agrupamento'] == self::AGRP_PRODUTOS) {
                $group = array_merge($group, array('NProduto.codigo', 'NProduto.descricao'));
                $fields = array_merge($fields,
                    array('NProduto.codigo AS codigo', 'NProduto.descricao AS descricao')
                );
            }
            if ($filtros['agrupamento'] == self::AGRP_SEGURADORAS) {
                $group = array_merge($group, array('Seguradora.codigo', 'Seguradora.nome'));
                $fields = array_merge($fields,
                    array('Seguradora.codigo AS codigo', 'Seguradora.nome AS descricao')
                );
            }
            if ($filtros['agrupamento'] == self::AGRP_GESTORES) {
                $group = array_merge($group, array('Usuario.codigo', 'Usuario.nome'));
                $fields = array_merge($fields,
                    array('Usuario.codigo AS codigo', 'Usuario.nome AS descricao')
                );
            }
            $sql_cte = 'WITH CTE_Base AS ('.$this->find('sql', compact('conditions', 'fields', 'group')).')';
            $field_acumulado = "(SELECT SUM(ROUND(vlmerc / {$totalRankingFaturamento} * 100,4)) FROM CTE_Base AS CTE_A WHERE CTE_A.registro <= CTE_Base.registro) AS acumulado";
            if ($type == 'count') {
                $query = $sql_cte."SELECT COUNT(1/1) AS contagem FROM CTE_Base WHERE filtro = 1";
                $result = $this->query($query);
                return $result[0][0]['contagem'];
            }

            $query = "SELECT ".(isset($limit) ? "TOP {$limit}" : "")." registro, vlmerc, participacao, codigo, descricao, {$field_acumulado} FROM CTE_Base WHERE filtro = 1".(isset($page) && $page > 1? " AND registro >= ".($page - 1) * ($limit + 1) : "")." ORDER BY registro";
            $query = $sql_cte.$query;
            return $this->query($query);
        }
        return null;
    }

    private function bindRanking() {
        $this->bindModel(array('belongsTo' => array(
            'Notafis' => array('foreignKey' => false, 'conditions' => array(
                'Notafis.empresa = Notaite.empresa',
                'Notafis.numero = Notaite.nnotafis',
                'Notafis.seq = Notaite.seq',
                'Notafis.serie = Notaite.serie',
            )),
            'Cliente' => array('foreignKey' => 'cliente'),
            'NCliente' => array('foreignKey' => 'cliente'),
            'Seguradora' => array('foreignKey' => false, 'conditions' => array('Cliente.codigo_seguradora = Seguradora.codigo')),
            'Corretora' => array('foreignKey' => false, 'conditions' => array('Cliente.codigo_corretora = Corretora.codigo')),
            'Usuario' => array('foreignKey' => false, 'conditions' => array('Cliente.codigo_gestor = Usuario.codigo')),
            'NProduto' => array('foreignKey' => 'produto'),
            'GrupoEconomicoCliente' => array('foreignKey' => false, 'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'),
            'GrupoEconomico' => array('foreignKey' => false, 'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'),
        )));
    }

    function baseComparativoAnual($filtros) {
        $this->bindRanking();
        $group = array("DATEPART(mm, Notafis.dtemissao)");
        $fields = array(
            "DATEPART(mm, Notafis.dtemissao) AS mes",
            "SUM(Notaite.preco * Notaite.qtde) AS valor",
        );
        $conditions = $this->baseComparativoAnualConditions($filtros);
        $conditions['Notafis.cancela'] = 'N';
        return $this->find('sql', compact('conditions', 'fields', 'group'));
    }

    private function baseComparativoAnualConditions($filtros) {

        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->LojaNaveg->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
                $this->NProduto->databaseTable = 'dbNavegarqLider';
        }
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->LojaNaveg->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
                $this->NProduto->databaseTable = 'dbNavegarqNatec';
        }
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_SOLEN) {
                $this->databaseTable = 'dbNavegarqSolen';
                $this->Notaite->dataTable = 'dbNavegarqSolen';
                $this->Notafis->databaseTable = 'dbNavegarqSolen';
                $this->LojaNaveg->databaseTable = 'dbNavegarqSolen';
                $this->NProduto->databaseTable = 'dbNavegarqSolen';

        }

        $conditions = array(
            'Notafis.dtemissao BETWEEN ? AND ?' => array($filtros['ano'].'-01-01 00:00:00', $filtros['ano'].'-12-31 23:59:59'),
        );
        if (isset($filtros['empresa']) && !empty($filtros['empresa'])) {
            $conditions['Notaite.empresa'] = $filtros['empresa'];
        }
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
            $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];
        }
        if (isset($filtros['codigo_gestor']) && !empty($filtros['codigo_gestor'])) {
            $conditions['Usuario.codigo'] = $filtros['codigo_gestor'];
        }
        if (isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora'])) {
            $conditions['Corretora.codigo'] = $filtros['codigo_corretora'];
        }
        if (isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])) {
            $conditions['Seguradora.codigo'] = $filtros['codigo_seguradora'];
        }
        if (isset($filtros['codigo_filial']) && !empty($filtros['codigo_filial'])) {
            $conditions['Cliente.codigo_endereco_regiao'] = $filtros['codigo_filial'];
        }
        if (isset($filtros['codigo_grupo_economico']) && !empty($filtros['codigo_grupo_economico'])) {
            $conditions['GrupoEconomico.codigo'] = $filtros['codigo_grupo_economico'];
        }
        if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto'])) {
            $conditions['Notaite.produto'] = $filtros['codigo_produto'];
        }
        return $conditions;
    }

    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        if (isset($extra['tipo_ranking'])) {
            return $this->rankingFaturamento('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'extra'));
        }
        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group'));
    }

    function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
        if (isset($extra['tipo_ranking'])) {
            return $this->rankingFaturamento('count', compact('conditions', 'recursive', 'extra'));
        }
        return $this->find('count', compact('conditions', 'recursive'));
    }
}
?>