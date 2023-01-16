<?php

class PServicoProfissionalTipo extends AppModel {

    var $name = 'PServicoProfissionalTipo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'produto_servico_profissional_tipo';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    public function temProfissional($codigo_produto_servico) {
        return $this->find('count', array('conditions' => array('codigo_produto_servico' => $codigo_produto_servico)));
    }

    public function listarPorCodigo($codigo_produto_servico) {
        $results = $this->find('all', array(
            'conditions' => array(
                'codigo_produto_servico' => $codigo_produto_servico
            )
                ));

        return $results;
    }

    /**
     * Inclui todos os profissionais de um ClienteProduto
     * 
     * @param int $codigo_cliente_produto
     * @param int $codigo_cliente
     * @param int $codigo_produto 
     */
    function incluirProfissionaisPorClienteProduto($codigo_cliente, $codigo_produto, $codigo_cliente_produto) {
        $this->ProdutoServico = & ClassRegistry::init('ProdutoServico');
        $this->ClienteProdutoServico = & ClassRegistry::init('ClienteProdutoServico');

        $produto_servico_profissionais_tipo = array();
        
        $ProdutoServicos = $this->ProdutoServico->find('all', array(
            'conditions' => array(
                'codigo_produto' => $codigo_produto
            )
                ));
        
        foreach ($ProdutoServicos as $produto_servico) {
            $codigo_servico = $produto_servico['Servico']['codigo'];
            $codigo_produto_servico = $produto_servico['ProdutoServico']['codigo'];

            $ProdutoServicoProfissionais = $this->listarPorCodigo($codigo_produto_servico);
            
            // Porque alguns serviços não tem profissionais
            // Esses registros precisam ir na tabela com codigo_profissional_tipo = NULL
            $total_cliente_produto_servico_profissionais = count($ProdutoServicoProfissionais);
            if($total_cliente_produto_servico_profissionais == 0) {
                $query_cliente_produto_servico = array(
                    'codigo_cliente_produto' => $codigo_cliente_produto,
                    'codigo_servico' => $codigo_servico,
                    'codigo_profissional_tipo' => null,
                    'valor' => 0,
                    'codigo_cliente_pagador' => $codigo_cliente,
                    'consistencia_motorista' => 0, //???????
                    'consistencia_veiculo' => 0, //??????
                    'consulta_embarcador' => 0, //???????
                    'tempo_pesquisa' => 0, //?????
                    'validade' => 0, //??????

                    'data_inclusao' => '2010-05-26 00:00:00',
                    'codigo_usuario_inclusao' => 1,
                );

                $result = $this->ClienteProdutoServico->incluir(array(
                    'ClienteProdutoServico' => $query_cliente_produto_servico
                ));
            }
            
            foreach ($ProdutoServicoProfissionais as $produto_servico_profissional) {
                $codigo_profissional_tipo = $produto_servico_profissional['PServicoProfissionalTipo']['codigo_profissional_tipo'];
                
                $query_cliente_produto_servico = array(
                    'codigo_cliente_produto' => $codigo_cliente_produto,
                    'codigo_servico' => $codigo_servico,
                    'codigo_profissional_tipo' => $codigo_profissional_tipo,
                    'valor' => 0,
                    'codigo_cliente_pagador' => $codigo_cliente,
                    'consistencia_motorista' => 0, //???????
                    'consistencia_veiculo' => 0, //??????
                    'consulta_embarcador' => 0, //???????
                    'tempo_pesquisa' => 0, //?????
                    'validade' => 0, //??????

                    'data_inclusao' => '2010-05-26 00:00:00',
                    'codigo_usuario_inclusao' => 1,
                );

                $result = $this->ClienteProdutoServico->incluir(array(
                    'ClienteProdutoServico' => $query_cliente_produto_servico
                ));
            }
        }
    }

}

?>