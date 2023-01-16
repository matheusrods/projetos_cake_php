<?php
class AgendamentoExame extends AppModel {

	var $name = 'AgendamentoExame';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'agendamento_exames';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $errors = array();

	var $validate = array(
		'data' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Data',
			'required' => true
		),
		'hora' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Hora'
			 )
		),
		'codigo_fornecedor' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Fornecedor'
			)
		)
	);
	
	function converteFiltroEmCondition($data) {
		$conditions = array(); 
		if (!empty($data['codigo_fornecedor']))
			$conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];
	
		if (!empty($data['codigo_cliente']))
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];

		if (!empty($data['codigo_funcionario']))
			$conditions['Funcionario.codigo'] = $data['codigo_funcionario'];
			
		if (!empty($data['nome_funcionario']))
			$conditions["Funcionario.nome LIKE "] = '%' . $data['nome_funcionario'] . '%';
	
		if (!empty($data['data']))
			$conditions['AgendamentoExame.data'] = AppModel::dateToDbDate($data['data']);
	
		return $conditions;
	}
	
	function retorna_agenda($codigo_fornecedor, $codigo_servico, $data_inicial, $data_final) {
	
		$options['fields'] = array(
			'AgendamentoExame.data',
			'AgendamentoExame.hora',
			'AgendamentoExame.codigo_fornecedor',
			'Exame.codigo_servico',
			'ItemPedidoExame.codigo'
		);
			
		$options['joins']  = array(
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo = AgendamentoExame.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
			)
		);
	
		$options['order'] = array('AgendamentoExame.data ASC', 'AgendamentoExame.hora ASC');
		
		$options['group'] = array(
			'AgendamentoExame.data',
			'AgendamentoExame.hora',
			'AgendamentoExame.codigo_fornecedor',
			'Exame.codigo_servico',
			'ItemPedidoExame.codigo'
		);
	
		
		$options['conditions'] = array(
			'AgendamentoExame.codigo_fornecedor' => $codigo_fornecedor,
			'AgendamentoExame.ativo' => '1',
			'AgendamentoExame.data  BETWEEN ? AND ?' => array($data_inicial, $data_final)
		);
		
		
		if($codigo_servico) {
			$options['conditions']['Exame.codigo_servico'] = $codigo_servico;
		} else {
			$options['conditions']['AgendamentoExame.codigo_lista_de_preco_produto_servico'] = $codigo_servico;
		}
		
		return $this->find('all', $options);
	}	

	public function enviar_notificacao_comparecimento_exame()
    {

    	//$this->query('begin transaction');

        //tira o tempo de limit para processamento
        set_time_limit(0);

        // Pega usuário que estão marcados com "Não comparecimento em exame"
        $usuarios = $this->getUsersNotCompExame();

        //template do e-mail utilizado no envio do arquivo
        $template = 'envio_notificacao_comparecimento_exame';
        $assunto = 'Não Comparecimento ao Exame';

        $msgErro = "";

        //verifica se existe uruaios
        if(!empty($usuarios)){
       	
            //declara o array com os registros
            $reg = array();
            $contador = 0;

            //varre os usuarios
            foreach($usuarios as $usuario){

                //Se não possui e-mail de contato, não gera o arquivo
                if(empty($usuario['Usuario']['email'])) {
                    $msgErro ="Usuário não possui e-mail para envio";
                }
                
                //pega os dados da vigencia caso exista
                $dados_envio = $this->get_func_nao_comp($usuario['Usuario']['codigo_cliente']);

                //verifica se tem dados para gerar o alerta da vigencia ppra pcmso
                if(!empty($dados_envio)) {

                	//monta os registros do alerta
                    $reg['Alerta']['codigo_cliente'] = null;
                    $reg['Alerta']['descricao'] = $assunto;
                    $reg['Alerta']['assunto'] = $assunto;
                    $reg['Alerta']['data_inclusao'] = date('Y-m-d H:i:s');
                    $reg['Alerta']['codigo_alerta_tipo'] = $usuario['UsuarioAlertaTipo']['codigo_alerta_tipo']; 
                    $reg['Alerta']['model'] = "Usuario"; 
                    $reg['Alerta']['foreign_key'] = $usuario['Usuario']['codigo'];

                    $dadosEmail = '';

                    foreach( $dados_envio as $de ){
                    	//pr( $usuario['Usuario'] );
                    	// Usuário deseja somente exames criados por ele
		                if( $usuario['Usuario']['alerta_sm_usuario'] && 
		            		$usuario['Usuario']['codigo'] != $de['PedidosExames']['codigo_usuario_inclusao']  ){
		                	continue;
		                } 		 

                    	$dadosEmail['NomeCliente'] = $de[0]['cliente'];	
                    	$dadosEmail['NomeFornecedor'] = $de[0]['fornecedor'];	
                    	$dadosEmail['NomeFuncionario'] = $de[0]['funcionario'];	
                    	$dadosEmail['NomeExame'] = $de['Exames']['descricao'];	

                    	$DataAgendamento = trim($de[0]['DataAgendamento']);
                    	if( preg_match('/^(.*)\s([0-9]{2}[0-9]{2})$/', $DataAgendamento, $m) ){
                    		
                    		$data = $m[1];
                    		$hora = trim($m[2]);

                    		$DataAgendamento = Comum::formataData( $data, "timestamp", "dmy" )." ".preg_replace('/^([0-9]{2})([0-9]{2})$/', '$1:$2', $hora );
                    	} else {
                    		$DataAgendamento = '';
                    	}

                    	$reg['Alerta']['descricao_email'] = $this->montaEmail($dadosEmail, $DataAgendamento); 

                    	//verifica se o embarcador é o mesmo do transportador
	                    if($usuario['Usuario']['codigo_cliente'] != "") {

	                        //pega o codigo do cliente
	                        $reg['Alerta']['codigo_cliente'] = $usuario['Usuario']['codigo_cliente'];

	                        //realiza o insert na tabela dos alertas
	                        if($this->insereAlerta($reg)) {
	                        	$resAtualizaPE = $this->AtualizaItemPedidoExame( $de['ItemPedidoExame']['codigo'] );
	                        	if( !$resAtualizaPE ){
	                        		$this->log('Usuario:'.$usuario['Usuario']['nome'], 'debug');
                    				$this->log( "Erro ao atualizar Item do Exame" , 'debug');
	                        	}
	                            $contador++;
	                        }

	                    }
	                    else {
	                        //realiza o insert na tabela dos alertas
	                        if($this->insereAlerta($reg)) {
	                        	$resAtualizaPE = $this->AtualizaItemPedidoExame( $de['ItemPedidoExame']['codigo'] );
	                        	if( !$resAtualizaPE ){
	                        		$this->log('Usuario:'.$usuario['Usuario']['nome'], 'debug');
                    				$this->log( "Erro ao atualizar Item do Exame" , 'debug');
	                        	}
	                            $contador++;
	                        }//fim inseriu na alerta

	                    }//fim verifica se emba = tran
                    }                                        
                   

                } else {
                	//echo "Nenhuma notificação a ser enviada para: ". $usuario['Usuario']['codigo'] ." ==> ☺ <==  \n ";
                }

                   
                if($msgErro != "") {
                    $this->log('Usuario:'.$usuario['Usuario']['nome'], 'debug');
                    $this->log($ex->getMessage(), 'debug');

                    $msgErro = "";
                }

            }//fim foreach
        
        }//fim if empty usuarios
        

        //$this->rollback();
        return true;

    }//FINAL FUNCTION envia_arquivo_vigencia_ppra_pcmso
	

    public function getUsersNotCompExame()
    {
        //carrega a model
        $UsuarioAlertaTipo = ClassRegistry::init('UsuarioAlertaTipo');


        //pega os campos
        $fields = array(
            'Usuario.codigo',
            'Usuario.email',
            'Usuario.nome',
            'Usuario.codigo_cliente',
            'UsuarioAlertaTipo.codigo_alerta_tipo',
            'Usuario.alerta_sm_usuario'
        );

        //monta o join
        $joins = array(
            array(
                'table' => 'usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => array('Usuario.codigo = UsuarioAlertaTipo.codigo_usuario')
            ),
        );

        //tipos dos alertas  26 => Não comparecimento em exame
        $tipos = 26;
        //monta o filtro
        $conditions = array(
            'Usuario.email IS NOT NULL',
            'Usuario.alerta_email' => 1,
            'UsuarioAlertaTipo.codigo_alerta_tipo' => $tipos
        );

        $options = array('fields' =>$fields, 'joins' => $joins, 'conditions' => $conditions);
        //pr( $UsuarioAlertaTipo->find('sql',$options) );
        //monta a chamada dos usuarios que iram ser disparados os alertas
        $usuarios = $UsuarioAlertaTipo->find('all',$options);

        //retorna os usuarios, emails, e qual grupo economico se participar
        return $usuarios;

    }


    public function get_func_nao_comp($codigo_cliente = null)
    {
		// popula varivel para SELECT
		$fields = array(
		          "Cliente.nome_fantasia AS cliente",
		          "Funcionario.nome AS funcionario",
		          "Fornecedor.nome AS fornecedor",
		          "Exames.descricao",
		          "ItemPedidoExame.codigo",
		          "PedidosExames.codigo_usuario_inclusao",
		          "PedidosExames.codigo",
		          "( CASE WHEN  AgendamentoExame.codigo IS NULL THEN '' ELSE CONCAT( AgendamentoExame.data, ' ', AgendamentoExame.hora ) END ) AS DataAgendamento "
		);

		// popula varivel para WHERE
		$conditions = array(
		          "ItemPedidoExame.compareceu = 0",
		          "Cliente.codigo = ".$codigo_cliente,
		          "ItemPedidoExame.data_notificacao_nc IS NULL",
		          " ( (AgendamentoExame.codigo IS NULL AND DATEDIFF( day, ItemPedidoExame.data_inclusao  , GETDATE() ) >= 3 )  
   					OR  (AgendamentoExame.codigo IS NOT NULL AND DATEDIFF( day, AgendamentoExame.data  , GETDATE() ) >= 1) )  "
		);



		// popula varivel para FROM
		$joins = array(
		           array(
		          "table" => "pedidos_exames",
		          "alias" => "PedidosExames",
		          "conditions" => "ItemPedidoExame.codigo_pedidos_exames = PedidosExames.codigo"
		),
		           array(
		          "table" => "exames",
		          "alias" => "Exames",
		          "conditions" => "ItemPedidoExame.codigo_exame = Exames.codigo"
		),
		           array(
		          "table" => "cliente_funcionario",
		          "alias" => "ClientesFuncionarios",
		          "conditions" => "PedidosExames.codigo_cliente_funcionario = ClientesFuncionarios.codigo"
		),
		           array(
		          "table" => "funcionarios",
		          "alias" => "Funcionario",
		          "conditions" => "Funcionario.codigo = ClientesFuncionarios.codigo_funcionario"
		),
		           array(
		          "table" => "cliente",
		          "alias" => "Cliente",
		          "conditions" => "ClientesFuncionarios.codigo_cliente = Cliente.codigo"
		),
		           array(
		          "table" => "fornecedores",
		          "alias" => "Fornecedor",
		          "conditions" => "ItemPedidoExame.codigo_fornecedor = Fornecedor.codigo"
		),
		          array(
		          "table" => "agendamento_exames",
		          "alias" => "AgendamentoExame",
		          'type' => 'LEFT',
		          "conditions" => "AgendamentoExame.codigo_itens_pedidos_exames = ItemPedidoExame.codigo AND AgendamentoExame.ativo = 1 "
		)
		);

		// popula varivel para GROUP BY
		$group = array();

		// popula varivel para ORDER BY
		$order = array();

		// define options para ORM
		$options = array(
		          "fields" => $fields,
		          "joins" => $joins,
		          "conditions" => $conditions,
		          "group" => $group,
		          "order" => $order,
		          "recursive" => -1
		);


		$ItemPedidoExame = ClassRegistry::init('ItemPedidoExame');
		//echo "\n\n".pr($ItemPedidoExame->find( 'sql', $options ))."\n\n"; exit;
		return $ItemPedidoExame->find( 'all', $options );

    }


    public function montaEmail($dados, $DataAgendamento = '')
    {
    	$html = '';

    	if( $DataAgendamento != '' ){
    		$texto = '<p>Informamos que o colaborador '. $dados['NomeFuncionario'] .', não compareceu na clínica '. $dados['NomeFornecedor'] .' em '. $DataAgendamento .' para a realização do exame '. $dados['NomeExame'] .'.</p>';
    	} else {
    		$texto = '<p>Informamos que o colaborador '. $dados['NomeFuncionario'] .', até a presente data não compareceu na clínica '. $dados['NomeFornecedor'] .' para a realização do exame '. $dados['NomeExame'] .'.</p>';
    	}
    	
        //monta o html para disparar o alerta
        $html = utf8_encode('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                            <html xmlns="http://www.w3.org/1999/xhtml">

                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            </head>

                            <body>
                                <div style="clear:both;">
                                    <div> <img style="display:block;" src="http://portal.rhhealth.com.br/portal/img/logo-rhhealth.png" style="float:left;">
                                        <hr style="border:1px solid #EEE; display:block;" /> </div>
                                        <div style="background: #fff; float:none; height: 10px; margin-top:5px; padding:8px 10px 0 0; width:99%;"></div>
                                    </div>
                                    <div style="clear:both;padding-top:50px;padding-left:50px;width:98.4%;min-height:300px;">
                                        <p>Prezado cliente '. $dados['NomeCliente'] .',</p> 
										'. $texto .'
										<p>Pedimos que por gentileza realize novo agendamento ou solicite através da nossa central a remarcação deste exame. </p>
										<p>Obrigado,</p>
										<p>Equipe RH Health.</p>
                                        
                                        <p>E-MAIL</p>
                                        <p>relacionamento@rhhealth.com.br</p>
                                        
                                        <p>TELEFONE</p>
                                        <p>(11) 5079-2550</p>
                                        
                                        <p>Obrigado pela atenção!</p>
                                    
                                    <p>Um abraço,</p>
                                    <b>Equipe RH Health</b><br />
                                    <a href="http://www.rhhealth.com.br" target="_blank">www.rhhealth.com.br</a><br />
                                </div>
                            </body>
                            </html>');
            

        return $html;
    }

    private function insereAlerta($dados)
    {
        //instancia a tabela de alertas
        $this->alerta = ClassRegistry::init('Alerta');
        //array com os dados a serem inseridos na alerta
        if($this->alerta->incluir($dados)) {
            return true;
        }
        return false;

    }

    private function AtualizaItemPedidoExame( $item_pedido_codigo ){

    	$ItemPedidoExame = ClassRegistry::init('ItemPedidoExame');

    	$dados['ItemPedidoExame'] = array( 	"data_notificacao_nc" => date('Y-m-d H:i:s'),
    										"codigo" => $item_pedido_codigo );
    	$dados['ItemPedidosExameLog'] = array( 	"codigo_usuario_inclusao" => 1 );

    	if( $ItemPedidoExame->atualizar($dados, array('callbacks'=>false)) ){

    		return true;

    	} else {

    		return false;

    	}

    }

}

?>