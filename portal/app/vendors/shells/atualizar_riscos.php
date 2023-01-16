<?php
class AtualizarRiscosShell extends Shell {
    
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
        echo "Script de atualizacao dos riscos\n";
        $this->importa_medico();

    } //fim main

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




    // //varre a planilha
    // public function importa_medico() {
    //     $_SESSION['Auth']['Usuario']['codigo'] = 63085;
    //     //tempo ilimitado
    //     ini_set('max_execution_time',0);
    //     //$path_resultado = APP . 'tmp' . DS . 'cfm_resultado' . DS;
    //     $path_listas = APP . 'tmp' . DS . 'cfm' . DS;
    //     // print $path_listas."\n";exit;
    //     $path_lista_concluida = APP . 'tmp' . DS . 'cfm_finalizada' . DS;


    //     $lista_arquivos = glob($path_listas.'*.txt');


    //     if(count($lista_arquivos) > 0){
    //         foreach($lista_arquivos as $key => $arquivo_medicos){

    //             if(file_exists($arquivo_medicos)){
    //                 $arquivo = fopen($arquivo_medicos,"r");
    //                 $arquivo_lido = basename($arquivo_medicos);

    //                 echo "***********************************************"."\n";
    //                 echo "Arquivo importado: ".basename($arquivo_lido)."\n";
    //                 echo "***********************************************"."\n";
    //                 $count_linha_arquivo = 0;
    //                 $medicos_cadastrados_arquivo=0;
    //                 $medicos_atualizados_arquivo=0;
    //                 $medicos_diferentes_arquivo=0;
    //                 print "Inicio processamento: ".date("d/m/Y H:i:s")."\n";

    //                 while (!feof($arquivo)) {
    //                     $linha = trim(fgets($arquivo, 4096));
                        
    //                     if(!empty($linha)){
    //                         $dados = explode("!", $linha);
                            
    //                         $numero_conselho = $dados[0];
    //                         $estado_conselho = $dados[1];
    //                         $nome_medico = trim($dados[2]);
    //                         $status = trim($dados[4]);
    //                         $especialidade = $dados[5];


    //                         // retira a aspas simples para não dar erro na inclusao caso necessario
    //                         $nome_medico = str_replace("'","",$nome_medico);

    //                         //Se o status for qualquer valor diferente de ativo, considera inativo
    //                         if($status == 'Ativo'){
    //                             $atualiza_status = 1;
    //                         } else {
    //                             $atualiza_status = 0;
    //                         }

    //                         //first ou all
    //                         // $medico_base = $this->Medico->find('all',array('conditions' => array("CAST(RHHealth.publico.ufn_limpa_texto(numero_conselho,0) AS int) =  $numero_conselho", "conselho_uf" => $estado_conselho, "codigo_conselho_profissional" => 1, "ativo" => 1, "codigo_empresa" => 1),'recursive' => -1 ));
    //                         // 
    //                         $sqlMedicos = "SELECT codigo, numero_conselho, conselho_uf, nome, ativo 
    //                                         FROM RHHealth.dbo.medicos 
    //                                         WHERE numero_conselho = '". $numero_conselho."' AND conselho_uf = '".$estado_conselho."' AND codigo_conselho_profissional = '1' AND ativo = '1' AND codigo_empresa = '1';";
    //                         // print $sqlMedicos."\n";
    //                         $medico_base = $this->Medico->query($sqlMedicos);

    //                         //Se um registro ou mais foram encontrados na base
    //                         if(!empty($medico_base)){

    //                             // pr($medico_base);exit;

    //                             foreach($medico_base as $medico){
    //                                 $sql = "";
    //                                 $medico_registro =  $medico[0]['codigo'].";".$medico[0]['numero_conselho'].";".$medico[0]['conselho_uf'].";".$medico[0]['nome'].";".$medico[0]['ativo'].";".$numero_conselho.";". $estado_conselho.";".$nome_medico.";".$status.";".$linha."\n";

    //                                 //Se o primeiro e o último nome do médico são iguais ao cadastrado no sistema
    //                                 if($this->compara_nomes($medico[0]['nome'], $nome_medico)){

    //                                     //Se o status é inativo o médico encontrado na base que esta ativo sera inativado
    //                                     if($atualiza_status == 0){
    //                                         //Recupera registro do médico para atualizar o status
    //                           /*              $dados_medicos = $this->Medico->read(null, $medico[0]['codigo']);
    //                                         $this->log('$dados_medicos','debug');
    //                                         $this->log($dados_medicos,'debug');*/
    //                                        // $this->Medico->id = $medico[0]['codigo'];
    //                                         $sql = "UPDATE RHHealth.dbo.medicos set ativo = 0, data_alteracao = '".date("Y-m-d H:i:s")."' where codigo = ".$medico[0]['codigo'];

    //                                         if($this->Medico->query($sql)){
    //                                             //$this->Medico->saveField('data_alteracao',date("Y-m-d h:i:s"));
    //                                             $this->medicos_atualizados++;
    //                                             $medicos_atualizados_arquivo++;

    //                                             $nome_arquivo = $estado_conselho."_log_medico_atualizado_inativado_.csv";

    //                                             if(!file_exists(APP . 'tmp' . DS . 'cfm_resultado' . DS.$nome_arquivo)) {
    //                                                 $cabecalho = "base_codigo;base_numero_conselho;base_conselho_uf;base_nome;base_ativo;arq_numero_conselho;arq_estado_conselho;arq_nome_medico;arq_status;arq_linha\n";
    //                                                 $medico_registro = $cabecalho.$medico_registro;
    //                                             }

    //                                             $this->grava_log_arquivo( $nome_arquivo,$medico_registro);

    //                                            // $this->grava_log_arquivo("log_medicos_atualizados",$medico_registro);
    //                                         } else {
    //                                             $nome_arquivo = $estado_conselho."_log_erro_atualizacao_.csv";

    //                                             if(!file_exists(APP . 'tmp' . DS . 'cfm_resultado' . DS.$nome_arquivo)) {
    //                                                 $cabecalho = "base_codigo;base_numero_conselho;base_conselho_uf;base_nome;base_ativo;arq_numero_conselho;arq_estado_conselho;arq_nome_medico;arq_status;arq_linha\n";
    //                                                 $medico_registro = $cabecalho.$medico_registro;
    //                                             }

    //                                             $this->grava_log_arquivo( $nome_arquivo,$medico_registro);

    //                                         }
        
    //                                     }

    //                                 } else {
    //                                     $nome_arquivo = $estado_conselho."_log_nome_divergente_.csv";

    //                                     if(!file_exists(APP . 'tmp' . DS . 'cfm_resultado' . DS.$nome_arquivo)) {
    //                                         $cabecalho = "base_codigo;base_numero_conselho;base_conselho_uf;base_nome;base_ativo;arq_numero_conselho;arq_estado_conselho;arq_nome_medico;arq_status;arq_linha\n";
    //                                         $medico_registro = $cabecalho.$medico_registro;
    //                                     }

    //                                     $this->grava_log_arquivo($nome_arquivo,$medico_registro);
    //                                      //Se o CRM foi encontrado porém está com o nome diferente
    //                                     //$crm_diferente = $path_resultado."crm_diferente_inativado_".$arquivo_lido;
    //                                     //file_put_contents($crm_diferente, $medico_diferente, FILE_APPEND);
    //                                     $this->medicos_diferentes++;
    //                                     $medicos_diferentes_arquivo++;
    //                                 }//fim if compara nomes
    //                             }//fim foreach
   
    //                         //Se o registro do médico não foi encontrado
    //                         } else {
    //                             // print "aqui\n";
    //                             //Cadastrar o medico somente se este for ativo
    //                             if($atualiza_status == 1){

    //                                 // print "ativo\n";

    //                                 $especial_arquivo = NULL;
    //                                 if(!empty($especialidade)) {
    //                                     $especialidade = trim($especialidade);

    //                                     //Verifica se existe MEDICINA DO TRABALHO entre as especialidades
    //                                     //Se existe, essa será priorizada
    //                                     if(stripos($especialidade,'MEDICINA DO TRABALHO') !== false){
    //                                         $especial_arquivo = 'MEDICINA DO TRABALHO';
    //                                     } else {
    //                                         //Caso contrário utilizar a primeira função
    //                                        $especial_arquivo =  strstr($especialidade, ' - RQE', true);
    //                                     }        
                                        
    //                                 }//fim if empty

    //                                 // $medico_cadastro['Medico'] = array( 'nome' => $nome_medico,
    //                                 //                                     'numero_conselho' => $numero_conselho,
    //                                 //                                     'conselho_uf' => $estado_conselho,
    //                                 //                                     'especialidade' => $especial_arquivo,
    //                                 //                                     'codigo_conselho_profissional' => 1,
    //                                 //                                     'codigo_empresa' => 1,
    //                                 //                                     'ativo' => 1
    //                                 //                             );

    //                                 $query = "INSERT INTO RHHealth.dbo.medicos (nome,numero_conselho,conselho_uf,especialidade,codigo_conselho_profissional,codigo_empresa,ativo,data_inclusao) values ('{$nome_medico}','{$numero_conselho}','{$estado_conselho}','{$especial_arquivo}',1,1,1,'".date('Y-m-d H:i:s')."')";
    //                                 // print $query."\n";
    //                                 $result = $this->Medico->query($query);

    //                                 if($result) {
    //                                     $this->medicos_cadastrados++;
    //                                     $medicos_cadastrados_arquivo++;

    //                                     $medico_erro_cadastro = $numero_conselho.";". $estado_conselho.";".$nome_medico.";".$status.";".$linha."\n";
    //                                     $nome_arquivo = $estado_conselho."_log_medico_cadastrado_.csv";
    //                                     //medicos que estao no arquivo como inativos status 0
    //                                     if(!file_exists(APP . 'tmp' . DS . 'cfm_resultado' . DS.$nome_arquivo)) {
    //                                         $cabecalho = "arq_numero_conselho;arq_estado_conselho;arq_nome_medico;arq_status;arq_linha\n";
    //                                         $medico_erro_cadastro = $cabecalho.$medico_erro_cadastro;
    //                                     }
    //                                     $this->grava_log_arquivo($nome_arquivo,$medico_erro_cadastro);
    //                                 }
    //                                 else {
    //                                     $medico_erro_cadastro = $numero_conselho.";". $estado_conselho.";".$nome_medico.";".$status.";".$linha."\n";
    //                                     $nome_arquivo = $estado_conselho."_log_erro_cadastro_.csv";
    //                                     //medicos que estao no arquivo como inativos status 0
    //                                     if(!file_exists(APP . 'tmp' . DS . 'cfm_resultado' . DS.$nome_arquivo)) {
    //                                         $cabecalho = "arq_numero_conselho;arq_estado_conselho;arq_nome_medico;arq_status;arq_linha\n";
    //                                         $medico_erro_cadastro = $cabecalho.$medico_erro_cadastro;
    //                                     }
    //                                     $this->grava_log_arquivo($nome_arquivo,$medico_erro_cadastro);  
    //                                 }

    //                                 // if($this->Medico->incluir($medico_cadastro)){
    //                                 //    $this->medicos_cadastrados++;
    //                                 //    $medicos_cadastrados_arquivo++;

    //                                 // } else {
    //                                 //     $medico_erro_cadastro = $numero_conselho.";". $estado_conselho.";".$nome_medico.";".$status."\n";
    //                                 //     $nome_arquivo = $estado_conselho."_log_erro_cadastro_.csv";
    //                                 //     $this->grava_log_arquivo($nome_arquivo,$medico_erro_cadastro);  
    //                                 // }

    //                                 //INCLUIR E REGISTRAR EM ARQUIVO
    //                                 /*$crm_incluido = $path_resultado."crm_incluido_".$arquivo_lido;
    //                                 file_put_contents($crm_incluido, $linha, FILE_APPEND);*/
    //                             }//fim atualiza_status
    //                             else {
    //                                 $medico_erro_cadastro = $numero_conselho.";". $estado_conselho.";".$nome_medico.";".$status.";".$linha."\n";
    //                                 $nome_arquivo = $estado_conselho."_log_erro_atualiza_status_0_.csv";
    //                                 //medicos que estao no arquivo como inativos status 0
    //                                 if(!file_exists(APP . 'tmp' . DS . 'cfm_resultado' . DS.$nome_arquivo)) {
    //                                     $cabecalho = "arq_numero_conselho;arq_estado_conselho;arq_nome_medico;arq_status;arq_linha\n";
    //                                     $medico_erro_cadastro = $cabecalho.$medico_erro_cadastro;
    //                                 }
    //                                 $this->grava_log_arquivo($nome_arquivo,$medico_erro_cadastro); 
    //                             }
    //                        }//else de cadastro
                            
    //                         $this->count_linha++;
    //                         $count_linha_arquivo++;
    //                         if($count_linha_arquivo % 1000 == 0){
    //                            print "{$estado_conselho} Registros processados: ".$count_linha_arquivo." : " .date("Y-m-d H:i:s"). "\n";
    //                         }

    //                         if($count_linha_arquivo % 10000 == 0){
    //                            print "{$estado_conselho} SLEEP 5 Seg.: ".$count_linha_arquivo." : " .date("Y-m-d H:i:s"). "\n";
    //                            sleep(5);
    //                         }

    //                     }//if empty(linha)                        

    //                 }//while feof

    //                 fclose($arquivo);
    //                 $origem = $path_listas.$arquivo_lido;
    //                 $destino = $path_lista_concluida.$arquivo_lido;
    //                 rename($origem,$destino);


    //                 print "Fim processamento: ".date("d/m/Y H:i:s")."\n\n";
    //                 $msg = '';
    //                 $msg .= "Medicos cadastrados: " . $medicos_cadastrados_arquivo."\n";
    //                 $msg .= "Medicos divergentes: " . $medicos_diferentes_arquivo . "\n";
    //                 $msg .= "Medicos atualizados: " . $medicos_atualizados_arquivo ."\n";
    //                 $msg .= "Quantidade registros lidos: " . $count_linha_arquivo."\n\n\n";

    //                 $nome_arquivo_msg = "log_msg".$estado_conselho;                    
    //                 $this->grava_log_arquivo($nome_arquivo_msg,$msg);

    //                 print $msg;

    //             }//if file_exists 

    //         }//foreach arquivos

    //     } else {

    //         echo "Nenhum arquivo encontrado"."\n";
    //     }

    //     print "***********************************************"."\n";
    //     print "Medicos cadastrados: " . $this->medicos_cadastrados."\n";
    //     print "Medicos divergentes: " . $this->medicos_diferentes . "\n";
    //     print "Medicos atualizados: " . $this->medicos_atualizados ."\n";
    //     print "Quantidade registros lidos: " . $this->count_linha."\n";

      

    // }//fim importa_medico

    // public function compara_nomes($medico_base, $medico_arquivo){

    //     $medico_base = trim($medico_base);
    //     $medico_arquivo = trim($medico_arquivo);

    //     $nome_base = explode(' ',$medico_base);
    //     $nome_arquivo = explode(' ',$medico_arquivo);

    //     $primeiro_nome_base = trim(strtoupper(Comum::trata_nome($nome_base[0])));
    //     $ultimo_nome_base = trim(strtoupper(Comum::trata_nome($nome_base[count($nome_base)-1])));

    //     $primeiro_nome_arquivo = trim(strtoupper(Comum::trata_nome($nome_arquivo[0])));
    //     $ultimo_nome_arquivo = trim(strtoupper(Comum::trata_nome($nome_arquivo[count($nome_arquivo)-1])));

    //     //Se o primeiro e o último nome do médico são iguais ao cadastrado no sistema
    //     if($primeiro_nome_base == $primeiro_nome_arquivo && $ultimo_nome_base == $ultimo_nome_arquivo){
    //         return true;
    //     } else {

    //         $medico_formatado = "";
    //         //Se o nome possui '-' ou '(' indicando UF ou especialidade
    //         //recupera o nome da posição zero até o registro encontrado
    //         if($medico_formatado = strstr($medico_base, '-', true)){
    //             $medico_base = $medico_formatado;
    //         }

    //         if($medico_formatado = strstr($medico_base, '(', true)){
    //             $medico_base = $medico_formatado;
    //         }


    //         $medico_base = trim($medico_base);
    //         $nome_base = explode(' ',$medico_base);
    //         $primeiro_nome_base = trim(strtoupper(Comum::trata_nome($nome_base[0])));
    //         $ultimo_nome_base = trim(strtoupper(Comum::trata_nome($nome_base[count($nome_base)-1])));


    //         //Verifica se existe o prefixo de doutor (Dr.) no nome em nosso cadastro
    //         if(stripos($primeiro_nome_base,'DR') !== false){
    //             //Neste caso o segundo nome será utilizado
    //             $primeiro_nome_base = trim(strtoupper(Comum::trata_nome($nome_base[1])));
               
    //         }

    //         //compara os registros 
    //         if($primeiro_nome_base == $primeiro_nome_arquivo && $ultimo_nome_base == $ultimo_nome_arquivo){
    //             return true;
    //         }

    //         return false;
    //     }
    // }

    public function grava_log_arquivo($nome_arquivo,$linha){
        $path_resultado = APP . 'tmp' . DS;
        $caminho_arquivo = $path_resultado.$nome_arquivo; 
        file_put_contents($caminho_arquivo, $linha, FILE_APPEND);
    }

}//fim class
?>