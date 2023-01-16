<?php
App::import('Model', 'LogFaturamentoDicem');
App::import('Model', 'ClienteSubTipo');
App::import('Model', 'Departamento');
App::import('Model', 'Uperfil');

class Cliente extends AppModel
{

    var $name = 'Cliente';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cliente';
    var $primaryKey = 'codigo';
    var $displayField = 'nome_fantasia';
    var $actsAs = array('Secure', 'SincronizarCodigoDocumento', 'Loggable' => array('foreign_key' => 'codigo_cliente'), 'InscricaoEstadual');
    var $validate = array(
        'tipo_unidade' => array(
            'rule' => array('inList', array('F', 'O')),
            'message' => 'Informe o Tipo da Unidade',
        ),
        'codigo_documento' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o documento',
            ),
            'isUnique' => array(
                'rule' => 'unicoCnpj',
                'message' => 'CNPJ já existente na base',
            ),
            'documentoValido' => array(
                'rule' => 'documentoValido',
                'message' => 'CNPJ é invalido!',
                'on' => 'create'
            )
        ),
        'inscricao_estadual' => array(
            'rule' => 'validaInscricaoEstadual',
            'message' => 'Inscrição Estadual inválida',
        ),
        'ccm' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Inscrição Municipal',
        ),
        'razao_social' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Razão Social',
        ),
        'codigo_regime_tributario' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Regime Tributário',
        ),
        // 'codigo_gestor' => array(
        // 	'rule' => 'notEmpty',
        // 	'message' => 'Informe o Gestor',
        // ),
        'cnae' => array(
            'rule' => 'cnaeValido',
            'message' => 'O Cnae informado é invalido'
        ),
        'ativo' => array(
            'rule' => 'unicoAtivo',
            'message' => 'Já existe outro código com mesmo CNPJ ativo',
            'on' => 'update',
        ),
        'codigo_externo' => array(
            // 'notEmpty' => array(
            // 	'rule' => 'notEmpty',
            // 	'message' => 'Informe o Código Externo',
            // 	),
            'isUnique' => array(
                'rule' => 'unicoCodigoExterno',
                'message' => 'Código Externo já existente',
            ),
        ),
        'codigo_medico_pcmso' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Médico Coordenador',
        ),
    );

    const SUBTIPO_EMBARCADOR = 1;
    const SUBTIPO_TRANSPORTADOR = 4;
    const SENTIDO_BUONNY_GUARDIAN = 1;
    const SENTIDO_GUARDIAN_BUONNY = 2;

    public function unbindAll()
    {
        $tiposDeJoins = array(
            'hasMany',
            'hasOne',
            'belongsTo',
            'hasAndBelongsToMany'
        );

        foreach ($tiposDeJoins as $join) {
            $models = array_keys($this->$join);
            $this->unbindModel(array($join => $models));
        }
    }

    function bindCorretora()
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Corretora' => array(
                    'class' => 'Corretora',
                    'foreignKey' => 'codigo_corretora'
                )
            )
        ));
    }

    function unbindCorretora()
    {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Corretora'
            )
        ));
    }

    function bindEnderecoRegiao()
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoRegiao' => array(
                    'class' => 'EnderecoRegiao',
                    'foreignKey' => 'codigo_endereco_regiao'
                )
            )
        ));
    }

    function bindGestor()
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'class' => 'Usuario',
                    'foreignKey' => 'codigo_gestor'
                )
            )
        ));
    }

    function bindGestorContrato()
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'class' => 'Usuario',
                    'foreignKey' => 'codigo_gestor_contrato'
                )
            )
        ));
    }

    function bindGestorOperacao()
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'class' => 'Usuario',
                    'foreignKey' => 'codigo_gestor_operacao'
                )
            )
        ));
    }

    function unbindEnderecoRegiao()
    {
        $this->unbindModel(array(
            'belongsTo' => array(
                'EnderecoRegiao'
            )
        ));
    }

    function bindEnderecoComercial()
    {
        $this->bindModel(array(
            'hasOne' => array(
                'ClienteEndereco' => array(
                    'class' => 'ClienteEndereco',
                    'foreignKey' => false,
                    'conditions' => array(
                        'Cliente.codigo = ClienteEndereco.codigo_cliente',
                        'ClienteEndereco.codigo_tipo_contato = 2'
                    )
                ),
            )
        ));
    }

    function unicoCnpj()
    {
        $count = 0;
        $conditions = array(
            "{$this->name}.codigo_empresa" => $_SESSION['Auth']['Usuario']['codigo_empresa'],
            "{$this->name}.ativo" => 1,
            "{$this->name}.codigo_documento" => $this->data[$this->name]['codigo_documento']
        );
        if (isset($this->data[$this->name]['codigo'])) {
            $conditions['codigo !='] = $this->data[$this->name]['codigo'];
        }
        if (!empty($this->data[$this->name]['ativo'])) {
            $count = $this->find(
                'count',
                array(
                    'conditions' => $conditions
                )
            );
        }
        if ($count) {
            return false;
        } else {
            return true;
        }
    }

    function unicoCodigoExterno()
    {
        $count = 0;
        $conditions = array(
            "{$this->name}.codigo_empresa" => $_SESSION['Auth']['Usuario']['codigo_empresa'],
            "{$this->name}.ativo" => 1,
            "{$this->name}.codigo_externo" => $this->data[$this->name]['codigo_externo'],
            "{$this->name}.codigo_externo !=" => ''
        );
        if (isset($this->data[$this->name]['codigo'])) {
            $conditions['codigo !='] = $this->data[$this->name]['codigo'];
        }
        if (!empty($this->data[$this->name]['ativo'])) {
            $count = $this->find(
                'count',
                array(
                    'conditions' => $conditions
                )
            );
        }
        if ($count) {
            return false;
        } else {
            return true;
        }
    }

    function unicoAtivo()
    {
        if ($this->data[$this->name]['ativo'] == true) {
            $conditions = array(
                "{$this->name}.codigo_documento" => $this->data[$this->name]['codigo_documento'],
                "{$this->name}.ativo" => 1,
                'NOT' => array("{$this->name}.codigo" => $this->data[$this->name]['codigo'])
            );
            $outro_ativo = $this->find('count', array('recursive' => -1, 'conditions' => $conditions));
            return ($outro_ativo == false);
        }
        return true;
    }

    function documentoValido()
    {
        $model_documento = &ClassRegistry::init('Documento');
        $codigo_documento = $this->data[$this->name]['codigo_documento'];
        if ($this->data[$this->name]['tipo_unidade'] == 'O') return true;
        if ($model_documento->isCPF($codigo_documento) == false && $model_documento->isCNPJ($codigo_documento) == false)
            return false;
        else
            return true;
    }

    function cnaeValido()
    {

        if (!isset($this->data[$this->name]['cnae']) || empty($this->data[$this->name]['cnae']) || $this->data[$this->name]['cnae'] == ' ') {
            return true;
        } else {
            $cnae = $this->data[$this->name]['cnae'];
        }

        $model_cnae = &ClassRegistry::init('Cnae');

        $result = $model_cnae->find('count', array(
            'conditions' => array(
                'cnae' => $cnae
            )
        ));

        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }

    function carregar($codigo, $recursive = 1)
    {
        return $this->find('first', array('conditions' => array($this->name . '.codigo' => $codigo), 'recursive' => $recursive));
    }

    function bindClienteLog()
    {
        $this->bindModel(array(
            'hasMany' => array(
                'ClienteLog' => array(
                    'class' => 'ClienteLog',
                    'foreignKey' => 'codigo_cliente'
                )
            )

        ));
    }

    function unbindClienteLog()
    {
        $this->unbindModel(array(
            'hasMany' => array(
                'ClienteLog'
            )
        ));
    }

    function bindClienteEndereco()
    {
        $this->bindModel(array(
            'hasMany' => array(
                'ClienteEndereco' => array(
                    'class' => 'ClienteEndereco',
                    'foreignKey' => 'codigo_cliente'
                )
            )

        ));

        $this->ClienteEndereco->bindModel(array(
            'belongsTo' => array(
                'Cliente' => array(
                    'className' => 'Cliente',
                    'foreignKey' => 'codigo'
                )
            )
        ));
    }


    function bindUsuario()
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'class' => 'Usuario',
                    'foreignKey' => 'codigo_usuario_inclusao'
                )
            )
        ));
    }

    function unbindUsuario()
    {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Usuario'
            )
        ));
    }

    function converteFiltroEmCondition($data, $condition_vazia_bloqueada = false, $duplicados = false)
    {

        $conditions = array();
        if (!empty($data['codigo']))
            $conditions['Cliente.codigo'] = $data['codigo'];
        if (!empty($data['codigo_cliente']))
            $conditions['Cliente.codigo'] = $data['codigo_cliente'];
        if (!empty($data['razao_social']))
            $conditions['Cliente.razao_social like'] = '%' . $data['razao_social'] . '%';
        if (!empty($data['codigo_documento']))
            $conditions['Cliente.codigo_documento like'] = '%' . str_replace(array('.', '/', '-', ''), '', $data['codigo_documento']) . '%';
        if (!empty($data['cliente_vip']))
            $conditions['ClienteProdutoVip.cliente_vip'] = $data['cliente_vip'];
        if (!empty($data['codigo_corretora']))
            $conditions['Cliente.codigo_corretora'] = $data['codigo_corretora'];
        if (!empty($data['codigo_gestor']))
            $conditions['Cliente.codigo_gestor'] = $data['codigo_gestor'];
        if (!empty($data['codigo_gestor_contrato']))
            $conditions['Cliente.codigo_gestor_contrato'] = $data['codigo_gestor_contrato'];
        if (!empty($data['codigo_gestor_operacao']))
            $conditions['Cliente.codigo_gestor_operacao'] = $data['codigo_gestor_operacao'];
        if (!empty($data['ultima_atualizacao']))
            $conditions['UltimaAlteracao.data_inclusao <='] = AppModel::dateToDbDate2($data['ultima_atualizacao']);
        if (isset($data['ativo']) && (!empty($data['ativo']) || $data['ativo'] == '0'))
            $conditions['Cliente.ativo'] = $data['ativo'];
        if (!empty($data['inscricao_estadual']))
            $conditions['Cliente.inscricao_estadual like'] = $data['inscricao_estadual'] . '%';
        if (!empty($data['codigo_endereco_regiao']))
            $conditions['Cliente.codigo_endereco_regiao'] = $data['codigo_endereco_regiao'];
        if (isset($data['data_inicio']) && !empty($data['data_inicio']))
            $conditions[$this->name . '.data_inclusao >'] = AppModel::dateToDbDate2($data['data_inicio']) . ' 00:00:00.0';
        if (isset($data['data_fim']) && !empty($data['data_fim']))
            $conditions[$this->name . '.data_inclusao <'] = AppModel::dateToDbDate2($data['data_fim']) . ' 23:59:59.997';
        if ($duplicados) {
            if (isset($data['codigo_cliente_pagador']) && !empty($data['codigo_cliente_pagador']))
                $conditions[] = "ClienteProdutoServicoBSAT.codigo_cliente_pagador = " . $data['codigo_cliente_pagador'] . " OR ClienteProdutoServicoTLC.codigo_cliente_pagador = " . $data['codigo_cliente_pagador'];
        } else {
            if (isset($data['codigo_cliente_pagador']) && !empty($data['codigo_cliente_pagador']))
                $conditions['ClienteProdutoServico2.codigo_cliente_pagador'] = $data['codigo_cliente_pagador'];
        }
        if (isset($data['nome_fantasia']) && !empty($data['nome_fantasia']))
            $conditions['Cliente.nome_fantasia like'] =  '%' . $data['nome_fantasia'] . '%';
        if (isset($data['regiao_tipo_faturamento']) && (!empty($data['regiao_tipo_faturamento']) || $data['regiao_tipo_faturamento'] == '0'))
            $conditions['Cliente.regiao_tipo_faturamento'] = $data['regiao_tipo_faturamento'];
        if (isset($data['somente_buonnysat']) && $data['somente_buonnysat']) {
            $ClienteProduto = &ClassRegistry::init('ClienteProduto');
            $conditions[] = "EXISTS(SELECT TOP 1 1 FROM {$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable} ClienteProduto WHERE ClienteProduto.codigo_cliente = Cliente.codigo AND ClienteProduto.codigo_motivo_bloqueio IN (1,8) AND ClienteProduto.codigo_produto=82)";
        }
        if (count($conditions) == 0) {
            if ($condition_vazia_bloqueada)
                $conditions = array('Cliente.codigo' => null);
        }

        if (isset($data['ano_mes']) && !empty($data['ano_mes'])) {
            $conditions = array();
            if ($data['novos']) {
                $conditions['Cliente.data_inclusao BETWEEN ? AND ?'] = Comum::periodoMensal($data['ano_mes']);
                $conditions[] = "(Cliente.data_inativacao IS NULL OR LEFT(CONVERT(VARCHAR, Cliente.data_inativacao, 120),7) <> LEFT(CONVERT(VARCHAR, Cliente.data_inclusao, 120),7))";
            } else {
                $conditions['Cliente.data_inativacao BETWEEN ? AND ?'] = Comum::periodoMensal($data['ano_mes']);
            }
        }

        $authUsuario = $_SESSION['Auth'];

        if (!empty($authUsuario['Usuario']['codigo_corretora']))
            $conditions['Cliente.codigo_corretora'] = $authUsuario['Usuario']['codigo_corretora'];
        if (!empty($authUsuario['Usuario']['codigo_filial']))
            $conditions['Cliente.codigo_endereco_regiao'] = $authUsuario['Usuario']['codigo_filial'];
        return $conditions;
    }

    function listar($conditions = null, $limit = null)
    {
        return $this->find('all', array('conditions' => $conditions, 'limit' => $limit));
    }

    function lista_por_cliente($codigo, $bloqueado = false, $unidade_fiscal = null)
    {

        $this->GrupoEconomico = &ClassRegistry::init('GrupoEconomico'); //GrupoEconomico

        $this->GrupoEconomico->virtualFields = array( //GrupoEconomico
            'razao_social' => 'CONCAT(Cliente.codigo, \' - \', Cliente.nome_fantasia)'
        );

        //monta um filtro para pegar somente as unidades fiscais
        $where_fiscal = ''; //GrupoEconomico
        if (!is_null($unidade_fiscal)) {
            $where_fiscal = " AND Cliente.e_tomador <> '1' ";
        }

        $unidades = $this->GrupoEconomico->find(
            'list',
            array(
                'joins' => array(
                    array(
                        'table' => 'grupos_economicos_clientes',
                        'alias' => 'GrupoEconomicoCliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
                        )
                    ),
                    array(
                        'table' => 'cliente',
                        'alias' => 'Cliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente AND Cliente.ativo = 1' . $where_fiscal
                        )
                    ),
                ),
                'conditions' => array(
                    "GrupoEconomico.codigo_cliente " . $this->rawsql_codigo_cliente($codigo)
                ),
                'fields' => array('Cliente.codigo', 'razao_social'),
                'order' => 'Cliente.nome_fantasia'
            )
        );

        return $unidades; //GrupoEconomico

    } //GrupoEconomico


    /**
     * 
     */
    function lista_por_pagador($codigo, $bloqueado = false)
    {
        $this->GrupoEconomico = &ClassRegistry::init('GrupoEconomico');

        // $this->GrupoEconomico->virtualFields = array(
        // 	'razao_social' => 'CONCAT(Cliente.codigo, \' - \', Cliente.nome_fantasia)'
        // 	);

        $unidades = $this->GrupoEconomico->find(
            'all',
            array(
                // $unidades = $this->GrupoEconomico->find('sql', array(
                'joins' => array(
                    array(
                        'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                        'alias' => 'GrupoEconomicoCliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
                        )
                    ),
                    array(
                        'table' => 'RHHealth.dbo.cliente',
                        'alias' => 'Cliente',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente AND Cliente.ativo = 1'
                        )
                    ),
                    array(
                        'table' => 'RHHealth.dbo.cliente_produto_servico2',
                        'alias' => 'AlocacaoCliProdServico2',
                        'type' => 'LEFT',
                        'conditions' => array('AlocacaoCliProdServico2.codigo_cliente_pagador = Cliente.codigo')
                    ),
                    array(
                        'table' => 'RHHealth.dbo.cliente_produto_servico2',
                        'alias' => 'MatrizCliProdServico2',
                        'type' => 'LEFT',
                        'conditions' => array('MatrizCliProdServico2.codigo_cliente_pagador = Cliente.codigo')
                    ),
                ),
                'conditions' => array(
                    "[GrupoEconomico].[codigo_cliente]" => $codigo,
                    "ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IS NOT NULL"
                ),
                'fields' => array('Cliente.codigo', 'Cliente.nome_fantasia'),
                'group' => 'Cliente.codigo, Cliente.nome_fantasia',
                'order' => 'Cliente.nome_fantasia'
            )
        );

        // debug($unidades);exit;

        return $unidades;
    } //fim lista por pagador

    function buscaPorCodigo($codigo_cliente, $fields = null)
    {
        $conditions = array('Cliente.codigo' => $codigo_cliente);
        return $this->find('first', compact('conditions', 'fields'));
    }

    public function listarClientesDuplicados($conditions = array())
    {

        $ClienteProdutoServico2      = ClassRegistry::init('ClienteProdutoServico2');
        $ClienteProduto             = ClassRegistry::init('ClienteProduto');
        $ItemPedido                    = ClassRegistry::init('ItemPedido');
        $Pedido                        = ClassRegistry::init('Pedido');
        $LogFaturamentoTeleconsult     = ClassRegistry::init('LogFaturamentoTeleconsult');
        $MotivoBloqueio             = ClassRegistry::init('MotivoBloqueio');

        $dbo = $this->getDataSource();

        $group = array(
            "Cliente.codigo_documento",
        );

        $fields = array(
            "count(*) as conta",
            "Cliente.codigo_documento",
        );

        $duplicados = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => $this->databaseTable . '.' . $this->tableSchema . '.' . $this->useTable,
                'alias' => '[Cliente]',
                'limit' => null,
                'offset' => null,
                'order' => null,
                'conditions' => array('codigo_documento <> ' => '00000000000000'),
                'group' => $group,
            ),
            $this
        );

        $cliente_produto_servico_tlc = $dbo->buildStatement(
            array(
                'fields' => array('DISTINCT codigo_cliente_pagador', 'codigo_cliente', 'MotivoBloqueio.descricao', 'ClienteProduto.codigo_produto'),
                'table' => $ClienteProdutoServico2->databaseTable . '.' . $ClienteProdutoServico2->tableSchema . '.' . $ClienteProdutoServico2->useTable,
                'alias' => '[ClienteProdutoServico2]',
                'joins' => array(
                    array(
                        'table' => $ClienteProduto->databaseTable . '.' . $ClienteProduto->tableSchema . '.' . $ClienteProduto->useTable,
                        'alias' => 'ClienteProduto',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto',
                        ),
                    ),
                    array(
                        'table' => $MotivoBloqueio->databaseTable . '.' . $MotivoBloqueio->tableSchema . '.' . $MotivoBloqueio->useTable,
                        'alias' => 'MotivoBloqueio',
                        'type' => 'INNER',
                        'conditions' => array(
                            'MotivoBloqueio.codigo = ClienteProduto.codigo_motivo_bloqueio',
                        ),
                    ),
                ),

                'limit' => null,
                'offset' => null,
                'order' => null,
                'conditions' => 'ClienteProduto.codigo_produto IN (1,2)',
                'group' => null,
            ),
            $this
        );

        $cliente_produto_servico_bsat = $dbo->buildStatement(
            array(
                'fields' => array('DISTINCT codigo_cliente_pagador', 'codigo_cliente', 'MotivoBloqueio.descricao', 'ClienteProduto.codigo_produto'),
                'table' => $ClienteProdutoServico2->databaseTable . '.' . $ClienteProdutoServico2->tableSchema . '.' . $ClienteProdutoServico2->useTable,
                'alias' => '[ClienteProdutoServico2]',
                'joins' => array(
                    array(
                        'table' => $ClienteProduto->databaseTable . '.' . $ClienteProduto->tableSchema . '.' . $ClienteProduto->useTable,
                        'alias' => 'ClienteProduto',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto',
                        ),
                    ),
                    array(
                        'table' => $MotivoBloqueio->databaseTable . '.' . $MotivoBloqueio->tableSchema . '.' . $MotivoBloqueio->useTable,
                        'alias' => 'MotivoBloqueio',
                        'type' => 'INNER',
                        'conditions' => array(
                            'MotivoBloqueio.codigo = ClienteProduto.codigo_motivo_bloqueio',
                        ),
                    ),
                ),

                'limit' => null,
                'offset' => null,
                'order' => null,
                'conditions' => 'ClienteProduto.codigo_produto = 82',
                'group' => null,
            ),
            $this
        );


        $itens_pedidos = $dbo->buildStatement(
            array(
                'fields' => array('COUNT(*) AS itens', 'Pedido.codigo_cliente_pagador'),
                'table' => $ItemPedido->databaseTable . '.' . $ItemPedido->tableSchema . '.' . $ItemPedido->useTable,
                'alias' => '[ItemPedido]',
                'joins' => array(
                    array(
                        'table' => $Pedido->databaseTable . '.' . $Pedido->tableSchema . '.' . $Pedido->useTable,
                        'alias' => 'Pedido',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Pedido.codigo = ItemPedido.codigo_pedido',
                        ),
                    ),
                ),
                'limit' => null,
                'offset' => null,
                'order' => null,
                'conditions' => array('DATEDIFF(month,ItemPedido.data_inclusao ,CURRENT_TIMESTAMP) between ? and ?' => array(0, 5)),
                'group' => 'Pedido.codigo_cliente_pagador',
            ),
            $this
        );


        $logfaturamento = $dbo->buildStatement(
            array(
                'fields' => array('COUNT(*) AS logs', 'codigo_cliente_pagador'),
                'table' => $LogFaturamentoTeleconsult->databaseTable . '.' . $LogFaturamentoTeleconsult->tableSchema . '.' . $LogFaturamentoTeleconsult->useTable,
                'alias' => '[LogFaturamentoTeleconsult]',
                'limit' => null,
                'offset' => null,
                'order' => null,
                'conditions' => array('DATEDIFF(month,data_inclusao ,CURRENT_TIMESTAMP) between ? and ?' => array(0, 5)),
                'group' => 'codigo_cliente_pagador',
            ),
            $this
        );


        $clientes = $dbo->buildStatement(
            array(
                'fields' => array(
                    'Cliente.codigo',
                    'Cliente.codigo_documento',
                    'Cliente.razao_social',
                    "CASE WHEN Cliente.ativo = 'true' THEN 'Ativo' ELSE 'Inativo' END Status",
                    "ClienteProdutoServicoTLC.descricao TLCMotivo",
                    "ClienteProdutoServicoTLC.codigo_produto TLCProduto",
                    'ClienteProdutoServicoTLC.codigo_cliente_pagador TLCPagador',
                    "ClienteProdutoServicoBSAT.descricao BSATMotivo",
                    "ClienteProdutoServicoBSAT.codigo_produto BSATProduto",
                    'ClienteProdutoServicoBSAT.codigo_cliente_pagador BSATPagador',
                    "CASE WHEN Itens.itens > 0 THEN 'SIM' ELSE 'NAO' END itens",
                    "CASE WHEN LogsFaturamento.logs > 0 THEN 'SIM' ELSE 'NAO' END logs"
                ),
                'table' => $this->databaseTable . '.' . $this->tableSchema . '.' . $this->useTable,
                'alias' => '[Cliente] WITH (NOLOCK)',
                'joins' => array(
                    array(
                        'table' => "({$duplicados})",
                        'alias' => 'Duplicados',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Duplicados.codigo_documento = Cliente.codigo_documento',
                            'Duplicados.conta > 1',
                        ),
                    ),
                    array(
                        'table' => "({$cliente_produto_servico_tlc})",
                        'alias' => 'ClienteProdutoServicoTLC',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ClienteProdutoServicoTLC.codigo_cliente = Cliente.codigo'
                        ),
                    ),
                    array(
                        'table' => "({$cliente_produto_servico_bsat})",
                        'alias' => 'ClienteProdutoServicoBSAT',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ClienteProdutoServicoBSAT.codigo_cliente = Cliente.codigo'
                        ),
                    ),
                    array(

                        'table' => "({$itens_pedidos})",
                        'alias' => 'Itens',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Itens.codigo_cliente_pagador = Cliente.codigo'
                        ),
                    ),
                    array(

                        'table' => "({$logfaturamento})",
                        'alias' => 'LogsFaturamento',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'LogsFaturamento.codigo_cliente_pagador = Cliente.codigo'
                        ),
                    ),
                ),
                'conditions' => $conditions,
                'limit' => null,
                'offset' => null,
                'order' => array('Cliente.codigo'),
                'group' => null
            ),
            $this
        );

        return ($this->query($clientes));
    }

    public function eliminarClientesDuplicados($codigo_cliente, $cnpj_cliente)
    {
        $duplicados = $this->find('all', array('conditions' => array('Cliente.codigo_documento' => $cnpj_cliente, 'Cliente.codigo <>' => $codigo_cliente)));
        $duplicados_atualizar = array();

        try {

            $this->create();
            $this->query('begin transaction');

            foreach ($duplicados as $duplicado) {

                $duplicados_atualizar                                    = $duplicado;
                $duplicados_atualizar['Cliente']['codigo_documento'] = '00000000000000';
                $duplicados_atualizar['Cliente']['razao_social']      = $cnpj_cliente . '|' . $duplicado['Cliente']['razao_social'];
                $duplicados_atualizar['Cliente']['ativo']                = 0;

                if (!parent::atualizar($duplicados_atualizar, false, array('razao_social', 'codigo_documento', 'ativo'))) {
                    throw new Exception();
                }
            }

            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

    public function carregarCorretora($codigo_cliente, $all_fields = false)
    {
        $this->bindCorretora();
        $cliente = $this->carregar($codigo_cliente);
        $this->unbindCorretora();

        return ($all_fields) ? $cliente['Corretora'] : $cliente['Corretora']['codigo'];
    }

    public function formatarClientePortal($cliente_desformatado, $sistema)
    {

        $MSeguradora = ClassRegistry::init('MSeguradora');
        $MCorretora  = ClassRegistry::init('MCorretora');
        $Seguradora = ClassRegistry::init('Seguradora');
        $Corretora  = ClassRegistry::init('Corretora');

        if ($sistema == 'MONITORA') {

            $cliente_formatado = array();
            $i = 0;

            foreach ($cliente_desformatado as $cliente) {

                $seguradora = $MSeguradora->carregar($cliente[0]['FUN_SEGURADORA']);
                $corretora  = $MCorretora->carregar($cliente[0]['FUN_CORRETORA']);

                $seguradora_portal = $Seguradora->find(
                    'first',
                    array(
                        'conditions' => array(
                            'nome' => $seguradora['MSeguradora']['Descricao']
                        )
                    )
                );

                $corretora_portal  = $Corretora->find(
                    'first',
                    array(
                        'conditions' => array(
                            'nome' => $corretora['MCorretora']['Descricao']
                        )
                    )
                );

                $cliente_formatado[$i]['Cliente']['codigo_documento']          = str_replace(array('.', '-', '/', ' '), '', $cliente[0]['CNPJCPF']);
                $cliente_formatado[$i]['Cliente']['razao_social']             = $cliente[0]['Raz_Social'];
                $cliente_formatado[$i]['Cliente']['nome_fantasia']             = $cliente[0]['Raz_Social'];
                $cliente_formatado[$i]['Cliente']['inscricao_estadual']      = ($cliente[0]['ISCEstadual'] == null) ? 'ISENTO' : $cliente[0]['ISCEstadual'];
                $cliente_formatado[$i]['Cliente']['ccm']                       = ($cliente[0]['ISCMunicipal'] == null) ? 'ISENTO' : $cliente[0]['ISCMunicipal'];
                $cliente_formatado[$i]['Cliente']['codigo_endereco_regiao']  = ($cliente[0]['Regiao'] == 2) ? 1 : ($cliente[0]['Regiao'] == 4) ? 2 : $cliente[0]['Regiao'];
                $cliente_formatado[$i]['Cliente']['regiao_tipo_faturamento'] = ($cliente[0]['Faturamento'] == 1) ? 1 : 2;

                $cliente_formatado[$i]['Cliente']['codigo_corretora']          = ($corretora_portal['Corretora']['codigo'] == null) ? 223 : $corretora_portal['Corretora']['codigo'];
                $cliente_formatado[$i]['Cliente']['codigo_usuario_inclusao'] = 1;

                $i++;
            }

            return $cliente_formatado;
        }
    }

    public function carregarPorDocumento($documento, $fields = null)
    {
        $conditions = array('codigo_documento' => str_replace(array('.', '/', '-', ''), '', $documento));
        return $this->find('first', compact('conditions', 'fields'));
    }

    function listarClientesDataCadastro($conditions = null)
    {
        $this->bindCorretora();
        $this->bindEnderecoRegiao();
        $this->bindUsuario();
        $result = $this->find('all', array('conditions' => $conditions, 'limit' => 50));
        $this->unbindCorretora();
        $this->unbindEnderecoRegiao();
        $this->unbindUsuario();

        return $result;
    }

    function joinsClientesProdutosServicos()
    {
        $ClienteProduto = ClassRegistry::init('ClienteProduto');
        $ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
        $Produto = ClassRegistry::init('Produto');
        $Seguradora = ClassRegistry::init('Seguradora');
        $Corretora = ClassRegistry::init('Corretora');
        $Servico = ClassRegistry::init('Servico');
        $ProfissionalTipo = ClassRegistry::init('ProfissionalTipo');
        $MotivoBloqueio = ClassRegistry::init('MotivoBloqueio');
        $EnderecoRegiao = ClassRegistry::init('EnderecoRegiao');
        $joins = array(
            array(
                'table' => $ClienteProduto->useTable,
                'databaseTable' => $ClienteProduto->databaseTable,
                'tableSchema' => $ClienteProduto->tableSchema,
                'alias' => $ClienteProduto->name,
                'type' => 'LEFT',
                'conditions' => "{$this->name}.{$this->primaryKey} = {$ClienteProduto->name}.codigo_cliente"
            ),
            array(
                'table' => $Produto->useTable,
                'databaseTable' => $Produto->databaseTable,
                'tableSchema' => $Produto->tableSchema,
                'alias' => $Produto->name,
                'type' => 'LEFT',
                'conditions' => "{$ClienteProduto->name}.codigo_produto = {$Produto->name}.{$Produto->primaryKey}"
            ),
            array(
                'table' => $ClienteProdutoServico2->useTable,
                'databaseTable' => $ClienteProdutoServico2->databaseTable,
                'tableSchema' => $ClienteProdutoServico2->tableSchema,
                'alias' => $ClienteProdutoServico2->name,
                'conditions' => "{$ClienteProduto->name}.{$ClienteProduto->primaryKey} = {$ClienteProdutoServico2->name}.codigo_cliente_produto"
            ),
            array(
                'table' => $Seguradora->useTable,
                'databaseTable' => $Seguradora->databaseTable,
                'tableSchema' => $Seguradora->tableSchema,
                'alias' => $Seguradora->name,
                'type' => 'LEFT',
                'conditions' => "{$this->name}.codigo_seguradora = {$Seguradora->name}.{$Seguradora->primaryKey}"
            ),
            array(
                'table' => $Corretora->useTable,
                'databaseTable' => $Corretora->databaseTable,
                'tableSchema' => $Corretora->tableSchema,
                'alias' => $Corretora->name,
                'type' => 'LEFT',
                'conditions' => "{$this->name}.codigo_corretora = {$Corretora->name}.{$Corretora->primaryKey}"
            ),
            array(
                'table' => $Servico->useTable,
                'databaseTable' => $Servico->databaseTable,
                'tableSchema' => $Servico->tableSchema,
                'alias' => $Servico->name,
                'type' => 'LEFT',
                'conditions' => "{$ClienteProdutoServico2->name}.codigo_servico = {$Servico->name}.{$Servico->primaryKey}"
            ),
            array(
                'table' => $ProfissionalTipo->useTable,
                'databaseTable' => $ProfissionalTipo->databaseTable,
                'tableSchema' => $ProfissionalTipo->tableSchema,
                'alias' => $ProfissionalTipo->name,
                'type' => 'LEFT',
                'conditions' => "{$ClienteProdutoServico2->name}.codigo_profissional_tipo = {$ProfissionalTipo->name}.{$ProfissionalTipo->primaryKey}"
            ),
            array(
                'table' => $MotivoBloqueio->useTable,
                'databaseTable' => $MotivoBloqueio->databaseTable,
                'tableSchema' => $MotivoBloqueio->tableSchema,
                'alias' => $MotivoBloqueio->name,
                'type' => 'LEFT',
                'conditions' => "{$ClienteProduto->name}.codigo_motivo_bloqueio = {$MotivoBloqueio->name}.{$MotivoBloqueio->primaryKey}"
            ),
            array(
                'table' => $EnderecoRegiao->useTable,
                'databaseTable' => $EnderecoRegiao->databaseTable,
                'tableSchema' => $EnderecoRegiao->tableSchema,
                'alias' => $EnderecoRegiao->name,
                'type' => 'LEFT',
                'conditions' => "{$this->name}.codigo_endereco_regiao = {$EnderecoRegiao->name}.{$EnderecoRegiao->primaryKey}"
            )
        );
        return $joins;
    }

    function atualizar($dados, $parent_method = false)
    {
        if ($parent_method) {
            return parent::save($dados);
        } else {
            $this->bindModel(array('hasOne' => array(
                'ClienteEndereco' => array('foreignKey' => 'codigo_cliente'),
                'ClienteProduto' => array('foreignKey' => 'codigo_cliente'),
            )));

            if (!isset($dados['Cliente']['codigo']) || empty($dados['Cliente']['codigo']))
                return false;
            try {

                if ($this->useDbConfig == 'test_suite') {
                    $this->query('BEGIN TRANSACTION');
                } else {
                    $this->query('begin transaction');
                }
                $dadosClienteAnterior = $this->carregar($dados['Cliente']['codigo']);
                $this->alteracoesDadosCadastrais = array();
                $this->alteracaoStatusCliente = null;


                if (isset($dados['Cliente']['razao_social']) && ($dadosClienteAnterior['Cliente']['razao_social'] != $dados['Cliente']['razao_social']))
                    array_push($this->alteracoesDadosCadastrais, 'razao_social');

                if (isset($dados['Cliente']['ativo']) && $dados['Cliente']['ativo'] == 0 && $dadosClienteAnterior['Cliente']['ativo'] == 1) {
                    $dados['Cliente']['data_inativacao'] = date('d/m/Y H:i:s');
                    if (!$this->ClienteProduto->inativarProdutos($dados['Cliente']['codigo']))
                        throw new Exception('Não inativou os produtos do cliente');
                }

                if (isset($dados['Cliente']['ativo']) && $dados['Cliente']['ativo'] == 1 && $dadosClienteAnterior['Cliente']['ativo'] == 0)
                    $dados['Cliente']['data_ativacao'] = date('d/m/Y H:i:s');

                if (isset($dados['Cliente']['ativo']) && ($dadosClienteAnterior['Cliente']['ativo'] != $dados['Cliente']['ativo']))
                    $this->alteracaoStatusCliente = $dados['Cliente']['ativo'];

                if (isset($dados['Cliente']['codigo_documento'])) {
                    $dados['Cliente']['codigo_documento'] = str_replace(array('/', '.', '-'), '', $dados['Cliente']['codigo_documento']);
                }


                if (!parent::atualizar($dados))
                    throw new Exception('Não atualizou cliente');

                if (isset($dados['ClienteEndereco'])) {
                    if (!$this->atualizarEnderecoComercial($dados))
                        throw new Exception('Não atualizou endereço');
                }


                // parent::query('UPDATE RHHealth.dbo.cliente set codigo = ' . $dados['Cliente']['codigo'] . ' where codigo = ' . $dados['Cliente']['codigo'] );

                if ($this->useDbConfig == 'test_suite') {
                    $this->commit();
                } else {
                    $this->commit();
                }

                return true;
            } catch (Exception $ex) {

                // $this->log($ex->getMessage(),'debug');

                if ($this->useDbConfig == 'test_suite') {
                    $this->rollback();
                } else {
                    $this->rollback();
                }
                return false;
            }
        }
    }

    /**
     * Metodo para incluir o cliente
     * 
     * @param
     * 	$data: dados ccliente para insercao
     *  $nao_vincula_grupo_economico: paramentro para não vincular o grupo economico 
     */
    function incluir($data, $nao_vincula_grupo_economico = null)
    {
        $this->LastId    = &ClassRegistry::Init('LastId');
        $this->bindModel(array('hasOne' => array(
            'GrupoEconomicoCliente' => array('foreignKey' => 'codigo_cliente'),
            'GrupoEconomico' => array('foreignKey' => false, 'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'),

        )));

        try {
            $this->create();
            if ($this->useDbConfig != 'test_suite') {
                $this->query('begin transaction');
            }

            #################################################################
            ##################### INCLUSÃO NO RHHEALTH  #####################
            #################################################################

            if (empty($data[$this->name]['codigo_regime_tributario'])) {
                $data[$this->name]['codigo_regime_tributario'] = 3;
            }
            $data[$this->name]['ativo'] = 1;
            $data[$this->name]['razao_social'] = strtoupper(comum::trata_nome($data[$this->name]['razao_social']));

            if (isset($data[$this->name]['codigo_documento'])) {
                $data[$this->name]['codigo_documento'] = str_replace(array('/', '.', '-'), '', $data[$this->name]['codigo_documento']);
            }

            $data[$this->name]['codigo_naveg'] = $this->LastId->last_id('Cliente');

            if (!parent::incluir($data)) {
                // var_dump($this->validationErrors);
                throw new Exception('Erro ao incluir cliente');
            }

            //valida se nao é para vincular/criar o grupo economico
            if (is_null($nao_vincula_grupo_economico)) {
                // eh Filial ?
                if (isset($data['GrupoEconomicoCliente']['codigo_grupo_economico']) && !empty($data['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
                    if (!$this->GrupoEconomicoCliente->incluir(array(
                        'codigo_cliente' => $this->id,
                        'codigo_grupo_economico' => $data['GrupoEconomicoCliente']['codigo_grupo_economico']
                    ))) throw new Exception("Erro ao vincular no Grupo Econômico", 1);
                } else {

                    //Inclui matriz como unidade tambem e insere em implantacao
                    $this->GrupoEconomico->incluir(array('GrupoEconomico' => array('codigo_cliente' => $this->id, 'descricao' => $data[$this->name]['razao_social'])));
                }
            } //fim validacao grupo economico

            $data['Cliente']['codigo'] = $this->id;
            if (!$this->atualizarEnderecoComercial($data)) {
                throw new Exception('Erro ao incluir endereço comercial');
            }


            #################################################################
            ################### FIM INCLUSÃO NO RHHEALTH  ###################
            #################################################################

            if ($this->useDbConfig != 'test_suite') {
                $this->commit();
            }
            return true;
        } catch (Exception $ex) {

            // $this->log($ex->getMessage(),'debug');

            if ($this->useDbConfig != 'test_suite') {
                $this->rollback();
            }
            return false;
        }
    }

    private function atualizarGuardian($dados)
    {

        try {
            if (!$this->TPessPessoa->atualizar($dados))
                throw new Exception('Erro ao atualizar um Pessoa');

            if (!$this->TPjurPessoaJuridica->atualizar($dados))
                throw new Exception('Erro ao atualizar um Pessoa');

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }


    private function atualizarMonitora($dados)
    {
        $this->TConfConfiguracao    = &ClassRegistry::Init('TConfConfiguracao');
        $ClientEmpresa  = ClassRegistry::Init('ClientEmpresa');
        $pgpg             = $this->TConfConfiguracao->pgrPadrao();

        $codigo = $ClientEmpresa->find('all', array(
            'conditions' => array(
                "codigo_documento" => str_replace(array('/', '.', '-',), '', $dados['Cliente']['codigo_documento'])
            ),
            'fields' => array('Codigo'),
        ));

        if ($codigo)
            $codigo = Set::extract('/ClientEmpresa/Codigo', $codigo);
        else
            $codigo = array($ClientEmpresa->novoCodigo());

        $data_cliente_empresa = array(
            'ClientEmpresa' => array(
                'CNPJCPF' => Comum::formatarDocumento($dados['Cliente']['codigo_documento']),
                'Empresa' => 1,
                'Qualifica' => 'MATRIZ',
                'FisicaJuridica' => (strlen($dados['Cliente']['codigo_documento']) > 11) ? 'J' : 'F',
                'codigopgr' => $pgpg,
                'codigo_cliente' => $dados['Cliente']['codigo'],
            ),
        );

        if (isset($dados['Cliente']['ativo'])) {
            $data_cliente_empresa['ClientEmpresa']['WebAtivo'] = $dados['Cliente']['ativo'] ? 'S' : 'N';
            $data_cliente_empresa['ClientEmpresa']['Status']   = $dados['Cliente']['ativo'] ? 'S' : 'N';
        }

        if (isset($dados['Cliente']['razao_social']) && !empty($dados['Cliente']['razao_social'])) {
            $data_cliente_empresa['ClientEmpresa']['Raz_Social'] = $dados['Cliente']['razao_social'];
        }
        if (isset($dados['Cliente']['codigo_endereco_regiao']) && !empty($dados['Cliente']['codigo_endereco_regiao'])) {
            $data_cliente_empresa['ClientEmpresa']['Regiao'] = $this->deParaCodigoRegiao($dados['Cliente']['codigo_endereco_regiao']);
        }
        if (isset($dados['Cliente']['regiao_tipo_faturamento']) && !empty($dados['Cliente']['regiao_tipo_faturamento'])) {
            $data_cliente_empresa['ClientEmpresa']['Faturamento'] = ($dados['Cliente']['regiao_tipo_faturamento'] == 1) ? 1 : 2;
        }
        if (isset($dados['Cliente']['inscricao_estadual']) && !empty($dados['Cliente']['inscricao_estadual'])) {
            $data_cliente_empresa['ClientEmpresa']['ISCEstadual'] = $dados['Cliente']['inscricao_estadual'];
        }

        try {
            foreach ($codigo as $key => $value) {
                $data_cliente_empresa['ClientEmpresa']['Codigo'] = $value;
                if (!$ClientEmpresa->save($data_cliente_empresa))
                    throw new Exception("");
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deParaTipoEmpresa($codigo)
    {
        switch ($codigo) {
            case in_array($codigo, array(1, 7, 13, 19)):
                return 4;
                break;
            case in_array($codigo, array(20, 8, 2, 14)):
                return 1;
                break;
            case in_array($codigo, array(21, 9, 3, 15)):
                return 3;
                break;
            default:
                return 2;
                break;
        }
    }

    private function deParaCodigoRegiao($codigo)
    {
        switch ($codigo) {
            case 6:
                return 6;
                break;
            case 1:
                return 2;
                break;
            case 2:
                return 4;
                break;
            case 3:
                return 3;
                break;
            default:
                return 0;
                break;
        }
    }

    function atualizarEnderecoComercial($dados)
    {
        $this->bindModel(array('hasOne' => array(
            'ClienteEndereco' => array('foreignKey' => 'codigo_cliente'),
        )));

        $dados_endereco = array('ClienteEndereco' => $dados['ClienteEndereco']);

        // Para repetir endereço
        if (isset($dados['Outros'])) {
            $dados_endereco['Outros'] = $dados['Outros'];
        }
        $dados_endereco['ClienteEndereco']['codigo_usuario_inclusao']  = $_SESSION['Auth']['Usuario']['codigo']; //$dados['Cliente']['codigo_usuario_inclusao'];
        $dados_endereco['ClienteEndereco']['codigo_cliente'] = $dados['Cliente']['codigo'];
        $dados_endereco['ClienteEndereco']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;

        ####### DEFINE LAT E LONG #############
        // if(Ambiente::TIPO_MAPA == 1) {
        App::import('Component', array('ApiGoogle'));
        $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($dados['ClienteEndereco']['logradouro'] . ' ' . $dados['ClienteEndereco']['numero'] . ' - ' . $dados['ClienteEndereco']['cidade'] . ' - ' . $dados['ClienteEndereco']['estado_descricao']);

        $dados_endereco['ClienteEndereco']['latitude'] = $latitude;
        $dados_endereco['ClienteEndereco']['longitude'] = $longitude;
        ####### DEFINE LAT E LONG #############

        if (!isset($dados_endereco['ClienteEndereco']['codigo']) || empty($dados_endereco['ClienteEndereco']['codigo'])) {
            $result = $this->ClienteEndereco->incluir($dados_endereco);
            return $result;
        } else {
            $dados_antigos = $this->ClienteEndereco->carregar($dados_endereco['ClienteEndereco']['codigo']);
            if (($dados_endereco['ClienteEndereco']['numero'] != $dados_antigos['ClienteEndereco']['numero']) ||
                ($dados_endereco['ClienteEndereco']['complemento'] != $dados_antigos['ClienteEndereco']['complemento']) ||
                ($dados_endereco['ClienteEndereco']['logradouro'] != $dados_antigos['ClienteEndereco']['logradouro']) ||
                ($dados_endereco['ClienteEndereco']['bairro'] != $dados_antigos['ClienteEndereco']['bairro']) ||
                ($dados_endereco['ClienteEndereco']['cidade'] != $dados_antigos['ClienteEndereco']['cidade']) ||
                ($dados_endereco['ClienteEndereco']['estado_descricao'] != $dados_antigos['ClienteEndereco']['estado_descricao']) ||
                ($dados_endereco['ClienteEndereco']['estado_abreviacao'] != $dados_antigos['ClienteEndereco']['estado_abreviacao'])
            ) {
                $dados_endereco = array('ClienteEndereco' => array_merge($dados_antigos['ClienteEndereco'], $dados_endereco['ClienteEndereco']));
                $dados_endereco['ClienteEndereco']['codigo_endereco'] = NULL;
                $result = $this->ClienteEndereco->atualizar($dados_endereco);
                array_push($this->alteracoesDadosCadastrais, 'endereco');
                return $result;
            } else {
                return true;
            }
        }
    }

    function subQueryParaUltimaAtualizacao($conditions = null)
    {
        $this->bindClienteLog();

        $dbo = $this->getDataSource();
        $subquery = $dbo->buildStatement(
            array(
                'fields' => array('MAX(data_inclusao) AS data_inclusao', 'codigo_cliente'),
                'table' => $this->ClienteLog->databaseTable . '.' . $this->ClienteLog->tableSchema . '.' . $this->ClienteLog->table,
                'alias' => 'UltimaAlteracao',
                'limit' => null,
                'offset' => null,
                'joins' => array(),
                'conditions' => array(),
                'order' => null,
                'group' => 'codigo_cliente'
            ),
            $this
        );
        $subquery = ' (' . $subquery . ') ';

        $this->unbindClienteLog();

        if (isset($conditions['ultima_atualizacao']) && !empty($conditions['ultima_atualizacao'])) {
            return array(
                array(
                    'table' => $subquery,
                    'alias' => 'UltimaAlteracao',
                    'type' => 'INNER',
                    'conditions' => 'Cliente.codigo = UltimaAlteracao.codigo_cliente'
                )
            );
        }
        return false;
    }

    function temEnderecoComercial($codigo_cliente)
    {
        $this->bindClienteEndereco();
        $endereco = $this->ClienteEndereco->getByTipoContato($codigo_cliente, TipoContato::TIPO_CONTATO_COMERCIAL);

        if (isset($endereco['ClienteEndereco']['codigo'])) {
            return true;
        } else {
            return false;
        }
    }

    function validaInscricaoEstadual()
    {
        $cgccpf = Comum::soNumero($this->data['Cliente']['codigo_documento']);
        if (strlen($cgccpf) > 11) {
            if (isset($this->data['ClienteEndereco']['codigo_endereco'])) {
                $endereco = $this->ClienteEndereco->find('first', array('conditions' => $this->data['ClienteEndereco']['codigo_endereco']));
                $estado   = $endereco['ClienteEndereco']['estado_abreviacao'];
                if ($this->data['Cliente']['inscricao_estadual'] != 'ISENTO') {
                    if (!empty($estado)) {
                        return $this->checkIE($this->data['Cliente']['inscricao_estadual'], $estado);
                    } else {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function carregarParaEdicao($codigo_cliente)
    {
        $dados = $this->read(null, $codigo_cliente);
        $this->bindModel(array('hasOne' => array(
            'ClienteEndereco' => array('foreignKey' => 'codigo_cliente'),
        )));
        $endereco_comercial = $this->ClienteEndereco->getByTipoContato($codigo_cliente, TipoContato::TIPO_CONTATO_COMERCIAL);
        if ($endereco_comercial)
            $dados += $endereco_comercial;
        return $dados;
    }

    function codigosMesmaBaseCNPJ($codigo_cliente)
    {
        $cliente = $this->read('codigo_documento', $codigo_cliente);
        if ($cliente) {
            $codigos = $this->find('all', array('fields' => 'codigo', 'conditions' => array('codigo_documento like' => substr($cliente['Cliente']['codigo_documento'], 0, 8) . '%')));
            return Set::extract('/Cliente/codigo', $codigos);
        }
    }

    function porBaseCNPJ($cnpj_cliente, $type = 'list')
    {
        $conditions = array('codigo_documento like' => substr($cnpj_cliente, 0, 8) . '%');
        $clientes = $this->find($type, compact('conditions'));
        return (($clientes) ? $clientes : array());
    }

    function porCNPJ($cnpj_cliente, $type = 'list', $fields = null)
    {
        $conditions = array('codigo_documento' => str_replace(array('/', '.', '-'), '', $cnpj_cliente));
        $clientes     = $this->find($type, compact('conditions', 'fields'));
        return (($clientes) ? $clientes : array());
    }

    function estatisticaPorClientePagador2($filtros, $detalhar_filhos = false, $retornar_instrucao_sql = false, $integracao = false, $verificar_falha = false, $historico = false)
    {
        $filtros['data_inicial'] .= ' 00:00:00';
        $filtros['data_final']      .= ' 23:59:59';

        $conditions = array(
            'LogFaturamentoTeleconsult.data_inclusao BETWEEN ? AND ?' => array(AppModel::dateToDbDate2($filtros['data_inicial']), AppModel::dateToDbDate2($filtros['data_final'])),
        );

        if ($integracao)
            $conditions['LogFaturamentoTeleconsult.codigo_item_pedido'] = null;
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
            $conditions['LogFaturamentoTeleconsult.codigo_cliente_pagador'] = $filtros['codigo_cliente'];
        }

        $Pedido                     = &ClassRegistry::init('Pedido');
        $ItemPedido                 = &ClassRegistry::init('ItemPedido');
        $TipoOperacao                 = &ClassRegistry::init('TipoOperacao');
        $LogFaturamentoTeleconsult     = &ClassRegistry::init('LogFaturamentoTeleconsult');
        $Produto                     = &ClassRegistry::init('Produto');
        $Documento                     = &ClassRegistry::init('Documento');
        $ClienteEndereco             = &ClassRegistry::init('ClienteEndereco');
        $ClienteProduto             = &ClassRegistry::init('ClienteProduto');
        $ClienteProdutoServico2        = &ClassRegistry::init('ClienteProdutoServico2');
        $ClienteProdutoDesconto     = &ClassRegistry::init('ClienteProdutoDesconto');

        $dbo = $this->getDataSource();

        $group = array(
            "LogFaturamentoTeleconsult.codigo_cliente_pagador",
            "LogFaturamentoTeleconsult.codigo_cliente",
            "LogFaturamentoTeleconsult.codigo_produto",
            "LogFaturamentoTeleconsult.codigo_usuario_inclusao",
        );

        $fields = array(
            "LogFaturamentoTeleconsult.codigo_cliente_pagador",
            "LogFaturamentoTeleconsult.codigo_cliente",
            "LogFaturamentoTeleconsult.codigo_produto",
            "SUM(LogFaturamentoTeleconsult.valor) AS valor",
            "LogFaturamentoTeleconsult.codigo_usuario_inclusao",
            "SUM(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao <> 1 AND TipoOperacao.cobrado = 1 THEN 1 ELSE 0 END) as qtd_cobrado",
            "SUM(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao <> 1 AND TipoOperacao.cobrado = 1 THEN LogFaturamentoTeleconsult.valor ELSE 0 END) as valor_cobrado",
            "SUM(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao <> 1 AND TipoOperacao.cobrado = 1 THEN 0 ELSE 1 END) as qtd_nao_cobrado",
            "SUM(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao <> 1 AND TipoOperacao.cobrado = 1 THEN 0 ELSE LogFaturamentoTeleconsult.valor END) as valor_nao_cobrado",
        );

        $joins = array(
            array(
                'table'      => "{$TipoOperacao->databaseTable}.{$TipoOperacao->tableSchema}.{$TipoOperacao->useTable}",
                'alias'      => 'TipoOperacao',
                'type'          => 'LEFT',
                'conditions' => array('TipoOperacao.codigo = LogFaturamentoTeleconsult.codigo_tipo_operacao'),
            ),
            array(
                'table'      => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
                'alias'      => 'ClienteProduto',
                'type'           => 'LEFT',
                'conditions' => array(
                    'ClienteProduto.codigo_cliente = LogFaturamentoTeleconsult.codigo_cliente',
                    'ClienteProduto.codigo_produto = LogFaturamentoTeleconsult.codigo_produto'
                ),
            ),
        );

        if ($historico) {
            $group = array_merge(
                $group,
                array(
                    "ItemPedido.valor_premio_minimo",
                    "ItemPedido.valor_taxa_bancaria",
                    "ItemPedido.valor_taxa_corretora",
                )
            );
            $fields = array_merge(
                $fields,
                array(
                    "ISNULL(ItemPedido.valor_premio_minimo,0)  AS valor_premio_minimo",
                    "ISNULL(ItemPedido.valor_taxa_bancaria,0)  AS valor_taxa_bancaria",
                    "ISNULL(ItemPedido.valor_taxa_corretora,0) AS valor_taxa_corretora",
                )
            );
            $joins = array_merge(
                $joins,
                array(
                    array(
                        'table'      => "{$Pedido->databaseTable}.{$Pedido->tableSchema}.{$Pedido->useTable}",
                        'alias'      => 'Pedido',
                        'type'          => 'INNER',
                        'conditions' => array(
                            'Pedido.codigo_cliente_pagador = LogFaturamentoTeleconsult.codigo_cliente_pagador',
                            'Pedido.mes_referencia' => $filtros['mes_referencia'],
                            'Pedido.ano_referencia' => $filtros['ano_referencia']
                        ),
                    ),
                    array(
                        'table'      => "{$ItemPedido->databaseTable}.{$ItemPedido->tableSchema}.{$ItemPedido->useTable}",
                        'alias'      => 'ItemPedido',
                        'type'            => 'INNER',
                        'conditions' => array(
                            'ItemPedido.codigo_pedido = Pedido.codigo',
                            'ItemPedido.codigo = LogFaturamentoTeleconsult.codigo_item_pedido'
                        ),
                    )
                )
            );
        } else {
            $fields = array_merge(
                $fields,
                array(
                    "MAX(ISNULL(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao = 1
				THEN 0 ELSE
				(CASE WHEN ClienteProduto.valor_premio_minimo = NULL OR ClienteProduto.valor_premio_minimo = 0
				THEN LogFaturamentoTeleconsult.valor_premio_minimo
				ELSE ClienteProduto.valor_premio_minimo
				END)
				END,0)) AS valor_premio_minimo",
                    "MAX(ISNULL(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao = 1 THEN 0 ELSE ClienteProduto.valor_taxa_bancaria  END,0)) AS valor_taxa_bancaria",
                    "MAX(ISNULL(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao = 1 THEN 0 ELSE ClienteProduto.valor_taxa_corretora END,0)) AS valor_taxa_corretora",
                )
            );
        }

        $servicos = $dbo->buildStatement(
            array(
                'fields'      => $fields,
                'table'      => $LogFaturamentoTeleconsult->databaseTable . '.' . $LogFaturamentoTeleconsult->tableSchema . '.' . $LogFaturamentoTeleconsult->useTable,
                'alias'      => '[LogFaturamentoTeleconsult] WITH (NOLOCK)',
                'limit'      => null,
                'offset'      => null,
                'joins'      => $joins,
                'conditions' => $conditions,
                'order'      => null,
                'group'      => $group,
            ),
            $this
        );

        $group = array(
            "codigo_cliente_pagador",
            "codigo_cliente",
            "codigo_produto",
        );

        $fields = array(
            "codigo_cliente_pagador",
            "codigo_cliente",
            "codigo_produto",
            "SUM(qtd_cobrado) as qtd_cobrado",
            "SUM(valor_cobrado) as valor_cobrado",
            "SUM(qtd_nao_cobrado) as qtd_nao_cobrado",
            "SUM(valor_nao_cobrado) as valor_nao_cobrado",
            "SUM(valor_cobrado) as valor_a_pagar",
        );

        $filhos_sub_totalizados = $dbo->buildStatement(
            array(
                'fields'      => $fields,
                'table'      => "({$servicos})",
                'alias'      => 'Servicos',
                'limit'      => null,
                'offset'      => null,
                'joins'      => array(),
                'conditions' => null,
                'order'      => null,
                'group'      => $group,
            ),
            $this
        );

        $group = array(
            "codigo_cliente_pagador",
            "codigo_cliente",
            "Cliente.razao_social",
            "codigo_produto",
            "Produto.descricao",
        );

        $fields = array(
            "codigo_cliente_pagador",
            "codigo_cliente",
            "Cliente.razao_social",
            "codigo_produto",
            "Produto.descricao",
            "SUM(qtd_cobrado) as qtd_cobrado",
            "SUM(valor_cobrado) as valor_cobrado",
            "SUM(qtd_nao_cobrado) as qtd_nao_cobrado",
            "SUM(valor_nao_cobrado) as valor_nao_cobrado",
            "SUM(valor_cobrado) as valor_a_pagar",
        );

        $filhos_totalizados = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table'  => "({$filhos_sub_totalizados})",
                'alias'  => 'FilhosSubTotalizados',
                'limit'  => null,
                'offset' => null,
                'joins'  => array(
                    array(
                        'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
                        'alias' => 'Cliente',
                        'type' => 'LEFT',
                        'conditions' => array('FilhosSubTotalizados.codigo_cliente = Cliente.codigo'),
                    ),
                    array(
                        'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
                        'alias' => 'Produto',
                        'type' => 'LEFT',
                        'conditions' => array('FilhosSubTotalizados.codigo_produto = Produto.codigo'),
                    ),
                ),
                'conditions' => null,
                'order' => null,
                'group' => $group,
            ),
            $this
        );

        if ($detalhar_filhos) {
            if ($retornar_instrucao_sql) {
                return $filhos_totalizados;
            } else {
                return $this->query($filhos_totalizados);
            }
        }

        $group = array(
            "codigo_cliente_pagador",
        );

        $data_inicial = "'" . AppModel::dateToDbDate2($filtros['data_inicial']) . "'";
        $data_final   = "'" . AppModel::dateToDbDate2($filtros['data_final']) . "'";

        $fields = array(
            "codigo_cliente_pagador",
            "SUM(qtd_cobrado) AS qtd_cobrado",
            "SUM(valor_cobrado) AS valor_cobrado",
            "SUM(qtd_nao_cobrado) AS qtd_nao_cobrado",
            "SUM(valor_nao_cobrado) AS valor_nao_cobrado",
            "SUM(valor_a_pagar) AS valor_a_pagar",
        );

        $conditions = null;
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
            $conditions['FilhosTotalizados.codigo_cliente_pagador'] = $filtros['codigo_cliente'];

        $pagadores_sub_totalizados = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table'  => "({$filhos_totalizados})",
                'alias'  => 'FilhosTotalizados',
                'limit'  => null,
                'offset' => null,
                'joins'  => array(),
                'conditions' => $conditions,
                'order'      => null,
                'group'      => $group,
            ),
            $this
        );

        $queryTaxas = $ClienteProduto->taxasTeleconsultPorCliente($filtros);
        $pagadores_sub_totalizados_taxas = $dbo->buildStatement(
            array(
                'fields' => array(
                    "CASE WHEN PagadoresSubTotalizadosTaxas.codigo_cliente_pagador IS NULL THEN Taxas.codigo_cliente ELSE PagadoresSubTotalizadosTaxas.codigo_cliente_pagador END AS codigo_cliente_pagador",
                    "ISNULL(Taxas.valor_premio_minimo,0) AS valor_premio_minimo",
                    "ISNULL(Taxas.valor_taxa_corretora,0) AS valor_taxa_corretora",
                    "ISNULL(Taxas.valor_taxa_bancaria,0) AS valor_taxa_bancaria",
                    "ISNULL(PagadoresSubTotalizadosTaxas.qtd_cobrado,0) AS qtd_cobrado",
                    "ISNULL(PagadoresSubTotalizadosTaxas.valor_cobrado,0) AS valor_cobrado",
                    "ISNULL(PagadoresSubTotalizadosTaxas.qtd_nao_cobrado,0) AS qtd_nao_cobrado",
                    "ISNULL(PagadoresSubTotalizadosTaxas.valor_nao_cobrado,0) AS valor_nao_cobrado",
                    "ISNULL(PagadoresSubTotalizadosTaxas.valor_a_pagar,0) AS valor_a_pagar",
                ),
                'table'  => "({$pagadores_sub_totalizados})",
                'alias'  => 'PagadoresSubTotalizadosTaxas',
                'limit'  => null,
                'offset' => null,
                'joins'  => array(
                    array(
                        'table' => "($queryTaxas)",
                        'alias' => 'Taxas',
                        'type'  => 'FULL',
                        'conditions' => array('Taxas.codigo_cliente = PagadoresSubTotalizadosTaxas.codigo_cliente_pagador'),
                    ),
                ),
                'conditions' => null,
                'order'      => null,
                'group'      => null,
            ),
            $this
        );

        $fields = array(
            "codigo_cliente_pagador",
            "DATEDIFF(day, {$data_inicial}, {$data_final}) + 1 AS dias_periodo",
            "{$this->databaseTable}.{$Documento->tableSchema}.ufn_menor_valor(
		DATEDIFF(day, {$data_inicial}, {$data_final}) + 1,
		CASE WHEN Cliente.ativo=1
		THEN DATEDIFF(day, {$this->databaseTable}.{$Documento->tableSchema}.ufn_maior_data(Cliente.data_inclusao, $data_inicial), {$data_final}) + 1
		ELSE {$this->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(
		0,
		DATEDIFF(day, DATEADD(dd,-DAY($data_final)+1, $data_final) + 1,Cliente.data_inativacao))
		END
		) AS dias_utilizados",
            "CASE WHEN Cliente.ativo=1 OR qtd_cobrado>0 THEN valor_premio_minimo ELSE 0 END AS valor_premio_minimo",
            "CASE WHEN Cliente.ativo=1 OR qtd_cobrado>0 THEN valor_taxa_corretora ELSE 0 END AS valor_taxa_corretora",
            "CASE WHEN Cliente.ativo=1 OR qtd_cobrado>0 THEN valor_taxa_bancaria ELSE 0 END AS valor_taxa_bancaria",
            "qtd_cobrado",
            "valor_cobrado",
            "qtd_nao_cobrado",
            "valor_nao_cobrado",
            "valor_a_pagar",
        );
        $pagadores_sub_totalizados = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table'  => "({$pagadores_sub_totalizados_taxas})",
                'alias'  => 'PagadoresSubTotalizados',
                'limit'  => null,
                'offset' => null,
                'joins'  => array(
                    array(
                        'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
                        'alias' => 'Cliente',
                        'type'  => 'INNER',
                        'conditions' => array('Cliente.codigo = PagadoresSubTotalizados.codigo_cliente_pagador'),
                    ),
                ),
                'conditions' => null,
                'order'      => null,
                'group'      => null,
            ),
            $this
        );

        $fields = array(
            "codigo_cliente_pagador",
            "Cliente.razao_social",
            "valor_premio_minimo",
            "dias_utilizados",
            "valor_taxa_bancaria",
            "valor_taxa_corretora",
            "qtd_cobrado",
            "valor_cobrado",
            "qtd_nao_cobrado",
            "valor_nao_cobrado",
            "ClienteProdutoDesconto.valor as valor_desconto",
            "(CASE WHEN {$this->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(valor_premio_minimo, valor_cobrado) + valor_taxa_corretora > 0 AND {$this->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(ROUND(valor_premio_minimo / dias_periodo * dias_utilizados,2), valor_cobrado) + valor_taxa_corretora - ISNULL(ClienteProdutoDesconto.valor,0) BETWEEN 0.01 AND 199.99
		THEN valor_taxa_bancaria
		ELSE 0
		END) + ({$this->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(ROUND(valor_premio_minimo / dias_periodo * dias_utilizados,2), valor_cobrado) - ISNULL(ClienteProdutoDesconto.valor,0) + valor_taxa_corretora) AS valor_a_pagar ",
            "ClienteEndereco.codigo as codigo_endereco",
        );

        $conditions = array(
            "dias_utilizados > 0",
            "CONVERT(DECIMAL(15,2), (CASE WHEN {$this->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(valor_premio_minimo, valor_cobrado) BETWEEN 0.01 AND 199.99
		THEN valor_taxa_bancaria
		ELSE 0
		END) + {$this->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(valor_premio_minimo, valor_cobrado) + valor_taxa_corretora) > 0",
        );

        if ($verificar_falha) {
            $conditions['OR'] = array('ClienteEndereco.codigo IS NULL', 'valor_a_pagar < 0');
        }

        $pagadores_totalizados = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table'  => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
                'alias'  => 'Cliente',
                'limit'  => null,
                'offset' => null,
                'joins'  => array(
                    array(
                        'table'      => "({$pagadores_sub_totalizados})",
                        'alias'      => 'PagadoresSubTotalizados',
                        'type'          => 'INNER',
                        'conditions' => array('PagadoresSubTotalizados.codigo_cliente_pagador = Cliente.codigo'),
                    ),
                    array(
                        'table'      => "{$ClienteEndereco->databaseTable}.{$ClienteEndereco->tableSchema}.{$ClienteEndereco->useTable}",
                        'alias'      => 'ClienteEndereco',
                        'type'          => 'LEFT',
                        'conditions' => array(
                            'ClienteEndereco.codigo_cliente = Cliente.codigo',
                            'ClienteEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
                        ),
                    ),
                    array(
                        'table'      => "{$ClienteProdutoDesconto->databaseTable}.{$ClienteProdutoDesconto->tableSchema}.{$ClienteProdutoDesconto->useTable}",
                        'alias'      => 'ClienteProdutoDesconto',
                        'type'          => 'LEFT',
                        'conditions' => array(
                            'ClienteProdutoDesconto.codigo_cliente = Cliente.codigo',
                            'ClienteProdutoDesconto.mes_ano BETWEEN ? AND ?' => array(AppModel::dateToDbDate2($filtros['data_inicial']), AppModel::dateToDbDate2($filtros['data_final'])),
                            'ClienteProdutoDesconto.codigo_produto' => 1,
                        ),
                    ),
                ),
                'conditions' => $conditions,
                'order'      => null,
                'group'      => null,
            ),
            $this
        );

        if ($retornar_instrucao_sql) {
            return $pagadores_totalizados;
        } else {
            return $this->query($pagadores_totalizados);
        }
    }

    function semUsoValores2($filtros)
    {
        $Produto         = &ClassRegistry::init('Produto');
        $ClienteProduto = &ClassRegistry::init('ClienteProduto');
        $matriz_premio_minimo = $this->matrizClientesSemUso2($filtros);

        $clientes_combinados = $this->clientesCombinados2();

        $dbo = $this->getDataSource();
        $fields = array(
            'ClienteCombinado.codigo_cliente_pagador',
            'ClienteCombinado.codigo_cliente',
            'ClienteCombinado.codigo_produto',
            'ISNULL(PremioMinimo.valor_premio_minimo,0) AS valor_premio_minimo',
            'ISNULL(PremioMinimo.valor_taxa_bancaria,0) AS valor_taxa_bancaria',
            'ISNULL(PremioMinimo.valor_taxa_corretora,0) AS valor_taxa_corretora',
            "0 AS qtd_cobrado",
            "0 AS valor_cobrado",
            "0 AS qtd_nao_cobrado",
            "0 AS valor_nao_cobrado",
            '(CASE WHEN ISNULL(PremioMinimo.valor_premio_minimo,0) < 200 THEN ISNULL(PremioMinimo.valor_taxa_bancaria,0) ELSE 0 END) + ISNULL(PremioMinimo.valor_premio_minimo,0) AS valor_a_pagar',
        );
        $conditions = array(
            'ClienteCombinado.codigo_produto' => array(1, 2),
            'OR' => array(
                'PremioMinimo.valor_premio_minimo IS NOT NULL',
                'PremioMinimo.valor_taxa_bancaria IS NOT NULL',
            )
        );
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
            $conditions['ClienteCombinado.codigo_cliente_pagador'] = $filtros['codigo_cliente'];
        $pagadores_totalizados = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => "({$clientes_combinados})",
                'alias' => 'ClienteCombinado',
                'limit' => null,
                'offset' => null,
                'joins' => array(
                    array(
                        'table' => "({$matriz_premio_minimo})",
                        'alias' => 'PremioMinimo',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'PremioMinimo.codigo_cliente_pagador = ClienteCombinado.codigo_cliente_pagador',
                            'PremioMinimo.codigo_cliente = ClienteCombinado.codigo_cliente',
                            'PremioMinimo.codigo_produto = ClienteCombinado.codigo_produto',
                        ),
                    ),

                ),
                'conditions' => $conditions,
                'order' => null,
                'group' => null,
            ),
            $this
        );
        return $pagadores_totalizados;
    }

    function matrizClientesSemUso2($filtros)
    {
        $ClienteProdutoServico2 = &ClassRegistry::init('ClienteProdutoServico2');
        $ClienteProduto         = &ClassRegistry::init('ClienteProduto');
        $dbo = $this->getDataSource();

        $joins = array(
            array(
                'table' => $ClienteProduto->databaseTable . '.' . $ClienteProduto->tableSchema . '.' . $ClienteProduto->useTable,
                'alias' => 'ClienteProduto',
                'type' => 'INNER',
                'conditions' => array('ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto'),
            ),
            array(
                'table' => $this->databaseTable . '.' . $this->tableSchema . '.' . $this->useTable,
                'alias' => 'Pagador',
                'type' => 'INNER',
                'conditions' => array('Pagador.codigo = ClienteProdutoServico2.codigo_cliente_pagador'),
            ),
            array(
                'table' => $this->databaseTable . '.' . $this->tableSchema . '.' . $this->useTable,
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => array('Cliente.codigo = ClienteProduto.codigo_cliente'),
            ),
        );

        $queryClientesQueUtilizaramTeleconsult = $this->queryClientesQueUtilizaramTeleconsult($filtros);
        $conditions = array(
            'ClienteProdutoServico2.codigo_servico' => 1,
            'ClienteProduto.valor_premio_minimo >' => 0,
            'Pagador.ativo' => 1,
            'Cliente.ativo' => 1,
            'ClienteProduto.codigo_motivo_bloqueio' => 1,
            'Cliente.data_inclusao <=' => AppModel::dateToDbDate2($filtros['data_final']),
            "CONVERT(VARCHAR(7), Cliente.codigo)+' '+CONVERT(VARCHAR(7), Pagador.codigo) NOT IN ({$queryClientesQueUtilizaramTeleconsult})"
        );
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
            $conditions['ClienteProdutoServico2.codigo_cliente_pagador'] = $filtros['codigo_cliente'];
        }
        $fields = array(
            'ClienteProdutoServico2.codigo_cliente_pagador',
            'ClienteProduto.codigo_cliente',
            'ClienteProduto.codigo_produto',
            'ClienteProduto.valor_premio_minimo',
            'ClienteProduto.valor_taxa_bancaria',
            'ClienteProduto.valor_taxa_corretora',
        );
        $matriz_premio_minimo = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => $ClienteProdutoServico2->databaseTable . '.' . $ClienteProdutoServico2->tableSchema . '.' . $ClienteProdutoServico2->useTable,
                'alias' => 'ClienteProdutoServico2',
                'limit' => null,
                'offset' => null,
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => null,
                'group' => null,
            ),
            $this
        );
        return $matriz_premio_minimo;
    }

    function clientesCombinados2()
    {
        $ClienteProduto = &ClassRegistry::init('ClienteProduto');
        $ClienteProdutoServico2 = &ClassRegistry::init('ClienteProdutoServico2');
        $dbo = $this->getDataSource();
        $fields = array(
            "DISTINCT ClienteProdutoServico2.codigo_cliente_pagador",
            'ClienteProduto.codigo_cliente',
            'ClienteProduto.codigo_produto',
        );
        $query = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => $ClienteProdutoServico2->databaseTable . '.' . $ClienteProdutoServico2->tableSchema . '.' . $ClienteProdutoServico2->useTable,
                'alias' => 'ClienteProdutoServico2',
                'limit' => null,
                'offset' => null,
                'joins' => array(
                    array(
                        'table' => $ClienteProduto->databaseTable . '.' . $ClienteProduto->tableSchema . '.' . $ClienteProduto->useTable,
                        'alias' => 'ClienteProduto',
                        'type' => 'INNER',
                        'conditions' => array('ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto'),
                    ),
                ),
                'conditions' => null,
                'order' => null,
                'group' => null,
            ),
            $this
        );
        return $query;
    }

    function queryClientesQueUtilizaramTeleconsult($filtros)
    {
        $LogFaturamentoTeleconsult = &ClassRegistry::init('LogFaturamentoTeleconsult');
        $TipoOperacao = &ClassRegistry::init('TipoOperacao');
        $conditions = array(
            'TipoOperacao.cobrado' => 1,
            'LogFaturamentoTeleconsult.codigo_usuario_inclusao <>' => 1,
            'LogFaturamentoTeleconsult.data_inclusao BETWEEN ? AND ?' => array(
                AppModel::dateToDbDate2($filtros['data_inicial']),
                AppModel::dateToDbDate2($filtros['data_final']),
            ),
        );
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
            $conditions['LogFaturamentoTeleconsult.codigo_cliente_pagador'] = $filtros['codigo_cliente'];
        $dbo = $this->getDataSource();
        $query = $dbo->buildStatement(
            array(
                'fields' => array("DISTINCT CONVERT(VARCHAR(7), codigo_cliente)+' '+CONVERT(VARCHAR(7), codigo_cliente_pagador)"),
                'table' => $LogFaturamentoTeleconsult->databaseTable . '.' . $LogFaturamentoTeleconsult->tableSchema . '.' . $LogFaturamentoTeleconsult->useTable,
                'alias' => 'LogFaturamentoTeleconsult',
                'limit' => null,
                'offset' => null,
                'joins' => array(
                    array(
                        'table' => $TipoOperacao->databaseTable . '.' . $TipoOperacao->tableSchema . '.' . $TipoOperacao->useTable,
                        'alias' => 'TipoOperacao',
                        'type' => 'INNER',
                        'conditions' => array('TipoOperacao.codigo = LogFaturamentoTeleconsult.codigo_tipo_operacao'),
                    ),
                ),
                'conditions' => $conditions,
                'order' => null,
                'group' => null,
            ),
            $this
        );
        return $query;
    }

    function clientesCombinados()
    {
        $ClienteProduto = &ClassRegistry::init('ClienteProduto');
        $ClienteProdutoServico2 = &ClassRegistry::init('ClienteProdutoServico2');
        $dbo = $this->getDataSource();
        $fields = array(
            "DISTINCT ClienteProdutoServico2.codigo_cliente_pagador",
            'ClienteProduto.codigo_cliente',
            'ClienteProduto.codigo_produto',
        );
        $query = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => $ClienteProdutoServico2->databaseTable . '.' . $ClienteProdutoServico2->tableSchema . '.' . $ClienteProdutoServico2->useTable,
                'alias' => 'ClienteProdutoServico2',
                'limit' => null,
                'offset' => null,
                'joins' => array(
                    array(
                        'table' => $ClienteProduto->databaseTable . '.' . $ClienteProduto->tableSchema . '.' . $ClienteProduto->useTable,
                        'alias' => 'ClienteProduto',
                        'type' => 'INNER',
                        'conditions' => array('ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto'),
                    ),
                ),
                'conditions' => null,
                'order' => null,
                'group' => null,
            ),
            $this
        );
        return $query;
    }

    function retornarClienteSubTipo($codigo_cliente)
    {
        $cliente = $this->carregar($codigo_cliente);
        return ClienteSubTipo::subTipo($cliente['Cliente']['codigo_cliente_sub_tipo']);
    }

    function ativo($codigo_cliente)
    {
        $conditions = array($this->name . '.codigo' => $codigo_cliente, 'ativo' => 1);
        return ($this->find('count', compact('conditions')) > 0);
    }

    function carregarClientePagadorSemBloqueio($codigo_cliente_transportador, $codigo_cliente_embarcador, $codigo_cliente_logado, $codigo_produto)
    {
        $this->EmbarcadorTransportador     = &ClassRegistry::init('EmbarcadorTransportador');
        $this->MatrizFilial             = &ClassRegistry::init('MatrizFilial');
        $this->ClienteProduto             = &ClassRegistry::init('ClienteProduto');

        if (!$codigo_cliente_embarcador)
            $codigo_cliente_embarcador = $codigo_cliente_transportador;

        $pagador = $this->EmbarcadorTransportador->carregarClientePagador($codigo_cliente_transportador, $codigo_cliente_embarcador, $codigo_produto);
        if ($pagador) {
            $cliente = $this->ClienteProduto->carregarPagadorPorClienteProduto($pagador['Cliente']['codigo'], $codigo_produto);
            if ($cliente) return $cliente;
        }

        $pagador = $this->MatrizFilial->carregarClientePagador($codigo_cliente_logado, $codigo_produto);
        if ($pagador) {
            $cliente = $this->ClienteProduto->carregarPagadorPorClienteProduto($pagador['Cliente']['codigo'], $codigo_produto);
            if ($cliente) return $cliente;
        }

        $cliente = $this->ClienteProduto->carregarPagadorPorClienteProduto($codigo_cliente_logado, $codigo_produto);
        if ($cliente) return $cliente;

        return FALSE;
    }


    function carregarEnderecoPorDocumento($documento)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'ClienteEndereco' => array(
                    'foreignKey' => FALSE,
                    'conditions' => 'Cliente.codigo = ClienteEndereco.codigo_cliente AND ClienteEndereco.codigo_tipo_contato = 2', // 2  - CONTATO COMERCIAL
                ),
                'Endereco' => array(
                    'foreignKey' => FALSE,
                    'conditions' => 'Endereco.codigo = ClienteEndereco.codigo_endereco',
                ),
            )
        ));

        $conditions = array('codigo_documento' => $documento);
        return $this->find('first', compact('conditions'));
    }

    function estatisticaCadastroClientes($ano, $campo_data_referencia)
    {
        $fields = array(
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 1 THEN 1 ELSE 0 END) AS [Jan]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 2 THEN 1 ELSE 0 END) AS [Fev]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 3 THEN 1 ELSE 0 END) AS [Mar]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 4 THEN 1 ELSE 0 END) AS [Abr]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 5 THEN 1 ELSE 0 END) AS [Mai]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 6 THEN 1 ELSE 0 END) AS [Jun]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 7 THEN 1 ELSE 0 END) AS [Jul]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 8 THEN 1 ELSE 0 END) AS [Ago]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 9 THEN 1 ELSE 0 END) AS [Set]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 10 THEN 1 ELSE 0 END) AS [Out]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 11 THEN 1 ELSE 0 END) AS [Nov]",
            "SUM(CASE WHEN MONTH($campo_data_referencia) = 12 THEN 1 ELSE 0 END) AS [Dez]",
        );
        $conditions = array(
            "$campo_data_referencia BETWEEN ? AND ?" => array($ano . '0101 00:00:00', $ano . '1231 23:59:59'),
        );
        $recursive = -1;
        $result = $this->find('all', compact('fields', 'conditions', 'recursive'));
        if ($result) {
            return $result;
        } else {
            return array(array(array("Jan" => 0, "Fev" => 0, "Mar" => 0, "Abr" => 0, "Mai" => 0, "Jun" => 0, "Jul" => 0, "Ago" => 0, "Set" => 0, "Out" => 0, "Nov" => 0, "Dez" => 0)));
        }
    }

    public function ClientesFolhamatic()
    {
        $Folhamatic = "
	SELECT
	'E010'as NOME_REGISTRO,
	RHHealth.publico.lpad(cli.codigo, 20, '0') as CODIGO_CLIENTE,
	left(RHHealth.publico.rpad(cli.razao_social, 100, ''),100) COLLATE sql_latin1_general_cp1251_ci_as as NOME,
	(CONVERT(VARCHAR(11),cli.data_inclusao,112)) AS DATA_INCLUSAO,

	/* Dados de endereco */
	isNull(RHHealth.publico.rpad(left(uvwend.endereco_tipo,15),15,''),'               ') COLLATE sql_latin1_general_cp1251_ci_as as TIPO_LOGRADOURO,
	left(RHHealth.publico.rpad(uvwend.endereco_logradouro, 100, ''),100) COLLATE sql_latin1_general_cp1251_ci_as as LOGRADOURO,
	left(RHHealth.publico.rpad(cli_end.numero, 10, ''),100) as NUMERO_LOGRADOURO,
	left(isNull(RHHealth.publico.rpad(cli_end.complemento, 50, ''),'                                                  '),50) as COMPLEMENTO_LOGRADOURO,
	left(isNull(RHHealth.publico.rpad(uvwend.endereco_bairro, 30, ''),'                              '),30) COLLATE sql_latin1_general_cp1251_ci_as as BAIRRO,
	left(RHHealth.publico.rpad(uvwend.endereco_estado, 2, ''),2) as ESTADO,
	left(isNull(RHHealth.publico.lpad(isnull(end_city_distrito.ibge, end_city.ibge), 7, 0),'0000000'),7) as CIDADE_IBGE,
			------------------------
			left(RHHealth.publico.rpad(uvwend.endereco_cep, 8, 0),8) as CEP,
			'01058' as PAIS,
			/* ------------------- */

			left(RHHealth.publico.rpad(cli.codigo_documento, 14, ' '),14) as CNPJ,
			--    '                  ' as INSCRICAO_ESTADUAL,
			left(isNull(RHHealth.publico.rpad(CASE WHEN LEN(cli.codigo_documento)<14 THEN NULL ELSE cli.inscricao_estadual END, 18, ' '), '                  '),18) as INSCRICAO_ESTADUAL,
			--   isNull(RHHealth.publico.lpad(cli.ccm, 14, ''), '              ') as INSCRICAO_MUNICIPAL,
			left(RHHealth.publico.lpad(replace(replace(replace(replace(replace(isNull(cli.ccm, '              '), '-',''), '.',''), '/', ''),'ISENTO',''),'NULL',''), 14, ' '),14) as INSCRICAO_MUNICIPAL,
			left(isNull(RHHealth.publico.lpad(replace(cli.suframa,'NULL',''), 9, ''), '         '),9) as SUFRAMA,


			/* Dados de contato */
			left(isNull(RHHealth.publico.rpad((
			SELECT TOP 1
			clicontel.nome
			FROM
			RHHealth.dbo.cliente_contato clicontel
			WHERE
			clicontel.codigo_cliente = cli.codigo AND
			clicontel.codigo_tipo_contato = 2 AND
			clicontel.codigo_tipo_retorno = 3
			), 35, ''), '                                   '),35) COLLATE sql_latin1_general_cp1251_ci_as as CONTATO,

			/* ------------------- */
			/* DADOS DO TELEFONE */
			/* ------------------- */
			left(isNull(
			RHHealth.publico.rpad(
			'('+SUBSTRING(
			(
			SELECT TOP 1
			RHHealth.publico.lpad(clicontel.descricao,10,'0')
			FROM
			RHHealth.dbo.cliente_contato clicontel
			WHERE
			clicontel.codigo_cliente = cli.codigo AND
			clicontel.codigo_tipo_contato = 2 AND
			clicontel.codigo_tipo_retorno = 1
			)
			,1,2) collate SQL_Latin1_General_CP1_CI_AS +')'+SUBSTRING(
			(
			SELECT TOP 1
			RHHealth.publico.lpad(clicontel.descricao,10,'0')
			FROM
			RHHealth.dbo.cliente_contato clicontel
			WHERE
			clicontel.codigo_cliente = cli.codigo AND
			clicontel.codigo_tipo_contato = 2 AND
			clicontel.codigo_tipo_retorno = 1
			)
			, 3, 4) collate SQL_Latin1_General_CP1_CI_AS +'-'+SUBSTRING(
			(
			SELECT TOP 1
			RHHealth.publico.lpad(clicontel.descricao,10,'0')
			FROM
			RHHealth.dbo.cliente_contato clicontel
			WHERE
			clicontel.codigo_cliente = cli.codigo AND
			clicontel.codigo_tipo_contato = 2 AND
			clicontel.codigo_tipo_retorno = 1
			)
			, 7, 12),16,'') collate SQL_Latin1_General_CP1_CI_AS, '(00)0000-0000   '),16) as TELEFONE,

			/* ------------------- */
			/* DADOS DO FAX */
			/* ------------------- */
			left(isNull(RHHealth.publico.rpad(
			'('+SUBSTRING(
			(
			SELECT TOP 1
			RHHealth.publico.lpad(cliconfax.descricao,10,'0')
			FROM
			RHHealth.dbo.cliente_contato cliconfax
			WHERE
			cliconfax.codigo_cliente = cli.codigo AND
			cliconfax.codigo_tipo_contato = 2 AND
			cliconfax.codigo_tipo_retorno = 3
			)
			,1,2) collate SQL_Latin1_General_CP1_CI_AS +')'+SUBSTRING(
			(
			SELECT TOP 1
			RHHealth.publico.lpad(cliconfax.descricao,10,'0')
			FROM
			RHHealth.dbo.cliente_contato cliconfax
			WHERE
			cliconfax.codigo_cliente = cli.codigo AND
			cliconfax.codigo_tipo_contato = 2 AND
			cliconfax.codigo_tipo_retorno = 3
			)
			, 3, 4) collate SQL_Latin1_General_CP1_CI_AS +'-'+SUBSTRING(
			(
			SELECT TOP 1
			RHHealth.publico.lpad(cliconfax.descricao,10,'0')
			FROM
			RHHealth.dbo.cliente_contato cliconfax
			WHERE
			cliconfax.codigo_cliente = cli.codigo AND
			cliconfax.codigo_tipo_contato = 2 AND
			cliconfax.codigo_tipo_retorno = 3
			)
			, 7, 12), 16,'') collate SQL_Latin1_General_CP1_CI_AS ,'(00)0000-0000   '),16) as FAX,

				'        ' as DATA_ALTERACAO_EFISCAL, -- // ????

				'S' as CLIENTE,
				'N' as FORNECEDOR,
				'N' as PRODUTOR_RURAL, -- // ????
				'N' as FORNECEDOR_TRIBUTARIO, -- // ????
				'N' as SIMPLES_NACIONAL, -- // ????
				'N' as INSCRITO_MUNICIPIO, -- // ????

				/* ------------------- */
				/* DADOS DO EMAIL */
				/* ------------------- */
				isNull(RHHealth.publico.rpad(
				(
				SELECT TOP 1
				cliconmail.descricao
				FROM
				RHHealth.dbo.cliente_contato cliconmail
				WHERE
				cliconmail.codigo_cliente = cli.codigo AND
				cliconmail.codigo_tipo_contato = 2 AND
				cliconmail.codigo_tipo_retorno = 2
				), 100, ''), '                                                                                                    ') as EMAIL,
				/* ------------------- */

				'                                                                                                    ' as WEBSITE, -- // ????
				'N' as HOST_SITES, -- // ????
				'               ' as HOST_IP_SITE, -- // ????
				'                                                                                                    ' as HOST_WEBSITE, -- // ????
				'        ' as HOST_DATA_INICIO, -- // ????
				'        ' as HOST_DATA_FIM, -- // ????
				'N' as GATEWAY_PAGAMENTOS, -- // ????
				'        ' as GATEWAY_DATA_INICIO, -- // ????
				'        ' as GATEWAY_DATA_FIM, -- // ????
				'N' as LOJA_VIRTUAL, -- // ????
				'               ' as LOJA_VIRTUAL_IP, -- // ????
				'                                                                                                    ' as LOJA_VIRTUAL_SITE, -- // ????
				'        ' as LOJA_VIRTUAL_DATA_INICIO, -- // ????
				'        ' as LOJA_VIRTUAL_DATA_FIM, -- // ????
				0 as CONTROLE_DO_SISTEMA -- // ????
				FROM
				RHHealth.dbo.[cliente] cli (nolock)

			--LEFT JOIN RHHealth.dbo.documento doc
			--    ON doc.codigo = cli.codigo_documento

			INNER JOIN RHHealth.dbo.cliente_endereco cli_end
			ON cli_end.codigo_cliente = cli.codigo

			LEFT JOIN RHHealth.dbo.uvw_endereco uvwend
			ON cli_end.codigo_endereco = uvwend.endereco_codigo

			LEFT JOIN RHHealth.dbo.endereco_cidade end_city
			ON uvwend.endereco_codigo_cidade = end_city.codigo

			LEFT JOIN RHHealth.dbo.endereco_distrito
			ON endereco_distrito.codigo = uvwend.endereco_codigo_distrito

			LEFT JOIN RHHealth.dbo.endereco_cidade end_city_distrito
			ON endereco_distrito.codigo_endereco_cidade = end_city_distrito.codigo

			inner JOIN
			(
			select distinct cliente from dbnavegarqNatec..notafis where notafis.dtemissao >= '2012-01-01 00:00:00'
			) as notafis ON (cli.codigo = notafis.cliente )
			WHERE
			cli_end.codigo_tipo_contato = 2 and cli.tipo_unidade='F'
			";

        return $Folhamatic;
    }

    public function clienteTemProdutoAtivo($codigo_cliente, $codigo_produto, $servico = null)
    {
        $this->ClienteProduto = &ClassRegistry::init('ClienteProduto');
        $this->ClienteProdutoServico2 = &ClassRegistry::init('ClienteProdutoServico2');
        $joins = array(
            array(
                'table' => $this->ClienteProduto->databaseTable . '.' . $this->ClienteProduto->tableSchema . '.' . $this->ClienteProduto->useTable,
                'alias' => 'ClienteProduto',
                'conditions' => 'ClienteProduto.codigo_cliente =  Cliente.codigo',
                'type' => 'INNER'
            ),
        );
        $conditions = array(
            'Cliente.codigo' => $codigo_cliente,
            'Cliente.ativo' => 1,
            'ClienteProduto.codigo_motivo_bloqueio' => 1,
            'ClienteProduto.codigo_produto' => $codigo_produto,
        );
        if ($servico) {
            $joins[] = array(
                'table' => $this->ClienteProdutoServico2->databaseTable . '.' . $this->ClienteProdutoServico2->tableSchema . '.' . $this->ClienteProdutoServico2->useTable,
                'alias' => 'ClienteProdutoServico2',
                'conditions' => 'ClienteProdutoServico2.codigo_cliente_produto =  ClienteProduto.codigo',
                'type' => 'INNER'
            );
            $conditions[] = array('ClienteProdutoServico2.codigo_servico' => $servico);
        }
        return $this->find('count', compact('conditions', 'joins'));
    }

    function carregarClientePagador($codigo_cliente_transportador, $codigo_cliente_embarcador, $codigo_cliente_logado, $codigo_produto)
    {
        $this->EmbarcadorTransportador     = &ClassRegistry::init('EmbarcadorTransportador');
        $this->MatrizFilial             = &ClassRegistry::init('MatrizFilial');

        if (!$codigo_cliente_embarcador)
            $codigo_cliente_embarcador = $codigo_cliente_transportador;

        $pagador = $this->EmbarcadorTransportador->carregarClientePagador($codigo_cliente_transportador, $codigo_cliente_embarcador, $codigo_produto);
        if ($pagador) {
            return $pagador;
        }

        $pagador = $this->MatrizFilial->carregarClientePagador($codigo_cliente_logado, $codigo_produto);
        if ($pagador) {
            return $pagador;
        }

        $pagador = $this->carregar($codigo_cliente_logado);
        return $pagador;
    }

    function completarTViagViagemCodigosDbBuonny($viag_viagem)
    {
        if (isset($viag_viagem['Embarcador']['pjur_cnpj'])) {
            $cliente = $this->porCNPJ($viag_viagem['Embarcador']['pjur_cnpj']);
            if ($cliente)
                $viag_viagem['Embarcador']['codigo'] = key($cliente);
        }
        if (isset($viag_viagem['Transportador']['pjur_cnpj'])) {
            $cliente = $this->porCNPJ($viag_viagem['Transportador']['pjur_cnpj']);
            if ($cliente)
                $viag_viagem['Transportador']['codigo'] = key($cliente);
        }
        return $viag_viagem;
    }

    function qtdClientesFaturadosPorAno($filtros)
    {
        $this->Notafis = ClassRegistry::init('Notafis');
        $this->Cliente = ClassRegistry::init('Cliente');
        $data_inicial = $filtros['ano'] . '-01-01';
        $data_final = $filtros['ano'] . '-12-31';

        $conditions = array(
            'Notafis.dtemissao BETWEEN ? AND ?' => array(
                $data_inicial,
                $data_final
            ),
        );

        if (!empty($filtros['codigo_cliente'])) {
            $conditions['Notafis.cliente'] = $filtros['codigo_cliente'];
        }
        if (!empty($filtros['codigo_seguradora'])) {
            $conditions['Cliente.codigo_seguradora'] = $filtros['codigo_seguradora'];
        }
        if (!empty($filtros['codigo_corretora'])) {
            $conditions['Cliente.codigo_corretora'] = $filtros['codigo_corretora'];
        }

        $fields = array(
            'COUNT(distinct(cliente)) as qtd_clientes',
            'SUBSTRING(CONVERT(VARCHAR,dtemissao, 103), 4, 7) AS mes',
        );

        $group = array(
            'SUBSTRING(CONVERT(VARCHAR,dtemissao, 103), 4, 7)',
        );
        $joins = array(
            array(
                'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'Notafis.cliente = Cliente.codigo',
            ),
        );
        $resultado = $this->Notafis->find('all', array(
            'conditions' => $conditions,
            'fields' => $fields,
            'group' => $group,
            'joins' => $joins,
        ));

        return $resultado;
    }

    public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array())
    {
        if (isset($extra['method']) && $extra['method'] == 'consultar_pgr') {
            return $this->listarAnaliticoPgr($conditions, 'all', $limit, compact('page'));
        }
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];
        if (isset($extra['group']))
            $group = $extra['group'];
        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
    }

    public function paginateCount($conditions = null, $recursive = 0, $extra = array())
    {
        if (isset($extra['method']) && $extra['method'] == 'consultar_pgr') {
            return $this->listarAnaliticoPgr($conditions, 'count', NULL);
        }

        $joins = null;

        if (isset($extra['joins'])) {
            $joins = $extra['joins'];
        }

        if (isset($extra['group']) && !empty($extra['group'])) {
            $group = $extra['group'];

            return count(
                $this->find(
                    "all",
                    array(
                        "fields" => $group,
                        "conditions" => $conditions,
                        "joins" => $joins,
                        "group" => $group
                    )
                )
            );
        }

        return $this->find('count', compact('conditions', 'recursive', 'joins'));
    }

    public function listagemSinteticaPgr($conditions, $agrupamento = 1)
    {
        switch ($agrupamento) {
            case 1:
                $nome = 'corretora';
                break;
            case 2:
                $nome = 'seguradora';
                break;
            case 3:
                $nome = 'gestor';
                break;
            case 4:
                $nome = 'filial';
                break;
            case 5:
                $nome = 'gestor_npe';
                break;
        }

        $query_analitico = $this->listarAnaliticoPgr($conditions, 'sql');
        $query_sintetica = " WITH analiticaPgr AS (" . $query_analitico . ")";
        $query_sintetica .= " SELECT COUNT(analitica.$nome) as total, analitica.$nome AS nome, analitica.codigo_$nome AS codigo, ";
        $query_sintetica .= " SUM(CASE WHEN validade_apolice IS NULL OR validade_apolice < getdate() THEN 1 ELSE 0 END) AS sem_pgr, ";
        $query_sintetica .= " SUM(CASE WHEN validade_apolice >= getdate() THEN 1 ELSE 0 END) AS com_pgr, ";
        $query_sintetica .= " SUM(CASE WHEN regra_de_aceite >= '1' THEN 1 ELSE 0 END) AS com_regra, ";
        $query_sintetica .= " SUM(CASE WHEN regra_de_aceite = '0' OR regra_de_aceite IS NULL THEN 1 ELSE 0 END ) AS sem_regra ";
        $query_sintetica .= " FROM analiticaPgr as analitica ";
        $query_sintetica .= " WHERE analitica.$nome IS NOT NULL ";
        $query_sintetica .= " GROUP BY analitica.$nome, analitica.codigo_$nome";

        $dados = $this->query($query_sintetica);
        return $dados;
    }

    function estatisticaAssinaturaPorCliente($dados)
    {
        $this->Pedido = ClassRegistry::init('Pedido');
        $ano_mes = $dados['Cliente']['ano_referencia'] . str_pad($dados['Cliente']['mes_referencia'], 2, '0', STR_PAD_LEFT);
        $data_inicial = $ano_mes . '01 00:00:00';
        $data_final   = date('Ymt', strtotime($dados['Cliente']['ano_referencia'] . '-' . $dados['Cliente']['mes_referencia'])) . ' 23:59:59';

        $pedido = $this->Pedido->find('count', array('conditions' => array(
            'Pedido.mes_referencia' => $dados['Cliente']['mes_referencia'],
            'Pedido.ano_referencia' => $dados['Cliente']['ano_referencia'],
            'Pedido.codigo_servico' => Pedido::CODIGO_SERVICO_ASSINATURA
        )));

        $ano_mes_atual = date('Ym');

        if ($pedido > 0) {
            $this->ItemPedido = ClassRegistry::init('ItemPedido');
            return $this->ItemPedido->listarItemPedidosPorClienteAssinatura($dados);
        } else if ($ano_mes == $ano_mes_atual) {
            $conditions = array(
                'ClienteProduto.codigo_motivo_bloqueio = 1',
                'ClienteProdutoServico2.valor > 0'
            );
            if (!empty($dados['Cliente']['codigo_cliente'])) {
                $conditions['ClienteProduto.codigo_cliente'] = $dados['Cliente']['codigo_cliente'];
            }

            $this->ClienteProduto         = ClassRegistry::init('ClienteProduto');
            $this->ClienteProdutoDesconto = ClassRegistry::init('ClienteProdutoDesconto');
            // $this->ClienteProdutoServico  = ClassRegistry::init('ClienteProdutoServico2');
            $this->Produto                = ClassRegistry::init('Produto');

            $sql_desconto = $this->ClienteProdutoDesconto->find(
                'all',
                array(
                    'fields' => array('SUM(ClienteProdutoDesconto.valor)'),
                    'conditions' => array(
                        'ClienteProdutoDesconto.codigo_cliente = ClienteProduto.codigo_cliente',
                        'ClienteProdutoDesconto.codigo_produto = ClienteProduto.codigo_produto',
                        'ClienteProdutoDesconto.mes_ano BETWEEN \'' . $data_inicial . '\' AND \'' . $data_final . '\'',
                    ),
                    'returnSQL' => true,
                    'recursive' => false
                )
            );

            $this->ClienteProduto->bindModel(
                array(
                    'belongsTo' =>
                    array(
                        'Produto' => array(
                            'foreignKey' => false,
                            'type'       => 'INNER',
                            'conditions' => array(
                                'Produto.codigo = ClienteProduto.codigo_produto',
                                'Produto.codigo not' => $this->Produto->produtos_quantitativos(),
                                'Produto.codigo_naveg IS NOT NULL',
                                'Produto.codigo_naveg != \'\'',
                                'Produto.mensalidade = 1',
                                'Produto.ativo = 1'
                            )
                        ),
                        'Cliente' => array(
                            'foreignKey' => false,
                            'type'       => 'INNER',
                            'conditions' => array('Cliente.codigo = ClienteProduto.codigo_cliente')
                        ),
                        'ClienteProdutoServico2' => array(
                            'foreignKey' => false,
                            'type'       => 'LEFT',
                            'conditions' => 'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo',
                        ),
                        'ProdutoServico' => array(
                            'foreignKey' => false,
                            'type'       => 'INNER',
                            'conditions' => array(
                                'ClienteProdutoServico2.codigo_servico = ProdutoServico.codigo_servico',
                                'Produto.codigo = ProdutoServico.codigo_produto',
                                'ProdutoServico.ativo = 1'
                            )
                        )
                    )
                )
            );
            $resultado = $this->ClienteProduto->find(
                'all',
                array(
                    'fields' => array(
                        'ClienteProduto.codigo_cliente as codigo_cliente',
                        'Cliente.razao_social',
                        'ClienteProduto.codigo_produto as codigo_produto',
                        'Produto.descricao as produto',
                        '1 as quantidade',
                        'SUM(COALESCE(ClienteProdutoServico2.valor,0)) as valor',
                        'COALESCE((' . $sql_desconto . '),0) as desconto',
                    ),
                    'conditions' => $conditions,
                    'group' => array('ClienteProduto.codigo_produto', 'ClienteProduto.codigo_cliente', 'Cliente.razao_social', 'Produto.descricao')
                )
            );
            $retorno = array();
            foreach ($resultado as $linha) {
                $retorno[$linha[0]['produto']][$linha[0]['codigo_cliente']] = array(
                    'codigo_produto' => $linha[0]['codigo_produto'],
                    'nome'           => $linha['Cliente']['razao_social'],
                    'quantidade'     => $linha[0]['quantidade'],
                    'total'          => $linha[0]['valor'] - $linha[0]['desconto'],
                    'desconto'       => $linha[0]['desconto'],
                    'valor'          => $linha[0]['valor'],
                );
            }
            return $retorno;
        }
    }

    function importacao_cliente_unidade($data)
    {
        $this->ClienteImplantacao = &ClassRegistry::init('ClienteImplantacao');
        $this->GrupoEconomico = &ClassRegistry::init('GrupoEconomico');
        $this->GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');

        $retorno = '';

        $data['Cliente']['ativo'] = 1;

        if (isset($data['Cliente']['codigo']) && !empty($data['Cliente']['codigo'])) {

            $data['Cliente']['codigo'] = $data['Cliente']['codigo'];

            $validate = $this->validacao_cliente($data, 'atualizar');

            if ($validate) {
                if (!parent::atualizar($data, false)) {
                    $erro_cliente = '';
                    foreach ($this->validationErrors as $key => $value) {
                        $erro_cliente .= utf8_decode($value) . '|';
                        $this->validationErrors[$key] = $erro_cliente;
                    }
                    $retorno['Unidade'] = $this->validationErrors;
                } else {

                    $codigo_cliente = $data['Cliente']['codigo'];

                    //INSERE SEMPRE UMA UNIDADE. O CODIGO DA EMPRESA MATRIZ É ENVIADO NO METODO DE IMPORTACAO
                    $grupo_economico = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $data['GrupoEconomico']['codigo_cliente'])));

                    if (!empty($grupo_economico)) {

                        $dados_grupo_economico_cliente = array(
                            'GrupoEconomicoCliente' => array(
                                'codigo_cliente' => $codigo_cliente,
                                'codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo']
                            )
                        );

                        $conditions_grupo_economico = array(
                            'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente,
                            'GrupoEconomicoCliente.codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo']
                        );

                        $consulta_grupo_economico_cliente = $this->GrupoEconomicoCliente->find('first', array('conditions' => $conditions_grupo_economico));

                        if (empty($consulta_grupo_economico_cliente)) {

                            if (!$this->GrupoEconomicoCliente->incluir($dados_grupo_economico_cliente)) {
                                $erro_grupo_economico_cliente = '';
                                foreach ($this->GrupoEconomicoCliente->validationErrors as $key => $value) {
                                    $erro_grupo_economico_cliente .= utf8_decode($value) . '|';
                                    $this->GrupoEconomicoCliente->validationErrors[$key] = $erro_grupo_economico_cliente;
                                }
                                $retorno['GrupoEconomicoCliente'] = $this->GrupoEconomicoCliente->validationErrors;
                            }
                        } else {

                            $dados_grupo_economico_cliente['GrupoEconomicoCliente']['codigo'] = $consulta_grupo_economico_cliente['GrupoEconomicoCliente']['codigo'];

                            if (!$this->GrupoEconomicoCliente->atualizar($dados_grupo_economico_cliente)) {
                                $erro_grupo_economico_cliente = '';
                                foreach ($this->GrupoEconomicoCliente->validationErrors as $key => $value) {
                                    $erro_grupo_economico_cliente .= utf8_decode($value) . '|';
                                    $this->GrupoEconomicoCliente->validationErrors[$key] = $erro_grupo_economico_cliente;
                                }
                                $retorno['GrupoEconomicoCliente'] = $this->GrupoEconomicoCliente->validationErrors;
                            }
                        }
                    } //fim if empty grupo_economico

                }
            } else {
                $retorno['Unidade'] = $this->validationErrors;
            }
        } else {
            $validate = $this->validacao_cliente($data, 'incluir');

            if ($validate) {
                if (!parent::incluir($data, false)) {
                    $erro_cliente = '';
                    foreach ($this->validationErrors as $key => $value) {
                        $erro_cliente .= utf8_decode($value) . '|';
                        $this->validationErrors[$key] = $erro_cliente;
                    }
                    $retorno['Unidade'] = $this->validationErrors;
                } else {
                    $codigo_cliente = $this->id;
                    //INSERE SEMPRE UMA UNIDADE. O CODIGO DA EMPRESA MATRIZ É ENVIADO NO METODO DE IMPORTACAO
                    $grupo_economico = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $data['GrupoEconomico']['codigo_cliente'])));
                    if (!empty($grupo_economico)) {
                        $dados_grupo_economico_cliente = array(
                            'GrupoEconomicoCliente' => array(
                                'codigo_cliente' => $codigo_cliente,
                                'codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo']
                            )
                        );

                        if (!$this->GrupoEconomicoCliente->incluir($dados_grupo_economico_cliente)) {
                            $erro_grupo_economico_cliente = '';
                            foreach ($this->GrupoEconomicoCliente->validationErrors as $key => $value) {
                                $erro_grupo_economico_cliente .= utf8_decode($value) . '|';
                                $this->GrupoEconomicoCliente->validationErrors[$key] = $erro_grupo_economico_cliente;
                            }
                            $retorno['GrupoEconomicoCliente'] = $this->GrupoEconomicoCliente->validationErrors;
                        }
                    }
                }
            } else {
                $retorno['Unidade'] = $this->validationErrors;
            }
        }

        return $retorno;
    }

    function localiza_cliente_importacao($data)
    {
        $this->GrupoEconomico = &ClassRegistry::Init('GrupoEconomico');
        $this->GrupoEconomicoCliente = &ClassRegistry::Init('GrupoEconomicoCliente');

        $codigo_cliente_grupo_economico = $data['codigo_cliente_grupo_economico'];
        $codigo_externo = $data['codigo_externo'];
        $retorno = '';

        $conditions = array(
            'GrupoEconomico.codigo_cliente' => $codigo_cliente_grupo_economico,
            'Cliente.codigo_externo' => substr($codigo_externo, 0, 50),
            'Cliente.ativo' => 1
        );

        $fields = array(
            'Cliente.codigo', 'Cliente.codigo_documento', 'Cliente.razao_social', 'Cliente.nome_fantasia', 'Cliente.inscricao_estadual', 'Cliente.ccm', 'Cliente.ativo', 'Cliente.cnae', 'Cliente.codigo_regime_tributario',
            'GrupoEconomico.codigo', 'GrupoEconomico.descricao', 'GrupoEconomico.codigo_cliente',
            'GrupoEconomicoCliente.codigo', 'GrupoEconomicoCliente.codigo_grupo_economico', 'GrupoEconomicoCliente.codigo_cliente', 'GrupoEconomicoCliente.bloqueado'
        );

        $joins     = array(
            array(
                'table'    => $this->GrupoEconomicoCliente->databaseTable . '.' . $this->GrupoEconomicoCliente->tableSchema . '.' . $this->GrupoEconomicoCliente->useTable,
                'alias'    => 'GrupoEconomicoCliente',
                'conditions' => 'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente',
            ),
            array(
                'table'    => $this->GrupoEconomico->databaseTable . '.' . $this->GrupoEconomico->tableSchema . '.' . $this->GrupoEconomico->useTable,
                'alias'    => 'GrupoEconomico',
                'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
            ),
        );

        $dados = $this->find('first', compact('conditions', 'fields', 'joins'));

        if (empty($dados)) {
            $retorno['Erro']['Cliente'] = array('codigo_cliente' => utf8_decode('Unidade não encontrada!'));
        } else {
            $retorno['Dados'] = $dados;
        }

        return $retorno;
    }

    function geraCnpjFicticioUnico($cnpj, $qtd_unidades = 0)
    {

        $model_documento = &ClassRegistry::init('Documento');
        $qtd_unidades++;

        $n = str_split(preg_replace('/[^0-9]/', '', $cnpj));
        $n[8] = 9;

        if (strlen($qtd_unidades) > 1) {
            $array_unidade = array_reverse(str_split($qtd_unidades));

            for ($i = 0; $i <= (count($array_unidade) - 1); $i++) {
                $n[11 - $i] = $array_unidade[$i];
            }
        } else {
            $n[11] = $qtd_unidades;
        }

        $d1 = $n[11] * 2 + $n[10] * 3 + $n[9] * 4 + $n[8] * 5 + $n[7] * 6 + $n[6] * 7 + $n[5] * 8 + $n[4] * 9 + $n[3] * 2 + $n[2] * 3 + $n[1] * 4 + $n[0] * 5;
        $d1 = 11 - ($d1 % 11);

        if ($d1 >= 10)
            $d1 = 0;

        $d2 = $d1 * 2 + $n[11] * 3 + $n[10] * 4 + $n[9] * 5 + $n[8] * 6 + $n[7] * 7 + $n[6] * 8 + $n[5] * 9 + $n[4] * 2 + $n[3] * 3 + $n[2] * 4 + $n[1] * 5 + $n[0] * 6;
        $d2 = 11 - ($d2 % 11);

        if ($d2 >= 10)
            $d2 = 0;

        $n[12] = $d1;
        $n[13] = $d2;

        $codigo_documento = implode($n);

        if ($model_documento->isCNPJ($codigo_documento)) {
            return $codigo_documento;
        }
    }

    function GeraCnpjFicticio($cnpj)
    {
        $parte1 = substr($cnpj, 0, 8);

        $conditions = array('codigo_documento LIKE ' => $parte1 . '%');
        $fields = array('MAX(SUBSTRING(codigo_documento,9,4)) as codigo_documento');

        $qtd_cnpj = $this->find('first', compact('conditions', 'fields', 'joins'));

        $parte2 = $qtd_cnpj[0]['codigo_documento'] + 1;

        if (strlen($parte2) < 4) {
            $parte2 = str_pad($parte2, 3, 0, STR_PAD_LEFT);
        }

        if (substr($parte2, 0, 1) <> "9") {
            $parte2 = "9" . $parte2;
        }

        $digito_verificador = '99';

        $cnpj_ficticio = $parte1 . $parte2 . $digito_verificador;

        $retorno = $cnpj_ficticio;
        return $retorno;
    }

    function validacao_cliente($data, $acao)
    {
        $retorno = '';
        if ($acao == "atualizar") {
            $consulta_ativo = $this->unicoAtivoImportacao($data);

            if (!$consulta_ativo) {
                $this->validationErrors = array('ativo' => 'Já existe outro código com mesmo CNPJ ativo!');
            }
        }

        if (empty($data['Cliente']['razao_social'])) {
            $this->validationErrors = array('Informe a Razão Social!');
        }

        if (empty($data['Cliente']['codigo_documento'])) {
            $this->validationErrors = array('Informe o documento!');
        } else {
            if ($acao == "incluir") {
                $consulta_codigo_documento = $this->find("first", array('conditions' => array('codigo_documento' => $data['Cliente']['codigo_documento'], 'ativo' => 1)));
                if (!empty($consulta_codigo_documento)) {
                    $this->validationErrors = array(utf8_decode('CNPJ já existente na base!'));
                }
            }
        }


        if (empty($data['Cliente']['inscricao_estadual'])) {
            $this->validationErrors = array('Informe a Inscrição Estadual!');
        } else {
            $consulta_incricao_estadual = $this->validaInscricaoEstadualImportacao($data);
            if (!$consulta_incricao_estadual) {
                $this->validationErrors = array('Inscrição Estadual inválida!');
            }
        }


        if (empty($data['Cliente']['ccm'])) {
            $this->validationErrors = array('Informe a Inscrição Municipal!');
        }

        if (empty($data['Cliente']['codigo_regime_tributario'])) {
            $this->validationErrors = array('Informe o Regime Tributário!');
        }

        if (!empty($data['Cliente']['cnae'])) {
            $consulta_cnae = $this->cnaeValido($data);
            if (!$consulta_cnae) {
                $this->validationErrors = array('O Cnae informado é invalido!');
            }
        }

        if (empty($this->validationErrors)) {
            return true;
        } else {
            return false;
        }
    }

    function validaInscricaoEstadualImportacao($dados)
    {
        $this->data = $dados;

        $cgccpf = Comum::soNumero($this->data['Cliente']['codigo_documento']);
        if (strlen($cgccpf) > 11) {
            if (isset($this->data['ClienteEndereco']['codigo_endereco'])) {
                $endereco  = $this->data['ClienteEndereco']['logradouro'] . ' ' . $this->data['ClienteEndereco']['numero'] . ' - ' . $this->data['ClienteEndereco']['cidade'] . ' - ' . $this->data['ClienteEndereco']['estado_abreviacao'];
                $estado    = $endereco['ClienteEndereco']['estado_abreviacao'];
                if ($this->data['Cliente']['inscricao_estadual'] != 'ISENTO') {
                    if (!empty($estado)) {
                        return $this->checkIE($this->data['Cliente']['inscricao_estadual'], $estado);
                    } else {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    function unicoAtivoImportacao($dados = null)
    {
        if (!empty($dados)) {
            $this->data = $dados;
        }

        if ($this->data[$this->name]['ativo'] == true) {
            $conditions = array(
                "{$this->name}.codigo_documento" => $this->data[$this->name]['codigo_documento'],
                "{$this->name}.ativo" => 1,
                'NOT' => array("{$this->name}.codigo" => $this->data[$this->name]['codigo'])
            );

            $outro_ativo = $this->find('count', array('recursive' => -1, 'conditions' => $conditions));

            return ($outro_ativo == false);
        }
        return true;
    }

    public function scriptImportaClientesTiny($filename = null)
    {

        if (is_null($filename)) return false;

        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 999999);
        set_time_limit(0);

        $destino = APP . 'tmp' . DS . 'importacao_tiny' . DS;
        if (!is_dir($destino))
            mkdir($destino);

        $nome_arquivo = $destino . md5(date('Y-m-d h:i:s')) . '.csv';

        move_uploaded_file($filename['tmp_name'], $nome_arquivo);

        if (!file_exists($nome_arquivo))
            exit("ARQUIVO NAO LOCALIZADO");

        $this->Endereco = ClassRegistry::init('Endereco');
        $this->ClienteContato = ClassRegistry::init('ClienteContato');

        if ($handle = fopen($nome_arquivo, "r")) {

            // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component', array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
            // }
            // else if(Ambiente::TIPO_MAPA == 2) {
            //     App::import('Component',array('ApiGeoPortal'));
            //     $this->ApiMaps = new ApiGeoPortalComponent();
            // }

            $conteudo = fread($handle, filesize($nome_arquivo));
            $erros = array();
            $c = 0;

            foreach (explode("\n", $conteudo) as $key => $linha) {
                if ($key > 0) {
                    $temp = explode(";", $linha);

                    // ajuste de quebra de linha	
                    if (!empty($temp[35])) $temp[35] = trim($temp[35]);

                    // se nao existir endereço, preencher com o da buonny	
                    if (empty($temp[8]) && !empty($temp[0])) {
                        $temp[8] = '04053-040';
                        $temp[5] = '102';
                        $temp[6] = '';
                        $temp[4] = 'Alameda dos Guatás';
                    }

                    if (empty($temp[2]) && empty($temp[3])) {
                        $erros[$c] = $temp;
                        $erros[$c][] = 'Sem nome definido';
                        $c++;
                        continue;
                    }
                    if (empty($temp[2])) $temp[2] = $temp[3];
                    if (empty($temp[3])) $temp[3] = $temp[2];

                    if (empty($temp[18])) {
                        $erros[$c] = $temp;
                        $erros[$c][] = 'Sem numero de documento definido';
                        $c++;
                        continue;
                    }

                    // cria os dados a serem salvos (utiliza o metodo de salvamento do proprio form de incluisao de fornecedor)
                    $data['Cliente']['razao_social'] = $temp[2];
                    $data['Cliente']['nome_fantasia'] = $temp[3];
                    $data['Cliente']['tipo_unidade'] = 'F';
                    $data['Cliente']['codigo_documento'] = str_replace('-', '', str_replace('.', '', $temp[18]));
                    $data['Cliente']['codigo_fornecedor_fiscal'] = '';
                    $data['Cliente']['inscricao_estadual'] = 'ISENTO';
                    $data['Cliente']['ccm'] = 'ISENTO';
                    $data['Cliente']['codigo_regime_tributario'] = 1;
                    $data['Cliente']['tipo_unidade'] = 'F';
                    $data['Cliente']['cnae'] = NULL;
                    $data['Cliente']['cnaeDescricao'] = NULL;
                    $data['Cliente']['codigo_externo'] = $temp[1];
                    $data['Cliente']['regiao_tipo_faturamento'] = 1;
                    $data['Cliente']['iss'] = '0,00';
                    $data['Fornecedor']['codigo_empresa'] = 2; //campo chumbado por garantia
                    $data['Fornecedor']['responsavel_administrativo'] = empty($temp[3]) ? $temp[2] : $temp[3];
                    $data['Fornecedor']['acesso_portal'] = 0;
                    $data['Fornecedor']['interno'] = 0;

                    // busca o endereco pelo cep
                    $endereco = $this->Endereco->buscarEnderecoParaImportacao(trim(str_replace('.', '', str_replace('-', '', $temp[8]))), $temp[4]);

                    // busca a latitude e a longtude
                    $lat_lng = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($endereco['EnderecoTipo']['descricao'] . " " . $endereco['Endereco']['descricao'] . " - " . $temp[5] . " - " . $endereco['EnderecoCidade']['descricao'] . "  " . $endereco['EnderecoEstado']['descricao']);

                    if (empty($lat_lng)) {
                        $lat_lng[0] = 0;
                        $lat_lng[1] = 0;
                    }

                    $data['ClienteEndereco']['codigo_endereco'] = $endereco['Endereco']['codigo'];
                    $data['ClienteEndereco']['numero'] = $temp[5];
                    $data['ClienteEndereco']['complemento'] = $temp[6];
                    $data['ClienteEndereco']['latitude'] = $lat_lng[0];
                    $data['ClienteEndereco']['longitude'] = $lat_lng[1];
                    $data['ClienteEndereco']['raio'] = '150';
                    $data['ClienteEndereco']['poligono'] = '';
                    $data['ClienteEndereco']['cep'] = trim(str_replace('.', '', str_replace('-', '', $temp[8])));

                    if ($this->incluir($data)) {

                        $data_contato['ClienteContato']['codigo_cliente'] = $this->id;
                        $data_contato['ClienteContato']['codigo_tipo_contato'] = 2;
                        $data_contato['ClienteContato']['nome'] = $temp[3];
                        $data_contato['ClienteContato']['codigo_empresa'] = 2; //campo chumbado por garantia

                        if (!empty($temp[12])) {
                            $data_contato['ClienteContato']['codigo_tipo_retorno'] = 1;
                            $data_contato['ClienteContato']['descricao'] = $temp[12];
                            $this->ClienteContato->incluir($data_contato);
                        }

                        if (!empty($temp[13])) {
                            $data_contato['ClienteContato']['codigo_tipo_retorno'] = 3;
                            $data_contato['ClienteContato']['descricao'] = $temp[13];
                            $this->ClienteContato->incluir($data_contato);
                        }

                        if (!empty($temp[14])) {
                            $data_contato['ClienteContato']['codigo_tipo_retorno'] = 7;
                            $data_contato['ClienteContato']['descricao'] = $temp[14];
                            $this->ClienteContato->incluir($data_contato);
                        }

                        if (!empty($temp[15])) {
                            $data_contato['ClienteContato']['codigo_tipo_retorno'] = 2;
                            $data_contato['ClienteContato']['descricao'] = $temp[15];
                            $this->ClienteContato->incluir($data_contato);
                        }
                    } else {
                        $erro = '';
                        foreach ($this->validationErrors as $key => $value) {
                            $erro .= $value . " | ";
                        }
                        $erros[$c] = $temp;
                        $erros[$c][] = $erro;
                        $c++;
                        continue;
                    }
                } else {
                    // cria o topo da planilha com os erros
                    $temp = explode(";", $linha);
                    foreach ($temp as $key => $value) {
                        $temp[$key] = trim($value);
                    }
                    $temp[] = 'Erros' . "\r\n";
                    $topo = implode(';', $temp);
                }
            }
        }

        if (!empty($erros)) {
            $this->devolveErroImportacaoTiny($erros, $topo);
        }
        return true;
    }

    public function devolveErroImportacaoTiny($erros = array(), $topo)
    {
        $str = $topo;
        foreach ($erros as $data) {
            $str  .= implode(';', $data) . ';' . "\r\n";
        }
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=dados.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $str;
        exit;
    }


    /**
     * Retorna lista de clientes informando um codigo de 
     * cliente qua pode ser um inteiro ou array
     * 
     * ex retorno
     *  	Array
     *			(
     *				[Cliente] => Array
     *				(
     *                  [codigo] => 71758					
     *                  [codigo_corporacao] =>
     *					[codigo_endereco_regiao] =>
     *					[codigo_cliente_sub_tipo] =>
     * 					[codigo_corretora] =>
     *	 				[temperatura_de] =>
     *					[temperatura_ate] =>
     *					[codigo_regime_tributario] => 3
     *					[tempo_minimo_mopp] =>
     *					
     *
     * 
     * @param [type] $codigo_cliente
     * @return void
     */
    public function obterClientesPorCodigo($codigo_cliente = null)
    {
        $clientes = $this->find('all', array('conditions' => array('codigo' => $codigo_cliente)));

        return $clientes;
    }


    public function obterCodigoClientePorCodigoFuncionario($codigo_funcionario)
    {
        $codigo_cliente = null;

        $model    = &ClassRegistry::init('ClienteFuncionario');

        $fields = array('ClienteFuncionario.codigo_cliente');
        $conditions = array('ClienteFuncionario.codigo_funcionario' => $codigo_funcionario);
        $dados = $model->find('first', compact('conditions', 'fields'));

        if (isset($dados['ClienteFuncionario']) && isset($dados['ClienteFuncionario']['codigo_cliente'])) {
            $codigo_cliente = $dados['ClienteFuncionario']['codigo_cliente'];
        }

        return $codigo_cliente;
    }

    public function obterCodigoClientePorCodigoClienteFuncionario($codigo_cliente_funcionario)
    {
        $codigo_cliente = null;

        $model    = &ClassRegistry::init('ClienteFuncionario');

        $fields = array('ClienteFuncionario.codigo_cliente');
        $conditions = array('ClienteFuncionario.codigo' => $codigo_cliente_funcionario);

        $dados = $model->find('first', compact('conditions', 'fields'));

        if (isset($dados['ClienteFuncionario']) && isset($dados['ClienteFuncionario']['codigo_cliente'])) {
            $codigo_cliente = $dados['ClienteFuncionario']['codigo_cliente'];
        }

        return $codigo_cliente;
    }

    public function obterCodigoClientePorCodigoPedidoExame($codigo_pedido_exame)
    {
        $codigo_cliente = null;

        $model    = &ClassRegistry::init('PedidoExame');

        $fields = array('PedidoExame.codigo_cliente_funcionario');
        $conditions = array('PedidoExame.codigo' => $codigo_pedido_exame);

        $dados = $model->find('first', compact('conditions', 'fields'));

        if (isset($dados['PedidoExame']) && isset($dados['PedidoExame']['codigo_cliente_funcionario'])) {

            $codigo_cliente_funcionario = $dados['PedidoExame']['codigo_cliente_funcionario'];
            $codigo_cliente = $this->obterCodigoClientePorCodigoClienteFuncionario($codigo_cliente_funcionario);
        }

        return $codigo_cliente;
    }

    public function obterCodigoClientePorCodigoFichaPsicossocial($codigo_ficha_psicossocial)
    {
        $codigo_cliente = null;

        $model    = &ClassRegistry::init('FichaPsicossocial');

        $fields = array('FichaPsicossocial.codigo_pedido_exame');
        $conditions = array('FichaPsicossocial.codigo' => $codigo_ficha_psicossocial);

        $dados = $model->find('first', compact('conditions', 'fields'));

        if (isset($dados['FichaPsicossocial']) && isset($dados['FichaPsicossocial']['codigo_pedido_exame'])) {

            $codigo_pedido_exame = $dados['FichaPsicossocial']['codigo_pedido_exame'];
            $codigo_cliente = $this->obterCodigoClientePorCodigoPedidoExame($codigo_pedido_exame);
        }

        return $codigo_cliente;
    }


    public function obterCodigoClientePorCodigoFichaClinica($codigo_ficha_clinica)
    {
        $codigo_cliente = null;

        $model    = &ClassRegistry::init('FichaClinica');

        $fields = array('FichaClinica.codigo_pedido_exame');
        $conditions = array('FichaClinica.codigo' => $codigo_ficha_clinica);

        $dados = $model->find('first', compact('conditions', 'fields'));

        if (isset($dados['FichaClinica']) && isset($dados['FichaClinica']['codigo_pedido_exame'])) {

            $codigo_pedido_exame = $dados['FichaClinica']['codigo_pedido_exame'];
            $codigo_cliente = $this->obterCodigoClientePorCodigoPedidoExame($codigo_pedido_exame);
        }

        return $codigo_cliente;
    }

    /**
     * Obter Caminho da imagem do logotipo 
     *
     * @param array $opcoes
     * @return string|null
     */
    public function obterURLMatrizLogotipo($opcoes = array())
    {

        $url = null;
        $codigo_cliente = null;

        // avaliar opcoes para buscar codigo_cliente usando das opcoes recebidas
        if (isset($opcoes['CODIGO_CLIENTE']) && !empty($opcoes['CODIGO_CLIENTE'])) {
            $codigo_cliente = $opcoes['CODIGO_CLIENTE'];
        }

        if (empty($codigo_cliente) && isset($opcoes['CODIGO_CLIENTE_FUNCIONARIO']) && !empty($opcoes['CODIGO_CLIENTE_FUNCIONARIO'])) {

            $codigo_cliente_funcionario = $opcoes['CODIGO_CLIENTE_FUNCIONARIO'];
            $codigo_cliente = $this->obterCodigoClientePorCodigoClienteFuncionario($codigo_cliente_funcionario);
        }

        if (empty($codigo_cliente) && isset($opcoes['CODIGO_PEDIDO_EXAME']) && !empty($opcoes['CODIGO_PEDIDO_EXAME'])) {

            $codigo_pedido_exame = $opcoes['CODIGO_PEDIDO_EXAME'];
            $codigo_cliente = $this->obterCodigoClientePorCodigoPedidoExame($codigo_pedido_exame);
        }

        if (empty($codigo_cliente) && isset($opcoes['CODIGO_FICHA_PSICOSSOCIAL']) && !empty($opcoes['CODIGO_FICHA_PSICOSSOCIAL'])) {

            $codigo_ficha_psicossocial = $opcoes['CODIGO_FICHA_PSICOSSOCIAL'];
            $codigo_cliente = $this->obterCodigoClientePorCodigoFichaPsicossocial($codigo_ficha_psicossocial);
        }

        if (empty($codigo_cliente) && isset($opcoes['CODIGO_FICHA_CLINICA']) && !empty($opcoes['CODIGO_FICHA_CLINICA'])) {
            $codigo_ficha_clinica = $opcoes['CODIGO_FICHA_CLINICA'];
            $codigo_cliente = $this->obterCodigoClientePorCodigoFichaClinica($codigo_ficha_clinica);
        }

        // Se não encontrou então buscar do usuario logado
        if (empty($codigo_cliente)) {

            //$codigo_cliente = // buscar baseado no usuario
        }


        // se existir um codigo_cliente buscar a matriz e a url da imagem
        if (!empty($codigo_cliente)) {

            // buscar matriz
            $GrupoEconomicoModel = ClassRegistry::init('GrupoEconomico');
            $codigo_matriz = $GrupoEconomicoModel->codigoMatrizPeloCodigoFilial($codigo_cliente);

            App::Import('Component', array('FileServer'));
            $this->FileServer = new FileServerComponent();

            $ClienteModel = ClassRegistry::init('Cliente');
            $caminho_arquivo_logo = $ClienteModel->find('first', array('conditions' => array('Cliente.codigo' => $codigo_matriz)));

            if (
                isset($caminho_arquivo_logo['Cliente'])
                && isset($caminho_arquivo_logo['Cliente']['caminho_arquivo_logo'])
                && trim($caminho_arquivo_logo['Cliente']['caminho_arquivo_logo']) != ''
            ) {

                $url = $this->FileServer->getUrl($caminho_arquivo_logo['Cliente']['caminho_arquivo_logo']);
            }
        }

        return $url;
    }

    public function get_unidades($codigo_cliente)
    {

        $fields = array('Cliente.codigo', 'Cliente.nome_fantasia');
        //conditions
        $conditions = array(
            'GrupoEconomico.codigo_cliente' => $codigo_cliente
        );

        $joins = array(
            array(
                "table" => "cliente",
                "alias" => "Unidade",
                "type" => "INNER",
                "conditions" => array("Unidade.codigo = GrupoEconomicoCliente.codigo_cliente")
                //trecho comentado, o ajuste que pediram é que no momento que flegar a matriz ele possa trazer todas as unidades
                // "conditions" => array("Unidade.codigo = GrupoEconomicoCliente.codigo_cliente AND Unidade.validar_pre_faturamento = 1")
            ),
            array(
                "table" => "grupos_economicos",
                "alias" => "GrupoEconomicos",
                "type" => "LEFT",
                "conditions" => array("GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomicos.codigo")
            ),
            array(
                "table" => "cliente",
                "alias" => "Matriz",
                "type" => "INNER",
                "conditions" => array("Matriz.codigo = GrupoEconomicos.codigo_cliente AND Matriz.validar_pre_faturamento = 1")
            ),
        );
        // debug($conditions);exit;

        $dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields
        );

        // pr( $this->find('sql',$dados) );exit;

        return $dados;
    } //fim query

    public function get_clientes_usuarios($codigo_cliente)
    {

        $conditions = array('GrupoEconomicos.codigo_cliente' => $codigo_cliente);

        $joins = array(
            array(
                "table" => "cliente",
                "alias" => "Unidade",
                "type" => "INNER",
                "conditions" => array("Unidade.codigo = GrupoEconomicoCliente.codigo_cliente")
                // "conditions" => array("Unidade.codigo = GrupoEconomicoCliente.codigo_cliente AND Unidade.validar_pre_faturamento = 1")
            ),
            array(
                "table" => "grupos_economicos",
                "alias" => "GrupoEconomicos",
                "type" => "LEFT",
                "conditions" => array("GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomicos.codigo")
            ),
            array(
                "table" => "cliente",
                "alias" => "Matriz",
                "type" => "INNER",
                "conditions" => array("Matriz.codigo = GrupoEconomicos.codigo_cliente AND Matriz.validar_pre_faturamento = 1")
            ),
        );
        // debug($conditions);exit;

        $dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => array(
                'Unidade.codigo'
            ),
        );

        // pr( $this->find('sql',$dados) );exit;
        return $dados;
    } //fim query

    public function getCliente($codigo_cliente)
    {
        $fields = array(
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'Cliente.razao_social',
            'Cliente.codigo_documento',
        );

        $conditions = array(
            "Cliente.ativo" => 1,
            "Cliente.codigo" => $codigo_cliente
        );

        $dados = $this->find('first', array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50
        ));

        if (!empty($codigo_cliente)) {

            if (is_array($codigo_cliente)) {
                $sql = "select top(1) codigo_grupo_economico from grupos_economicos_clientes where codigo_cliente = {$codigo_cliente[0]}";
            } else {
                $sql = "select top(1) codigo_grupo_economico from grupos_economicos_clientes where codigo_cliente = {$codigo_cliente}";
            }

            $query = $this->query($sql);

            if (!empty($query)) {
                $dados['Cliente']['codigo_grupo_economico'] = $query[0][0]['codigo_grupo_economico'];
            } else {
                $dados = array();
            }
        }

        return $dados;
    }

    public function dadosMeta($codigo_cliente, $codigo_setor, $codigo_cliente_bu = null, $codigo_cliente_opco = null)
    {
        $fields = array(
            'Cliente.codigo',
            'Cliente.razao_social',
            'Cliente.nome_fantasia',
            'ClienteOpco.codigo',
            'ClienteOpco.descricao',
            'ClienteBu.codigo',
            'ClienteBu.descricao',
            'Setores.codigo',
            'Setores.descricao',
            'PosMetas.codigo',
            'PosMetas.valor',
            'PosMetas.dia_follow_up'
        );

        $conditionsJoinPosMetas = array(
            "PosMetas.codigo_cliente = Cliente.codigo",
            "PosMetas.codigo_setor = Setores.codigo"
        );

        $conditionsJoinClienteOpco = array();
        $conditionsJoinClienteBu = array();

        if (!empty($codigo_cliente_bu)) {
            $conditionsJoinPosMetas[] = "PosMetas.codigo_cliente_bu = $codigo_cliente_bu";
            $conditionsJoinClienteBu[] = "ClienteBu.codigo = PosMetas.codigo_cliente_bu OR ClienteBu.codigo = {$codigo_cliente_bu}";
        } else {
            $conditionsJoinClienteBu[] = "ClienteBu.codigo = PosMetas.codigo_cliente_bu";
        }

        if (!empty($codigo_cliente_opco)) {
            $conditionsJoinPosMetas[] = "PosMetas.codigo_cliente_opco = $codigo_cliente_opco";
            $conditionsJoinClienteOpco[] = "ClienteOpco.codigo = PosMetas.codigo_cliente_opco OR ClienteOpco.codigo = {$codigo_cliente_opco}";
        } else {
            $conditionsJoinClienteOpco[] = "ClienteOpco.codigo = PosMetas.codigo_cliente_opco";
        }

        $joins = array(
            array(
                'table' => 'setores',
                'alias' => 'Setores',
                'type' => 'LEFT',
                'conditions' => array(
                    "Setores.codigo = {$codigo_setor}"
                )
            ),
            array(
                'table' => 'pos_metas',
                'alias' => 'PosMetas',
                'type' => 'LEFT',
                'conditions' => $conditionsJoinPosMetas
            ),
            array(
                'table' => 'cliente_opco',
                'alias' => 'ClienteOpco',
                'type' => 'LEFT',
                'conditions' => $conditionsJoinClienteOpco
            ),
            array(
                'table' => 'cliente_bu',
                'alias' => 'ClienteBu',
                'type' => 'LEFT',
                'conditions' => $conditionsJoinClienteBu
            )
        );

        $conditions = array(
            "Cliente.codigo_empresa = 1 and Cliente.codigo = $codigo_cliente and Setores.codigo = $codigo_setor"
        );

        $pos_metas = $this->find("first", array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions
        ));

        return $pos_metas;
    }

    /**
     * metodo com a base para pegar as assinaturas
     */
    public function assinaturaPDASWTPOBS($codigo_cliente, $chave = null)
    {

        $authUsuario = $_SESSION['Auth'];

        $GrupoEconomico = ClassRegistry::init('GrupoEconomico');
        $Configuracao = ClassRegistry::init('Configuracao');
        $Cliente = ClassRegistry::init('Cliente');

        $codigo_empresa = $authUsuario['Usuario']['codigo_empresa'];

        // $codigo_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

        $chaves = "'PLANO_DE_ACAO', 'OBSERVADOR_EHS', 'SAFETY_WALK_TALK'";
        $whereChave = '';
        if (!is_null($chave)) {
            $chaves = "'{$chave}'";
            $whereChave = " AND chave = '{$chave}'";
        }

        $configuracao = $Configuracao->find("all", array(
            "fields" => array(
                "valor"
            ),
            "conditions" => array(
                "chave IN ({$chaves})",
                "codigo_empresa" => $codigo_empresa
            )
        ));

        $codigo_produtos = '';
        if (!empty($configuracao)) {
            foreach ($configuracao as $config) {
                $codigo_produtos .= "," . $config['Configuracao']['valor'];
            }

            $codigo_produtos = substr($codigo_produtos, 1);
        }

        if (is_array($codigo_cliente)) {
            $codigo_cliente = implode(",", $codigo_cliente);
        }

        $sql = "SELECT cM.codigo
					    ,ISNULL(cpAlo.codigo_produto,cpM.codigo_produto) AS codigo_produto
					    ,conf.chave
				FROM grupos_economicos ge 
					INNER JOIN cliente cM on ge.codigo_cliente = cM.codigo
					INNER JOIN grupos_economicos_clientes gec on gec.codigo_grupo_economico = ge.codigo
					INNER JOIN cliente cAlo on gec.codigo_cliente = cAlo.codigo
                    
                    LEFT JOIN cliente_produto cpAlo ON cAlo.codigo = cpAlo.codigo_cliente
                        AND cpAlo.codigo_produto IN ({$codigo_produtos})
                    LEFT JOIN cliente_produto_servico2 cpsAlo ON cpAlo.codigo = cpsAlo.codigo_cliente_produto
                    
                    LEFT JOIN cliente_produto cpM ON cpM.codigo_cliente = cM.codigo
                        AND cpM.codigo_produto IN ({$codigo_produtos})
                    LEFT JOIN cliente_produto_servico2 cpsM ON cpM.codigo = cpsM.codigo_cliente_produto
                    LEFT JOIN configuracao conf ON ISNULL(cast(cpAlo.codigo_produto AS varchar), cast(cpM.codigo_produto AS varchar)) = conf.valor 
                        AND conf.codigo in (SELECT codigo FROM configuracao WHERE chave in ('PLANO_DE_ACAO','OBSERVADOR_EHS','SAFETY_WALK_TALK') AND codigo_empresa = 1)

                WHERE gec.codigo_cliente IN (
						select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico IN (
							select codigo from grupos_economicos where codigo_cliente IN ({$codigo_cliente})
						))
                    {$whereChave}
                GROUP BY cM.codigo
                        ,ISNULL(cpAlo.codigo_produto,cpM.codigo_produto)
                        ,conf.chave";
        // debug($sql);exit;
        $assinaturas = array();
        $assinaturas_produto = array();
        if (!empty($codigo_cliente)) {
            $configuracoes = $Cliente->query($sql);

            foreach ($configuracoes as $c) {
                if (!empty($c[0]['codigo_produto'])) {
                    if (!in_array($c[0]['codigo'], $assinaturas)) {
                        $assinaturas[] = $c[0]['codigo']; //pega os codigos de cliente que tem a assinatura do pda/swt/obs
                    }
                    $assinaturas_produto[$c[0]['codigo']][$c[0]['codigo_produto']] = "'" . $c[0]['chave'] . "'";
                }
            }
        } //fim assinaturas

        return array('clientes' => $assinaturas, 'produtos_clientes' => $assinaturas_produto);
    }

    /**
     * Metodo para pegar se os clientes passados tem assinatura PDA SWT OBS informando no retorno se 
     *     codigo_cliente => tem assinatura
     * caso não tenha o codigo do cliente não tem assinatura
     * Exemplo de retorno:
     *     return = array(
     *         10011
     *     );
     * 
     */
    public function getAssinaturaPDASWTOBS($codigo_cliente, $chave = null)
    {
        $dados = $this->assinaturaPDASWTPOBS($codigo_cliente, $chave);
        return $dados['clientes'];
    } //fim getAssinaturaCodigoCliente($codigo_cliente, $chave = null)

    /**
     * metodo para retornar o array com o codigo do cliente e os produtos habilitados PDA/SWT/OBS
     * @param type $codigo_cliente 
     * @param type|null $chave 
     * @return type
     */
    public function getAssinaturaProdutoPDASWTOBS($codigo_cliente, $chave = null)
    {
        $dados = $this->assinaturaPDASWTPOBS($codigo_cliente, $chave);
        return $dados['produtos_clientes'];
    }
}
