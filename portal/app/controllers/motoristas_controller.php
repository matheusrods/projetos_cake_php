<?php

class MotoristasController extends AppController {

    public $name = 'Motoristas';
    public $helpers = array('Buonny');
    var $uses = array('Cliente', 'Profissional', 'ProfissionalEndereco','ProfissionalContato', 'TipoContato','TipoRetorno',
                      'ClientEmpresa', 'EnderecoCidade', 'EnderecoEstado','Motorista','TipoCnh','Endereco','Cidade');
    var $components = array('RequestHandler', 'Importa');

     private function limpa_arquivos_antigos ($diretorio) {
        $key = 0 ;
        $ponteiro  = opendir($diretorio);
       while ( $nome_itens = readdir($ponteiro)) {
        $key++;
           if(substr($nome_itens, 0, 1) != '.') {
               if(substr($nome_itens, -4, 1) == '.') {
                    $itens[$key]['arquivos'] = $nome_itens;
                    $itens[$key]['data_ultima_modificao'] = filectime($diretorio.$nome_itens);
               }
           }
        }
        if(!empty($itens)) {
            foreach ($itens as $key => $item) {
                if($item['data_ultima_modificao'] < strtotime('- 30 days')) {
                   unlink($diretorio.$item['arquivos']);
                }
            }
        }
    }

    function importar_motorista() {

        $diretorio = APP.DS.'tmp'.DS;

        $this->limpa_arquivos_antigos($diretorio);

        ini_set('max_execution_time', 0);
        set_time_limit(0);
        $this->set('listaAcoes',$this->listaAcoes('Motorista'));
        $qtde_campos = 32;
        $quebra = chr(13) . chr(10);
        $this->pageTitle = 'Importar Motorista';
        // Postado, enviado Cliente e Arquivo
        if ($this->RequestHandler->isPost() &&
                !empty($this->data['Motorista']['codigo_cliente']) &&
                !empty($this->data['Motorista']['arquivo']['name'])) {

            $tipo = substr($this->data['Motorista']['arquivo']['name'], -3,3); // Tipo deve ser CSV
            $max_size = (1024*1024)*0.4194;// Máximo de 1 MB

            // Verifica o Tipo (CSV)  e Tamanho
            if ( $tipo === "csv" && $this->data['Motorista']['arquivo']['size'] < $max_size ) {

                // Procura o Cliente na Base de dados
                $cliente = $this->Cliente->carregar($this->data['Motorista']['codigo_cliente']);

                //Achou o Cliente
                if (!empty($cliente) && is_array($cliente)) {
                    $campos = array('CODIGO' => 'codigo_reg', 'NOME' => 'nome', 'ENDERECO' => 'codigo_endereco', 'NUMERO' => 'numero',
                        'COMPLEMENTO' => 'complemento', 'CIDADE' => 'cidade', 'ESTADO' => 'estado', 'CEP' => 'cep',
                        'TELEFONE_MOTORISTA'=>'telefone','CELULAR_MOTORISTA'=>'celular','RADIO_MOTORISTA'=>'radio',
                        'DATA_NASCIMENTO' => 'data_nascimento', 'CIDADE_NASCIMENTO' => 'codigo_endereco_cidade_naturalidade',
                        'ESTADO_NASCIMENTO' => 'estado_nascimento', 'NACIONALIDADE' => 'estrangeiro',
                        'CPF' => 'codigo_documento', 'RG_NUMERO' => 'rg', 'RG_ESTADO' => 'codigo_estado_rg',
                        'RG_DATA_EMISSAO' => 'rg_data_emissao', 'CNH_NUMERO' => 'cnh', 'CNH_TIPO' => 'codigo_tipo_cnh',
                        'CNH_DATA_PRIMEIRA' => 'data_primeira_cnh', 'CNH_DATA_VENCIMENTO' => 'cnh_vencimento',
                        'CNH_ESTADO' => 'codigo_endereco_estado_emissao_cnh', 'CNH_CODIGO_SEGURANCA' => 'codigo_seguranca_cnh',
                        'NOME_PAI' => 'nome_pai', 'NOME_MAE' => 'nome_mae', 'NOME_CONTATO' => 'nome_contato', 'TELEFONE' => 'telefone_contato',
                        'CELULAR' => 'celular_contato', 'RADIO' => 'radio_contato', 'EMAIL' => 'email_contato'
                    );

                    $separador = '----------------------------------------------------------------------';
                    if (strpos($this->data['Motorista']['arquivo']['name'], "*.*") !== 0) {
                        set_time_limit(120);
                        // Pega o local onde esta o arquivo importado
                        $destino = $this->data['Motorista']['arquivo']['tmp_name'];
                        $this->arq_erros_importacao = APP.'tmp'.DS.date('YmdHis').'.txt';
                        //$destino = WWW_ROOT."files".DS."arquivos".DS.$this->data['Motorista']['Motorista']['name'];  //+++
                        //if ( move_uploaded_file($this->data['Motorista']['arquivo']['tmp_name'], $destino) == TRUE ) {
                        // Importa o arquivo para um array como Component Importa.php
                        // $this->Importa->importar_csv($file_name,$parse_header=false,$delimiter="\t",$campos, $qtde_registro, $max_lines=1000,$length = 8000) {
                        $importando = $this->Importa->importar_csv($destino, true, ';', $campos, 31);

                        // Checa a consistencia dos dados
                        if($importando != 'A Quantidade de Items no Cabeçalho no arquivo CSV é incompátivel'){
                            $resultado = $this->validaDadosCsv($importando, $qtde_campos);

                            //Grava os ERROS
                            $gravar  = $separador.$quebra;
                            $gravar .= '    INICIO   DA  IMPORTAÇÃO'.$quebra;
                            $gravar .= $separador.$quebra;
                            $gravar .= 'ERROS DE REGISTROS DOS MOTORISTAS'.$quebra;
                            $errado = false;

                            foreach($resultado as $key=> $resul) {
                                $gravar .= 'Registro '. ($key+1)."\n".$resul;
                                if (strpos($resultado[$key],"RRO:")) {
                                    $errado = true;
                                }
                            }
                            //debug(strpos($resultado[$registro+1],"RRO:")); die;
                            //Mensagem ao cliente
                            if ($errado) {
                                $gravar .= $quebra.'------- Não será possivel gravar os registros acima com ERRO. -------'.$quebra;
                            }

                            //Grava os erros
                            $this->gravaArquivoLogImportacao($this->arq_erros_importacao,$gravar);

                            // Loop para checar no Banco de Dados e Gravar
                            // INICIO da Leitura dos registros sem erros
                            $erros=''; $mens =''; $gravar  = '';
                            for ($registro=0; $registro<count($importando); $registro++) {
                                $erros='';
                                //Verifica se veio da VALIDACAO => validaDadosCsv sem erros
                                $checando1 =  strpos($resultado[$registro],"RRO:");
                                $checando2 =  strpos($resultado[$registro],"BRIGATORIO:");
                                if ( ($checando1=='') && empty($checando2)) {

                                    // Busca pelo codigo
                                    $importando[$registro]['codigo_documento'] = str_pad(preg_replace('/\D/', '', $importando[$registro]['codigo_documento']),11,'0',STR_PAD_LEFT);
                                    $codigo_profissional = $this->Profissional->buscaPorCPF($importando[$registro]['codigo_documento']);

                                    //$codigo_profissional = $this->Profissional->find('first', array('fields'=>array('codigo'),'conditions'=>array('codigo_documento'=>$codigo_busca)));
                                    //debug($codigo_profissional['Profissional']['codigo']); die;
                                    if (!empty($codigo_profissional)) {
                                        $codigo_profissional_endereco = $this->ProfissionalEndereco->find('first', array('fields'=>array('codigo'),'conditions'=>array('codigo_profissional'=>$codigo_profissional['Profissional']['codigo'])));
                                        $codigo_profissional_contato = $this->ProfissionalContato->find('first', array('fields'=>array('codigo'),'conditions'=>array('codigo_profissional'=>$codigo_profissional['Profissional']['codigo'])));
                                    }

                                    //Buscar os codigos para substituir e incluir
                                    //ESTADOS
                                    $estados = $this->EnderecoEstado->combo();
                                    //
                                    $codigo_estado_rg = array_search(strtoupper($importando[$registro]['codigo_estado_rg']), $estados);
                                    if(empty($codigo_estado_rg)) {
                                        $erros .='RG_ESTADO = '.$importando[$registro]['codigo_estado_rg'] .' é inválido.'.$quebra;
                                    } else {
                                        $importando[$registro]['codigo_estado_rg'] = $codigo_estado_rg ;
                                    }
                                    // Cidade Nascimento
                                    $cidade_natural = $importando[$registro]['codigo_endereco_cidade_naturalidade'];
                                    $cidade_natural =  Comum::trata_nome(strtoupper($cidade_natural));
                                    $estado_natural =  strtoupper($importando[$registro]['estado_nascimento']);
                                    $codigo_cidade = $this->Cidade->pegaCodigo($cidade_natural,$estado_natural);
                                    
                                    $importando[$registro]['codigo_endereco_cidade_naturalidade'] = empty($codigo_cidade) ?  $codigo_cidade : null;
                                    
                                    $estado_nascimento = array_search(strtoupper($importando[$registro]['estado_nascimento']), $estados);
                                    if(empty($codigo_estado_rg)) {
                                        $erros .='ESTADO_NASCIMENTO = '.$importando[$registro]['estado_nascimento'] .' é inválido.'.$quebra;
                                    } else {
                                        $importando[$registro]['estado_nascimento'] = $estado_nascimento ;
                                    }
                                    //CNH Estado
                                    $codigo_endereco_estado_emissao_cnh = array_search(strtoupper($importando[$registro]['codigo_endereco_estado_emissao_cnh']), $estados);
                                    if(empty($codigo_estado_rg)) {
                                        $erros .='CNH_ESTADO = '.$importando[$registro]['codigo_endereco_estado_emissao_cnh'] .' é inválido.'.$quebra;
                                    } else {
                                        $importando[$registro]['codigo_endereco_estado_emissao_cnh'] = $codigo_endereco_estado_emissao_cnh ;
                                    }
                                    //Tipo CNH
                                    $tipo_cnh = $this->TipoCnh->find('list', array('fields'=>array('codigo', 'descricao')));
                                    $codigo_tipo_cnh =array_search(strtoupper($importando[$registro]['codigo_tipo_cnh']), $tipo_cnh);
                                    if(empty($codigo_tipo_cnh)) {
                                        $erros .='CNH_TIPO = '.$importando[$registro]['codigo_tipo_cnh'] .' é inválido.'.$quebra;
                                    } else {
                                        $importando[$registro]['codigo_tipo_cnh'] = $codigo_tipo_cnh ;
                                    }
                                    //Nacionalidade
                                    $tipo_nacao = array('BRASILEIRA','BRASILEIRO','ESTRANGEIRA','ESTRANGEIRO');
                                    $estrangeiro = strtoupper($importando[$registro]['estrangeiro']);
                                    if (!in_array($estrangeiro,$tipo_nacao) ) {
                                        $erros .='NACIONALIDADE '.$importando[$registro]['estrangeiro'] .' é inválida.'.$quebra;
                                    } else {
                                        if ($estrangeiro == $tipo_nacao[0] || $estrangeiro == $tipo_nacao[1]) {
                                            $importando[$registro]['estrangeiro'] = 0;  //BRASILEIRA
                                        } else {
                                            $importando[$registro]['estrangeiro'] = 1;  //ESTRANGEIRA
                                        }
                                    }

                                    // Endereco  = buscar => PELO CEP >> RUA, CIDADE e ESTADO';
                                    $importando[$registro]['cep'] = str_pad(str_replace('-', '', $importando[$registro]['cep']),8, '0', STR_PAD_LEFT);
                                    $busca_cep_endereco  = $this->Endereco->buscarEnderecoPeloCep($importando[$registro]['cep']);
                                    // if (empty($busca_cep_endereco)) {
                                    //     $erros .='CEP = '.$importando[$registro]['cep'] .' é inválido.'.$quebra;
                                    // } else {
                                    //     $importando[$registro]['codigo_endereco'] = $busca_cep_endereco['Endereco']['codigo'];
                                    // }
                                    $importando[$registro]['codigo_endereco'] = !empty($busca_cep_endereco) ? $busca_cep_endereco['Endereco']['codigo'] : null;

                                    // Tipo Retorno
                                    $tipo_retorno = $this->TipoRetorno->listar();
                                    $tipo_contato = $this->TipoContato->listar();
                                    $array_retorno = array();
                                    if(!empty($importando[$registro]['nome_contato'])) {
                                        $nome_contato = $importando[$registro]['nome_contato'];
                                        $conta=0;
                                        if (!empty($importando[$registro]['telefone_contato'])) {
                                            $telefone_retorno =array_search('TELEFONE', $tipo_retorno);
                                            $telefone_contato =array_search('RESIDENCIAL',$tipo_contato);

                                            $array_retorno[$conta][0] = $nome_contato;
                                            $array_retorno[$conta][1] = $telefone_retorno;
                                            $array_retorno[$conta][2] = $telefone_contato;
                                            //extrair DDD e DDI se tiver - Tira os espacos
                                            $importando[$registro]['telefone_contato'] = str_replace(' ', '', $importando[$registro]['telefone_contato']);
                                            $p0 = strpos(trim($importando[$registro]['telefone_contato']),'(');
                                            $p1 = strpos($importando[$registro]['telefone_contato'],')');
                                            //
                                            if ($importando[$registro]['estrangeiro']=='1') {
                                                $array_retorno[$conta][3] = trim(substr($importando[$registro]['telefone_contato'],$p1+1 ));
                                                $array_retorno[$conta][4] = trim(substr($importando[$registro]['telefone_contato'],$p0+1,2)); //DDD
                                                $array_retorno[$conta][5] = trim(substr( $importando[$registro]['telefone_contato'],0,$p0)); //DDI
                                            } else {
                                                $array_retorno[$conta][3] = trim(substr($importando[$registro]['telefone_contato'],$p1+1 ));
                                                $array_retorno[$conta][4] = substr($importando[$registro]['telefone_contato'],$p0+1,2); ///DDD
                                            }
                                            // Mensagem de Erro
                                            if (strlen($array_retorno[$conta][4])>2 ) {
                                                $erros .='TELEFONE = '.$importando[$registro]['telefone_contato'] .' é inválido.'.$quebra;
                                            }
                                            $conta++;
                                        }
                                        if (!empty($importando[$registro]['celular_contato'])) {
                                            $celular_motorista =array_search('CELULAR MOTORISTA', $tipo_retorno);
                                            $celular_contato =array_search('REFERENCIA',$tipo_contato);

                                            $array_retorno[$conta][0] = $nome_contato;
                                            $array_retorno[$conta][1] = $celular_motorista;
                                            $array_retorno[$conta][2] = $celular_contato;
                                            //extrair DDD e DDI se tiver - Tira os espacos
                                            $importando[$registro]['celular_contato'] = str_replace(' ', '', $importando[$registro]['celular_contato']);
                                            $p0 = strpos($importando[$registro]['celular_contato'],'(');
                                            $p1 = strpos($importando[$registro]['celular_contato'],')');


                                            if ($importando[$registro]['estrangeiro']=='1') {
                                                $array_retorno[$conta][3] = trim(substr($importando[$registro]['celular_contato'],$p1+1 ));
                                                $array_retorno[$conta][4] = trim(substr($importando[$registro]['celular_contato'],$p0+1,2)); //DDD
                                                $array_retorno[$conta][5] = trim(substr( $importando[$registro]['celular_contato'],0,$p0)); //DDI
                                            } else {
                                                $array_retorno[$conta][3] = trim(substr($importando[$registro]['celular_contato'],$p1+1 ));
                                                $array_retorno[$conta][4] = substr( $importando[$registro]['celular_contato'],$p0+1,2);
                                            }
                                            // Mensagem de Erro
                                            if (strlen($array_retorno[$conta][4])>2 ) {
                                                $erros .='CELULAR = '.$importando[$registro]['celular_contato'] .' é inválido.'.$quebra;
                                            }
                                            $conta++;
                                        }
                                        if (!empty($importando[$registro]['radio_contato'])) {
                                            $radio_motorista =array_search('RADIO', $tipo_retorno);
                                            $radio_contato =array_search('REFERENCIA',$tipo_contato);
                                            $array_retorno[$conta][0] = $nome_contato;
                                            $array_retorno[$conta][1] = $radio_motorista;
                                            $array_retorno[$conta][2] = $radio_contato;
                                            $array_retorno[$conta][3] = $importando[$registro]['radio_contato'];
                                            $conta++;
                                        }
                                        if (!empty($importando[$registro]['email_contato'])) {
                                            $email_motorista =array_search('E-MAIL', $tipo_retorno);
                                            $email_contato =array_search('REFERENCIA',$tipo_contato);
                                            $array_retorno[$conta][0] = $nome_contato;
                                            $array_retorno[$conta][1] = $email_motorista;
                                            $array_retorno[$conta][2] = $email_contato;
                                            $array_retorno[$conta][3] = $importando[$registro]['email_contato'];
                                        }
                                    }


                                    //Verifica se existe o Profissional
                                    if(empty($erros)) {
                                        // Acrescenta nas Tabelas no Profissional com os campos

                                        //*PROFISSIONAL
                                        // inclusao - Checagem do sistema
                                        $data['motorista_cpf'] = $importando[$registro]['codigo_documento'];
                                        $data['motorista_nome'] = $importando[$registro]['nome'];
                                        $data['estrangeiro'] = ($importando[$registro]['estrangeiro']==0);
                                        //TPessPessoa
                                        $data['nome'] = $importando[$registro]['nome'];
                                        //$data['logradouro'] = null//$importando[$registro]['codigo_endereco'];
                                        //$data['numero'] =  $importando[$registro]['numero'];
                                        //$data['complemento'] = $importando[$registro]['complemento'];
                                        $data['codigo_documento'] = $importando[$registro]['codigo_documento'];
                                        $data['rg'] = $importando[$registro]['rg'];
                                        $data['data_nascimento'] = $importando[$registro]['data_nascimento'];
                                        $data['usuario_adicionou'] = $this->data['Motorista']['codigo_cliente'];
                                        //
                                        $data['Profissional']['codigo_profissional_tipo'] = 3;
                                        $data['Profissional']['codigo_modulo'] = 2;
                                        $data['Profissional']['codigo_usuario_inclusao'] = $this->data['Motorista']['codigo_cliente'];
                                        $data['Profissional']['data_inclusao'] = date('Y-m-d H:i:s');
                                        $data['Profissional']['observacao'] = 'Importação de Dados pelo Usuario';
                                        // -- Dados Pessoais
                                        $data['Profissional']['codigo_documento'] = $importando[$registro]['codigo_documento']; //$key
                                        $data['Profissional']['nome'] = $importando[$registro]['nome'];
                                        $data['Profissional']['rg'] = $importando[$registro]['rg'];
                                        $data['Profissional']['codigo_estado_rg'] = $importando[$registro]['codigo_estado_rg'];
                                        $data['Profissional']['rg_data_emissao'] = trim($importando[$registro]['rg_data_emissao']);
                                        $data['Profissional']['codigo_endereco_cidade_naturalidade'] = $importando[$registro]['codigo_endereco_cidade_naturalidade'];
                                        $data['Profissional']['data_nascimento'] = trim($importando[$registro]['data_nascimento']);
                                        if(!empty($importando[$registro]['nome_pai'])) { $data['Profissional']['nome_pai'] = $importando[$registro]['nome_pai']; }
                                        $data['Profissional']['nome_mae'] = $importando[$registro]['nome_mae'];
                                        // -- CNH
                                        $data['Profissional']['cnh'] = $importando[$registro]['cnh'];
                                        $data['Profissional']['codigo_tipo_cnh'] = $importando[$registro]['codigo_tipo_cnh'];
                                        $data['Profissional']['cnh_vencimento'] = trim($importando[$registro]['cnh_vencimento']);
                                        $data['Profissional']['codigo_endereco_estado_emissao_cnh'] = $importando[$registro]['codigo_endereco_estado_emissao_cnh'];
                                        $data['Profissional']['codigo_seguranca_cnh'] = $importando[$registro]['codigo_seguranca_cnh'];

                                        //*PROFISSIONAL_ENDERECO
                                        //$data['ProfissionalEndereco']['codigo_profissional'] = '???'; //LastReg ??
                                        $data['ProfissionalEndereco']['codigo_usuario_inclusao'] = $this->data['Motorista']['codigo_cliente'];
                                        $data['ProfissionalEndereco']['data_inclusao'] = date('Y-m-d H:i:s');
                                        $data['ProfissionalEndereco']['codigo_tipo_contato'] = 1;
                                        $data['ProfissionalEndereco']['codigo_endereco'] = $importando[$registro]['codigo_endereco'];
                                        $data['ProfissionalEndereco']['complemento'] = $importando[$registro]['complemento'];
                                        $data['ProfissionalEndereco']['numero'] = $importando[$registro]['numero'];

                                        //*PROFISSIONAL_CONTATO
                                        for ($ct=0;$ct<count($array_retorno);$ct++) {
                                            //$data['ProfissionalContato'][$ct]['codigo_profissional'] = '???'; //LastReg ??
                                            $data['ProfissionalContato'][$ct]['data_inclusao'] = date('Y-m-d H:i:s');
                                            $data['ProfissionalContato'][$ct]['descricao'] =  $array_retorno[$ct][3];
                                            $data['ProfissionalContato'][$ct]['codigo_tipo_contato'] =  $array_retorno[$ct][2];
                                            $data['ProfissionalContato'][$ct]['codigo_tipo_retorno'] =  $array_retorno[$ct][1];
                                            $data['ProfissionalContato'][$ct]['ddi'] = (!empty($array_retorno[$ct][5]))?$array_retorno[$ct][5]:'';
                                            $data['ProfissionalContato'][$ct]['ddd'] = (!empty($array_retorno[$ct][4]))?$array_retorno[$ct][4]:'';
                                            $data['ProfissionalContato'][$ct]['nome'] =  $array_retorno[$ct][0];
                                            $data['ProfissionalContato'][$ct]['codigo_usuario_inclusao'] = $this->data['Motorista']['codigo_cliente'];
                                        }

                                        //Motorista - MONITORA
                                        $data['Motorista']['Nome'] = $importando[$registro]['nome'];
                                        $data['Motorista']['Nacionalidae'] = ($importando[$registro]['estrangeiro']==0)?'N':'S';
                                        $data['Motorista']['CNH_Validade'] = $importando[$registro]['cnh_vencimento'];
                                        $data['Motorista']['CNH'] = $importando[$registro]['cnh'];
                                        $data['Motorista']['RG'] = $importando[$registro]['rg'];
                                        $data['Motorista']['CPF'] = $importando[$registro]['codigo_documento'];
                                        $data['Motorista']['Telefone'] = $importando[$registro]['telefone'];
                                        $data['Motorista']['Celular'] = $importando[$registro]['celular'];
                                        //data['Motorista']['DDDTelefone'] = $importando[$registro]['ddd_telefone']; //+++
                                        //$data['Motorista']['DDDCelular'] = $importando[$registro]['ddd_celular']; //+++
                                        $data['Motorista']['ID_Radio'] = $importando[$registro]['radio'];
                                        $data['Motorista']['Data'] = date('Y-m-d H:i:s');

                                        //Salvar nas Tabelas (4 Tabelas)
                                        $this->Profissional->chamada_inclusao_motorista($data);
                                        //$cod = $this->Profissional->id;
                                        //$usuario = $this->Usuario->findById($this->Usuario->id);

                                        //Mensagem de Sucesso
                                        $mens .= "REGISTRO No ".($registro+1)." foi ACRESCENTADO".$quebra.$quebra ;                                        

                                    } else {
                                        //Escreve no arquivo de retorno ao Usuario para Download
                                        $mens .= $quebra."REGISTRO No ".($registro+1).' foi REJEITADO'.$quebra;
                                        // if(!empty($codigo_profissional)) {
                                        //     $mens .= "O Profissional Motorista de CPF ".$importando[$registro]['codigo_documento']." já está cadastrado no sistema.".$quebra ;
                                        // }
                                        // if (!empty($codigo_profissional_endereco)) {
                                        //     $mens .="O endereço se encontra cadastrado no sistema.".$quebra ;
                                        // }
                                        // if (!empty($codigo_profissional_contato)) {
                                        //     $mens .="O contato se encontra cadastrado no sistema.".$quebra ;
                                        // }
                                        // Acrescenta os erros
                                        $mens .= $erros;                                                                                
                                    }
                                }
                            } // FIM da Leitura dos registros sem erros

                            //Grava as MENSAGENS
                            $gravar .= $quebra.$quebra;
                            $gravar .= $separador.$quebra;
                            $gravar .= 'REGISTROS DE MOTORISTAS (Incluidos e Rejeitados)'.$quebra;
                            $gravar .= $separador.$quebra;
                            $gravar .= $mens.$quebra;
                            $gravar .= $separador.$quebra;
                            $gravar .= 'FIM   DA  IMPORTAÇÃO'.$quebra;
                            $gravar .= $separador.$quebra;
                            //Provisorio
                            //if(!empty($data)) { $gravar .= $quebra.print_r($data); }

                            //Escreve as Validacoes e Mensagens
                            $resposta = $this->gravaArquivoLogImportacao($this->arq_erros_importacao, $gravar);
                        }else{
                            $resposta = $this->gravaArquivoLogImportacao($this->arq_erros_importacao, $importando);
                        }

                        //Exporta o arquivo para download
                        if ($resposta) {
                            //Retornar ao Cliente o VALIDA e MENSAGENS
                            if( file_get_contents($this->arq_erros_importacao) ){
                                Configure::write('debug',0);
                                header("Content-Type: application/force-download");
                                header('Content-Disposition: attachment; filename="importacao_motorista.txt"');
                                echo file_get_contents($this->arq_erros_importacao);
                                unlink($this->arq_erros_importacao);
                                exit;
                            }
                        }
                    }
                } else {
                    $this->BSession->setFlash('no_client_user');
                }
            } else {
                $this->BSession->setFlash('arquivo_grande');
            }
        } else {
            // Informa os erros de Cliente = '' e Arquivo não informado
            if ($this->RequestHandler->isPost()) {
                if (empty($this->data['Motorista']['codigo_cliente'])) {
                    $this->BSession->setFlash('cliente_nao_informado');
                } else {
                    if (empty($this->data['Motorista']['arquivo']['name'])) {
                        $this->BSession->setFlash('no_file');
                    }
                }
            }
        }
    }

    private function gravaArquivoLogImportacao( $nome_arquivo, $data ){
        $fp = fopen($this->arq_erros_importacao, "a+");
        fwrite($fp, $data);
        if(!$fp)
            return false;
        fclose($fp);
        return true;
    }

    private function validaDadosCsv(&$dados_importados, $qtde = 0) {

        //$dados_importacao=array();
        //Posicoes dos campos do arquivo CSV => Array
        $campos = array('nome', 'codigo_endereco', 'numero', 'complemento', 'cidade', 'estado', 'cep','telefone','celular','radio','data_nascimento', 'codigo_endereco_cidade_naturalidade', 'estado_nascimento', 'estrangeiro','codigo_documento', 'rg', 'codigo_estado_rg', 'rg_data_emissao', 'cnh', 'codigo_tipo_cnh','data_primeira_cnh', 'cnh_vencimento', 'codigo_endereco_estado_emissao_cnh', 'codigo_seguranca_cnh', 'nome_pai','nome_mae', 'nome_contato', 'telefone_contato', 'celular_contato', 'radio_contato', 'email_contato');
        $campos_planilha = array('NOME', 'ENDERECO', 'NUMERO', 'COMPLEMENTO', 'CIDADE', 'ESTADO', 'CEP', 'TELEFONE_MOTORISTA','CELULAR_MOTORISTA','RADIO_MOTORISTA', 'DATA_NASCIMENTO', 'CIDADE_NASCIMENTO', 'ESTADO_NASCIMENTO', 'NACIONALIDADE', 'CPF', 'RG_NUMERO', 'RG_ESTADO', 'RG_DATA_EMISSAO', 'CNH_NUMERO', 'CNH_TIPO', 'CNH_DATA_PRIMEIRA', 'CNH_DATA_VENCIMENTO', 'CNH_ESTADO', 'CNH_CODIGO_SEGURANCA', 'NOME_PAI', 'NOME_MAE', 'NOME_CONTATO', 'TELEFONE', 'CELULAR', 'RADIO', 'EMAIL');
        $campos_obrigatorios = array('nome', 'codigo_endereco', 'numero', '', 'cidade', 'estado', 'cep', 'telefone', '', '', 'data_nascimento', 'codigo_endereco_cidade_naturalidade', 'estado_nascimento', 'estrangeiro', 'codigo_documento', 'rg', 'codigo_estado_rg', 'rg_data_emissao', 'cnh', 'codigo_tipo_cnh', 'data_primeira_cnh', 'cnh_vencimento', 'codigo_endereco_estado_emissao_cnh', 'codigo_seguranca_cnh', '', '', '', '', '',  '', '');
        $campos_tipo = array('TEXTO', 'TEXTO', 'NUMERO', 'TEXTO', 'TEXTO', 'LETRA2', 'GENERICO',  'TELEFONE', 'CELULAR', 'RADIO', 'DATA', 'TEXTO', 'LETRA2', 'TEXTO', 'CPF', 'TEXTO', 'LETRA2', 'DATA', 'NUMERO', 'LETRA','DATA', 'DATA', 'LETRA2', 'NUMERO', 'TEXTO', 'TEXTO', 'TEXTO', 'TELEFONE', 'CELULAR', 'RADIO', 'EMAIL');

        $erros = array();
        $quebra = chr(13) . chr(10);
        foreach ($dados_importados as $key1 => $dados) {
            $reg = $key1;
            $erros[$reg] = 'Registro No ' . $reg."\n";
            $msg = '';

            foreach ($campos_obrigatorios as $key => $valor) {

                // Checa a Obrigatoriedade
                if ($valor != '' && isset($dados[$campos_obrigatorios[$key]]) && $dados[$campos_obrigatorios[$key]] == '') {
                    $msg .='OBRIGATORIO: ' . strtoupper($campos_planilha[$key]) . " é um campo obrigatório." . $quebra;
                }
                //Checa o Tipo de dado no campo
                if (!empty($dados[$campos[$key]]) && (isset($dados[$campos_obrigatorios[$key]]) ) ) {
                    if ($campos_tipo[$key] == 'NUMERO') {
                        $dados_importados[$key1][$campos[$key]] = str_replace('.', '', str_replace('-', '', $dados[$campos[$key]]));
                        $msg .= (is_numeric($dados_importados[$key1][$campos[$key]])) ? "" : "ERRO: " . $dados_importados[$key1][$campos[$key]] . " <= $campos_planilha[$key] é um campo númerico.\n" ;
                    }
                    if ($campos_tipo[$key] == 'CPF') {
                        $msg .= (Comum::validarCPF($dados[$campos[$key]]))? "" : "ERRO: " . $dados[$campos[$key]] . " <= " . $campos_planilha[$key] . " é inválido.\n" ;
                    }
                    if ($campos_tipo[$key] == 'TEXTO' || $campos_tipo[$key] == 'GENERICO') {
                        $msg .= (strlen(trim($dados[$campos[$key]])) > 3) ? "" : "ERRO: " . $dados[$campos[$key]] . " <= " . $campos_planilha[$key] . " deve ter mais que 3 caracteres.\n" ;
                    }
                    if ($campos_tipo[$key] == 'LETRA2') {
                        $msg .= (strlen(trim($dados[$campos[$key]])) == 2) ? "" : "ERRO: " . $dados[$campos[$key]] . " <= " . $campos_planilha[$key] . " maior ou menor que 2 caracteres.\n" ;
                    }
                    if ($campos_tipo[$key] == 'DATA') {
                        $msg .= ($this->validaData($dados[$campos[$key]])) ? "" : "ERRO: " . $dados[$campos[$key]] . " <= " . $campos_planilha[$key] . " data inválida.\n" ;
                    }
                    if ($campos_tipo[$key] == 'EMAIL') {
                        $mail = Comum::validaEmail($dados[$campos[$key]]);
                        if (empty($mail)) {
                            $msg .= "ERRO: " . $dados[$campos[$key]] . " <= " . $campos_planilha[$key] . " e-mail inválido." ;
                        }
                    }
                    if ($campos_tipo[$key] == 'TELEFONE' || $campos_tipo[$key] == 'CELULAR') {
                        $phone = str_replace(' ', '', $dados[$campos[$key]]);
                        $phone = preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1)$2-$3", trim($phone));
                        if (empty($phone) || strlen($phone) > 16 || strlen($phone) < 10) {
                            $msg .= "ERRO: " . $dados[$campos[$key]] . " <= " . $campos_planilha[$key] . " telefone inválido. Formato: (xx) 12345-1234\n";
                        }
                    }
                }

                if (empty($msg)) {
                    $erros[$reg] = "Registro sem erros ou inconsistência. " . $quebra;
                } else {
                    $erros[$reg] = $msg . $quebra;
                }
            }

        }

        return $erros;
    }

    private function validaData($data) {
        $exp_regular = "/^[0-9]{2}[\.\/\-][0-9]{2}[\.\/\-][0-9]{4}/";
        return preg_match($exp_regular, $data) ? true : false;
        // VALIDA DATAS NOS FORMATOS: dd/mm/AAAA | dd-mm-AAAA | dd.mm.AAAA
    }


	public function listaAcoes($model_busca=null) {
		if (!empty($model_busca)) {
	 		return get_class_methods($model_busca);
	 	}
	}


}
?>