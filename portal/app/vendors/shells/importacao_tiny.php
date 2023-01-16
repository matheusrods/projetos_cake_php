<?php
/**
 * Script para importar os clientes do ERP Tiny
 * 
 * Modo de usar:
 * 
 *  é necessário colocar os arquivos do formato csv separado por ";" no diretorio:
 *      /app/tmp/tiny
 * 
 *  $path_portal/cake/console/cake -app $path_portal/app importacao_tiny
 * 
 * irá gerar os arquivos na pasta:
 * 
 *  
 * 
 * 
 * @author Willians P Pedroso 18/05/2018
 */
class ImportacaoTinyShell extends Shell {
    
    //atributo que instancia as models
    var $uses = array(
		'Cliente',
        'ClienteEndereco',
        'ClienteContato',
        'VEndereco',
        'Bairro',
        'CargaTipo',
		'LogIntegracao',
		'Pedido',
    	);

    public $clientes_cadastrado=0;
    public $clientes_ja_cadastrado=0;
    public $clientes_nao_cadastrado=0;
    public $clientes_sem_documento=0;
        
    /**
     * Metodo para iniciar o script como o contrutor da classe
     */
    public function main()
    {
    	echo "Script de importacao\n";
        echo "cake/console/cake -app ./app importacao_tiny getArquivo <opcional: nome_arquivo>\n";
    	

    } //fim main


    //varre a planilha
    public function getArquivo() 
    {
        //tempo ilimitado
        ini_set('max_execution_time',0);

        $this->log("INICIO DO PROCESSAMENTO",'debug');

        $path_tiny = APP . 'tmp' . DS . 'tiny' . DS;

        $arquivo = null;        
        if(!empty($this->args)) {
            $arquivo = $path_tiny.$this->args[0];
            $this->getDados($arquivo, $path_tiny, $this->args[0]);
        }
        else {

            //pega os arquivos
            $array_arquivos = scandir($path_tiny);
            
            //varre o diretorio
            foreach($array_arquivos as $arq){

                //verifica os pontos para não travar
                if($arq == "." || $arq == "..") {                
                    continue;
                }//fim pontos
                //monta o caminho completo do arquivo
                $arquivo = $path_tiny.$arq;

                //executa os dados
                $this->getDados($arquivo,$path_tiny,$arq);

            }//fim foreach
        }


        print "Clientes cadastrados: " . $this->clientes_cadastrado."\n";
        print "Clientes ja cadastrados: " . $this->clientes_ja_cadastrado . "\n";
        print "Clientes nao cadastrados: " . $this->clientes_nao_cadastrado . "\n";
        print "Clientes sem documento: " . $this->clientes_sem_documento ."\n";

        $this->log("|_Clientes cadastrados: " . $this->clientes_cadastrado,'debug');
        $this->log("|_Clientes ja cadastrados: " . $this->clientes_ja_cadastrado,'debug');
        $this->log("|_Clientes nao cadastrados: " . $this->clientes_nao_cadastrado,'debug');
        $this->log("|_Clientes sem documento: " . $this->clientes_sem_documento,'debug');

        $this->log("FIM DO PROCESSAMENTO",'debug');

    }//fim getArquivo

    public function getDados($arquivo,$path,$nome_arquivo)
    {

        $this->log("Arquivo: " . $nome_arquivo,'debug');

        $cont_reg = 0;

        //abre o arquivo para leitura
        $file = fopen($arquivo,"r");
        
        //varre todo o arquivo
        while(!feof($file)) {

            //le as linhas do arquivo
            $linha = fgets($file, 4096);
            
            if(empty($linha)) {
                continue;
            }//fim linha vazia

            //separa os dados
            $dados = explode(';', $linha);

            //verificar a primeira linha ID
            if(trim($dados[0]) == "ID" || empty($dados[0])) {
                continue;
            } //verifica os dados do ID
            
            //apresentacao do registros
            $cont_reg++;
            if($cont_reg == 100) {
                $this->log("Lido: " . $nome_arquivo . " = " . $cont_reg,'debug');

                $this->log("'--Clientes cadastrados: " . $this->clientes_cadastrado,'debug');
                $this->log("'--Clientes ja cadastrados: " . $this->clientes_ja_cadastrado,'debug');
                $this->log("'--Clientes nao cadastrados: " . $this->clientes_nao_cadastrado,'debug');
                $this->log("'--Clientes sem documento: " . $this->clientes_sem_documento,'debug');

                $cont_reg = 0;
            }

            //valida se tem cpf/cnpj
            if(empty($dados[18])) {
                $this->clientes_sem_documento++;

                //monta arquivo de cliente que não tem o documento por planilha
                $sem_documento = $path."sem_documento".DS."sem_doc_".$nome_arquivo;
                file_put_contents($sem_documento, $linha, FILE_APPEND);

                continue;
            }
            
            // $this->log(print_r($dados,1),'debug');
            $this->verificaCliente($dados, $linha, $path, $nome_arquivo);

        }//fim while

    } //fim getDados


    public function verificaCliente($dados, $linha, $path, $nome_arquivo)
    {
        //pega os dados
        $documento = trim($dados[18]);

        //retira a formatacao
        $codigo_documento = str_replace(".", "", str_replace("-", "", str_replace("/", "", $documento)));

        //busca no banco        
        $cliente = $this->Cliente->find('first', array('conditions' => array('Cliente.codigo_empresa' => 5, 'Cliente.codigo_documento' => $codigo_documento)));

        $endereco   = array();

        //seta os dados do cliente, monta o array para cadastrar o cliente
        $cliente["codigo_documento"]    = $codigo_documento;
        $cliente["nome_pagador"]        = trim($dados[2]);
        $cliente["cep"]                 = trim($dados[8]);
        $cliente["complemento"]         = trim($dados[6]);

        //contato
        $cliente['telefone']            = trim($dados[12]);
        $cliente['celular']             = trim($dados[14]);
        $cliente['email']               = trim($dados[15]);
        
        $cliente["numero"]              = 1;
        if(trim($dados[5]) != "") {
            $cliente["numero"] = comum::soNumero(trim($dados[5]));

            if(empty($cliente['numero'])) {
                $cliente["numero"]       = 1;
            }
        }

        //pega os dados do endereco
        $endereco["logradouro"]         = trim($dados[4]);
        $endereco["bairro"]             = trim($dados[7]);
        $endereco["cidade"]             = trim($dados[9]);
        $endereco["estado"]             = trim($dados[10]);

        //verifica se o cliente existe
        if(!empty($cliente['Cliente'])) {

            //atualiza os dados do cliente            
            if($this->alterarClienteImportado($cliente, $endereco)) {
                $this->clientes_ja_cadastrado++;

                //monta arquivo de cliente que não tem o documento por planilha
                $ja_cadastrados = $path."ja_cadastrados".DS."ja_cad_".$nome_arquivo;
                file_put_contents($ja_cadastrados, $linha, FILE_APPEND);
            }
            else {
                $this->clientes_nao_cadastrado++;

                //monta arquivo de cliente que não tem o documento por planilha
                $nao_importados = $path."importados".DS."nao_atualizado_".$nome_arquivo;
                file_put_contents($nao_importados, $linha, FILE_APPEND);
            }
            
        }
        else {

            //verifica se insere o cliente
            if($this->insereClienteImportado($cliente, $endereco)) {
                $this->clientes_cadastrado++;

                //monta arquivo de cliente que não tem o documento por planilha
                $importados = $path."importados".DS."imp_".$nome_arquivo;
                file_put_contents($importados, $linha, FILE_APPEND);

            }
            else {
                
                $this->clientes_nao_cadastrado++;

                //monta arquivo de cliente que não tem o documento por planilha
                $nao_importados = $path."importados".DS."nao_imp_".$nome_arquivo;
                file_put_contents($nao_importados, $linha, FILE_APPEND);

            }
            
        }//fim cliente

    }//fim verificaCliente

    /**
     * Metodo para inserir cliente do arquivo cnab
     */
    public function insereClienteImportado($dados, $dados_endereco)
    {
        //sessao
        $_SESSION['Auth']['Usuario']['codigo_empresa'] = 5;
        $_SESSION['Auth']['Usuario']['codigo'] = 1;

        //pega os ceps
        $cep_format = str_replace(".", "", str_replace("-","",$dados["cep"]));
        $endereco = $this->VEndereco->find('first', array('conditions' => array('OR' => array('endereco_cep' => $dados["cep"], 'endereco_cep' => $cep_format))));

        // $this->log(print_r($dados['cep'],1),'debug');

        $codigo_endereco = "";
        //verifica se existe o endereco
        if(!empty($endereco)) {
            // $this->log('cep','debug');
            $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
        } 
        else {

            //verifica se existe os campos para realizar as buscas e inclusao dos registros
            if(empty($dados_endereco['logradouro']) || empty($dados_endereco['bairro']) || empty($dados_endereco['cidade']) || empty($dados_endereco['estado'])) {

                $this->log('Cliente que nao foi cadastrado verificar endereco: ' . $dados['codigo_documento'] .' : '. $dados['nome_pagador'],'debug');
                return false;
            }

            //retira a rua, alameda, travessa
            $logradouro = explode(' ',$dados_endereco['logradouro']);
            unset($logradouro[0]);
            $logradouro = implode(' ', $logradouro);
            
            //tenta pegar o codigo pela rua, bairro, cidade e estado
            $endereco = $this->VEndereco->find('first', 
                array('conditions' => 
                    array(
                        "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_logradouro) LIKE" => '%' .$logradouro. '%',
                        "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_bairro) LIKE" => '%' .$dados_endereco["bairro"]. '%',
                        "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_cidade) LIKE" => '%' .$dados_endereco["cidade"]. '%',
                        'endereco_estado_abreviacao' => $dados_endereco["estado"],
                    )
                )
            );

            // $this->log($endereco, 'debug');exit;
            if(!empty($endereco)) {
                // $this->log('logradouro','debug');
                $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
            } else {
                //tenta pegar o codigo pela bairro, cidade e estado dbBuonny.publico.
                $endereco = $this->VEndereco->find('first', 
                    array('conditions' => 
                        array(
                            "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_bairro) LIKE" => '%' .$dados_endereco["bairro"]. '%',
                            "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_cidade) LIKE" => '%' .$dados_endereco["cidade"]. '%',
                            "endereco_estado_abreviacao" => $dados_endereco["estado"],
                        )
                    )
                );

                if(!empty($endereco)) {
                    // $this->log("bairro", 'debug');
                    $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
                } else {
                    //tenta pegar o codigo pela bairro, cidade e estado
                    $endereco = $this->VEndereco->find('first', 
                        array('conditions' => 
                            array(
                                "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_cidade) LIKE" => '%' .$dados_endereco["cidade"]. '%',
                                "endereco_estado_abreviacao" => $dados_endereco["estado"],
                            )
                        )
                    );

                    if(!empty($endereco)) {
                        // $this->log("cidade", 'debug');                       
                        $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
                    } else {

                        //tenta pegar o codigo pela bairro, cidade e estado
                        $endereco = $this->VEndereco->find('first', array('conditions' => array("endereco_estado_abreviacao" => trim($dados_endereco["estado"]))));
                        $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
                    }
                }//fim if bairro cidade
            }//fim if endereco logradouro, bairro, cidade, estado
            
        }//fim if endereco cep

        // $this->log($codigo_endereco,'debug');
        // $this->log($endereco,'debug');

        //seta os dados para insercao
        $cliente['Cliente']["codigo_documento"]         = $dados["codigo_documento"];
        $cliente['Cliente']["razao_social"]             = $dados["nome_pagador"];
        $cliente['Cliente']["nome_fantasia"]            = $dados["nome_pagador"];
        $cliente['Cliente']["obrigar_loadplan"]         = 0;
        $cliente['Cliente']["iniciar_por_checklist"]    = 0;
        $cliente['Cliente']["monitorar_retorno"]        = 0;
        $cliente['Cliente']['inscricao_estadual']       = 'ISENTO';
        $cliente['Cliente']['ccm']                      = '1';
        $cliente['Cliente']['codigo_regime_tributario'] = '3';
        $cliente['Cliente']['ativo']                    = 1;
        $cliente['Cliente']['codigo_externo']           = '';
        $cliente['Cliente']['tipo_unidade']             = 'F';
        $cliente['Cliente']['codigo_usuario_inclusao']  = 1;
        
        $cliente['ClienteEndereco']['codigo_endereco']  = $codigo_endereco;
        $cliente['ClienteEndereco']['numero']           = $dados["numero"];
        $cliente['ClienteEndereco']['complemento']      = $dados["complemento"];

        //verifica se tem endereço carregado
        if(is_array($endereco)) {
            $cliente_merge = array_merge($cliente, $endereco);
        }
        else {
            $cliente_merge = $cliente;
        }

        // if($dados["codigo_documento"] == "10284572780") {
            // $this->log(print_r($cliente_merge,1), 'debug');
        // }

        //grava os dados do cliente, e não vincula a grupo economico
        $cli = $this->Cliente->incluir($cliente_merge, 1);

        // $this->log($this->Cliente->id, 'debug');
        // $this->log("#####################", 'debug');

        if(!$this->Cliente->id) {            
            return false;
        }

        //verifica se existe contatos para serem cadastrados
        $contato = array();
        if($dados['telefone'] != "" && $this->Cliente->id) {
            $contato['ClienteContato']['codigo_cliente']            = $this->Cliente->id;
            $contato['ClienteContato']['codigo_tipo_contato']       = 2;
            $contato['ClienteContato']['codigo_tipo_retorno']       = 1;
            $contato['ClienteContato']['descricao']                 = $dados['telefone'];
            $contato['ClienteContato']['nome']                      = $dados['nome_pagador'];
            $contato['ClienteContato']['codigo_usuario_inclusao']   = 1;
            $contato['ClienteContato']['codigo_empresa']            = 5;

            $this->ClienteContato->incluir($contato);
        }//fim telefone

        $contato = array();
        if($dados['celular'] != "" && $this->Cliente->id) {
            $contato['ClienteContato']['codigo_cliente']            = $this->Cliente->id;
            $contato['ClienteContato']['codigo_tipo_contato']       = 2;
            $contato['ClienteContato']['codigo_tipo_retorno']       = 7;
            $contato['ClienteContato']['descricao']                 = $dados['celular'];
            $contato['ClienteContato']['nome']                      = $dados['nome_pagador'];
            $contato['ClienteContato']['codigo_usuario_inclusao']   = 1;
            $contato['ClienteContato']['codigo_empresa']            = 5;

            $this->ClienteContato->incluir($contato);
        }//fim celular

        $contato = array();
        if($dados['email'] != "" && $this->Cliente->id) {
            $contato['ClienteContato']['codigo_cliente']            = $this->Cliente->id;
            $contato['ClienteContato']['codigo_tipo_contato']       = 2;
            $contato['ClienteContato']['codigo_tipo_retorno']       = 2;
            $contato['ClienteContato']['descricao']                 = $dados['email'];
            $contato['ClienteContato']['nome']                      = $dados['nome_pagador'];
            $contato['ClienteContato']['codigo_usuario_inclusao']   = 1;
            $contato['ClienteContato']['codigo_empresa']            = 5;

            $this->ClienteContato->incluir($contato);
        }//fim email

        return $this->Cliente->id;

    } //fim insere cliente


     /**
     * Metodo para inserir cliente do arquivo cnab
     */
    public function alterarClienteImportado($dados, $dados_endereco)
    {
        //sessao
        $_SESSION['Auth']['Usuario']['codigo_empresa'] = 5;
        $_SESSION['Auth']['Usuario']['codigo'] = 1;

        $codigo_cliente = $dados['Cliente']['codigo'];

        // $this->log($codigo_cliente . "----" . $dados['codigo_documento'], 'debug');

        if(empty($codigo_cliente)) {
            $this->log("nao existe codigo para atualizar: " . $dados['codigo_documento'], 'debug');
            return false;
        }

        //pega os ceps
        // $endereco = $this->VEndereco->find('first', array('conditions' => array('endereco_cep' => $dados["cep"])));
        // $codigo_endereco = "";
        // //verifica se existe o endereco
        // if(!empty($endereco)) {
        //     // $this->log('cep','debug');
        //     $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
        // } else {

        //     //verifica se existe os campos para realizar as buscas e inclusao dos registros
        //     if(empty($dados_endereco['logradouro']) || empty($dados_endereco['bairro']) || empty($dados_endereco['cidade']) || empty($dados_endereco['estado'])) {

        //         $this->log('Cliente que nao foi atualizado verificar endereco: ' . $dados['codigo_documento'] .' : '. $dados['nome_pagador'],'debug');
        //         return false;
        //     }


        //     //retira a rua, alameda, travessa
        //     $logradouro = explode(' ',$dados_endereco['logradouro']);
        //     unset($logradouro[0]);
        //     $logradouro = implode(' ', $logradouro);
            
        //     //tenta pegar o codigo pela rua, bairro, cidade e estado
        //     $endereco = $this->VEndereco->find('first', 
        //         array('conditions' => 
        //             array(
        //                 "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_logradouro) LIKE" => '%' .$logradouro. '%',
        //                 "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_bairro) LIKE" => '%' .$dados_endereco["bairro"]. '%',
        //                 "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_cidade) LIKE" => '%' .$dados_endereco["cidade"]. '%',
        //                 'endereco_estado_abreviacao' => $dados_endereco["estado"],
        //             )
        //         )
        //     );

        //     // $this->log($endereco, 'debug');exit;
        //     if(!empty($endereco)) {
        //         // $this->log('logradouro','debug');
        //         $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
        //     } else {
        //         //tenta pegar o codigo pela bairro, cidade e estado dbBuonny.publico.
        //         $endereco = $this->VEndereco->find('first', 
        //             array('conditions' => 
        //                 array(
        //                     "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_bairro) LIKE" => '%' .$dados_endereco["bairro"]. '%',
        //                     "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_cidade) LIKE" => '%' .$dados_endereco["cidade"]. '%',
        //                     "endereco_estado_abreviacao" => $dados_endereco["estado"],
        //                 )
        //             )
        //         );

        //         if(!empty($endereco)) {
        //             // $this->log("bairro", 'debug');
        //             $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
        //         } else {
        //             //tenta pegar o codigo pela bairro, cidade e estado
        //             $endereco = $this->VEndereco->find('first', 
        //                 array('conditions' => 
        //                     array(
        //                         "{$this->Bairro->databaseTable}.{$this->CargaTipo->tableSchema}.remover_acentos(endereco_cidade) LIKE" => '%' .$dados_endereco["cidade"]. '%',
        //                         "endereco_estado_abreviacao" => $dados_endereco["estado"],
        //                     )
        //                 )
        //             );

        //             if(!empty($endereco)) {
        //                 // $this->log("cidade", 'debug');                       
        //                 $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
        //             } else {

        //                 //tenta pegar o codigo pela bairro, cidade e estado
        //                 $endereco = $this->VEndereco->find('first', array('conditions' => array("endereco_estado_abreviacao" => trim($dados_endereco["estado"]))));
        //                 $codigo_endereco = $endereco['VEndereco']["endereco_codigo"];
        //             }
        //         }//fim if bairro cidade
        //     }//fim if endereco logradouro, bairro, cidade, estado
            
        // }//fim if endereco cep

        // // $this->log($codigo_endereco,'debug');
        // //seta os dados para insercao
        // $cliente['Cliente']['codigo']                   = $codigo_cliente;
        // $cliente['Cliente']["codigo_documento"]         = $dados["codigo_documento"];
        // $cliente['Cliente']["razao_social"]             = $dados["nome_pagador"];
        // $cliente['Cliente']["nome_fantasia"]            = $dados["nome_pagador"];
        // $cliente['Cliente']["obrigar_loadplan"]         = 0;
        // $cliente['Cliente']["iniciar_por_checklist"]    = 0;
        // $cliente['Cliente']["monitorar_retorno"]        = 0;
        // $cliente['Cliente']['inscricao_estadual']       = 'ISENTO';
        // $cliente['Cliente']['ccm']                      = '1';
        // $cliente['Cliente']['codigo_regime_tributario'] = '3';
        // $cliente['Cliente']['ativo']                    = 1;
        // $cliente['Cliente']['codigo_externo']           = '';
        // $cliente['Cliente']['tipo_unidade']             = 'F';
        // $cliente['Cliente']['codigo_usuario_inclusao']  = 1;
        
        // $cliente['ClienteEndereco']['codigo_endereco']  = $codigo_endereco;
        // $cliente['ClienteEndereco']['numero']           = $dados["numero"];
        // $cliente['ClienteEndereco']['complemento']      = $dados["complemento"];

        // //verifica se tem endereço carregado
        // if(is_array($endereco)) {
        //     $cliente_merge = array_merge($cliente, $endereco);
        // }
        // else {
        //     $cliente_merge = $cliente;
        // }

        // $this->log(print_r($cliente_merge,1), 'debug');

        //grava os dados do cliente, e não vincula a grupo economico
        // $cli = $this->Cliente->atualizar($cliente_merge, 1);

        // $this->log($this->Cliente->id, 'debug');
        // $this->log("#####################", 'debug');

        //verifica se existe contatos para serem cadastrados
        $contato = array();
        if($dados['telefone'] != "") {

            $contato = $this->ClienteContato->find('first', array('conditions' => array('ClienteContato.codigo_cliente' => $codigo_cliente, 'ClienteContato.codigo_tipo_contato' => 2, 'ClienteContato.codigo_tipo_retorno' => 1, 'ClienteContato.descricao' => $dados['telefone'])));

            if(!empty($contato)) {
                $contato['ClienteContato']['descricao']                 = $dados['telefone'];
                $this->ClienteContato->atualizar($contato);
            }
            else {

                $contato['ClienteContato']['codigo_cliente']            = $codigo_cliente;
                $contato['ClienteContato']['codigo_tipo_contato']       = 2;
                $contato['ClienteContato']['codigo_tipo_retorno']       = 1;
                $contato['ClienteContato']['descricao']                 = $dados['telefone'];
                $contato['ClienteContato']['nome']                      = $dados['nome_pagador'];
                $contato['ClienteContato']['codigo_usuario_inclusao']   = 1;
                $contato['ClienteContato']['codigo_empresa']            = 5;

                $this->ClienteContato->incluir($contato);
            }

        }//fim telefone

        $contato = array();
        if($dados['celular'] != "") {

            $contato = $this->ClienteContato->find('first', array('conditions' => array('ClienteContato.codigo_cliente' => $codigo_cliente, 'ClienteContato.codigo_tipo_contato' => 2, 'ClienteContato.codigo_tipo_retorno' => 7, 'ClienteContato.descricao' => $dados['celular'])));

            if(!empty($contato)) {
                $contato['ClienteContato']['descricao']                 = $dados['celular'];
                $this->ClienteContato->atualizar($contato);
            }
            else {

                $contato['ClienteContato']['codigo_cliente']            = $codigo_cliente;
                $contato['ClienteContato']['codigo_tipo_contato']       = 2;
                $contato['ClienteContato']['codigo_tipo_retorno']       = 7;
                $contato['ClienteContato']['descricao']                 = $dados['celular'];
                $contato['ClienteContato']['nome']                      = $dados['nome_pagador'];
                $contato['ClienteContato']['codigo_usuario_inclusao']   = 1;
                $contato['ClienteContato']['codigo_empresa']            = 5;

                $this->ClienteContato->incluir($contato);
            }

        }//fim celular

        $contato = array();
        if($dados['email'] != "") {

            $contato = $this->ClienteContato->find('first', array('conditions' => array('ClienteContato.codigo_cliente' => $codigo_cliente, 'ClienteContato.codigo_tipo_contato' => 2, 'ClienteContato.codigo_tipo_retorno' => 2, 'ClienteContato.descricao' => $dados['email'])));

            if(!empty($contato)) {
                $contato['ClienteContato']['descricao']                 = $dados['email'];

                $this->ClienteContato->atualizar($contato);
            }
            else {
                $contato['ClienteContato']['codigo_cliente']            = $codigo_cliente;
                $contato['ClienteContato']['codigo_tipo_contato']       = 2;
                $contato['ClienteContato']['codigo_tipo_retorno']       = 2;
                $contato['ClienteContato']['descricao']                 = $dados['email'];
                $contato['ClienteContato']['nome']                      = $dados['nome_pagador'];
                $contato['ClienteContato']['codigo_usuario_inclusao']   = 1;
                $contato['ClienteContato']['codigo_empresa']            = 5;

                $this->ClienteContato->incluir($contato);
            }

        }//fim email

        return $codigo_cliente;

    } //fim alterar cliente


}//fim class
?>