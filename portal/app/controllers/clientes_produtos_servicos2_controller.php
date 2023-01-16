<?php
class ClientesProdutosServicos2Controller extends AppController {

    public $name = 'ClientesProdutosServicos2';
    public $layout = 'cliente';
    public $uses = array('ClienteProdutoServico2', 'ClienteProduto');
	public $components = array('StringView', 'mailer.Scheduler');

	function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'adendo_contrato',
			'enviar_email_contrato',
			'salvar_adendo_contrato'
        ));
    }

	public function adendo_contrato() {
		$this->layout = 'default';

		$data_contrato = Comum::dataPorExtenso(date('d/m/Y'));
		$codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
		$this->set(compact('data_contrato', 'codigo_cliente'));
	}

	function salvar_adendo_contrato() {
		$codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];

		$cliente_produto = $this->ClienteProduto->find('first', array('fields' => array('codigo'),'conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_produto' => array(1,2))));
		$dados = $this->ClienteProdutoServico2->find('first', array('conditions' => array('codigo_cliente_produto' => $cliente_produto['ClienteProduto']['codigo'], 'codigo_servico' => 4)));
		$dados['ClienteProdutoServico2']['ip']						= $_SERVER['REMOTE_ADDR'];
		$dados['ClienteProdutoServico2']['browser']					= $_SERVER['HTTP_USER_AGENT'];
		$dados['ClienteProdutoServico2']['codigo_usuario_inclusao']	= $this->authUsuario['Usuario']['codigo'];
		$dados['ClienteProdutoServico2']['data_inclusao']			= date('Y-m-d H:i:s');

		if ($this->ClienteProdutoServico2->save($dados)) {
			$this->enviar_email_contrato($codigo_cliente);
			$this->Session->destroy();
		} else {
			echo 'Erro: ';
			echo Comum::implodeRecursivo(';',$this->ClienteProdutoServico2->invalidFields());
		}
		exit;
	}

	function enviar_email_contrato($codigo_cliente) {
		$this->loadModel('Usuario');
		$this->loadModel('ClienteContato');

		$dados = $this->Usuario->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));
		$todos_contatos = '';
		$contatos_cliente = $this->ClienteContato->find('all', array('fields' => array('DISTINCT descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));

		foreach ($contatos_cliente as $contato_cliente)
			$todos_contatos .= str_replace(' ', ';', $contato_cliente['ClienteContato']['descricao']).';';

		$todos_contatos = substr($todos_contatos, 0, strlen($todos_contatos) - 1);
		App::import('Component', array('StringView', 'Mailer.Scheduler'));

		$this->StringView = new StringViewComponent();
		$this->Scheduler  = new SchedulerComponent();
		$this->StringView->reset();
		$this->StringView->set(compact('dados', 'senha_usuario'));
		$content = $this->StringView->renderMail('email_renovacao_automatica', 'default');

		$options = array(
			'from' => 'portal@buonny.com.br',
			'sent' => null,
			'to'   => $todos_contatos,
			'subject' => 'Confirmacao de assinatura de contrato',
		);

		$retorno = $this->Scheduler->schedule($content, $options) ? true: false;
		return $retorno;
	}

	function gg_por_cliente() {
        $filtros = urldecode(Comum::descriptografarLink($this->data['Cliente']['hash']));
        $filtros = explode('|', $filtros);
        $filtros = array('ano' => $filtros[0], 'codigo_cliente' => $filtros[1]);
        $this->set('produtos_servicos', $this->ClienteProdutoServico2->produtosEServicos($filtros['codigo_cliente']));
    }
}