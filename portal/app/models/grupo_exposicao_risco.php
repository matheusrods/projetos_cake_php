<?php
class GrupoExposicaoRisco extends AppModel {

    var $name = 'GrupoExposicaoRisco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'grupos_exposicao_risco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_exposicao_risco'));

    var $validate = array(
        'codigo_grupo_exposicao' => array(
            'rule' => 'notEmpty',
            'message' => 'Grupo de Exposi��o n�o enviado!'
        ),
        'codigo_risco' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o Risco!',
            ),
            'validaExposicaoRisco' => array(
                'rule' => 'validaExposicaoRisco',
                'message' => 'Risco j� cadastrado!!',
                'on' => 'create'
            )
        )
    );

	function retorna_dados($codigo) {
        $model_GrupoExposicao = & ClassRegistry::init('GrupoExposicao');
    	$model_Risco = & ClassRegistry::init('Risco');
    	$model_ClienteSetor = & ClassRegistry::init('ClienteSetor');
    	$model_Cargo = & ClassRegistry::init('Cargo');
    	$model_Setor = & ClassRegistry::init('Setor');
    	$model_GrupoHomogeneo = & ClassRegistry::init('GrupoHomogeneo');
    	$model_GrupoHomDetalhe = & ClassRegistry::init('GrupoHomDetalhe');
    	
    	$conditions = array('GrupoExposicaoRisco.codigo' => $codigo);
    	
    	$fields = array(
    			'GrupoExposicaoRisco.codigo',
    			'GrupoExposicao.codigo',
    			'GrupoExposicao.codigo_cargo',
    			'ClienteSetor.codigo_cliente',
    			'ClienteSetor.codigo_setor',
    			'Setor.descricao',
    			'Cargo.descricao',
    			'GrupoHomogeneo.codigo',
    			'GrupoHomogeneo.descricao',
    			'Risco.nome_agente'
    	);
    	
    	$joins  = array(
    			array(
    					'table' => $model_GrupoExposicao->databaseTable.'.'.$model_GrupoExposicao->tableSchema.'.'.$model_GrupoExposicao->useTable,
    					'alias' => 'GrupoExposicao',
    					'type' => 'INNER',
    					'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao',
    			),
    			array(
    					'table' => $model_Risco->databaseTable.'.'.$model_Risco->tableSchema.'.'.$model_Risco->useTable,
    					'alias' => 'Risco',
    					'type' => 'INNER',
    					'conditions' => 'Risco.codigo = GrupoExposicaoRisco.codigo_risco',
    			),
    			array(
    					'table' => $model_ClienteSetor->databaseTable.'.'.$model_ClienteSetor->tableSchema.'.'.$model_ClienteSetor->useTable,
    					'alias' => 'ClienteSetor',
    					'type' => 'LEFT',
    					'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
    			),
    			array(
    					'table' => $model_Cargo->databaseTable.'.'.$model_Cargo->tableSchema.'.'.$model_Cargo->useTable,
    					'alias' => 'Cargo',
    					'type' => 'LEFT',
    					'conditions' => 'Cargo.codigo = GrupoExposicao.codigo_cargo',
    			),
    			array(
    					'table' => $model_Setor->databaseTable.'.'.$model_Setor->tableSchema.'.'.$model_Setor->useTable,
    					'alias' => 'Setor',
    					'type' => 'LEFT',
    					'conditions' => 'Setor.codigo = ClienteSetor.codigo_setor',
    			),
    			array(
    					'table' => $model_GrupoHomogeneo->databaseTable.'.'.$model_GrupoHomogeneo->tableSchema.'.'.$model_GrupoHomogeneo->useTable,
    					'alias' => 'GrupoHomogeneo',
    					'type' => 'LEFT OUTER',
    					'conditions' => 'GrupoHomogeneo.codigo = GrupoExposicao.codigo_grupo_homogeneo AND ClienteSetor.codigo_cliente = GrupoHomogeneo.codigo_cliente',
    			),
    			array(
    					'table' => $model_GrupoHomDetalhe->databaseTable.'.'.$model_GrupoHomDetalhe->tableSchema.'.'.$model_GrupoHomDetalhe->useTable,
    					'alias' => 'GrupoHomDetalhe',
    					'type' => 'LEFT OUTER',
    					'conditions' => 'GrupoHomDetalhe.codigo_setor = Setor.codigo AND GrupoHomDetalhe.codigo_cargo = Cargo.codigo and GrupoHomogeneo.codigo = GrupoHomDetalhe.codigo_grupo_homogeneo',
    			),
    	);
    	
    	return $this->find('first', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));
    }    function converteFiltroEmCondition($data) {
        $conditions = array();
       
        if (! empty ( $data ['codigo_grupo_exposicao'] ))
			$conditions ['GrupoExposicaoRisco.codigo_grupo_exposicao'] = $data['codigo_grupo_exposicao'];

        if (! empty ( $data ['codigo_risco'] ))
			$conditions ['GrupoExposicaoRisco.codigo_risco'] = $data['codigo_risco'];
		
		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(GrupoExposicaoRisco.ativo = ' . $data ['ativo'] . ' OR GrupoExposicaoRisco.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['GrupoExposicaoRisco.ativo'] = $data ['ativo'];
	    }
    
        return $conditions;
    }
    
    function excluir($codigo) {
        $GrupoExpRiscoFonteGera =& ClassRegistry::Init('GrupoExpRiscoFonteGera');
        $GrupoExposicaoRiscoEpi =& ClassRegistry::Init('GrupoExposicaoRiscoEpi');
        $GrupoExposicaoRiscoEpc =& ClassRegistry::Init('GrupoExposicaoRiscoEpc');

        try{  
            $this->query('begin transaction');


            $fonte_geradora = $GrupoExpRiscoFonteGera->find("first", array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo)));
            if(!(empty($fonte_geradora))){
                if(!$GrupoExpRiscoFonteGera->deleteAll(array('GrupoExpRiscoFonteGera.codigo_grupos_exposicao_risco' => $codigo), false)){
                    throw new Exception("Ocorreu um erro: GrupoExpRiscoFonteGera");
                }
            }

            $epi = $GrupoExposicaoRiscoEpi->find("first", array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo)));
            if(!empty($epi)){
                if(!$GrupoExposicaoRiscoEpi->deleteAll(array('GrupoExposicaoRiscoEpi.codigo_grupos_exposicao_risco' => $codigo), false)){
                    throw new Exception("Ocorreu um erro: GrupoExposicaoRiscoEpi");
                }
            }

            $epc = $GrupoExposicaoRiscoEpc->find("first", array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo)));
            if(!empty($epc)){
                if(!$GrupoExposicaoRiscoEpc->deleteAll(array('GrupoExposicaoRiscoEpc.codigo_grupos_exposicao_risco' => $codigo), false)){
                    throw new Exception("Ocorreu um erro: GrupoExposicaoRiscoEpc");
                }
            }

            if (!parent::excluir($codigo)) {
               throw new Exception("Ocorreu um erro: GrupoExposicaoRisco");
            }

            $this->commit();
            return true;
        } 
        catch (Exception $ex) {
            //debug($ex->getmessage());
            $this->rollback();
            return false;
        }
    }

    function validaExposicaoRisco() {
        $conditions = array(
            'codigo_grupo_exposicao' => $this->data['GrupoExposicaoRisco']['codigo_grupo_exposicao'],
            'codigo_risco' => $this->data['GrupoExposicaoRisco']['codigo_risco']
            );
        $fields = array(
            'GrupoExposicaoRisco.codigo','GrupoExposicaoRisco.codigo_grupo_exposicao','GrupoExposicaoRisco.codigo_risco'
        );

        $validar = $this->find('first', array('conditions' => $conditions, 'fields' => $fields));
        if(empty($validar)){
            return true;
        }
        else{               
            return false;
        }
    }

    function grupo_exposicao_risco_importacao($dados){

        if (isset($dados['GrupoExposicaoRisco']['codigo_grupo_exposicao']) && !empty($dados['GrupoExposicaoRisco']['codigo_grupo_exposicao'])) { //INSERE NA TABELA CLIENTES_SETORES            

            if(isset($dados['GrupoExposicaoRisco']['codigo']) && !empty($dados['GrupoExposicaoRisco']['codigo']))
            {
                if(!parent::atualizar($dados, false)){
                    $erro = '';
                    foreach ($this->validationErrors as $key => $value) {
                        $erro .= utf8_decode($value).'|';
                        $this->validationErrors[$key] = $erro;
                    }
                    $retorno['Erro']['GrupoExposicaoRisco'] = $this->validationErrors;
                }
                else{
                    if(!empty($this->id)){
                        $consulta_dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));
                        if(empty($consulta_dados)){
                            $retorno['Erro']['GrupoExposicaoRisco'] = array('codigo_grupo_exposicao_risco' => 'Erro ao cadastrar o Grupo de Exposi��o!');
                        }
                        else{
                            $retorno['Dados'] = $consulta_dados;
                        }
                    }
                }
            }else{
                if(!parent::incluir($dados, false)){
                    $erro = '';
                    foreach ($this->validationErrors as $key => $value) {
                        $erro .= utf8_decode($value).'|';
                        $this->validationErrors[$key] = $erro;
                    }
                    $retorno['Erro']['GrupoExposicaoRisco'] = $this->validationErrors;
                }
                else{
                    if(!empty($this->id)){
                        $consulta_dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));
                        if(empty($consulta_dados)){
                            $retorno['Erro']['GrupoExposicaoRisco'] = array('codigo_grupo_exposicao_risco' => 'Erro ao cadastrar o Grupo de Exposi��o!');
                        }
                        else{
                            $retorno['Dados'] = $consulta_dados;
                        }
                    }
                }
            }
        }
        else{
            $retorno['Erro']['GrupoExposicaoRisco'] = array('codigo_grupo_exposicao_risco' => 'Erro ao cadastrar o Grupo de Exposi��o!');
        }
        return $retorno;
    }

    function retorna_grupo_exposicao_risco_importacao($dados_grupo_exposicao, $data){
        $this->Risco =& ClassRegistry::Init('Risco');
        $conditions = array(
            'GrupoExposicaoRisco.codigo_grupo_exposicao' => $dados_grupo_exposicao['GrupoExposicao']['codigo'],
            'Risco.nome_agente' => $data['risco']
            );

        $joins = array(
            array(
              'table' => $this->Risco->databaseTable.'.'.$this->Risco->tableSchema.'.'.$this->Risco->useTable, 
              'alias' => 'Risco',
              'type' => 'LEFT',
              'conditions' => 'GrupoExposicaoRisco.codigo_risco = Risco.codigo',
            ),
        );
        
        $consulta_grupo_exposicao_risco = $this->find('first',  compact('conditions', 'joins'));

        if(empty($consulta_grupo_exposicao_risco)){
            $retorno['Erro']['GrupoExposicao'] = array('codigo_grupo_exposicao' => ('Riscos do Grupo de Exposi��o n�o encontrado!'));
        }
        else{
            $retorno['Dados'] = $consulta_grupo_exposicao_risco;
        }

        return $retorno;

    }

    /**
     * [get_grupo_exposicao_risco description]
     * 
     * metodo para pegar os dados do grupo de exposicao risco
     * 
     * @return [type] [description]
     */
    public function get_grupo_exposicao_risco($dados,$codigo_grupo_exposicao)
    {   
        //variavel auxiliar
        $arr_dados=array();
        
        //varre os riscos
        foreach($dados as $key => $risco){

            //retira o k que � o vazio que sempre vem no array de riscos
            if(is_numeric($key)) {

                //verifica se tem codigo
                if(!empty($risco['codigo_risco'])) {

                    //pega os dados para 
                    $ger = $this->find('first', array('conditions' => array('codigo_grupo_exposicao' => $codigo_grupo_exposicao,'codigo_risco' => $risco['codigo_risco'])));
                    
                    //verifica se tem registro caso na tenha monta o array novo para inserir
                    if(empty($ger)) {

                        $arr_dados[] = array(
                            'codigo' => null,
                            'codigo_grupo_risco' => $risco['codigo_grupo_risco'],
                            'codigo_risco' => $risco['codigo_risco'],
                            'codigo_risco_atributo' => $risco['codigo_risco_atributo'],
                            'codigo_tecnica_medicao' => $risco['codigo_tecnica_medicao'],
                            'valor_maximo' => $risco['valor_maximo'],
                            'valor_medido' => $risco['valor_medido'],
                            'tempo_exposicao' => $risco['tempo_exposicao'],
                            'minutos_tempo_exposicao' => $risco['minutos_tempo_exposicao'],
                            'jornada_tempo_exposicao' => $risco['jornada_tempo_exposicao'],
                            'intensidade' => $risco['intensidade'],
                            'dano' => $risco['dano'],
                            'medidas_controle' => $risco['medidas_controle'],
                            'medidas_controle_recomendada' => $risco['medidas_controle_recomendada'],
                        );

                    }//fim ger
                    else {
                        //seta o risco
                        $arr_dados[] = $risco;
                    }//fim else ger

                }//fim codigo risco
            }//fim chave k
            
        }//fim foreach

        //mota o array com o k
        $arr_dados['k'] = $dados['k'];
        
        return $arr_dados;

    }//fim get_grupo_exposicao_risco


    /**
     * [getLastSaveTecMedGrupoExposicaoRisco description]
     * 
     * metodo para pegar o último registro salvo no BD, pelo codigo do funcionario
     *  do grupo de exposicao risco
     * 
     * @return [type] [description]
     */
    public function getLastSaveTecMedGrupoExposicaoRisco($codigo_grupo_exposicao)
    { 
        $dados = $this->find('first', array('conditions' => array('GrupoExposicaoRisco.codigo_grupo_exposicao' => $codigo_grupo_exposicao), 'fields' => array('GrupoExposicaoRisco.codigo', 'GrupoExposicaoRisco.codigo_tec_med_ppra'),'order' => array('GrupoExposicaoRisco.data_inclusao DESC')));
        return $dados;

    }

    public function getClientesSemTecnicaMedicao() {

        $queryOptions = array(
            'fields' => array(
                'Cliente.codigo',
                'Cliente.nome_fantasia',
                'Cliente.razao_social',
                'ClienteMatriz.codigo',
                'ClienteMatriz.nome_fantasia',
                'ClienteMatriz.razao_social',				
                'Risco.codigo',
                'Risco.nome_agente',
                'GrupoExposicaoRisco.codigo',
                'GrupoExposicaoRisco.codigo_risco',
                'GrupoExposicaoRisco.codigo_grupo_exposicao',
                'GrupoExposicaoRisco.codigo_tec_med_ppra',
                'ClienteSetor.codigo',
                'ClienteSetor.codigo_cliente',
                'ClienteSetor.codigo_setor',
                'Setor.codigo',
                'Setor.descricao',
            ),
            'joins' => array(
                array(
                    'table' => 'riscos',
                    'alias' => 'Risco',
                    'conditions' => 'Risco.codigo = GrupoExposicaoRisco.codigo_risco',
                    'type' => 'INNER',						
                ),
                array(
                    'table' => 'grupo_exposicao',
                    'alias' => 'GrupoExposicao',
                    'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao',
                    'type' => 'INNER',						
                ),				
                array(
                    'table' => 'clientes_setores',
                    'alias' => 'ClienteSetor',
                    'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
                    'type' => 'INNER',						
                ),	
                array(
                    'table' => 'setores',
                    'alias' => 'Setor',
                    'conditions' => 'Setor.codigo = ClienteSetor.codigo_setor',
                    'type' => 'INNER',						
                ),	
                array(
                    'table' => 'cliente',
                    'alias' => 'Cliente',
                    'conditions' => 'Cliente.codigo = ClienteSetor.codigo_cliente',
                    'type' => 'INNER',						
                ),	
                array(
                    'table' => 'grupos_economicos_clientes',
                    'alias' => 'GrupoEconomicoCliente',
                    'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo',
                    'type' => 'INNER',
                ),
                array(
                    'table' => 'grupos_economicos',
                    'alias' => 'GrupoEconomico',
                    'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
                    'type' => 'INNER',
                ),
                array(
                    'table' => 'cliente',
                    'alias' => 'ClienteMatriz',
                    'conditions' => 'ClienteMatriz.codigo = GrupoEconomico.codigo_cliente',
                    'type' => 'INNER',						
                ),																												
            ),
            'conditions' => array(
                'Risco.ativo' => 1,				
                'GrupoExposicaoRisco.codigo_tec_med_ppra IS NULL',
                'GrupoExposicaoRisco.codigo_tipo_medicao' => 1,
                'Cliente.codigo = ClienteMatriz.codigo',
                'Cliente.codigo_empresa' => 1,
                'Cliente.ativo' => 1,
                // 'ClienteMatriz.codigo' => array(
                //     20,
                //     37,
                //     38,
                //     79,
                //     116,
                //     1942,
                //     2394,
                //     8821,
                //     10011,
                //     10155,
                //     51321,
                //     56977,
                //     58127,
                //     58399,
                //     58522,
                //     59917,
                //     69891,
                //     70148,
                //     72293,
                //     80177,
                //     81071,
                //     81157,
                //     81731,
                //     82476,
                //     82513,
                //     84706,
                //     86300,
                //     90602,
                //     92338,
                //     93852,
                //     94286,
                //     94422,
                //     94514,
                //     94547,
                //     94573,
                //     94577,
                //     94581,
                //     94697,
                //     94848,
                //     96368,
                //     96454,
                //     317218
                // )
            ),
            'order' => array(
                'GrupoEconomico.codigo',
                'Cliente.codigo',
                'Risco.codigo'
            ),
            //'limit' => 96,
            'recursive' => -1
        );
        
        // echo '<pre>';
        // print_r($this->find('sql', $queryOptions));
        // echo '</pre>';

        $query = $this->find('all', $queryOptions);

        return $query;
    }    
}