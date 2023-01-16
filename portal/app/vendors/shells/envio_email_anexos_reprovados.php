<?php
App::import('Component', 'Mailer.Scheduler');
App::import('Component', 'StringView');

class EnvioEmailAnexosReprovadosShell extends Shell {
    var $uses = array(
        'AuditoriaExame',
    );

  
    function main() {
        echo "Funcoes: \n";
        echo "=> enviar_email_anexos_reprovados \n";
        $this->enviar_email();
    }

    function is_alive(){
        $retorno = shell_exec("ps -ef | grep \"envio_email_anexos_reprovados \" | wc -l");
        return ($retorno > 3);
    }

    function enviar_email(){
        if($this->is_alive())
            return false;
            
        $this->enviar_email_anexos_reprovados();
    }

    function enviar_email_anexos_reprovados(){
        $this->layout = 'email';

        $query_params = $this->getExamesReprovados();
		$exames_reprovados = $this->AuditoriaExame->find('all',
            array(
                'fields' => $query_params['fields'],
                'joins' => $query_params['joins']
            )
        );
        $array_fornecedores = array();
        if(!empty($exames_reprovados)) {
            foreach($exames_reprovados as $key => $exame_reprovado){
                $array_fornecedores[$exame_reprovado['Fornecedor']['codigo']]['nome'] = $exame_reprovado['Fornecedor']['nome'];
                $array_fornecedores[$exame_reprovado['Fornecedor']['codigo']]['email'] = $exame_reprovado['FornecedorContato']['descricao'];
                $array_fornecedores[$exame_reprovado['Fornecedor']['codigo']]['exames'][] = $exame_reprovado;
            }
        }
        foreach($array_fornecedores as $array_fornecedor){
            $this->EmailNotificacao($array_fornecedor['email'],$array_fornecedor['nome']);
        }
    }



    private function getExamesReprovados(){

        $fields = array(
			'AuditoriaExame.data_inclusao',
			'AuditoriaExame.data_alteracao',
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.data_realizacao_exame',
            'PedidoExame.codigo', 
			'Fornecedor.codigo', 
			'Fornecedor.nome', 
			'Funcionario.nome', 
			'Exame.codigo', 
			'Exame.descricao',
			'Usuario.nome', 
			'AnexosExames.caminho_arquivo',
            'FornecedorContato.descricao'
        );

        $joins = array(
            array(
                'table' => 'pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'AuditoriaExame.codigo_pedido_exame = PedidoExame.codigo AND AuditoriaExame.codigo_status_auditoria_imagem = 2'
            ),
            array(
                'table' => 'itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => 'AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo'
            ),
			array(
                'table' => 'fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'INNER',
                'conditions' => 'AuditoriaExame.codigo_fornecedor = Fornecedor.codigo AND Fornecedor.prestador_qualificado = 1'
            ),
            array(
                'table' => 'fornecedores_contato',
                'alias' => 'FornecedorContato',
                'type' => 'INNER',
                'conditions' => 'Fornecedor.codigo = FornecedorContato.codigo_fornecedor and FornecedorContato.codigo_tipo_contato = 8 and FornecedorContato.codigo_tipo_retorno = 2'
            ),
			array(
                'table' => 'cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo'
            ),
			array(
                'table' => 'funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
            ),
			array(
                'table' => 'exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo'
            ),
			array(
                'table' => 'usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => 'AuditoriaExame.codigo_usuario_inclusao = Usuario.codigo'
            ), 
			array(
                'table' => 'anexos_exames',
                'alias' => 'AnexosExames',
                'type' => 'INNER',
                'conditions' => 'AnexosExames.codigo_item_pedido_exame = ItemPedidoExame.codigo'
            ),            
        );



	    $imagens = array(
			'fields' => $fields,
			'joins' => $joins, 
		);

		return $imagens;
    }

    private function EmailNotificacao( $email, $nome_credenciado){
        $template = "envio_anexos_reprovados";
        $link     = "https://portal.rhhealth.com.br/portal/anexos";


        $MailerOutbox = ClassRegistry::init('MailerOutbox');
        return $MailerOutbox->enviaEmail(array(
            'link' => $link,
            'nome_credenciado' => $nome_credenciado
        ), "Anexos Reprovados", $template, $email);
    }
}