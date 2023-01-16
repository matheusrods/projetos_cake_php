<?php
class ImportacaoLayoutsController extends AppController
{

    public $name = 'ImportacaoLayouts';
    public $layout = 'importacao_layouts';
    public $components = array('Filtros', 'RequestHandler', 'ExportCsv', 'Upload');
    public $helpers = array('Html', 'Ajax', 'Buonny', 'Ithealth');
    public $uses = array(
        'MapLayout',
        'MapLayoutDetalhe',
        'IntUploadCliente',
        'IntClienteCargos',
        'IntClienteEmpresa',
        'IntClienteSetores',
        'IntClienteCentroResultado',
        'IntClienteFuncionarios',
        'IntClienteFuncionariosEmpresa'
    );

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }

    /**
     * Redirect to self index method
     * 
     * @returns
     */
    public function para_o_inicio()
    {
        return $this->redirect(array('action' => 'index'));
    }

    /**
     * Get authenticated user
     * 
     * @return array
     */
    public function auth() {
        return $_SESSION['Auth']['Usuario'];
    }

    /**
     * Tela inicial da listagem de arquivos
     * 
     * @return view
     */
    public function index()
    {
        $this->pageTitle = "Uploads";
    }

    /**
     * Listagem de arquivos
     * 
     * @return view
     */
    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->IntUploadCliente->name);
        $authUsuario = $this->BAuth->user();

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            if(empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }
        
        $data = array();
        $codigo_cliente = '';
        if (!empty($filtros['codigo_cliente'])) {

            $codigo_cliente = $filtros['codigo_cliente'];

            $joins = array(
                array(
                    'table' => 'RHHealth.dbo.status_transferencia',
                    'alias' => 'StatusTransferencia',
                    'type' => 'INNER',
                    'conditions' => array('StatusTransferencia.codigo = IntUploadCliente.codigo_status_transferencia')
                )
            );

            $contagem_registros_processados = "(CASE 
                                                when [IntUploadCliente].tabela_referencia = 'int_cliente_empresa' then 
                                                    (select count(*) from int_cliente_empresa where codigo_int_upload_cliente = [IntUploadCliente].codigo and codigo_status_transferencia <> 3 )
                                                when [IntUploadCliente].tabela_referencia = 'int_cliente_setores' then 
                                                    (select count(*) from int_cliente_setores where codigo_int_upload_cliente = [IntUploadCliente].codigo and codigo_status_transferencia <> 3 )
                                                when [IntUploadCliente].tabela_referencia = 'int_cliente_cargos' then 
                                                    (select count(*) from int_cliente_cargos where codigo_int_upload_cliente = [IntUploadCliente].codigo and codigo_status_transferencia <> 3 )
                                                when [IntUploadCliente].tabela_referencia = 'int_cliente_funcionarios' then 
                                                    (select count(*) from int_cliente_funcionarios where codigo_int_upload_cliente = [IntUploadCliente].codigo and codigo_status_transferencia <> 3 )
                                                when [IntUploadCliente].tabela_referencia = 'int_cliente_centro_resultado' then 
                                                    (select count(*) from int_cliente_centro_resultado where codigo_int_upload_cliente = [IntUploadCliente].codigo and codigo_status_transferencia <> 3 )
                                                when [IntUploadCliente].tabela_referencia = 'int_cliente_funcionarios_empresa' then 
                                                    (select count(*) from int_cliente_funcionarios_empresa where codigo_int_upload_cliente = [IntUploadCliente].codigo and codigo_status_transferencia <> 3 )
                                                else '0' end ) as total_processado";
            $validacao_erro = "(CASE
                    WHEN [IntUploadCliente].tabela_referencia = 'int_cliente_empresa' THEN
                           (SELECT count(*) FROM int_cliente_empresa WHERE codigo_int_upload_cliente = [IntUploadCliente].codigo AND codigo_status_transferencia = 6)
                    WHEN [IntUploadCliente].tabela_referencia = 'int_cliente_setores' THEN
                           (SELECT count(*) FROM int_cliente_setores WHERE codigo_int_upload_cliente = [IntUploadCliente].codigo AND codigo_status_transferencia = 6 )
                    WHEN [IntUploadCliente].tabela_referencia = 'int_cliente_cargos' THEN
                           (SELECT count(*) FROM int_cliente_cargos WHERE codigo_int_upload_cliente = [IntUploadCliente].codigo AND codigo_status_transferencia = 6 )
                    WHEN [IntUploadCliente].tabela_referencia = 'int_cliente_funcionarios' THEN
                           (SELECT count(*) FROM int_cliente_funcionarios WHERE codigo_int_upload_cliente = [IntUploadCliente].codigo AND codigo_status_transferencia = 6 )
                    WHEN [IntUploadCliente].tabela_referencia = 'int_cliente_centro_resultado' THEN
                           (SELECT count(*) FROM int_cliente_centro_resultado WHERE codigo_int_upload_cliente = [IntUploadCliente].codigo AND codigo_status_transferencia = 6 )
                    WHEN [IntUploadCliente].tabela_referencia = 'int_cliente_funcionarios_empresa' THEN
                           (SELECT count(*) FROM int_cliente_funcionarios_empresa WHERE codigo_int_upload_cliente = [IntUploadCliente].codigo AND codigo_status_transferencia = 6 )
                    ELSE '0'
                END) AS erro";

            $conditions = $this->IntUploadCliente->converteFiltroEmCondition($filtros);
            $this->paginate['IntUploadCliente'] = array(
                'joins' => $joins,
                'conditions' => $conditions,            
                'fields' => array('IntUploadCliente.*','StatusTransferencia.descricao',$contagem_registros_processados,$validacao_erro),
                'order' => array('IntUploadCliente.codigo DESC'),
                'limit' => 50,
            );


            // debug($this->IntUploadCliente->find('all',$this->paginate['IntUploadCliente']));exit;

            $data = $this->paginate('IntUploadCliente');
        }

        // debug($data);exit;
        // $statuses = $this->IntUploadCliente->status;
        $this->set(
            compact(
                'data',
                'statuses',
                'codigo_cliente'
            )
        );
    }

    /**
     * Output json data
     * 
     * @param array $data
     * @returns
     */
    private function json($data)
    {
        echo json_encode($data);
        exit;
    }

    /**
     * Obtêm um arquivo por upload
     * 
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    private function file($name, $default = false)
    {
        return isset($_FILES[$name]) ? $_FILES[$name] : $default;
    }

    /**
     * Obtêm a extensão de um arquivo
     * 
     * @return string
     */
    protected function getFileExtension($file)
    {
        return strtolower(end((explode(".", $file))));
    }

    /**
     * Processa arquivo, verificando sua validade
     * 
     * @throws Exception
     * @return array
     */
    protected function process($file)
    {
        $ext = $this->getFileExtension($file['name']);

        if ($ext != "csv") {
            throw new Exception(
                "Formato de arquivo inválido."
            );
        }
        $hasOldFile = $this->IntUploadCliente->find('first', array('conditions' => array('nome_arquivo' => $file['name'], 'ativo' => 1,'codigo_status_transferencia IN (1,3,4,5,9)')));
        if ($hasOldFile != false) {
            throw new Exception(
                utf8_decode("O arquivo: {$file['name']} já está na fila de processamento")
            );
        }

        $layouts         = $this->MapLayout->find('all', array('conditions' => array('ativo' => 1)));
        $valido          = false;
        $valorComparacao = (explode("_", $file['name']));
        $valorComparacao = $valorComparacao[0];
        $mapLayout       = array();
        foreach ($layouts as $layout) {
            if (strtoupper($valorComparacao) == strtoupper($layout['MapLayout']['dsname'])) {
                $valido    = true;
                $mapLayout = $layout['MapLayout'];
                break;
            }
        }

        if ($valido == false) {
            throw new Exception(
                "Não existe um layout definido para o valor: {$valorComparacao}"
            );
        }

        return $mapLayout;
    }

    /** 
     * Upload do arquivo no storage
     * 
     * @param array $file
     * @param int $client
     * @return string
     */
    private function upload($file, $client)
    {
        $nome_arquivo = strtolower($file['name']);
        $uploadDir = APP . "tmp" . DS . "folha_pagto";
        $dirComCliente = $uploadDir . DS . $client;

        if (is_dir($dirComCliente) == false) {
            mkdir($dirComCliente);
        }
        $destino = $dirComCliente . DS . $nome_arquivo;
        if (is_file($destino) == true) {
            unlink($destino);
        }

        $resultado = move_uploaded_file($file['tmp_name'], $destino);

        if ($resultado == false) {
            throw new Exception(
                "Falha ao realizar upload do arquivo, por favor tente novamente"
            );
        }

        return $destino;
    }

    public function contarLinhas($arquivo)
	{
        $stream = new SplFileObject($arquivo, 'r');
		$stream->seek(PHP_INT_MAX);
        $lines = $stream->key(); // ignore first line if not +1 
        
        return $lines;
	}

    /**
     * Inclui arquivo para upload
     * 
     * @return string
     */
    public function incluir()
    {
        try {
            $file = $this->file("arquivo");
            $codigoCliente = isset($_POST['codigo_cliente']) ? $_POST['codigo_cliente'] : false;
            if (!$file || !$codigoCliente) {
                throw new Exception(
                    "Arquivo inválido"
                );
            }

            $mapLayout = $this->process($file);
            $path      = $this->upload($file, $codigoCliente);

            $mapLayoutDetalhes = $this->MapLayoutDetalhe->find('all',  array('conditions' => array('codigo_map_layout' => $mapLayout['codigo'], 'ativo' => 1)));
            if (!$mapLayoutDetalhes) {
                throw new Exception(
                    "O layout para este arquivo não possui colunas vinculadas"
                );
            }
            // debug($mapLayoutDetalhes);exit;
            $tabela_referencia = $mapLayoutDetalhes[0]['MapLayoutDetalhe']['tabela'];
            $qtdLinhas = $this->contarLinhas($path);
            $data = array(
                'IntUploadCliente' => array(
                    'codigo_cliente'              => $codigoCliente,
                    'codigo_empresa'              => $mapLayout['codigo_empresa'],
                    'nome_arquivo'                => $file['name'],
                    'caminho_arquivo'             => $path,
                    'qtd_linhas'                  => $qtdLinhas,
                    'qtd_linhas_processadas'      => 0,
                    'codigo_status_transferencia' => 1,
                    'estado'                      => $this->IntUploadCliente->getStatus(1),
                    'ativo'                       => 1,
                    'tabela_referencia'           => $tabela_referencia,
                    'codigo_map_layout'           => $mapLayout['codigo'],
                )
            );
            $user = $this->auth();
            $salvo = $this->IntUploadCliente->incluir($data);

            if (!$salvo) {
                throw new Exception(
                    "Falha ao salvar arquivo, por favor tente novamente mais tarde"
                );
            }

            $codigo_int_upload_cliente = $this->IntUploadCliente->id;
            // debug($codigo_int_upload_cliente);exit;
            Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' importacao_layouts run_arquivo ' . $user['codigo'] . ' ' . $codigo_int_upload_cliente);
            
            return $this->json(array('success' => true));
        } catch (Exception $e) {
            return $this->json(array(
                'errors' => array(utf8_encode($e->getMessage())),
            ));
        }
    }

    public function download($codigo)
    {
        $uploadCliente = $this->IntUploadCliente->carregar($codigo);
        if(!$uploadCliente) {
            return $this->cakeError('error404');
        }
        $path = $uploadCliente['IntUploadCliente']['caminho_arquivo'];
        $nome = $uploadCliente['IntUploadCliente']['nome_arquivo'];

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$nome.'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        ob_clean();
        flush();
        readfile($path);
        exit();
    }

    public function troca_status($codigo)
    {
        try {
            $data                 = $this->IntUploadCliente->carregar($codigo);
            $data['IntUploadCliente']['ativo'] = !$data['IntUploadCliente']['ativo'];
            $status               = $data['IntUploadCliente']['ativo'];
            $this->IntUploadCliente->atualizar($data);
            return $this->json($status);
        } catch (\Exception $e) {
            return $this->json(false);
        }
    }

    public function download_erros($codigo)
    {
        $uploadCliente = $this->IntUploadCliente->carregar($codigo);
        if(!$uploadCliente) {
            return $this->cakeError('error404');
        }


        ####################### LAYOUT
        
        //busca o map_layout para organizar os dados no layout configurado
        $codigo_map_layout = $uploadCliente['IntUploadCliente']['codigo_map_layout'];
        $mapLayout = array();
        
        $cabecalho = "";
        $array_cabecalho = array();

        if(!empty($codigo_map_layout)) {
            $mapLayout = $this->MapLayout->with_bind($codigo_map_layout);

            //verifica se existe o detalhe
            if(isset($mapLayout['MapLayoutDetalhe'])) {
                //verifica se tem valor no detalhe
                if(!empty($mapLayout['MapLayoutDetalhe'])) {
                    //varre os detalhes
                    foreach($mapLayout['MapLayoutDetalhe'] AS $campos) {
                        //pega somenta os ativos
                        if($campos['ativo'] == 1) {
                            //set o cabecalho
                            $array_cabecalho[trim($campos['campo_saida'])] =  strtoupper(trim($campos['campo_saida']));
                        }
                    }//fim foreach
                    $array_cabecalho['observacao'] = 'OBSERVACAO';

                    //verifica se tem cabecalho
                    if($mapLayout['MapLayout']['ignora_primeira_linha'] != 1) {
                        //monta o cabecao
                        $cabecalho = implode(";", $array_cabecalho)."\n";
                    }

                }//fim detalhes em branco
            }//fim mapLayout
        }//fim codigo_map_layout

        if(empty($array_cabecalho)) {
            return $this->cakeError('error404');
        }

        ###################### DADOS
        $tabelaReferencia = $uploadCliente['IntUploadCliente']['tabela_referencia'];
        $dados = '';
        if(!empty($tabelaReferencia)) {
            $dados_erros = $this->IntUploadCliente->getDadosErros($codigo, $tabelaReferencia);
            // debug($dados_erros);exit;

            //verifica se tem dados
            if(!empty($dados_erros)) { 

                $nome_arquivo = $uploadCliente['IntUploadCliente']['nome_arquivo']."_".date('YmdHis').'erro.csv';
                //headers
                ob_clean();
                header('Content-Encoding: ISO-8859-1');
                header('Content-type: text/csv; charset=ISO-8859-1');
                header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
                header('Pragma: no-cache');

                echo $cabecalho;

                //varre os dados
                foreach($dados_erros as $errors) {
                    $dados = '';
                    $erro = $errors[0];
                    // debug($erro);
                    $linha = array();

                    //varre o cabecalho com os indices cos campos
                    foreach($array_cabecalho AS $campos => $val) {

                        switch ($campos) {
                            case 'codigo_externo_centro_resultado':
                                $campos = 'codigo_externo_centro_resultad';
                                break;
                            case 'numero_registro_chefia_imediata':
                                $campos = 'numero_registro_chefia_imediat';
                                break;
                        }

                        $linha[] = $erro[$campos];
                    }

                    $dados .= implode(';',$linha)."\n";
                    echo utf8_decode($dados);
                }
            }//fim verificacao se tem dados
        }
        exit();
    }
}
