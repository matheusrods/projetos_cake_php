<?php

class FichaPesquisa extends AppModel {

    var $name = 'FichaPesquisa';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_pesquisa';
    var $primaryKey = 'codigo';
//	var $displayField = 'descricao';
    var $actsAs = array('Secure');

    function finaliza($codigo_ficha) {
        $usuario = & ClassRegistry::init('Usuario');

        $codigo_usuario_inclusao = $usuario->find('first', array('fields' => 'codigo',
            'conditions' => array('apelido' => 'pesquisador_automatico')));

        $ficha_pesquisa_pendente = $this->find('first', array('conditions' => array('codigo_ficha' => $codigo_ficha, 'codigo_tipo_pesquisa' => 1)));
        $ficha_pesquisa_pendente['FichaPesquisa']['codigo_tipo_pesquisa'] = 2;
        $ficha_pesquisa_pendente['FichaPesquisa']['codigo_status_profissional'] = 1;
        $ficha_pesquisa_pendente['FichaPesquisa']['codigo_usuario_alteracao'] = $codigo_usuario_inclusao['Usuario']['codigo'];
        $ficha_pesquisa_pendente['FichaPesquisa']['data_alteracao'] = date('Y-m-d H:i:s');

        return $this->save($ficha_pesquisa_pendente);
    }

    function obterFichasParaRenovacao($data, $pesquisador_automatico = false) {
        list($ano, $mes, $dia) = explode('-', $data);
        $fichaPesquisa = & ClassRegistry::init('FichaPesquisa');
        $this->bindModel(array(
            'belongsTo' => array(
                'Ficha' => array(
                    'class' => 'Ficha',
                    'foreignKey' => 'codigo_ficha'
                    ))));
        $conditions = array(
            'FichaPesquisa.codigo_usuario_alteracao' => 159,
            'FichaPesquisa.codigo_tipo_pesquisa' => 1,
            //'FichaPesquisa.data_inclusao BETWEEN ? AND ?' => array("$ano-$mes-$dia 00:00:00.000", "$ano-$mes-$dia 23:59:59.999")
            'FichaPesquisa.data_inclusao <= ?'=> "$ano-$mes-$dia 23:59:59.999"
            );
        if ($pesquisador_automatico) {
            $conditions[] = array('OR' => array('Ficha.pesquisador_automatico = 0','Ficha.pesquisador_automatico IS NULL'));
        }
        $fields = array('FichaPesquisa.codigo_ficha');
        $fichasNovas = $this->find('all', compact('fields', 'conditions'));
        return Set::extract('/FichaPesquisa/codigo_ficha', $fichasNovas);
    }

    function disponivelParaPesquisaAutomatica($codigo_ficha, $bloquear = false) {
        $this->bindModel(array('belongsTo' => array('Ficha' => array('foreignKey' => 'codigo_ficha'))));
        $disponivel = $this->find('count', array('conditions' => array('Ficha.codigo' => $codigo_ficha, 'OR' => array('Ficha.pesquisador_automatico = 0','Ficha.pesquisador_automatico IS NULL'))));
        if ($disponivel) {
            if ($bloquear) {
                $this->Ficha->id = $codigo_ficha;
                if ($this->Ficha->saveField('pesquisador_automatico', 1)) {
                    return true;
                }
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    function obterUltimaFichaPesquisa($codigo_ficha) {
        $options = array(
            'recursive' => -1,
            'conditions' => array(
                'FichaPesquisa.codigo_ficha' => $codigo_ficha,
            ),
            'limit' => 1,
            'order' => 'FichaPesquisa.codigo DESC'
        );

        return $this->find('first', $options);
    }

    function duplicar($codigo_ficha_antiga, $parametros) {
        try {
            if (empty($codigo_ficha_antiga)) {
                throw new Exception('Codigo ficha não informado');
            }

            $ficha_pesquisa_antiga = $this->obterUltimaFichaPesquisa($codigo_ficha_antiga);
            $ficha_pesquisa_nova[$this->name] = array_merge($ficha_pesquisa_antiga[$this->name], $parametros);
            //$this->log(var_export($ficha_pesquisa_nova[$this->name],true),'ws_teleconsult');
            $result = $this->incluir($ficha_pesquisa_nova);

            if ($result) {
                return $this->id;
            } else {
                throw new Exception('Erro ao duplicar ficha');
            }
        } catch (Exception $e) {
            $msg = (!empty($e) ? $e->getmessage() : '');
            $this->invalidate('',$msg);
            return false;
        }
    }

    function duplicarFichaCompleta($codigo_ficha_antiga, $parametros = null) {
        try {
            $this->FichaPesquisaArtCriminal = ClassRegistry::init('FichaPesquisaArtCriminal');
            $this->FichaPesquisaQR = ClassRegistry::init('FichaPesquisaQR');

            if (empty($codigo_ficha_antiga)) {
                throw new Exception('Codigo da Ficha está inválido');
            }

            $dados_ficha = $this->find('first',array('conditions'=>array('codigo'=>$codigo_ficha_antiga)));
            if (empty($dados_ficha['FichaPesquisa']['codigo'])) throw new Exception("Ficha de Pesquisa não encontrada");

            $codigo_ficha = $this->duplicar($dados_ficha['FichaPesquisa']['codigo_ficha'], $parametros[$this->name]);
            if (!$codigo_ficha) throw new Exception(var_export($this->validationErrors,true));

            if (!$this->FichaPesquisaArtCriminal->duplicar($dados_ficha['FichaPesquisa']['codigo'], $codigo_ficha)) {
                $msg_erro = (!empty($this->FichaPesquisaArtCriminal->validationErrors) ? implode("\n",$this->FichaPesquisaArtCriminal->validationErrors) : 'Erro ao salvar Art. Criminal da Ficha de Pesquisa');
                throw new Exception($msg_erro);
                return false;                        
            }

            if (!$this->FichaPesquisaQR->duplicar($dados_ficha['FichaPesquisa']['codigo'], $codigo_ficha)) {
                $msg_erro = (!empty($this->FichaPesquisaQR->validationErrors) ? implode("\n",$this->FichaPesquisaQR->validationErrors) : 'Erro ao salvar Resposta da Ficha de Pesquisa');
                throw new Exception($msg_erro);
                return false;                        
            }

            return $codigo_ficha;

        } catch (Exception $e) {
            $msg = (!empty($e) ? $e->getmessage() : '');
            $this->invalidate('',$msg);
            return false;
        }
    }

    /**
     * Lista fichas dado um cliente e um periodo MES/ANO.
     * 
     * @param $codigo_cliente
     * @param $mes
     * @param $ano
     * @param $params
     *
     * @return 
     */
    function listaFichasComTempo($codigo_cliente, $mes, $ano, $conditions = array()) {
        try {

            if (empty($codigo_cliente)) {
                throw new Exception('Código Cliente é obrigatório');
            }

            if (empty($mes)) {
                throw new Exception('Mês é obrigatório');
            }

            if (empty($ano)) {
                throw new Exception('Ano é obrigatório');
            }

            $data_ini = $ano . '-' . $mes . '-' . '01 00:00:00.000';
            $data_fim = date('Y-m-d H:i:s.997', mktime(23, 59, 59, $mes+1, 0, $ano));
            $fields = array(
                //'LogFaturamento.codigo_tipo_operacao',
                'Ficha.codigo_profissional_log',
                'ProfissionalLog.codigo_documento',
                'ProfissionalLog.nome',
                'ProfissionalLog.codigo_profissional_tipo',
                'Ficha.data_inclusao',
                'FichaPesquisa.codigo',
                'FichaPesquisa.data_alteracao',
                'FichaPesquisa.tempo_restante',
                'ProfissionalStatus.codigo',
                'ProfissionalStatus.descricao',
                'convert(int, datediff(MINUTE, Ficha.data_inclusao, FichaPesquisa.data_alteracao)) as tempo_pesquisa_ficha'
            );

            $joins = array(
                array(
                    'table' => 'ficha',
                    'tableSchema' => 'informacoes',
                    'databaseTable' => 'dbTeleconsult',
                    'alias' => 'Ficha',
                    'type' => 'INNER',
                    'conditions' => array('FichaPesquisa.codigo_ficha = Ficha.codigo')
                ),
                array(
                    'table' => 'log_faturamento',
                    'tableSchema' => 'informacoes',
                    'databaseTable' => 'dbTeleconsult',
                    'alias' => 'LogFaturamento',
                    'type' => 'LEFT',
                    'conditions' => array('Ficha.codigo = LogFaturamento.codigo_ficha')
                ),
                array(
                    'table' => 'cliente',
                    'tableSchema' => 'vendas',
                    'databaseTable' => 'dbBuonny',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => array('Ficha.codigo_cliente = Cliente.codigo')
                ),
                array(
                    'table' => 'profissional_log',
                    'tableSchema' => 'publico',
                    'databaseTable' => 'dbBuonny',
                    'alias' => 'ProfissionalLog',
                    'type' => 'INNER',
                    'conditions' => array('Ficha.codigo_profissional_log = ProfissionalLog.codigo')
                ),
                array(
                    'table' => 'status',
                    'tableSchema' => 'informacoes',
                    'databaseTable' => 'dbTeleconsult',
                    'alias' => 'ProfissionalStatus',
                    'type' => 'INNER',
                    'conditions' => array('FichaPesquisa.codigo_status_profissional = ProfissionalStatus.codigo')
                ),
                array(
                    'table' => "(SELECT MAX(codigo) as codigo FROM $this->databaseTable.$this->tableSchema.$this->useTable WHERE {$this->useTable}.data_inclusao BETWEEN '$data_ini' and '$data_fim' group by codigo_ficha)",
                    'alias' => 'UltimaFichaPesquisa',
                    'type' => 'INNER',
                    'conditions' => array('FichaPesquisa.codigo = UltimaFichaPesquisa.codigo')
                ),
            );

            $filtros_obrigatorios = array(
                'Cliente.codigo' => $codigo_cliente,
                'FichaPesquisa.data_inclusao BETWEEN ? AND ?' => array($data_ini, $data_fim)
            );

            $conditions = array_merge($conditions, $filtros_obrigatorios);

            $result = $this->find('all', array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => 'FichaPesquisa.codigo_status_profissional'
                    ));

            if (is_array($result)) {
                return $result;
            } else {
                throw new Exception('Falha ao buscar fichas');
            }
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function calcularPorcentagem($dados) {
        $retorno = array();
        $tempo_total = array();

        $totais = array();
        $totais['quantidade'] = 0;
        $totais['Estatistica'] = array(
            'no_prazo' => 0,
            'fora_do_prazo' => 0
        );

        foreach ($dados as $key => $value) {
            $indice = $value['ProfissionalStatus']['descricao'];

            $tempo_restante = $value['FichaPesquisa']['tempo_restante'];
            $tempo_gasto = $value[0]['tempo_pesquisa_ficha'];

            if (!isset($tempo_total[$indice])) {
                $tempo_total[$indice] = 0;
            }
            $tempo_total[$indice] += $tempo_gasto;

            if (!isset($retorno[$indice]['Estatistica'])) {
                $retorno[$indice]['Estatistica'] = array(
                    'no_prazo' => 0,
                    'fora_do_prazo' => 0
                );
            }

            if ($tempo_gasto > $tempo_restante) {
                $retorno[$indice]['Estatistica']['fora_do_prazo']++;
                $totais['Estatistica']['fora_do_prazo']++;
            } else {
                $retorno[$indice]['Estatistica']['no_prazo']++;
                $totais['Estatistica']['no_prazo']++;
            }

            if (!isset($retorno[$indice]['quantidade'])) {
                $retorno[$indice]['quantidade'] = 0;
            }

            $retorno[$indice]['quantidade']++;
            
            $totais['quantidade']++;
        }
        if (count($retorno) > 0) {
            $tempo_total['QUANTIDADE TOTAL'] = array_sum($tempo_total);
            $retorno['QUANTIDADE TOTAL'] = $totais;
        }
        foreach (array_keys($retorno) as $key) {
            $quantidade = $retorno[$key]['quantidade'];
            $porcentagem_fora_do_prazo = $retorno[$key]['Estatistica']['fora_do_prazo'] * 100 / $quantidade;
            $tempo_medio = $tempo_total[$key] / $quantidade;
            $retorno[$key]['Estatistica']['porcentagem_fora_do_prazo'] = $porcentagem_fora_do_prazo;
            $retorno[$key]['Estatistica']['tempo_medio'] = $tempo_medio;
        }

        return $retorno;
    }
    
    function salvarDaFicha($codigo_ficha, $tempo_pesquisa, $codigo_ultima_ficha_pesquisa = null) {
    	ClassRegistry::init('Status');
    	
        if (!empty($codigo_ultima_ficha_pesquisa)) {
        	$ultima_ficha = $this->find('first', array(
        		'conditions'=>array('FichaPesquisa.codigo'=>$codigo_ultima_ficha_pesquisa),
        		'fields'=>array('FichaPesquisa.observacao')
        	));
        }

    	$ficha_pesquisa['FichaPesquisa']['codigo_ficha'] = $codigo_ficha;
    	$ficha_pesquisa['FichaPesquisa']['codigo_tipo_pesquisa'] = 1;
    	$ficha_pesquisa['FichaPesquisa']['codigo_status_profissional'] = Status::EM_PESQUISA;
    	$ficha_pesquisa['FichaPesquisa']['tempo_restante'] = $tempo_pesquisa;
    	if (!empty($ultima_ficha['FichaPesquisa']['observacao'])) $ficha_pesquisa['FichaPesquisa']['observacao'] = $ultima_ficha['FichaPesquisa']['observacao'];

    	$this->create();
    	return $this->save($ficha_pesquisa);
    }
    
    function codigoUltimaFichaPesquisa($codigo_documento) {
    	$this->bindModel(array(
    		'belongsTo' => array(
    				'Ficha' => array('foreignKey'=>false, 'conditions'=>'FichaPesquisa.codigo_ficha = Ficha.codigo'),
    				'ProfissionalLog' => array('foreignKey'=>false, 'conditions'=>'Ficha.codigo_profissional_log = ProfissionalLog.codigo'),
    		)
    	));
    	
    	$ultima_ficha = $this->find('first', array(
    		'conditions'=>array('ProfissionalLog.codigo_documento'=>preg_replace('/\D/', '', $codigo_documento)),
    		'fields'=>array('FichaPesquisa.codigo'), 
    		'order'=>'FichaPesquisa.codigo DESC'
    	));
    	
    	return $ultima_ficha['FichaPesquisa']['codigo'];
    }

    function ultimasPesquisasPorFicha() {
        $this->bindModel(array('belongsTo' => array(
            'Ficha' => array('foreignKey' => 'codigo_ficha'),
        )));
        $Sinistro = ClassRegistry::init('Sinistro');
        $fichas = $Sinistro->ultimasFichasTLC();
        $fields = array(
            'MAX(FichaPesquisa.codigo) AS codigo_ficha_pesquisa'
        );
        $conditions = array("Ficha.codigo IN ({$fichas})");
        $group = array('FichaPesquisa.codigo_ficha');
        return $this->find('sql', compact('conditions', 'fields', 'group'));
    }
}
