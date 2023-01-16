<?php
class FichasComponent {
	var $name = 'Fichas';
	
	function initialize(&$controller, $settings = array()) {        
		// saving the controller reference for later use        
		$this->controller =& $controller;    
	}
        
	function carregarCombos() {
		$this->Usuario = ClassRegistry::init('Usuario');
		$this->EmbarcadorTransportador = ClassRegistry::init('EmbarcadorTransportador');
		$this->VEndereco = ClassRegistry::init('VEndereco');
		$this->ProfissionalTipo = ClassRegistry::init('ProfissionalTipo');
		$this->TipoRetorno = ClassRegistry::init('TipoRetorno');
		$this->EnderecoEstado = ClassRegistry::init('EnderecoEstado');
		$this->EnderecoCidade = ClassRegistry::init('EnderecoCidade');
		$this->TipoCnh = ClassRegistry::init('TipoCnh');
		$this->VeiculoModelo = ClassRegistry::init('VeiculoModelo');
		$this->VeiculoCor = ClassRegistry::init('VeiculoCor');
		$this->VeiculoFabricante = ClassRegistry::init('VeiculoFabricante');
		$this->CargaTipo = ClassRegistry::init('CargaTipo');
		$this->CargaValor = ClassRegistry::init('CargaValor');
		$this->FichaScorecardQuestao = ClassRegistry::init('FichaScorecardQuestao');
		$this->TipoContato = ClassRegistry::init('TipoContato');
		$this->Tecnologia = ClassRegistry::init('Tecnologia');
        $this->ParametroScore = ClassRegistry::init('ParametroScore');
        
		$usuarios = array();
    	if(isset($this->controller->data['FichaScorecard']['codigo_cliente'])) {
    		$usuarios = $this->Usuario->listaPorClienteList($this->controller->data['FichaScorecard']['codigo_cliente']);
    	}
    	$codigo_cliente = isset($this->controller->data['FichaScorecard']['codigo_cliente']) ? $this->controller->data['FichaScorecard']['codigo_cliente'] : null;
    	$dados = $this->EmbarcadorTransportador->dadosPorCliente($codigo_cliente);
    	$embarcadores = $dados['embarcadores'];
    	if (count($embarcadores) == 1) {
    		$this->controller->data['FichaScorecard']['codigo_embarcador'] = key($embarcadores);
    	}
    	$transportadores = $dados['transportadores'];
    	if (count($transportadores) == 1) {
    		$this->controller->data['FichaScorecard']['codigo_transportador'] = key($transportadores);
    	}
		$profissional_enderecos = array();
    	if (isset($this->controller->data['ProfissionalEndereco']['endereco_cep'])) {
			$profissional_enderecos = $this->VEndereco->listarParaComboPorCep($this->controller->data['ProfissionalEndereco']['endereco_cep']);
		}
    	
    	$profissional_tipo = $this->ProfissionalTipo->find('list');
    	$tipo_retorno_cliente = $this->TipoRetorno->find('list', array('conditions' => array('cliente' => true)));
    	$endereco_estado = $this->EnderecoEstado->comboPorPais(1);
    	$cidades_profissional = array();
    	if(isset($this->controller->data['Profissional']['codigo_estado_naturalidade'])) {
    		$cidades_profissional = $this->EnderecoCidade->combo($this->controller->data['Profissional']['codigo_estado_naturalidade']);
    	}
    	$tipo_cnh = $this->TipoCnh->find('list', array('fields'=>array('codigo', 'descricao')));
    	$tipo_retorno_profissional = $this->TipoRetorno->find('list', array('conditions' => array('profissional' => true)));
    	$tipo_retorno_proprietario = $this->TipoRetorno->find('list', array('conditions' => array('proprietario' => true)));
    	$tipo_contato = $this->TipoContato->listarParFichaScorecard();
    	$eh_motorista = array(1=>'Sim', 0=>'Não');
		
        $proprietario_enderecos = array(array(), array(), array());
        $cidades_veiculo = array(array(), array(), array());
        $modelos_veiculo = array(array(), array(), array());
        for($index = 0; $index < 3; $index++){
	    	if (isset($this->controller->data['FichaScorecardVeiculo'][$index]['ProprietarioEndereco']['endereco_cep'])) {
				$proprietario_enderecos[$index] = $this->VEndereco->listarParaComboPorCep($this->controller->data['FichaScorecardVeiculo'][$index]['ProprietarioEndereco']['endereco_cep']);
			}
	    	if(isset($this->controller->data['FichaScorecardVeiculo'][$index]['Veiculo']['codigo_estado'])) {
	    		$cidades_veiculo[$index] = $this->EnderecoCidade->combo($this->controller->data['FichaScorecardVeiculo'][$index]['Veiculo']['codigo_estado']);
	    	}
	    	if(isset($this->controller->data['FichaScorecardVeiculo'][$index]['Veiculo']['codigo_veiculo_fabricante'])) {
	    		$modelos_veiculo[$index] = $this->VeiculoModelo->combo($this->controller->data['FichaScorecardVeiculo'][$index]['Veiculo']['codigo_veiculo_fabricante']);
	    	}
    	}
    	
    	$tecnologias = $this->Tecnologia->lista();
        //debug($tecnologias);die();
    	$cores = $this->VeiculoCor->lista();
    	$fabricantes = $this->VeiculoFabricante->lista();
		
    	$carga_tipos = $this->CargaTipo->lista();
    	$carga_valores = $this->CargaValor->lista();
    	$cidades_origem = array();
    	if(isset($this->controller->data['FichaScorecard']['codigo_estado_origem'])) {
    		$cidades_origem = $this->EnderecoCidade->combo($this->controller->data['FichaScorecard']['codigo_estado_origem']);
    	}
    	$cidades_destino = array();
    	if(isset($this->controller->data['FichaScorecard']['codigo_estado_destino'])) {
    		$cidades_destino = $this->EnderecoCidade->combo($this->controller->data['FichaScorecard']['codigo_estado_destino']);
    	}
    	
    	$questoes = $this->FichaScorecardQuestao->buscarQuestionarioFicha();    	
    	$produtos = ClassRegistry::init('ClienteProduto')->listaProdutosTLCS($codigo_cliente);
        $classificacao_tlc = array( 
            ParametroScore::OURO         => 'PERFIL ADEQUADO AO RISCO', 
            ParametroScore::INSUFICIENTE => 'PERFIL INSUFICIENTE', 
            ParametroScore::DIVERGENTE   => 'PERFIL DIVERGENTE'
        );
    	$this->controller->set(compact('usuarios', 'tipo_retorno_cliente', 'profissional_tipo', 'endereco_estado', 'cidades_profissional', 'tipo_cnh', 'tipo_retorno_profissional', 
    			'tipo_contato', 'profissional_enderecos', 'eh_motorista', 'tipo_retorno_proprietario', 'tecnologias', 'cores', 'fabricantes', 'carga_tipos', 'carga_valores',
    			'cidades_origem', 'cidades_destino', 'questoes', 'embarcadores', 'transportadores', 'proprietario_enderecos', 'cidades_veiculo', 'modelos_veiculo', 'produtos',
                'classificacao_tlc'));
    }
    
	function carregarDadosFicha($codigo_ficha, $modelFicha = 'FichaScorecard', $primaryKey = 'codigo'){
    	$this->$modelFicha = ClassRegistry::init($modelFicha);
    	$this->QuestaoResposta = ClassRegistry::init('QuestaoResposta');
        $this->ProfissionalContatoLog = ClassRegistry::init('ProfissionalContatoLog');
        $this->VeiculoLog = ClassRegistry::init('VeiculoLog');
        $this->ProprietarioLog = ClassRegistry::init('ProprietarioLog');
        $this->ProprietarioContatoLog = ClassRegistry::init('ProprietarioContatoLog');      
    	$this->EnderecoCidade = ClassRegistry::init('EnderecoCidade');
        $this->$modelFicha->primaryKey = $primaryKey;
        $this->$modelFicha->bindModel(
            array(
                'belongsTo'=>array(
                    'Cliente'=>array('foreignKey'=>'codigo_cliente'),
                    'Usuario'=>array('foreignKey'=>'codigo_usuario_responsavel'),
                    'ProfissionalLog'=>array('foreignKey'=>'codigo_profissional_log'),
                    'Profissional'=>array('foreignKey'=>FALSE, 'conditions' =>array('Profissional.codigo = ProfissionalLog.codigo_profissional') ),
                    'ProfissionalEnderecoLog'=>array('foreignKey'=>'codigo_profissional_endereco_log'),
                    'VEndereco'=>array('foreignKey'=>false, 'conditions'=>'ProfissionalEnderecoLog.codigo_endereco = VEndereco.endereco_codigo'),
                    'EnderecoCidadeOrigem' => array('className'=>'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_origem'),
                    'EnderecoCidadeDestino' => array('className'=>'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_destino'),
                ),
                'hasMany'=>array(
                    'FichaScorecardRetorno'=>array('foreignKey'=>'codigo_ficha_scorecard'),
                    'FichaScProfContatoLog'=>array('foreignKey'=>'codigo_ficha_scorecard'),
                    'FichaScorecardVeiculo'=>array('foreignKey'=>'codigo_ficha_scorecard'),
                    'FichaScorecardQuestaoResp' => array('foreignKey'=>'codigo_ficha_scorecard'),
                )
            )
        );
         
        $ficha_scorecard = $this->$modelFicha->find('first', array('conditions'=>array($modelFicha.'.codigo'=>$codigo_ficha)));
         
        $this->controller->data['FichaScorecard'] = $ficha_scorecard[$modelFicha];
        $this->controller->data['FichaScorecard']['codigo_estado_origem']  = $ficha_scorecard['EnderecoCidadeOrigem']['codigo_endereco_estado'];
        $this->controller->data['FichaScorecard']['codigo_estado_destino'] = $ficha_scorecard['EnderecoCidadeDestino']['codigo_endereco_estado'];
        $this->controller->data['Cliente'] = $ficha_scorecard['Cliente'];
        $this->controller->data['FichaScorecardRetorno'] = $ficha_scorecard['FichaScorecardRetorno'];
        $this->controller->data['Profissional'] = $ficha_scorecard['ProfissionalLog'];
        $this->controller->data['Profissional']['data_inclusao'] = $ficha_scorecard['Profissional']['data_inclusao'];

        $dados_cidade_nat_prof = $this->EnderecoCidade->find('first',array('conditions' => array('EnderecoCidade.codigo'=>$this->controller->data['Profissional']['codigo_endereco_cidade_naturalidade'] )));
        $cidade_nat_prof = $dados_cidade_nat_prof['EnderecoCidade']['descricao'];
        $estado_nat_prof = $dados_cidade_nat_prof['EnderecoEstado']['abreviacao'];
        $this->controller->data['Profissional']['cidade_naturalidade_profissional'] = $cidade_nat_prof.' - '.$estado_nat_prof;

        $this->controller->data['ProfissionalEndereco'] = $ficha_scorecard['ProfissionalEnderecoLog'];
        $this->controller->data['ProfissionalEndereco']['endereco_cep'] = $ficha_scorecard['VEndereco']['endereco_cep'];
        $this->controller->data['Usuario']['nome'] = $ficha_scorecard['Usuario']['nome'];
        $cidade_carga_origem  = $this->EnderecoCidade->find('first',array('conditions' => array('EnderecoCidade.codigo' => $ficha_scorecard[$modelFicha]['codigo_endereco_cidade_carga_origem'])));
        $cidade_carga_destino = $this->EnderecoCidade->find('first',array('conditions' => array('EnderecoCidade.codigo'=> $ficha_scorecard[$modelFicha]['codigo_endereco_cidade_carga_destino'])));
        
        $cidade_origem  = $cidade_carga_origem['EnderecoCidade']['descricao'] .'-'. $cidade_carga_origem['EnderecoEstado']['descricao'];
        $cidade_destino = $cidade_carga_destino['EnderecoCidade']['descricao'] .'-'. $cidade_carga_destino['EnderecoEstado']['descricao'];
        $this->controller->data['FichaScorecard']['cidade_origem']  = ($cidade_origem  != '-' ? $cidade_origem : 'NÃO INFORMADO');
    	$this->controller->data['FichaScorecard']['cidade_destino'] = ($cidade_destino != '-' ? $cidade_destino : 'NÃO INFORMADO');
        if (!empty($ficha_scorecard['FichaScorecardQuestaoResp'])) {
            $questoes_resposta = $this->QuestaoResposta->find('list', array('conditions'=>array('codigo'=>Set::extract('/codigo_questao_resposta', $ficha_scorecard['FichaScorecardQuestaoResp'])), 'fields'=>array('codigo', 'codigo_questao')));
                foreach($ficha_scorecard['FichaScorecardQuestaoResp'] as $ficha_scorecard_questao_resposta){
        			$this->controller->data['FichaScorecardQuestaoResposta'][$questoes_resposta[$ficha_scorecard_questao_resposta['codigo_questao_resposta']]]['codigo_questao_resposta'] = $ficha_scorecard_questao_resposta['codigo_questao_resposta'];
                    $this->controller->data['FichaScorecardQuestaoResposta'][$questoes_resposta[$ficha_scorecard_questao_resposta['codigo_questao_resposta']]]['observacao'] = $ficha_scorecard_questao_resposta['observacao'];
    		}
    	}


    	$codigos_prof_contato = Set::extract('/codigo_profissional_contato_log', $ficha_scorecard['FichaScProfContatoLog']);
    	if(!empty($codigos_prof_contato)){
    		$profissionais_contato = $this->ProfissionalContatoLog->find('all', array('conditions'=>array('codigo'=>$codigos_prof_contato), 'fields'=>array('nome', 'codigo_tipo_contato', 'codigo_tipo_retorno', 'descricao')));
    		$this->controller->data['ProfissionalContato'] = Set::extract('/ProfissionalContatoLog/.', $profissionais_contato);
    	}        
        $this->controller->data['FichaScorecardVeiculo']['possui_veiculo']           = 'N';
        $this->controller->data['FichaScorecardVeiculo'][0]['Veiculo']['veiculo_sn']            = 'N';
        $this->controller->data['FichaScorecardVeiculo'][1]['Veiculo']['veiculo_sn'] = 'N';
        $this->controller->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn'] = 'N';
        if ( isset($ficha_scorecard['FichaScorecardVeiculo']) ){
            $this->controller->data['FichaScorecardVeiculo']['possui_veiculo'] = 'S';
            foreach($ficha_scorecard['FichaScorecardVeiculo'] as $key=> $ficha_scorecard_veiculo ){
                $this->VeiculoLog->bindModel(array(
                    'belongsTo' => array(
                            'VeiculoModelo'  => array('foreignKey' => 'codigo_veiculo_modelo'),
                            'EnderecoCidade' => array('foreignKey' => 'codigo_cidade_emplacamento'),
                            'EnderecoEstado' => array('foreignKey' => false, 'conditions'=>'EnderecoCidade.codigo_endereco_estado = EnderecoEstado.codigo'),
                    ),
                ));
                $veiculo = $this->VeiculoLog->find('first', array('conditions' =>
                        array('VeiculoLog.codigo'=>$ficha_scorecard_veiculo['codigo_veiculo_log'])
                    )
                );                
                $this->controller->data['FichaScorecardVeiculo'][$key]['Veiculo'] = $veiculo['VeiculoLog'];
                $this->controller->data['FichaScorecardVeiculo'][$key]['Veiculo']['veiculo_sn'] = 'S';
                $this->controller->data['FichaScorecardVeiculo'][$key]['Veiculo']['codigo_veiculo_tecnologia'] = $ficha_scorecard_veiculo['codigo_tecnologia'];
                $this->controller->data['FichaScorecardVeiculo'][$key]['Veiculo']['codigo_veiculo_fabricante'] = $veiculo['VeiculoModelo']['codigo_veiculo_fabricante'];
                $this->controller->data['FichaScorecardVeiculo'][$key]['Veiculo']['codigo_estado'] = $veiculo['EnderecoCidade']['codigo_endereco_estado'];                
                $this->controller->data['FichaScorecardVeiculo'][$key]['EnderecoCidade']['cidade_emplacamento'] = $veiculo['EnderecoCidade']['descricao'].' - '.$veiculo['EnderecoEstado']['abreviacao'];
                if(!empty($ficha_scorecard_veiculo['codigo_proprietario_endereco_log'])){
            		$this->ProprietarioLog->bindModel(
            				array(
            						'belongsTo'=>array(
            								'ProprietarioEnderecoLog'=>array('foreignKey'=>false, 'conditions'=>'ProprietarioEnderecoLog.codigo = '.$ficha_scorecard_veiculo['codigo_proprietario_endereco_log']),
            								'VEndereco'=>array('foreignKey'=>false, 'conditions'=>'ProprietarioEnderecoLog.codigo_endereco = VEndereco.endereco_codigo'),
            						),
            						'hasMany'=>array(
            								'FichaPropContatoLog'=>array('className'=>'FichaScVeicPropContatoLog', 'foreignKey'=>false, 'conditions'=>'FichaPropContatoLog.codigo_ficha_scorecard_veiculo = '.$ficha_scorecard_veiculo['codigo']),
            						)
            				)
            		);
            		$proprietario = $this->ProprietarioLog->find('first', array('conditions'=>array('ProprietarioLog.codigo'=>$ficha_scorecard_veiculo['codigo_proprietario_log'])));
                    if(!empty($proprietario)){
                        $this->controller->data['FichaScorecardVeiculo'][$key]['Proprietario'] = $proprietario['ProprietarioLog'];
                        $this->controller->data['FichaScorecardVeiculo'][$key]['ProprietarioEndereco'] = $proprietario['ProprietarioLog'];
                        $this->controller->data['FichaScorecardVeiculo'][$key]['ProprietarioEndereco'] = $proprietario['ProprietarioEnderecoLog'];
                        $this->controller->data['FichaScorecardVeiculo'][$key]['ProprietarioEndereco']['endereco_cep'] = $proprietario['VEndereco']['endereco_cep'];
                        $this->controller->data['Motorista'][$key]['proprietario'] = ($this->controller->data['Profissional']['codigo_documento']==$this->controller->data['FichaScorecardVeiculo'][$key]['Proprietario']['codigo_documento'] ? 1 : 0);
                        $codigos = Set::extract('/codigo_proprietario_contato_log', $proprietario['FichaPropContatoLog']);
                    }
                }
        		if(!empty($codigos)){
        			$proprietarios_contato = $this->ProprietarioContatoLog->find('all', array('conditions'=>array('codigo'=>$codigos), 'fields'=>array('nome', 'codigo_tipo_contato', 'codigo_tipo_retorno', 'descricao')));
        			$this->controller->data['FichaScorecardVeiculo'][$key]['ProprietarioContato'] = Set::extract('/ProprietarioContatoLog/.', $proprietarios_contato);
        		}
        	}
        }
        $this->formataDados( );

    }

    function formataDados( ){
        //FichaScorecard
        if( isset($this->controller->data['FichaScorecard']['data_inclusao'] ))
            $this->controller->data['FichaScorecard']['data_inclusao']   = substr($this->controller->data['FichaScorecard']['data_inclusao'],0, 10 );
        //Data Profissional
        if( isset($this->controller->data['Profissional']['rg_data_emissao'] ))
            $this->controller->data['Profissional']['rg_data_emissao']   = substr($this->controller->data['Profissional']['rg_data_emissao'],0, 10 );
        if( isset($this->controller->data['Profissional']['data_nascimento'] ))
            $this->controller->data['Profissional']['data_nascimento']   = substr($this->controller->data['Profissional']['data_nascimento'],0, 10 );
        if( isset($this->controller->data['Profissional']['cnh_vencimento'] ))
            $this->controller->data['Profissional']['cnh_vencimento']    = substr($this->controller->data['Profissional']['cnh_vencimento'],0, 10 );
        if( isset($this->controller->data['Profissional']['data_primeira_cnh'] ))
            $this->controller->data['Profissional']['data_primeira_cnh'] = substr($this->controller->data['Profissional']['data_primeira_cnh'],0, 10 );
        if( isset($this->controller->data['Profissional']['data_inclusao'] ))
            $this->controller->data['Profissional']['data_inclusao']     = substr($this->controller->data['Profissional']['data_inclusao'],0, 10 );
        if( isset($this->controller->data['Profissional']['data_inicio_mopp'] ))
            $this->controller->data['Profissional']['data_inicio_mopp']     = substr($this->controller->data['Profissional']['data_inicio_mopp'],0, 10 );
    }

    function listProfissionalTipoAutorizado() {
        $this->ProfissionalTipo = ClassRegistry::init('ProfissionalTipo');
        $codigo_uperfil = $this->controller->authUsuario['Usuario']['codigo_uperfil'];
        $profissionais_tipos = array();
        if ($this->controller->BAuth->temPermissao($codigo_uperfil, 'obj-visualizar_carreteiro')) {
            $profissionais_tipos[ProfissionalTipo::CARRETEIRO] = 'Carreteiro';
        }
        if ($this->controller->BAuth->temPermissao($codigo_uperfil, 'obj-visualizar_diferente_carreteiro')) {
            $profissionais_tipos[ProfissionalTipo::OUTROS] = 'Outros';
        }
        return $profissionais_tipos;
    }
}