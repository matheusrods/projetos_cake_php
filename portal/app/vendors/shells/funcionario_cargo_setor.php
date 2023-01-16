<?php
/**
 * Script para gerar o mvc e evoluir para o crud
 *  
 * 
 * Modo de usar:
 * 
 *  $path_portal/cake/console/cake -app $path_portal/app generate_crud comando tabela
 * 
 * 
 * 
 * @author Willians P Pedroso 30/05/2017
 */
class FuncionarioCargoSetorShell extends Shell{
    
    /**
     * Metodo para iniciar o script como o contrutor da classecls
     * 
     */
    public function main()
    {
    	//inciando o comando
      echo "Iniciando o Script de Atualizacao\n";

      //pegando o path da planilha
      $path = "app/tmp/importacao_dados/";
      $arquivo = "";
      if(!empty($this->args[0])) {
        $arquivo = $this->args[0];
      } else {
        echo "Favor colocar o arquivo no caminho: $portal/app/tmp/importacao_dados/ \n";
        exit;
      }
      
      //verifica se o arquivo existe
      $arquivo_ler = $path.$arquivo;

      $this->ler_arquivo($arquivo_ler);
      

    } //fim main

    /**
     * 
     */
    public function ler_arquivo($arquivo_ler) 
    {

        $this->Funcionario =& ClassRegistry::Init('Funcionario');
        $this->Setor =& ClassRegistry::Init('Setor');
        $this->ClienteFuncionario =& ClassRegistry::Init('ClienteFuncionario');
        $this->FuncionarioSetorCargo =& ClassRegistry::Init('FuncionarioSetorCargo');

        $arquivo = fopen($arquivo_ler, "r");
        $linha = '';
        $l = 1;

        while (!feof($arquivo)) {

            // $linha = utf8_encode(trim(fgets($arquivo)));
            $linha = trim(fgets($arquivo));
            $linha = trim(str_replace("'" , "", str_replace('"' , '', $linha)));
            $linha = trim(str_replace('  ' , ' ', $linha));

            $funcionario = "";
            $setor = "";

            //pula a primeira linha
            if($l > 1) {
                
                if((strlen(str_replace(';', '', $linha))) >0 ){

                    $dados = explode(';', $linha );
                    
                    $dados_arquivo['setor_descricao'] = isset($dados[1])? $dados[1]:'';
                    $dados_arquivo['cpf'] = isset($dados[14])? empty($dados[14])? '': str_pad(Comum::soNumero($dados[14]), 11, 0, STR_PAD_LEFT):'';
                    
                    if(count($dados) >= 56){

                        //verifica se o funcionario tem setor null
                        $conditions = array('Funcionario.cpf' => $dados_arquivo['cpf'], 'FuncionarioSetorCargo.codigo_setor' => null);
                        $joins = array(
                            array(
                                'table' => $this->ClienteFuncionario->databaseTable.'.'.$this->ClienteFuncionario->tableSchema.'.'.$this->ClienteFuncionario->useTable, 
                                'alias' => 'ClienteFuncionario',
                                'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
                                ),
                            array(
                                'table' => $this->FuncionarioSetorCargo->databaseTable.'.'.$this->FuncionarioSetorCargo->tableSchema.'.'.$this->FuncionarioSetorCargo->useTable, 
                                'alias' => 'FuncionarioSetorCargo',
                                'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
                                ),
                            );

                        $fields = "FuncionarioSetorCargo.codigo, FuncionarioSetorCargo.codigo_cliente";
                        $funcionario = $this->Funcionario->find("first", compact('conditions','joins','fields'));

                        if(!empty($funcionario)) {

                            $codigo_cliente = $funcionario["FuncionarioSetorCargo"]["codigo_cliente"];
                            $codigo_funcionario_setor_cargo = $funcionario["FuncionarioSetorCargo"]["codigo"];

                            //pega o codigo do setores
                            $descricao_setor = substr(trim($dados_arquivo['setor_descricao']),0,50);
                            $cond_setor = array(
                                                // "Setor.codigo_cliente" => $codigo_cliente,
                                                "Setor.descricao LIKE '%".($descricao_setor)."%'",
                                                "Setor.ativo" => 1
                                            );
                            $field_setor =  array('Setor.codigo');
                            $setor = $this->Setor->find('first',array( 
                                                                'recursive' => -1,
                                                                'conditions' => $cond_setor, 
                                                                'fields' => $field_setor,)
                            );

                            $codigo_setor = $setor["Setor"]["codigo"];
                            if(empty($setor)) {
                                $dados_setor = array(
                                                    'Setor' => array(
                                                        'descricao' => $descricao_setor,
                                                        'codigo_cliente' => $codigo_cliente
                                                        )
                                                    );
                                $_SESSION['Auth']['Usuario']['codigo'] = 64915;
                                $_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
                                $setor = $this->Setor->importacao_setor_unidade($dados_setor);
                                // $insert = "INSERT INTO RHHealth.dbo.[setores] ([descricao], [codigo_cliente], [ativo], [data_inclusao], [codigo_usuario_inclusao], [codigo_empresa]) VALUES ('".$descricao_setor."', ".$codigo_cliente.", '1', '20170608 17:08:00', 64915,1)\n";
                                // file_put_contents("app/tmp/importacao_dados/inserts_setores.sql", $insert, FILE_APPEND);


                                pr($setor);

                                // $codigo_setor = $setor["Setor"]["codigo"];
                                // echo "inserido o setor:" . $codigo_setor."\n";
                            }

                            print "$l Atualizando:" . $codigo_cliente ."--". $codigo_setor."--".$codigo_funcionario_setor_cargo."\n";

                            //atualiza a funcionario setor cargo
                            $dados_funcionario_setor_cargo = array(
                                'FuncionarioSetorCargo' => array(
                                    'codigo_cliente' => $codigo_cliente,
                                    'codigo_setor' => $codigo_setor,
                                    'codigo'        => $codigo_funcionario_setor_cargo
                                    )
                            );

                            $this->FuncionarioSetorCargo->atualizar($dados_funcionario_setor_cargo);

                        }


                        

                    }
                    else{
                        // $retorno_linha_invalida['Erro'][$c]['erros'] = array('Unidade' => array( 0 => utf8_decode("Linha inválida. Quantidade de colunas inválidas.")));
                        // $retorno_linha_invalida['Erro'][$c]['dados'] = $dados_arquivo;
                    } //fim count linha 56
                }
            }
            

            $l++;
            
        }

        fclose($arquivo);


    } 


}//fim GenerateCrud
