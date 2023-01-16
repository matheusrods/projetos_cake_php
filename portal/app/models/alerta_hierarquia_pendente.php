<?php

class AlertaHierarquiaPendente extends AppModel {

	var $name = 'AlertaHierarquiaPendente';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'alertas_hierarquias_pendentes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'codigo_cliente_alocacao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente',
			'on' => 'create',
			'required' => true
		),
		'codigo_setor' => array(
		// 'rule' => 'notEmpty',
		// 'message' => 'Informe o Setor',
		// 'on' => 'create',
		// 'required' => true
		),		
		'codigo_cargo' => array(
			// 'notEmpty' => array(
			// 		// 'rule' => 'notEmpty',
			// 		// 'message' => 'Informe o Setor',
			// 		// 'on' => 'create',
			// 		// 'required' => true
			// 		),
			'alertaExistente' => array(
					'rule' => 'alertaExistente',
					'message' => 'Já existe alerta com este usuário e hierarquia',
					'on' => 'create',
					'required' => true
				),				
			'pendentePpraPcmso' => array(
					'rule' => 'pendentePpraPcmso',
					'message' => 'Já existe PPRA e PCMSO configurados para esta hierarquia',
					'on' => 'create',
					'required' => true
				),		
	
			),
	);


	//Verifica se existe alerta pendente para este usuário e hierarquia
	function existe_alerta($codigo_cliente_alocacao,$codigo_setor, $codigo_cargo,$cod_usuario_inclusao, $origem = NULL){
		
		if(!empty($codigo_cliente_alocacao) && !empty($codigo_setor) && !empty($codigo_cargo) && !empty($cod_usuario_inclusao)){

			$conditions = array(
				'codigo_cliente_alocacao' => $codigo_cliente_alocacao,
				'codigo_setor' => $codigo_setor,
				'codigo_cargo' => $codigo_cargo, 
				'codigo_usuario_inclusao' => $cod_usuario_inclusao,
				'alerta_enviado' => 0);

			if(!is_null($origem)){
				$conditions['origem'] = $origem;
			}

			$alerta_hierarquia = $this->find('first', array('conditions' => $conditions));

			//Se existe alerta
			if(!empty($alerta_hierarquia)){
				return true;
			}
		}
		return false;
	}

	//Verifica se existe ppra ou pcmso pendentes para esta hierarquia
	function ppra_pcmso_aplicado($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo){

		//Se existe PPRA, verifica se existe PCMSO
		if($this->existe_ppra($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo)){

			//verifica se existe PCMSO
			if($this->existe_pcmso($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo)){
				return true;
			}
		}
		return false;
	}



	//Verifica se a hierarquia solicitada possui PCMSO
	function existe_pcmso($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo){
	

        $AplicacaoExame =& ClassRegistry::Init('AplicacaoExame');

        $joins  = array(
        	array(
        		'table' =>'cliente',
        		'alias' => 'Cliente',
        		'conditions' => array('Cliente.codigo = AplicacaoExame.codigo_cliente_alocacao',
        			'Cliente.ativo = 1'),
        	),
        	array(
        		'table' =>'setores',
        		'alias' => 'Setor',
        		'conditions' =>  array('Setor.codigo = AplicacaoExame.codigo_setor',
        			'Setor.ativo = 1'),
        	),  
        	array(
        		'table' =>'cargos',
        		'alias' => 'Cargo',
        		'conditions' =>  array('Cargo.codigo = AplicacaoExame.codigo_cargo',
        			'Cargo.ativo = 1'),
        	)                
        );

        $conditions = array('AplicacaoExame.codigo_setor' => $codigo_setor,
        	'AplicacaoExame.codigo_cargo' => $codigo_cargo,
        	'AplicacaoExame.codigo_cliente_alocacao' => $codigo_cliente_alocacao
        );

        $resultado_pcmso = $AplicacaoExame->find('first',array('fields' => array('codigo'),'joins' => $joins,'conditions' => $conditions,'recursive' => -1));

        
        //Se for encontrado registro
        if(!empty($resultado_pcmso)){
        	return true;
        }

        return false;
	}

	//Verifica se a hierarquia solicitada não possui PPRA
	function existe_ppra($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo){

        $GrupoExposicao =& ClassRegistry::Init('GrupoExposicao');
  
        $joins  = array(
        	array(
        		'table' => 'clientes_setores',
        		'alias' => 'ClienteSetor',
        		'type' => 'INNER',
        		'conditions' => array('ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor',
        			'ClienteSetor.codigo_cliente_alocacao' => $codigo_cliente_alocacao,
        			'ClienteSetor.codigo_setor' => $codigo_setor,
        		),
        	),	        
        	array(
        		'table' => 'grupos_exposicao_risco',
        		'alias' => 'GrupoExposicaoRisco',
        		'type' => 'INNER',
        		'conditions' => 'GrupoExposicaoRisco.codigo_grupo_exposicao = GrupoExposicao.codigo',
        	),
        	array(
        		'table' =>'cliente',
        		'alias' => 'Cliente',
        		'type' => 'INNER',
        		'conditions' => array('Cliente.codigo = ClienteSetor.codigo_cliente_alocacao',
        			'Cliente.ativo = 1'),
        	),
        	array(
        		'table' =>'setores',
        		'alias' => 'Setor',
        		'type' => 'INNER',
        		'conditions' =>  array('Setor.codigo = ClienteSetor.codigo_setor',
        			'Setor.ativo = 1'),
        	),  
        	array(
        		'table' =>'cargos',
        		'alias' => 'Cargo',
        		'type' => 'INNER',
        		'conditions' =>  array('Cargo.codigo = GrupoExposicao.codigo_cargo',
        			'Cargo.ativo = 1'),
        	)        
        );


        $conditions = array('GrupoExposicao.codigo_cargo' => $codigo_cargo,
        );

        $resultado_ppra = $GrupoExposicao->find('first',array('fields' => array('codigo'),'joins' => $joins,'conditions' => $conditions,'recursive' => -1));

             
        //Se for encontrado registro
        if(!empty($resultado_ppra)){
        	return true;
        }

        return false;
	}

	//Envia alertas de uma hierarquia liberada
	function envia_alerta_hierarquia($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo, $aplicacao = NULL){
		
		$this->bindModel(array(
			'belongsTo' => array(
				'Usuario' => array('foreignKey' => 'codigo_usuario_inclusao'),
				'Cliente' => array('foreignKey' => 'codigo_cliente_alocacao'), 
				'Setor' => array('foreignKey' => 'codigo_setor'), 
				'Cargo' => array('foreignKey' => 'codigo_cargo') 
			)
		));

		if(!empty($aplicacao)){
			//PCMSO concluído e somente o PPRA será analisado
			if($aplicacao == 'PPRA'){
				$aplicacao_concluida = $this->existe_ppra($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo);
			} else {
				//PPRA concluído e somente PCMSO será analisado
				$aplicacao_concluida = $this->existe_pcmso($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo);
			}
		} else {
			//Nenhum parâmetro então PPRA e PCMSO serão analisados
			$aplicacao_concluida = $this->ppra_pcmso_aplicado($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo);
		}

		//Se a hierarquia não está mais pendente
		if($aplicacao_concluida){

			$conditions = array(
				'AlertaHierarquiaPendente.alerta_enviado' => 0,
				'AlertaHierarquiaPendente.codigo_cliente_alocacao' => $codigo_cliente_alocacao,
				'AlertaHierarquiaPendente.codigo_setor' => $codigo_setor,
				'AlertaHierarquiaPendente.codigo_cargo' => $codigo_cargo
			); 

			$fields = array(
				'AlertaHierarquiaPendente.codigo',
				'AlertaHierarquiaPendente.codigo_cliente_alocacao',
				'AlertaHierarquiaPendente.codigo_cargo',
				'AlertaHierarquiaPendente.codigo_setor',
				'Usuario.email',
				'Setor.descricao',
				'Cargo.descricao',
				'Cliente.nome_fantasia'
			);

			//Recupera todos os alertas pendentes dessa hierarquia
			$pendentes = $this->find('all',array('fields' => $fields,'conditions' => $conditions));

			foreach ($pendentes as $dado){
				
				$email_usuario = $dado['Usuario']['email'];
				//dados para o e-mail
				$dados_email = array(
					'cliente' => $dado['Cliente']['nome_fantasia'],
					'setor' => $dado['Setor']['descricao'],
					'cargo' => $dado['Cargo']['descricao']
				);

				//Se o e-mail foi disparado, atualiza o registro
				if($this->disparaEmail($dados_email,NULL,'alerta_hierarquia_liberada',$email_usuario)){

					$this->read(null, $dado['AlertaHierarquiaPendente']['codigo']);
					$this->set('email_envio', $email_usuario);
					$this->set('data_envio', date('Y-m-d H:i:s'));
					$this->set('alerta_enviado', 1);

					if(!$this->save()) {
						$this->log('Erro ao atualizar alerta_hierarquia_pendente','debug');
						$this->log($this->validationErrors,'debug');
					}
				}
			}//fim foreach
		}//fim if verifica ppra_pcmso_pendente

		return true;
	}

	public function disparaEmail($dados, $assunto, $template, $to, $attachment = null) {

		if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
			$to = 'tid@ithealth.com.br';
			$cc = null;
		} else {
			$cc = 'agendamento@rhhealth.com.br';
		}

		if(empty($assunto)){
			$assunto  = 'Função com Agendamento liberado';
		}

		App::import('Component', array('StringView', 'Mailer.Scheduler'));

		$this->stringView = new StringViewComponent();
		$this->scheduler = new SchedulerComponent();
		$this->stringView->reset();
		$this->stringView->set('dados', $dados);
		
		$content = $this->stringView->renderMail($template);
		
		return $this->scheduler->schedule($content, array (
			'from' => 'portal@rhhealth.com.br',
			'to' => $to,
			'cc' => $cc,
			'subject' => $assunto
			));

	}
	

	//verifica se já existe alerta pendente de envio para este usuário e para esta hierarquia 
	function alertaExistente() {

		$codigo_cliente_alocacao = $this->data['AlertaHierarquiaPendente']['codigo_cliente_alocacao']; 
		$codigo_setor = $this->data['AlertaHierarquiaPendente']['codigo_setor'];
		$codigo_cargo = $this->data['AlertaHierarquiaPendente']['codigo_cargo'];
		$cod_usuario_inclusao = $_SESSION['Auth']['Usuario']['codigo'];

		//Se não existe alerta
		if(!$this->existe_alerta($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo, $cod_usuario_inclusao)){
			return true;
		}

		return false;
	}

	//Verifica se o PPRA ou PCMSO estão pendentes para permitir a inclusão de alerta
	function pendentePpraPcmso() {

		$codigo_cliente_alocacao = $this->data['AlertaHierarquiaPendente']['codigo_cliente_alocacao']; 
		$codigo_setor = $this->data['AlertaHierarquiaPendente']['codigo_setor'];
		$codigo_cargo = $this->data['AlertaHierarquiaPendente']['codigo_cargo'];

		//Se existe PPRA ou PCMSO pendente será necessário gerar alerta
		if(!$this->ppra_pcmso_aplicado($codigo_cliente_alocacao, $codigo_setor, $codigo_cargo)){
			return true;
		}
		return false;
	}

}
?>