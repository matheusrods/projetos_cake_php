<?php
class GrupoExposicao extends AppModel {

	var $name = 'GrupoExposicao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupo_exposicao';
	var $primaryKey = 'codigo';
    var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_grupo_exposicao'));

	var $validate = array(
        'codigo_cargo' => array(
            // 'rule' => 'notEmpty',
            // 'message' => 'Informe o Cargo',
            // 'required' => true
        ),
        // 'codigo_grupo_homogeneo' => array(
        //    'rule' => 'notEmpty',
        //    'message' => 'Informe o grupo homogeneo',
        //    'required' => true
        //    ),
        // 'data_inicio_vigencia' => array(
        //     'rule' => 'notEmpty',
        //     'message' => 'Informe a data de início de vigência',
        //     'required' => true
        // ),
        'codigo_cliente_setor' => array(
            // 'notEmpty' => array(
            //     'rule' => 'notEmpty',
            //     'message' => 'Informe o Setor!',
            // ),
            'validaGrupoExposicao' => array(
                'rule' => 'validaGrupoExposicao',
                'message' => 'Grupo de Exposicao já cadastrado!',
                'on' => 'create'
            )
        )
    );

	function converteFiltroEmCondition($data) {
		
        $conditions = array();
        if (!empty($data['codigo']))
            $conditions['GrupoExposicao.codigo'] = $data['codigo'];
        
        if (!empty($data['codigo_cliente_alocacao']))
            $conditions['ClienteSetor.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'];

        if (!empty($data['codigo_setor']))
            $conditions['ClienteSetor.codigo_setor'] = $data['codigo_setor'];

        if (!empty($data['codigo_cargo']))
            $conditions['GrupoExposicao.codigo_cargo'] = $data['codigo_cargo'];
        
        if (!empty($data['codigo_grupo_homogeneo']))
            $conditions['GrupoExposicao.codigo_grupo_homogeneo'] = $data['codigo_grupo_homogeneo'];

        if(!empty($data['codigo_funcionario']))
            $conditions['GrupoExposicao.codigo_funcionario'] = $data['codigo_funcionario'];

        $conditions['Setor.ativo'] = 1;
        $conditions['Cargo.ativo'] = 1;

        return $conditions;
    }


    /**
     * Metodo para inclusao do dados
     */ 
    function incluir($data) {
        $ClienteSetor =& ClassRegistry::Init('ClienteSetor');
        $OrdemServico =& ClassRegistry::Init('OrdemServico');
        $OrdemServicoItem =& ClassRegistry::Init('OrdemServicoItem');
        $GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
        $GrupoExposicaoRisco =& ClassRegistry::Init('GrupoExposicaoRisco');
        $GrupoExpRiscoFonteGera =& ClassRegistry::Init('GrupoExpRiscoFonteGera');
        $GrupoExposicaoRiscoEpi =& ClassRegistry::Init('GrupoExposicaoRiscoEpi');
        $GrupoExposicaoRiscoEpc =& ClassRegistry::Init('GrupoExposicaoRiscoEpc');
        $Configuracao =& ClassRegistry::Init('Configuracao');
        $AtribuicaoGrupoExpo =& ClassRegistry::Init('AtribuicaoGrupoExpo');
        $GrupoExpRiscoAtribDet =& ClassRegistry::Init('GrupoExpRiscoAtribDet');
        $AlertaHierarquiaPendente =& ClassRegistry::Init('AlertaHierarquiaPendente');

        try{
            $this->query('begin transaction');

            if(isset($data['GrupoExposicao']['descricao_tipo_setor_cargo']) && !empty($data['GrupoExposicao']['descricao_tipo_setor_cargo'])){

                //VERIFICA SE EXISTE A RELACAO CLIENTEXSETOR.
                $conditions = array('ClienteSetor.codigo_setor' => $data['ClienteSetor']['codigo_setor'], 'ClienteSetor.codigo_cliente_alocacao' => $data['ClienteSetor']['codigo_cliente_alocacao']);

                //busca o cliente setor
                $verifica_setor = $ClienteSetor->find('first', array('conditions' => $conditions));

                if(empty($verifica_setor)){
                    $rsClienteSetor = $ClienteSetor->incluir($data);
                } else {
                    $data['ClienteSetor']['codigo'] = $verifica_setor['ClienteSetor']['codigo'];
                    $rsClienteSetor = $ClienteSetor->atualizar($data);
                }

                if($rsClienteSetor) {

                    $codigo_cliente_setor = $ClienteSetor->id;

                    $data['GrupoExposicao']['codigo_cliente_setor'] = $codigo_cliente_setor;

                    if(isset($data['GrupoHomDetalhe']) && !empty($data['GrupoHomDetalhe'])){
                        foreach ($data['GrupoHomDetalhe'] as $linha => $dados) {

                            if(isset($dados['codigo_setor_ghe']) && ($dados['codigo_setor_ghe'] == $data['ClienteSetor']['codigo_setor']) && ($dados['codigo_cargo_ghe'] == $data['GrupoExposicao']['codigo_cargo'])){
                                $data['GrupoExposicao']['descricao_atividade'] = $dados['descricao_atividade_ghe'];
                            }
                        }
                    }

                    //INCLUI O GRUPO DE EXPOSICAO
                    if (parent::incluir($data)) {
                        $codigo_grupo_exposicao = $this->id;

                        $joins  = array(
                            array(
                              'table' => $OrdemServicoItem->databaseTable.'.'.$OrdemServicoItem->tableSchema.'.'.$OrdemServicoItem->useTable,
                              'alias' => 'OrdemServicoItem',
                              'conditions' => 'OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico',
                            )
                        );

                        $conditions = array('codigo_cliente' => $data['ClienteSetor']['codigo_cliente_alocacao']);

                        $verifica_ordem_servico = $OrdemServico->find('first', array('conditions' => $conditions,  'joins' => $joins));

                        $codigo_servico_ppra = $OrdemServico->getPPRAByCodigoCliente($data['ClienteSetor']['codigo_cliente_alocacao']);

                        //JA POSSUI PPRA. PREENCHER SOMENTE O FORMULARIO.
                        if(empty($verifica_ordem_servico)){

                            /**
                             * validacao para este cliente pois é o unico que esta duplicando, logando para identificar o problema
                             */
                            if($data['ClienteSetor']['codigo_cliente_alocacao'] == 80013) {
                                //transformar array em json
                                $arrJson = json_encode($data);

                                //instancia a model
                                $this->LogIntegracao = ClassRegistry::init('LogIntegracao');
                                
                                //seta os valores
                                $log_integracao['LogIntegracao']['codigo_cliente']          = '71758';
                                $log_integracao['LogIntegracao']['codigo_usuario_inclusao'] = $this->cod_usuario;
                                $log_integracao['LogIntegracao']['descricao']               = $arrJson;
                                $log_integracao['LogIntegracao']['arquivo']                 = 'ERRO_ORDEM_SERVICO';
                                $log_integracao['LogIntegracao']['conteudo']                = $arrJson;
                                $log_integracao['LogIntegracao']['retorno']                 = '-';
                                $log_integracao['LogIntegracao']['sistema_origem']          = '$arquivo';
                                $log_integracao['LogIntegracao']['data_arquivo']            = date('Y-m-d H:i:s');
                                $log_integracao['LogIntegracao']['status']                  = '99'; 
                                $log_integracao['LogIntegracao']['tipo_operacao']           = 'I'; //inserido

                                //inclui na tabela
                                $this->LogIntegracao->incluir($log_integracao);
                            }

                            $matriz = $GrupoEconomicoCliente->retorna_dados_cliente($data['ClienteSetor']['codigo_cliente_alocacao']);

                            $dados = array('OrdemServico'=>
                                array(
                                    'codigo_grupo_economico' => $matriz['GrupoEconomicoCliente']['codigo_grupo_economico'],
                                    'codigo_cliente' => $data['ClienteSetor']['codigo_cliente_alocacao'],
                                    'codigo_fornecedor' => 0,
                                    'status_ordem_servico' => 3
                                )
                            );                            

                            if($OrdemServico->incluir($dados)){

                                $codigo_ordem_servico = $OrdemServico->id;

                                

                                $dados_item = array('OrdemServicoItem' => 
                                    array(
                                        'codigo_ordem_servico' => $codigo_ordem_servico,
                                        'codigo_servico' => $codigo_servico_ppra
                                    )
                                );
                                
                                if(!$OrdemServicoItem->incluir($dados_item)) {
                                    throw new Exception("Ocorreu um erro: OrdemServicoItem");
                                }                            
                            } else {
                                throw new Exception("Ocorreu um erro: OrdemServico");
                            }
                        } else {
                            //NAO POSSUI PPRA EXISTENTE. CADASTRO EFETUADO ATRAVES DA RHHEALTH
                            if(!$OrdemServico->atualiza_status($verifica_ordem_servico['OrdemServico']['codigo'], 2, $codigo_servico_ppra)){
                                throw new Exception("Ocorreu um erro: OrdemServico");
                            }
                        }

                        //INCLUI AS ATRIBUICOES
                        if(!empty($data['atribuicoes'])){

                            foreach ($data['atribuicoes'] as $atribuicao) {

                                $atribuicoes_grupos['AtribuicaoGrupoExpo'] = array(
                                    'codigo_atribuicao' => $atribuicao,
                                    'codigo_grupo_exposicao' => $codigo_grupo_exposicao
                                ); 


                                if(!$AtribuicaoGrupoExpo->incluir($atribuicoes_grupos)){
                                   throw new Exception("Ocorreu um erro: AtribuicaoGrupoExpo");
                                }
                            }
                        }

                        if(isset($data['GrupoExposicaoRisco']['k'])){
                            unset($data['GrupoExposicaoRisco']['k']);
                        }

                        //INCLUI OS RISCOS
                        if (isset($data['GrupoExposicaoRisco'])){
                            if(count($data['GrupoExposicaoRisco']) > 0){

                                foreach ($data['GrupoExposicaoRisco'] as $key => $dados) {
                                    if(!empty($dados['codigo_risco'])){
                                        if(isset($dados['codigo_tipo_medicao'])){
                                            if($dados['codigo_tipo_medicao'] == 2){
                                                $dados['codigo_tecnica_medicao'] = null;
                                                $dados['valor_maximo'] = null;
                                                $dados['valor_medido'] = null;
                                            }
                                        } else {
                                            $dados['codigo_tipo_medicao'] = null;
                                        }

                                        if(empty($dados['resultante'])) $dados['resultante'] = null;
                                        if(empty($dados['descanso_tbn'])) $dados['descanso_tbn'] = null;
                                        if(empty($dados['descanso_tbs'])) $dados['descanso_tbs'] = null;
                                        if(empty($dados['descanso_tg'])) $dados['descanso_tg'] = null;
                                        if(empty($dados['trabalho_tbn'])) $dados['trabalho_tbn'] = null;
                                        if(empty($dados['trabalho_tbs'])) $dados['trabalho_tbs'] = null;
                                        if(empty($dados['trabalho_tg'])) $dados['trabalho_tg'] = null;
                                        if(empty($dados['grau_risco'])) $dados['grau_risco'] = null;   
                                        if(empty($dados['codigo_risco_atributo'])) $dados['codigo_risco_atributo'] = null;   
                                        if(empty($dados['meio_propagacao'])) $dados['meio_propagacao'] = null;   
                                        if(empty($dados['codigo_tecnica_medicao'])) $dados['codigo_tecnica_medicao'] = null;   
                                        if(empty($dados['codigo_tipo_medicao'])) $dados['codigo_tipo_medicao'] = null;   
                                        if(empty($dados['descanso_tempo_exposicao'])) $dados['descanso_tempo_exposicao'] = null;   
                                        if(empty($dados['valor_maximo'])) $dados['valor_maximo'] = null;   
                                        if(empty($dados['valor_medido'])) $dados['valor_medido'] = null;   
                                        if(!isset($dados['avaliacao_instantanea']) && empty($dados['avaliacao_instantanea'])) $dados['avaliacao_instantanea'] = null;
                                        if(!isset($dados['dosimetria']) && empty($dados['dosimetria'])) $dados['dosimetria'] = null;
                                        if(!isset($dados['descanso_no_local']) && empty($dados['descanso_no_local'])) $dados['descanso_no_local'] = null;
                                        if(!isset($dados['carga_solar']) && empty($dados['carga_solar'])) $dados['carga_solar'] = null;
                                        if(!isset($dados['epi_eficaz']) && empty($dados['epi_eficaz'])) $dados['epi_eficaz'] = null;
                                        if(!isset($dados['medidas_controle']) && empty($dados['medidas_controle'])) $dados['medidas_controle'] = null;
                                        if(!isset($dados['medidas_controle_recomendada']) && empty($dados['medidas_controle_recomendada'])) $dados['medidas_controle_recomendada'] = null;

                                        $dados_grupo_exposicao_risco = array(
                                            'GrupoExposicaoRisco' => array(
                                                'codigo_risco'              => $dados['codigo_risco'],
                                                'codigo_grupo_exposicao'    => $codigo_grupo_exposicao,
                                                'tempo_exposicao'           => $dados['tempo_exposicao'],
                                                'minutos_tempo_exposicao'   => $dados['minutos_tempo_exposicao'],
                                                'jornada_tempo_exposicao'   => $dados['jornada_tempo_exposicao'],
                                                'descanso_tempo_exposicao'  => $dados['descanso_tempo_exposicao'],
                                                'intensidade'               => $dados['intensidade'],
                                                'resultante'                => $dados['resultante'],
                                                'dano'                      => $dados['dano'],
                                                'grau_risco'                => $dados['grau_risco'],
                                                'codigo_tipo_medicao'       => $dados['codigo_tipo_medicao'],
                                                'codigo_tecnica_medicao'    => $dados['codigo_tecnica_medicao'],
                                                'valor_maximo'              => $dados['valor_maximo'],
                                                'valor_medido'              => $dados['valor_medido'],
                                                'meio_propagacao'           => $dados['meio_propagacao'],
                                                'codigo_efeito_critico'     => isset($dados['codigo_efeito_critico']) ? $dados['codigo_efeito_critico'] : null,
                                                'codigo_risco_atributo'     => $dados['codigo_risco_atributo'],
                                                'dosimetria'                => $dados['dosimetria'],
                                                'avaliacao_instantanea'     => $dados['avaliacao_instantanea'],
                                                'descanso_tbn'              => $dados['descanso_tbn'],
                                                'descanso_tbs'              => $dados['descanso_tbs'],
                                                'descanso_tg'               => $dados['descanso_tg'],
                                                'descanso_no_local'         => $dados['descanso_no_local'],
                                                'trabalho_tbn'              => $dados['trabalho_tbn'],
                                                'trabalho_tbs'              => $dados['trabalho_tbs'],
                                                'trabalho_tg'               => $dados['trabalho_tg'],
                                                'carga_solar'               => $dados['carga_solar'],
                                                'medidas_controle'          => $dados['medidas_controle'],
                                                'medidas_controle_recomendada'  => $dados['medidas_controle_recomendada'],
                                            )
                                        );
                                        if (!$GrupoExposicaoRisco->incluir($dados_grupo_exposicao_risco)){

                                            foreach ($GrupoExposicaoRisco->validationErrors as $campo => $erro) {
                                                $erros[$key]['GrupoExposicaoRisco'] = array($campo => utf8_encode($erro));
                                            }

                                            if(isset($erros)){
                                                $GrupoExposicaoRisco->validationErrors = $erros;
                                            }

                                        } else {

                                            $codigo_grupo_exposicao_risco = $GrupoExposicaoRisco->id;

                                            //INCLUI AS FONTES GERADOAS DO RISCO
                                            if(isset($dados['GrupoExpRiscoFonteGera']) && !empty($dados['GrupoExpRiscoFonteGera'])){

                                                if(isset($dados['GrupoExpRiscoFonteGera']['x']))
                                                    unset($dados['GrupoExpRiscoFonteGera']['x']);

                                                foreach ($dados['GrupoExpRiscoFonteGera'] as $key_fonte_geradora => $dados_fontes_geradoras) {

                                                    $dados_fg = array(
                                                        'GrupoExpRiscoFonteGera' => array(
                                                            'codigo_fontes_geradoras' => $dados_fontes_geradoras['codigo_fontes_geradoras'],
                                                            'codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco
                                                        )
                                                    );

                                                    if(!$GrupoExpRiscoFonteGera->incluir($dados_fg)){

                                                        foreach ($GrupoExpRiscoFonteGera->validationErrors as $campo => $erro) {
                                                            $erros[$key]['GrupoExpRiscoFonteGera'][$key_fonte_geradora] = array($campo => $erro);
                                                        }

                                                    }
                                                }

                                                if(isset($erros)){
                                                    $GrupoExpRiscoFonteGera->validationErrors= $erros;
                                                }

                                            } //fim fontes geradoras

                                            //INCLUI OS EFEITOS CRITICOS
                                            if(isset($dados['GrupoExpEfeitoCritico']) && !empty($dados['GrupoExpEfeitoCritico'])){

                                                if(isset($dados['GrupoExpEfeitoCritico']['x']))
                                                    unset($dados['GrupoExpEfeitoCritico']['x']);

                                                foreach ($dados['GrupoExpEfeitoCritico'] as $key_efeito_critico => $dados_efeito_critico) {

                                                    $dados_fg = array(
                                                        'GrupoExpRiscoAtribDet' => array(
                                                            'codigo_riscos_atributos_detalhes'  => $dados_efeito_critico['codigo_efeito_critico'],
                                                            'codigo_grupos_exposicao_risco'     => $codigo_grupo_exposicao_risco,
                                                            'codigo_usuario_inclusao'           => $_SESSION['Auth']['Usuario']['codigo']
                                                        )
                                                    );

                                                    if(!$GrupoExpRiscoAtribDet->incluir($dados_fg)){

                                                        foreach ($GrupoExpRiscoAtribDet->validationErrors as $campo => $erro) {
                                                            $erros[$key]['GrupoExpRiscoAtribDet'][$key_efeito_critico] = array($campo => $erro);
                                                        }

                                                    }
                                                }

                                                if(isset($erros)){
                                                    $GrupoExpRiscoAtribDet->validationErrors= $erros;
                                                } //fim erros

                                            } //fim efeitos criticos 

                                            //INCLUI OS EPI'S DO RISCO
                                            if(isset($dados['GrupoExposicaoRiscoEpi']) && !empty($dados['GrupoExposicaoRiscoEpi'])){
                                                if(isset($dados['GrupoExposicaoRiscoEpi']['x']))
                                                    unset($dados['GrupoExposicaoRiscoEpi']['x']);

                                                foreach ($dados['GrupoExposicaoRiscoEpi'] as $key_epi => $epi) {
                                                    if(!isset($epi['epi_eficaz']))$epi['epi_eficaz'] = null;

                                                    $dados_epi = array(
                                                        'GrupoExposicaoRiscoEpi' => array(
                                                            'codigo_epi'                    => $epi['codigo_epi'],
                                                            'codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco,
                                                            'controle'                      => isset($epi['controle'])? $epi['controle'] : null,
                                                            'epi_eficaz'                    => $epi['epi_eficaz'],
                                                            'numero_ca'                     => $epi['numero_ca'],
                                                            'data_validade_ca'              => $epi['data_validade_ca'],
                                                            'med_protecao'                  => $epi['med_protecao'],
                                                            'cond_functo'                   => $epi['cond_functo'],
                                                            'uso_epi'                       => $epi['uso_epi'],
                                                            'prz_valid'                     => $epi['prz_valid'],
                                                            'periodic_troca'                => $epi['periodic_troca'],
                                                            'higienizacao'                  => $epi['higienizacao'],
                                                        )
                                                    );

                                                    if(!$GrupoExposicaoRiscoEpi->incluir($dados_epi)){

                                                        foreach ($GrupoExposicaoRiscoEpi->validationErrors as $campo => $erro) {
                                                            $erros[$key]['GrupoExposicaoRiscoEpi'][$key_epi] = array($campo => $erro);
                                                        }

                                                    }
                                                }

                                                if(isset($erros)){
                                                    $GrupoExposicaoRiscoEpi->validationErrors = $erros;
                                                }

                                            }

                                            //INCLUI OS EPC'S DO RISCO
                                            if(isset($dados['GrupoExposicaoRiscoEpc']) && !empty($dados['GrupoExposicaoRiscoEpc'])){
                                                if(isset($dados['GrupoExposicaoRiscoEpc']['x']))
                                                    unset($dados['GrupoExposicaoRiscoEpc']['x']);

                                                foreach ($dados['GrupoExposicaoRiscoEpc'] as $key_epc => $epc) {

                                                    $dados_epc = array(
                                                        'GrupoExposicaoRiscoEpc' => array(
                                                            'codigo_epc' => $epc['codigo_epc'],
                                                            'codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco,
                                                            'controle' => isset($epc['controle'])? $epc['controle'] : null,
                                                            'epc_eficaz' => isset($epc['epc_eficaz'])? $epc['epc_eficaz'] : null
                                                        )
                                                    );

                                                    if(!$GrupoExposicaoRiscoEpc->incluir($dados_epc)){

                                                        foreach ($GrupoExposicaoRiscoEpc->validationErrors as $campo => $erro) {
                                                            $erros[$key]['GrupoExposicaoRiscoEpc'][$key_epc] = array($campo => $erro);
                                                        }
                                                    }

                                                }

                                                if(isset($erros)){
                                                    $GrupoExposicaoRiscoEpc->validationErrors = $erros;
                                                }
                                            }                     
                                        }
                                    }
                                }
                            } else {

                                //QUANDO O RISCO NÃO É ENVIADO, AUTOMATICAMENTE O GRUPO DE EXPOSIÇÃO É ATRIBUIDO AO RISCO: AUSENCIA DE RISCO.
                                $configuracao_ausencia_risco = $Configuracao->find("first", array('conditions' => array('chave' => 'AUSENCIA_DE_RISCO')));
                                if(!empty($configuracao_ausencia_risco)){

                                    $dados_grupo_exposicao_risco = array(
                                        'GrupoExposicaoRisco' => array(
                                            'codigo_risco'              => $configuracao_ausencia_risco['Configuracao']['valor'],
                                            'codigo_grupo_exposicao'    => $codigo_grupo_exposicao,
                                            )
                                        );
                                    
                                    if (!$GrupoExposicaoRisco->incluir($dados_grupo_exposicao_risco)){
                                        foreach ($GrupoExposicaoRisco->validationErrors as $campo => $erro) {
                                            $erros[$key] = array($campo => $erro);
                                        }

                                        $GrupoExposicaoRisco->validationErrors = $erros;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $this->invalidate('descricao_tipo_setor_cargo', 'Selecione o Tipo de Grupo de Exposição');
                throw new Exception("Ocorreu um erro: descricao_tipo_setor_cargo não enviado");
            }

            if(empty($ClienteSetor->validationErrors) && empty($this->validationErrors) && empty($GrupoExposicaoRisco->validationErrors) && empty($GrupoExpRiscoFonteGera->validationErrors) && empty($GrupoExposicaoRiscoEpi->validationErrors) && empty($GrupoExposicaoRiscoEpc->validationErrors)){
                $this->commit();
                return true;
            } else {
                throw new Exception("Ocorreu um erro");
            }
        }
        catch (Exception $ex) {
            $this->rollback();
            return false;
        }

    } //fim metodo incluir

    /**
     * metodo para atualziar os dados da tela editar do ppra
     * 
     */ 
    function atualizar($data){
        
        $ClienteSetor =& ClassRegistry::Init('ClienteSetor');
        $OrdemServico =& ClassRegistry::Init('OrdemServico');
        $OrdemServicoItem =& ClassRegistry::Init('OrdemServicoItem');
        $GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
        $GrupoExposicaoRisco =& ClassRegistry::Init('GrupoExposicaoRisco');
        $GrupoExpRiscoFonteGera =& ClassRegistry::Init('GrupoExpRiscoFonteGera');
        $GrupoExposicaoRiscoEpi =& ClassRegistry::Init('GrupoExposicaoRiscoEpi');
        $GrupoExposicaoRiscoEpc =& ClassRegistry::Init('GrupoExposicaoRiscoEpc');
        $AtribuicaoGrupoExpo =& ClassRegistry::Init('AtribuicaoGrupoExpo');
        $Configuracao =& ClassRegistry::Init('Configuracao');
        $GrupoExpRiscoAtribDet =& ClassRegistry::Init('GrupoExpRiscoAtribDet');
       
        try{
            $this->query('begin transaction');

            if(isset($data['GrupoExposicao']['descricao_tipo_setor_cargo']) && !empty($data['GrupoExposicao']['descricao_tipo_setor_cargo'])){

                //Se existe ClienteSetor
                if(!empty($data['ClienteSetor']['codigo'])){

                    if($ClienteSetor->atualizar($data)) {

                        //descricao atividade grupo homogeneo
                        if(isset($data['GrupoHomDetalhe']) && !empty($data['GrupoHomDetalhe'])){
                            foreach ($data['GrupoHomDetalhe'] as $linha => $dados) {
                                if(($dados['codigo_setor_ghe'] == $data['ClienteSetor']['codigo_setor']) && ($dados['codigo_cargo_ghe'] == $data['GrupoExposicao']['codigo_cargo'])){
                                    $data['GrupoExposicao']['descricao_atividade'] = $dados['descricao_atividade_ghe'];
                                }
                            }
                        }

                        $conditions_grupo_exposicao = array(
                            'codigo_cliente_setor' => $data['ClienteSetor']['codigo'], 
                            'codigo' => $data['GrupoExposicao']['codigo']
                        );

                        $verifica_grupo_exposicao = $this->find('first', array('conditions' => $conditions_grupo_exposicao));

                        if(!empty($verifica_grupo_exposicao)){
                            //Esses dados não podem ser alterados                                                 
                            $data['GrupoExposicao']['codigo_cliente_setor'] = $verifica_grupo_exposicao['GrupoExposicao']['codigo_cliente_setor'];
                            $data['GrupoExposicao']['codigo_cargo'] = $verifica_grupo_exposicao['GrupoExposicao']['codigo_cargo'];
                            $data['GrupoExposicao']['codigo_grupo_homogeneo'] =  $verifica_grupo_exposicao['GrupoExposicao']['codigo_grupo_homogeneo'];

                            // ATUALIZA O GRUPO DE EXPOSICAO  
                            if (parent::atualizar($data)) {

                                $codigo_grupo_exposicao = $data['GrupoExposicao']['codigo'];

                                //ATUALIZO STATUS ORDEM SERVIÇO.
                                $joins  = array(
                                    array(
                                      'table' => $OrdemServicoItem->databaseTable.'.'.$OrdemServicoItem->tableSchema.'.'.$OrdemServicoItem->useTable,
                                      'alias' => 'OrdemServicoItem',
                                      'conditions' => 'OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico',
                                    )
                                );

                                $codigo_servico_ppra = $OrdemServico->getPPRAByCodigoCliente($data['ClienteSetor']['codigo_cliente_alocacao']);

                                $conditions = array('codigo_cliente' => $data['ClienteSetor']['codigo_cliente_alocacao'], 'codigo_servico' => $codigo_servico_ppra);

                                $verifica_ordem_servico = $OrdemServico->find('first', array('conditions' => $conditions,  'joins' => $joins));
                                

                                if(!empty($verifica_ordem_servico)){
                                        //ATUALIZO STATUS ORDEM SERVIÇO.
                                    if(!$OrdemServico->atualiza_status($verifica_ordem_servico['OrdemServico']['codigo'], 2, $codigo_servico_ppra)){
                                        throw new Exception("Ocorreu um erro: OrdemServico");
                                    }
                                }
                                
                                //REMOVE AS ATRIBUIÇÕES DESTE GRUPO
                                $AtribuicaoGrupoExpo->deleteAll(array('codigo_grupo_exposicao' => $codigo_grupo_exposicao), false);

                                //INCLUI AS ATRIBUICOES
                                if(!empty($data['atribuicoes'])){

                                    foreach ($data['atribuicoes'] as $atribuicao) {
                                        $atribuicoes_grupos['AtribuicaoGrupoExpo'] = array(
                                            'codigo_atribuicao' => $atribuicao,
                                            'codigo_grupo_exposicao' => $codigo_grupo_exposicao ); 


                                        if(!$AtribuicaoGrupoExpo->incluir($atribuicoes_grupos)){
                                           throw new Exception("Ocorreu um erro: AtribuicaoGrupoExpo");
                                        }

                                    }   
                                }

                                //para retirar o indice auxiliar da view
                                if(isset($data['GrupoExposicaoRisco']['k'])){
                                    unset($data['GrupoExposicaoRisco']['k']);
                                }

                                //INCLUI OS RISCOS
                                if (isset($data['GrupoExposicaoRisco'])){

                                    if(count($data['GrupoExposicaoRisco']) > 0){

                                        foreach ($data['GrupoExposicaoRisco'] as $key => $dados) {

                                            if(isset($dados['codigo_tipo_medicao'])){
                                                if($dados['codigo_tipo_medicao'] == 2){
                                                    $dados['codigo_tecnica_medicao'] = null;
                                                    $dados['valor_maximo'] = null;
                                                    $dados['valor_medido'] = null;
                                                }
                                            } else {
                                                $dados['codigo_tipo_medicao'] = null;
                                            }

                                            if(empty($dados['resultante'])) $dados['resultante'] = null;
                                            if(empty($dados['descanso_tbn'])) $dados['descanso_tbn'] = null;
                                            if(empty($dados['descanso_tbs'])) $dados['descanso_tbs'] = null;
                                            if(empty($dados['descanso_tg'])) $dados['descanso_tg'] = null;
                                            if(empty($dados['trabalho_tbn'])) $dados['trabalho_tbn'] = null;
                                            if(empty($dados['trabalho_tbs'])) $dados['trabalho_tbs'] = null;
                                            if(empty($dados['trabalho_tg'])) $dados['trabalho_tg'] = null;
                                            if(empty($dados['grau_risco'])) $dados['grau_risco'] = null;   
                                            if(empty($dados['codigo_risco_atributo'])) $dados['codigo_risco_atributo'] = null;   
                                            if(empty($dados['codigo_efeito_critico'])) $dados['codigo_efeito_critico'] = null;   
                                            if(empty($dados['meio_propagacao'])) $dados['meio_propagacao'] = null;   
                                            if(empty($dados['codigo_tecnica_medicao'])) $dados['codigo_tecnica_medicao'] = null;   
                                            if(empty($dados['codigo_tipo_medicao'])) $dados['codigo_tipo_medicao'] = null;   
                                            if(empty($dados['descanso_tempo_exposicao'])) $dados['descanso_tempo_exposicao'] = null;   
                                            if(empty($dados['valor_maximo'])) $dados['valor_maximo'] = null;   
                                            if(empty($dados['valor_medido'])) $dados['valor_medido'] = null;   
                                            if(!isset($dados['avaliacao_instantanea']) && empty($dados['avaliacao_instantanea'])) $dados['avaliacao_instantanea'] = null;
                                            if(!isset($dados['dosimetria']) && empty($dados['dosimetria'])) $dados['dosimetria'] = null;
                                            if(!isset($dados['descanso_no_local']) && empty($dados['descanso_no_local'])) $dados['descanso_no_local'] = null;
                                            if(!isset($dados['carga_solar']) && empty($dados['carga_solar'])) $dados['carga_solar'] = null;
                                            if(!isset($dados['epi_eficaz']) && empty($dados['epi_eficaz'])) $dados['epi_eficaz'] = null;
                                            if(!isset($dados['medidas_controle']) && empty($dados['medidas_controle'])) $dados['medidas_controle'] = null;
                                            if(!isset($dados['medidas_controle_recomendada']) && empty($dados['medidas_controle_recomendada'])) $dados['medidas_controle_recomendada'] = null;

                                            // debug(array("Dados=>", $dados)); die;

                                            
                                            //verifica se existe o risco aplicado na tabela grupos_exposicao_risco
                                            $ger_aux = $GrupoExposicaoRisco->find('first', array('conditions' => array('codigo_grupo_exposicao' => $codigo_grupo_exposicao, 'codigo_risco' => $dados['codigo_risco'])));
                                            //verifica se existe dados no banco com o filtro do grupo_exposicao e o risco
                                            if(!empty($ger_aux)) {
                                                $dados['codigo'] = $ger_aux['GrupoExposicaoRisco']['codigo'];
                                            }//fim verificacao ge_risco

                                            if(empty($dados['codigo'])){ //INCLUSAO DE NOVO RISCO                                                

                                                if(!empty($dados['codigo_risco'])){

                                                    $dados_grupo_exposicao_risco = array(
                                                        'GrupoExposicaoRisco' => array(
                                                            'codigo_risco'              => $dados['codigo_risco'],
                                                            'codigo_grupo_exposicao'    => $codigo_grupo_exposicao,
                                                            'tempo_exposicao'           => $dados['tempo_exposicao'],
                                                            'minutos_tempo_exposicao'   => $dados['minutos_tempo_exposicao'],
                                                            'jornada_tempo_exposicao'   => $dados['jornada_tempo_exposicao'],
                                                            'descanso_tempo_exposicao'  => $dados['descanso_tempo_exposicao'],
                                                            'intensidade'               => $dados['intensidade'],
                                                            'resultante'                => $dados['resultante'],
                                                            'dano'                      => $dados['dano'],
                                                            'grau_risco'                => $dados['grau_risco'],
                                                            'codigo_tec_med_ppra'       => $dados['codigo_tec_med_ppra'],
                                                            'codigo_tipo_medicao'       => $dados['codigo_tipo_medicao'],
                                                            'codigo_tecnica_medicao'    => $dados['codigo_tecnica_medicao'],
                                                            'valor_maximo'              => $dados['valor_maximo'],
                                                            'valor_medido'              => $dados['valor_medido'],
                                                            'codigo_efeito_critico'     => $dados['codigo_efeito_critico'],
                                                            'codigo_risco_atributo'     => $dados['codigo_risco_atributo'],
                                                            'dosimetria'                => $dados['dosimetria'],
                                                            'avaliacao_instantanea'     => $dados['avaliacao_instantanea'],
                                                            'descanso_tbn'              => $dados['descanso_tbn'],
                                                            'descanso_tbs'              => $dados['descanso_tbs'],
                                                            'descanso_tg'               => $dados['descanso_tg'],
                                                            'descanso_no_local'         => $dados['descanso_no_local'],
                                                            'trabalho_tbn'              => $dados['trabalho_tbn'],
                                                            'trabalho_tbs'              => $dados['trabalho_tbs'],
                                                            'trabalho_tg'               => $dados['trabalho_tg'],
                                                            'carga_solar'               => $dados['carga_solar'],
                                                            'medidas_controle'          => $dados['medidas_controle'],
                                                            'medidas_controle_recomendada'  => $dados['medidas_controle_recomendada'],
                                                        )
                                                    );
                                                    
                                                    if (!$GrupoExposicaoRisco->incluir($dados_grupo_exposicao_risco)){

                                                        // debug(array('aqui',$dados_grupo_exposicao_risco)); die;

                                                        foreach ($GrupoExposicaoRisco->validationErrors as $campo => $erro) {
                                                            $erros[$key] = array($campo => $erro);
                                                        }

                                                        if(isset($erros)){
                                                            $GrupoExposicaoRisco->validationErrors = $erros;
                                                        }

                                                    }
                                                    else{
                                                        // debug(array('aqui2',$dados_grupo_exposicao_risco)); die;
                                                        
                                                        $codigo_grupo_exposicao_risco = $GrupoExposicaoRisco->id;

                                                        //INCLUI AS FONTES GERADOAS DO RISCO
                                                        if(isset($dados['GrupoExpRiscoFonteGera']) && !empty($dados['GrupoExpRiscoFonteGera'])){

                                                            if(isset($dados['GrupoExpRiscoFonteGera']['x']))
                                                                unset($dados['GrupoExpRiscoFonteGera']['x']);

                                                            foreach ($dados['GrupoExpRiscoFonteGera'] as $key_fonte_geradora => $dados_fontes_geradoras) {
                                                                $dados_fg = array(
                                                                    'GrupoExpRiscoFonteGera' => array(
                                                                        'codigo_fontes_geradoras' => $dados_fontes_geradoras['codigo_fontes_geradoras'],
                                                                        'codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco
                                                                        )
                                                                    );

                                                                if(!$GrupoExpRiscoFonteGera->incluir($dados_fg)){

                                                                    foreach ($GrupoExpRiscoFonteGera->validationErrors as $campo => $erro) {
                                                                        $erros[$key]['GrupoExpRiscoFonteGera'][$key_fonte_geradora] = array($campo => $erro);
                                                                        
                                                                    }

                                                                }
                                                            }

                                                            if(isset($erros)){
                                                                $GrupoExpRiscoFonteGera->validationErrors= $erros;
                                                            }
                                                        } 

                                                        //INCLUI OS EFEITOS CRITICOS
                                                        if(isset($dados['GrupoExpEfeitoCritico']) && !empty($dados['GrupoExpEfeitoCritico'])){

                                                            if(isset($dados['GrupoExpEfeitoCritico']['x']))
                                                                unset($dados['GrupoExpEfeitoCritico']['x']);

                                                            foreach ($dados['GrupoExpEfeitoCritico'] as $key_efeito_critico => $dados_efeito_critico) {
                                                                $dados_fg = array(
                                                                    'GrupoExpRiscoAtribDet' => array(
                                                                        'codigo_riscos_atributos_detalhes'  => $dados_efeito_critico['codigo_efeito_critico'],
                                                                        'codigo_grupos_exposicao_risco'     => $codigo_grupo_exposicao_risco,
                                                                        'codigo_usuario_inclusao'           => $_SESSION['Auth']['Usuario']['codigo']
                                                                        )
                                                                    );

                                                                if(!$GrupoExpRiscoAtribDet->incluir($dados_fg)){

                                                                    foreach ($GrupoExpRiscoAtribDet->validationErrors as $campo => $erro) {
                                                                        $erros[$key]['GrupoExpRiscoAtribDet'][$key_efeito_critico] = array($campo => $erro);
                                                                    }

                                                                }
                                                            }

                                                            if(isset($erros)){
                                                                $GrupoExpRiscoAtribDet->validationErrors= $erros;
                                                            } //fim erros

                                                        } //fim efeitos criticos

                                                        //INCLUI OS EPI'S DO RISCO
                                                        if(isset($dados['GrupoExposicaoRiscoEpi']) && !empty($dados['GrupoExposicaoRiscoEpi'])){

                                                            if(isset($dados['GrupoExposicaoRiscoEpi']['x'])) {
                                                                unset($dados['GrupoExposicaoRiscoEpi']['x']);
                                                            }

                                                            // print "<pre>"; print_r($dados['GrupoExposicaoRiscoEpi']);exit;

                                                            foreach ($dados['GrupoExposicaoRiscoEpi'] as $key_epi => $epi) {

                                                                 if(!isset($epi['epi_eficaz'])) $epi['epi_eficaz'] = null;

                                                                $dados_epi = array(
                                                                    'GrupoExposicaoRiscoEpi' => array(
                                                                        'codigo_epi'                    => $epi['codigo_epi'],
                                                                        'codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco,
                                                                        'controle'                      => isset($epi['controle'])? $epi['controle'] : null,
                                                                        'epi_eficaz'                    => $epi['epi_eficaz'],
                                                                        'numero_ca'                     => $epi['numero_ca'],
                                                                        'data_validade_ca'              => $epi['data_validade_ca'],
                                                                        'atenuacao'                     => $epi['atenuacao'],
                                                                        'med_protecao'                  => $epi['med_protecao'],
                                                                        'cond_functo'                   => $epi['cond_functo'],
                                                                        'uso_epi'                       => $epi['uso_epi'],
                                                                        'prz_valid'                     => $epi['prz_valid'],
                                                                        'periodic_troca'                => $epi['periodic_troca'],
                                                                        'higienizacao'                  => $epi['higienizacao'],
                                                                        )
                                                                    );

                                                                if(!$GrupoExposicaoRiscoEpi->incluir($dados_epi)){

                                                                    foreach ($GrupoExposicaoRiscoEpi->validationErrors as $campo => $erro) {
                                                                        $erros[$key]['GrupoExposicaoRiscoEpi'][$key_epi] = array($campo => $erro);
                                                                    }
                                                                }
                                                            }

                                                            if(isset($erros)){
                                                                $GrupoExposicaoRiscoEpi->validationErrors = $erros;
                                                            }
                                                        }

                                                        //INCLUI OS EPC'S DO RISCO
                                                        if(isset($dados['GrupoExposicaoRiscoEpc']) && !empty($dados['GrupoExposicaoRiscoEpc'])){
                                                            if(isset($dados['GrupoExposicaoRiscoEpc']['x']))
                                                                unset($dados['GrupoExposicaoRiscoEpc']['x']);

                                                            foreach ($dados['GrupoExposicaoRiscoEpc'] as $key_epc => $epc) {

                                                                $dados_epc = array(
                                                                    'GrupoExposicaoRiscoEpc' => array(
                                                                        'codigo_epc' => $epc['codigo_epc'],
                                                                        'codigo_grupos_exposicao_risco' => $codigo_grupo_exposicao_risco,
                                                                        'controle' => isset($epc['controle'])? $epc['controle'] : null,
                                                                        'epc_eficaz' => isset($epc['epc_eficaz'])? $epc['epc_eficaz'] : null
                                                                        )
                                                                    );

                                                                if(!$GrupoExposicaoRiscoEpc->incluir($dados_epc)){
                                                                    foreach ($GrupoExposicaoRiscoEpc->validationErrors as $campo => $erro) {
                                                                        $erros[$key]['GrupoExposicaoRiscoEpc'][$key_epc] = array($campo => $erro);
                                                                    }
                                                                    
                                                                }
                                                            }

                                                            if(isset($erros)){
                                                                $GrupoExposicaoRiscoEpc->validationErrors = $erros;
                                                            }
                                                        }                                
                                                    }
                                                }
                                                // debug(array("Dados=>", $dados)); die;
                                            } //fim codigo vazio
                                            else {
                                                //ATUALIZAR RISCOS JA CADASTRADOS.
                                                $conditions_grupo_exposicao_risco = array(
                                                    'codigo_grupo_exposicao' => $data['GrupoExposicao']['codigo'],
                                                    'codigo' => $dados['codigo']
                                                );

                                                $verifica_grupo_exposicao_risco = $GrupoExposicaoRisco->find('first', array('conditions' => $conditions_grupo_exposicao_risco));
                        
                                                if(!empty($verifica_grupo_exposicao_risco)){
                                                    $dados['codigo'] = $verifica_grupo_exposicao_risco['GrupoExposicaoRisco']['codigo'];
                                                    $dados['codigo_grupo_exposicao'] = $verifica_grupo_exposicao_risco['GrupoExposicaoRisco']['codigo_grupo_exposicao'];

                                                    unset($dados['meio_exposicao']);

                                                    $dados_grupo_exposicao_risco['GrupoExposicaoRisco'] = $dados;

                                                    if (!$GrupoExposicaoRisco->atualizar($dados_grupo_exposicao_risco)) {


                                                        foreach ($GrupoExposicaoRisco->validationErrors as $campo => $erro) {
                                                            $erros[$key] = array($campo => $erro);
                                                        }

                                                        $GrupoExposicaoRisco->validationErrors = $erros;
                                                    }

                                                    if($GrupoExpRiscoFonteGera->deleteAll(array('GrupoExpRiscoFonteGera.codigo_grupos_exposicao_risco' => $dados['codigo']), false)){
                                                        if(isset($dados['GrupoExpRiscoFonteGera']) && !empty($dados['GrupoExpRiscoFonteGera'])){

                                                            if(isset($dados['GrupoExpRiscoFonteGera']['x']))
                                                                unset($dados['GrupoExpRiscoFonteGera']['x']);

                                                            foreach ($dados['GrupoExpRiscoFonteGera'] as $key_fonte_geradora => $dados_fontes_geradoras) {

                                                                $dados_fg = array(
                                                                    'GrupoExpRiscoFonteGera' => array(
                                                                        'codigo_fontes_geradoras' => $dados_fontes_geradoras['codigo_fontes_geradoras'],
                                                                        'codigo_grupos_exposicao_risco' => $dados['codigo']
                                                                        )
                                                                    );

                                                                if(!$GrupoExpRiscoFonteGera->incluir($dados_fg)){

                                                                    foreach ($GrupoExpRiscoFonteGera->validationErrors as $campo => $erro) {
                                                                        $erros[$key]['GrupoExpRiscoFonteGera'][$key_fonte_geradora] = array($campo => $erro);
                                                                    }
                                                                }
                                                            }
                                                            if(isset($erros)){
                                                                $GrupoExpRiscoFonteGera->validationErrors= $erros;
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        foreach ($GrupoExpRiscoFonteGera->validationErrors as $campo => $erro) {
                                                            $erros[$key]['GrupoExpRiscoFonteGera'][$key_fonte_geradora] = array($campo => $erro);
                                                        }
                                                        if(isset($erros)){
                                                            $GrupoExpRiscoFonteGera->validationErrors= $erros;
                                                        }
                                                    }

                                                    //EFEITO CRITICO
                                                    if($GrupoExpRiscoAtribDet->deleteAll(array('GrupoExpRiscoAtribDet.codigo_grupos_exposicao_risco' => $dados['codigo']), false)){
                                                        if(isset($dados['GrupoExpEfeitoCritico']) && !empty($dados['GrupoExpEfeitoCritico'])){

                                                            if(isset($dados['GrupoExpEfeitoCritico']['x']))
                                                                unset($dados['GrupoExpEfeitoCritico']['x']);

                                                            foreach ($dados['GrupoExpEfeitoCritico'] as $key_efeito_critico => $dados_efeito_critico) {

                                                                $dados_fg = array(
                                                                    'GrupoExpRiscoAtribDet' => array(
                                                                        'codigo_riscos_atributos_detalhes'  => $dados_efeito_critico['codigo_efeito_critico'],
                                                                        'codigo_grupos_exposicao_risco'     => $dados['codigo'],
                                                                        'codigo_usuario_inclusao'           => $_SESSION['Auth']['Usuario']['codigo']
                                                                        )
                                                                    );

                                                                if(!$GrupoExpRiscoAtribDet->incluir($dados_fg)){

                                                                    foreach ($GrupoExpRiscoAtribDet->validationErrors as $campo => $erro) {
                                                                        $erros[$key]['GrupoExpRiscoAtribDet'][$key_efeito_critico] = array($campo => $erro);
                                                                    }
                                                                }
                                                            }
                                                            if(isset($erros)){
                                                                $GrupoExpRiscoAtribDet->validationErrors= $erros;
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        foreach ($GrupoExpRiscoAtribDet->validationErrors as $campo => $erro) {
                                                            $erros[$key]['GrupoExpRiscoAtribDet'][$key_efeito_critico] = array($campo => $erro);
                                                        }
                                                        if(isset($erros)){
                                                            $GrupoExpRiscoAtribDet->validationErrors= $erros;
                                                        }
                                                    } //fim efeito critico

                                                    if($GrupoExposicaoRiscoEpi->deleteAll(array('GrupoExposicaoRiscoEpi.codigo_grupos_exposicao_risco' => $dados['codigo']), false)){
                                                        if(isset($dados['GrupoExposicaoRiscoEpi']) && !empty($dados['GrupoExposicaoRiscoEpi'])){

                                                            if(isset($dados['GrupoExposicaoRiscoEpi']['x'])) {
                                                                unset($dados['GrupoExposicaoRiscoEpi']['x']);
                                                            }

                                                            // pr($dados['GrupoExposicaoRiscoEpi']);

                                                            foreach ($dados['GrupoExposicaoRiscoEpi'] as $key_epi => $epi) {

                                                                if(!isset($epi['epi_eficaz'])) $epi['epi_eficaz'] = null;
                                                                $dados_epi = array(
                                                                    'GrupoExposicaoRiscoEpi' => array(
                                                                        'codigo_epi'                    => $epi['codigo_epi'],
                                                                        'codigo_grupos_exposicao_risco' => $dados['codigo'],
                                                                        'controle'                      => isset($epi['controle'])? $epi['controle'] : null,
                                                                        'epi_eficaz'                    => $epi['epi_eficaz'],
                                                                        'numero_ca'                     => $epi['numero_ca'],
                                                                        'data_validade_ca'              => $epi['data_validade_ca'],
                                                                        'atenuacao'                     => $epi['atenuacao'],
                                                                        'med_protecao'                  => $epi['med_protecao'],
                                                                        'cond_functo'                   => $epi['cond_functo'],
                                                                        'uso_epi'                       => $epi['uso_epi'],
                                                                        'prz_valid'                     => $epi['prz_valid'],
                                                                        'periodic_troca'                => $epi['periodic_troca'],
                                                                        'higienizacao'                  => $epi['higienizacao'],
                                                                        )
                                                                    );

                                                                if(!$GrupoExposicaoRiscoEpi->incluir($dados_epi)){
                                                                    foreach ($GrupoExposicaoRiscoEpi->validationErrors as $campo => $erro) {
                                                                        $erros[$key]['GrupoExposicaoRiscoEpi'][$key_epi] = array($campo => $erro);
                                                                    }

                                                                }

                                                            }
                                                            if(isset($erros)){
                                                                $GrupoExposicaoRiscoEpi->validationErrors= $erros;
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        foreach ($GrupoExposicaoRiscoEpi->validationErrors as $campo => $erro) {
                                                            $erros[$key]['GrupoExposicaoRiscoEpi'][$key_epi] = array($campo => $erro);
                                                        }

                                                        if(isset($erros)){
                                                            $GrupoExposicaoRiscoEpi->validationErrors= $erros;
                                                        }
                                                    }

                                                    if($GrupoExposicaoRiscoEpc->deleteAll(array('GrupoExposicaoRiscoEpc.codigo_grupos_exposicao_risco' => $dados['codigo']), false)){
                                                        if(isset($dados['GrupoExposicaoRiscoEpc']) && !empty($dados['GrupoExposicaoRiscoEpc'])){
                                                            if(isset($dados['GrupoExposicaoRiscoEpc']['x']))
                                                                unset($dados['GrupoExposicaoRiscoEpc']['x']);

                                                            foreach ($dados['GrupoExposicaoRiscoEpc'] as $key_epc => $epc) {

                                                                $dados_epc = array(
                                                                    'GrupoExposicaoRiscoEpc' => array(
                                                                        'codigo_epc' => $epc['codigo_epc'],
                                                                        'codigo_grupos_exposicao_risco' => $dados['codigo'],
                                                                        'controle' => isset($epc['controle'])? $epc['controle'] : null,
                                                                        'epc_eficaz' => isset($epc['epc_eficaz'])? $epc['epc_eficaz'] : null
                                                                        )
                                                                    );

                                                                if(!$GrupoExposicaoRiscoEpc->incluir($dados_epc)){
                                                                    foreach ($GrupoExposicaoRiscoEpc->validationErrors as $campo => $erro) {
                                                                        $erros[$key]['GrupoExposicaoRiscoEpc'][$key_epc] = array($campo => $erro);
                                                                    }

                                                                } 
                                                            }
                                                            if(isset($erros)){
                                                                $GrupoExposicaoRiscoEpc->validationErrors= $erros;
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        foreach ($GrupoExposicaoRiscoEpc->validationErrors as $campo => $erro) {
                                                            $erros[$key]['GrupoExposicaoRiscoEpc'][$key_epc] = array($campo => $erro);
                                                        }

                                                        if(isset($erros)){
                                                            $GrupoExposicaoRiscoEpc->validationErrors= $erros;
                                                        }
                                                    }                                                
                                                }//grupo risco existe
                                            }//if atualizar
                                                
                                        }//foreach
                                        
                                    }//enviado GrupoExposicaoRisco
                                    else{
                                           
                                        //QUANDO O RISCO NÃO É ENVIADO, AUTOMATICAMENTE O GRUPO DE EXPOSIÇÃO É ATRIBUIDO AO RISCO: AUSENCIA DE RISCO.
                                        $configuracao_ausencia_risco = $Configuracao->find("first", array('conditions' => array('chave' => 'AUSENCIA_DE_RISCO')));
                                        if(!empty($configuracao_ausencia_risco)){

                                            $dados_grupo_exposicao_risco = array(
                                                'GrupoExposicaoRisco' => array(
                                                    'codigo_risco'              => $configuracao_ausencia_risco['Configuracao']['valor'],
                                                    'codigo_grupo_exposicao'    => $codigo_grupo_exposicao,
                                                )
                                            );
                                                
                                            if (!$GrupoExposicaoRisco->incluir($dados_grupo_exposicao_risco)){
                                                foreach ($GrupoExposicaoRisco->validationErrors as $campo => $erro) {
                                                    $erros[$key] = array($campo => $erro);
                                                }

                                                $GrupoExposicaoRisco->validationErrors = $erros;
                                            }
                                        }
                                    }
                                }
                            } else {

                                foreach ($this->validationErrors as $campo => $erro) {
                                    $erros[]['GrupoExposicao'][] = array($campo => $erro);
                                }

                                if(isset($erros)){
                                    $this->validationErrors= $erros;
                                }
                            }
                        } else {
                            $this->invalidate('descricao_tipo_setor_cargo', 'Grupo de Exposicao não encontrado');
                        }
                    } else {
                        foreach ($ClienteSetor->validationErrors as $campo => $erro) {
                            $erros[$key]['ClienteSetor'][$key_epc] = array($campo => $erro);
                        }

                        if(isset($erros)){
                            $ClienteSetor->validationErrors= $erros;
                        }
                    }
                } else {
                    $ClienteSetor->invalidate('descricao_tipo_setor_cargo', 'Cliente Setor não encontrado');       
                }
            } else {
                $this->invalidate('descricao_tipo_setor_cargo', 'Selecione o Tipo de Grupo de Exposição');
            }
            
            // debug($ClienteSetor->validationErrors);
            // debug($this->validationErrors);
            // debug($GrupoExposicaoRisco->validationErrors);
            // debug($GrupoExpRiscoFonteGera->validationErrors);
            // debug($GrupoExpRiscoAtribDet->validationErrors);
            // debug($GrupoExposicaoRiscoEpi->validationErrors);
            // debug($GrupoExposicaoRiscoEpc->validationErrors);

            if(empty($ClienteSetor->validationErrors) && empty($this->validationErrors) && empty($GrupoExposicaoRisco->validationErrors) && empty($GrupoExpRiscoFonteGera->validationErrors) && empty($GrupoExpRiscoAtribDet->validationErrors) && empty($GrupoExposicaoRiscoEpi->validationErrors) && empty($GrupoExposicaoRiscoEpc->validationErrors)){
                $this->commit();
                return true;
            } else {

                //mensagem erro
                $erro = array(
                    'cliente_setor' => $ClienteSetor->validationErrors,
                    'grupo_exp' => $this->validationErrors,
                    'grupo_exp_risco' => $GrupoExposicaoRisco->validationErrors,
                    'grupo_exp_risco_fonte_geradora' => $GrupoExpRiscoFonteGera->validationErrors,
                    'grupo_exp_risco_atrib_deta' => $GrupoExpRiscoAtribDet->validationErrors,
                    'grupo_exp_risco_epi' => $GrupoExposicaoRiscoEpi->validationErrors,
                    'grupo_exp_risco_epc' => $GrupoExposicaoRiscoEpc->validationErrors
                );
                throw new Exception("Ocorreu um erro: " . print_r($erro,1));
            }
        } 
        catch (Exception $ex) {

            $this->log($ex->getMessage(),'debug');

            $this->rollback();
            return false;
        }

    } // fim metodo atualizar

    function retorna_dados_grupo_exposicao_ghe($codigo_cliente, $codigo_grupo_homogeneo){
        $ClienteSetor =& ClassRegistry::Init('ClienteSetor');

        $joins  = array(
            array(
              'table' => $ClienteSetor->databaseTable.'.'.$ClienteSetor->tableSchema.'.'.$ClienteSetor->useTable,
              'alias' => 'ClienteSetor',
              'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
              )                );

        $conditions = array('codigo_cliente_setor' => $codigo_cliente, 'codigo_grupo_homogeneo' => $codigo_grupo_homogeneo);

        $fields  = array(
            'GrupoExposicao.codigo', 'GrupoExposicao.codigo_cargo', 'GrupoExposicao.descricao_atividade', 'GrupoExposicao.data_documento', 'GrupoExposicao.observacao', 'GrupoExposicao.codigo_cliente_setor', 'GrupoExposicao.codigo_grupo_homogeneo',        
            'ClienteSetor.codigo', 'ClienteSetor.codigo_cliente', 'ClienteSetor.codigo_cliente_alocacao', 'ClienteSetor.codigo_setor', 'ClienteSetor.data_inclusao', 'ClienteSetor.codigo_usuario_inclusao', 'ClienteSetor.codigo_empresa', 'ClienteSetor.pe_direito', 'ClienteSetor.cobertura', 'ClienteSetor.iluminacao', 'ClienteSetor.ventilacao', 'ClienteSetor.piso', 'ClienteSetor.estrutura'
            );
        $dados = $this->find('all', array('conditions' => $conditions,  'joins' => $joins, 'fields' => $fields));

        return $dados;
    }

    function excluir($codigo){
        $GrupoExposicaoRisco =& ClassRegistry::Init('GrupoExposicaoRisco');
        $GrupoExpRiscoFonteGera =& ClassRegistry::Init('GrupoExpRiscoFonteGera');
        $GrupoExposicaoRiscoEpi =& ClassRegistry::Init('GrupoExposicaoRiscoEpi');
        $GrupoExposicaoRiscoEpc =& ClassRegistry::Init('GrupoExposicaoRiscoEpc');
        $GrupoExpRiscoAtribDet =& ClassRegistry::Init('GrupoExpRiscoAtribDet');

        //infortipos atribuicoes
        $AtribuicaoGrupoExpo =& ClassRegistry::Init('AtribuicaoGrupoExpo');

        try{  
            $this->query('begin transaction');

            $grupo_exposicao_risco = $GrupoExposicaoRisco->find("all", array('conditions' => array('codigo_grupo_exposicao' => $codigo)));

            if(!empty($grupo_exposicao_risco)){
                foreach ($grupo_exposicao_risco as $key => $dados) {
                    $codigo_grupos_exposicao_risco = $dados['GrupoExposicaoRisco']['codigo'];

                    $fonte_geradora = $GrupoExpRiscoFonteGera->find("all", array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco)));
                    if(!empty($fonte_geradora)){
                        if(!$GrupoExpRiscoFonteGera->deleteAll(array('GrupoExpRiscoFonteGera.codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco), false)){
                            throw new Exception("Ocorreu um erro: GrupoExpRiscoFonteGera");
                        }
                    }

                    $epi = $GrupoExposicaoRiscoEpi->find("all", array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco)));
                    if(!empty($epi)){
                        if(!$GrupoExposicaoRiscoEpi->deleteAll(array('GrupoExposicaoRiscoEpi.codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco), false)){
                            throw new Exception("Ocorreu um erro: GrupoExposicaoRiscoEpi");
                        }
                    }

                    $epc = $GrupoExposicaoRiscoEpc->find("all", array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco)));
                    if(!empty($epc)){
                        if(!$GrupoExposicaoRiscoEpc->deleteAll(array('GrupoExposicaoRiscoEpc.codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco), false)){
                            throw new Exception("Ocorreu um erro: GrupoExposicaoRiscoEpc");
                        }
                    }

                    $atributos_detalhes = $GrupoExpRiscoAtribDet->find("all", array('conditions' => array('codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco)));
                    if(!empty($atributos_detalhes)){
                        if(!$GrupoExpRiscoAtribDet->deleteAll(array('GrupoExpRiscoAtribDet.codigo_grupos_exposicao_risco' => $codigo_grupos_exposicao_risco), false)){
                            throw new Exception("Ocorreu um erro: GrupoExpRiscoAtribDet");
                        }
                    }

                    if(!$GrupoExposicaoRisco->excluir($codigo_grupos_exposicao_risco)){
                        throw new Exception("Ocorreu um erro: GrupoExposicaoRisco");
                    }
                }
            }

            //pega os infotipos ou atribuições para deletar do grupo de exposição
            $infoTipos = $AtribuicaoGrupoExpo->find('first', array('conditions' => array('codigo_grupo_exposicao' => $codigo)));

            //verifica se existe atribuições
            if(!empty($infoTipos)) {
                //tenta deletar os infotipos
                if(!$AtribuicaoGrupoExpo->deleteAll(array('AtribuicaoGrupoExpo.codigo_grupo_exposicao' => $codigo))) {                    
                    throw new Exception("Ocorreu um erro: AtribuicaoGrupoExpo");    
                }//fim excluir

            }//fim if infotipos

            if(!parent::excluir($codigo)){
                throw new Exception("Ocorreu um erro: GrupoExposicao");
            }

            $this->commit();
            return true;
        } 
        catch (Exception $ex) {
            $this->log("ERRO EXCLUIR GRUPO EXPOSICAO:" . $ex->getMessage(),'debug');
            $this->rollback();
            return false;
        }
    }

    function retornaDescricaoGrupoHomogeneo($codigo){
        $ClienteSetor =& ClassRegistry::Init('ClienteSetor');
        $GrupoHomogeneo =& ClassRegistry::Init('GrupoHomogeneo');
        $GrupoHomDetalhe =& ClassRegistry::Init('GrupoHomDetalhe');
        $Setor =& ClassRegistry::Init('Setor');
        $Cargo =& ClassRegistry::Init('Cargo');

        $joins  = array(
            array(
                'table' => $ClienteSetor->databaseTable.'.'.$ClienteSetor->tableSchema.'.'.$ClienteSetor->useTable,
                'alias' => 'ClienteSetor',
                'type' => 'LEFT',
                'conditions' => 'GrupoExposicao.codigo_cliente_setor = ClienteSetor.codigo',
            ),
            array(
                'table' => $GrupoHomogeneo->databaseTable.'.'.$GrupoHomogeneo->tableSchema.'.'.$GrupoHomogeneo->useTable,
                'alias' => 'GrupoHomogeneo',
                'type' => 'LEFT OUTER',
                'conditions' => 'GrupoExposicao.codigo_grupo_homogeneo = GrupoHomogeneo.codigo',
            ),
            array(
                'table' => $GrupoHomDetalhe->databaseTable.'.'.$GrupoHomDetalhe->tableSchema.'.'.$GrupoHomDetalhe->useTable,
                'alias' => 'GrupoHomDetalhe',
                'type' => 'LEFT',
                'conditions' => 'GrupoHomDetalhe.codigo_grupo_homogeneo = GrupoHomogeneo.codigo AND GrupoExposicao.codigo_cargo = GrupoHomDetalhe.codigo_cargo AND ClienteSetor.codigo_setor = GrupoHomDetalhe.codigo_setor',
            ),
            array(
                'table' => $Setor->databaseTable.'.'.$Setor->tableSchema.'.'.$Setor->useTable,
                'alias' => 'Setor',
                'type' => 'LEFT',
                'conditions' => 'GrupoHomDetalhe.codigo_setor = Setor.codigo',
            ),
            array(
                'table' => $Cargo->databaseTable.'.'.$Cargo->tableSchema.'.'.$Cargo->useTable,
                'alias' => 'Cargo',
                'type' => 'LEFT',
                'conditions' => 'GrupoHomDetalhe.codigo_cargo = Cargo.codigo',
            )
        );

        $conditions = array('GrupoHomogeneo.ativo' => 1, 'GrupoHomogeneo.codigo' => $codigo);

        $fields = array(
            'GrupoHomogeneo.codigo', 'GrupoHomogeneo.descricao', 'GrupoHomogeneo.codigo_cliente',
            'GrupoHomDetalhe.codigo', 'GrupoHomDetalhe.codigo_grupo_homogeneo', 'GrupoHomDetalhe.codigo_setor', 'GrupoHomDetalhe.codigo_cargo',
            'Setor.codigo', 'Setor.descricao',
            'Cargo.codigo', 'Cargo.descricao',
            'GrupoExposicao.codigo', 'GrupoExposicao.codigo_grupo_homogeneo', 'GrupoExposicao.codigo_cliente_setor', 'GrupoExposicao.descricao_atividade'
        );

        $order = array('Setor.descricao ASC, Cargo.descricao ASC');
        $dados = $this->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields, 'order' => $order));

        return $dados;
    }

    function validaGrupoExposicao() {
        $ClienteSetor =& ClassRegistry::Init('ClienteSetor');

        $conditions = array(
            'codigo_cliente' => $this->data['ClienteSetor']['codigo_cliente_alocacao'],
            'codigo_setor' => $this->data['ClienteSetor']['codigo_setor'],
            'codigo_cargo' => $this->data['GrupoExposicao']['codigo_cargo'],
            'codigo_funcionario' => (isset($this->data['GrupoExposicao']['codigo_funcionario']) && !empty($this->data['GrupoExposicao']['codigo_funcionario'])) ? $this->data['GrupoExposicao']['codigo_funcionario'] : null
        );

        if($this->data['GrupoExposicao']['descricao_tipo_setor_cargo'] == 1){
            //Individual

            //Codigo do Grupo Homogeneo tem que ser nulo.
            if(isset($this->data['GrupoExposicao']['codigo_grupo_homogeneo']) && empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])){
                $conditions_ghe = array('codigo_grupo_homogeneo IS NULL');
                $conditions = array_merge($conditions, $conditions_ghe);
            }

            //PPRA Individual Funcionario.
            if(isset($this->data['GrupoExposicao']['codigo_funcionario']) && !empty($this->data['GrupoExposicao']['codigo_funcionario'])){
                $conditions_funcionario = array('GrupoExposicao.codigo_funcionario' => $this->data['GrupoExposicao']['codigo_funcionario']);
                $conditions = array_merge($conditions, $conditions_funcionario);
            } else {
                $conditions_funcionario = array('GrupoExposicao.codigo_funcionario IS NULL');
                $conditions = array_merge($conditions, $conditions_funcionario);
            }

        } else {
            //GHE

            //Codigo do Grupo Homogeneo não pode ser nulo.
            if(isset($this->data['GrupoExposicao']['codigo_grupo_homogeneo']) && !empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])){
                $conditions_ghe = array('codigo_grupo_homogeneo' =>  $this->data['GrupoExposicao']['codigo_grupo_homogeneo']);
                $conditions = array_merge($conditions, $conditions_ghe);
            }
        }

        $joins  = array(
            array(
                'table' => $ClienteSetor->databaseTable.'.'.$ClienteSetor->tableSchema.'.'.$ClienteSetor->useTable,
                'alias' => 'ClienteSetor',
                'type' => 'LEFT',
                'conditions' => 'GrupoExposicao.codigo_cliente_setor = ClienteSetor.codigo',
            ),
        );

        $fields = array(
            'GrupoExposicao.codigo', 'GrupoExposicao.codigo_cargo', 'GrupoExposicao.codigo_cliente_setor', 'GrupoExposicao.codigo_grupo_homogeneo', 'GrupoExposicao.codigo_funcionario', 
            'ClienteSetor.codigo','ClienteSetor.codigo_cliente','ClienteSetor.codigo_cliente_alocacao','ClienteSetor.codigo_setor'
        );

        $validar = $this->find('first', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));

        if(empty($validar)){
            return true;
        }
        else{
            return false;
        }       
    }

    function grupo_exposicao_importacao($dados, $data){
        $GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
        $OrdemServicoItem =& ClassRegistry::Init('OrdemServicoItem');
        $OrdemServico =& ClassRegistry::Init('OrdemServico');
        
        $retorno = array();
        // As validações foram retiradas, agora que a importação usa o $this->validate da model. O $this->data não será mais utilizado para as validações.
        // $this->data = array_merge($data, $dados);
        // $this->data['GrupoExposicao']['descricao_tipo_setor_cargo'] = $data['DadoArquivo']['tipo_ppra'];
        // $this->validaGrupoExposicao($this->data);
        $dados['ClienteSetor'] = $data['ClienteSetor'];
        $dados['GrupoExposicao']['descricao_tipo_setor_cargo'] = $data['DadoArquivo']['tipo_ppra'];
        

        // if($dados['GrupoExposicao']['codigo_cargo'] == ""){
        //     $this->validationErrors = array('codigo_cargo' => 'Informe o Cargo!');
        // }
        // if($dados['GrupoExposicao']['codigo_cliente_setor'] == ""){
        //     $this->validationErrors = array('codigo_cliente_setor' => 'Informe o Setor!');
        // }

        if(empty($this->validationErrors)){

            if (!isset($dados['GrupoExposicao']['codigo']) && empty($dados['GrupoExposicao']['codigo'])) {

                if(!parent::incluir($dados)){
                    $erro = '';
                    foreach ($this->validationErrors as $key => $value) {
                        $erro .= utf8_decode($value).'|';
                        $this->validationErrors[$key] = $erro;
                    }
                    $retorno['Erro']['GrupoExposicao'] = $this->validationErrors;
                }
            } else {
                if(!parent::atualizar($dados)){
                    $erro = '';
                    foreach ($this->validationErrors as $key => $value) {
                        $erro .= utf8_decode($value).'|';
                        $this->validationErrors[$key] = $erro;
                    }
                    $retorno['Erro']['GrupoExposicao'] = $this->validationErrors;
                } else {
                    $codigo_grupo_exposicao = $this->id;
                    
                    $joins  = array(
                        array(
                            'table' => $OrdemServicoItem->databaseTable.'.'.$OrdemServicoItem->tableSchema.'.'.$OrdemServicoItem->useTable,
                            'alias' => 'OrdemServicoItem',
                            'conditions' => 'OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico',
                        )
                    );

                    $conditions = array('codigo_cliente' => $data['ClienteSetor']['codigo_cliente_alocacao']);

                    $codigo_servico_ppra = $OrdemServico->getPPRAByCodigoCliente($data['ClienteSetor']['codigo_cliente_alocacao']);

                    $verifica_ordem_servico = $OrdemServico->find('first', array('conditions' => $conditions,  'joins' => $joins));                    
                    
                    //JA POSSUI PPRA. PREENCHER SOMENTE O FORMULARIO.
                    if(empty($verifica_ordem_servico)){
                        $matriz = $GrupoEconomicoCliente->retorna_dados_cliente($data['ClienteSetor']['codigo_cliente_alocacao']);
                        $dados = array('OrdemServico'=>
                            array(
                                'codigo_grupo_economico' => $matriz['GrupoEconomicoCliente']['codigo_grupo_economico'],
                                'codigo_cliente' => $data['ClienteSetor']['codigo_cliente_alocacao'],
                                'codigo_fornecedor' => 0,
                                'status_ordem_servico' => 3
                                )
                            );                        
                        
                        if($OrdemServico->incluir($dados)){
                            $codigo_ordem_servico = $OrdemServico->id;
                            
                            $dados_item = array('OrdemServicoItem' => 
                                array(
                                    'codigo_ordem_servico' => $codigo_ordem_servico,
                                    'codigo_servico' => $codigo_servico_ppra
                                    )
                                );
                            
                            if(!$OrdemServicoItem->incluir($dados_item)) {
                                $retorno['Erro']['OrdemServico'] = array('codigo_ordem_servico' => 'Ordem Serviço não encontrada!');
                            }                            
                        } else {                            
                            $retorno['Erro']['OrdemServico'] = array('codigo_ordem_servico' => 'Ordem Serviço não encontrada!');
                            
                        }
                    }
                }
            }

            if(!empty($this->id)){
                $consulta_dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));            
                if(empty($consulta_dados)){
                    $retorno['Erro']['GrupoExposicao'] = array('codigo_grupo_exposicao' => 'Grupo de GrupoExposicao não encontrado!');
                }
                else{
                    $retorno['Dados'] = $consulta_dados;
                }
            }
        } else {
            $retorno['Erro']['GrupoExposicao'] = $this->validationErrors;
        }
            
        return $retorno;
    }

    function retorna_grupo_exposicao_importacao($dados_unidade, $dados_setor, $dados_cargo, $dados_funcionario = null){

        $this->ClienteSetor =& ClassRegistry::Init('ClienteSetor');

        $conditions = array(
            'ClienteSetor.codigo_cliente_alocacao' => $dados_unidade['Cliente']['codigo'],
            'ClienteSetor.codigo_setor' => $dados_setor['Setor']['codigo'],
            'GrupoExposicao.codigo_cargo' => $dados_cargo['Cargo']['codigo'],
        );

        $joins = array(
            array(
              'table' => $this->ClienteSetor->databaseTable.'.'.$this->ClienteSetor->tableSchema.'.'.$this->ClienteSetor->useTable, 
              'alias' => 'ClienteSetor',
              'type' => 'LEFT',
              'conditions' => 'GrupoExposicao.codigo_cliente_setor = ClienteSetor.codigo',
            ),
        );

        $consulta_grupo_exposicao = $this->find('first',  compact('conditions', 'joins'));


        if(empty($consulta_grupo_exposicao)){
            $retorno['Erro']['GrupoExposicao'] = array('codigo_grupo_exposicao' => utf8_decode('Grupo de Exposição não encontrado!!'));
        } else {
            $retorno['Dados'] = $consulta_grupo_exposicao;
        }

        return $retorno;
    }

    public function preenche_com_ausencia_risco($codigo_cliente = null, $usuario = null, $hierarquia = false){

        $ausencia_risco = $this->query('SELECT TOP 1 COUNT(codigo) as count FROM riscos WHERE ausencia_de_risco = 1 AND ativo = 1 AND codigo_empresa = '.$usuario['Usuario']['codigo_empresa']);
        if(!$ausencia_risco[0][0]['count']) {
            return array('erro' => true, 'mensagem' => 'Ausência de risco não definido no sistema.');
        }

        if(is_null($codigo_cliente) || is_null($usuario))
        {
            return array('erro' => true, 'mensagem' => 'Cliente ou usuário inválido');
        }
        set_time_limit(0);

        $query = "  DECLARE @codigo_setor           INT, 
                            @codigo_cargo           INT, 
                            @codigo_empresa         INT, 
                            @codigo_grupo_exposicao INT, 
                            @codigo_cliente_setor   INT, 
                            @count_riscos           INT, 
                            @codigo_cliente         INT, 
                            @cod_cli_set_atual      INT, 
                            @cod_risco_ausente      INT; 
                    DECLARE @novo_codigo_grupo_exposicao TABLE 
                      ( 
                         codigo INT 
                      ); 
                    DECLARE @novo_codigo_cliente_setor TABLE 
                      ( 
                         codigo INT 
                      ); 

                    -- OBTEM O CODIGO DO RISCO AUSENCIA DE RISCO 
                    SET @cod_risco_ausente = (SELECT TOP 1 codigo 
                                              FROM   riscos 
                                              WHERE  ausencia_de_risco = 1 
                                                    AND ativo = 1
                                                    AND codigo_empresa = ".$usuario['Usuario']['codigo_empresa']."); 

                    -- CRIA O CURSOR COM TODOS OS DEPARTAMENTOS QUE NAO POSSUEM RISCO ESPECIFICADO 
                    ";

        if( $hierarquia ){

            $query .= " DECLARE cur_resultado CURSOR FOR   
                        SELECT csc.codigo_setor, 
                                 csc.codigo_cargo, 
                                 csc.codigo_empresa, 
                                 ge.codigo                                   codigo_grupo_exposicao, 
                                 cs.codigo                                   codigo_cliente_setor, 
                                 (SELECT Count(codigo) quant 
                                  FROM   grupos_exposicao_risco 
                                  WHERE  codigo_grupo_exposicao = ge.codigo) count_riscos, 
                                 csc.codigo_cliente_alocacao                 AS codigo_cliente  

                        FROM    clientes_setores_cargos csc 
                                 LEFT JOIN clientes_setores cs 
                                        ON( cs.codigo_cliente_alocacao = csc.codigo_cliente_alocacao 
                                            AND cs.codigo_setor = csc.codigo_setor ) 
                                 LEFT JOIN grupo_exposicao ge 
                                        ON( ge.codigo_cargo = csc.codigo_cargo 
                                            AND ge.codigo_cliente_setor = cs.codigo ) 
                          WHERE  csc.codigo_cliente_alocacao = ".$codigo_cliente." 
                                 AND (SELECT Count(codigo) quant 
                                      FROM   grupos_exposicao_risco 
                                      WHERE  codigo_grupo_exposicao = ge.codigo) = 0 ";
                if(!empty($usuario['Usuario']['codigo_empresa'])) {
                    $query .= " AND csc.codigo_empresa = ".$usuario['Usuario']['codigo_empresa'];
                }
                $query .= " GROUP BY csc.codigo_setor, csc.codigo_cargo, csc.codigo_empresa, ge.codigo, cs.codigo, csc.codigo_cliente_alocacao; "; 

        } else {
            $query .= "   DECLARE cur_resultado CURSOR FOR 
                          SELECT fsc.codigo_setor, 
                                 fsc.codigo_cargo, 
                                 cf.codigo_empresa, 
                                 ge.codigo                                   codigo_grupo_exposicao, 
                                 cs.codigo                                   codigo_cliente_setor, 
                                 (SELECT Count(codigo) quant 
                                  FROM   grupos_exposicao_risco 
                                  WHERE  codigo_grupo_exposicao = ge.codigo) count_riscos, 
                                 fsc.codigo_cliente_alocacao                 AS codigo_cliente 
                          
                          FROM   funcionario_setores_cargos fsc 
                                 LEFT JOIN cliente_funcionario cf 
                                        ON( cf.codigo = fsc.codigo_cliente_funcionario ) 
                                 LEFT JOIN clientes_setores cs 
                                        ON( cs.codigo_cliente_alocacao = fsc.codigo_cliente_alocacao 
                                            AND cs.codigo_setor = fsc.codigo_setor ) 
                                 LEFT JOIN grupo_exposicao ge 
                                        ON( ge.codigo_cargo = fsc.codigo_cargo 
                                            AND ge.codigo_cliente_setor = cs.codigo ) 
                          WHERE  fsc.codigo_cliente_alocacao = ".$codigo_cliente." 
                                 AND cf.ativo = 1 
                                 AND (SELECT Count(codigo) quant 
                                      FROM   grupos_exposicao_risco 
                                      WHERE  codigo_grupo_exposicao = ge.codigo) = 0 ";
            if(!empty($usuario['Usuario']['codigo_empresa'])) {
                $query .= " AND cf.codigo_empresa = ".$usuario['Usuario']['codigo_empresa'];
            }
            $query .= "AND 
                            ( 
                              fsc.data_fim IS NULL 
                              OR 
                              fsc.data_fim = '' 
                            ) 
                            GROUP BY fsc.codigo_setor, fsc.codigo_cargo, cf.codigo_empresa, ge.codigo, cs.codigo, fsc.codigo_cliente_alocacao; ";    
        }

        $query .=  " -- EXECUTA UM LAÇO PARA INSERIR O RISCO AUSÊNCIA DE RISCO EM TODOS OS DEPARTAMENTOS INCLUIDOS NO CURSOR
                        OPEN cur_resultado

                        FETCH next
                        FROM  cur_resultado 
                        INTO  @codigo_setor, 
                              @codigo_cargo, 
                              @codigo_empresa, 
                              @codigo_grupo_exposicao, 
                              @codigo_cliente_setor, 
                              @count_riscos, 
                              @codigo_cliente;WHILE @@FETCH_STATUS = 0 
                        BEGIN 
                          -- FARÁ A INCLUSÃO DO GRUPO DE EXPOSIÇÃO E O RISCO AUSENCIA DE RISCO PARA OS SETORES E CARGOS QUE NÃO POSSUEM GRUPO DE EXPOSIÇÃO
                          IF (@codigo_grupo_exposicao IS NULL 
                          OR 
                          @codigo_grupo_exposicao = '') -- SE O GRUPO DE EXPOSIÇÃO FOR VAZIO, EXECUTE: 
                          BEGIN 
                            BEGIN TRANSACTION 
                            IF(@codigo_cliente_setor IS NULL 
                            OR 
                            @codigo_cliente_setor = '') 
                            BEGIN 
                              -- VERIFICA SE JA EXISTE SETOR CADASTRADO, CASO CONTRARIO NÃO SALVA NOVO SETOR 
                              SET @cod_cli_set_atual = 
                              ( 
                                     SELECT codigo 
                                     FROM   clientes_setores 
                                     WHERE  codigo_cliente_alocacao = @codigo_cliente 
                                     AND    codigo_setor = @codigo_setor); 
                              IF(@cod_cli_set_atual IS NULL 
                              OR 
                              @cod_cli_set_atual = '') 
                              BEGIN 
                                BEGIN try 
                                  -- CRIA SETOR DO CLIENTE CASO NÃO EXISTA 
                                  INSERT INTO clientes_setores 
                                              ( 
                                                          codigo_cliente, 
                                                          codigo_cliente_alocacao, 
                                                          codigo_setor, 
                                                          data_inclusao, 
                                                          codigo_usuario_inclusao, 
                                                          codigo_empresa 
                                              ) 
                                              output inserted.codigo 
                                  INTO        @novo_codigo_cliente_setor VALUES 
                                              ( 
                                                          @codigo_cliente, 
                                                          @codigo_cliente, 
                                                          @codigo_setor, 
                                                          Getdate(), 
                                                          ".$usuario['Usuario']['codigo'].", 
                                                          @codigo_empresa 
                                              ); -- automatizar codigo_usuario_inclusao 
                                  SET @codigo_cliente_setor = 
                                  ( 
                                         SELECT codigo 
                                         FROM   @novo_codigo_cliente_setor 
                                  ); 
                                  DELETE 
                                  FROM   @novo_codigo_cliente_setor; 
                                 
                                END try 
                                BEGIN catch 
                                  SELECT Error_number()   AS ErrorNumber , 
                                         Error_severity() AS ErrorSeverity , 
                                         Error_state()    AS ErrorState , 
                                         Error_line()     AS ErrorLine , 
                                         Error_message()  AS ErrorMessage; 
                                   
                                  ROLLBACK; 
                                END catch; 
                              END; 
                              ELSE 
                              BEGIN 
                                SET @codigo_cliente_setor = @cod_cli_set_atual; 
                              END; 
                            END; 
                            IF @@TRANCOUNT > 0 
                            BEGIN 
                              BEGIN try 
                                -- CRIA O GRUPO DE EXPOSIÇÃO 
                                INSERT INTO grupo_exposicao 
                                            ( 
                                                        codigo_cargo, 
                                                        codigo_cliente_setor, 
                                                        data_inclusao, 
                                                        codigo_empresa 
                                            ) 
                                            output inserted.codigo 
                                INTO        @novo_codigo_grupo_exposicao VALUES 
                                            ( 
                                                        @codigo_cargo, 
                                                        @codigo_cliente_setor, 
                                                        Getdate(), 
                                                        @codigo_empresa 
                                            ); 
                                 
                                SET @codigo_grupo_exposicao = 
                                ( 
                                       SELECT codigo 
                                       FROM   @novo_codigo_grupo_exposicao 
                                ); 
                                DELETE 
                                FROM   @novo_codigo_grupo_exposicao; 
                               
                              END try 
                              BEGIN catch 
                                SELECT Error_number()   AS ErrorNumber , 
                                       Error_severity() AS ErrorSeverity , 
                                       Error_state()    AS ErrorState , 
                                       Error_line()     AS ErrorLine , 
                                       Error_message()  AS ErrorMessage; 
                                 
                                ROLLBACK; 
                              END catch; 
                            END; 
                            IF @@TRANCOUNT > 0 
                            BEGIN 
                              BEGIN try 
                                -- CRIA O RISCO (AUSÊNCIA DE RISCO) 
                                INSERT INTO grupos_exposicao_risco 
                                            ( 
                                                        codigo_grupo_exposicao, 
                                                        codigo_risco, 
                                                        data_inclusao, 
                                                        codigo_usuario_inclusao, 
                                                        codigo_empresa 
                                            ) 
                                            VALUES 
                                            ( 
                                                        @codigo_grupo_exposicao, 
                                                        @cod_risco_ausente, 
                                                        Getdate(), 
                                                        ".$usuario['Usuario']['codigo'].", 
                                                        @codigo_empresa 
                                            ); 
                               
                              END try 
                              BEGIN catch 
                                SELECT Error_number()   AS ErrorNumber , 
                                       Error_severity() AS ErrorSeverity , 
                                       Error_state()    AS ErrorState , 
                                       Error_line()     AS ErrorLine , 
                                       Error_message()  AS ErrorMessage; 
                                 
                                ROLLBACK; 
                              END catch; 
                            END; 
                            IF @@TRANCOUNT > 0 
                            COMMIT TRANSACTION; 
                          END; 
                          ELSE 
                          -- SE O GRUPO DE EXPOSIÇÃO NÃO FOR VAZIO, EXECUTE: 
                          BEGIN 
                            BEGIN TRANSACTION 
                            BEGIN try 
                              INSERT INTO grupos_exposicao_risco 
                                          ( 
                                                      codigo_grupo_exposicao, 
                                                      codigo_risco, 
                                                      data_inclusao, 
                                                      codigo_usuario_inclusao, 
                                                      codigo_empresa 
                                          ) 
                                          VALUES 
                                          ( 
                                                      @codigo_grupo_exposicao, 
                                                      @cod_risco_ausente, 
                                                      Getdate(), 
                                                      ".$usuario['Usuario']['codigo'].", 
                                                      @codigo_empresa 
                                          ); 
                             
                            END try 
                            BEGIN catch 
                              SELECT Error_number()   AS ErrorNumber , 
                                     Error_severity() AS ErrorSeverity , 
                                     Error_state()    AS ErrorState , 
                                     Error_line()     AS ErrorLine , 
                                     Error_message()  AS ErrorMessage; 
                               
                              ROLLBACK TRANSACTION; 
                            END catch; 
                            IF @@TRANCOUNT > 0 
                            COMMIT TRANSACTION; 
                          END; 
                          FETCH next 
                          FROM  cur_resultado 
                          INTO  @codigo_setor, 
                                @codigo_cargo, 
                                @codigo_empresa, 
                                @codigo_grupo_exposicao, 
                                @codigo_cliente_setor, 
                                @count_riscos, 
                                @codigo_cliente; 

                        END;
                        CLOSE cur_resultado;
                        DEALLOCATE cur_resultado; ";
        
        if($this->query($query)) {
            return array('erro' => false, 'mensagem' => 'Sucesso!');
        } else {
            return array('erro' => true, 'mensagem' => 'Ouve uma falhe, por favor tente novamente');
        }
    }

    public function dados_modal_pcmso_pendente($codigo_unidade,$codigo_setor,$codigo_cargo,$codigo_funcionario = null){
        $this->Cliente = ClassRegistry::Init('Cliente');
        $this->Setor   = ClassRegistry::Init('Setor');
        $this->Cargo   = ClassRegistry::Init('Cargo');
        $this->AplicacaoExame = ClassRegistry::init('AplicacaoExame');
        $this->Funcionario = ClassRegistry::init('Funcionario');

        $dados_funcionario = array();

        $dados_cliente = $this->Cliente->findbyCodigo($codigo_unidade);
        $dados_setor = $this->Setor->findbyCodigo($codigo_setor);
        $dados_cargo = $this->Cargo->findbyCodigo($codigo_cargo);

        $conditions = array(
            'AplicacaoExame.codigo_cliente_alocacao' => $codigo_unidade,
            'AplicacaoExame.codigo_setor' => $codigo_setor,
            'AplicacaoExame.codigo_cargo' => $codigo_cargo,
        );

        if( empty($codigo_funcionario) ){
            $conditions[] = 'AplicacaoExame.codigo_funcionario IS NULL';
        } else {
            $conditions[] = 'AplicacaoExame.codigo_funcionario IS NULL OR AplicacaoExame.codigo_funcionario = '.$codigo_funcionario;
        }

        $joins = array(
            array(
                'table' => 'exames',
                'alias' => 'Exame',
                'type' => 'LEFT',
                'conditions' => 'AplicacaoExame.codigo_exame = Exame.codigo'
            )
        );

        $dados_exames = $this->AplicacaoExame->find('list',array('conditions' => $conditions,'joins' => $joins,'fields' => array('AplicacaoExame.codigo_exame','Exame.descricao'),'recursive' => -1));

        $dados_modal = array(
            'codigo_unidade' => $dados_cliente['Cliente']['codigo'],
            'nome_fantasia' => $dados_cliente['Cliente']['nome_fantasia'],
            'codigo_setor' => $dados_setor['Setor']['codigo'],
            'setor' => $dados_setor['Setor']['descricao'],
            'codigo_cargo' => $dados_cargo['Cargo']['codigo'],
            'cargo' => $dados_cargo['Cargo']['descricao'],
            'exames' => $dados_exames,
        );

        if( !empty($codigo_funcionario) ){
            $dados_funcionario = $this->Funcionario->findbyCodigo($codigo_funcionario);
            $dados_modal['funcionario'] = $dados_funcionario['Funcionario']['nome'];
        }

        return $dados_modal;
    }

    /**
     * [reprocessamento_api_ppra_log metodo para buscar no log o reprocessamento ]
     * @param  [type] $data_inicio [description]
     * @param  [type] $data_fim    [description]
     * @param  [type] $cpf         [description]
     * @return [type]              [description]
     */
    public function reprocessamento_api_ppra_log ($data_inicio = null, $data_fim = null, $cpf = null)
    {

        // $data_inicio =  '2020-01-01 00:00:00';
        // $data_fim =     '2020-12-03 23:59:59';

        $where = '';
        if(!empty($data_inicio) && !empty($data_fim)) {
            $where .= " AND data_inclusao >= '{$data_inicio}' and data_inclusao <= '{$data_fim}'";
        }

        if(!empty($cpf)) {
            $where .= " AND conteudo like '%{$cpf}%'";
        }

        $log_reprocessado = array();

        //pega os dados do log
        $query_log = "
            SELECT
                l.codigo,
                l.data_inclusao,
                l.retorno,
                l.descricao,
                l.conteudo
            FROM logs_integracoes l 
            WHERE l.arquivo = 'API_PPRA_SINCRONIZAR'
                AND l.status = 0
                {$where}
        ";

        $dados_log = $this->query($query_log);

        // debug($dados_log);exit;
        
        if(!empty($dados_log)) {

            //varre o log
            foreach($dados_log AS $log) {
                
                $codigo_log = $log[0]['codigo'];
                $array_conteudo = explode('api/ppra/sincronizar;',$log[0]['conteudo']);
                
                $conteudo = (trim($array_conteudo[1]));
                $dados_conteudo = json_decode($conteudo);

                $query_riscos = "
                    SELECT 
                        ge.codigo as ge_codigo
                        , ge.data_inclusao as ge_data_inclusao
                        , ge.data_alteracao as ger_data_alteracao
                        , ger.codigo as ger_codigo
                        , ger.codigo_risco as ger_codigo_risco
                        , ger.data_inclusao as ger_data_inclusao
                        , ger.data_alteracao as ger_data_alteracao
                    from clientes_setores cs
                        inner join grupo_exposicao ge on ge.codigo_cliente_setor = cs.codigo
                            and ge.codigo_cargo = (select cae.codigo_cargo from cargos_externo cae where cae.codigo_externo = '{$dados_conteudo->codigo_externo_cargo}')
                            and ge.codigo_funcionario = (select f.codigo from funcionarios f where f.cpf = '{$dados_conteudo->cpf_funcionario}')
                        left join grupos_exposicao_risco ger on ge.codigo = ger.codigo_grupo_exposicao

                    where cs.codigo_cliente = (select ce.codigo_cliente from clientes_externo ce where ce.codigo_externo = '{$dados_conteudo->codigo_externo_unidade_alocacao}')
                        AND cs.codigo_setor = (select se.codigo_setor from setores_externo se where se.codigo_externo = '{$dados_conteudo->codigo_externo_setor}')
                        AND ger.codigo IS NULL
                ";

                $dados_risco = $this->query($query_riscos);

                if(!empty($dados_risco)) {

                    $url = 'https://tstportal.rhhealth.com.br/portal/api/ppra/sincronizar';
                    if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
                        $url = 'https://portal.rhhealth.com.br/portal/api/ppra/sincronizar';
                    }

                    // debug($url);exit;

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_POSTFIELDS => $conteudo,
                      CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                      ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    
                    //resposta
                    $log_reprocessado[$codigo_log] = 'nao reprocessado!';
                    $resp = json_decode($response);                
                    if($resp->status = '0') {
                        $log_incluido[$codigo_log] = 'reprocessado';
                    }
                }
            }//fim foreach para verificar se foi cadastrado o risco
        }

        return $log_reprocessado;

    }//fim getAPIPPRA

    public function ConditionXmlS2240($data) 
    {

        // debug($data);exit;
        //seta a variavel para inicio do metodo
        $conditions = array();
        $this->OrdemServico =& ClassRegistry::Init('OrdemServico');

        //verifica se tem valores nos filtros
        if (!empty($data['codigo_cliente'])) {
            $conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_cliente_alocacao'])) {
            $conditions['ClienteSetor.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'];
        }

        if (!empty($data['codigo_cargo'])) {
            $conditions['GrupoExposicao.codigo_cargo'] = $data['codigo_cargo'];
        }

        if (!empty($data['codigo_setor'])) {
            $conditions['ClienteSetor.codigo_setor'] = $data['codigo_setor'];
        }

        if (!empty($data['nome_funcionario'])) {
            $conditions["Funcionario.nome LIKE"] = '%'. $data['nome_funcionario'] . '%';
        }

        if (!empty($data['cpf'])) {
            $conditions["Funcionario.cpf"] = Comum::soNumero($data['cpf']);
        }

        if (!empty($data['codigo_grupo_exposicao'])) {
            $conditions['GrupoExposicao.codigo'] = $data['codigo_grupo_exposicao'];
        }

        //logica para as datas de filtros
        $data_inicio = date('Y-m-').'01 00:00:00';
        $data_fim = date('Y-m-d').' 23:59:59';
        if(!empty($data["data_inicio"])) {          
            $data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
            $data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');          
        }//fim if
        else if(!empty($data["data_fim"])) {            
            $data_inicio = date('Y-m-').'01 00:00:00';
            $data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');          
        }//fim if

        if(!isset($data['tipo_periodo'])){
            $data['tipo_periodo'] = 'I';
        }

        if($data['tipo_periodo'] == 'I'){// se for data de inicio vigencia
            $conditions['OrdemServico.inicio_vigencia_pcmso >= '] = $data_inicio;
            $conditions['OrdemServico.inicio_vigencia_pcmso <= '] = $data_fim;
        } else if ($data['tipo_periodo'] == 'D') { // se for a data de conclusao
            $conditions[] = "((ClienteFuncionario.data_inclusao >= '".$data_inicio."') OR (FuncionarioSetorCargo.data_inclusao >= '".$data_inicio."'))";
            $conditions[] = "((ClienteFuncionario.data_inclusao <= '".$data_fim."') OR (FuncionarioSetorCargo.data_inclusao <= '".$data_fim."'))";
        } 
        else if ($data['tipo_periodo'] == 'F') { // para pegar os funcionários inativos dentro desta data
            $conditions[] = "((ClienteFuncionario.data_demissao >= '".$data_inicio."') OR (FuncionarioSetorCargo.data_fim >= '".$data_inicio."'))";
            $conditions[] = "((ClienteFuncionario.data_demissao <= '".$data_fim."') OR (FuncionarioSetorCargo.data_fim <= '".$data_fim."'))";

            $conditions[] = "ClienteFuncionario.ativo = 0";
        }

        // Duda, solicitou via whatsapp para o evento esocial s2240 transmitir os xmls dos funcionários.
        // $conditions['Setor.ativo'] = 1;
        // $conditions['Cargo.ativo'] = 1;

        if($data['tipo_periodo'] == 'I') {
            $conditions[] = "ClienteFuncionario.ativo <> 0";
            
            $codigo_ppra = $this->OrdemServico->getPPRAByCodigoCliente($data['codigo_cliente']);

            if($codigo_ppra){
                $conditions['OrdemServicoItem.codigo_servico'] = $codigo_ppra;
            }
        }


        if(!empty($data['bt_filtro'])) {

            switch($data['bt_filtro']) {
                case '1':
                    $conditions[] = 'IntEsocialEvento.codigo_int_esocial_status IS NULL';
                    break;
                default:
                    $conditions['IntEsocialEvento.codigo_int_esocial_status'] = $data['bt_filtro'];
                    break;
            }// fim switch

        }//fim bt_filtro
        
        // die(debug($conditions));
        return $conditions;
        
    } //fim ConditionXmlS2240

    public function returnJoinsEsocial(){
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.clientes_setores',
                'alias' => 'ClienteSetor',
                'type' => 'LEFT',
                'conditions' => 'ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
            ),
            array(
                'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'LEFT',
                'conditions' => 'GrupoEconomicoCliente.codigo_cliente = ClienteSetor.codigo_cliente_alocacao',
            ),
            array(
                'table' => 'RHHealth.dbo.grupos_economicos',
                'alias' => 'GrupoEconomico',
                'type' => 'LEFT',
                'conditions' => 'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo',
            ),
        );

        return $joins;
    }

    public function monta_array_query_ppra(){

		$fields = array(
        	"Setores.descricao AS Setor",
        	"Cargos.descricao AS Cargo",
        	"GrupoEconomicoCliente.codigo_cliente",
        	"Setores.codigo AS CodigoSetor",
        	"Cargos.codigo AS CodigoCargo",
			"(SELECT 
				COUNT(*) AS T 
			FROM funcionario_setores_cargos FuncionarioSetorCargo 
			WHERE Cargos.codigo = FuncionarioSetorCargo.codigo_cargo and Setores.codigo = FuncionarioSetorCargo.codigo_setor and GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao ) AS funcionarios",
			"COUNT( ClientesSetoresCargos.codigo ) AS total",
			"(CASE WHEN GrupoExposicaoRisco.codigo_grupo_exposicao IS NOT NULL THEN 2 ELSE 1 END) AS status",
	    );

		$joins = array(
			array(
				"table"      => "RHHealth.dbo.grupos_economicos_clientes",
				"alias"      => "GrupoEconomicoCliente",
				"conditions" => "GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo"
			),
			array(
				"table"      => "RHHealth.dbo.clientes_setores_cargos",
				"alias"      => "ClientesSetoresCargos",
				"conditions" => "GrupoEconomicoCliente.codigo_cliente = ClientesSetoresCargos.codigo_cliente_alocacao "
			),
			array(
				"table"      => "RHHealth.dbo.setores",
				"alias"      => "Setores",
				"conditions" => "ClientesSetoresCargos.codigo_setor = Setores.codigo"
			),
			array(
				"table"      => "RHHealth.dbo.cargos",
				"alias"      => "Cargos",
				"conditions" => "ClientesSetoresCargos.codigo_cargo = Cargos.codigo"
			),
			array(
				"table"      => "RHHealth.dbo.clientes_setores",
				"alias"      => "ClienteSetor",
				"type"       => "LEFT",
				"conditions" => "ClientesSetoresCargos.codigo_cliente_alocacao = ClienteSetor.codigo_cliente_alocacao AND ClientesSetoresCargos.codigo_setor = ClienteSetor.codigo_setor",
			),
			array(
				"table"      => "RHHealth.dbo.grupo_exposicao",
				"alias"      => "GrupoExposicao",
				"type"       => "LEFT",
				"conditions" => "ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor AND GrupoExposicao.codigo_cargo = ClientesSetoresCargos.codigo_cargo",
			),
			array(
				"table"      => "RHHealth.dbo.grupos_exposicao_risco",
				"alias"      => "GrupoExposicaoRisco",
				"type"       => "LEFT",
				"conditions" => "GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao",
			)
	   );

		$group  = array(
			'Cargos.codigo',
			'Cargos.descricao',
			'Setores.codigo',
			'Setores.descricao',
			'GrupoEconomicoCliente.codigo_cliente',
			'GrupoExposicaoRisco.codigo_grupo_exposicao',
		);

        // popula varivel para ORDER BY
		$order = array(
			"Setores.descricao", 
			"Cargos.descricao"
		); /**/

		$dados = array(
	        'fields'    => $fields,
	        'joins'     => $joins,
	        'group'     => $group,
	        'order'     => $order,
	    );

		return $dados;
	}

}