<?php
/**
 * Script para reprocessar os dados da remessa bancaria pegando os dados do log.
 * 
 * Modo de usar:
 * 
 *  $path_portal/cake/console/cake -app $path_portal/app remessa_bancaria_reprocessamento comando
 * 
 * irá gerar os arquivos na pasta:
 * 
 *  $path_portal/app/tmp
 * 
 * 
 * @author Willians P Pedroso 08/03/2018
 */
class RemessaBancariaReprocessamentoShell extends Shell {
    
    //atributo que instancia as models
    var $uses = array(
		'RemessaBancaria',
        'RemessaRetorno',
		'LogIntegracao',
		'Pedido',
    	);

    //atributos da class
    public $linha_header = '';
    public $linha_detalhes = '';
    public $linha_trailler = '';
    public $brancos = '';
    public $zeros = 0;
    public $agencia = "367";
    public $conta = "11441";
    public $digito = "0";
    public $numero_seq = 0;
    public $numero_arquivo = '';
    public $mensagens = '';
    public $cont_titulo_existente = 0;
    public $cont_novo_titulo = 0;
    public $cont_cliente_erro = 0;
    public $cont_titulo_baixado = 0;
    public $cont_titulo_cancelado = 0;
    public $cont_titulo_nao_encontrados = 0;
    public $cont_titulo_atualizado = 0;
    public $cont_titulo_erro = 0;
    public $cont_pedido_criado = 0;
    public $cont_pedido_erro = 0;
    public $cont_pedido_ja_existente = 0;
    public $linha_pedido_erro = "";
    public $linha_pedido_existe = "";
    public $codigos_remessas_bancarias = array();
    public $codigo_banco = null;
    public $array_remessa = array();
    public $linhasPQR = "";
    public $linhasTU = "";
    public $sequencial = "";
    public $naveg_codigo_banco = null;
    
    /**
     * Metodo para iniciar o script como o contrutor da classe
     */
    public function main()
    {
    	echo "Iniciando o Script de correcao\n";
    	echo "Pode usar os parametros:\n";
    	echo "reprocessamento_033_dt_pagto_null => reprocessa a remessa bancaria pegando os dados do log para atualizar a data de pagamento\n";
    } //fim main


    /**
     * Metodo para pegar os fornecedores que estao com a latitude/longitude como null
     * 
     */
   	public function reprocessamento_033_dt_pagto_null() 
   	{

   		echo "Iniciando reprocesasmento pela data de integracao do pedido como null e não tem data de pagamento.\n";

        //query para pegar os clientes e buscar os dados no log
        $query_cliente = "
            SELECT retorno, arquivo, codigo_usuario_inclusao, data_inclusao
            FROM RHHealth.dbo.logs_integracoes 
            WHERE codigo_cliente IN (SELECT p.codigo_cliente_pagador
                                    FROM RHHealth.dbo.remessa_bancaria r
                                        INNER JOIN RHHealth.dbo.pedidos p ON r.codigo_pedido = p.codigo 
                                    WHERE r.codigo_banco = '033'
                                        AND r.codigo_pedido IS NOT NULL
                                        AND r.data_pagamento IS NULL
                                        AND p.data_integracao IS NULL)
                AND descricao = 'ATUALIZADO'
            ORDER BY data_inclusao ";
        //executa no banco
        $dados = $this->RemessaBancaria->query($query_cliente);

        if(empty($dados)) {
            echo "nao existe dados para serem atualizados!";
            exit;
        }
        
        $this->codigo_banco = '033';

        $codigo_remessas = array();
        
        // pr($dados);exit;
        //varre os dados da query
        foreach($dados as $key => $dado) {

            $linha  = $dado[0]['retorno'];
            $arquivo = $dado[0]['arquivo'];
            $codigo_usuario = $dado[0]['codigo_usuario_inclusao'];
        
            //separa a linha
            $segT = substr($linha,0,242);
            $segU = substr($linha,242);
           
            //separa os dados
            $this->setSegT($segT, $codigo_usuario);
            $this->setSegU($segU);
           
            if($this->array_remessa['RemessaBancaria']['data_pagamento'] == '' && $this->array_remessa['RemessaBancaria']['codigo_pedido'] != "") {


                $this->linhasTU = $linha;

                // $codigo_remessas[] = $this->array_remessa['RemessaBancaria']['codigo'];

                //pr("linha {$key}");
                // pr($this->array_remessa);
                $this->gravaRetorno240();
                
            } //fim if data pagamento

        } //fim foreach linhas remessa


        // echo "codigos:\n".implode(",",$codigo_remessas)."\n\n";

        echo "Titulos baixados: " . $this->cont_titulo_baixado. "\n";
        echo "Titulos atualizado: " . $this->cont_titulo_atualizado. "\n";
        echo "Titulos cont_titulo_erro: " . $this->cont_titulo_erro. "\n";


   	}//fim metodo


    /**
     * [setSegT description]
     * 
     * metodo para leitura do segT
     * 
     * 
     * @param [type] $linha [description]
     */
    private function setSegT($linha, $codigo_usuario = null)
    {
        $this->sequencial = substr($linha,8,13); //009 - 013 N sequencial do registro no lote

        //campos para serem carregados na tabela
        $nosso_numero           = substr($linha,40,13); //041 –053 Identificação do título no Banco
        
        //pega o valor cobrado para ver se teve desconto/abatimento
        $valor_cobrado          = comum::formatarValorCnab(substr($linha, 77,15)); //078 – 092 Valor nominal do título

        //campos
        $valor_tarifa           = comum::formatarValorCnab(substr($linha, 193,15)); //194 – 208 Valor da Tarifa/Custas
        $data_vencimento        = comum::formatarDataCnab240(trim(substr($linha,69,8))); //070 – 077 Data do vencimento do título
        $codigo_usuario_retorno = $codigo_usuario;

        //pega a ocorrencia para atualizar a tabela
        $ocorrencia = substr($linha,15,2); //016 - 017 Código de movimento (ocorrência)

        //pega na tabela remessa_retorno o codigo da ocorrencia
        $remessa_retorno = $this->RemessaRetorno->find('first', array(
                                                                    'conditions' => array(  'RemessaRetorno.codigo_ocorrencia' => $ocorrencia, 
                                                                                            'RemessaRetorno.codigo_banco' => $this->codigo_banco)
                                                                ));

        //verifica se ja carregou o titulo        
        $this->array_remessa = $this->RemessaBancaria->find('first', array(
                                            'recursive' => -1,
                                            'conditions' => array('RemessaBancaria.nosso_numero' => $nosso_numero, 
                                                                'RemessaBancaria.codigo_banco' => $this->codigo_banco,
                                                                'RemessaBancaria.codigo_pedido IS NOT NULL')
                                            ));
        if(!empty($this->array_remessa)) {

            if(!empty($this->array_remessa["RemessaBancaria"]["codigo"])) {
                $codigo_remessa = $this->array_remessa["RemessaBancaria"]["codigo"];
            }

            $this->array_remessa["ocorrencia"] = $ocorrencia;

            //seta o nova ocorrencia            
            $this->array_remessa["RemessaBancaria"]["codigo_remessa_retorno"]           = $remessa_retorno['RemessaRetorno']['codigo'];         
            $this->array_remessa["RemessaBancaria"]["valor_tarifa"]                     = $valor_tarifa;
            $this->array_remessa["RemessaBancaria"]["codigo_usuario_retorno"]           = $codigo_usuario_retorno;
            $this->array_remessa['RemessaBancaria']["data_vencimento"]                  = $data_vencimento;

        }

    } //fim setSegT()

    /**
     * Metodo para leitura da linha do segmento u
     *
     */
    private function setSegU($linha)
    {
        
        //verifica se a linha ja foi carregada 
        if(!empty($this->array_remessa)) {
            $this->array_remessa["RemessaBancaria"]["valor_abatimento"]     = comum::formatarValorCnab(substr($linha, 47,15)); //048 - 062 Valor do Abatimento Concedido/Cancelado
            $this->array_remessa["RemessaBancaria"]["valor_iof"]            = comum::formatarValorCnab(substr($linha, 62,15)); //063 - 077Valor do IOF recolhido
            $this->array_remessa["RemessaBancaria"]["valor_principal"]      = comum::formatarValorCnab(substr($linha, 77,15)); //078 - 092 Valor pago pelo Pagador
            $this->array_remessa["RemessaBancaria"]["valor_pago"]           = $this->array_remessa["RemessaBancaria"]["valor_principal"];
            $this->array_remessa["RemessaBancaria"]["valor_juros"]          = comum::formatarValorCnab(substr($linha, 17,15)); //018 - 032 Juros / Multa / Encargos
            $this->array_remessa["RemessaBancaria"]["data_credito"]         = comum::formatarDataCnab240(trim(substr($linha, 145,8))); // 146 - 153 Data da efetivação do crédito
            
            //setado com outro indice pois existe uma validacao se foi realizado o pagamento
            $this->array_remessa["data_pagamento"]                          = comum::formatarDataCnab240(trim(substr($linha,137,8))); //138 - 145 Data da ocorrência
        }


    } //fim setSegU($linha,$arquivo);

    /**
     * metodo para gravar o retorno do arquivo 240
     *
     */
    private function gravaRetorno240()
    {

        // print $linha."<br>";
        //verifica se a linha ja foi carregada 
        if(!empty($this->array_remessa)) {

            $this->array_remessa["RemessaBancaria"]["linha_retorno"] = $this->linhasTU;
            
            //verifica se existe o codigo de retorno, se existir significa que esta sendo reprocessado
            if(empty($this->array_remessa["RemessaBancaria"]["codigo_remessa_retorno"])) {

                //pega o status da ocorrencia
                $status = 2;

                // $remessa['RemessaBancaria']['codigo'] = $remessa_bancaria['RemessaBancaria']['codigo'];
                $this->array_remessa["RemessaBancaria"]["codigo_remessa_status"] = $status;

                //verifica foi pago
                if($status == 2) {
                    //seta o codigo do banco naveg
                    // $this->array_remessa['RemessaBancaria']['codigo_banco_naveg'] = $this->naveg_codigo_banco;
                    //seta a data do pagamento quando houver pagamento
                    $this->array_remessa["RemessaBancaria"]["data_pagamento"] = $this->array_remessa["data_pagamento"];
                }

                //atualiza
                if(!$this->RemessaBancaria->atualizar($this->array_remessa)){
                    $this->$cont_titulo_erro++;
                    echo "033 - Nao foi possivel atualizar o titulo: " . $this->array_remessa['RemessaBancaria']['nosso_numero']."\n";
                }

                
                //setado com pago
                if($status == 2) {
                    $this->cont_titulo_baixado++;
                } 

            } 
            else {
                //verifica se tem a data e o codigo cliente para colocar o status de pago
                if(isset($this->array_remessa['RemessaBancaria']['codigo_cliente']) && isset($this->array_remessa['RemessaBancaria']['data_emissao'])) {
                    if(!is_null($this->array_remessa['RemessaBancaria']['codigo_cliente']) && !is_null($this->array_remessa['RemessaBancaria']['data_emissao'])) {
                        
                        //seta o status
                        $this->array_remessa["RemessaBancaria"]["codigo_remessa_status"] = 2; //pago
                        
                        //seta o codigo do banco naveg
                        // $this->array_remessa['RemessaBancaria']['codigo_banco_naveg'] = $this->naveg_codigo_banco;

                        //seta a data do pagamento quando houver pagamento
                        $this->array_remessa["RemessaBancaria"]["data_pagamento"] = $this->array_remessa["data_pagamento"];
                        
                        $this->cont_titulo_baixado++;

                    } //fim if  
                } 
                else {
                    $this->cont_titulo_atualizado++;
                }//fim codigo cliente e data de emissao
                
                //atualiza              
                if(!$this->RemessaBancaria->atualizar($this->array_remessa)){
                    $this->$cont_titulo_erro++;
                    echo "033 - Nao foi possivel atualizar o titulo: " . $this->array_remessa['RemessaBancaria']['nosso_numero']."\n";
                }
            }
        }//fim remessa bancaria  
        

    }//fim 


}
?>