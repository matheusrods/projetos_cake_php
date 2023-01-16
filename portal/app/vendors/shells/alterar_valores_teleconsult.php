<?php
class AlterarValoresTeleconsultShell extends Shell {
    
    public function main() {
        if (!$this->im_running('alterar_valores_teleconsult'))
            $this->run();
    }
    
    var $arquivo_cp2;
    var $arquivo_lf;
    
    function carrega_arquivo($tipo_arq, $titulo,$tipo = 'a'){
        /*echo "**********************************************\n";
        echo "$ \n";
        echo "$ ".$titulo."\n";
        echo "$ \n";
        echo "**********************************************\n\n";*/
        if ($tipo_arq=="cp2") {
            $this->arquivo_cp2  = fopen(APP.'tmp'.DS.'logs'.DS.$titulo.'.txt', $tipo);
        } elseif($tipo_arq=="lf") {
            $this->arquivo_lf  = fopen(APP.'tmp'.DS.'logs'.DS.$titulo.'.txt', $tipo);
        }
    }

    function fecha_arquivo($tipo_arq){
        if ($tipo_arq=="cp2") {
            fclose($this->arquivo_cp2);
        } elseif($tipo_arq=="lf") {
            fclose($this->arquivo_lf);
        }
    }

    function escreve_arquivo($tipo_arq, $texto){
        if ($tipo_arq=="cp2") {
            fwrite($this->arquivo_cp2, $texto);
        } elseif($tipo_arq=="lf") {
            fwrite($this->arquivo_lf, $texto);
        }
    }

    public function run($limit = 300) {
        /*
SELECT
    cp.codigo as codigo_cliente_produto,
    cp.codigo_cliente,
    cp.codigo_produto,
    cps2.codigo,
    cps2.codigo_servico,
    cps2.codigo_cliente_pagador,
    cps2.valor,
    cpc.data_vigencia,
    cI.codigo_cliente as cliente_pagador,
    cI.codigo_servico as servico_pagador,
    cI.codigo_produto as produto_pagador,
    cI.valor as valor_pagador
    --(select top 1 cps2I.valor
    --from dbBuonny.vendas.cliente_produto_servico2 cps2I
    --    join dbBuonny.vendas.cliente_produto cpI on cps2I.codigo_cliente_produto = cpI.codigo 
    --where cpI.codigo_cliente = cps2.codigo_cliente_pagador
    --    and cps2I.codigo_servico = cps2.codigo_servico
    --) as valor_pagador
FROM
    dbBuonny.vendas.cliente_produto cp 
        INNER JOIN dbBuonny.vendas.cliente_produto_servico2 cps2 
        ON cps2.codigo_cliente_produto = cp.codigo 
        INNER JOIN dbBuonny.vendas.cliente_produto_contrato cpc 
        ON cpc.codigo_cliente_produto = cp.codigo 
        left join (
            select cpI.codigo_cliente, cps2I.codigo_servico, cpI.codigo_produto, cps2I.valor
            from dbBuonny.vendas.cliente_produto_servico2 cps2I
                join dbBuonny.vendas.cliente_produto cpI on cps2I.codigo_cliente_produto = cpI.codigo 
        ) cI on cI.codigo_servico = cps2.codigo_servico and cI.codigo_cliente = cps2.codigo_cliente_pagador and cI.codigo_produto = cp.codigo_produto
WHERE
    --cps2.codigo_cliente_pagador = 342 AND
    cp.codigo_produto in (1,2) and
    cp.codigo_motivo_bloqueio = 1 and
    cps2.valor <> cI.valor
order by codigo


select * from dbTeleconsult.informacoes.log_faturamento lf
    join dbTeleconsult.informacoes.tipo_operacao tpo on lf.codigo_tipo_operacao = tpo.codigo
where codigo_cliente = 692
    and codigo_produto = 1
    and codigo_servico = 1
    and lf.data_inclusao >= '20150401 00:00:00'
 
        */
        $this->ClienteProduto = ClassRegistry::init('ClienteProduto');
        $this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
        $this->ClienteProdutoContrato = ClassRegistry::init('ClienteProdutoContrato');
        $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
        $this->TipoOperacao = ClassRegistry::init('TipoOperacao');

        $this->carrega_arquivo("cp2","registros_cliente_produto_servico2");
        $this->carrega_arquivo("lf","registros_log_faturamento");
        echo "/***********************************************************/\n";
        echo "/**** INICIANDO ROTINA DE ACERTO DE VALORES TELECONSULT ****/\n";
        echo "/***********************************************************/\n\n";
        $this->escreve_arquivo("cp2","##### INICIO ##### \n");
        $this->escreve_arquivo("lf","##### INICIO ##### \n");
        $joins = array(
            array(
               'table' => "{$this->ClienteProduto->databaseTable}.{$this->ClienteProduto->tableSchema}.{$this->ClienteProduto->useTable}",
                'alias' => 'cpI',
                'conditions' => 'cps2I.codigo_cliente_produto = cpI.codigo',
                'type' => 'INNER',
            ),
        );

        $fields = Array(
            'cpI.codigo_cliente', 
            'cps2I.codigo_servico', 
            'cpI.codigo_produto', 
            'cps2I.valor'
        );

        $dbo = $this->ClienteProdutoServico2->getDatasource();
        $queryIn = $dbo->buildStatement(
            array(
                'fields' => $fields,
                'table' => "{$this->ClienteProdutoServico2->databaseTable}.{$this->ClienteProdutoServico2->tableSchema}.{$this->ClienteProdutoServico2->useTable}",
                'alias' => 'cps2I',
                'limit' => null,
                'offset' => null,
                'joins' => $joins,
                'conditions' => null,
                'order' => null,
                'group' => null,
            ), $this->ClienteProdutoServico2
        );

        $joins = array(
            array(
               'table' => "{$this->ClienteProdutoServico2->databaseTable}.{$this->ClienteProdutoServico2->tableSchema}.{$this->ClienteProdutoServico2->useTable}",
                'alias' => 'ClienteProdutoServico2',
                'conditions' => 'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo',
                'type' => 'INNER',
            ),
            array(
               'table' => "{$this->ClienteProdutoContrato->databaseTable}.{$this->ClienteProdutoContrato->tableSchema}.{$this->ClienteProdutoContrato->useTable}",
                'alias' => 'ClienteProdutoContrato',
                'conditions' => 'ClienteProdutoContrato.codigo_cliente_produto = ClienteProduto.codigo',
                'type' => 'LEFT',
            ),
            array(
               'table' => "({$queryIn})",
                'alias' => 'ClienteProdutoServicoPag',
                'conditions' => Array(
                    'ClienteProdutoServicoPag.codigo_servico = ClienteProdutoServico2.codigo_servico',
                    'ClienteProdutoServicoPag.codigo_cliente = ClienteProdutoServico2.codigo_cliente_pagador',
                    'ClienteProdutoServicoPag.codigo_produto = ClienteProduto.codigo_produto',
                ),
                'type' => 'LEFT',
            ),            
        );

        $fields = Array(
            'ClienteProduto.codigo',
            'ClienteProduto.codigo_cliente',
            'ClienteProduto.codigo_produto',
            'ClienteProdutoServico2.codigo',
            'ClienteProdutoServico2.codigo_servico',
            'ClienteProdutoServico2.codigo_cliente_pagador',
            'ClienteProdutoServico2.valor',
            //'ClienteProdutoContrato.data_vigencia',
            'ClienteProdutoServicoPag.codigo_cliente',
            'ClienteProdutoServicoPag.codigo_servico',
            'ClienteProdutoServicoPag.codigo_produto',
            'ClienteProdutoServicoPag.valor'
        );

        $conditions = Array(
            'ClienteProduto.codigo_produto' => Array(1,2),
            'ClienteProduto.codigo_motivo_bloqueio' => 1,
            'ClienteProdutoServico2.valor < ClienteProdutoServicoPag.valor',
        );
        
        $order = Array(
            Array(
                'ClienteProduto.codigo_cliente',
                'ClienteProduto.codigo_produto'
            )
        );

        echo "-- PESQUISA DE SERVICOS COM VALORES DIFERENTES DO PAGADOR \n";
        $servicos_diferenca = $this->ClienteProduto->find('all',compact('fields','conditions','joins','order'));
        $total = count($servicos_diferenca);
        foreach ($servicos_diferenca as $key => $dados_servico) {
            $perc = round((($key+1)/$total) * 100,2);
            echo "-------------------------------------".($key+1)." / ".$total." (".$perc."%) "."\n";
            echo "Cliente: ".$dados_servico['ClienteProduto']['codigo_cliente']."\n";
            echo "Pagador: ".$dados_servico['ClienteProdutoServico2']['codigo_cliente_pagador']."\n";
            echo "Produto: ".$dados_servico['ClienteProduto']['codigo_produto']."\n";
            echo "Serviço: ".$dados_servico['ClienteProdutoServico2']['codigo_servico']."\n";
            echo "Valor Atual: ".$dados_servico['ClienteProdutoServico2']['valor']."\n";
            echo "Valor Corrigido: ".$dados_servico['ClienteProdutoServicoPag']['valor']."\n\n";
            
            $this->escreve_arquivo("cp2",$dados_servico['ClienteProdutoServico2']['codigo'].";".$dados_servico['ClienteProdutoServico2']['valor']."\n");

            $this->ClienteProdutoServico2->id = $dados_servico['ClienteProdutoServico2']['codigo'];
            if (!$this->ClienteProdutoServico2->saveField('valor',$dados_servico['ClienteProdutoServicoPag']['valor'])) {
                echo "Erro ao atribuir o Valor do Servico\n";
                continue;
            }

            $joins = array(
                array(
                   'table' => "{$this->TipoOperacao->databaseTable}.{$this->TipoOperacao->tableSchema}.{$this->TipoOperacao->useTable}",
                    'alias' => 'TipoOperacao',
                    'conditions' => 'LogFaturamentoTeleconsult.codigo_tipo_operacao = TipoOperacao.codigo',
                    'type' => 'INNER',
                ),
            );

            $conditions = Array(
                'LogFaturamentoTeleconsult.codigo_cliente' => $dados_servico['ClienteProduto']['codigo_cliente'],
                'LogFaturamentoTeleconsult.codigo_produto' => $dados_servico['ClienteProduto']['codigo_produto'],
                'TipoOperacao.codigo_servico' => $dados_servico['ClienteProdutoServico2']['codigo_servico'],
                'LogFaturamentoTeleconsult.data_inclusao >=' => '01/04/2015 00:00:00'
            );
            //debug($this->LogFaturamentoTeleconsult->find('sql',compact('conditions','joins')));
            $logs = $this->LogFaturamentoTeleconsult->find('all',compact('conditions','joins'));
            foreach ($logs as $key_log => $dados_log) {
                echo "Log Faturamento: ".$key_log." \n";
                echo "Codigo: ".$dados_log['LogFaturamentoTeleconsult']['codigo']." \n";
                echo "Valor Atual: ".$dados_log['LogFaturamentoTeleconsult']['valor']." \n";
                echo "Valor Corrigido: ".$dados_servico['ClienteProdutoServicoPag']['valor']." \n\n";
                $this->escreve_arquivo("lf",$dados_log['LogFaturamentoTeleconsult']['codigo'].";".$dados_log['LogFaturamentoTeleconsult']['valor']."\n");

                $this->LogFaturamentoTeleconsult->id = $dados_log['LogFaturamentoTeleconsult']['codigo'];
                if (!$this->LogFaturamentoTeleconsult->saveField('valor',$dados_servico['ClienteProdutoServicoPag']['valor'])) {
                    echo "Erro ao atribuir o Valor no Log do Faturamento\n";
                    continue;
                }

            }

            echo "-------------------------------------------\n\n";
        }
        $this->escreve_arquivo("cp2","##### FIM ##### \n\n");
        $this->escreve_arquivo("lf","##### FIM ##### \n\n");
        
        $this->fecha_arquivo("cp2");
        $this->fecha_arquivo("lf");

    }



    private function im_running($tipo) {
        if (PHP_OS!='WINNT') {
            $cmd = shell_exec("ps aux | grep '{$tipo}'");
            // 1 execução é a execução atual
            return substr_count($cmd, 'cake.php -working') > 1;
        } else {
            $cmd = `tasklist /v | findstr /R /C:"{$tipo}"`;
            $ret = substr_count($cmd, 'cake\console\cake') > 1;         
        }
    }
    
}
?>