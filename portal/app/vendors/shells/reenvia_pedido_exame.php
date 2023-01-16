<?php
/** 
 * Shell para reenviar os pedidos de exames, ou seja gerar novamente os kit de atendimento
 * 
 * @author Willians Paulo Pedroso <willianspedroso@gmail.com>
 * @version 0.1 
 * @package Cron
 * @example cake/console/cake -app ./app reenvia_pedido_exame {codigos_pedidos separado por virgula}
 */


class ReenviaPedidoExameShell extends Shell {

	var $uses = array(
		'PedidoExame',
		'TipoNotificacaoValor',

	);

	function main() {
		echo "==================================================\n\n";
		echo "=> reenvia_pedido_exame => Reenvia pedidos de exames gerando novamenteo o kit de atendimento. \n\n";
	}

	function run() {
		if(!$this->im_running('reenvia_pedido_exame')) {

			$codigo_pedido = (isset($this->args[0])) ? $this->args[0] : '';

			if(empty($codigo_pedido)) {
				print "NECESSARIO PASSAR O CODIGO DO PEDIDO QUE IRÁ REENVIAR O KIT\n";
				return false;
			}

			//gera um array dos codigos separados por virgulas
			$codigos = explode(",",$codigo_pedido);
			
			// print_r($codigos);exit;

			$contador = 1;
			foreach($codigos AS $codigo_pedido_exame){
				if(empty($codigo_pedido_exame))
					continue;
				
				$this->reenvia_pedido_exame($codigo_pedido_exame);

				// if($contador == 5) {
				// 	print "\n\n";
				// 	exit;
				// }

				$contador++;
			}
		}
    }
    
	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep '{$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}


	/**
     * [reenviar_email_pedido metodo para reenviar os pedidos de exames com anexo]
     * @return [type] [description]
     */
    public function reenvia_pedido_exame($codigo_pedido)
    {

    	print "INICIANDO O PROCESSAMENTO: $codigo_pedido \n";
    	// exit;

    	$query_pedidos_exames_notificacao = "select * from pedidos_exames_notificacao where codigo_pedido_exame = {$codigo_pedido};";
    	$dados_email = $this->PedidoExame->query($query_pedidos_exames_notificacao);

    	// debug($dados_email);
    	// exit;
    	
    	$pedidos_exame_email = array();
    	if(!empty($dados_email)) {

    		//setando o auth
    		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;

    		$dados_tipos_notificacoes = array(
    			'1' => 'Pedido de Exame',
	            '2' => 'ASO',
	            '3' => 'Ficha Clínica',
	            '4' => 'Laudo Caracterizador de Deficiência',
	            '5' => 'Recomendações',
	            '6' => 'Audiometria',
	            '7' => 'Ficha Assistencial',
	            '8' => 'Avaliação Psicossocial',
    		);

    		print "IDENTIFICANDO OS EMAILS E AS CONFIGURACOES DOS PEDIDOS A SEREM DISPARADOS \n";


    		if(!empty($dados_email[0][0])) {

    			//variavel auxiliar
    			$tipo_email = 'fornecedor';
    			$pedidos_exame_email[$codigo_pedido][$tipo_email][$dados_email[0][0]['codigo_fornecedor']] = $dados_email[0][0]['clinica_email'];
    		}
    		// exit;

    		// debug($pedidos_exame_email);exit;

    		App::import('Core', 'Controller');
    		App::import('Controller', 'PedidosExames');
    		$pedido_exame_controller = new PedidosExamesController();
    		$pedido_exame_controller->constructClasses();

    		print "PREPARANDO PARA REENVIAR OS EMAILS COM OS ANEXOS \n";

    		//varre os pedidos de exames
    		foreach($pedidos_exame_email AS $codigo_pedido_exame => $dados) {

				$dadosPedido = $this->PedidoExame->read(null, $codigo_pedido_exame);
				$codigo_funcionario_setor_cargo = $dadosPedido['PedidoExame']['codigo_func_setor_cargo'];
				$codigo_cliente_funcionario = $dadosPedido['PedidoExame']['codigo_cliente_funcionario'];
				
    			$dados_itens_pedido = $this->PedidoExame->retornaItensDoPedidoExame($codigo_pedido_exame);
				$dados_tipo_notificacao_valor = $this->TipoNotificacaoValor->find('all', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedido_exame)));

				$contatosClienteFuncionario = $this->PedidoExame->retornaContatosClienteFuncionario($codigo_funcionario_setor_cargo);
				//Dados do Cliente e Funcionario
				$cliente_nome       = $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_razao_social'];
				$funcionario_nome   = $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_nome'];
				
				//padra de exames a serem disparados
	    		$dados_post = array(
	    			'PedidosExames' => array(
	    				'funcionario' => array(),
	    				'cliente' => array(),
	    				'fornecedor' => array(),
	    				'vias_aso' => ''
	    			),
	    			'EmailFuncionario' => array(
	    				'email' => ''//$dados['funcionario'][0]
	    			),
	            	'EmailCliente' => array(
	            		'email' => ''//$dados['solicitante'][0]
	            	),
	            	'EmailFornecedor' => array(),
	            	'cliente_nome' => $cliente_nome,
	            	'funcionario_nome' => $funcionario_nome
	    		);
		        
				foreach($dados_tipo_notificacao_valor as $dadoTNV) {

					if($dadoTNV['TipoNotificacaoValor']['campo_funcionario'] == 1) {
						$dados_post['PedidosExames']['funcionario'][$dadoTNV['TipoNotificacaoValor']['codigo_tipo_notificacao']] = 1;
					}

					if($dadoTNV['TipoNotificacaoValor']['campo_cliente'] == 1) {
						$dados_post['PedidosExames']['cliente'][$dadoTNV['TipoNotificacaoValor']['codigo_tipo_notificacao']] = 1;
					}

					if($dadoTNV['TipoNotificacaoValor']['campo_fornecedor'] == 1) {
						$dados_post['PedidosExames']['fornecedor'][$dadoTNV['TipoNotificacaoValor']['codigo_tipo_notificacao']] = 1;
					}

					if(!empty($dadoTNV['TipoNotificacaoValor']['vias_aso'])) {
						$dados_post['PedidosExames']['vias_aso'] = $dadoTNV['TipoNotificacaoValor']['vias_aso'];	
					}
				}

				foreach($dados['fornecedor'] as $codigo_fornecedor => $email_forn) {
					$dados_post['EmailFornecedor'][$codigo_fornecedor]['fornecedor'] = $email_forn;
				}
				
				// debug($dados_post);exit;

				//chamando a controller que vai reenviar os relatorios				
				if($pedido_exame_controller->__enviaRelatorios($dados_post, $dados_itens_pedido, $codigo_cliente_funcionario, $codigo_pedido_exame, $dados_tipos_notificacoes)) {
					print "PEDIDO: ".$codigo_pedido_exame." REENVIADO \n";
				}
				// debug($dados_post);exit;

    		}

    	}// fim emails

    	print "FIM DO REENVIO DOS EMAILS: $codigo_pedido \n";
    	

    }//fim reenviar_email_pedido

}
?>
