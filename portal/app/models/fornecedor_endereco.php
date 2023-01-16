<?php

class FornecedorEndereco extends AppModel {

    var $name = 'FornecedorEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'fornecedores_endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_fornecedor_endereco'));
    var $belongsTo = array(
        'TipoContato' => array(
            'className' => 'TipoContato',
            'foreignKey' => 'codigo_tipo_contato'
        ),
    );
    var $validate = array(
        'codigo_fornecedor' => array(
            'rule' => 'notEmpty',
            'message' => 'Fornecedor não informada',
            'required' => true
        ),
        'numero' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o numero'
        ),
        'codigo_tipo_contato' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o tipo de contato'
            ),
            'tipoContatoEnderecoUnico' => array(
                'rule' => 'tipoContatoEnderecoUnico',
                'message' => 'Tipo já informado'
            )
        ),
        'endereco_cep' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe um CEP'
            ),
        )/*,
        'codigo_endereco' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Selecione o endereço.',
                'required' => true
            ),
        ),*/
    );     


    
    public function bindFornecedor() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Fornecedor' => array(
                    'className'  => 'Fornecedor',
                    'foreignKey' => false,
                    'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo'
                ),
            ),
        ), false);
    }
    
    function tipoContatoEnderecoUnico($field = array()) {
        $edit_mode = isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']);

        if ($edit_mode)
            return true;
        else
            $conditions = array(
                'conditions' => array(
                    'codigo_fornecedor' => $this->data[$this->name]['codigo_fornecedor'],
                    'codigo_tipo_contato' => $this->data[$this->name]['codigo_tipo_contato']
                )
            );

        $tipoContatoExistente = $this->find('count', $conditions);

        if ($tipoContatoExistente > 0) {
            return false;
        }

        return true;
    }
    
    function getByTipoContato($codigo_fornecedor = 0, $codigo_tipo_contato = 0) {
        $fornecedor_endereco = $this->find('first', array('conditions' => array('FornecedorEndereco.codigo_fornecedor' => $codigo_fornecedor, 'FornecedorEndereco.codigo_tipo_contato' => $codigo_tipo_contato)));
        return $fornecedor_endereco;
    }
    
    private function listaEnderecos($conditions) {

        if (empty($conditions) || !isset($conditions) || !is_array($conditions))
            return false;

        $join = array();
        if ($this->useDbConfig == 'test_suite') {
            //$join[0]['table'] = $this->getDataSource()->config['database'].'.dbo.uvw_endereco';
            $join[0]['table'] = 'uvw_endereco';
            $join[0]['tableSchema'] = 'dbo';
            $join[0]['databaseTable'] = $this->getDataSource()->config['database'];
        }
        return $this->find('all', array(
                    'fields' => array(
                        'FornecedorEndereco.*',
                        'TipoContato.descricao',
                        'FornecedorEndereco.logradouro AS endereco_logradouro',
                        'FornecedorEndereco.bairro AS endereco_bairro',
                        'FornecedorEndereco.cidade AS endereco_cidade',
                        'FornecedorEndereco.estado_abreviacao AS endereco_estado_abreviacao',
                        'FornecedorEndereco.estado_descricao AS endereco_estado',
                        'FornecedorEndereco.cep AS endereco_cep'
                    ),
                    'joins' => $join,
                    'conditions' => $conditions));
    }
    
    function listaEnderecosExcetoTipoContato($codigo_fornecedor, $tipo_contato) {
        return $this->listaEnderecos(
                        array(
                            'FornecedorEndereco.codigo_fornecedor' => $codigo_fornecedor,
                            'FornecedorEndereco.codigo_tipo_contato !=' => $tipo_contato
                        )
        );
    }
    
    function enderecoCompleto($codigo) {
        $fornecedor_endereco = $this->carregar($codigo);
        return $fornecedor_endereco;
    }
    
    public function listaFornecedoresProximos($conditions, $codigo_servico) {
    	
    	$options['joins'] = array(
    		array(
    			'table' => 'fornecedores',
    			'alias' => 'Fornecedor',
    			'type' => 'INNER',
    			'conditions' => 'Fornecedor.codigo = FornecedorEndereco.codigo_fornecedor'
    		),
    		array(
    			'table' => 'fornecedores_contato',
    			'alias' => 'FornecedorContato',
    			'type' => 'LEFT',
    			'conditions' => 'FornecedorContato.codigo_fornecedor = Fornecedor.codigo AND FornecedorContato.codigo_tipo_retorno = 1'
    		),
    		array(
    			'table' => 'listas_de_preco',
    			'alias' => 'ListaDePreco',
    			'type' => 'INNER',
    			'conditions' => 'ListaDePreco.codigo_fornecedor = Fornecedor.codigo'
    		),
    		array(
    			'table' => 'listas_de_preco_produto',
    			'alias' => 'ListaDePrecoProduto',
    			'type' => 'LEFT',
    			'conditions' => 'ListaDePrecoProduto.codigo_lista_de_preco = ListaDePreco.codigo'
    		),
    		array(
    			'table' => 'listas_de_preco_produto_servico',
    			'alias' => 'ListaDePrecoProdutoServico',
    			'type' => 'LEFT',
    			'conditions' => 'ListaDePrecoProdutoServico.codigo_lista_de_preco_produto = ListaDePrecoProduto.codigo'
    		)
    	);
    	
    	$options['fields'] = array(
    		'Fornecedor.codigo',
    		'Fornecedor.nome',
    		'FornecedorContato.descricao',
    		'FornecedorEndereco.logradouro',
    		'FornecedorEndereco.numero',
    		'FornecedorEndereco.bairro',
    		'FornecedorEndereco.cidade',
    		'FornecedorEndereco.estado_descricao',
    		'FornecedorEndereco.latitude',
    		'FornecedorEndereco.longitude',
    		'Fornecedor.interno',
    		'ListaDePrecoProdutoServico.codigo_servico'    			
    	);
    	
    	$conditionsOR = array(
    			'Fornecedor.interno' => '1',
    			'ListaDePrecoProdutoServico.codigo_servico' => $codigo_servico
    	);
    	
    	$options['conditions'] = array('OR' => array($conditionsOR, $conditions));
    	
        // debug($this->find('sql', $options));exit;
        $dados = $this->find('all', $options);

        return $dados;
    }

    /**
     * [getCidadeUnidade description]
     * 
     * metodo para pegar todos as cidades da matriz/alocacao
     * 
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function getCidadeFornecedor($codigo_cliente, $uf = null)
    {
        //declara a variavel
        $dados = array();

        //verifica se existe o codigo cliente
        if($codigo_cliente) {

            $where = '';
            //verifica se existe valor na uf 
            if(!is_null($uf)) {
                $where = " AND fe.estado_abreviacao = '".$uf."' ";
            }

            //pega os dados da cliente_endereco, para todas as unidades relacionadas no grupo_economico_cliente.
            $query_sql ="SELECT fe.cidade AS cidade
                        FROM RHHealth.dbo.fornecedores_endereco fe
                        WHERE fe.codigo_fornecedor IN (
                            SELECT ipe.codigo_fornecedor
                            FROM RHHealth.dbo.pedidos_exames pe
                                INNER JOIN RHHealth.dbo.itens_pedidos_exames ipe ON pe.codigo = ipe.codigo_pedidos_exames
                            WHERE pe.codigo_cliente IN (SELECT codigo_cliente 
                                                        FROM RHHealth.dbo.grupos_economicos_clientes 
                                                        WHERE codigo_grupo_economico = (SELECT codigo_grupo_economico 
                                                                                        FROM RHHealth.dbo.grupos_economicos_clientes 
                                                                                        WHERE codigo_cliente = {$codigo_cliente})) 
                            GROUP BY ipe.codigo_fornecedor)
                            AND cidade IS NOT NULL
                            AND estado_abreviacao IS NOT NULL
                            {$where}
                        GROUP BY fe.cidade
                        ORDER BY fe.cidade;";

            //executa a query
            $dados = $this->query($query_sql);

        }//fim if codigo_cliente

        return $dados;

    }//fim getCidadeUnidade

     /**
     * [get_combo_cidade description]
     * 
     * metodo para montar o combo de cidade com o codigo do cliente passado por parametro
     * 
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function get_combo_cidade($codigo_cliente, $uf = null)
    {
        //pega as cidades e estados do codigo unidade pesquisado
        $cid_unidade = $this->getCidadeFornecedor($codigo_cliente,$uf);
        $result = array();
        foreach ($cid_unidade as $value) {
            $result[] = array('codigo' => $value[0]['cidade'], 'descricao' => $value[0]['cidade']);
        }

        return $result;

    }//fim get_combo_cidade

    /**
     * [getEstadosUnidade description]
     * 
     * metodo para pegar todos os estados da matriz/alocacao
     * 
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function getEstadosFornecedor($codigo_cliente)
    {
        //declara a variavel
        $dados = array();

        //verifica se existe o codigo cliente
        if($codigo_cliente) {

            //pega os dados da cliente_endereco, para todas as unidades relacionadas no grupo_economico_cliente.
            $query_sql ="SELECT fe.estado_abreviacao AS estado
                        FROM RHHealth.dbo.fornecedores_endereco fe
                        WHERE fe.codigo_fornecedor IN (
                            SELECT ipe.codigo_fornecedor
                            FROM RHHealth.dbo.pedidos_exames pe
                                INNER JOIN RHHealth.dbo.itens_pedidos_exames ipe ON pe.codigo = ipe.codigo_pedidos_exames
                            WHERE pe.codigo_cliente IN (SELECT codigo_cliente 
                                                        FROM RHHealth.dbo.grupos_economicos_clientes 
                                                        WHERE codigo_grupo_economico = (SELECT codigo_grupo_economico 
                                                                                        FROM RHHealth.dbo.grupos_economicos_clientes 
                                                                                        WHERE codigo_cliente = {$codigo_cliente})) 
                            GROUP BY ipe.codigo_fornecedor)
                            AND cidade IS NOT NULL
                            AND estado_abreviacao IS NOT NULL
                        GROUP BY fe.estado_abreviacao
                        ORDER BY fe.estado_abreviacao;";

            //executa a query
            $dados = $this->query($query_sql);

        }//fim if codigo_cliente

        return $dados;

    }//fim getEstadosUnidade

     /**
     * [get_combo_estado description]
     * 
     * metodo para montar o array do select box, combo na tela
     * 
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function get_combo_estado($codigo_cliente)
    {

        //pega as estados do codigo unidade pesquisado
        $est_unidade = $this->getEstadosFornecedor($codigo_cliente);
        $result = array();
        foreach ($est_unidade as $value) {
            $result[] = array('codigo' => $value[0]['estado'], 'descricao' => $value[0]['estado']);
        }

        return $result;

    }//fim get_combo_estado



}

?>