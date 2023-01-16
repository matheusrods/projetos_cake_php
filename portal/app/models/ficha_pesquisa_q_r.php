<?php

class FichaPesquisaQR extends AppModel {

    var $name = 'FichaPesquisaQR';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_pesquisa_questao_resposta';
    //var $primaryKey = null;
    var $actsAs = array('Secure');

    const QUESTAO_CONSTA_CHEQUE = 6;
    const QUESTAO_CNH = 7;
    const QUESTAO_VEICULO = 8;
    const QUESTAO_CARRETA = 9;
    const QUESTAO_PROFISSIONAL_PROPRIETÁRIO_COM_ANOMALIA = 17;
    const QUESTAO_OCORRÊNCIA_DE_VEÍCULO = 18;

    const CARRETA_OK = 18;
    const CNH_OK = 14;
    const CHEQUE_OK = 13;
    const VEICULO_OCORRENCIA_OK = 39;
    const PROFISSIONAL_PROPRIETARIO_OK = 37;
    const VEICULO_OK = 16;
    const PROFISSIONAL_PROPRIETÁRIO_COM_ANOMALIA_OK = 36;
    const OCORRÊNCIA_VEICULO_OK = 38;

    function carregarPorQuestao($codigo_ficha_pesquisa, $codigo_questao) {
        $this->bindModel(array('belongsTo' => array(
                'QuestaoResposta' => array(
                    'className' => 'QuestaoResposta',
                    'foreignKey' => 'codigo_questao_resposta'
                )
                )));

        $result = $this->find('first', array('conditions' => array('QuestaoResposta.codigo_questao' => $codigo_questao,
                'FichaPesquisaQR.codigo_ficha_pesquisa' => $codigo_ficha_pesquisa)));
        $this->unbindModel(array('belongsTo' => array('QuestaoResposta')));

        return $result;
    }

    function gravaAprovacaoCheque($codigo_ficha_pesquisa) {
        $result = $this->carregarPorQuestao($codigo_ficha_pesquisa, self::QUESTAO_CONSTA_CHEQUE);

        if (empty($result)) {
            $result['FichaPesquisaQR']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa;
            $result['FichaPesquisaQR']['codigo_questao_resposta'] = self::CHEQUE_OK;
//            var_dump("inserir cheque");
            return $this->inserir($result);
        } else {
            $atualizado = $result;
            $atualizado['FichaPesquisaQR']['codigo_questao_resposta'] = self::CHEQUE_OK;
//            var_dump("atualizar cheque");
            return $this->atualizar($result, $atualizado);
        }
    }

    function gravaAprovacaoCnh($codigo_ficha_pesquisa) {
        $result = $this->carregarPorQuestao($codigo_ficha_pesquisa, self::QUESTAO_CNH);

        if (empty($result)) {
            $result['FichaPesquisaQR']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa;
            $result['FichaPesquisaQR']['codigo_questao_resposta'] = self::CNH_OK;
            return $this->inserir($result);
        } else {
            $atualizado = $result;
            $atualizado['FichaPesquisaQR']['codigo_questao_resposta'] = self::CNH_OK;
            return $this->atualizar($result, $atualizado);
        }
    }

    function gravaAprovacaoVeiculo($codigo_ficha_pesquisa) {
        $result = $this->carregarPorQuestao($codigo_ficha_pesquisa, self::QUESTAO_VEICULO);

        if (empty($result)) {
            $result['FichaPesquisaQR']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa;
            $result['FichaPesquisaQR']['codigo_questao_resposta'] = self::VEICULO_OK;
            return $this->inserir($result);
        } else {
            $atualizado = $result;
            $atualizado['FichaPesquisaQR']['codigo_questao_resposta'] = self::VEICULO_OK;
            return $this->atualizar($result, $atualizado);
        }
    }

    function gravaAprovacaoCarreta($codigo_ficha_pesquisa) {
        $result = $this->carregarPorQuestao($codigo_ficha_pesquisa, self::QUESTAO_CARRETA);

        if (empty($result)) {
            $result['FichaPesquisaQR']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa;
            $result['FichaPesquisaQR']['codigo_questao_resposta'] = self::CARRETA_OK;
            return $this->inserir($result);
        } else {
            $atualizado = $result;
            $atualizado['FichaPesquisaQR']['codigo_questao_resposta'] = self::CARRETA_OK;
            return $this->atualizar($result, $atualizado);
        }
    }

    function gravaAprovacaoProfissional($codigo_ficha_pesquisa) {
        $result = $this->carregarPorQuestao($codigo_ficha_pesquisa, self::QUESTAO_PROFISSIONAL_PROPRIETÁRIO_COM_ANOMALIA);
        if (empty($result)) {
            $result['FichaPesquisaQR']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa;
            $result['FichaPesquisaQR']['codigo_questao_resposta'] = self::PROFISSIONAL_PROPRIETÁRIO_COM_ANOMALIA_OK;
            return $this->inserir($result);
        } else {
            $atualizado = $result;
            $atualizado['FichaPesquisaQR']['codigo_questao_resposta'] = self::PROFISSIONAL_PROPRIETÁRIO_COM_ANOMALIA_OK;
            return $this->atualizar($result, $atualizado);
        }

    }

    function gravaAprovacaoOcorrenciaVeiculo($codigo_ficha_pesquisa) {
        $result = $this->carregarPorQuestao($codigo_ficha_pesquisa, self::QUESTAO_OCORRÊNCIA_DE_VEÍCULO);

        if (empty($result)) {
            $result['FichaPesquisaQR']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa;
            $result['FichaPesquisaQR']['codigo_questao_resposta'] = self::OCORRÊNCIA_VEICULO_OK;
            return $this->inserir($result);
        } else {
            $atualizado = $result;
            $atualizado['FichaPesquisaQR']['codigo_questao_resposta'] = self::OCORRÊNCIA_VEICULO_OK;
            return $this->atualizar($result, $atualizado);
        }
    }

    function inserir($dados) {
        $query = "insert into {$this->databaseTable}.{$this->tableSchema}.ficha_pesquisa_questao_resposta (codigo_ficha_pesquisa, codigo_questao_resposta, observacao) values ('{$dados['FichaPesquisaQR']['codigo_ficha_pesquisa']}', '{$dados['FichaPesquisaQR']['codigo_questao_resposta']}', '' )";
        return ($this->query($query) !== false);
    }

    function atualizar($dados_antigo, $dados_novos) {
        return $this->updateAll(array('codigo_questao_resposta' => $dados_novos['FichaPesquisaQR']['codigo_questao_resposta']), array('codigo_ficha_pesquisa' => $dados_antigo['FichaPesquisaQR']['codigo_ficha_pesquisa'],
                    'codigo_questao_resposta' => $dados_antigo['FichaPesquisaQR']['codigo_questao_resposta'])
        );
    }

    function duplicar($codigo_ficha_pesquisa_antiga, $codigo_ficha_pesquisa) {
        try {
            $result = $this->query("insert
                        into {$this->databaseTable}.{$this->tableSchema}.ficha_pesquisa_questao_resposta 
                        (
                            codigo_ficha_pesquisa,
                            codigo_questao_resposta,
                            observacao 
                        )
                        select
                            '{$codigo_ficha_pesquisa}',
                            codigo_questao_resposta,
                            observacao 
                        from
                            {$this->databaseTable}.{$this->tableSchema}.ficha_pesquisa_questao_resposta
                        where
                            codigo_ficha_pesquisa = '{$codigo_ficha_pesquisa_antiga}'");
                        
            if ($result === false) {
                throw new Exception('Falha ao gravar a ficha_pesquisa_questao_resposta');
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function obterCriteriosUltimaFichaProfissional($codigo_cliente, $documento_profissional) {
    	App::import('Model', 'Status');
    	$this->Criterio = ClassRegistry::init('Criterio');
    	$this->StatusCriterio = ClassRegistry::init('StatusCriterio');
    	$this->FichaPesquisa = ClassRegistry::init('FichaPesquisa');
    	
    	$conditions = array(
    			'Ficha.codigo_status' => array(Status::RECOMENDADO, Status::NAO_RECOMENDADO, Status::INSUFICIENCIA_DADOS),
    			'Ficha.codigo_cliente' => $codigo_cliente,
    			'ProfissionalLog.codigo_documento' => preg_replace('/[-\.]/', '', $documento_profissional)
    	);
    	 
    	$order = array('FichaPesquisa.data_inclusao DESC');
    	 
    	$this->FichaPesquisa->bindModel(array(
    			'belongsTo' => array(
    					'Ficha' => array(
    							'class' => 'Ficha',
    							'foreignKey' => 'codigo'
    					),
    					'ProfissionalLog' => array(
    							'class' => 'ProfissionalLog',
    							'foreignKey' => false,
    							'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log'
    					),
    			)
    	));
    	 
    	$fields = array('FichaPesquisa.codigo', 'FichaPesquisa.data_inclusao');
    	 
    	$ficha_pesquisa = $this->FichaPesquisa->find('first', compact('fields', 'conditions', 'order'));
    	 
    	$this->bindModel(array(
    			'belongsTo' => array(
    					'QuestaoResposta' => array(
    							'class' => 'Ficha',
    							'foreignKey' => 'codigo_questao_resposta'
    					),
    			)
    	));
    	$respostas = $this->find('all', array('conditions'=>array('codigo_ficha_pesquisa'=>$ficha_pesquisa['FichaPesquisa']['codigo'])));
    	$respostas = Set::combine($respostas, '/QuestaoResposta/codigo_questao', '/QuestaoResposta/codigo_resposta');
    	
    	$criterios = array();
    	foreach($respostas as $questao=>$resposta){
    		$criterio = $this->Criterio->field('codigo', array('codigo_questao'=>$questao));
    		if(!empty($criterio)){
    			$status_criterio = $this->StatusCriterio->field('codigo', array('codigo_resposta'=>$resposta, 'codigo_criterio'=>$criterio));
    			if(!empty($status_criterio)){
    				$criterios[$criterio] = $status_criterio;
    			}
    		}
    	}
    	return array($ficha_pesquisa['FichaPesquisa']['data_inclusao'], $criterios);
    }
    
    function salvarDaFicha($codigo_ultima_ficha_pesquisa, $codigo_ficha_pesquisa) {
    	$respostas = $this->find('all', array(
    			'conditions' => array(
    					'codigo_ficha_pesquisa' => $codigo_ultima_ficha_pesquisa
    			),
    			'fields' => array(
    					'codigo_questao_resposta',
    					'observacao'
    			)
    	)
    	);
    	
    	if (!empty($respostas)) {
    		foreach ($respostas as $resposta) {
    			$resposta['FichaPesquisaQR']['codigo_ficha_pesquisa'] = $codigo_ficha_pesquisa;
    			$this->create();
    			if (!@$this->save($resposta)) {
    				return false;
    			}
    		}
    	}
    }

    function sintetico() {
        $dbo = $this->getDatasource();
        $consolidadoQuestaoResposta = $this->sinteticoQuestaoResposta();
        $group = array('codigo_questao');
        $fields = array_merge($group, array('SUM(qtd) AS qtd_questao'));
        $sinteticoQuestao = $dbo->buildStatement(array(
            'fields' => $fields,
            'table' => "SinteticoQuestaoResposta",
            'alias' => "SinteticoQuestao",
            'limit' => null,
            'offset' => null,
            'joins' => array(),
            'conditions' => null,
            'order' => null,
            'group' => $group,
        ),$this);
        $fields = array(
            'SinteticoQuestaoResposta.codigo_questao',
            'descricao_questao',
            'codigo_resposta',
            'descricao_resposta',
            'qtd',
            'qtd_questao',
            'ROUND(CONVERT(DECIMAL,qtd) / CONVERT(DECIMAL, qtd_questao),2) * 100 AS percentual'
        );
        $order = array(
            'SinteticoQuestaoResposta.codigo_questao',
            'codigo_resposta',
        );
        $sql = $dbo->buildStatement(array(
            'fields' => $fields,
            'table' => "SinteticoQuestaoResposta",
            'alias' => "SinteticoQuestaoResposta",
            'limit' => null,
            'offset' => null,
            'joins' => array(
                array(
                    'table' => "({$sinteticoQuestao})",
                    'alias' => 'SinteticoQuestao',
                    'type' => 'LEFT',
                    'conditions' => 'SinteticoQuestao.codigo_questao = SinteticoQuestaoResposta.codigo_questao',
                ),
            ),
            'conditions' => null,
            'order' => $order,
            'group' => null,
        ),$this);
        $sql = "WITH SinteticoQuestaoResposta AS ({$consolidadoQuestaoResposta}) ".$sql;
        return $sql;
    }

    function sinteticoQuestaoResposta() {
        $this->bindModel(array('belongsTo' => array(
            'FichaPesquisa' => array('foreignKey' => 'codigo_ficha_pesquisa'),
            'QuestaoResposta' => array('foreignKey' => 'codigo_questao_resposta'),
            'Questao' => array('foreignKey' => false, 'conditions' => 'Questao.codigo = QuestaoResposta.codigo_questao'),
            'Resposta' => array('foreignKey' => false, 'conditions' => 'Resposta.codigo = QuestaoResposta.codigo_resposta'),
        )));
        $ultimasPesquisasPorFicha = $this->FichaPesquisa->ultimasPesquisasPorFicha();
        $conditions = array("FichaPesquisa.codigo IN ({$ultimasPesquisasPorFicha})");
        $group = array(
            'QuestaoResposta.codigo',
            'Questao.codigo',
            'Questao.descricao',
            'Resposta.codigo',
            'Resposta.descricao',
        );
        $fields = array(
            'QuestaoResposta.codigo AS codigo_questao_resposta',
            'Questao.codigo AS codigo_questao',
            'Questao.descricao AS descricao_questao',
            'Resposta.codigo AS codigo_resposta',
            'Resposta.descricao AS descricao_resposta',
            'COUNT(1/1) AS qtd'
        );
        return $this->find('sql', compact('conditions', 'fields', 'group'));
    }

    function obter($codigo_ficha_pesquisa) {
        $this->bindModel(array('belongsTo' => array(
            'QuestaoResposta' => array('foreignKey' => 'codigo_questao_resposta'),
            'Questao' => array('foreignKey' => false, 'conditions' => 'Questao.codigo = QuestaoResposta.codigo_questao'),
            'Resposta' => array('foreignKey' => false, 'conditions' => 'Resposta.codigo = QuestaoResposta.codigo_resposta'),
        )));
        return $this->find('all', array('fields' => array('codigo_questao_resposta','QuestaoResposta.codigo_questao', 'QuestaoResposta.codigo_resposta', 'Questao.codigo', 'Questao.descricao', 'Resposta.codigo', 'Resposta.descricao'), 'conditions' => array('codigo_ficha_pesquisa' => $codigo_ficha_pesquisa)));
    }
}