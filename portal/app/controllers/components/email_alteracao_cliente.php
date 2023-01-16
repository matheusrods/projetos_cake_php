<?php

class EmailAlteracaoClienteComponent extends Object {

    var $name = 'EmailAlteracaoCliente';
    
    var $components = array('StringView', 'mailer.Scheduler');

    //called before Controller::beforeFilter()
    function initialize(&$controller, $settings = array()) {
        $this->controller =& $controller;
    }

    function informaAlteracaoCliente($tipoEmail='inclusao_cliente', $cliente=null, $alteracoes=null) {
        $this->layout = 'email';
        $this->Cliente =& ClassRegistry::init('Cliente');
        $this->ClienteProduto =& ClassRegistry::init('ClienteProduto');
        $this->ClienteEndereco =& ClassRegistry::init('ClienteEndereco');

        $listaEmail = array(
            'adm.contratos@buonny.com.br',
            'nataly.arandas@buonny.com.br'
        );

        if (!$cliente) {
            $cliente = $this->Cliente->find('first', array(
                'conditions' => array(
                    'Cliente.codigo' => 543
                )
            ));
        }

        if (is_numeric($cliente)) {
            $cliente = $this->Cliente->find('first', array(
                'conditions' => array(
                    'Cliente.codigo' => $cliente
                )
            ));
        }
        //5464

        if (!$alteracoes) {
            $alteracoes = 'Teleco';
        }

// OK 1) Quando o cliente é cadastrado: e-mail informando novo código e os produtos.//     Ex: Cadastrado cliente código XXXXX, nos produtos Teleconsult, Buonny Sat
//
// OK 2) Quando a reativação de código//     Ex: Código XXXXX reativado nos produtos XXXX e XXXX
//
//OK 3) Quando há alteração de dados cadastrais como razão social endereço //     Ex: Alteração de razão social código XXXX
//
//OK 4) Quando há alteração de valores//     Ex: Alteração de valores no produto XXXXXX
//
//OK5) Quando o cliente é cancelado//    Ex: Cancelamento do código XXXX nos produtos XXX e XXX

        $codigo_cliente = $cliente['Cliente']['codigo'];

        $produtos = $this->ClienteProduto->find('list', array(
            'recursive' => 0,
            'fields' => array(
                'ClienteProduto.codigo', 'Produto.descricao', 'MotivoBloqueio.descricao',
            ),
            'conditions' => array(
                'ClienteProduto.codigo_cliente' => $codigo_cliente,
                //'ClienteProduto.codigo_motivo_bloqueio' => MotivoBloqueio::MOTIVO_OK,
            )
        ));

        $nome_cliente = $cliente['Cliente']['razao_social'];

        $documento = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cliente['Cliente']['codigo_documento']);

        $endereco_comercial = array_shift($this->ClienteEndereco->listaEnderecoByCodigoCliente($codigo_cliente, TipoContato::TIPO_CONTATO_COMERCIAL));

        $preTitulo = '';
        $deuTudoErrado = false;

        $mensagens = array();
        switch (strtolower($tipoEmail)) {
            case 'inclusao_cliente':
                // $alterações = (string) Produto.descricao
                $preTitulo = 'Cadastro de novo cliente';
                array_push($mensagens, 'Novo Cliente cadastrado no produto: ' . $alteracoes);
                break;
            case 'alteracao_dados': // razao_social || endereco_fiscal (comercial)
                $preTitulo = 'Alteração de dados do cliente';
                if ($alteracoes) {
                    if (in_array('endereco', $alteracoes)) {
                        array_push($mensagens, 'Foi realizada alteração do endereço comercial do cliente');
                    }
                    if (in_array('razao_social', $alteracoes)) {
                        array_push($mensagens, 'Foi realizada alteração da razão social do cliente');
                    }
                }
                break;
            case 'alteracao_valores':
                // $alterações = (array) [produto|servico]
                $preTitulo = 'Alteração de valores no produto do cliente';
                $produto = $alteracoes['produto']['descricao'];
                $servico = $alteracoes['servico']['descricao'];
                array_push($mensagens, "Alterado valores do serviço <strong>{$servico}</strong> no produto <strong>{$produto}</strong>");
                break;
            case 'cancelamento_produto':
                $preTitulo = "Cancelamento de produto";
                array_push($mensagens, 'Cancelado os seguintes produtos: ' . implode(', ', $alteracoes));
                break;
            case 'alteracao_produto':
                $preTitulo = "Alteração de produto";
                array_push($mensagens, 'Cancelado os seguintes produtos: ' . implode(', ', $alteracoes));
                break;
            case 'inclusao_produto':
                // $alterações = (string) Produto.descricao
                $preTitulo = 'Inclusao de produto';
                array_push($mensagens, 'Inclusão de novo produto: ' . $alteracoes);
                break;
            case 'alteracao_status':
                // $alterações = Cliente.codigo_status 1 / 0
                if ($alteracoes == 1) {
                    $preTitulo = 'Reativação do cliente';
                    array_push($mensagens, 'O Cliente foi reativado.');
                } else {
                    $preTitulo = 'Inativaçao do cliente';
                    array_push($mensagens, 'O Cliente foi inativado.');
                }
                break;
            default:
                $deuTudoErrado = true;
                break;
        }

        //implode(', ', $produtos['OK']));

        $subject = implode(': ', array($preTitulo, $codigo_cliente . ' - ' . $nome_cliente));

        $this->StringView->set(compact('cliente', 'tipoEmail', 'preTitulo', 'subject', 'mensagens', 'endereco_comercial'));

        $content = $this->StringView->renderMail('emails_alteracao_cliente', 'default');

        //echo $content;

        //$this->Scheduler->schedule($corpo_mail_bruto, $options);
        //$x = $this->Mailer->send($content, $options);
//        pr($x);
//        pr($this->Mailer);
//
//        exit;
        //$this->_send_email('esqueci_senha', $usuario, $this->data['Usuario']['email'], 'AlteraÃ§Ã£o de senha');
        //$this->Email->to = 'c@ralho.com';
        //$this->Email->send();
        foreach ($listaEmail as $email) {
            $options = array(
                'from' => 'portal@buonny.com.br',
                //'cc' => 'retorno.perfil@buonnny.com.br',
                'sent' => null,
                'to' => $email,
                'subject' => $subject,
            );
            $this->Scheduler->schedule($content, $options);
        }
        //exit(0);
    }

}