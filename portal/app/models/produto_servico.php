<?php
class ProdutoServico extends AppModel {
    var $name = 'ProdutoServico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'produto_servico';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $belongsTo = array(
        'Produto' => array(
            'className' => 'Produto',
            'foreignKey' => 'codigo_produto'
        ),
        'Servico' => array(
            'className' => 'Servico',
            'foreignKey' => 'codigo_servico'
        )
    );

    function servicosPorProduto($codigo_produto){
        $ids_servicos = $this->find('list', 
            array('fields' => array('codigo_servico'),
             'conditions' => array('codigo_produto' => $codigo_produto, 'ativo' => 1)));
        if (count($ids_servicos) > 0)
            return $this->Servico->find('list', array('conditions' => array('Servico.codigo' => $ids_servicos,'Servico.ativo'=>true)));
        else
            return array();
    }
    
    
    function listarServicosPorProduto($codigo_produto, $apenas_ativos = false) {
        $conditions = array('codigo_produto' => $codigo_produto);
        if ($apenas_ativos) $conditions['Servico.ativo'] = 1;

        $result = $this->find('all', array('conditions' => $conditions));
        return $result;
    }
    
    function possuiTipoProfissional($codigo_produto, $codigo_servico) {
        $result = $this->find('count',
            array(
                'recursive' => 0,
            	'conditions' => array(
                	$this->name . '.codigo_produto' => $codigo_produto,
                	$this->name . '.codigo_servico' => $codigo_servico,
                    'ProdutoServicoProfissionalTipo.codigo >' => 0
                ),
                'joins' => array(
                    array(
                        'alias' => 'ProdutoServicoProfissionalTipo',
                    	'table' => $this->databaseTable . '.' . $this->tableSchema . '.produto_servico_profissional_tipo',
                        'type' => 'INNER',
                        'conditions' => 'ProdutoServico.codigo = ProdutoServicoProfissionalTipo.codigo_produto_servico'
                    )
                )
            )
        );
        return $result > 0;
    }
    
    /**
     * Método que retorna o código de ProdutoServico passando código do Produto e o do Serviço.
     * 
     * @param int $codigo_produto
     * @param int $codigo_servico 
     */
    function getCodigoProdutoServico($codigo_produto, $codigo_servico) {
        $codigo = $this->field('codigo', array(
            'codigo_produto' => $codigo_produto,
            'codigo_servico' => $codigo_servico
        ));
        
        return $codigo;
    }

    function produtosTeleconsult() {
        return array(1, 2);
    }

    function servicosTeleconsult() {
        $result = $this->find('all', array('fields' => 'codigo_servico', 'group' => 'codigo_servico', 'conditions' => array('codigo_produto' => $this->produtosTeleconsult(), 'not' => array('codigo_servico' => array(5, 6, 7)) )));
        return Set::extract('/ProdutoServico/codigo_servico', $result);
    }

}

?>