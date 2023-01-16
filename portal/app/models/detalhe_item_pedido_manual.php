<?php
class DetalheItemPedidoManual extends AppModel {

    public $name = 'DetalheItemPedidoManual';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'detalhes_itens_pedidos_manuais';
    public $primaryKey = 'codigo';
    public $displayField = 'descricao';
    public $actsAs = array('Secure');

    public $belongsTo = array(
        'ItemPedido' => array(
            'className' => 'ItemPedido',
            'foreignKey' => 'codigo_item_pedido',
            )
        );

    public function carregarPedidosAutomaticosTlc($mes_referencia, $ano_referencia) {
    	$this->bindModel(array('belongsTo' => array('ItemPedido' => array('foreignKey' => 'codigo_item_pedido'))));
    	$query = $this->ItemPedido->consolidadoTlcPorServico('sql', $mes_referencia, $ano_referencia);
    	$insert = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_item_pedido, codigo_servico, valor, quantidade, data_inclusao, codigo_usuario_inclusao) {$query}";
    	return ($this->query($insert) !== false);
    }

    public function carregarPedidosAutomaticosBSat($mes_referencia, $ano_referencia, $in_another_transaction = false) {
        $FrotaPedido =& ClassRegistry::init('FrotaPedido');
        $DetalheItemPedido =& ClassRegistry::init('DetalheItemPedido');
        $AvulsoPedido =& ClassRegistry::init('AvulsoPedido');
        try {
            if (!$in_another_transaction) $this->query('BEGIN TRANSACTION');
            $query = $FrotaPedido->consolidadoPorItemPedido('sql', $mes_referencia, $ano_referencia);
            $insert = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_item_pedido, codigo_servico, valor, quantidade, data_inclusao, codigo_usuario_inclusao) {$query}";
            if ($this->query($insert) === false) throw new Exception("Erro ao incluir serviço frotas", 1);
            $query = $AvulsoPedido->consolidadoPorItemPedido('sql', $mes_referencia, $ano_referencia);
            $insert = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_item_pedido, codigo_servico, valor, quantidade, data_inclusao, codigo_usuario_inclusao) {$query}";
            if ($this->query($insert) === false) throw new Exception("Erro ao incluir serviço avulso", 1);
            $query = $DetalheItemPedido->consolidadoPorItemPedido('sql', $mes_referencia, $ano_referencia);
            $insert = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_item_pedido, codigo_servico, valor, quantidade, data_inclusao, codigo_usuario_inclusao) {$query}";
            if ($this->query($insert) === false) throw new Exception("Erro ao incluir serviço avulso", 1);
            if (!$in_another_transaction) $this->commit();
            return true;
        } catch (Exception $ex) {
            if (!$in_another_transaction) $this->rollback();
            return false;
        }
    }

    /**
     * Carregar Pedidos Automaticos da Assinatura
     * @param  [datetime]   $data_inclusao  [data da inclusão no formato Americano (YYYY-MM-DD hh:ii:Ss)]
     * @param  [string]     $mes_referencia [mês de referencia]
     * @param  [string]     $ano_referencia [ano de referencia]
     * @return [boolean]    retorna true ou false prar insert
     */
    public function carregarPedidosAutomaticosAssinaturas($data_inclusao, $mes_referencia, $ano_referencia, $aguardar_liberacao = null, $codigo_cliente=null) {

        $this->ClienteProduto         = ClassRegistry::init('ClienteProduto');        
        $this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
        $this->Produto                = ClassRegistry::init('Produto');
        $this->Pedido                 = ClassRegistry::init('Pedido');        
        $this->ItemPedido             = ClassRegistry::init('ItemPedido');

        $this->ClienteProduto->bindModel(
            array(
                'belongsTo' => 
                array(
                    'Produto' => array(
                        'foreignKey' => false, 
                        'type'       => 'INNER',
                        'conditions' => array(
                            'Produto.codigo = ClienteProduto.codigo_produto',                            
                            'Produto.codigo = \''.Pedido::CODIGO_PRODUTO_PACOTE_MENSAL.'\'',
                            'Produto.codigo_naveg IS NOT NULL',
                            'Produto.codigo_naveg != \'\'',
                            //'Produto.mensalidade = 1' ,
                        )
                    ),
                    'ClienteProdutoServico2' => array(
                        'foreignKey' => false, 
                        'type'       => 'INNER',
                        'conditions' => 'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo',
                    ),
                    'Pedido' => array(
                        'foreignKey' => false,
                        'type'       => 'INNER',
                        //'conditions' => 'Pedido.codigo_cliente_pagador = ClienteProduto.codigo_cliente', 
                        'conditions' => 'Pedido.codigo_cliente_pagador = ClienteProdutoServico2.codigo_cliente_pagador',
                                        "Pedido.data_inclusao = '{$data_inclusao}'",
                                        'Pedido.codigo_servico = \''.Pedido::CODIGO_SERVICO_ASSINATURA.'\'',
                                        'Pedido.manual = 0'
                    ),
                    'ItemPedido' => array(
                        'foreignKey' => false,
                        'type'       => 'INNER',
                        'conditions' => 'ItemPedido.codigo_pedido = Pedido.codigo AND 
                                         ItemPedido.codigo_produto = ClienteProduto.codigo_produto'
                    ),
                    'ProdutoServico' => array(
                        'foreignKey' => false,
                        'type'       => 'INNER',
                        'conditions' => array(
                            'ProdutoServico.codigo_servico = ClienteProdutoServico2.codigo_servico',
                            'ProdutoServico.codigo_produto = Produto.codigo',
                            'ProdutoServico.ativo = 1',
                            //'ProdutoServico.codigo_produto = \''.Pedido::CODIGO_PRODUTO_PACOTE_MENSAL.'\'',

                        )
                    ),
                    'Cliente' => array(
                        'foreignKey' => false, 
                        'type'       => 'INNER',
                        'conditions' => array('ClienteProduto.codigo_cliente = Cliente.codigo')
                    ),
                )
           )
        );

        $Cliente        = ClassRegistry::init('Cliente');
        $unidades_teste = $Cliente->lista_por_cliente(10011);
        $unidades_teste = implode(array_keys($unidades_teste), ', ');

        $conditionsIPEB = array(
            'ClienteProduto.codigo_motivo_bloqueio = 1',
            "Pedido.mes_referencia = {$mes_referencia}",
            "Pedido.ano_referencia = {$ano_referencia}",
            "ClienteProduto.codigo_cliente NOT IN({$unidades_teste})"
        );

        if(!is_null($aguardar_liberacao)) {
            $conditionsIPEB[] = "Cliente.aguardar_liberacao <> 1";
        }

        if(!empty($codigo_cliente)){
             $conditionsIPEB[] = 'Cliente.codigo '.$this->rawsql_codigo_cliente($codigo_cliente);
        }   

        $sql = $this->ClienteProduto->find('sql', 
            array(                
                'fields' => array(  
                    'ItemPedido.codigo as codigo_item_pedido',
                    'ClienteProdutoServico2.valor as valor',
                    'Pedido.codigo_usuario_inclusao as codigo_usuario_inclusao',
                    'Pedido.data_inclusao as data_inclusao',
                    'ClienteProdutoServico2.codigo_servico as codigo_servico',
                    'ClienteProdutoServico2.quantidade as quantidade',
                    "{$_SESSION['Auth']['Usuario']['codigo_empresa']} AS codigo_empresa"
                ),
                'conditions' => $conditionsIPEB,                
                )
        );

        // debug($sql);exit;

        // die(debug("INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} 
        //     (codigo_item_pedido, valor, codigo_usuario_inclusao, data_inclusao, codigo_servico, quantidade, codigo_empresa)  
        //     ({$sql})
        //     "
        // ));
        return $this->query("INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} 
            (codigo_item_pedido, valor, codigo_usuario_inclusao, data_inclusao, codigo_servico, quantidade, codigo_empresa)
            ({$sql})
            "
        );
    }//FINAL FUNCTION carregarPedidosAutomaticosAssinaturas

    public function carregarPedidosAssinaturas($dados) {
        $this->ClienteProduto           = ClassRegistry::init('ClienteProduto');        
        $this->ClienteProdutoServico2   = ClassRegistry::init('ClienteProdutoServico2');
        $this->Produto                  = ClassRegistry::init('Produto');
        $this->Pedido                   = ClassRegistry::init('Pedido');
        $this->Notafis                   = ClassRegistry::init('Notafis');

        $ano_mes = $dados['Cliente']['ano_referencia'].str_pad($dados['Cliente']['mes_referencia'],2,'0', STR_PAD_LEFT);
        $data_inicial = $ano_mes.'01 00:00:00';         
        $data_final   = $ano_mes.date('t', strtotime("{$dados['Cliente']['ano_referencia']}.{$dados['Cliente']['mes_referencia']}-01")).' 23:59:59';

        $pedido = $this->Pedido->find('count', array('conditions' => array(
            'Pedido.mes_referencia' => $dados['Cliente']['mes_referencia'],
            'Pedido.ano_referencia' => $dados['Cliente']['ano_referencia'],
            'Pedido.codigo_cliente_pagador' => $dados['Cliente']['codigo_cliente'],
                //'Pedido.codigo_servico' => Pedido::CODIGO_SERVICO_ASSINATURA
            ))) ;
        $ano_mes_atual = date('Ym');

        if($pedido > 0 ){
            $this->bindModel(
                array(
                    'belongsTo' => 
                    array(
                        'ItemPedido' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => array(
                                'ItemPedido.codigo = DetalheItemPedidoManual.codigo_item_pedido')
                        ),
                        'Pedido' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => array(
                                'Pedido.codigo = ItemPedido.codigo_pedido', 
                                'Pedido.mes_referencia = '.$dados['Cliente']['mes_referencia'],
                                'Pedido.ano_referencia = '.$dados['Cliente']['ano_referencia'],
                                'Pedido.codigo_cliente_pagador = '.$dados['Cliente']['codigo_cliente'],
                                    //'Pedido.codigo_servico = '.Pedido::CODIGO_SERVICO_ASSINATURA
                            )
                        ),
                        'Produto' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => array(
                                'Produto.codigo = ItemPedido.codigo_produto AND Produto.codigo = '.$dados['Cliente']['codigo_produto'])
                        ),
                        'Servico' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => array('Servico.codigo = DetalheItemPedidoManual.codigo_servico')
                        ),
                        'Cliente' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => array('Cliente.codigo = Pedido.codigo_cliente_pagador')
                        ),
                    ),
                )
            );

            $fields = array(
                'Produto.codigo as codigo_produto',
                'Produto.descricao as produto',
                'Servico.descricao as servico',
                'DetalheItemPedidoManual.quantidade as quantidade',
                "DetalheItemPedidoManual.valor AS valor",
                'Cliente.codigo as codigo', 
                'Cliente.nome_fantasia as nome_fantasia', 
                'Pedido.codigo as codigo_pedido',
                );
            $retorno = $this->find('all', compact('fields'));

            // die( debug($this->find('sql', compact('fields','joins')) ));

            if(!empty($retorno)){
                foreach($retorno as $item => $pedido){
                    $nota_cancelada = 0;
                    $nota_cancelada = $this->Notafis->retorna_nota_status_pedido($pedido[0]['codigo_pedido']);
                    $retorno[$item][0]['nota_cancelada'] = $nota_cancelada;
                }
            }               
            //debug($retorno);
            return $retorno;            
        }else if($ano_mes==$ano_mes_atual){
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
                                'Produto.mensalidade = 1' ,
                                'Produto.codigo = '.$dados['Cliente']['codigo_produto'],
                                'Produto.ativo = 1'
                                )
                            ),
                        'ClienteProdutoServico2' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => 'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo',
                            ),
                        'Servico' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => 'ClienteProdutoServico2.codigo_servico = Servico.codigo',
                            ),
                        'ProdutoServico' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => array(
                                'ClienteProdutoServico2.codigo_servico = ProdutoServico.codigo_servico',
                                'ProdutoServico.codigo_produto = Produto.codigo',
                                'ProdutoServico.ativo = 1'
                                )
                            ),
                        )
                    ));
            $retorno = $this->ClienteProduto->find('all', array(                
                'fields' => array(  
                    'Produto.descricao as produto',
                    'Servico.descricao as servico',
                    'ClienteProdutoServico2.quantidade as quantidade',
                    'ClienteProdutoServico2.valor as valor'
                    ),
                'conditions' => array(
                    'ClienteProduto.codigo_motivo_bloqueio = 1',                            
                    'ClienteProdutoServico2.valor > 0',
                    'ClienteProduto.codigo_cliente = '.$dados['Cliente']['codigo_cliente']
                    ),
                ));
            return $retorno;  
        }
        return false;
    }//FINAL FUNCTION carregarPedidosAssinaturas

    public function carregar($codigo) {
        $retorno = $this->find ( 'first', array (
            'conditions' => array (
                $this->name . '.codigo_item_pedido' => $codigo 
                ) 
            ) );
        return $retorno;
    }//FINAL FUNCTION carregar


    public function carregarPedidosAssinaturasEC($dados) {
        $this->ClienteProduto           = ClassRegistry::init('ClienteProduto');        
        $this->ClienteProdutoServico2   = ClassRegistry::init('ClienteProdutoServico2');
        $this->Produto                  = ClassRegistry::init('Produto');
        $this->Pedido                   = ClassRegistry::init('Pedido');
        $this->Notafis                   = ClassRegistry::init('Notafis');

        $ano_mes = $dados['Cliente']['ano_referencia'].str_pad($dados['Cliente']['mes_referencia'],2,'0', STR_PAD_LEFT);
        $data_inicial = $ano_mes.'01 00:00:00';         
        $data_final   = $ano_mes.date('t', strtotime("{$dados['Cliente']['ano_referencia']}.{$dados['Cliente']['mes_referencia']}-01")).' 23:59:59';

        $pedido = $this->Pedido->find('count', array('conditions' => array(
            'Pedido.mes_referencia' => $dados['Cliente']['mes_referencia'],
            'Pedido.ano_referencia' => $dados['Cliente']['ano_referencia'],
            'Pedido.codigo_cliente_pagador' => $dados['Cliente']['codigo_cliente'],
                //'Pedido.codigo_servico' => Pedido::CODIGO_SERVICO_ASSINATURA
            ))) ;
        $ano_mes_atual = date('Ym');

        if($pedido > 0 ){
            $this->unbindModel(array('belongsTo' => array('ItemPedido')));

            //IMPLEMENTAR OS JOINS CORRETAMENTE
            $joins = array(
                array(
                    "table" => "RHHealth.dbo.itens_pedidos",
                    "alias" => "ItemPedido",
                    "type" => "INNER",
                    "conditions" => array("ItemPedido.codigo = DetalheItemPedidoManual.codigo_item_pedido")
                ),
                array(
                    "table" => "RHHealth.dbo.pedidos",
                    "alias" => "Pedido",
                    "type" => "INNER",
                    "conditions" => array(
                                'Pedido.codigo = ItemPedido.codigo_pedido', 
                                'Pedido.mes_referencia = '.$dados['Cliente']['mes_referencia'],
                                'Pedido.ano_referencia = '.$dados['Cliente']['ano_referencia'],
                                'Pedido.codigo_cliente_pagador = '.$dados['Cliente']['codigo_cliente'],
                            )
                ),
                array(
                    "table" => "RHHealth.dbo.produto",
                    "alias" => "Produto",
                    "type" => "INNER",
                    'conditions' => array('Produto.codigo = ItemPedido.codigo_produto AND Produto.codigo = '.$dados['Cliente']['codigo_produto'])
                ),
                array(
                    "table" => "RHHealth.dbo.servico",
                    "alias" => "Servico",
                    "type" => "INNER",
                    'conditions' => array('Servico.codigo = DetalheItemPedidoManual.codigo_servico')
                ),
                array(
                    "table" => "RHHealth.dbo.cliente",
                    "alias" => "Cliente",
                    "type" => "INNER",
                    'conditions' => array('Cliente.codigo = Pedido.codigo_cliente_pagador')
                ),

                array(
                    "table" => "RHHealth.dbo.exames",
                    "alias" => "e",
                    "type" => "INNER",
                    "conditions" => array("e.codigo_servico = DetalheItemPedidoManual.codigo_servico")
                ),
                array(
                    "table" => "RHHealth.dbo.pedidos_exames",
                    "alias" => "pe",
                    "type" => "INNER",
                    "conditions" => array("pe.codigo_cliente = DetalheItemPedidoManual.codigo_cliente_utilizador")
                ),
                array(
                    "table" => "RHHealth.dbo.itens_pedidos_exames",
                    "alias" => "ipe",
                    "type" => "INNER",
                    "conditions" => array("pe.codigo = ipe.codigo_pedidos_exames AND ipe.codigo_exame = e.codigo")
                ),
                array(
                    "table" => "RHHealth.dbo.itens_pedidos_exames_baixa",
                    "alias" => "ipeb",
                    "type" => "INNER",
                    "conditions" => array("ipeb.data_inclusao BETWEEN '".$data_inicial."' AND '".$data_final."'
                                            AND ipeb.fornecedor_particular=0
                                            AND ipeb.pedido_importado <> 1
                                            AND ipeb.codigo_itens_pedidos_exames = ipe.codigo")
                ),
                array(
                    "table" => "RHHealth.dbo.fornecedores",
                    "alias" => "forn",
                    "type" => "INNER",
                    "conditions" => array("forn.codigo = ipe.codigo_fornecedor")
                ),
            );

            $group = array(
               'Produto.codigo',
               'Produto.descricao',
               'Servico.descricao',
               'DetalheItemPedidoManual.quantidade',
               'forn.ambulatorio',
               'forn.prestador_particular',
               'DetalheItemPedidoManual.valor',
               'Cliente.codigo',
               'Cliente.nome_fantasia',
               'Pedido.codigo',
               'DetalheItemPedidoManual.codigo'
            );

            $order = array('Servico.descricao', 'DetalheItemPedidoManual.quantidade','DetalheItemPedidoManual.codigo');

            $fields = array(
                'Produto.codigo as codigo_produto',
                'Produto.descricao as produto',
                'Servico.descricao as servico',
                'DetalheItemPedidoManual.quantidade as quantidade',
                'SUM(CASE
                       WHEN forn.ambulatorio = 1 THEN 1
                       WHEN forn.prestador_particular = 1 THEN 1
                       ELSE 0
                   END) as quantidade_forn_particular',
               'SUM(CASE
                       WHEN forn.ambulatorio = 1 THEN 0
                       WHEN forn.prestador_particular = 1 THEN 0
                       ELSE 1
                   END) as quantidade_pagto',
                "(CASE
                   WHEN forn.ambulatorio = 1 THEN '0.00'
                   WHEN forn.prestador_particular = 1 THEN '0.00'
                   ELSE DetalheItemPedidoManual.valor
                END) AS valor",
                'Cliente.codigo as codigo', 
                'Cliente.nome_fantasia as nome_fantasia', 
                'Pedido.codigo as codigo_pedido',
                'DetalheItemPedidoManual.codigo as codigo_detalhe'
                );
            $retorno = $this->find('all', compact('fields','joins','group','order'));

            // die( debug($this->find('sql', compact('fields','joins')) ));

            if(!empty($retorno)){
                foreach($retorno as $item => $pedido){
                    $nota_cancelada = 0;
                    $nota_cancelada = $this->Notafis->retorna_nota_status_pedido($pedido[0]['codigo_pedido']);
                    $retorno[$item][0]['nota_cancelada'] = $nota_cancelada;
                }
            }               
            //debug($retorno);
            return $retorno;            
        }else if($ano_mes==$ano_mes_atual){
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
                                'Produto.mensalidade = 1' ,
                                'Produto.codigo = '.$dados['Cliente']['codigo_produto'],
                                'Produto.ativo = 1'
                                )
                            ),
                        'ClienteProdutoServico2' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => 'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo',
                            ),
                        'Servico' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => 'ClienteProdutoServico2.codigo_servico = Servico.codigo',
                            ),
                        'ProdutoServico' => array(
                            'foreignKey' => false, 
                            'type'       => 'INNER',
                            'conditions' => array(
                                'ClienteProdutoServico2.codigo_servico = ProdutoServico.codigo_servico',
                                'ProdutoServico.codigo_produto = Produto.codigo',
                                'ProdutoServico.ativo = 1'
                                )
                            ),
                        )
                    ));
            $retorno = $this->ClienteProduto->find('all', array(                
                'fields' => array(  
                    'Produto.descricao as produto',
                    'Servico.descricao as servico',
                    'ClienteProdutoServico2.quantidade as quantidade',
                    'ClienteProdutoServico2.valor as valor'
                    ),
                'conditions' => array(
                    'ClienteProduto.codigo_motivo_bloqueio = 1',                            
                    'ClienteProdutoServico2.valor > 0',
                    'ClienteProduto.codigo_cliente = '.$dados['Cliente']['codigo_cliente']
                    ),
                ));
            return $retorno;  
        }
        return false;
    }//FINAL FUNCTION carregarPedidosAssinaturas

    
    
}//FINAL CLASS DetalheItemPedidoManual