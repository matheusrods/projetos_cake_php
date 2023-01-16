<?php
class PedidosController extends AppController {
    public $name = 'Pedidos';
    var $uses = array('Importar','Exame','Servico', 'Esocial','GrupoEconomicoCliente');
    var $helpers = array('Html', 'Form'); 

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('importar','registros_arquivo','listagem_registros'));

        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');
    }   
   
    function importar($codigo_cliente){
        //die('foobar');
        $this->loadModel('GrupoEconomico');
        $this->loadModel('ImportacaoPedidos');        

        $this->pageTitle = 'Importação Pedidos de Exames';
        if(!isset($codigo_cliente) && empty($codigo_cliente)) {
            $this->redirect('/');
        }
        if (!empty($this->data)) {

            if(preg_match('@\.(csv)$@i', $this->data['ImportacaoPedidos']['nome_arquivo']['name'])) {
                $path_destino = APP.'tmp'.DS;
                $arquivo_destino = $this->data['ImportacaoPedidos']['nome_arquivo']['name'];
                if(move_uploaded_file($this->data['ImportacaoPedidos']['nome_arquivo']['tmp_name'], $path_destino.$arquivo_destino )){

                    if ($this->ImportacaoPedidos->incluir($path_destino, $arquivo_destino, $codigo_cliente)) {
                        $this->BSession->setFlash('save_success');
                    } else {
                        $error = $this->ImportacaoPedidos->invalidFields();
                        $this->BSession->setFlash(array(MSGT_ERROR, $error['codigo']));
                    }
                } else {
                    $this->BSession->setFlash('save_error');
                }
            } else {
                $this->Importar->invalidate('nome_arquivo','Extensão inválida!');
                $this->BSession->setFlash('save_error');
            }
        }
        $this->GrupoEconomico->bindModel(array('belongsTo' => array('Cliente' => array('foreignKey' => 'codigo_cliente'))));
        $grupo_economico = $this->GrupoEconomico->findByCodigoCliente($codigo_cliente);
        $conditions = array('codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo']);
        $this->ImportacaoPedidos->bindModel(array('belongsTo' => array('StatusImportacao' => array('foreignKey' => 'codigo_status_importacao'))));
        $arquivos_importados = $this->ImportacaoPedidos->find('all', compact('conditions'));
        $this->set(compact('arquivos_importados', 'grupo_economico'));
    }

    function registros_arquivo($codigo_cliente, $codigo_arquivo_importado) {
    	
        $this->pageTitle = 'Importação de Pedidos - Registros do Arquivo';
        $this->loadModel('ImportacaoPedidos');
        if ($this->RequestHandler->isPost()) {
            $this->log(ROOT . '/cake/console/cake -app '. ROOT . DS . 'app importacao pedidos '."{$_SESSION['Auth']['Usuario']['codigo_empresa']} {$_SESSION['Auth']['Usuario']['codigo']} {$codigo_arquivo_importado}",'debug');
            Comum::execInBackground(ROOT . '/cake/console/cake -app '. ROOT . DS . 'app importacao pedidos '."{$_SESSION['Auth']['Usuario']['codigo_empresa']} {$_SESSION['Auth']['Usuario']['codigo']} {$codigo_arquivo_importado}");
            $this->redirect(array('action' => 'importar', $codigo_cliente));
        }
        $pedidos = $this->ImportacaoPedidos->carregar($codigo_arquivo_importado);
        $this->set(compact('pedidos'));
    }

    function listagem_registros($codigo_arquivo_importado) {
        $this->loadModel('ImportacaoPedidos');
        $this->loadModel('ImportacaoPedidosRegistros');
        $conditions = array(
            'codigo_arquivo_importado' => $codigo_arquivo_importado,
        );
        $this->paginate['ImportacaoPedidosRegistros'] = array(
            'conditions' => $conditions,
            'limit' => 100,
            'order' => 'nome_funcionario',
            'extra' => array('importacao' => true)
        );
        $registros = $this->ImportacaoPedidosRegistros->find('All');
        //debug($registros);
        //$registros = $this->paginate('ImportacaoPedidosRegistros');
        //echo "<pre>";print_r($registros);die();
        $importacao_pedidos = $this->ImportacaoPedidos->carregar($codigo_arquivo_importado);
        $codigo_status_importacao = $importacao_pedidos['ImportacaoPedidos']['codigo_arquivo_importado'];
        $validaCadastrosRegistro = array();/*$this->ImportacaoAtestadosRegistros->alertasRegistrosCadastros($registros,$codigo_status_importacao);*/
        $alertas = array();/*$validaCadastrosRegistro['alertas'];*/
        $depara = array();/*$this->ImportacaoAtestadosRegistros->depara();*/
        $titulos = array();/*$this->ImportacaoAtestadosRegistros->titulos();*/
        $validacoes = array();/*$validaCadastrosRegistro['validacoes'];*/
        $this->set(compact('registros', 'alertas', 'depara', 'titulos', 'validacoes', 'codigo_status_importacao'));
    }
}    