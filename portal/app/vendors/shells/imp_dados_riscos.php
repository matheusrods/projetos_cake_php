<?php
class ImpDadosRiscosShell extends Shell {
    
    //atributo que instancia as models
    var $uses = array(
		'Risco',
        'GrupoRisco'
    	);

    public $riscos_cadastrados=0;
    public $riscos_atualizados=0;
    public $riscos_diferentes=0;
    public $count_linha=0;
        
    /**
     * Metodo para iniciar o script como o contrutor da classe
     */
    public function main()
    {
        echo "Script de importação dos dados da planilha do gestão de riscos\n";

        $this->importacao_planilha_gr();
        
    } //fim main

    public function importacao_planilha_gr()
    {

        print "INICIANDO A IMPORTACAO DA PLANILHA DE GESTÃO DE RISCOS \n";

        $arquivo = APP . 'tmp' . DS . "MODELO_IMPORTACAO_RISCOS.csv";

        if(file_exists($arquivo)){
        
            $arquivo = fopen($arquivo,"r");
            
            $count_linha_arquivo = 0;
            $riscos_cadastrados_arquivo=0;
            $riscos_encontrado_inativacao=0;
            $riscos_nao_encotrado_inativacao=0;
            print "Inicio processamento: ".date("d/m/Y H:i:s")."\n";

            //laço com os dados dos arquivos
            while (!feof($arquivo)) {
                $linha = trim(fgets($arquivo, 10096));

                if(!empty($linha)){
                    $dados = explode(";", $linha);

                    // print $dados[0]."\n";

                    if($count_linha_arquivo <= 3) {
                        debug($linha);
                        debug($dados);
                    }
                    if($count_linha_arquivo > 3) {
                        exit;
                    }
                }

                $count_linha_arquivo++;

            }//fim while

            fclose($arquivo);
            
            print "Fim processamento: ".date("d/m/Y H:i:s")."\n\n";
            $msg = '';
            $msg .= "riscos cadastrados: " . $riscos_cadastrados_arquivo."\n";
            $msg .= "riscos encontrados inativacao: " . $riscos_encontrado_inativacao . "\n";
            $msg .= "riscos nao econtrado inativacao: " . $riscos_nao_encotrado_inativacao ."\n";
            $msg .= "Quantidade registros lidos: " . $count_linha_arquivo."\n\n\n";

            print $msg;

        }//FIM file_exists
        else {
            print "Não existe arquivo\n";
        }

        print "FIM DA IMPORTACAO DA PLANILHA DE GESTÃO DE RISCOS \n";
        exit;

    }//fim importacao_planilha_gr


    /**
     * [atualiza_riscos metodo para atualizar os riscos pelo arquivo enviado seguindo a estrutura
     * 
     * #######CÓD.;Grupo (*);Nome Agente (*);Meio de Propagação;Unidade de Medida;Limite de Tolerência;Nível de Ação;;##########
     * 
     * ]
     * @return [type] [description]
     */
    public function atualiza()
    {

        print "INICIANDO PROCESSAMENTO DOS RISCOS: ". date('Y-m-d H:i:s')."\n";

        $_SESSION['Auth']['Usuario']['codigo'] = 1;
        //tempo ilimitado
        ini_set('max_execution_time',0);
        //$path_resultado = APP . 'tmp' . DS . 'cfm_resultado' . DS;
        $arquivo = APP . 'tmp' . DS . "RISCOS.csv";

        if(file_exists($arquivo)){
        
            $arquivo = fopen($arquivo,"r");
            $arquivo_lido = basename($arquivo);

            echo "***********************************************"."\n";
            echo "Arquivo importado: ".basename($arquivo_lido)."\n";
            echo "***********************************************"."\n";

            $count_linha_arquivo = 0;
            $riscos_cadastrados_arquivo=0;
            $riscos_encontrado_inativacao=0;
            $riscos_nao_encotrado_inativacao=0;
            print "Inicio processamento: ".date("d/m/Y H:i:s")."\n";

            while (!feof($arquivo)) {
                $linha = trim(fgets($arquivo, 4096));
                
                if(!empty($linha)){
                    $dados = explode(";", $linha);

                    if($dados[1] == 'Grupo (*)') {
                        continue;
                    }

                    /*
                    data[Risco][codigo_agente_nocivo_esocial] -> string
                    data[Risco][codigo_grupo] -> int
                    data[Risco][nome_agente] -> string
                    data[Risco][codigo_risco_atributo] -> int
                    data[Risco][unidade_medida] -> string
                    data[Risco][limite_tolerancia] -> string
                    data[Risco][nivel_acao] -> string

                    usuario_inclusao
                    data_inclusao

                    */
                    //pega os dados conforme os indices
                    $codigo_agente_nocivo_esocial = trim($dados[0]);
                    $grupo_risco = trim($dados[1]);
                    $nome_agente = trim($dados[2]);
                    $risco_atributo = trim($dados[3]);
                    $unidade_medida = trim($dados[4]);
                    $limite_tolerancia = trim($dados[5]);
                    $nivel_acao = trim($dados[6]);


                    //busca os riscos que já estao cadastrados para inativar
                    $riscos_antigos = $this->Risco->find('first',array('conditions' => array('nome_agente LIKE' => '%'.$nome_agente.'%')));

                    //verifica se nao encontrou o risco
                    $codigo_risco_inativacao = null;
                    if(!empty($riscos_antigos)) {
                        $codigo_risco_inativacao = $riscos_antigos['Risco']['codigo'];
                        $riscos_encontrado_inativacao++;
                    }
                    else {

                        $msg = "Risco nao encontrado: ". $nome_agente."\n";
                        $nome_arquivo = "log_risco_nao_econtrado_inativacao.txt";
                        $this->grava_log_arquivo( $nome_arquivo,$msg);

                        print $msg;
                        $riscos_nao_encotrado_inativacao++;
                    }

                    //pega o codigo do risco grupo 
                    $grupoRisco = $this->GrupoRisco->find('first',array('conditions' => array('descricao LIKE' => '%' . $grupo_risco . '%','codigo_empresa' => '1')));
                    $codigo_grupo = null;
                    if(!empty($grupoRisco)) {
                        $codigo_grupo = $grupoRisco['GrupoRisco']['codigo'];
                    }
                    else {
                        $this->log("Grupo nao encontrado: ". $grupo_risco,'debug');
                        print "Grupo nao encontrado: ". $grupo_risco."\n";
                    }

                    /*
                    20  Via AÃ©rea
                    21  Via AÃ©rea / Via cutÃ¢nea
                    22  Via cutÃ¢nea
                     */
                    $codigoRiscoAtributo = array(                        
                        'Via Aérea' => 20,
                        'Via Aérea / Via cutânea' => 21,
                        'Via cutânea' => 22
                    );

                    $codigo_risco_atributo = null;
                    if(!empty($risco_atributo)) {
                        $codigo_risco_atributo = $codigoRiscoAtributo[$risco_atributo];
                    }


                    //para inserir o dado corretamente
                    if(!is_null($codigo_grupo)) {

                        $msg_inc = "codigo_risco_inativacao:" . $codigo_risco_inativacao . " #### " . $count_linha_arquivo.": ".$codigo_agente_nocivo_esocial.";".$codigo_grupo.";".$nome_agente.";".$codigo_risco_atributo.";".$unidade_medida.";".$limite_tolerancia.";".$nivel_acao."\n";

                        //insere o risco
                        $dados_risco = array(
                            'Risco'=> array(                                
                                'codigo_agente_nocivo_esocial' => $codigo_agente_nocivo_esocial,
                                'codigo_grupo' => $codigo_grupo,
                                'nome_agente' => $nome_agente,
                                'codigo_risco_atributo' => $codigo_risco_atributo,
                                'unidade_medida' => $unidade_medida,
                                'limite_tolerancia' => $limite_tolerancia,
                                'nivel_acao' => $nivel_acao,
                                'ativo' => 1,
                                'codigo_empresa' => 1,
                                'usuario_inclusao' => 1,
                                'data_inclusao' => date('Y-m-d H:i:s'),
                            )
                        );

                        if($this->Risco->incluir($dados_risco)) {
                            $riscos_cadastrados_arquivo++;

                            if(!empty($codigo_risco_inativacao)) {
                                $query = "UPDATE riscos SET ativo = '0',codigo_usuario_alteracao='1',data_alteracao='".date('Y-m-d H:i:s')."' WHERE codigo = " . $codigo_risco_inativacao.";";
                                $this->Risco->query($query);
                            }//fim atualizacao
                        }
                        else {
                            
                            $nome_arquivo = "log_erro_incluir_risco.txt";
                            $this->grava_log_arquivo( $nome_arquivo,$msg_inc);
                        }

                        print $msg_inc;
                    }
                    
                    $count_linha_arquivo++;

                }//if empty(linha)                        

            }//while feof

            fclose($arquivo);
            
            print "Fim processamento: ".date("d/m/Y H:i:s")."\n\n";
            $msg = '';
            $msg .= "riscos cadastrados: " . $riscos_cadastrados_arquivo."\n";
            $msg .= "riscos encontrados inativacao: " . $riscos_encontrado_inativacao . "\n";
            $msg .= "riscos nao econtrado inativacao: " . $riscos_nao_encotrado_inativacao ."\n";
            $msg .= "Quantidade registros lidos: " . $count_linha_arquivo."\n\n\n";

            print $msg;
        } 
        else {
            echo "Nenhum arquivo encontrado"."\n";
        }
        
    }//fim atualiza_riscos






    public function grava_log_arquivo($nome_arquivo,$linha){
        $path_resultado = APP . 'tmp' . DS;
        $caminho_arquivo = $path_resultado.$nome_arquivo; 
        file_put_contents($caminho_arquivo, $linha, FILE_APPEND);
    }

}//fim class
?>