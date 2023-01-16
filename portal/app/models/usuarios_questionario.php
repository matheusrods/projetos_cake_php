<?php

class UsuariosQuestionario extends AppModel {

	var $name = 'UsuariosQuestionario';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'usuarios_questionarios';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'codigo_usuario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Usuário!',
			'required' => true
			),
		'codigo_questionario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Questionário!',
			'required' => true
			)    		
		);


	function verifica_formulario_pendente_de_preenchimento() {

		$Questionario =& ClassRegistry::Init('Questionario');
		$Usuario =& ClassRegistry::Init('Usuario');

		$todos_questionarios = $Questionario->find('all', array('conditions' => array('status' => '1'), 'recursive' => -1));

		$codigo = 0;
		$next_questionario = array();
		$array_questionario = array();
		foreach($todos_questionarios as $k => $questionario) {
			$next_questionario[$codigo] = $questionario['Questionario']['codigo'];

			$codigo = $questionario['Questionario']['codigo'];
			$array_questionario[$questionario['Questionario']['codigo']] = $questionario['Questionario'];
		}

		$todos_usuarios = $Usuario->find('all', array('conditions' => array('Usuario.codigo_uperfil' => '9'), 'recursive' => -1, 'joins' => array(
			array(
				'alias' => 'UsuariosQuestionario',
				'table' => 'usuarios_questionarios',
				'type' => 'LEFT',
				'conditions' => array(
					'UsuariosQuestionario.codigo_usuario = Usuario.codigo'
					)
				)
			), 'fields' => array('Usuario.codigo', 'Usuario.nome', 'Usuario.email', 'Usuario.data_inclusao', 'UsuariosQuestionario.codigo_questionario', 'UsuariosQuestionario.data_inclusao')));

		$array_usuarios_notificar = array();
		foreach($todos_usuarios as $k => $user) {
			if(!is_null($user['UsuariosQuestionario']['codigo_questionario'])) {
				if(isset($next_questionario[$user['UsuariosQuestionario']['codigo_questionario']])) {
					$array_usuarios_notificar[$user['Usuario']['codigo']]['codigo_questionario'] = $next_questionario[$user['UsuariosQuestionario']['codigo_questionario']];
					$array_usuarios_notificar[$user['Usuario']['codigo']]['data_preenchimento'] = $user['UsuariosQuestionario']['data_inclusao'];
					$array_usuarios_notificar[$user['Usuario']['codigo']]['email'] = $user['Usuario']['email'];
					$array_usuarios_notificar[$user['Usuario']['codigo']]['nome'] = $user['Usuario']['nome'];
					$array_usuarios_notificar[$user['Usuario']['codigo']]['dados'] = $array_questionario[$next_questionario[$user['UsuariosQuestionario']['codigo_questionario']]];
				}
			} else {
				$array_usuarios_notificar[$user['Usuario']['codigo']]['codigo_questionario'] = $next_questionario[0];
				$array_usuarios_notificar[$user['Usuario']['codigo']]['data_preenchimento'] = $user['Usuario']['data_inclusao'];
				$array_usuarios_notificar[$user['Usuario']['codigo']]['email'] = $user['Usuario']['email'];
				$array_usuarios_notificar[$user['Usuario']['codigo']]['nome'] = $user['Usuario']['nome'];    			
				$array_usuarios_notificar[$user['Usuario']['codigo']]['dados'] = $array_questionario[$next_questionario[0]];
			}
		}

		foreach($array_usuarios_notificar as $k => $item) {
			$qtd_dias = Comum::diffDate(Comum::dateToTimestamp(substr($item['data_preenchimento'], 0, 10)), strtotime(date("Y-m-d")));
			if($qtd_dias['dia'] > ($item['dados']['quantidade_dias_notificacao'] ? $item['dados']['quantidade_dias_notificacao'] : 0)) {
    			if(($qtd_dias['dia'] % 5) == 0) { // envia e-mail a cada 5 dias;
    				$this->disparaEmail(
    					$item,
    					'Formulário "' . $item['dados']['descricao'] . '" com Respostas Pendente',
    					'envio_lembrete_formulario_pendente',
    					$item['email']
    					);
    			}
    		}
    	}
    } 
    
    public function disparaEmail($dados, $assunto, $template, $to, $codigo = null) {

    	if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
    		$to = 'tid@ithealth.com.br';
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
    		'subject' => $assunto
    		));
    }    

    public function buscarQuestionarioPorCliente($conditions)
    {
    	$joins = array(
    		array(
    			'table' => 'usuarios_dados',
    			'alias' => 'UsuariosDados',
    			'type' => 'INNER',
    			'conditions' => array(
    				'UsuariosDados.codigo_usuario = UsuariosQuestionario.codigo_usuario'
    				)
    			),
    		array(
    			'table' => 'funcionarios',
    			'alias' => 'Funcionario',
    			'type' => 'INNER',
    			'conditions' => array(
    				'Funcionario.cpf = UsuariosDados.cpf'
    				)
    			),
    		array(
    			'table' => 'cliente_funcionario',
    			'alias' => 'ClienteFuncionario',
    			'type' => 'INNER',
    			'conditions' => array(
    				'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
    				)
    			),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => array (
                    "FuncionarioSetorCargo.codigo = (Select TOP 1 codigo from funcionario_setores_cargos Where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER by codigo DESC)"
                    )
                ),
    		array(
    			'table' => 'grupos_economicos_clientes',
    			'alias' => 'GrupoEconomicoCliente',
    			'type' => 'INNER',
    			'conditions' => array(
    				'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao'
    				)
    			),
    		array(
    			'table' => 'grupos_economicos',
    			'alias' => 'GrupoEconomico',
    			'type' => 'INNER',
    			'conditions' => array(
    				'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
    				)
    			),
    		);

    	return $this->find('all', array('conditions' => $conditions, 'joins' => $joins));
    }

    public function verificaExistenciaHistoricoAtivo($codigo_usuario, $codigo_empresa, $resposta)
    {
       	//verifica se existe no historico
    	$verifica_existencia_historico_ativo = $this->find('first', array(
    		'conditions' => array(
    			'UsuariosQuestionario.codigo_usuario' => $codigo_usuario,
    			'UsuariosQuestionario.codigo_questionario' => $resposta['Questao']['codigo_questionario'],
    			'UsuariosQuestionario.data_inclusao >= ' => date('Y-m-d', strtotime(date('Y-m-d'). '- 30 days')),
    			'UsuariosQuestionario.finalizado' => NULL  
    			)	
    		)
    	);

		// se nao existir historico cria um novo
    	if(!empty($verifica_existencia_historico_ativo)) {
    		return $verifica_existencia_historico_ativo['UsuariosQuestionario']['codigo'];
    	} else {
    		$this->incluir(array(
    			'UsuariosQuestionario' => array(
    				'codigo_usuario' => $codigo_usuario,
    				'codigo_questionario' => $resposta['Questao']['codigo_questionario'],
    				'data_inclusao' => date('Y-m-d'),
    				'codigo_empresa' => $codigo_empresa
    				)
    			)
    		);
    		return $this->id;
    	}
    }  
}