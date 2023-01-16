<?php
class Integfat extends AppModel {
    var $name = 'Integfat';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'integfat';
//    var $primaryKey = 'numero';
    var $actsAs = array('Secure');

    function verificaSepossuiItemIntegradoNesteMes($mes,$ano){
        $data = $ano.$mes.'01 00:00:00';

        $possui = $this->find('count',
                              array(
                                    'conditions' => array('dtlan >' => $data)
                                    )
                             );

        return $possui > 0;
    }

    /**
     * Metodo para importar os pedidos para o sistema Financeiro utilziado pela BUONNY NAVEG
     */ 
    public function importar($options, $in_another_transaction = false) 
    {
    	
        $codigo_servico = isset($options['codigo_servico']) ? $options['codigo_servico'] : null;
        $manual = isset($options['manual']) ? $options['manual'] : false;
        $mes_referencia_manual = isset($options['mes_referencia_manual']) ? $options['mes_referencia_manual'] : null;
        $ano_referencia_manual = isset($options['ano_referencia_manual']) ? $options['ano_referencia_manual'] : null;
        $codigos_pedidos = isset($options['codigo_pedido']) ? $options['codigo_pedido'] : null;
        $codigo_empresa = isset($options['codigo_empresa']) ? $options['codigo_empresa'] : null;

        try {
            if (!$in_another_transaction) $this->query('begin transaction');
            
            $Pedido = ClassRegistry::init('Pedido');
            $ItemPedido = ClassRegistry::init('ItemPedido');
            $Cliente = ClassRegistry::init('Cliente');
            $ClienteEndereco = ClassRegistry::init('ClienteEndereco');
            $Produto = ClassRegistry::init('Produto');
            $ExcecaoFormula = ClassRegistry::init('ExcecaoFormula');
            // $CancelamentoClienteVeiculo = ClassRegistry::init('CancelamentoClienteVeiculo');
            $RemessaBancaria = ClassRegistry::init('RemessaBancaria');

            if($codigos_pedidos) {

                //varre os pedidos para saber se tem banco vinculado quando remessa bancaria
                $erro_banco_naveg = array();
                foreach($codigos_pedidos as $pedido) {
                    //busca o pedido na remessa bancaria
                    $dados = $RemessaBancaria->find('first', array('conditions' => array('codigo_pedido' => $pedido)));
                    if(!empty($dados)) {
                        //verifica se tem o codigo do banco do naveg
                        if($dados['RemessaBancaria']['codigo_banco_naveg'] == "" && empty($dados['RemessaBancaria']['codigo_banco_naveg'])) {
                            $this->log("ERRO NA INTEGRACAO INTEGFAT NAVEG REMESSA SEM CODIGO BANCO NAVEG:" . $dados['RemessaBancaria']['codigo'], 'debug');
                            $erro_banco_naveg[$pedido] = $dados['RemessaBancaria']['nome_pagador'];

                        }
                    }//fim verifica os dados

                }//fim foreach

                //verfica se existe banco na remessa sem codigo do banco do naveg
                if(!empty($erro_banco_naveg)) {
                    //junta os codigos
                    $codigos_erros = implode(',',$erro_banco_naveg);
                    $msgintegracao = "Pedidos gerados na remessa bancaria faltando o codigo do banco: " . $codigos_erros;//seta a mensagem                    
                    
                    throw new Exception('Erro ao integrar pedido.'.$msgintegracao);

                }//fim erro_banco_naveg


                $condpag = "Pedido.codigo_condicao_pagamento AS condpag";
                $conditions =  array(
                    'ItemPedido.codigo_pedido' => $codigos_pedidos
                );
            }else {
                
                if(!is_null($manual) && $manual != "") {
                	$condpag = "Pedido.codigo_condicao_pagamento AS condpag";
                	$conditions = array(
                        'data_integracao IS NULL',
                        'Pedido.manual' => $options['manual'],
                        'mes_referencia' => (isset($mes_referencia_manual)?$mes_referencia_manual:date('m')),
                        'ano_referencia' => (isset($ano_referencia_manual)?$ano_referencia_manual:date('Y'))
                    );
                   
                    //Verifica se o parâmetro de codigo_empresa foi enviado
                    if(isset($codigo_empresa) && !empty($codigo_empresa)){
                        $conditions['Pedido.codigo_empresa'] = $codigo_empresa;
                    }

    			} else {
    				$condpag = ("'14' AS condpag");
    				$conditions = array(
                        'data_integracao IS NULL',
                        'Pedido.manual' => 0
                        //'Pedido.codigo_servico' => $codigo_servico
                    );
    			}
            }
            
            //verificacao se existe endereco do cliente para não dar pau na nota fiscal
            //campos
            $fields_cliente = array('Cliente.codigo as cliente_codigo',  
                            'Cliente.razao_social as razao_social', 
                            'ClienteEndereco.codigo as cliente_endereco_codigo',
                            'ClienteEndereco.codigo_endereco', 
                            'ClienteEndereco.numero', 
                            'Pedido.codigo',
                            'ClienteEndereco.logradouro', 
                            'ClienteEndereco.bairro', 
                            'ClienteEndereco.cidade', 
                            'ClienteEndereco.estado_descricao' );

            //conditions cliente endereco
            $joins_cliente = array(
                array(
                    'table' => "{$ItemPedido->databaseTable}.{$ItemPedido->tableSchema}.{$ItemPedido->useTable}",
                    'alias' => "ItemPedido",
                    'type' => 'INNER',
                    'conditions' => array('ItemPedido.codigo_pedido = Pedido.codigo'),
                ),
                array(
                    'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                    'alias' => "Cliente",
                    'type' => 'INNER',
                    'conditions' => array('Cliente.codigo = Pedido.codigo_cliente_pagador'),
                ),
                array(
                    'table' => "{$ClienteEndereco->databaseTable}.{$ClienteEndereco->tableSchema}.{$ClienteEndereco->useTable}",
                    'alias' => "ClienteEndereco",
                    'type' => 'LEFT',
                    'conditions' => array('ClienteEndereco.codigo_cliente = Cliente.codigo', 'ClienteEndereco.codigo_tipo_contato' => 2),
                ),
            );

            //monta a query
            $dbo = $this->getDataSource();
            $query_cliente_endereco = $dbo->buildStatement(
                array(
                    'fields' => $fields_cliente,
                    'table' => "{$Pedido->databaseTable}.{$Pedido->tableSchema}.{$Pedido->useTable}",
                    'alias' => 'Pedido',
                    'limit' => null,
                    'offset' => null,
                    'joins' => $joins_cliente,
                    'conditions' => $conditions,
                    'order' => null,
                    'group' => null,
                ), $this
            );
            //executa a query
            $cliente_endereco = $this->query($query_cliente_endereco) ;

            //variavel do erro
            $array_error = array();
            //verifica se está vazio
            if(!empty($cliente_endereco)) {
                //varre os enderecos
                foreach($cliente_endereco as $end) {
                    //verifica se esta vazio o numero do endereco
                    if(empty($end[0]['logradouro'])) {
                        $array_error[] = $end[0]['cliente_codigo']; //pega o codigo e numero do cliente
                    }//fim verificacao do numero do endereco em branco
                } //fim foreach
            } //fim if empty

            //retorna para a tela de pedidos integração o erro
            if(!empty($array_error)) {
                $codigos_clientes = implode(",", $array_error);
                throw new Exception('Os seguintes clientes: ('.$codigos_clientes.') com o endereço incompleto!');
            }
            
            /************************************************************
             * Verifica codigo de empresa para integração no naveg:
             * 
             * 18 - RHhrealth
             * 22 - Todos Bem
             ************************************************************/
            // $codigo_empresa = (isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa'] == 2) ? 22 : 18;
            /************************************************************/            
            
			$fields = 
				array(
                    "CASE 
                        WHEN Pedido.codigo_empresa IN (1) THEN 18 
                        WHEN Pedido.codigo_empresa IN (3) THEN 19 
                        WHEN Pedido.codigo_empresa IN (5) THEN 21 
                        ELSE (CASE WHEN EXISTS (SELECT codigo_produto FROM {$ItemPedido->databaseTable}.{$ItemPedido->tableSchema}.{$ItemPedido->useTable} WHERE codigo_pedido = [Pedido].codigo AND codigo_produto = 109) THEN 18 ELSE 22 END) 
                    END AS empresa",
                    "CONVERT(DATETIME, CONVERT(VARCHAR, [ano_referencia]) + '-' + CONVERT(VARCHAR, [mes_referencia]) + '-01') AS dtbasei",
                    "DATEADD(DAY, -(DAY(DATEADD(MONTH, 1, CONVERT(DATETIME, CONVERT(VARCHAR, [ano_referencia]) + '-' + CONVERT(VARCHAR, [mes_referencia]) + '-01') ))), DATEADD(MONTH, 1, CONVERT(datetime, CONVERT(VARCHAR, [ano_referencia]) + '-' + CONVERT(VARCHAR, [mes_referencia]) + '-01') )) AS dtbasef",
                    "REPLICATE('0',(10-LEN(CONVERT(CHAR(10),Cliente.codigo_naveg)))) + CONVERT(CHAR(10),Cliente.codigo_naveg) AS cliente",
                    "Cliente.codigo_documento AS cgc",
                    "Produto.codigo_naveg AS produto",
                    "ItemPedido.valor_total AS preco",
                    "1 AS qtde",
                    "'N' AS statimport",
                    $condpag,
                    "CASE WHEN ExcecaoFormula.valor_acima_formula IS NOT NULL AND ItemPedido.valor_total >= ExcecaoFormula.valor_acima_formula THEN ExcecaoFormula.codigo_formula_naveg ELSE (CASE WHEN Produto.formula_valor_acima_de > 0 AND ItemPedido.valor_total > Produto.formula_valor_acima_de THEN Produto.codigo_formula_naveg_acima ELSE Produto.codigo_formula_naveg END) END AS formula",
                    "Produto.codigo_ccusto_naveg AS ccusto",
                    "getDate() AS dtlan",
                    "'portal' AS usuario",
                    "'S' AS automat",
                    "CONVERT(VARCHAR, Pedido.codigo_naveg) AS seq",
                    "CASE WHEN ExcecaoFormula.valor_acima_formula IS NOT NULL AND ItemPedido.valor_total >= ExcecaoFormula.valor_acima_irrf THEN ExcecaoFormula.percentual_irrf ELSE (CASE WHEN Produto.valor_acima_irrf>0 AND ItemPedido.valor_total > Produto.valor_acima_irrf THEN Produto.percentual_irrf_acima ELSE Produto.percentual_irrf END) END AS perir",
                    "CASE WHEN RemessaBancaria.nosso_numero IS NOT NULL THEN RemessaBancaria.nosso_numero ELSE NULL END AS duplic", 
                    "CASE WHEN RemessaBancaria.codigo IS NOT NULL THEN 'S' ELSE 'N' END AS baixauto",
                    "CASE WHEN RemessaBancaria.data_pagamento IS NOT NULL THEN RemessaBancaria.data_pagamento ELSE NULL END AS dtpagto",
                    "CASE WHEN RemessaBancaria.valor_juros IS NOT NULL THEN RemessaBancaria.valor_juros ELSE '0.00' END AS juros",
                    "'0.00' as desconto",
                    "'0.00' as multa",
                    "CASE WHEN RemessaBancaria.codigo_banco_naveg IS NOT NULL THEN RemessaBancaria.codigo_banco_naveg ELSE NULL END AS banco",
			);
				
            $dbo = $this->getDataSource();
            $pedidos = $dbo->buildStatement(
                array(
                    'fields' => $fields,
                    'table' => "{$Pedido->databaseTable}.{$Pedido->tableSchema}.{$Pedido->useTable}",
                    'alias' => 'Pedido',
                    'limit' => null,
                    'offset' => null,
                    'joins' => array(
                        array(
                            'table' => "{$ItemPedido->databaseTable}.{$ItemPedido->tableSchema}.{$ItemPedido->useTable}",
                            'alias' => "ItemPedido",
                            'type' => 'INNER',
                            'conditions' => array('ItemPedido.codigo_pedido = Pedido.codigo'),
                        ),
                        array(
                            'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                            'alias' => "Cliente",
                            'type' => 'INNER',
                            'conditions' => array('Cliente.codigo = Pedido.codigo_cliente_pagador'),
                        ),
                        array(
                            'table' => "{$ClienteEndereco->databaseTable}.{$ClienteEndereco->tableSchema}.{$ClienteEndereco->useTable}",
                            'alias' => "ClienteEndereco",
                            'type' => 'LEFT',
                            'conditions' => array('ClienteEndereco.codigo_cliente = Cliente.codigo', 'ClienteEndereco.codigo_tipo_contato' => 2),
                        ),
                        array(
                            'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
                            'alias' => "Produto",
                            'type' => 'INNER',
                            'conditions' => array('Produto.codigo = ItemPedido.codigo_produto', 'Produto.codigo_naveg IS NOT NULL'),
                        ),
                        array(
                            'table' => "{$ExcecaoFormula->databaseTable}.{$ExcecaoFormula->tableSchema}.{$ExcecaoFormula->useTable}",
                            'alias' => "ExcecaoFormula",
                            'type' => 'LEFT',
                            'conditions' => array('ExcecaoFormula.codigo_cliente_pagador = Pedido.codigo_cliente_pagador', 'ExcecaoFormula.codigo_produto = ItemPedido.codigo_produto'),
                        ),
                        
                        //RELACIONAMENTO PARA INTEGRAR COM O NAVEG
                        array(
                            'table' => "{$RemessaBancaria->databaseTable}.{$RemessaBancaria->tableSchema}.{$RemessaBancaria->useTable}",
                            'alias' => "RemessaBancaria",
                            'type' => 'LEFT',
                            'conditions' => array('RemessaBancaria.codigo_pedido = Pedido.codigo'),
                        ),
                    ),
                    'conditions' => $conditions,
                    'order' => null,
                    'group' => null,
                ), $this
            );
            // debug($pedidos);die;
            // die(debug($pedidos));

            //$query = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (empresa,dtbasei,dtbasef,cliente,cgc,produto,preco,qtde,statimport,condpag,formula,ccusto,dtlan,usuario,automat,seq,perir,numlan) "; AQUI DEVERA MUDAR E INSERRI O CAMPO PARA INSERIR NA INTEGRAÇÃO COM O NAVEG
            

            $query = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (empresa,dtbasei,dtbasef,cliente,cgc,produto,preco,qtde,statimport,condpag,formula,ccusto,dtlan,usuario,automat,seq,perir, duplic, baixauto, dtpagto, juros, desconto, multa, banco) ";

            $query .= $pedidos;

            //die($query);
            if ($this->query($query) === false)  {
                throw new Exception();
            }

            $data_integracao = "'".date('Y-m-d H:i:s')."'";
            $data_integracao2 = date('Y-m-d H:i:s');
            
            $pedidos_update = substr_replace($pedidos, "UPDATE {$Pedido->databaseTable}.{$Pedido->tableSchema}.{$Pedido->useTable} SET data_integracao = {$data_integracao} ",0 , strrpos($pedidos, "FROM"));
            
            if ($this->query($pedidos_update) === false) {
                throw new Exception();
            } 
            else {
                //para atualiza o log
                if(isset($options['codigo_pedido'])) {
                    foreach ($options['codigo_pedido'] AS $codigo_pedido) {
                        //pega o pedido
                        $ped = $Pedido->find('first', array('conditions' => array('codigo' => $codigo_pedido)));                        
                        $ped['Pedido']['data_integracao'] = $data_integracao2;

                        $Pedido->atualizar($ped);

                    }//fiom foreach
                }//fim if
            }//fim else
            
            if (!$in_another_transaction) {
                $this->commit();
            }

            return true;

        } catch (Exception $ex) {

            $_SESSION['integfat_erro'] = $ex->getMessage();

            if (!$in_another_transaction) {
                $this->rollback();
            }

            return false;
        }
    }

}

?>