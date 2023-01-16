<?php

/**
 * Shell para importacao de arquivos de funcionarios via SFTP
 */
/*
App::import('Component', 'StringView');
App::import('Core', 'Controller');
App::import('Component', 'Email');
App::import('Lib', 'AppShell');
*/
class ImportarSftpShell extends Shell
{
    var $uses = array(
        'ImportacaoEstrutura',
        'ImportacaoSftp',
        'ImportacaoSftpArquivo',
        'StatusTransferencia',
        'LogImportacaoSftpArquivo',
        'LogImportacaoSftp',
        'IntUploadCliente',
        'MapLayout'
    );

    var $file_server_path = '/home/sistemas/rhhealth/samba-share/arquivos/importacao_sftp';

    public function main()
    {
        echo "*******************************************************************\n";
        echo "*      importar arquivos do SFTP para carga de funcionarios   	 \n";
        echo "*      cake/console/cake -app ./app importar_sftp transferir       \n";
        echo "*      cake/console/cake -app ./app importar_sftp importar         \n";
        echo "*      cake/console/cake -app ./app importar_sftp processar        \n";
        echo "*******************************************************************\n";
    }

    private function im_running($metodo,$paralelismo = 1) {
        if (PHP_OS!='WINNT') {
            $cmd = shell_exec("ps aux | grep 'importar_sftp {$metodo}'");
            $ret = substr_count($cmd, 'cake.php -working') > $paralelismo;
        } else {
            $cmd = `tasklist /v | findstr /R /C:"importar_sftp {$metodo}"`;
            $ret = substr_count($cmd, 'cake\console\cake') > $paralelismo;
        }
    }

    /**
     * Obtem lista de arquivos a serem processados (via ImportacaoSftp);
     * Registra arquivo transferido (via ImportacaoSftpArquivo)     *
     * Registra log de transferencia SFTP(via LogTransferenciaSftp)
     */
    public function transferir()
    {
        if ($this->im_running("transferir")) {
            echo "Já existe importação em andamento"."\n";
            exit;
        }

        $sftps = $this->ImportacaoSftp->find('all', array('ImportacaoSftp.sftp_host'));

        foreach ($sftps as $item) { // LOOP SFTPs

            $importacao_sftp = $item['ImportacaoSftp'];

            echo "Iniciando transferencias do SFTP: " . $importacao_sftp['codigo'] . "\n";

            $log_importacao_sftp = array(
                'codigo_importacao_sftp' => $importacao_sftp['codigo'],
                'data_transferencia' => date('Y-m-d H:i:s')
            );

            try {

                // CAMINHO DO ARQUIVO: CLIENTE/ANO/MES/DIA
                $base_path = '/' . $importacao_sftp['codigo_cliente'] . '/' .
                    date('Y') . '/' . date('m') . '/' . date('d') . '/';

                if (!file_exists($this->file_server_path . $base_path)) {
                    mkdir($this->file_server_path . $base_path, 0777, true);
                }

                $sftp_server = $importacao_sftp['host'];
                $sftp_login = $importacao_sftp['login'];
                $sftp_secret = $importacao_sftp['secret'];
                $sftp_port = $importacao_sftp['port'];

                $sftp_pastas = $importacao_sftp['caminho_interno'];

                // CONEXAO SFTP
                $conn = ssh2_connect($sftp_server, $sftp_port);

                // AUTENTICACAO SFTP
                $auth = ssh2_auth_password($conn, $sftp_login, $sftp_secret);

                if (!$auth) {
                    throw new Exception("Não foi possível realizar a autenticação no SFTP do Cliente.");
                }

                // INIT SFTP SUBSYSTEM
                $sftp = ssh2_sftp($conn);

                $dir_sftp = "ssh2.sftp://{$sftp}/".$sftp_pastas;

                $handle = opendir($dir_sftp."./");
                while (false != ($nome_arquivo = readdir($handle))) { // LOOP ARQUIVOS

                    // skipping
                    if ($nome_arquivo == '.') continue;

                    if ($nome_arquivo == '..') continue;


                    //verifica se o arquivo já existe baixado
                    $arquivo_existente = $this->ImportacaoSftpArquivo->find('first',array('conditions' => array('codigo_importacao_sftp' => $importacao_sftp['codigo'], 'codigo_status_transferencia <> 2', 'nome_arquivo' => $nome_arquivo)));
                    if(!empty($arquivo_existente)) {
                        print "Arquivo: ". $nome_arquivo . " ja baixado. CodImportacaoSftp: " . $importacao_sftp['codigo']."\n";
                        continue;
                    }
                    //fim verificacao se o arquivo ja foi baixado com status diferente de falha
                    
                    echo "Transferencia do Arquivo: " . $nome_arquivo . " iniciada\n";

                    $importacao_sftp_arquivo = array(
                        'codigo_importacao_sftp' => $importacao_sftp['codigo'],
                        'nome_arquivo' => $nome_arquivo,
                        'caminho_arquivo' => $base_path,
                        'data_transferencia' => date('Y-m-d H:i:s'),
                        'data_inclusao' => date('Y-m-d H:i:s')
                    );

                    $log_importacao_sftp_arquivo = array(
                        'data_transferencia' => date('Y-m-d H:i:s'),
                        'data_inclusao' => date('Y-m-d H:i:s')
                    );

                    try {

                        // transfere arquivo
                        $filepath = $this->file_server_path . $base_path . $nome_arquivo;
                        $source_stream = fopen($dir_sftp . '/' . $nome_arquivo, 'r');
                        $local_stream = fopen($filepath, 'w');

                        $stream_copy_result = stream_copy_to_stream($source_stream, $local_stream);

                        fclose($source_stream);
                        fclose($local_stream);

                        if (!$stream_copy_result) { // FALHOU
                            // APAGA NO DESTINO
                            unlink($filepath);
                            throw new Exception('Erro durante a cópia do arquivo para o servidor de arquivos');

                        } else { // SUCESSO
                            $importacao_sftp_arquivo['codigo_status_transferencia'] = StatusTransferencia::ARQUIVO_PRONTO;

                        }// END IF STREAM COPY

                    } catch (Exception $e) {
                        $importacao_sftp_arquivo['codigo_status_transferencia'] = StatusTransferencia::ARQUIVO_TRANSFERENCIA_FALHOU;
                        $log_importacao_sftp_arquivo['observacao'] = 'Erro no método [transferir]. Exception: ' . $e->getMessage();
                        echo "Erro durante a transferencia do arquivo" . $e->getMessage() . "\n";
                    }

                    // registra importação do arquivo
                    $this->ImportacaoSftpArquivo->create('ImportacaoSftpArquivo');
                    $this->ImportacaoSftpArquivo->save($importacao_sftp_arquivo);

                    // logging
                    $log_importacao_sftp_arquivo['codigo_importacao_sftp_arquivo'] = $this->ImportacaoSftpArquivo->getInsertId();
                    $this->LogImportacaoSftpArquivo->create('LogImportacaoSftpArquivo');
                    $this->LogImportacaoSftpArquivo->save($log_importacao_sftp_arquivo);

                    // TODO APAGA DA ORIGEM
                    if($importacao_sftp_arquivo['codigo_status_transferencia']== StatusTransferencia::ARQUIVO_PRONTO) {
                        // ssh2_sftp_unlink($sftp,'ssh2.sftp://'.$sftp.'/'. $nome_arquivo); 
                        ssh2_sftp_unlink($sftp, $dir_sftp . '/' . $nome_arquivo); 
                    }

                } // END ARQUIVOS LOOP

            } catch (Exception $e) {
                $log_importacao_sftp['observacao'] = $e->getMessage();
                $log_importacao_sftp['codigo_status_transferencia'] = StatusTransferencia::ARQUIVO_TRANSFERENCIA_FALHOU;
                $log_importacao_sftp['data_inclusao'] = date('Y-m-d H:i:s');
                $this->LogImportacaoSftp->create($log_importacao_sftp);
                $this->LogImportacaoSftp->save();
            }

        }//END SFTP LOOP

    }

    /**
     * Obtem primeiro registro de arquivo ainda não foi processado
     * Inclui importacao_estrutura
     * Registra log
     */
    public function importar()
    {
        if ($this->im_running("importar")) {
            echo "Já existe importação em andamento"."\n";
            exit;
        }

        $importacao_sftp_arquivo = $this->ImportacaoSftpArquivo->find('first', array(
            'conditions' => array('codigo_status_transferencia' => StatusTransferencia::ARQUIVO_PRONTO),
            'order' => array('codigo')
        ));

        if(empty($importacao_sftp_arquivo)){
            echo "ImportacaoSftpArquivo não existe"."\n";
            exit;
        }

        $importacao_sftp = $this->ImportacaoSftp->find('first', array(
            'conditions' => array('codigo' => $importacao_sftp_arquivo['ImportacaoSftpArquivo']['codigo_importacao_sftp'])
        ));

        $log_importacao_sftp_arquivo = $this->LogImportacaoSftpArquivo->find('first', array(
            'conditions' => array('codigo_importacao_sftp_arquivo' => $importacao_sftp_arquivo['ImportacaoSftpArquivo']['codigo'])
        ));

        try {

            echo "Iniciando importacação\n";
            $res = $this->ImportacaoEstrutura->incluir(
                $this->file_server_path . $importacao_sftp_arquivo['ImportacaoSftpArquivo']['caminho_arquivo'],
                $importacao_sftp_arquivo['ImportacaoSftpArquivo']['nome_arquivo'],
                $importacao_sftp['ImportacaoSftp']['codigo_cliente'],
                1
            );

            if($res){
                echo "Importação realizada\n";
                $importacao_sftp_arquivo['ImportacaoSftpArquivo']['codigo_status_transferencia'] = StatusTransferencia::IMPORTACAO_ESTRUTURA_INCLUIDO;
            } else {
                throw new Exception('Falha durante a importacao do arquivo');
            }

        } catch (Exception $e) {
            echo "Importação falhou\n";
            $importacao_sftp_arquivo['ImportacaoSftpArquivo']['codigo_status_transferencia'] = StatusTransferencia::FALHA_IMPORTACAO_ESTRUTURA;
            $log_importacao_sftp_arquivo['LogImportacaoSftpArquivo']['observacao'] = $e->getMessage();
        }

        $importacao_sftp_arquivo['ImportacaoSftpArquivo']['data_alteracao'] = date('Y-m-d H:i:s');
        $log_importacao_sftp_arquivo['LogImportacaoSftpArquivo']['data_alteracao'] = date('Y-m-d H:i:s');

        $this->ImportacaoSftpArquivo->atualizar($importacao_sftp_arquivo);
        $this->LogImportacaoSftpArquivo->atualizar($log_importacao_sftp_arquivo);

        echo "Importação fim\n";

    }

    /**
     * Obtem importacao_estrutura(s) a serem processados a partir de importacao_sftp_arquivo
     * Requisita ImportacaoEstrutura->importar()
     * Mantem importacao_sftp_arquivo
     */
    public function processar(){

        echo "######## INICIANDO PROCESSAMENTO ROBO SFTP ########\n";

        if ($this->im_running("processar",1)) {
            echo "Já existe processamento em andamento"."\n";
            exit;
        }

        $importacao_sftp_arquivo = $this->ImportacaoSftpArquivo->find('first', array(
            'conditions' => array('codigo_status_transferencia' => StatusTransferencia::IMPORTACAO_ESTRUTURA_INCLUIDO),
            'order' => array('codigo')
        ));

        if(empty($importacao_sftp_arquivo)){
            echo("Nenhum importacao_sftp_arquivo pronto\n");
            exit;
        }

        $importacao_sftp = $this->ImportacaoSftp->find('first', array(
            'conditions' => array('codigo' => $importacao_sftp_arquivo['ImportacaoSftpArquivo']['codigo_importacao_sftp']),
            'order' => array('codigo')
        ));

        $estruturas = $this->ImportacaoEstrutura->find('all',array(
            'conditions' => array(
                'codigo_grupo_economico' => $importacao_sftp['ImportacaoSftp']['codigo_grupo_economico'],
                'data_processamento' => null                ),
            'order' => array('codigo')
        ));

        $importacao_sftp_arquivo['ImportacaoSftpArquivo']['codigo_status_transferencia'] = StatusTransferencia::IMPORTACAO_ESTRUTURA_PROCESSADO;
        $this->ImportacaoSftpArquivo->atualizar($importacao_sftp_arquivo);

        foreach($estruturas as $item){
            // debug($item);
            
            $_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
            $_SESSION['Auth']['Usuario']['codigo'] = 1;
            $this->ImportacaoEstrutura->importar($item['ImportacaoEstrutura']['codigo']);
            
            // $codigo_importacao_estrutura = $item['ImportacaoEstrutura']['codigo'];
            // Comum::execInBackground(ROOT . '/cake/console/cake -app '. ROOT . DS . 'app importacao estrutura 1 1 '." {$codigo_importacao_estrutura}");
        }

        $importacao_sftp_arquivo['ImportacaoSftpArquivo']['codigo_status_transferencia'] = StatusTransferencia::IMPORTACAO_ESTRUTURA_PROCESSADO;
        $this->ImportacaoSftpArquivo->atualizar($importacao_sftp_arquivo);

        echo "######## FINALIANDO PROCESSAMENTO ROBO SFTP ########\n";

        //gerar alerta

    }

    /**
     * [sftp_folha_pagto metodo para buscar os dados no nosso sftp e configurado na tabela importacao_sftp]
     * @return [type] [description]
     */
    public function sftp_folha_pagto()
    {
        
        echo "################\n";
        echo "INICIANDO O PROCESSAMENTO DO SFTP\n";

        $conditions['ImportacaoSftp.ativo'] = 1;
        $conditions[] = "ImportacaoSftp.caminho_sftp IS NOT NULL";
        $conditions[] = "ImportacaoSftp.ambiente IS NOT NULL";


        //parametro passado na linha de comando
        $codigo_cliente = (isset($this->args[0])) ? $this->args[0] : null;

        if(!is_null($codigo_cliente)){
            $conditions['ImportacaoSftp.codigo_cliente'] = $codigo_cliente;
        }

        //busca todas as configuracoes ativas que usam nosso sftp
        $sftps = $this->ImportacaoSftp->find('all', array('conditions'=>$conditions));
        // debug($sftps);exit;
        if(!empty($sftps)) {

            //ordenacao para o processamento dos arquivos
            $order = array(
                'ITHEMPRESA',
                'ITHSETORES',
                'ITHCARGOS',
                'ITHCENTRORESULTADO',
                'ITHFUNCIONARIOS',
                'ITHFUNCIONARIOEMPRESA',
            );

            $path_base = "/home/sistemas/rhhealth/";
            $send_process = false;

            foreach($sftps AS $sftp)  {

                //variaveis auxiliares
                $codigo_cliente = $sftp['ImportacaoSftp']['codigo_cliente'];
                $codigo_empresa = $sftp['ImportacaoSftp']['codigo_empresa'];
                $codigo_usuario_inclusao = $sftp['ImportacaoSftp']['codigo_usuario_inclusao'];

                $process_code_files = array();

                //caminho completo
                $path_origem = $path_base.$sftp['ImportacaoSftp']['caminho_sftp'];
                $path_destino = $path_base.$sftp['ImportacaoSftp']['caminho_interno'];

                //verifica se o caminho de origem existe
                if(!is_dir($path_origem)) {
                    //dispara email
                    
                    //retorna um erro
                    echo "NAO FOI ENCONTRADO ESTE DIRETORIO DE ORIGEM SFTP: ".$path_origem."\n";
                    continue;
                }

                //verifica se o caminho de destino existe
                if(!is_dir($path_destino)) {
                    mkdir($path_destino.'/', 0777, true);
                }

                //pega o dado se tem conteudo no diretorio
                $empty_origem = ((count(glob("$path_origem/*")) === 0) ? true : false);

                //verifica se tem arquivos a serem processados
                if($empty_origem) {
                    //retorna um erro
                    echo "NAO EXISTE DADOS NESTE CAMINHO DE ORIGEM SFTP: ".$path_origem."\n";
                    continue;   
                }//fim empty_origem

                //pega os arquivos 
                $arquivos_origem = glob("$path_origem/*");

                //deixa na ordem os arquivos
                $path_arquivos_origem = array();
                foreach($arquivos_origem AS $arquivos) {
                    //pega o nome do arquivo
                    $arr_path_origem = explode("/",$arquivos);
                    $nome_arquivo = end($arr_path_origem);

                    //pega o dsname combinado
                    $arr_dsname = explode("_",$nome_arquivo);
                    $dsname = $arr_dsname[0];
                    $date = $arr_dsname[1];

                    foreach($order AS $key => $val) {
                        if($val == $dsname) {
                            $path_arquivos_origem[$date][$key] = array(
                                "path_origem" => $arquivos,
                                "nome_arquivo" => $nome_arquivo,
                                "dsname" => $dsname,
                                "order" => $key
                            );
                        }
                    }

                }//fim foreach
                sort($path_arquivos_origem);
                                
                //varre o array ordenado por data
                foreach($path_arquivos_origem AS $arr_arquivos_data){

                    //ordena o array pelo indice
                    ksort($arr_arquivos_data);

                    //varre os dados
                    foreach($arr_arquivos_data AS $dados) {

                        //monta para onde o arquivo de destino vai ficar 
                        $file_dest = $path_destino.DS.$dados['nome_arquivo'];
                        $rename = rename($dados['path_origem'], $file_dest);
                        if (!$rename) { // FALHOU

                            //EMAIL

                            // APAGA NO DESTINO
                            echo "ERRO AO COPIAR ARQUIVO DO DIRETORIO ORIGEM SFTP: ".$dados['path_origem']." PARA A ORIGEM: " . $file_dest."\n";
                            unlink($file_dest);
                            continue;

                        }//fim tratamento de erro

                        $dados_layout = $this->getLayout($dados['dsname'],$codigo_cliente);

                        //verifica se tem layout
                        if(!empty($dados_layout)) {

                            $codigo_map_layout = $dados_layout[0]['codigo'];
                            $tabela_referencia = $dados_layout[0]['tabela'];

                            $qtdLinhas = $this->contarLinhas($file_dest);

                            //monta o array para gravar na tabela de int_upload_cliente
                            $data = array(
                                'IntUploadCliente' => array(
                                    'codigo_cliente'              => $codigo_cliente,
                                    'codigo_empresa'              => $codigo_empresa,
                                    'nome_arquivo'                => $dados['nome_arquivo'],
                                    'caminho_arquivo'             => $file_dest,
                                    'qtd_linhas'                  => $qtdLinhas,
                                    'qtd_linhas_processadas'      => 0,
                                    'codigo_status_transferencia' => 1,
                                    'ativo'                       => 1,
                                    'tabela_referencia'           => $tabela_referencia,
                                    'codigo_map_layout'           => $codigo_map_layout,
                                    'codigo_usuario_inclusao'     => $codigo_usuario_inclusao,
                                )
                            );
                            
                            if (!$this->IntUploadCliente->incluir($data)) {
                                //email
                                
                                echo "Falha ao salvar arquivo, na tabela corretamente int_upload_cliente o arquivo de destino: ".$file_dest."\n";
                                continue;
                            }

                            $process_code_files[] = $this->IntUploadCliente->id;
                            $send_process = true;

                        }//fim if do layout
                        
                    }//fim foreach

                }//fim foreach arquivos movimentação
                
                //inicia o processo de processamento dos arquivos
                // if(!empty($process_code_files)) {
                //     foreach($process_code_files AS $code) {
                //         Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' importacao_layouts run_arquivo ' . $codigo_usuario_inclusao . ' ' . $code);
                //     }//fim processamento
                // }

            }//fim foreach

        }//fim validacao sftp


        if($send_process) {
            echo "INICIANDO O PROCESSAMENTO DOS ARQUIVOS DO SFTP\n";
            //executa os arquivos que estão na fila para processar
            Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' importacao_layouts run ' . $codigo_usuario_inclusao);
        }

        echo "FINALIZANDO O PROCESSAMENTO DO SFTP\n";
        echo "################\n";

    }//fim sftp_folha_pagto

    /**
     * [getLayout pega a tabela no banco que o arquivo vai ser processado]
     * @param  [type] $dsname [description]
     * @return [type]         [description]
     */
    public function getLayout($dsname, $codigo_cliente)
    {
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.map_layout_detalhe',
                'alias' => 'MapLayoutDetalhe',
                'type' => 'INNER',
                'conditions' => array('MapLayout.codigo = MapLayoutDetalhe.codigo_map_layout')
            )
        );

        //busca o layout pelo dsname
        $layout = $this->MapLayout->find('first',array('fields' => array('MapLayout.codigo AS codigo','MapLayoutDetalhe.tabela AS tabela'),'joins' => $joins,'conditions' => array('MapLayout.codigo_cliente' => $codigo_cliente,'MapLayout.dsname' => $dsname)));

        return $layout;

    }//fim getLayout

     public function contarLinhas($arquivo)
    {
        $stream = new SplFileObject($arquivo, 'r');
        $stream->seek(PHP_INT_MAX);
        $lines = $stream->key(); // ignore first line if not +1 
        
        return $lines;
    }



}

?>