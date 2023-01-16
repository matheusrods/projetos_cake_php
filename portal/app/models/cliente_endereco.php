<?php

class ClienteEndereco extends AppModel {

    var $name = 'ClienteEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cliente_endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_cliente_endereco'));
    var $belongsTo = array(
        'TipoContato' => array(
            'className' => 'TipoContato',
            'foreignKey' => 'codigo_tipo_contato'
        ),
    );
    var $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Cliente não informado',
            'required' => true
        ),
        'numero' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o numero'
            ),
            'Numeric' => array(
                'rule' => 'numeric',
                'message' => 'Informe Somente Números no campo do Endereço!'
            ) 
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
        ),
        // 'codigo_endereco' => array(
        //     'notEmpty' => array(
        //         'rule' => 'notEmpty',
        //         'message' => 'Selecione o endereço.',
        //         'required' => true
        //     ),
        // ),
    );      

    function bindEndereco(){
        $this->bindModel(array(
            'hasMany' => array(
                'Endereco' => array(
                    'class' => 'Endereco',
                    'foreignKey' => 'codigo_endereco'
                )
            )

        ));
    } 
    
    function tipoContatoEnderecoUnico($field = array()) {
        $edit_mode = isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']);

        if ($edit_mode)
            return true;
        else
            $conditions = array(
                'conditions' => array(
                    'codigo_cliente' => $this->data[$this->name]['codigo_cliente'],
                    'codigo_tipo_contato' => $this->data[$this->name]['codigo_tipo_contato']
                )
            );

        $tipoContatoExistente = $this->find('count', $conditions);

        if ($tipoContatoExistente > 0) {
            return false;
        }

        return true;
    }
    
    function listaEnderecos($conditions) {

        if (empty($conditions) || !isset($conditions) || !is_array($conditions))
            return false;

        $fields = array(
            'ClienteEndereco.*',
            'TipoContato.descricao',
        );

        return $this->find('all', array('conditions' => $conditions,'fields' => $fields));
    }

    function listaEnderecosByCodigoCliente($codigo_cliente) {
        return $this->listaEnderecos(
                        array('ClienteEndereco.codigo_cliente' => $codigo_cliente)
        );
    }

    function listaEnderecoByCodigoCliente($codigo_cliente, $tipo_contato) {
        return $this->listaEnderecos(
                        array(
                            'ClienteEndereco.codigo_cliente' => $codigo_cliente,
                            'ClienteEndereco.codigo_tipo_contato' => $tipo_contato
                        )
        );
    }

    function listaEnderecosExcetoByCodigoCliente($codigo_cliente, $tipo_contato) {
        return $this->listaEnderecos(
                        array(
                            'ClienteEndereco.codigo_cliente' => $codigo_cliente,
                            'ClienteEndereco.codigo_tipo_contato !=' => $tipo_contato
                        )
        );
    }

    function getByTipoContato($codigo_cliente = 0, $codigo_tipo_contato = 0) {
        $this->bindModel(array('belongsTo' => array('VEndereco' => array('className' => 'VEndereco', 'foreignKey' => 'codigo_endereco'))));
        $cliente_endereco = $this->find('first', array('conditions' => array('ClienteEndereco.codigo_cliente' => $codigo_cliente, 'ClienteEndereco.codigo_tipo_contato' => $codigo_tipo_contato)));
        $this->unbindModel(array('belongsTo' => array('VEndereco')));
        return $cliente_endereco;
    }
    
    function getByCodigoClienteEndereco($codigo_cliente_endereco) {
                
        $conditions = array(
            'conditions' => array(
                'ClienteEndereco.codigo' => $codigo_cliente_endereco
            )
        );
        
        $result = $this->find('first', $conditions);

        return $result;
    }

    function enderecoCompleto($codigo) {
        $cliente_endereco = $this->carregar($codigo);
        return $cliente_endereco;
    }
    
    function incluir($data = null, $validate = true, $fieldList = array()){
        if (isset($data['Outros']['repetir_para']) && !empty($data['Outros']['repetir_para'])) {
            $this->query('begin transaction');
            if (!parent::incluir($data, $validate, $fieldList)) {
                    $this->query('rollback');
                    return false;
            }

            foreach ($data['Outros']['repetir_para'] as $codigo_tipo_contato) {
                    if ($codigo_tipo_contato != $data[$this->name]['codigo_tipo_contato']) {
                            //var_dump($codigo_tipo_contato != $data[$this->name]['codigo_tipo_contato']);
                            $dados_replica_de_contato = $data;
                            $dados_replica_de_contato[$this->name]['codigo_tipo_contato'] = $codigo_tipo_contato;
                            if (!parent::incluir($dados_replica_de_contato)) {
                                    $this->query('rollback');
                                    return false;
                            }
                    }
            }
            $this->query('commit');
            return true;
        } else {
            return parent::incluir($data, $validate, $fieldList);
        }
    }

    function importacao_endereco_comercial($dados) {
        $this->Endereco = & ClassRegistry::init('Endereco');
        
        $dados_endereco = array('ClienteEndereco' => $dados['ClienteEndereco']);

        $enderecoCompleto = $dados_endereco['ClienteEndereco']['logradouro'] . ' ' . $dados_endereco['ClienteEndereco']['numero'] . ' - ' . $dados_endereco['ClienteEndereco']['cidade'] . ' - ' . $dados_endereco['ClienteEndereco']['estado_descricao'];

        $dados['ClienteEndereco']['latitude'] = null;
        $dados['ClienteEndereco']['longitude'] = null;

        if($dados['ClienteEndereco']['numero'] == ''){
            $dados['ClienteEndereco']['numero'] = 0;
        }

        $retorno = '';
        
        $dados['ClienteEndereco']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;
        
        if (!isset($dados['ClienteEndereco']['codigo']) && empty($dados['ClienteEndereco']['codigo'])) {
            if(!parent::incluir($dados)){
                $erro_cliente = '';
                foreach ($this->validationErrors as $key => $value) {
                    $erro_cliente .= utf8_decode($value).'|';
                    $this->validationErrors[$key] = $erro_cliente;
                }
                $retorno['ClienteEndereco'] = $this->validationErrors;
            }
        }
        else{
            if(!parent::atualizar($dados)){
                $erro_cliente = '';
                foreach ($this->validationErrors as $key => $value) {
                    $erro_cliente .= utf8_decode($value).'|';
                    $this->validationErrors[$key] = $erro_cliente;
                }
                $retorno['ClienteEndereco'] = $this->validationErrors;
            }
        }
        
        return $retorno;
    }
    
    function retornaEnderecoDoCliente($codigo_cliente) {
    	
    	if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        }
        else if(Ambiente::TIPO_MAPA == 2) {
            App::import('Component',array('ApiGeoPortal'));
            $this->ApiMaps = new ApiGeoPortalComponent();
        }
    	
    	$options['fields'] = array(
    			'ClienteEndereco.codigo_cliente',
    			'ClienteEndereco.logradouro',
    			'ClienteEndereco.numero',
    			'ClienteEndereco.cidade',
    			'ClienteEndereco.estado_descricao',
    			'ClienteEndereco.latitude',
    			'ClienteEndereco.longitude'
    	);
    		
    	$options['conditions'] = array('ClienteEndereco.codigo_cliente' => $codigo_cliente);   	
    	
    	return $this->find('first', $options);
    }


    /**
     * [getCidadeUnidade description]
     * 
     * metodo para pegar todos as cidades da matriz/alocacao
     * 
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function getCidadeUnidade($codigo_cliente,$uf=null)
    {
        //declara a variavel
        $dados = array();

        //verifica se existe o codigo cliente
        if($codigo_cliente) {

            $where = '';
            //verifica se existe valor na uf 
            if(!is_null($uf)) {
                $where = " AND cliente_endereco.estado_abreviacao = '".$uf."' ";
            }

            //pega os dados da cliente_endereco, para todas as unidades relacionadas no grupo_economico_cliente.
            $query_sql ="SELECT 
                            cliente_endereco.cidade AS cidade
                        FROM RHHealth.dbo.cliente_endereco 
                        WHERE cliente_endereco.codigo_cliente IN (  SELECT gec.codigo_cliente 
                                                                    FROM RHHealth.dbo.grupos_economicos_clientes gec 
                                                                    WHERE gec.codigo_grupo_economico = (SELECT TOP 1 g.codigo_grupo_economico 
                                                                                                        FROM RHHealth.dbo.grupos_economicos_clientes g 
                                                                                                        WHERE g.codigo_cliente = {$codigo_cliente}))
                            {$where}
                        GROUP BY cliente_endereco.cidade
                        ORDER BY cliente_endereco.cidade;";

            //executa a query
            $dados = $this->query($query_sql);

        }//fim if codigo_cliente

        return $dados;

    }//fim getCidadeUnidade

    /**
     * [get_combo_cidade description]
     * 
     * metodo para montar o combo de cidad com o codigo do cliente passado por parametro
     * 
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function get_combo_cidade($codigo_cliente, $uf=null)
    {
        //pega as cidades e estados do codigo unidade pesquisado
        $cid_unidade = $this->getCidadeUnidade($codigo_cliente,$uf);
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
    public function getEstadosUnidade($codigo_cliente)
    {
        //declara a variavel
        $dados = array();

        //verifica se existe o codigo cliente
        if($codigo_cliente) {

            //pega os dados da cliente_endereco, para todas as unidades relacionadas no grupo_economico_cliente.
            $query_sql ="SELECT 
                            cliente_endereco.estado_abreviacao AS estado 
                        FROM RHHealth.dbo.cliente_endereco 
                        WHERE cliente_endereco.codigo_cliente IN (  SELECT gec.codigo_cliente 
                                                                    FROM RHHealth.dbo.grupos_economicos_clientes gec 
                                                                    WHERE gec.codigo_grupo_economico = (SELECT TOP 1 g.codigo_grupo_economico 
                                                                                                        FROM RHHealth.dbo.grupos_economicos_clientes g 
                                                                                                        WHERE g.codigo_cliente = {$codigo_cliente}))
                        GROUP BY cliente_endereco.estado_abreviacao 
                        ORDER BY cliente_endereco.estado_abreviacao;";

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
        $est_unidade = $this->getEstadosUnidade($codigo_cliente);
        $result = array();
        foreach ($est_unidade as $value) {
            $result[] = array('codigo' => $value[0]['estado'], 'descricao' => $value[0]['estado']);
        }

        return $result;

    }//fim get_combo_estado


}

