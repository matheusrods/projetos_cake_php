<?php

class FornecedoresHistoricosController extends AppController {

    public $name = 'FornecedoresHistoricos';
    public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
    public $helpers = array('Html', 'Ajax', 'Highcharts','Buonny');
    public $uses = array(
        'FornecedorHistorico'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('lista_historico', 'incluir');
    }

    //listagem do historico
    function lista_historico($codigo_fornecedor) {
        $this->layout = 'ajax';

        $fields = array(
            'FornecedorHistorico.observacao',
            'Usuario.apelido',
            'FornecedorHistorico.codigo',
            'FornecedorHistorico.codigo_fornecedor', 
            'FornecedorHistorico.codigo_usuario_inclusao', 
            'FornecedorHistorico.codigo_usuario_alteracao', 
            'FornecedorHistorico.ativo', 
            'FornecedorHistorico.codigo_empresa', 
            'FornecedorHistorico.data_inclusao', 
            'FornecedorHistorico.data_alteracao', 
            'FornecedorHistorico.caminho_arquivo'
        );

        $joins  = array(
            array(
                'table' => 'usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => 'Usuario.codigo = FornecedorHistorico.codigo_usuario_inclusao',
            ),
        );

        $conditions = array('FornecedorHistorico.codigo_fornecedor' => $codigo_fornecedor);

        $historicos = $this->FornecedorHistorico->find('all', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins));

        // debug($historicos);exit;
        $this->set(compact('codigo_fornecedor','historicos'));
    }

    //incluir os anexos
    function incluir($codigo_fornecedor){

        if($this->RequestHandler->isPost()) {

            if(!empty($this->data['FornecedorHistorico']['caminho_arquivo']['name'])){

                $this->log('entrei no !empty', 'debug');

                $post_params = isset($this->data['FornecedorHistorico']['caminho_arquivo']) && !empty($this->data['FornecedorHistorico']['caminho_arquivo']) ? $this->data['FornecedorHistorico']['caminho_arquivo'] : null ;

                if(empty($post_params)){
                    $this->BSession->setFlash('save_error');
                    return;
                }

                $this->Upload->setOption('field_name', 'caminho_arquivo');            
                $this->Upload->setOption('accept_extensions', array('pdf','jpg','jpeg', 'png'));
                $this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
                $this->Upload->setOption('size_max', 5242880);
                $this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');

                $retorno = $this->Upload->fileServer($this->data['FornecedorHistorico']);

                if(!empty($this->data['FornecedorHistorico']['caminho_arquivo']['name']) && $this->data['FornecedorHistorico']['caminho_arquivo']['error'] == '0' && !empty($this->data['FornecedorHistorico']['observacao'])) {

                    // se ocorreu algum erro de comunicação com o fileserver
                    if (isset($retorno['error']) && !empty($retorno['error']) ){
                        // $erros = array('caminho_arquivo' => $retorno['msg']);
                        $chave = key($retorno['error']);
                        $retorno_erro['error'] = $retorno['error'][$chave];
                        // debug($retorno_erro);exit;
                        echo json_encode($retorno_erro);
                    }
                    else {

                        $nome_arquivo = $this->data['FornecedorHistorico']['caminho_arquivo']['name'];

                        unset($this->data['FornecedorHistorico']['caminho_arquivo']);

                        $this->data['FornecedorHistorico']['caminho_arquivo'] = $retorno['data'][$nome_arquivo]['path'];

                        $this->data['FornecedorHistorico']['ativo'] = 1;

                        if ($this->FornecedorHistorico->incluir($this->data)) {                
                            $this->BSession->setFlash('save_success');                
                            echo 1;
                        } 
                        else {
                            $this->BSession->setFlash('save_error');
                            $erros = $this->FornecedorDocumento->validationErrors;
                            echo json_encode($erros);
                        }
                    }
                }
                else{
                    $this->BSession->setFlash('save_error');
                    $erros = array(
                        'caminho_arquivo' => 'Informe o Arquivo!',
                        'observacao' => 'É necessario preencher a observacao'
                    );
                    echo json_encode($erros);
                }
                exit;
            } else {
                
                if(!empty($this->data['FornecedorHistorico']['observacao'])){
                
                    unset($this->data['FornecedorHistorico']['caminho_arquivo']['name']);
                    unset($this->data['FornecedorHistorico']['caminho_arquivo']['type']);
                    unset($this->data['FornecedorHistorico']['caminho_arquivo']['error']);
                    unset($this->data['FornecedorHistorico']['caminho_arquivo']['size']);
                    unset($this->data['FornecedorHistorico']['caminho_arquivo']);
                    $this->data['FornecedorHistorico']['ativo'] = 1;

                    if ($this->FornecedorHistorico->incluir($this->data)) {                
                        $this->BSession->setFlash('save_success');                
                        echo 1;
                    } 
                    else {
                        $this->BSession->setFlash('save_error');
                        $erros = $this->FornecedorDocumento->validationErrors;
                        echo json_encode($erros);
                    }
                }
                else{
                    $this->BSession->setFlash('save_error');
                    $erros = array(
                        'caminho_arquivo' => 'Informe o Arquivo!',
                        'observacao' => 'É necessario preencher a observacao'
                    );
                    echo json_encode($erros);
                }
                exit;
            }
        }
        $this->set(compact('codigo_fornecedor'));
    }
}