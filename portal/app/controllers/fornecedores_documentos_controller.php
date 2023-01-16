<?php
class FornecedoresDocumentosController extends AppController {
  public $name = 'FornecedoresDocumentos';
	public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
  var $uses = array('FornecedorDocumento',
      'TipoDocumento');
  
    function listagem_documentos_enviados($codigo_fornecedor){
        $this->layout = 'ajax';
      
        $documentos_enviados = $this->FornecedorDocumento->retorna_documentos_enviados($codigo_fornecedor);

        $this->set(compact('documentos_enviados','codigo_fornecedor'));
    }

  function listagem_documentos_pendentes($codigo_fornecedor){
    $this->layout = 'ajax';
      
      $documentos_pendentes = $this->FornecedorDocumento->retorna_documentos_pendentes($codigo_fornecedor);
      $this->set(compact('documentos_pendentes','codigo_fornecedor'));
  }

  function incluir($codigo_fornecedor) {

    if($this->RequestHandler->isPost()) {
      if(!empty($this->data['FornecedorDocumento']['caminho_arquivo']['name']) && $this->data['FornecedorDocumento']['caminho_arquivo']['error'] == '0') {

        $tipo_documento = $this->TipoDocumento->find('first', array('conditions' => array('codigo' => $this->data['FornecedorDocumento']['codigo_tipo_documento'])));

				$retorno = $this->_upload($this->data['FornecedorDocumento'], $this->data['FornecedorDocumento']['codigo_fornecedor'], str_replace(" ", "_", str_replace("*", "", strtolower(Comum::trata_nome($tipo_documento['TipoDocumento']['descricao'])))));

        if($retorno['upload']) {
          $dados = array(
            'FornecedorDocumento' => array(
              'codigo_fornecedor' => $this->data['FornecedorDocumento']['codigo_fornecedor'],
              'codigo_tipo_documento' => $this->data['FornecedorDocumento']['codigo_tipo_documento'],
              'data_validade' => $this->data['FornecedorDocumento']['data_validade'],
              'caminho_arquivo' => $retorno['nome'],
              'diretorio_file_server' => $retorno['url_arquivo']
              )
            );

          if ($this->FornecedorDocumento->incluir($dados)) {
            $this->BSession->setFlash('save_success');
            echo 1;
          } 
          else {
            $this->BSession->setFlash('save_error');
            $erros = $this->FornecedorDocumento->validationErrors;
            echo json_encode($erros);
          }
        } 
        else {
          $this->BSession->setFlash('save_error');
          $erros = array('caminho_arquivo' => $retorno['msg']);
          echo json_encode($erros);
        }
      }
      else{
        $this->BSession->setFlash('save_error');
        $erros = array('caminho_arquivo' => 'Informe o Arquivo!');
        echo json_encode($erros);
      }
      exit;
      
    }

    $tipo_documento = $this->TipoDocumento->find('list', array(
        'conditions' =>  array(
            'status' => 1, 
            'codigo NOT IN (
                SELECT codigo_tipo_documento 
                FROM '.$this->FornecedorDocumento->databaseTable.'.'.$this->FornecedorDocumento->tableSchema.'.'.$this->FornecedorDocumento->useTable.'
                WHERE status = 1 
                    AND codigo_fornecedor = '.$codigo_fornecedor.')'
        ), 
        'fields' => array('codigo', 'descricao')
        )
    );

    $this->set(compact('codigo_fornecedor','tipo_documento'));
  }

  function _upload($file, $codigo_fornecedor, $novo_nome) {

		$this->Upload->setOption('field_name', 'caminho_arquivo');            
		$this->Upload->setOption('accept_extensions', array('pdf','jpg','jpeg', 'png'));
		$this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
		$this->Upload->setOption('size_max', 5242880);
		$this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');

		$retorno = $this->Upload->fileServer($file);

		if (isset($retorno['error']) && !empty($retorno['error']) ){
			$chave = key($retorno['error']);
			return array('upload' => false, 'msg' => $retorno['error'][$chave]);
		} else {

			$nome_arquivo = $file['caminho_arquivo']['name'];

			return array(
				'upload' => true, 
				'msg' => $retorno['data'][$nome_arquivo]['message'], 
				'nome' => "fornecedor_" . $codigo_fornecedor . "_" . $novo_nome . "." . end(explode('.', $file['caminho_arquivo']['name'])),
				'url_arquivo' => $retorno['data'][$nome_arquivo]['path_url']
			);
		}
  }
  
  public function excluir($codigo) {
      
      if ($this->FornecedorDocumento->excluir($codigo)) {
          $this->BSession->setFlash('save_success');
          echo 1;
      } else {
          $this->BSession->setFlash('save_error');
          echo 0;
      }

      exit;
  }
}