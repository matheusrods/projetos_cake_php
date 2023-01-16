<?php
/*
 * Model da Manipulação das tabelas de cabeçalho e registros da importação de atestados
 */
class ItemPedidoAlocacao extends AppModel {
    var $name           = 'ItemPedidoAlocacao';
    var $tableSchema    = 'dbo';
    var $databaseTable  = 'RHHealth';
    var $useTable       = 'itens_pedidos_alocacao';
    var $primaryKey     = 'codigo';
    var $actsAs         = array('Secure');

    public function carregarDetalhes($dados){
        
        $this->ItemPedido   = ClassRegistry::init('ItemPedido');
        $this->Produto      = ClassRegistry::init('Produto');
        $this->Cliente      = ClassRegistry::init('Cliente');

        $fields = array(    "COUNT(*) as qtd_vidas",
                            "{$this->Cliente->name}.codigo", 
                            "{$this->Cliente->name}.nome_fantasia",     
                            "valor_assinatura", 
                            "SUM(valor) as valor",
                            "Produto.descricao",
                        );

        $conditions = array("codigo_cliente_pagador = {$dados['Cliente']['codigo_cliente']}", 
                            "valor_pro_rata IS NULL",
                            "mes_referencia = {$dados['Cliente']['mes_referencia']}",
                            "ano_referencia = {$dados['Cliente']['ano_referencia']}",
        );
           

        $joins = array(
                    array(
                        "table" => "{$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}",
                        "alias" => "{$this->ItemPedido->name}",
                        "type" => "INNER",
                        "conditions" => array("{$this->ItemPedido->name}.codigo_pedido = {$this->name}.codigo_pedido")
                    ),
                    array(
                        "table" => "{$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable}",
                        "alias" => "{$this->Produto->name}",
                        "type" => "INNER",
                        "conditions" => array("{$this->Produto->name}.codigo = {$this->ItemPedido->name}.codigo_produto")
                    ),
                    array(
                        "table" => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                        "alias" => "{$this->Cliente->name}",
                        "type" => "INNER",
                        "conditions" => array("{$this->Cliente->name}.codigo = {$this->name}.codigo_cliente_alocacao")
                    ),
        );

        $group = array('valor_assinatura', 'Produto.descricao', "{$this->Cliente->name}.codigo", "{$this->Cliente->name}.nome_fantasia");

        $resultado =  $this->find('all', compact('fields', 'conditions', 'joins', 'group'));

        $retorno = Set::extract($resultado, '{n}.0');

        return $retorno;
    }//FINAL FUNCTION carregarDetalhes

    public function carregarDetalhesProRata($dados){

        $this->Cliente = ClassRegistry::init('Cliente');

        $fields = array("{$this->Cliente->name}.codigo", 
                        "{$this->Cliente->name}.nome_fantasia", 
                        "{$this->name}.dias_cobrado",
                        "{$this->name}.valor_dia_assinatura",
                        "{$this->name}.valor_assinatura",
                        "{$this->name}.valor_pro_rata");

        $joins = array(
                    array(
                        "table" => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                        "alias" => "{$this->Cliente->name}",
                        "type" => "INNER",
                        "conditions" => array("{$this->Cliente->name}.codigo = {$this->name}.codigo_cliente_alocacao")
                    ),
        );

        $order = array("{$this->Cliente->name}.codigo", "{$this->Cliente->name}.nome_fantasia", "{$this->name}.valor_pro_rata");

        $conditions = array("codigo_cliente_pagador = {$dados['Cliente']['codigo_cliente']}", 
                            "valor_pro_rata IS NOT NULL",
                            "mes_referencia = {$dados['Cliente']['mes_referencia']}",
                            "ano_referencia = {$dados['Cliente']['ano_referencia']}",
        );

        return $this->find('all', compact('fields', 'conditions', 'joins', 'order'));
    }//FINAL FUNCTION carregarDetalhesProRata

    public function carregarDetalhesProRataTotal($dados){

    	$fields = array("COUNT(*) as qtd_vidas", 
                        "SUM(valor_pro_rata) as total_pro_rata",
        );

    	$conditions = array("codigo_cliente_pagador = {$dados['Cliente']['codigo_cliente']}", 
                            "valor_pro_rata IS NOT NULL",
                            "mes_referencia = {$dados['Cliente']['mes_referencia']}",
                            "ano_referencia = {$dados['Cliente']['ano_referencia']}",
        );

		return $this->find('first', compact('fields', 'conditions'));
    }//FINAL FUNCTION carregarDetalhesProRataTotal

    public function carregaDetalhesTotal($dados){
        $fields = array('COUNT(*) as qtd_vidas', 
                            'SUM(valor_assinatura) AS valor_assinatura', 
                            'SUM(valor) as valor',
                        );

        $conditions = array("codigo_cliente_pagador = {$dados['Cliente']['codigo_cliente']}",
                            "mes_referencia = {$dados['Cliente']['mes_referencia']}",
                            "ano_referencia = {$dados['Cliente']['ano_referencia']}",

        );
           
        //$group = array('valor_assinatura');

        $retorno =  $this->find('first', compact('fields', 'conditions'));

        //$retorno = Set::extract($resultado, '{n}.0');

        return $retorno[0];
    }//FINAL FUNCTION carregaDetalhesTotal

    public function carregarParcialGroupValorAssinatura($dados){
        

        $fields = array(    "COUNT(*) as qtd_vidas",
                            "valor_assinatura", 
                            "SUM(valor) as valor",
                        );

        $conditions = array("codigo_cliente_pagador = {$dados['codigo_cliente']}", 
                            "valor_pro_rata IS NULL",
                            "mes_referencia = {$dados['mes_referencia']}",
                            "ano_referencia = {$dados['ano_referencia']}",
        );
           
        $this->ItemPedido   = ClassRegistry::init('ItemPedido');
        $this->Produto      = ClassRegistry::init('Produto');
        $this->Cliente      = ClassRegistry::init('Cliente');

        $joins = array(
                    array(
                        "table" => "{$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}",
                        "alias" => "{$this->ItemPedido->name}",
                        "type" => "INNER",
                        "conditions" => array("{$this->ItemPedido->name}.codigo_pedido = {$this->name}.codigo_pedido")
                    ),
                    array(
                        "table" => "{$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable}",
                        "alias" => "{$this->Produto->name}",
                        "type" => "INNER",
                        "conditions" => array("{$this->Produto->name}.codigo = {$this->ItemPedido->name}.codigo_produto")
                    ),
                    array(
                        "table" => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                        "alias" => "{$this->Cliente->name}",
                        "type" => "INNER",
                        "conditions" => array("{$this->Cliente->name}.codigo = {$this->name}.codigo_cliente_alocacao")
                    ),
        );

        $group = array('valor_assinatura');

        $resultado =  $this->find('all', compact('fields', 'conditions', 'joins', 'group'));

        $retorno = Set::extract($resultado, '{n}.0');

        return $retorno;
    }//FINAL FUNCTION carregarParcialGroupValorAssinatura

    public function carregarDetalhesProRataGroupValor($dados){

        $this->Cliente = ClassRegistry::init('Cliente');

        $fields = array("COUNT(*) as qtd_vidas",
                        "{$this->name}.valor_pro_rata"
        );

        $joins = array(
            array(
                "table" => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                "alias" => "{$this->Cliente->name}",
                "type" => "INNER",
                "conditions" => array("{$this->Cliente->name}.codigo = {$this->name}.codigo_cliente_alocacao")
            ),
        );

        $conditions = array("codigo_cliente_pagador = {$dados['codigo_cliente']}", 
                            "valor_pro_rata IS NOT NULL",
                            "mes_referencia = {$dados['mes_referencia']}",
                            "ano_referencia = {$dados['ano_referencia']}",
        );  

        $group = array("{$this->name}.valor_pro_rata");

        $resultado = $this->find('all', compact('fields', 'conditions', 'joins', 'group'));
        
        $retorno = Set::extract($resultado, '{n}.0');

        return $retorno;
    }//FINAL FUNCTION carregarDetalhesProRataGroupValor

}//FINAL CLASS ItemPedidoAlocacao