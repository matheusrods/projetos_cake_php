<?php
App::import('Model', 'TipoOperacao');
App::import('Model', 'ClienteOperacao');
App::import('Model', 'ClienteProduto');
App::import('Model', 'Produto');
class LogFaturamentoTeleconsult extends AppModel {

    var $name = 'LogFaturamentoTeleconsult';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'log_faturamento';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $validate = array(
        'numero_liberacao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o numero liberacao',
             ),
        )
    );

    const SOMATORIA_VALOR_TOTAL = 1;
    const SOMATORIA_VALOR_UNITARIO = 2;
    const TIPO_PERIODO_MENSAL = 1;
    const TIPO_PERIODO_DIARIO = 2;
    const TIPO_PERIODO_HORA = 3;
    const TIPO_SMONLINE_SOMENTE = 1;
    const TIPO_SMONLINE_SEM = 2;
    const TIPO_COBRANCA_SOMENTE_COBRADO = 1;
    const TIPO_COBRANCA_SEM = 2;

    function bindTipoOperacao() {
        $this->bindModel(array(
            'belongsTo' => array(
                'TipoOperacao' => array(
                    'className' => 'TipoOperacao',
                    'foreignKey' => 'codigo_tipo_operacao'
            ))));
    }

    function unbindTipoOperacao() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'TipoOperacao'
            )
        ));
    }

    function incluir($dados) {
        try {

            ## Remove codigo e data_inclusao
            unset($dados[$this->name]['codigo']);
            unset($dados[$this->name]['data_inclusao']);
            unset($dados[$this->name]['numero_liberacao']);

            ## Cria um novo faturamento
            $this->create();
            $result = $this->save($dados);
            ## Atualiza o faturamento inserido setando o código no numero_liberacao
            $codigo_novo = $this->id;
            $faturamento_inserido = $this->findByCodigo($codigo_novo);
            $faturamento_inserido[$this->name]['numero_liberacao'] = $faturamento_inserido[$this->name]['codigo'];
            $result = $this->save($faturamento_inserido);

            if(!$result) {
                throw new Exception('Não foi possível Incluir');
            }

            return $codigo_novo;
        } catch(Exception $e) {
            return false;
        }
    }

    public function duplicar($codigo, $params=null, $cobrar=false) {
        ClassRegistry::init('TipoOperacao');
        try {
            if (empty($codigo)) {
                throw new Exception();
            }

            $model_data = $this->find('first', array(
                'conditions' => array(
                    "{$this->name}.codigo" => $codigo
                    )));

            $model_data[$this->name] = array_merge($model_data[$this->name], (array) $params);

            if (!$cobrar) {
                $model_data[$this->name]['codigo_tipo_operacao'] = TipoOperacao::ATUALIZACAO_SEM_COBRANCA;
                $model_data[$this->name]['premio_minimo'] = 0;
                $model_data[$this->name]['valor'] = 0;
                $model_data[$this->name]['valor_taxa_bancaria'] = 0;
            }

            $result = $this->incluir($model_data);

            if ($result) {
                return $this->id;
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function converteParametroEmCondition($link) {
        $parametros = explode('|' , $link);
        $codigo_cliente = $parametros[1];
        $ano_mes = $parametros[2];
        $periodo = Comum::periodo($ano_mes);
        $condition = "WHERE faturamento.codigo_usuario_inclusao <> 1 and tipo_operacao.cobrado = 1 and (faturamento.codigo_cliente = $codigo_cliente or faturamento.codigo_cliente_pagador = $codigo_cliente) and faturamento.data_inclusao between '$periodo[0]' and '$periodo[1]'";
        return $condition;
    }

    public function totalPorProdutoServicoProfissional($periodo) {
        $Produto = ClassRegistry::init('Produto');
        $Servico = ClassRegistry::init('Servico');
        $TipoOperacao = ClassRegistry::init('TipoOperacao');
        $ProfissionalTipo = ClassRegistry::init('ProfissionalTipo');
        $group = array("left(convert(varchar, LogFaturamentoTeleconsult.data_inclusao, 102),7)", "Produto.descricao", "Servico.descricao", "case ProfissionalTipo.codigo when 1 then ProfissionalTipo.descricao else 'OUTROS' end");
        $fields = array("left(convert(varchar, LogFaturamentoTeleconsult.data_inclusao, 102),7) as ano_mes", "Produto.descricao", "Servico.descricao", "case ProfissionalTipo.codigo when 1 then ProfissionalTipo.descricao else 'OUTROS' end AS ProfissionalTipo", "COUNT(*) as qtd");
        $this->bindModel(array('belongsTo' => array(
            'Produto' => array('foreignKey' => 'codigo_produto'),
            'ProfissionalTipo' => array('foreignKey' => 'codigo_profissional_tipo'),
            'TipoOperacao' => array('foreignKey' => 'codigo_tipo_operacao'),
            'Servico' => array('foreignKey' => false, 'conditions' => array('Servico.codigo = TipoOperacao.codigo_servico')),
        )));
        $periodo[0] = AppModel::dateToDbDate($periodo[0]);
        $periodo[1] = AppModel::dateToDbDate($periodo[1]);
        $conditions = array($this->name.'.data_inclusao BETWEEN ? AND ?' => $periodo, 'TipoOperacao.cobrado' => 1);
        $order = array('left(convert(varchar, LogFaturamentoTeleconsult.data_inclusao, 102),7)');
        return $this->find('all', array('escape' => false, 'fields' => $fields, 'group' => $group, 'conditions' => $conditions, 'order' => $order));
    }
    public function converteFiltroEmCondition($data) {
        $conditions = array();
        if (isset($data['numero_liberacao'])) {
            if (!empty($data['numero_liberacao']) || $data['numero_liberacao'] === 0)  {
                $conditions['LogFaturamentoTeleconsult.numero_liberacao'] = $data['numero_liberacao'];
            }
        }
        if (!empty($data['codigo_cliente'])) {
            $conditions['Cliente.codigo'] = preg_replace('/\D/', '', $data['codigo_cliente']);
        }
        if (!empty($data['razao_social'])) {
            $conditions['Cliente.razao_social like'] = '%' . $data['razao_social'] . '%';
        }
        if (!empty($data['codigo_documento'])) {
            $conditions['Profissional.codigo_documento'] = preg_replace('/\D/', '', $data['codigo_documento']);
        }
        if (!empty($data['codigo_produto'])) {
            $conditions['Produto.codigo'] = $data['codigo_produto'];
        }
        if (!empty($data['placa_veiculo'])) {
            $data['placa_veiculo'] = str_replace(array(' ', '-'), '', $data['placa_veiculo']);
            $conditions[] = array(
                'OR' => array(
                    'Veiculo.placa' => $data['placa_veiculo'], 
                    'AND' => array(
                        'LogFaturamentoTeleconsult.placa' => $data['placa_veiculo'], 
                        'LogFaturamentoTeleconsult.codigo_veiculo' => null
                    )
                )
            );
        }
        if (!empty($data['data_inclusao_inicio'])) {
            $conditions['LogFaturamentoTeleconsult.data_inclusao >='] = AppModel::dateToDbDate($data['data_inclusao_inicio']) . ' 00:00:00.0';
        }   
        if (!empty($data['data_inclusao_fim'])) {
            $conditions['LogFaturamentoTeleconsult.data_inclusao <='] = AppModel::dateToDbDate($data['data_inclusao_fim']) . ' 23:59:59.997';
        }
        if (!empty($data['tipo_cobranca'])) {
            $conditions['TipoOperacao.cobrado'] = $data['tipo_cobranca'] == 1 ? 1: 0;
        }
        if (!empty($data['tipo_smonline'])) {
            if($data['tipo_smonline'] == 1) {
                $conditions['LogFaturamentoTeleconsult.codigo_usuario_inclusao'] = 1;
                $conditions['LogFaturamentoTeleconsult.valor'] = 0;
            } else {
                $conditions['LogFaturamentoTeleconsult.codigo_usuario_inclusao <> '] = 1;
                $conditions['LogFaturamentoTeleconsult.valor <>'] = 0;
            }
        }

        if (!empty($data['data_inicial'])) {
            $conditions['LogFaturamentoTeleconsult.data_inclusao >='] = AppModel::dateTimeToDbDateTime2($data['data_inicial']);
        }
        if (!empty($data['data_final'])) {
            $conditions['LogFaturamentoTeleconsult.data_inclusao <='] = AppModel::dateTimeToDbDateTime2($data['data_final']);
        }

        // Condições para a listagem de Utilização de servicos

        if (!empty($data['codigo_pagador'])) {
             $conditions['ClientePagador.codigo'] = $data['codigo_pagador'];
        }
        if (!empty($data['codigo_utilizador'])) {
             $conditions['ClienteUtilizador.codigo'] = $data['codigo_utilizador'];
        }
        if (!empty($data['codigo_seguradora'])) {
             $conditions['Seguradora.codigo'] = $data['codigo_seguradora'];
        }
        if (!empty($data['codigo_gestor'])) {
            $conditions['Gestor.codigo'] = $data['codigo_gestor'];
        }
        if (!empty($data['codigo_endereco_regiao'])) {
             $conditions['EnderecoRegiao.codigo'] = $data['codigo_endereco_regiao'];
        }
        if (!empty($data['codigo_corretora'])) {
             $conditions['Corretora.codigo'] = $data['codigo_corretora'];
        }
        if (!empty($data['codigo_servico'])) {
            $conditions['Servico.codigo'] = $data['codigo_servico'];
        }
        return $conditions;
    }

    public function listagemSegundaViaProfissional($type = 'all', $options = array()) {
        $this->Ficha = ClassRegistry::init('Ficha');
        $this->Produto = ClassRegistry::init('Produto');
        $this->Cliente = ClassRegistry::init('Cliente');
        $this->Profissional = ClassRegistry::init('Profissional');
        $this->Veiculo = ClassRegistry::init('Veiculo');
        $this->TipoOperacao = ClassRegistry::init('TipoOperacao');

        $default_options = array(
            'recursive' => -1,
            'fields' => array(
                'LogFaturamentoTeleconsult.codigo',
                'LogFaturamentoTeleconsult.numero_liberacao',
                'LogFaturamentoTeleconsult.data_inclusao',
                'LogFaturamentoTeleconsult.observacao',
                'TipoOperacao.mensagem',
                'TipoOperacao.mensagem_cor',
                'TipoOperacao.observacao_cor',
                'Produto.descricao',
                'Cliente.codigo',
                'Cliente.razao_social',
                'Profissional.nome',
                'Profissional.codigo_documento',
                'Profissional.rg',
                'ISNULL(Veiculo.placa, LogFaturamentoTeleconsult.placa) AS veiculo_placa',
                'ISNULL(Carreta.placa, LogFaturamentoTeleconsult.placa_carreta) AS veiculo_carreta_placa',
            ),
            'joins' => array(
                array(
                    'table' => $this->Produto->databaseTable . '.' . $this->Produto->tableSchema . '.' . $this->Produto->useTable,
                    'alias' => $this->Produto->name,
                    'type' => 'INNER',
                    'conditions' => 'Produto.codigo = LogFaturamentoTeleconsult.codigo_produto'
                ),
                array(
                    'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                    'alias' => $this->Cliente->name,
                    'type' => 'INNER',
                    'conditions' => 'Cliente.codigo = LogFaturamentoTeleconsult.codigo_cliente'
                ),
                array(
                    'table' => $this->Profissional->databaseTable . '.' . $this->Profissional->tableSchema . '.' . $this->Profissional->useTable,
                    'alias' => $this->Profissional->name,
                    'type' => 'INNER',
                    'conditions' => 'Profissional.codigo = LogFaturamentoTeleconsult.codigo_profissional'
                ),
                array(
                    'table' => $this->Veiculo->databaseTable . '.' . $this->Veiculo->tableSchema . '.' . $this->Veiculo->useTable,
                    'alias' => $this->Veiculo->name,
                    'type' => 'LEFT',
                    'conditions' => 'Veiculo.codigo = LogFaturamentoTeleconsult.codigo_veiculo'
                ),
                array(
                    'table' => $this->Veiculo->databaseTable . '.' . $this->Veiculo->tableSchema . '.' . $this->Veiculo->useTable,
                    'alias' => 'Carreta',
                    'type' => 'LEFT',
                    'conditions' => 'Carreta.codigo = LogFaturamentoTeleconsult.codigo_veiculo_carreta'
                ),
                array(
                    'table' => $this->TipoOperacao->databaseTable . '.' . $this->TipoOperacao->tableSchema . '.' . $this->TipoOperacao->useTable,
                    'alias' => 'TipoOperacao',
                    'type' => 'LEFT',
                    'conditions' => 'TipoOperacao.codigo = LogFaturamentoTeleconsult.codigo_tipo_operacao'
                ),
            ),
        );

        if (!isset($options['conditions'])) {
            $options['conditions'] = array();
        }
        $options['conditions']['TipoOperacao.codigo'] = array(1,2,3,4,7,125);

        $options = array_merge($default_options, $options);
        if ($type == 'count') {
            unset($options['fields'], $options['limit'], $options['order']);
        }

        if ($type == 'paginate') {
            return $options;
        }

        return $this->find($type, $options);
    }

    public function parametrosVisualizarSegundaVia($codigo = null) {
        if (empty($codigo)) {
            return false;
        }

        $options = array(
            'conditions' => array(
                $this->name . '.codigo' => $codigo
            )
        );
        $dados = $this->listagemSegundaViaProfissional('first', $options);

        if (empty($dados)) {
            return false;
        }

        $parametros = array();
        $parametros['nome_profissional'] = $dados['Profissional']['nome'];
        $parametros['rg_profissional'] = $dados['Profissional']['rg'];
        $parametros['mensagem'] = $dados['TipoOperacao']['mensagem'];
        $parametros['observacao'] = $dados['LogFaturamentoTeleconsult']['observacao'];
        $parametros['placa'] = Comum::formatarPlaca($dados[0]['veiculo_placa']);
        $parametros['carreta'] = $dados[0]['veiculo_carreta_placa'];
        $parametros['numero_liberacao'] = $dados['LogFaturamentoTeleconsult']['numero_liberacao'];
        $parametros['produto'] = $dados['Produto']['descricao'];
        $parametros['mensagem_cor'] = $dados['TipoOperacao']['mensagem_cor'];
        $parametros['observacao_cor'] = $dados['TipoOperacao']['observacao_cor'];
        $parametros['codigo_log_faturamento'] = $dados['LogFaturamentoTeleconsult']['codigo'];
        $parametros['codigo_documento'] = $dados['Profissional']['codigo_documento'];
        $parametros['data_inclusao'] = $dados['LogFaturamentoTeleconsult']['data_inclusao'];
        $parametros['codigo'] = $dados['LogFaturamentoTeleconsult']['codigo'];

        return $parametros;
    }

    function quantidadeServicoPorProduto($filtros){
        $TipoOperacao = classRegistry::init('TipoOperacao');
        $Servico = classRegistry::init('Servico');
        $Produto = classRegistry::init('Produto');
        $Cliente = classRegistry::init('Cliente');
        $group = array(
            'LogFaturamentoTeleconsult.codigo_cliente',
            'Cliente.razao_social',
            'LogFaturamentoTeleconsult.codigo_produto',
            'Produto.descricao',
            'Servico.codigo',
            'Servico.descricao',
        );
        $fields = array_merge($group, array('COUNT(LogFaturamentoTeleconsult.codigo_produto) as quantidade'));

        $filtros['data_inicial'] = AppModel::dateToDbDate($filtros['data_inicial']).' 00:00:00';
        $filtros['data_final'] = AppModel::dateToDbDate($filtros['data_final']).' 23:59:59';
        $this->bindModel(array('belongsTo' => array(
            'TipoOperacao' => array('foreignKey' => 'codigo_tipo_operacao'),
            'Servico' => array('foreignKey' => false, 'conditions' => array('TipoOperacao.codigo_servico = Servico.codigo')),
            'Produto' => array('foreignKey' => 'codigo_produto'),
            'Cliente' => array('foreignKey' => 'codigo_cliente'),
        )));
        $conditions = array(
            'LogFaturamentoTeleconsult.codigo_cliente' => $filtros['codigo_cliente'],
            'LogFaturamentoTeleconsult.data_inclusao BETWEEN ? AND ?' => array($filtros['data_inicial'], $filtros['data_final']),
            'TipoOperacao.codigo_servico IS NOT NULL'
        );
        if (isset($filtros['tipo_smonline'])) {
            if ($filtros['tipo_smonline'] == self::TIPO_SMONLINE_SOMENTE)
                $conditions['LogFaturamentoTeleconsult.codigo_usuario_inclusao'] = 1;
            if ($filtros['tipo_smonline'] == self::TIPO_SMONLINE_SEM)
                $conditions['LogFaturamentoTeleconsult.codigo_usuario_inclusao !='] = 1;
        }
        if (isset($filtros['tipo_cobranca'])) {
            if ($filtros['tipo_cobranca'] == self::TIPO_COBRANCA_SOMENTE_COBRADO)
                $conditions['TipoOperacao.cobrado'] = 1;
            if ($filtros['tipo_cobranca'] == self::TIPO_COBRANCA_SEM)
                $conditions['TipoOperacao.cobrado !='] = 1;
        }
        $resultado = $this->find('all', compact('fields', 'joins', 'conditions', 'group'));
        return $resultado;
    }

    function relatorioConsolidadoTeleconsult($filtros){
        App::import('Model', 'Cliente');
        App::import('Model', 'TipoOperacao');
        App::import('Model', 'Servico');
        App::import('Model', 'Produto');

        $data_inicial = AppModel::dateToDbDate( $filtros['LogFaturamentoTeleconsult']['data_inicial'] ).' 00:00:00';
        $data_final = AppModel::dateToDbDate( $filtros['LogFaturamentoTeleconsult']['data_final'] ).' 23:59:59';
        $conditions['LogFaturamentoTeleconsult.data_inclusao BETWEEN ? AND ?'] = array($data_inicial, $data_final);

        $this->bindModel(array(
           'belongsTo' => array(
               'ClientePagador' => array(
                   'className' => 'Cliente',
                   'foreignKey' => 'codigo_cliente_pagador'
               ),
               'TipoOperacao' => array(
                   'className' => 'TipoOperacao',
                   'foreignKey' => 'codigo_tipo_operacao'
               ),
               'Servico' => array(
                   'className' => 'Servico',
                   'foreignKey' => false,
                   'conditions' => 'TipoOperacao.codigo_servico = Servico.codigo',
               ),

               'Cliente' => array(
                   'className' => 'Cliente',
                   'foreignKey' => 'codigo_cliente'
               )
            )
        ));

        if ( isset($filtros['LogFaturamentoTeleconsult']['codigo_cliente_pagador']) && !empty($filtros['LogFaturamentoTeleconsult']['codigo_cliente_pagador']) )
            $conditions['LogFaturamentoTeleconsult.codigo_cliente_pagador'] = $filtros['LogFaturamentoTeleconsult']['codigo_cliente_pagador'];

        if ( isset($filtros['LogFaturamentoTeleconsult']['codigo_cliente_utilizador']) && !empty($filtros['LogFaturamentoTeleconsult']['codigo_cliente_utilizador']) )
            $conditions['LogFaturamentoTeleconsult.codigo_cliente'] = $filtros['LogFaturamentoTeleconsult']['codigo_cliente_utilizador'];

        if ( isset($filtros['LogFaturamentoTeleconsult']['codigo_servico']) && !empty($filtros['LogFaturamentoTeleconsult']['codigo_servico']) )
            $conditions['Servico.codigo'] = $filtros['LogFaturamentoTeleconsult']['codigo_servico'];

        if ( isset($filtros['LogFaturamentoTeleconsult']['codigo_produto']) && !empty($filtros['LogFaturamentoTeleconsult']['codigo_produto']) )
            $conditions['LogFaturamentoTeleconsult.codigo_produto'] = $filtros['LogFaturamentoTeleconsult']['codigo_produto'];

        $conditions[] = 'TipoOperacao.codigo_servico IS NOT NULL';

        $resultado = $this->find( 'all', array(
                'fields' => array(
                    'Servico.descricao',
                    'LogFaturamentoTeleconsult.codigo_cliente_pagador',
                    'TipoOperacao.descricao',
                    'ClientePagador.razao_social',
                    'LogFaturamentoTeleconsult.codigo_cliente',
                    'Cliente.razao_social',
                    'count(LogFaturamentoTeleconsult.codigo_produto) AS quantidade'
                ),
                'group' => 'Servico.descricao,TipoOperacao.codigo_servico,TipoOperacao.descricao,ClientePagador.razao_social,Cliente.razao_social,LogFaturamentoTeleconsult.codigo_cliente_pagador,LogFaturamentoTeleconsult.codigo_cliente',
                'conditions' => $conditions
            )
        );
        return $resultado;
    }

    function servicosPorPeriodo($conditions, $filtros) {
        $this->Produto = ClassRegistry::init('Produto');
        $this->Servico = ClassRegistry::init('Servico');
        $this->TipoOperacao = ClassRegistry::init('TipoOperacao');
        $this->ProfissionalTipo = ClassRegistry::init('ProfissionalTipo');

        $corte_periodo = 7;
        if (isset($filtros['tipo_periodo'])) {
            if ($filtros['tipo_periodo'] == self::TIPO_PERIODO_HORA) {
                $corte_periodo = 13;
            } elseif ($filtros['tipo_periodo'] == self::TIPO_PERIODO_DIARIO) {
                $corte_periodo = 10;
            }
        }

        $fields = array(
            "Produto.descricao as 'produto'",
            "Servico.descricao as 'servico'",
            "CASE
                WHEN ProfissionalTipo.codigo = 1
                THEN ProfissionalTipo.descricao
                ELSE 'OUTROS'
            END AS profissional",
            "SUBSTRING(CONVERT(VARCHAR,LogFaturamentoTeleconsult.data_inclusao, 120), 1, {$corte_periodo}) AS 'data_inclusao'",
            "COUNT(*) AS qtd"
        );
        $joins = array(
            array(
                'table' => "{$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable}",
                'alias' => 'Produto',
                'type' => 'INNER',
                'conditions' => 'Produto.codigo = LogFaturamentoTeleconsult.codigo_produto'
            ),
            array(
                'table' => "{$this->TipoOperacao->databaseTable}.{$this->TipoOperacao->tableSchema}.{$this->TipoOperacao->useTable}",
                'alias' => 'TipoOperacao',
                'type' => 'INNER',
                'conditions' => 'TipoOperacao.codigo = LogFaturamentoTeleconsult.codigo_tipo_operacao'
            ),
            array(
                'table' => "{$this->Servico->databaseTable}.{$this->Servico->tableSchema}.{$this->Servico->useTable}",
                'alias' => 'Servico',
                'type' => 'INNER',
                'conditions' => 'Servico.codigo = TipoOperacao.codigo_servico'
            ),
            array(
                'table' => "{$this->ProfissionalTipo->databaseTable}.{$this->ProfissionalTipo->tableSchema}.{$this->ProfissionalTipo->useTable}",
                'alias' => 'ProfissionalTipo',
                'type' => 'INNER',
                'conditions' => 'ProfissionalTipo.codigo = LogFaturamentoTeleconsult.codigo_profissional_tipo'
            ),
        );
        $conditions['LogFaturamentoTeleconsult.codigo_produto'] = array(1,2);
        $conditions['TipoOperacao.codigo_servico'] = array(1,2,3,4);
        $group = array(
            "Produto.descricao",
            "Servico.descricao",
            "CASE
                WHEN ProfissionalTipo.codigo = 1
                THEN ProfissionalTipo.descricao
                ELSE 'OUTROS'
            END",
            "SUBSTRING(CONVERT(VARCHAR,LogFaturamentoTeleconsult.data_inclusao, 120), 1, {$corte_periodo})"
        );
        $order = array(
            'produto DESC',
            'servico DESC',
            'profissional DESC',
            'data_inclusao DESC'
        );

        $dados = $this->find('all', compact('fields', 'joins', 'conditions', 'group', 'order'));
        if (!empty($dados))
            $dados = $this->formatarDadosDasEstatisticas($dados, $filtros['tipo_periodo']);
        return $dados;
    }

    function formatarDadosDasEstatisticas($dados, $tipo_periodo) {
        $retorno = array();
        $lista = array();
        $last  = array();
        $head_title = array();
        $index = 0;
        foreach($dados as &$dado) {
            $objeto= array();
            $valor = array();
            $objeto['name'] = $dado[0]['produto'].' '.$this->firstWord($dado[0]['servico']).' '.$dado[0]['profissional'];
            $valor[trim($dado[0]['data_inclusao'])] = $dado[0]['qtd'];
            if (!isset($last['name']) || $last['name'] != $objeto['name']){
                $index++;
                $lista[$index] = $objeto;
                $lista[$index]['values'][trim($dado[0]['data_inclusao'])]   = $dado[0]['qtd'];
            }else{
                $lista[$index]['values'][trim($dado[0]['data_inclusao'])]   = $dado[0]['qtd'];
            }

            if (!in_array(trim($dado[0]['data_inclusao']), $head_title))
                $head_title[] = trim($dado[0]['data_inclusao']);
            $last = $objeto;

        }

        sort($head_title);

        if ($tipo_periodo == 1 && $head_title) {
            $ano = substr($head_title[0], strlen($head_title[0]) - 4, 4);
            $meses = range(1,12);
            $head_title = array();
            foreach($meses as $mes)
                $head_title[] = str_pad($mes, 2, '0', STR_PAD_LEFT).'/'.$ano;
            foreach($lista as &$valores) {
                foreach($head_title as $title) {
                    if (!array_key_exists($title, $valores['values']))
                        $valores['values'][$title] = 0;
                }
                ksort($valores['values']);
            }
        } elseif($tipo_periodo == 2 && $head_title) {
            $ano = substr($head_title[0], strlen($head_title[0]) - 4, 4);
            $mes = substr($head_title[0], 3, 2);
            $dias = range(1, cal_days_in_month(CAL_GREGORIAN, $mes, $ano));
            $head_title = array();
            $head_title_aux = array();
            foreach($dias as $dia) {
                $head_title[] = str_pad($dia, 2, '0', STR_PAD_LEFT);
                $head_title_aux[] = str_pad($dia, 2, '0', STR_PAD_LEFT).'/'.$mes.'/'.$ano;
            }
            foreach($lista as &$valores) {
                foreach($head_title_aux as $title) {
                    if (!array_key_exists($title, $valores['values']))
                        $valores['values'][$title] = 0;
                }
                ksort($valores['values']);
            }
        } else {
            $horas = range(0,23);
            $data_base = substr($head_title[0], 0, 11);
            $head_title = array();
            $head_title_aux = array();
            foreach($horas as $hora) {
                $head_title[] = str_pad($hora, 2, '0', STR_PAD_LEFT);
                $head_title_aux[] = $data_base.str_pad($hora, 2, '0', STR_PAD_LEFT);
            }
            foreach($lista as &$valores) {
                foreach($head_title_aux as $title) {
                    if (!array_key_exists($title, $valores['values']))
                        $valores['values'][$title] = 0;
                }
                ksort($valores['values']);
            }
        }

        $retorno['headtitle'] = $head_title;
        $retorno['lista']   =& $lista;
        return $retorno;
    }

    function firstWord($palavras) {
        return substr($palavras, 0, strpos($palavras,' '));
    }

    function servicosPeriodo($filtros){
        $this->Cliente = ClassRegistry::init('Cliente');

        $corte_periodo = 7;
        if (isset($filtros['tipo_periodo'])) {
            if ($filtros['tipo_periodo'] == self::TIPO_PERIODO_HORA) {
                $corte_periodo = 13;
            } elseif ($filtros['tipo_periodo'] == self::TIPO_PERIODO_DIARIO) {
                $corte_periodo = 10;
            }
        }
        $group = array(
            "substring(convert(varchar,LogFaturamentoTeleconsult.data_inclusao, 120), 1, {$corte_periodo})",
            'TipoOperacao.codigo_servico'
        );
        if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto']))
            $group[] = 'LogFaturamentoTeleconsult.codigo_produto';
        if (isset($filtros['codigo_profissional_tipo'])) {
            $group[] = 'LogFaturamentoTeleconsult.codigo_profissional_tipo';
        }

        $fields = array(
            "substring(convert(varchar,LogFaturamentoTeleconsult.data_inclusao, 120), 1, {$corte_periodo}) as 'data'",
            'COUNT(LogFaturamentoTeleconsult.codigo_produto) as quantidade'
        );
        $this->bindModel(array('belongsTo' => array(
            'TipoOperacao' => array('foreignKey' => 'codigo_tipo_operacao'),
        )));
        if (isset($filtros['data_inicial'])) {
            $periodo = array(AppModel::dateToDbDate($filtros['data_inicial']).' 00:00:00', AppModel::dateToDbDate($filtros['data_final']).' 23:59:59');
        } else {
            $periodo = array($filtros['ano'].'-01-01 00:00:00', $filtros['ano'].'-12-31 23:59:59');
        }
        $periodo =
        $conditions = array(
            'LogFaturamentoTeleconsult.data_inclusao BETWEEN ? AND ?' => $periodo,
            'TipoOperacao.codigo_servico IS NOT NULL'
        );
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
            $conditions['LogFaturamentoTeleconsult.codigo_cliente'] = $filtros['codigo_cliente'];
        if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto']))
            $conditions['LogFaturamentoTeleconsult.codigo_produto'] = $filtros['codigo_produto'];
        if (isset($filtros['codigo_servico']) && !empty($filtros['codigo_servico']))
            $conditions['TipoOperacao.codigo_servico'] = $filtros['codigo_servico'];
        if (isset($filtros['codigo_profissional_tipo'])) {
            if ($filtros['codigo_profissional_tipo']) {
                $conditions['LogFaturamentoTeleconsult.codigo_profissional_tipo'] = 1;
            } else {
                $conditions['NOT'] = array('LogFaturamentoTeleconsult.codigo_profissional_tipo' => 1);
            }
        }
        if (isset($filtros['tipo_smonline'])) {
            if ($filtros['tipo_smonline'] == self::TIPO_SMONLINE_SOMENTE)
                $conditions['LogFaturamentoTeleconsult.codigo_usuario_inclusao'] = 1;
            if ($filtros['tipo_smonline'] == self::TIPO_SMONLINE_SEM)
                $conditions['LogFaturamentoTeleconsult.codigo_usuario_inclusao !='] = 1;
        }
        if (isset($filtros['tipo_cobranca'])) {
            if ($filtros['tipo_cobranca'] == self::TIPO_COBRANCA_SOMENTE_COBRADO)
                $conditions['TipoOperacao.cobrado'] = 1;
            if ($filtros['tipo_cobranca'] == self::TIPO_COBRANCA_SEM)
                $conditions['TipoOperacao.cobrado !='] = 1;
        }
        if (isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])){
            $conditions['Cliente.codigo_seguradora'] = $filtros['codigo_seguradora'];
        }
        if (isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora'])){
            $conditions['Cliente.codigo_corretora'] = $filtros['codigo_corretora'];
        }

        $joins = array(
            array(
                'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'LogFaturamentoTeleconsult.codigo_cliente = Cliente.codigo',
            ),
        );

        $resultado = $this->find('all', compact('fields', 'joins', 'conditions', 'group'));

        return $this->servicosPeriodoFormatar($filtros, $resultado);
    }

    private function servicosPeriodoFormatar($filtros, $resultado) {
        if (isset($filtros['tipo_periodo']) && $filtros['tipo_periodo'] == self::TIPO_PERIODO_HORA) {
            $horas = array();
            $current_time = AppModel::dateToDbDate2($filtros['data_inicial']).' 00:00:00';
            $final_time = AppModel::dateToDbDate2($filtros['data_final']).' 23:00:00';
            while ($current_time <= $final_time) {
                $horas[$current_time] = array(array('data' => AppModel::dbDateToDate($current_time),'quantidade' => 0));
                $current_time = Date('Y-m-d H:00:00', strtotime('+1 hour', strtotime($current_time)));
            }
            foreach ($resultado as $faturamento) {
                $horas[$faturamento[0]['data'].':00:00'][0]['quantidade'] = $faturamento[0]['quantidade'];
            }
            return $horas;
        } elseif (isset($filtros['tipo_periodo']) && $filtros['tipo_periodo'] == self::TIPO_PERIODO_DIARIO) {
            $dias = array();
            $current_day = AppModel::dateToDbDate2($filtros['data_inicial']).' 00:00:00';
            $final_day = AppModel::dateToDbDate2($filtros['data_final']).' 00:00:00';
            while ($current_day <= $final_day) {
                $dias[substr($current_day,0,10)] = array(array('data' => AppModel::dbDateToDate(substr($current_day,0,10)),'quantidade' => 0));
                $current_day = Date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($current_day)));
            }
            foreach ($resultado as $faturamento) {
                $dias[$faturamento[0]['data']][0]['quantidade'] = $faturamento[0]['quantidade'];
            }
            return $dias;
        } else {
            $meses = array();
            for($mes = 1; $mes <= 12; $mes++) {
                if ((int)$filtros['ano'] == date('Y') && $mes >date('m'))
                    break;
                $meses[$mes] = array(array('data' => str_pad($mes,2,'0',STR_PAD_LEFT).'/'.$filtros['ano'],'quantidade' => 0));
            }

            foreach ($resultado as $faturamento) {
                $mes = (int)substr($faturamento[0]['data'],5,2);
                $meses[$mes][0]['quantidade'] = $faturamento[0]['quantidade'];
            }
            return $meses;
        }
    }

    function subQueryParaClientesQueNaoUtilizaram($mes,$ano){
        $dbo = $this->getDataSource();

        $base_select = array('fields'        => array('top 1 1'),
                             'table'         => $this->useTable,
                             'databaseTable' => $this->databaseTable,
                             'tableSchema'   => $this->tableSchema,
                             'alias'         => $this->name,
                             'limit'         => NULL,
                             'offset'        => NULL,
                             'joins'         => array(),
                             'conditions'    => array('LogFaturamentoTeleconsult.codigo_cliente_pagador = ClienteProdutoServico.codigo_cliente_pagador',
                                                      'month(LogFaturamentoTeleconsult.data_inclusao)'       => $mes,
                                                      'year(LogFaturamentoTeleconsult.data_inclusao)'        => $ano,
                                                      'LogFaturamentoTeleconsult.codigo_usuario_inclusao <>' => 1
                                                     ),
                             'order'         => NULL,
                             'group'         => NULL);

        $subquery = $dbo->buildStatement($base_select,$this);

        return $subquery;
    }

    function atualizaItensPedidos($filtros, $data_inclusao) {
        $filtros['data_inicial'] = AppModel::dateToDbDate($filtros['data_inicial'].' 00:00:00');
        $filtros['data_final'] = AppModel::dateToDbDate($filtros['data_final'].' 23:59:59');
        $this->Pedido =& ClassRegistry::init('Pedido');
        $this->ItemPedido =& ClassRegistry::init('ItemPedido');
        $query_atualizacao = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
            SET
                log_faturamento.codigo_item_pedido = itens_pedidos.codigo
            FROM
                {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
            INNER JOIN {$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}
                ON pedidos.codigo_cliente_pagador = log_faturamento.codigo_cliente_pagador
                 AND pedidos.data_inclusao BETWEEN '".$data_inclusao.".000"."' AND '".$data_inclusao.".999"."'
                 AND pedidos.codigo_servico = '03085'
            INNER JOIN {$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}
                ON itens_pedidos.codigo_pedido = pedidos.codigo AND itens_pedidos.codigo_produto = 1
            WHERE
                log_faturamento.data_inclusao BETWEEN '{$filtros['data_inicial']}' AND '{$filtros['data_final']}'";
        return ($this->query($query_atualizacao) !== false);
    }

    function ajustarFaturamento($filtros) {
        $adicionar_hora = strlen($filtros['data_inicial']) < 11;
        $filtros['data_inicial'] = AppModel::dateToDbDate($filtros['data_inicial'].($adicionar_hora ? ' 00:00:00':''));
        $filtros['data_final'] = AppModel::dateToDbDate($filtros['data_final'].($adicionar_hora ? ' 23:59:59':''));
        $TipoOperacao =& ClassRegistry::init('TipoOperacao');
        $ClienteProduto =& ClassRegistry::init('ClienteProduto');
        $ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
        $query_atualizacao = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
            SET
                log_faturamento.valor = cliente_produto_servico2.valor, log_faturamento.codigo_cliente_pagador = cliente_produto_servico2.codigo_cliente_pagador
            FROM
                {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
            INNER JOIN {$TipoOperacao->databaseTable}.{$TipoOperacao->tableSchema}.{$TipoOperacao->useTable}
                ON tipo_operacao.codigo = log_faturamento.codigo_tipo_operacao AND tipo_operacao.cobrado = 1
            INNER JOIN {$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}
                ON cliente_produto.codigo_cliente = log_faturamento.codigo_cliente AND cliente_produto.codigo_produto = log_faturamento.codigo_produto
            INNER JOIN {$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}
                ON cliente_produto_servico2.codigo_cliente_produto = cliente_produto.codigo AND cliente_produto_servico2.codigo_servico = tipo_operacao.codigo_servico --AND cliente_produto_servico2.codigo_cliente_pagador = log_faturamento.codigo_cliente_pagador
        WHERE
          log_faturamento.data_inclusao BETWEEN '{$filtros['data_inicial']}' AND '{$filtros['data_final']}' AND log_faturamento.codigo_item_pedido IS NULL
        AND (log_faturamento.valor <> cliente_produto_servico2.valor OR log_faturamento.codigo_cliente_pagador <> cliente_produto_servico2.codigo_cliente_pagador)";
        return ($this->query($query_atualizacao) !== false);
    }

    function gerarFaturamentoFicha($ficha, $clienteProdutoServico, $codigo_ficha, $codigo_profissional, $codigos_veiculo, $profissionalExisteNoBanco = false) {
        $this->Cliente =& ClassRegistry::init('Cliente');
        $this->ClienteProdutoServico =& ClassRegistry::init('ClienteProdutoServico');
        if($clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']==''){
            $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador'] = $ficha['Ficha']['codigo_cliente_pagador'];//['codigo'];
            //debug($clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']);die();
        }
        $data = array();
        $data['codigo_produto'] = $ficha['Ficha']['codigo_produto'];
        $data['codigo_cliente'] = $ficha['Ficha']['codigo_cliente'];
        $data['codigo_corporacao'] = $this->Cliente->field('codigo_corporacao', array('codigo'=>$ficha['Ficha']['codigo_cliente']));
        $data['codigo_cliente_pagador'] = $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador'];
        $data['codigo_profissional'] = $codigo_profissional;
        $data['codigo_profissional_tipo'] = $ficha['Ficha']['codigo_profissional_tipo'];
        $data['codigo_ficha'] = $codigo_ficha;


        $data['codigo_tipo_operacao'] = ($profissionalExisteNoBanco ? $this->obterTipoOperacaoLogFaturamento($data['codigo_cliente'], $data['codigo_profissional'], $data['codigo_produto']) : TipoOperacao::TIPO_OPERACAO_CADASTRO);

        if ($data['codigo_tipo_operacao'] == TipoOperacao::ATUALIZACAO_SEM_COBRANCA) {
            $data['valor_premio_minimo'] = 0;
            $data['valor_taxa_bancaria'] = 0;
            $data['valor'] = 0;
        } else {
            $premio_minimo = $this->ClienteProdutoServico->obterPorPagador(
                    Servico::PREMIO_MINIMO, $ficha['Ficha']['codigo_produto'], $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']
            );

            $data['valor_premio_minimo'] = (
                    $premio_minimo ?
                    $premio_minimo :
                    0
            );
            $valor_taxa_bancaria = $this->ClienteProdutoServico->obterPorPagador(
                    Servico::TAXA_BANCARIA, $ficha['Ficha']['codigo_produto'], $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']
            );
            $data['valor_taxa_bancaria'] = (
                    $valor_taxa_bancaria ?
                    $valor_taxa_bancaria :
                    0
            );

            $data['valor'] = $this->ClienteProdutoServico->obterPorPagador(
                    ($profissionalExisteNoBanco ? Servico::ATUALIZACAO_DE_FICHA : Servico::CADASTRO_DE_FICHA), $ficha['Ficha']['codigo_produto'], $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']
            );
        }

        if (isset($codigos_veiculo[0])) {
            $data['codigo_veiculo'] = $codigos_veiculo[0];
        }

        if (isset($codigos_veiculo[1])) {
            $data['codigo_veiculo_carreta'] = $codigos_veiculo[1];
        }
        $data['codigo_carga_valor'] = $ficha['Ficha']['codigo_carga_valor'];
        $data['codigo_carga_tipo'] = $ficha['Ficha']['codigo_carga_tipo'];
        $data['codigo_endereco_cidade_origem'] = $ficha['Ficha']['codigo_endereco_cidade_carga_origem'];
        $data['codigo_endereco_cidade_destino'] = $ficha['Ficha']['codigo_endereco_cidade_carga_destino'];

        $this->create();
        return $this->save($data);
    }

    function gerarFaturamentoFichaScorecard($ficha, $clienteProdutoServico, $codigo_ficha, $codigo_profissional, $codigos_veiculo, $profissionalExisteNoBanco = false) {
        $this->Cliente =& ClassRegistry::init('Cliente');
        $this->ClienteProdutoServico =& ClassRegistry::init('ClienteProdutoServico');
        if($clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']==''){
            $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador'] = $ficha['Ficha']['codigo_cliente_pagador'];//['codigo'];
        }
        $data = array();
        if(isset($ficha['observacao']))
            $data['observacao'] = $ficha['observacao'];
        $data['codigo_produto'] = $ficha['Ficha']['codigo_produto'];
        $data['codigo_cliente'] = $ficha['Ficha']['codigo_cliente'];
        $data['codigo_corporacao'] = $this->Cliente->field('codigo_corporacao', array('codigo'=>$ficha['Ficha']['codigo_cliente']));
        $data['codigo_cliente_pagador']   = $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador'];
        $data['codigo_profissional']      = $codigo_profissional;
        $data['codigo_profissional_tipo'] = @$ficha['Ficha']['codigo_profissional_tipo'];
        if ($data['codigo_profissional_tipo']==''){
            $data['codigo_profissional_tipo'] =$ficha['Profissional']['codigo_profissional_tipo'];
        }

        if (isset($ficha['codigo_usuario_inclusao']) && $ficha['codigo_usuario_inclusao'] ==159) {
            $data['codigo_usuario_inclusao'] =159;
        }

        if( !empty($ficha['Usuario']['codigo_usuario']) )
            $data['codigo_usuario_cliente'] = $ficha['Usuario']['codigo_usuario'];

        $data['codigo_ficha_scorecard'] = $codigo_ficha;

        if(!isset($ficha['codigo_tipo_operacao']))
            $data['codigo_tipo_operacao'] = ($profissionalExisteNoBanco ? $this->obterTipoOperacaoLogFaturamento($data['codigo_cliente'], $data['codigo_profissional'], $data['codigo_produto']) : TipoOperacao::TIPO_OPERACAO_CADASTRO);
        else
            $data['codigo_tipo_operacao'] = $ficha['codigo_tipo_operacao'];

        //Se a ficha foi cadastrada no periodo de 1 mes ela nao sera cobrada
        if( in_array($data['codigo_tipo_operacao'], array( TipoOperacao::TIPO_OPERACAO_ATUALIZACAO ) ) ) {
            $gera_cobranca = $this->ultimoLogFaturamentoProfissionalCliente( $data['codigo_cliente'], $codigo_profissional );
            $data['codigo_tipo_operacao'] = empty($gera_cobranca) ? TipoOperacao::TIPO_OPERACAO_ATUALIZACAO : TipoOperacao::ATUALIZACAO_SEM_COBRANCA;
        }

        if ($data['codigo_tipo_operacao'] == TipoOperacao::ATUALIZACAO_SEM_COBRANCA) {
            $data['valor_premio_minimo'] = 0;
            $data['valor_taxa_bancaria'] = 0;
            $data['valor'] = 0;
        } else {
            $premio_minimo = $this->ClienteProdutoServico->obterPorPagador(
                    Servico::PREMIO_MINIMO, $ficha['Ficha']['codigo_produto'], $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']
            );

            $data['valor_premio_minimo'] = $premio_minimo ? $premio_minimo : 0;
            $valor_taxa_bancaria = $this->ClienteProdutoServico->obterPorPagador(
                    Servico::TAXA_BANCARIA, $ficha['Ficha']['codigo_produto'], $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']
            );
            $data['valor_taxa_bancaria'] = $valor_taxa_bancaria ? $valor_taxa_bancaria : 0;

            $data['valor'] = $this->ClienteProdutoServico->obterPorPagador(
                    ($profissionalExisteNoBanco ? Servico::ATUALIZACAO_DE_FICHA : Servico::CADASTRO_DE_FICHA), $ficha['Ficha']['codigo_produto'], $clienteProdutoServico['ClienteProdutoServico']['codigo_cliente_pagador']
            );
        }

        if (isset($codigos_veiculo[0])) {
            $data['codigo_veiculo'] = $codigos_veiculo[0];
        }
        if (isset($codigos_veiculo[1])) {
            $data['codigo_veiculo_carreta'] = $codigos_veiculo[1];
        }
        if (isset($codigos_veiculo[2])) {
            $data['codigo_veiculo_bitrem'] = $codigos_veiculo[2];
        }


        if(isset($ficha['Ficha']['codigo_carga_valor']))
            $data['codigo_carga_valor'] = $ficha['Ficha']['codigo_carga_valor'];
        if(isset($ficha['Ficha']['codigo_carga_tipo']))
            $data['codigo_carga_tipo'] = $ficha['Ficha']['codigo_carga_tipo'];

        $data['codigo_endereco_cidade_origem'] = $ficha['Ficha']['codigo_endereco_cidade_carga_origem'];
        $data['codigo_endereco_cidade_destino'] = $ficha['Ficha']['codigo_endereco_cidade_carga_destino'];
        $data['codigo_usuario_inclusao'] = $ficha['Ficha']['codigo_usuario_inclusao'];

        if ($data['codigo_tipo_operacao'] == 100) {
            $data['valor_premio_minimo'] = 0;
            $data['valor_taxa_bancaria'] = 0;
            $data['valor'] = 0;
        }

        if ($data['codigo_tipo_operacao'] == 8) {
            $data['valor_premio_minimo'] = 0;
            $data['valor_taxa_bancaria'] = 0;
            $data['valor'] = 0;
        }

        @$ficha['Ficha']['placa'] = trim(str_replace('-','',$ficha['Ficha']['placa']));
        @$ficha['FichaScorecardVeiculo'][1]['Veiculo']['placa'] = strtoupper(trim(str_replace('-','',$ficha['FichaScorecardVeiculo'][1]['Veiculo']['placa'])));
        @$ficha['FichaScorecardVeiculo'][2]['Veiculo']['placa'] = strtoupper(trim(str_replace('-','',$ficha['FichaScorecardVeiculo'][2]['Veiculo']['placa'])));

        if(isset($ficha['Ficha']['placa']))
            $data['placa'] = $ficha['Ficha']['placa'];

        if(isset($ficha['FichaScorecardVeiculo'][1]['Veiculo']['placa']))
            $data['placa_carreta'] = $ficha['FichaScorecardVeiculo'][1]['Veiculo']['placa'];
        if(isset($ficha['FichaScorecardVeiculo'][2]['Veiculo']['placa']))
            $data['placa_veiculo_bitrem'] = $ficha['FichaScorecardVeiculo'][2]['Veiculo']['placa'];




        $this->create();
        if($this->save($data))
            return $this->id;
        else
            return false;
    }

    public function obterTipoOperacaoLogFaturamento($codigo_cliente, $codigo_profissional, $codigo_produto) { 
        $codigo_cliente           = preg_replace('/\D/', '', $codigo_cliente);
        $codigo_profissional      = preg_replace('/\D/', '', $codigo_profissional);
        $tipo_operacao_consulta   = str_replace("'","",TipoOperacao::TIPO_OPERACAO_CONSULTA);
        $data_inclusao            = $this->field('data_inclusao', array(
            'codigo_profissional' => $codigo_profissional, 
            'codigo_cliente'      => $codigo_cliente, 
            'codigo_produto'      => $codigo_produto, 
            'codigo_tipo_operacao'=> array(
                TipoOperacao::TIPO_OPERACAO_CADASTRO, 
                TipoOperacao::TIPO_OPERACAO_ATUALIZACAO, 
                TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA,
                1,2,3,4,6,8,9,10,74,100,108,109,110,111,112,113,114,115,116,120,121,122,123
            )), 'data_inclusao DESC'
        );
        if (empty($data_inclusao)) {
            return TipoOperacao::TIPO_OPERACAO_CADASTRO;
        } else {
            return $data_inclusao < date('Ymd', strtotime('-1 month')) ? TipoOperacao::ATUALIZACAO_SEM_COBRANCA : TipoOperacao::TIPO_OPERACAO_ATUALIZACAO;
        }
    }


    public function logFaturamentoScorecard($filtros, $retorna_param=FALSE) {
        $this->Produto = ClassRegistry::init('Produto');
        $this->Servico = ClassRegistry::init('Servico');
        $this->TipoOperacao = ClassRegistry::init('TipoOperacao');
        $this->ProfissionalTipo = ClassRegistry::init('ProfissionalTipo');
        $this->Cliente = ClassRegistry::init('Cliente');
        $this->Usuario = ClassRegistry::init('Usuario');
        $this->Profissional = ClassRegistry::init('Profissional');
        $this->Veiculo = ClassRegistry::init('Veiculo');
        $this->EnderecoCidade = ClassRegistry::init('EnderecoCidade');
        $this->EnderecoEstado = ClassRegistry::init('EnderecoEstado');
        $this->CargaTipo = ClassRegistry::init('CargaTipo');
        $corte_periodo = 10;

        $fields = array(
            "LogFaturamentoTeleconsult.codigo as codigo",
            "Cliente.razao_social as razao_social",
            // "Status.*",

            "CASE WHEN FichaScorecard.codigo_score_manual = 2 THEN 'Adequado' 
              WHEN FichaScorecard.codigo_score_manual = 7 THEN 'Insuficiente' 
              WHEN FichaScorecard.codigo_score_manual = 8 THEN 'Divergente' 
            END AS status_manual",
            "ParametroScore.nivel as classificacao_motorista",
            "LogFaturamentoTeleconsult.codigo_cliente as codigo_cliente",
            "Usuario.apelido as usuario",
            "TipoOperacao.descricao as tipo_operacao",
            "Profissional.nome as profissional",
            "Profissional.codigo_documento as cpf",
            "SUBSTRING(CONVERT(VARCHAR,LogFaturamentoTeleconsult.data_inclusao, 120), 1, {$corte_periodo}) AS data_inclusao",
            "LogFaturamentoTeleconsult.numero_liberacao as num_consulta",
            "LogFaturamentoTeleconsult.placa as placa",
            "LogFaturamentoTeleconsult.placa_carreta as carreta",
            "LogFaturamentoTeleconsult.placa_veiculo_bitrem as bitrem",
            "(select DESCRICAO from {$this->CargaTipo->databaseTable}.{$this->CargaTipo->tableSchema}.{$this->CargaTipo->useTable}  a
             where a.codigo = LogFaturamentoTeleconsult.codigo_carga_tipo)  AS carga_tipo_descricao",
            "(select descricao from {$this->ProfissionalTipo->databaseTable}.{$this->ProfissionalTipo->tableSchema}.{$this->ProfissionalTipo->useTable} a where a.codigo = LogFaturamentoTeleconsult.codigo_profissional_tipo ) as profissional_tipo",
            "EnderecoCidadeDestino.descricao + '-' + EnderecoEstadoDestino.descricao as endereco_destino",
            "EnderecoCidadeOrigem.descricao + '-' + EnderecoEstadoOrigem.descricao as endereco_origem",
            "LogFaturamentoTeleconsult.observacao as observacao"
        );

        $this->bindModel(array('belongsTo' => array(
            'Produto' => array('foreignKey' => 'codigo_produto'),
            'TipoOperacao' => array('foreignKey' => 'codigo_tipo_operacao'),
            'Servico' => array('foreignKey' => false, 'conditions' => 'TipoOperacao.codigo_servico = Servico.codigo'),
            'ProfissionalTipo' => array('foreignKey' => 'codigo_profissional_tipo'),
            'Cliente' => array('foreignKey' => 'codigo_cliente'),
            'Usuario' => array('foreignKey' => 'codigo_usuario_inclusao'),
            'Profissional' => array('foreignKey' => 'codigo_profissional'),
            'FichaScorecard' => array('foreignKey' => false, 'conditions' => 'FichaScorecard.codigo = LogFaturamentoTeleconsult.codigo_ficha_scorecard'),
            'ParametroScore' => array('foreignKey' => false, 'conditions' => 'ParametroScore.codigo = FichaScorecard.codigo_parametro_score'),
            'EnderecoCidadeOrigem' => array('className' => 'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_origem'),
            'EnderecoCidadeDestino' => array('className' => 'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_destino'),
            'EnderecoEstadoOrigem' => array('className' => 'EnderecoEstado', 'foreignKey' => false, 'conditions' => 'EnderecoCidadeOrigem.codigo_endereco_estado = EnderecoEstadoOrigem.codigo'),
            'EnderecoEstadoDestino' => array('className' => 'EnderecoEstado', 'foreignKey' => false, 'conditions' => 'EnderecoCidadeDestino.codigo_endereco_estado = EnderecoEstadoDestino.codigo'),
        )), false);

        $conditions = array();
        $conditions['LogFaturamentoTeleconsult.codigo_produto'] = array(Produto::SCORECARD);
        if(isset($filtros['codigo_cliente']) && $filtros['codigo_cliente'] > 0) {
            $conditions['LogFaturamentoTeleconsult.codigo_cliente'] = $filtros['codigo_cliente'];
        }
        if(isset($filtros['cpf']) && !empty($filtros['cpf'])) {
            $conditions['Profissional.codigo_documento'] = preg_replace('/[^\d]+/', '', $filtros['cpf']);
        }
        if(isset($filtros['placa']) && !empty($filtros['placa'])) {
            $filtros['placa'] = trim(str_replace('-','',$filtros['placa']));
            $conditions[] = array(
            'OR'=>array(
                'LogFaturamentoTeleconsult.placa'=> $filtros['placa'],
                'LogFaturamentoTeleconsult.placa_carreta'=> $filtros['placa'],
                'LogFaturamentoTeleconsult.placa_veiculo_bitrem'=> $filtros['placa']
            ));
        }
        if(isset($filtros['usuario']) && !empty($filtros['usuario'])) {
            $conditions['Usuario.apelido like '] = '%'.$filtros['usuario'].'%';
        }
        if(isset($filtros['tipos']) && !empty($filtros['tipos'])) {
            $conditions['TipoOperacao.cobrado'] = $filtros['tipos'] == 1 ? 0 : 1;
        }
        if(isset($filtros['tipo_operacao']) && !empty($filtros['tipo_operacao'])) {
            $conditions['TipoOperacao.codigo'] = $filtros['tipo_operacao'];
        }
        if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])) {
            $conditions['LogFaturamentoTeleconsult.data_inclusao >= '] = implode('-',array_reverse(explode('/',$filtros['data_inicial']))) . ' 00:00:00';
        }
        if(isset($filtros['data_final']) && !empty($filtros['data_final'])) {
            $conditions['LogFaturamentoTeleconsult.data_inclusao <= '] = implode('-',array_reverse(explode('/',$filtros['data_final']))) . ' 23:59:59';
        }
        if(isset($filtros['num_consulta']) && !empty($filtros['num_consulta'])) {
            $conditions['LogFaturamentoTeleconsult.numero_liberacao'] = $filtros['num_consulta'];
        }
        $order = 'codigo DESC';
        $limit = 50;
        if( $retorna_param === FALSE ){
            $dados = $this->find('all', compact('fields', 'conditions', 'group', 'order', 'limit'));
            return $dados;
        }else{
            //Retorno para realizar a pesquisa com o Paginate
            return compact('fields', 'conditions', 'group', 'order', 'limit');
        }
    }

    public function obterUltimoCodigoLogFaturamentoPorCliente($codigo_cliente, $codigo_profissional) {
        $dados_log = $this->find('first', array('fields' => array('numero_liberacao'),
            'conditions'=>array( 'codigo_cliente'      => $codigo_cliente,
                                 'codigo_profissional' => $codigo_profissional,
                                 'codigo_produto'      => Produto::SCORECARD),
            'order'     => 'data_inclusao DESC'));
        return $dados_log['LogFaturamentoTeleconsult']['numero_liberacao'];
    }


    public function excluiLogFaturamentoScorecard( $data ){
        $LogFaturamentoExcluido = ClassRegistry::init('LogFaturamentoExcluido');
        $codigo_log = isset($data['LogFaturamentoTeleconsult']['codigo']) ? $data['LogFaturamentoTeleconsult']['codigo'] : NULL;
        $motivo     = $data['LogFaturamentoExcluido']['motivo_exclusao'];
        $codigo_usuario_exclusao = $data['LogFaturamentoExcluido']['codigo_usuario_exclusao'];
        if( $codigo_log ){
            $dados_log  = $this->carregar($codigo_log);
            $dados_log_exclusao = array();
            $dados_log_exclusao[$LogFaturamentoExcluido->name] = $dados_log['LogFaturamentoTeleconsult'];
            $dados_log_exclusao[$LogFaturamentoExcluido->name]['codigo_log_faturamento']=$dados_log['LogFaturamentoTeleconsult']['codigo'];
            $dados_log_exclusao[$LogFaturamentoExcluido->name]['data_exclusao'] = AppModel::dateToDbDate( date("d/m/Y H:i:s") );
            $dados_log_exclusao[$LogFaturamentoExcluido->name]['motivo_exclusao'] = $motivo;
            $dados_log_exclusao[$LogFaturamentoExcluido->name]['codigo_usuario_exclusao'] = $data['LogFaturamentoExcluido']['codigo_usuario_exclusao'];
            unset($dados_log_exclusao[$LogFaturamentoExcluido->name]['codigo']);
            $LogFaturamentoExcluido->query('begin transaction');
            if( $LogFaturamentoExcluido->incluir( $dados_log_exclusao )){
                if( $this->delete( $codigo_log ) ){
                    $LogFaturamentoExcluido->commit();
                    return TRUE;
                }
            }
            $LogFaturamentoExcluido->rollback();
        }
        return false;
    }

    function estatisticas_relatorio_gerencial_pesquisa($dado_usuario,$mes,$ano){
        $this->TipoOperacao = ClassRegistry::init('TipoOperacao');
        $this->Usuario = ClassRegistry::init('Usuario');

         $fields = array(
               "TipoOperacao.mensagem as tipo",
               "count(*) as qtde "  );
         $joins = array(
            array(
                'table' => "{$this->TipoOperacao->databaseTable}.{$this->TipoOperacao->tableSchema}.{$this->TipoOperacao->useTable}",
                'alias' => 'TipoOperacao',
                'type' => 'INNER',
                'conditions' => 'LogFaturamentoTeleconsult.codigo_tipo_operacao = TipoOperacao.codigo',
            ),
            array(
                'table' => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => 'LogFaturamentoTeleconsult.codigo_usuario_inclusao = Usuario.codigo',
            ),
            );
         $conditions['month(LogFaturamentoTeleconsult.data_inclusao)'] = $mes;
         $conditions['year(LogFaturamentoTeleconsult.data_inclusao)']  = $ano;
         //$conditions['FichaScorecard.ativo'] =array(0,1);

         //$conditions['FichaScorecard.codigo_status not'] = array(4, 8);
         $conditions['Usuario.apelido']  = $dado_usuario;
         $group = array('TipoOperacao.mensagem');

        return $this->find('all', compact('conditions','joins','fields','group'));

    }

    function log_faturamento_por_codigo($codigo_faturamento){
        $conditions['codigo'] = $codigo_faturamento;
        return $this->find('all',compact('conditions'));
    }

    public function ultimoLogFaturamentoProfissionalCliente( $codigo_cliente, $codigo_profissional ) {
        $dados_log   = $this->find('count', array(
            'conditions'=> array(
                    'codigo_cliente'      => $codigo_cliente,
                    'codigo_profissional' => $codigo_profissional,
                    'codigo_produto'      => Produto::SCORECARD,
                    'DATEDIFF(d, data_inclusao, getdate()) <' => 31,
                    'codigo_tipo_operacao'=> array(
                        TipoOperacao::TIPO_OPERACAO_CADASTRO,
                        TipoOperacao::TIPO_OPERACAO_ATUALIZACAO,
                        TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA
                    )
                )
            )
        );
        return $dados_log;
    }

    public function converteFiltroEmConditionLogConsultas($filtros){
        $conditions = array();
        if(isset($filtros['nro_consulta']) && !empty($filtros['nro_consulta']))
          $conditions['LogFaturamentoTeleconsult.codigo'] = $filtros['nro_consulta'];

        if(isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
          $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];

        if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial']) && isset($filtros['data_final']) && !empty($filtros['data_final']))
          $conditions['LogFaturamentoTeleconsult.data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateToDbDate($filtros['data_inicial'].' 00:00'), AppModel::dateToDbDate($filtros['data_final'].' 23:59'));

        if(isset($filtros['cpf_profissional']) && !empty($filtros['cpf_profissional']))
          $conditions['ProfissionalLog.codigo_documento'] = $filtros['cpf_profissional'];
        
        if(isset($filtros['codigo_documento']) && !empty($filtros['codigo_documento']))
          $conditions['Profissional.codigo_documento'] = $filtros['codigo_documento'];

        if(isset($filtros['codigo_tipo_profissional']) && !empty($filtros['codigo_tipo_profissional'])){
          if ($filtros['codigo_tipo_profissional']=='1'){
             $conditions['LogFaturamentoTeleconsult.codigo_profissional_tipo'] = '1';
          }else{
             $conditions['LogFaturamentoTeleconsult.codigo_profissional_tipo <>'] = '1';
          }
        }
        if(isset($filtros['codigo_tipo_operacao']) && !empty($filtros['codigo_tipo_operacao'])){
             $conditions['LogFaturamentoTeleconsult.codigo_tipo_operacao'] = $filtros['codigo_tipo_operacao'];
        }
        if(isset($filtros['nome_profissional']) && !empty($filtros['nome_profissional']))
          $conditions['ProfissionalLog.nome LIKE'] = '%'.$filtros['nome_profissional'].'%';

        if(isset($filtros['cpf_proprietario']) && !empty($filtros['cpf_proprietario']))
          $conditions['ProprietarioLog.codigo_documento'] = $filtros['cpf_proprietario'];

        if(isset($filtros['nome_proprietario']) && !empty($filtros['nome_proprietario']))
          $conditions['ProprietarioLog.nome_razao_social LIKE'] = '%'.$filtros['nome_proprietario'].'%';

        if(isset($filtros['placa']) && !empty($filtros['placa'])){
          $conditions['LogFaturamentoTeleconsult.placa like '] = '%'.$filtros['placa'].'%';
          $conditions["'1'='1' OR LogFaturamentoTeleconsult.placa_carreta like "] = '%'.$filtros['placa'].'%';
          $conditions["'1'='1' OR LogFaturamentoTeleconsult.placa_veiculo_bitrem like "] = '%'.$filtros['placa'].'%';
        }
        if(isset($filtros['usuario']) && !empty($filtros['usuario']))
          $conditions['Usuario.apelido'] = $filtros['usuario'];

         if(isset($filtros['tipo_faturamento']) && !empty($filtros['tipo_faturamento'])){
            if ($filtros['tipo_faturamento']==0){
                $conditions['LogFaturamentoTeleconsult.valor >'] = 0;
            }else{
                $conditions['LogFaturamentoTeleconsult.valor <='] = 0;
            }
        }

        return $conditions;
    }

    function listarUltilizacaoServicos($findType, $options) {
        $this->bindModel(array('belongsTo' => array(
            'ClientePagador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_pagador'),
            'ClienteUtilizador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente'),
            'Produto' => array('foreignKey' => 'codigo_produto'),
            'TipoOperacao' => array('foreignKey' => 'codigo_tipo_operacao'),
            'Servico' => array('foreignKey' => false, 'conditions' => 'Servico.codigo = TipoOperacao.codigo_servico'),
            'Seguradora' => array('foreignKey' => false, 'conditions' => 'Seguradora.codigo = ClientePagador.codigo_seguradora'),
            'Gestor' => array('foreignKey' => false, 'conditions' => 'Gestor.codigo = ClientePagador.codigo_gestor'),
            'EnderecoRegiao' => array('foreignKey' => false, 'conditions' => 'EnderecoRegiao.codigo = ClientePagador.codigo_endereco_regiao'),
            'Corretora' => array('foreignKey' => false, 'conditions' => 'Corretora.codigo = ClientePagador.codigo_corretora'),
        )));

        $options['fields'] = array(
            'Seguradora.codigo AS codigo_seguradora',
            'Gestor.codigo AS codigo_gestor',
            'EnderecoRegiao.codigo AS codigo_endereco_regiao',
            'Corretora.codigo AS codigo_corretora',
            'LogFaturamentoTeleconsult.codigo_cliente_pagador AS codigo_cliente_pagador',
            'ClientePagador.razao_social AS razao_social_pagador',
            'LogFaturamentoTeleconsult.codigo_cliente AS codigo_cliente_utilizador',
            'ClienteUtilizador.razao_social AS razao_social_utilizador',
            'Produto.descricao AS produto_descricao',
            'Servico.descricao AS servico_descricao',
            'TipoOperacao.cobrado AS cobrado',
            '(CASE WHEN
                LogFaturamentoTeleconsult.codigo_usuario_inclusao = 1
                THEN 1
                ELSE 0
                END) AS online',
            'LogFaturamentoTeleconsult.data_inclusao',
            'COUNT(*) AS total',
            'SUM(LogFaturamentoTeleconsult.valor) AS precoSomado'
        );
        $options['group'] = array(
            'LogFaturamentoTeleconsult.codigo_cliente_pagador',
            'Seguradora.codigo',
            'Gestor.codigo',
            'EnderecoRegiao.codigo',
            'Corretora.codigo',
            'ClientePagador.razao_social',
            'LogFaturamentoTeleconsult.codigo_cliente',
            'ClienteUtilizador.razao_social',
            'Produto.descricao',
            'Servico.descricao',
            'TipoOperacao.cobrado',
            'LogFaturamentoTeleconsult.codigo_usuario_inclusao',
            'LogFaturamentoTeleconsult.data_inclusao'
        );        
        if ( $findType != 'count' )
            $options['order'] = 'LogFaturamentoTeleconsult.data_inclusao ASC';
        $query = $this->find('sql', $options);
        if ($findType == 'count') {
            $count = $this->query("SELECT COUNT(1/1) AS qtd FROM ({$query}) AS contagem");
            return $count[0][0]['qtd'];
        } elseif( $findType == 'sql') {
            return $query;
        } else {
            return $this->query($query);            
        }
    }

    public function paginate( $conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array() ) {
        if( isset( $extra['method'] ) && $extra['method'] == 'listagem_utilizacao_servicos' ){
            return $this->listarUltilizacaoServicos('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
        }
        if(isset($extra['joins']))
            $joins = $extra['joins'];
        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
    }

    public function paginateCount( $conditions = null, $recursive = 0, $extra = array() ) {
        if( isset( $extra['method'] ) && $extra['method'] == 'listagem_utilizacao_servicos' ){
            return $this->listarUltilizacaoServicos('count', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
        }
        if(isset($extra['joins']))
            $joins = $extra['joins'];
        return $this->find('count', compact('conditions', 'recursive', 'joins'));
    }


    public function verificaConsulta6horas( $codigo_profissional, $codigo_cliente, $codigo_produto ){
        $conditions['codigo_cliente']       = $codigo_cliente;
        $conditions['codigo_profissional']  = $codigo_profissional;
        $conditions['codigo_produto']       = $codigo_produto;
        $conditions['codigo_tipo_operacao'] = array(1,2,3,4,6,9,10,74,89,100,108,109,110,111,112,113,114,115,116,125);
        $conditions['data_inclusao BETWEEN ? AND ? '] = array( date('Ymd H:i:s', strtotime("-6 Hours")), date('Ymd H:i:s') );
        $order = 'data_inclusao DESC';
        $registro  = $this->find('first', compact('conditions', 'order'));
        return $registro;
    }

    function incluirLogFaturamento( $dados ) {
        return parent::incluir( $dados );
    }

}