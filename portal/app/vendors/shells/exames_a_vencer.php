<?php
App::import('Component', 'StringView');
App::import('Core', 'Controller');
App::import('Component', 'Email');
App::import('Lib', 'AppShell');

class ExamesAVencerShell extends Shell {
	var $frequencia = 10140;
	function main() {
		echo "*******************************************************************\n";
		echo "* Exames à vencer \n";
		echo "* cake/console/cake -app ./app exames_a_vencer run\n";
		echo "* cake/console/cake -app ./app exames_a_vencer run_cliente\n";
		echo "*******************************************************************\n";
		echo "\n";
	}

	function im_running(){
		$retorno = shell_exec("ps aux | grep \"exames_a_vencer\"");
		echo substr_count($retorno, 'cake.php -working');
		return substr_count($retorno, 'cake.php -working') > 1;
	}

	function run(){
		if($this->im_running()) {
			echo "Já em execução";
			return FALSE;
		}

		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();
        $this->Scheduler  = new SchedulerComponent();
		$this->ItemPedidoExame = ClassRegistry::init('ItemPedidoExame');
		$this->Alerta          = ClassRegistry::init('Alerta');
		$this->AlertaTipo      = ClassRegistry::init('AlertaTipo'); 
		$this->Configuracao    = ClassRegistry::init('Configuracao'); 

		$dias_vencimento_exame_notificacao = $this->Configuracao->findByChave('DIAS_VENCIMENTO_EXAME_NOTIFICACAO', 'valor');
		$data_envio = $this->Configuracao->findByChave('DATA_ENVIO', 'valor');
		$data_envio = date('d', strtotime(date('Y').'-'.date('m').'-'.$data_envio['Configuracao']['valor']));

		if(!empty($dias_vencimento_exame_notificacao) && !empty($data_envio) && $data_envio == date('d')) {	
			
			$dias_a_vencer = $dias_vencimento_exame_notificacao['Configuracao']['valor'];

			$dados = $this->getExamesAVencer($dias_a_vencer);

			foreach ($dados as $key => $dado) {
				$this->StringView->reset();
				$this->StringView->set('ItemPedidoExame', $dado['dados']);
				$content = $this->StringView->renderMail('email_exames_a_vencer', 'default');
				$alerta = array(
					'Alerta' => array( 
					    'codigo_cliente'     => $dado['codigo_cliente'],              
						'descricao'          => "Exames à vencer",
						'assunto'            => "Exames à vencer",
						'descricao_email'    => $content,
						'codigo_alerta_tipo' => AlertaTipo::ALERTA_EXAMES_A_VENCER,
						'model'              => 'ItemPedidoExame',
						'foreign_key'        => $dado['codigo_pedido_exame'],
						'email_agendados'    => false,
						'sms_agendados'      => false
						),
					);
				$this->Alerta->incluir($alerta);
			}
		}
	}

	public function enviarEmail($content,$options) {      
        $controller       = new Controller();
        $this->StringView = new StringViewComponent();
        $this->Email      = new EmailComponent();

        $this->StringView->reset();
       
        $this->Email->startup($controller);
        $this->Email->sendAs = 'html';
        $this->Email->from = 'portal@rhhealth.com.br>';
   
        $this->Email->subject = 'Alertas Pedidos Não Integrados';
        $this->Email->template  = null;
        $this->Email->layout    = null;
        $this->Email->smtpOptions = array(
            'port'=>'25',
            'timeout'=>'30',
            'host' => 'webmail.buonny.com.br',
        );
        $this->Email->delivery = 'smtp';
        $this->Email->from = 'portal@rhhealth.com.br';

        if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
            $this->Email->to = explode(';', $options['to']);
            $this->Email->subject = "[RH Health]" . $options['subject'];
        }else{
            $this->Email->subject = "[teste]" . $options['subject'];          
            $this->Email->to = array('tid@ithealth.com.br');
        }          
        return $this->Email->send($content);
    }


    /**
     * [getExamesAVencer description]
     * 
     * pega os exames a vencer em um pediodo de dias no futuro
     * 
     * quando passado codigo_cliente deve filtrar pelo codigo do mesmo
     * 
     * @param  [type] $dias_a_vencer  [description]
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function getExamesAVencer($dias_a_vencer, $codigo_cliente = null)
    {

    	$this->ItemPedidoExame = ClassRegistry::init('ItemPedidoExame');

    	//filtros
    	$conditions = array(
    		'ItemPedidoExameBaixa.data_validade <=' => date('Y-m-d', strtotime('+'.$dias_a_vencer.' days'))
		);

		//verifica se esta vindo o codigo cliente 
		if(!is_null($codigo_cliente)) {
			$conditions[] = "PedidoExame.codigo_cliente IN (SELECT codigo_cliente FROM grupos_economicos_clientes WHERE codigo_grupo_economico IN (SELECT codigo FROM grupos_economicos WHERE codigo_cliente = ".$codigo_cliente."))";
		} //verifica o codigo cliente

    	$exames = $this->ItemPedidoExame->find('all', array(
			'joins' => array(
				array(
					'table' => 'itens_pedidos_exames_baixa',
					'alias' => 'ItemPedidoExameBaixa',
					'type' => 'INNER',
					'conditions' => array(
						'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
						)
					),
				array(
					'table' => 'pedidos_exames',
					'alias' => 'PedidoExame',
					'type' => 'INNER',
					'conditions' => array(
						'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames'
						)
					),
				array(
					'table' => 'cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => array(
						'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
						)
					),
				array(
					'table' => 'exames',
					'alias' => 'Exame',
					'type' => 'INNER',
					'conditions' => array(
						'Exame.codigo = ItemPedidoExame.codigo_exame'
						)
					),
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array(
						'Cliente.codigo = ClienteFuncionario.codigo_cliente'
						)
					),
				array(
					'table' => 'funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => array(
						'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
						)
					),
				),
			'fields' => array(
				'ClienteFuncionario.codigo_cliente',
				'ClienteFuncionario.codigo_funcionario',
				'Exame.codigo',
				'Exame.descricao',
				'ItemPedidoExameBaixa.data_validade',
				'PedidoExame.codigo',
				'PedidoExame.data_inclusao',
				'Cliente.razao_social',
				'Funcionario.nome',
				),
			'order' => 'ClienteFuncionario.codigo_cliente',
			'conditions' => $conditions
			)
		);

		$dados = array();
		foreach ($exames as $key => $exame) {
			$dados[$exame['ClienteFuncionario']['codigo_cliente']]['dados'][] = array(
				'codigo_exame' 			=> $exame['Exame']['codigo'],
				'codigo_cliente' 		=> $exame['ClienteFuncionario']['codigo_cliente'],
				'exame_descricao' 		=> $exame['Exame']['descricao'],
				'data_validade' 		=> $exame['ItemPedidoExameBaixa']['data_validade'],
				'data_inclusao' 		=> $exame['PedidoExame']['data_inclusao'],
				'codigo_pedido_exame' 	=> $exame['PedidoExame']['codigo'],
				'razao_social'			=> $exame['Cliente']['razao_social'],
				'funcionario'			=> $exame['Funcionario']['nome'],
				'codigo_funcionario'	=> $exame['ClienteFuncionario']['codigo_funcionario']
				);	
			$dados[$exame['ClienteFuncionario']['codigo_cliente']]['codigo_cliente']		= $exame['ClienteFuncionario']['codigo_cliente'];
			$dados[$exame['ClienteFuncionario']['codigo_cliente']]['codigo_pedido_exame']	= $exame['PedidoExame']['codigo'];
		}

		return $dados;

    }//fim getExamesAVencer

    public function run_cliente()
    {
		if($this->im_running()) {
			echo "Já em execução";
			return FALSE;
		}

		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();
        $this->Scheduler  = new SchedulerComponent();
		
		$this->Alerta          		= ClassRegistry::init('Alerta');		
		$this->UsuarioAlertaTipo    = ClassRegistry::init('UsuarioAlertaTipo'); 
		$this->GrupoEconomico    	= ClassRegistry::init('GrupoEconomico'); 
		$this->Exame          		= ClassRegistry::init('Exame');

		$alerta_tipo = 41;
	    if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO){
	        $alerta_tipo = 1037;
	    }

	    
		//pega os usuarios que tem o alerta habilitado e tem o codigo de cliente ou multicliente
		$fields = array(
			"ISNULL(UsuarioMultiCliente.codigo_cliente, Usuario.codigo_cliente) AS codigo_cliente",
			"Usuario.nome"
		);
		
		$joins = array(
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => array('Usuario.codigo = UsuarioAlertaTipo.codigo_usuario')
			),
			array(
				'table' => 'usuario_multi_cliente',
				'alias' => 'UsuarioMultiCliente',
				'type' => 'LEFT',
				'conditions' => array('Usuario.codigo = UsuarioMultiCliente.codigo_usuario')
			),
		);

		$clientes = $this->UsuarioAlertaTipo->find('all', 
			array(
				'fields' => $fields, 
				'joins' => $joins, 
				'conditions' => array('UsuarioAlertaTipo.codigo_alerta_tipo' => $alerta_tipo),
				'group' => array('Usuario.codigo_cliente','UsuarioMultiCliente.codigo_cliente','Usuario.nome')
		));

		//verifica se tem registro para disparar as notificações de exames a vencer por cliente
		if(!empty($clientes)) {

			//variavel auxiliar
			$dados_exames_a_vencer = array();

			//join para o grupo economico trazendo os dados do cliente
			$join_ge = array(
				array(
					'table' => 'cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => array('Cliente.codigo = GrupoEconomico.codigo_cliente')
				),
			);

			//campos para exibicao
			$fields_ge = array(
				'GrupoEconomico.codigo',
				'GrupoEconomico.codigo_cliente',
				'GrupoEconomico.exames_dias_a_vencer',
				'Cliente.codigo',
				'Cliente.razao_social',
				'Cliente.nome_fantasia',
			);

			$data_de = date('Ymd');
			$var_aux = array();

			//varre as empresas para saber qual é a quantidade de dias para montar as notificações
			foreach($clientes AS $cliente) {

				//seta os dias padrao para verificar os exames a vencer
				$dias_a_vencer = 30;

				//pega os dados de configuraca do grupo economico
				$ge = $this->GrupoEconomico->find('first',
					array(
						'fields' => $fields_ge,
						'joins' => $join_ge,
						'conditions' => array('GrupoEconomico.codigo_cliente' => $cliente[0]['codigo_cliente'])
					)
				);

				//verifica se existe condiguracao
				if(!empty($ge['GrupoEconomico']['exames_dias_a_vencer'])) {
					$dias_a_vencer = $ge['GrupoEconomico']['exames_dias_a_vencer'];
				}

				//verifica se existe para nao processar 2 vezes a posicao de exames
				if(isset($var_aux[$dias_a_vencer][$ge['Cliente']['codigo']])) {
					continue;
				}//fim isset

				//variavel auxiliar para nao buscar os dados do mesmo cliente duas vezes
				$var_aux[$dias_a_vencer][$ge['Cliente']['codigo']] = true;

				//olha os dias a frente que deve ser a vencer de um determinado exame
				$data_ate = date('Ymd', strtotime('+'.$dias_a_vencer.' days'));

				//verifica se tem exames a vencer por cliente
				$cte_exames = $this->Exame->cte_posicao_exames_otimizada($ge['Cliente']['codigo'],false);

				//filtra os resultados da cte_exames
				$query_filtros = "
					SELECT top 1 
						analitico.codigo_matriz,
						analitico.codigo_unidade,
						analitico.vencer
					FROM cetBaixaPedido AS [analitico]   
					WHERE [analitico].[codigo_matriz] = '".$ge['Cliente']['codigo']."' 
						AND (((([analitico].[tipo_exame] = 'R')  
							AND  ([analitico].[codigo_pedido] IS NOT NULL)  
							AND  ([analitico].[data_realizacao_exame] IS NOT NULL)  
							AND  ([analitico].[ativo] <> 0))) 
							OR ((([analitico].[tipo_exame] = 'M')  
								AND  ([analitico].[codigo_pedido] IS NOT NULL)  
								AND  ([analitico].[data_realizacao_exame] IS NOT NULL)  
								AND  ([analitico].[ativo] <> 0))) 
							OR ((([analitico].[tipo_exame] = 'MT')  
								AND  ([analitico].[codigo_pedido] IS NOT NULL)  
								AND  ([analitico].[ativo] <> 0))) 
							OR ((([analitico].[tipo_exame] = 'P')  
								AND  ([analitico].[codigo_pedido] IS NOT NULL)  
								AND  ([analitico].[ativo] <> 0)))) 
						AND [analitico].[vencimento] BETWEEN '".$data_de."' AND '".$data_ate."'   
					";
								
				//executa a query
				$exames_a_vencer = $this->Exame->query($cte_exames. $query_filtros);

				//verifica se tem exames a vencer para alertar os usuarios
				if(!empty($exames_a_vencer)) {

					//dados para o email
					$dado['Usuario']['nome'] = $cliente['Usuario']['nome'];
					$dado['dias_a_vencer'] = $dias_a_vencer;
					//monta o link para disparar por email
					$dado['link'] = $this->linkExamesAVencer($ge['Cliente']['codigo'],$data_de,$data_ate);

					$this->StringView->reset();
					$this->StringView->set('dados', $dado);
					$content = $this->StringView->renderMail('email_exames_a_vencer_cliente', 'default');
					$alerta = array(
						'Alerta' => array( 
						    'codigo_cliente'     => $ge['Cliente']['codigo'],
							'descricao'          => "Exames à vencer",
							'assunto'            => "Exames à vencer",
							'descricao_email'    => $content,
							'codigo_alerta_tipo' => $alerta_tipo,
							'model'              => 'Cliente',
							'foreign_key'        => $ge['Cliente']['codigo'],
							'email_agendados'    => false,
							'sms_agendados'      => false
							),
						);
					$this->Alerta->incluir($alerta);
				}

				// debug($exames_a_vencer);

			}//fim foreach


			// foreach ($dados as $key => $dado) {
			// }

		}//fim usuario

	}//fim run_cliente

	 /**
     * @param  [codigo_cliente] codigo do clietne que irá gerar o hash
     * @param  [mes] mes
     * @param  [ano] ano
     * @return [link] link para acessar o relatorio de demonstrativo
     */
    private function linkExamesAVencer($codigo_cliente,$de,$ate) {
        //verifica se codigo_cliente está nulo para trazer todas as vigencias a vencer e vencidas.
        $codigo_cliente = "'{$codigo_cliente}'";
        $de = "'{$de}'";
        $ate = "'{$ate}'";

        $dados = $codigo_cliente."|".$de."|".$ate;

        //monta o hash para colocar no link
        $hash = Comum:: encriptarLink($dados);
        //monta o host
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));

        //monta o link
        $link_vigencia = "http://{$host}/portal/exames/gera_arquivo_exames_a_vencer?key=".urlencode($hash);

        //retorno o link a ser acessado
        return $link_vigencia;
    }


}