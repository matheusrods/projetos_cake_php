<?php

class AutotracFaturamentosController extends AppController {

    public $name = 'AutotracFaturamentos';
    public $layout = 'default';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax');
    public $uses = array(
            'AutotracParametro',
            'AutotracFaturamento',
            'AutotracInclusaoExclusao',
            'AutotracTaxa',
            'TTermTerminal',
            'Pedido',
            'ItemPedido',
            'DetalheItemPedidoManual',
            'Produto',
            'Servico',
            'AutotracExcecao'
        );    
 

    function index() {       
        $this->data['AutotracFaturamento'] = $this->Filtros->controla_sessao($this->data, $this->AutotracFaturamento->name);
        $this->pageTitle = 'Autotrac';
        $mes_referencia = Comum::listMeses();
        $ano_referencia = Comum::listAnos('2015');          

        $this->set(compact('mes_referencia','ano_referencia'))  ;   
    }  
    function listagem(){        
        $filtros = $this->Filtros->controla_sessao($this->data, 'AutotracFaturamento');
        $this->layout = 'ajax';
        
        unset($filtros['arquivo']);

        $inclusao_exclusao = null;
        $faturamento       = null;
        $pedido            = null;
        $pedido_faturado   = null;

        if(!is_null($filtros) && count($filtros) > 1){
            $inclusao_exclusao = $this->AutotracInclusaoExclusao->find('count', array('conditions' => $filtros));        
            $faturamento       = $this->AutotracFaturamento->find('count', array('conditions' => $filtros));
            $pedido            = $this->ItemPedido->existe_pedido_perido_produto($filtros['mes_referencia'], $filtros['ano_referencia'], Produto::AUTOTRAC, 2);
            
            $conditions = $filtros;
            $conditions[] = 'Pedido.data_integracao IS NOT NULL';
            $pedido_faturado   = $this->Pedido->find('count', array('conditions' => $conditions));
             
        }
        $mes_referencia = Comum::listMeses();
        $ano_referencia = Comum::listAnos('2015');     

        $this->set(compact('mes_referencia', 'ano_referencia', 'inclusao_exclusao', 'faturamento', 'pedido', 'pedido_faturado'))  ;   
    }
    function importar_excel_inclusao(){
        $this->layout = 'ajax';
        set_time_limit(0);
        $this->loadModel('AutotracInclusaoExclusao');
        $filtros = $this->Filtros->controla_sessao($this->data, 'AutotracFaturamento');
        
        $destino = $this->upload($this->data['AutotracInclusaoExclusao']['arquivo']);
        
        //joga informações do arquivo na tabela
        if($destino){
            $this->BSession->setFlash("envio_arquivo");            
            $reader = $this->ler_arquivo($destino);
            $erros = array();
            foreach($reader->sheets as $k =>$data) {  
                if($k==0){
                    foreach($data['cells'] as $key => $linha){                        
                        if($key == 9){                            
                            $invalido = true;
                            if(isset($linha[2])){                              
                                $periodo = str_replace(array('Período: ', ' 00:00 Até ', ' 23:59'), ' ', utf8_encode($linha[2]))  ;
                                $periodo = explode(' ', trim($periodo));

                                if(count($periodo) == 2){
                                    $inicio = explode('/', $periodo[0]);

                                    if(count($inicio) == 3){
                                        $mes_referencia = $inicio[1];
                                        $ano_referencia = $inicio[2];
                                        if($mes_referencia == $filtros['mes_referencia'] && $ano_referencia == $filtros['ano_referencia'])
                                            $invalido = false;                                        
                                    }
                                }
                            }
                            if($invalido){
                                $this->BSession->setFlash("arquivo_layout_invalido");                
                                break;
                            }
                        }else if($key==10){

                            if(!$this->AutotracInclusaoExclusao->valida_nome_colunas($linha)){
                                $this->BSession->setFlash("arquivo_layout_invalido");                
                                break;
                            }
                        }else if($key > 10){
                            if($mes_referencia && $ano_referencia){                                
                                if(trim($linha[8]) != ''){
                                    $dado = array( 
                                                'AutotracInclusaoExclusao' => 
                                                    array(
                                                        'mes_referencia' => $mes_referencia,
                                                        'ano_referencia' => $ano_referencia,
                                                        'placa'          => substr(str_replace(' ', '', trim($linha[2])),0,7),
                                                        'nome_conta'     => trim($linha[5]),
                                                        'tipo_servico'   => trim($linha[7]),
                                                        'status'         => trim($linha[8]),
                                                        'data_status'    => trim($linha[9]),
                                                        'usuario'        => trim($linha[12]),
                                                    )
                                        );                                
                                    if(!$this->AutotracInclusaoExclusao->incluir($dado))
                                        $erros[] = $key;
                                }
                            }
                        }
                    }
                }
            }
            unlink($destino);
        }else
            $this->BSession->setFlash("envio_arquivo_error");        
        $this->redirect(array('action' => 'index'));        
    }

    function importar_excel_faturamento(){
        App::Import('Component',array('DbbuonnyGuardian'));       
        set_time_limit(0);
        $this->layout = 'ajax';
        $this->loadModel('AutotracFaturamento');
        $filtros = $this->Filtros->controla_sessao($this->data, 'AutotracFaturamento');
        $destino = $this->upload($this->data['AutotracFaturamento']['arquivo']);

        if($destino){
             $this->BSession->setFlash("envio_arquivo");            
            $reader = $this->ler_arquivo($destino);
            
            $erros = array();
            $soma = 0;
            $parametros = false;
            $taxas_buonny = $this->AutotracParametro->find('first', array('order' => array('AutotracParametro.codigo DESC')));
            $taxas_buonny = $taxas_buonny['AutotracParametro'];

            foreach($reader->sheets as $k => $data) {  
                if($k==0){     
                    foreach($data['cells'] as $key => $linha){
                        //se achar a linha com os parametros                     
                        if($parametros){
                            
                            $taxas = $this->AutotracTaxa->find('first',
                                array('conditions' => array(
                                        'AutotracTaxa.validade_de >=' => $parametro_perido_de.' 00:00:00',
                                        'AutotracTaxa.validade_ate <=' => $parametro_perido_ate.' 23:59:59',
                                    )));                                    
                            if(!$taxas){  
                            
                                $dados_parametro = array('AutotracTaxa' => 
                                    array(
                                        'validade_de'                     => $parametro_perido_de,
                                        'validade_ate'                    => $parametro_perido_ate,
                                        'assinatura_mensal_mct_1_hora'    => !($reader->raw($key,26)) ? '0' : $reader->raw($key,26),
                                        'alarme_panico'                   => !($reader->raw($key,61)) ? '0' : $reader->raw($key,61),
                                        'mensagem'                        => !($reader->raw($key,98)) ? '0' : $reader->raw($key,98),
                                        'caracter_mensagem_texto'         => !($reader->raw(($key+2),26)) ? '0' : $reader->raw(($key+2),26),
                                        'pedido_posicao_adicional'        => !($reader->raw(($key+2),61)) ? '0' : $reader->raw(($key+2),61),
                                        'definicao_grupos_ass_mct_grupos' => !($reader->raw(($key+2),98)) ? '0' : $reader->raw(($key+2),98),
                                        'macro_mct'                       => !($reader->raw(($key+4),26)) ? '0' : $reader->raw(($key+4),26),
                                        'mensagem_grupo_02_49_mcts'       => !($reader->raw(($key+4),61)) ? '0' : $reader->raw(($key+4),61),
                                        'mensagem_grupo_50_99_mcts'       => !($reader->raw(($key+4),98)) ? '0' : $reader->raw(($key+4),98),
                                        'mensagem_grupo_acima_99_mcts'    => !($reader->raw(($key+5),26)) ? '0' : $reader->raw(($key+5),26),
                                        'mensagem_prioritaria'            => !($reader->raw(($key+5),61)) ? '0' : $reader->raw(($key+5),61),
                                        'mensagem_grupo_prior_02_49'      => !($reader->raw(($key+5),98)) ? '0' : $reader->raw(($key+5),98),
                                        'mensagem_grupo_prior_50_99'      => !($reader->raw(($key+8),26)) ? '0' : $reader->raw(($key+8),26),
                                        'mensagem_grupo_prior_acima_99'   => !($reader->raw(($key+8),61)) ? '0' : $reader->raw(($key+8),61),
                                        'copia_qmass_mensagem'            => !($reader->raw(($key+8),98)) ? '0' : $reader->raw(($key+8),98),
                                        'copia_qmass_mensagem_prior'      => !($reader->raw(($key+10),26)) ? '0' : $reader->raw(($key+10),26),
                                        'copia_qmass_alarme_panico'       => !($reader->raw(($key+10),61)) ? '0' : $reader->raw(($key+10),61),
                                        'copia_qmass_caracter'            => !($reader->raw(($key+10),98)) ? '0' : $reader->raw(($key+10),98),
                                        'copia_qmass_posicao'             => !($reader->raw(($key+12),26)) ? '0' : $reader->raw(($key+12),26),
                                        'comando_alerta_obc'              => !($reader->raw(($key+12),61)) ? '0' : $reader->raw(($key+12),61),
                                        'caracter_obc'                    => !($reader->raw(($key+12),98)) ? '0' : $reader->raw(($key+12),98),
                                        'copia_qmass_comando_obc'         => !($reader->raw(($key+13),26)) ? '0' : $reader->raw(($key+13),26),
                                        'copia_qmass_caracter_obc'        => !($reader->raw(($key+13),61)) ? '0' : $reader->raw(($key+13),61),
                                        'transferencia_mct_contas'        => !($reader->raw(($key+13),98)) ? '0' : $reader->raw(($key+13),98),
                                        'desativacao_reativacao_mct'      => !($reader->raw(($key+16),26)) ? '0' : $reader->raw(($key+16),26),
                                        'inclusao_exclusao_mct'           => !($reader->raw(($key+16),61)) ? '0' : $reader->raw(($key+16),61),
                                        'macro_convite_autotrac_cam'      => !($reader->raw(($key+16),98)) ? '0' : $reader->raw(($key+16),98)
                                    )
                                );                                   
                                $taxas = $this->AutotracTaxa->incluir($dados_parametro);
                            }
                            if($taxas){  
                                $faturamentos = $this->AutotracFaturamento->find('all',array
                                        ('conditions' => array(
                                            'mes_referencia' => $mes_referencia,
                                            'ano_referencia' => $ano_referencia
                                        ))
                                    );
                                //cálculos buonny
                                foreach($faturamentos as $fat){
                                    $fat = $fat['AutotracFaturamento'];
                                    $dados_atualizar['AutotracFaturamento']['inc_exc_ac_valor'] = round(($fat['inc_exc_ac_quantidade'] * $taxas['AutotracTaxa']['inclusao_exclusao_mct']),2);                                    
                                    $dados_atualizar['AutotracFaturamento']['inc_exc_buonny_valor'] = round(($fat['posicao_adicional_valor'] + $taxas_buonny['taxa_administrativa']), 2);
                                    $dados_atualizar['AutotracFaturamento']['inc_exc_buonny_quantidade'] = ceil($dados_atualizar['AutotracFaturamento']['inc_exc_buonny_valor']/$taxas['AutotracTaxa']['pedido_posicao_adicional']);                                    
                                    $dados_atualizar['AutotracFaturamento']['inc_exc_buonny_valor'] = round(($dados_atualizar['AutotracFaturamento']['inc_exc_buonny_quantidade'] * $taxas['AutotracTaxa']['pedido_posicao_adicional']),2);
                                    $dados_atualizar['AutotracFaturamento']['total'] = round(($fat['total'] + $dados_atualizar['AutotracFaturamento']['inc_exc_ac_valor']),2);
                                    $dados_atualizar['AutotracFaturamento']['total_buonny'] = round(($dados_atualizar['AutotracFaturamento']['total'] - $fat['posicao_adicional_valor'] + $dados_atualizar['AutotracFaturamento']['inc_exc_buonny_valor']),2);
                                    $dados_atualizar['AutotracFaturamento']['total_tributo'] = round(($dados_atualizar['AutotracFaturamento']['total_buonny'] + ($dados_atualizar['AutotracFaturamento']['total_buonny'] / 100 * $taxas_buonny['percentual_imposto'])),2);
                                    $dados_atualizar['AutotracFaturamento']['codigo'] = $fat['codigo'];                                    
                                    $this->AutotracFaturamento->atualizar($dados_atualizar);
                                }                                
                            }else
                                $erros[] = $key;
                            $parametros = false;
                            break;
                        //linha que deverá conter o período
                        }else if($key == 4){                            
                            $invalido = true;
                            if(isset($linha[37]) &&
                               utf8_encode($linha[37]) == 'Período:' && utf8_encode($linha[52]) == 'até' &&  
                               !empty($linha[43]) && !empty($linha[56])
                              ){

                                $inicio = explode('/', $linha[43]);
                                $fim    = explode('/', $linha[56]);

                                if(count($inicio) == 3 && count($fim) == 3){
                                    $mes_referencia = $inicio[1];
                                    $ano_referencia = $inicio[2];
                                    if($mes_referencia == $filtros['mes_referencia'] && $ano_referencia == $filtros['ano_referencia'])
                                        $invalido = false;                                        
                                }
                            }                         
                            if($invalido){
                                $this->BSession->setFlash("arquivo_layout_invalido");                
                                break;
                            }
                        //linha que deverá conter os nomes da coluna, faz a validação para ver se está no layout
                        }else if($key == 10){
                            if(!$this->AutotracFaturamento->valida_nome_colunas($linha)){
                                $this->BSession->setFlash("arquivo_layout_invalido");                
                                break;
                            }                                                
                        //a partir dessa linha deverá conter as informações que serão importadas
                        }else if($key > 11){  
                            //verifica se a linha está no padrão                          
                            if(     
                            !empty($linha[1]) && is_numeric($linha[1]) && trim($linha[1]) != 'Conta' && 
                            trim($linha[1]) != 'MCT' && trim($linha[1]) != 'Conta Principal: 56810496 - SVW BUONNY QMASS' &&
                            trim($linha[2]) != utf8_decode('Ass. Básica') && trim($linha[2]) != 'Qtd.' &&
                            trim($linha[21]) != utf8_decode('Tabela de preços dos serviços de comunicação válido no período de') &&
                            !isset($linha[6]) && $mes_referencia && $ano_referencia){                                
                                
                                //busca informações do veículo pelo número do terminal
                                $data_inicio = $ano_referencia.str_pad($mes_referencia, 2, '0', STR_PAD_LEFT).'01 00:00:00' ;
                                $data_fim = $ano_referencia.str_pad($mes_referencia, 2, '0', STR_PAD_LEFT).cal_days_in_month(CAL_GREGORIAN, $mes_referencia , $ano_referencia).' 23:59:59';                                
                                $dados_veiculo = $this->TTermTerminal->buscarDadosViagemPorTerminalPeriodo($linha[1], $data_inicio, $data_fim);

                                $transportadora = array();
                                $placa = array();
                                $transportadoras = array();
                                $placas = array();       
                                $inc_exc = array();
                              
                                //$clientes_excecao = $this->AutotracExcecao->lista_codigo_guardian_cliente_excecao();
                     
                                if(count($dados_veiculo) > 0){
                                    //conta na planilha de inclusão e exclusão quantas vezes foi incluído e excluída a placa
                                    foreach($dados_veiculo as $dado_veiculo){
                                        if((is_null($transportadora) || !array_key_exists($dado_veiculo[0]['TPjurPessoaJuridica__pjur_pess'], $transportadoras)) ){
                                            $transportadora = array(                                                   
                                                   'data_inicio' => $dado_veiculo[0]['TViagViagem__viag_data_inicio'],
                                                   'codigo_sm'   => $dado_veiculo[0]['TViagViagem__viag_codigo_sm'],
                                                   'codigo_cliente'  => $dado_veiculo[0]['codigo'],
                                                );
                                            $transportadoras[$dado_veiculo[0]['TPjurPessoaJuridica__pjur_pess']] = $transportadora;

                                        }
                                        if(is_null($placa) || !in_array($dado_veiculo[0]['TVeicVeiculo__veic_placa'], $placas)){
                                            $placa = $dado_veiculo[0]['TVeicVeiculo__veic_placa'];
                                            $placas[] = $placa;
                                        }

                                    }  
                                    $inc_exc = $this->AutotracInclusaoExclusao->find('all', array(
                                        'conditions' => array(
                                            'AutotracInclusaoExclusao.placa' => $placas,
                                            'AutotracInclusaoExclusao.mes_referencia' => $mes_referencia,
                                            'AutotracInclusaoExclusao.ano_referencia' => $ano_referencia
                                            )));
                                }  
                                if(count($transportadoras) <= 0){                                  
                                    $transportadoras = array('0' =>
                                        array(
                                            'data_inicio' => null,
                                            'codigo_sm' =>null,
                                            'codigo_cliente' =>null,
                                        ));
                                }                                 
                                $i = 0;                               
                              
                                foreach ($transportadoras as $chave => $transportadora) {
                                    $dados = array();
                                    $dados['mes_referencia']                  = $mes_referencia;
                                    $dados['ano_referencia']                  = $ano_referencia;
                                    $dados['numero_terminal']                 = trim($linha[1]);
                                    $dados['placa']                           = isset($placas[0]) ? $placas[0] : null;
                                    $dados['codigo_terminal']                 = isset($dados_veiculo[0]) ? $dados_veiculo[0][0]['TTermTerminal__term_codigo'] : 0;
                                    $dados['codigo_transportadora']           = $transportadora['codigo_cliente'];
                                    $dados['ass_basica_quantidade']           = $reader->raw($key,5)/count($transportadoras);
                                    $dados['ass_basica_valor']                = $reader->raw($key,7)/count($transportadoras);
                                    $colunas = array(                                    
                                    'mensagem'             => array('quantidade' => $reader->raw($key, 10), 'valor' => $reader->raw($key, 12)),
                                    'caracter'             => array('quantidade' => $reader->raw($key, 14), 'valor' => $reader->raw($key, 17)),
                                    'comando_alerta'       => array('quantidade' => $reader->raw($key, 19), 'valor' => $reader->raw($key, 22)),
                                    'caracter_obc'         => array('quantidade' => $reader->raw($key, 24), 'valor' => $reader->raw($key, 27)),
                                    'mensagem_prioritaria' => array('quantidade' => $reader->raw($key, 30), 'valor' => $reader->raw($key, 31)),
                                    'posicao_adicional'    => array('quantidade' => $reader->raw($key, 35), 'valor' => $reader->raw($key, 38)), 
                                    'macro'                => array('quantidade' => $reader->raw($key, 42), 'valor' => $reader->raw($key, 45)),
                                    'def_grupo'            => array('quantidade' => $reader->raw($key, 47), 'valor' => $reader->raw($key, 48)),
                                    'alarme_panico'        => array('quantidade' => $reader->raw($key, 51), 'valor' => $reader->raw($key, 53)),
                                    'mensagem_grupo'       => array('quantidade' => $reader->raw($key, 58), 'valor' => $reader->raw($key, 59)),
                                    'prior_grupo'          => array('quantidade' => $reader->raw($key, 63), 'valor' => $reader->raw($key, 67)),
                                    'transf_mct'           => array('quantidade' => $reader->raw($key, 73), 'valor' => $reader->raw($key, 74)),
                                    'desativ_reat'         => array('quantidade' => $reader->raw($key, 78), 'valor' => $reader->raw($key, 81)),
                                    'qmass'                => array('quantidade' => $reader->raw($key, 84), 'valor' => $reader->raw($key, 86)),
                                    'qtweb'                => array('quantidade' => $reader->raw($key, 100), 'valor' => $reader->raw($key, 102)),
                                    'perm_ac'              => array('quantidade' => $reader->raw($key, 104), 'valor' => $reader->raw($key, 108)),
                                    'macro_ac'             => array('quantidade' => $reader->raw($key, 93), 'valor' => $reader->raw($key, 97)),
                                    );

                                    foreach($colunas as $chave_coluna => $coluna){
                                        if(is_numeric($coluna['quantidade']) && $coluna['quantidade'] > 0){
                                            $dados[$chave_coluna.'_quantidade'] = $this->arredonda_qtd($coluna['quantidade'] , $i, count($transportadoras));
                                            $dados[$chave_coluna.'_valor'] = round((($coluna['valor']/$coluna['quantidade']) * $dados[$chave_coluna.'_quantidade']), 2);
                                        }else{
                                            $dados[$chave_coluna.'_quantidade'] = 0;
                                            $dados[$chave_coluna.'_valor'] =0;
                                        }
                                    }

                                    $dados['inc_exc_ac_quantidade'] = $this->arredonda_qtd((count($inc_exc)) , $i, count($transportadoras));
                                    $dados['data_ultima_viagem']    = $transportadora['data_inicio'];
                                    $dados['codigo_sm']             = $transportadora['codigo_sm'];
                                    $dados_gravar['AutotracFaturamento']   = $dados;    
                                    $total = 0;
                                    foreach($dados_gravar['AutotracFaturamento'] as $chave_autotrac => $dado){
                                        if(strpos($chave_autotrac,'valor')){                                            
                                            $total += $dado;
                                        }
                                    }
                                    $dados_gravar['AutotracFaturamento']['total'] = $total;     
                              
                                     if(!$this->AutotracFaturamento->incluir($dados_gravar))
                                         $erros[] = 'incluir - linha: '.$key;
                                    $i++;
                                }
                                //}else{
                                //    $erros[] = 'sem dados da viagem - linha: '.$key;
                                //}
                            //se acabou os dados e começou os parametros
                            }else if(trim($linha[21]) == utf8_decode('Tabela de preços dos serviços de comunicação válido no período de')){                            
                                 $parametros = true;
                                 $parametro_perido_de = implode('-', array_reverse(explode('/',$linha[66])));
                                 $parametro_perido_ate = implode('-', array_reverse(explode('/',$linha[80])));
                             }                            
                        }
                    }
                }                
            }            
            unlink($destino);
        }else
            $this->BSession->setFlash("envio_arquivo_error");
        
        $this->redirect(array('action' => 'index'));
    }
    private function upload($arquivo){
        //upload do arquivo
        if ($arquivo['name'] != NULL ) {
            $type = strtolower(end(explode('.', $arquivo['name'])));
            $max_size = (1024*1024)*5;//5 MB
            if ( $type === "xls" && $arquivo['size'] < $max_size ) {
                $destino = APP.DS.'tmp'.DS.urlencode('inclusaoexclusao'.date('YmdHis').$arquivo['name']);
                if ( move_uploaded_file($arquivo['tmp_name'], $destino) == TRUE ) {                    
                    return $destino;
                }                
            }
        }
        return false;   
    }
    private function ler_arquivo($destino){
        require_once APP . 'vendors' . DS . 'excel_reader' . DS . 'excel_reader2.php';
        $reader = new Spreadsheet_Excel_Reader($destino);
        //$reader->dump($row_numbers=false,$col_letters=false,$sheet=0,$table_class='excel');
        $reader->setUTFEncoder('iconv');
        $reader->setOutputEncoding('UTF-8');
        
        return $reader;
    }
    private function arredonda_qtd($valor, $posicao, $quantidade){        
        $divisao = floor($valor/$quantidade);
        if(($posicao+1) == $quantidade){
            $divisao = $valor - ($divisao*$posicao);
            //$divisao = floor($divisao * 100) / 100;

        }
        return $divisao;
    }
    private function arredonda_valor($valor, $posicao, $quantidade){        
        $divisao = floor(($valor/$quantidade) * 100) / 100;
        if(($posicao+1) == $quantidade){
            $divisao = $valor - ($divisao*$posicao);
            $divisao = floor($divisao * 100) / 100;
        }
        return $divisao;
    }
    function desfazer($tipo){        
        set_time_limit(0);
        $this->layout = 'ajax';
        $this->loadModel('AutotracInclusaoExclusao');
        $this->loadModel('AutotracFaturamento');
        $filtros = $this->Filtros->controla_sessao($this->data, 'AutotracFaturamento');
        unset($filtros['arquivo']);
        switch ($tipo) {
            case 'inclusao':
                $linhas = $this->AutotracInclusaoExclusao->find('all', array('conditions' => $filtros, 'fields' => 'codigo'));
                foreach($linhas as $linha){
                    $this->AutotracInclusaoExclusao->delete($linha['AutotracInclusaoExclusao']['codigo']);
                }
                break;
            case 'faturamento':
                $linhas = $this->AutotracFaturamento->find('all', array('conditions' => $filtros, 'fields' => 'codigo'));
                foreach($linhas as $linha){
                    $this->AutotracFaturamento->delete($linha['AutotracFaturamento']['codigo']);
                }
                break;
            case 'pedido':
                $pedidos = $this->ItemPedido->existe_pedido_perido_produto($filtros['mes_referencia'], $filtros['ano_referencia'], Produto::AUTOTRAC, 2);
                foreach($pedidos as $pedido){                    
                    $this->DetalheItemPedidoManual->query("DELETE FROM {$this->DetalheItemPedidoManual->databaseTable}.{$this->DetalheItemPedidoManual->tableSchema}.{$this->DetalheItemPedidoManual->useTable} WHERE codigo_item_pedido = ". $pedido['ItemPedido']['codigo']);
                    $this->ItemPedido->delete($pedido['ItemPedido']['codigo']);
                    $this->Pedido->delete($pedido['Pedido']['codigo']);
                }
                break;
        }
        $this->redirect(array('action' => 'index'));
    }

    function listagem_analitico($tipo_view, $cliente = null, $mes = null, $ano = null){
        $filtros = $this->Filtros->controla_sessao($this->data, 'AutotracFaturamento');
        unset($filtros['arquivo']);
        //$faturamento = $this->AutotracFaturamento->find('count', array('conditions' => $filtros));
        if($tipo_view == 'popup') {
            $this->layout = 'new_window';
        }

        if(!is_null($cliente)){
            $filtros['Transportadora.codigo'] = $cliente;
        }
        if(!is_null($mes)){
            $filtros['mes_referencia'] = $mes;
        }
        if(!is_null($ano)){
            $filtros['ano_referencia'] = $ano;
        }
        
        if($tipo_view == 'export'){
            $this->listagem_analitico_export($filtros);
        }
        $this->paginate['AutotracFaturamento'] = array(
            'conditions' => $filtros,
            'limit'      => 50,
            'order'      => 'AutotracFaturamento.placa,Transportadora.razao_social' ,
            'extra'      => 'autotrac_faturamento_analitico'
        );

        $listar = $this->paginate('AutotracFaturamento');  
        $periodo = $filtros['mes_referencia'].'/'.$filtros['ano_referencia'];
        $this->set(compact('listar', 'periodo', 'cliente'));
    }

    function listagem_analitico_export($conditions) {        
        $order = array('AutotracFaturamento.placa, Transportadora.razao_social');
        $registros= $this->AutotracFaturamento->listagem_analitico($conditions, null, null, $order);
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('faturamento_autotrac.csv')));
        header('Pragma: no-cache');

        if(isset($conditions['Transportadora.codigo'])){
            $colunas = utf8_encode('Terminal; "Placa";"Transportadora";"Data";"Ass. Básica Qtd";"Ass. Básica Vl.";"Mensagem Qtd.";"Mensagem Vl.";"Caracter Qtd.";"Caracter Vl.";"Comando/Alerta Qtd.";"Comando/Alerta Vl.";"Caracter OBC Qtd.";"Caracter OBC Vl.";"Msg. Prioritária Qtd.";"Msg. Prioritária Vl.";"Macro Qtd.";"Macro Vl.";"Def. Grupo Qtd.";"Def. Grupo Vl.";"Alarm Pânico Qtd.";"Alarm Pânico Vl.";"Msg. Grupo Qtd.";"Msg. Grupo Vl.";"Prior. Grupo Qtd.";"Prior. Grupo Vl.";"Transf MCT Qtd.";"Transf MCT Vl.";"Desativa/Reat Qtd.";"Desativa/Reat Vl.";"QMass Qtd.";"QMass Vl.";"Macro AC Qtd.";"Macro AC Vl.";"QTWEB Qtd.";"QTWEB Vl.";"Perm. A.C. Qtd.";"Perm. A.C. Vl.";"Inc./Exc. A.C. Qtd.";"Inc./Exc. A.C. Vl.";"Posição Adicional Qtd.";"Posição Adicional Vl.";"Total";"Total c/ Tribulos"');
        }else{
            $colunas = utf8_encode('Terminal; "Placa";"Transportadora";"Data";"Ass. Básica Qtd";"Ass. Básica Vl.";"Mensagem Qtd.";"Mensagem Vl.";"Caracter Qtd.";"Caracter Vl.";"Comando/Alerta Qtd.";"Comando/Alerta Vl.";"Caracter OBC Qtd.";"Caracter OBC Vl.";"Msg. Prioritária Qtd.";"Msg. Prioritária Vl.";"Macro Qtd.";"Macro Vl.";"Def. Grupo Qtd.";"Def. Grupo Vl.";"Alarm Pânico Qtd.";"Alarm Pânico Vl.";"Msg. Grupo Qtd.";"Msg. Grupo Vl.";"Prior. Grupo Qtd.";"Prior. Grupo Vl.";"Transf MCT Qtd.";"Transf MCT Vl.";"Desativa/Reat Qtd.";"Desativa/Reat Vl.";"QMass Qtd.";"QMass Vl.";"Macro AC Qtd.";"Macro AC Vl.";"QTWEB Qtd.";"QTWEB Vl.";"Perm. A.C. Qtd.";"Perm. A.C. Vl.";"Inc./Exc. A.C. Qtd.";"Inc./Exc. A.C. Vl.";"Posição Adicional Qtd.";"Posição Adicional Vl.";"Posição Buonny Qtd.";"Posição Buonny Vl.";"Total";"Total Buonny";"Total c/ Tribulos"');
        }

        echo iconv('UTF-8', 'ISO-8859-1', $colunas);
        $total_geral = 0;
        $total = 0; 
        $totais = array_fill(0, 43, 0);
        foreach($registros as $linha){
            $registro = $linha['AutotracFaturamento'];
            $cliente = $linha['Transportadora'];
            $total = 0;
            $linha = "";
            $linha .= '"' . $registro['numero_terminal'] . '";';
            $linha .= '"' . $registro['placa'] . '";';
            $linha .= !empty($cliente['codigo']) ? '"' .$cliente['codigo'].' - '. $cliente['razao_social']. '";' : '"";';
            $linha .= '"' . $registro['data_ultima_viagem'] . '";' ;
            $linha .= '"' . number_format($registro['ass_basica_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['ass_basica_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['mensagem_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['mensagem_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['caracter_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['caracter_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['comando_alerta_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['comando_alerta_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['caracter_obc_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['caracter_obc_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['mensagem_prioritaria_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['mensagem_prioritaria_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['macro_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['macro_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['def_grupo_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['def_grupo_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['alarme_panico_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['alarme_panico_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['mensagem_grupo_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['mensagem_grupo_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['prior_grupo_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['prior_grupo_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['transf_mct_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['transf_mct_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['desativ_reat_quantidade'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['desativ_reat_valor'], 2, ',','.') . '";';
            $linha .= '"' . number_format($registro['qmass_quantidade'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['qmass_valor'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['macro_ac_quantidade'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['macro_ac_valor'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['qtweb_quantidade'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['qtweb_valor'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['perm_ac_quantidade'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['perm_ac_valor'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['inc_exc_ac_quantidade'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['inc_exc_ac_valor'], 2, ',', '.') . '";';
            if(!isset($conditions['Transportadora.codigo'])){
                $linha .= '"' . number_format($registro['posicao_adicional_quantidade'], 2, ',','.') . '";';
                $linha .= '"' . number_format($registro['posicao_adicional_valor'], 2, ',','.') . '";';
            }
            $linha .= '"' . number_format($registro['inc_exc_buonny_quantidade'], 2, ',', '.') . '";';
            $linha .= '"' . number_format($registro['inc_exc_buonny_valor'], 2, ',', '.') . '";';
            if(!isset($conditions['Transportadora.codigo'])){
                $linha .= '"' . number_format($registro['total'],2,',','.') .'";';            
            }
            $linha .= '"' . number_format($registro['total_buonny'],2,',','.') .'";';            
            $linha .= '"' . number_format( $registro['total_tributo'],2,',','.') .'";';
            $linha .= '"";' ;

            $totais[0]  += $registro['ass_basica_quantidade'];
            $totais[1]  += $registro['ass_basica_valor'];
            $totais[2]  += $registro['mensagem_quantidade'];
            $totais[3]  += $registro['mensagem_valor'];
            $totais[4]  += $registro['caracter_quantidade'];
            $totais[5]  += $registro['caracter_valor'];
            $totais[6]  += $registro['comando_alerta_quantidade'];
            $totais[7]  += $registro['comando_alerta_valor'];
            $totais[8]  += $registro['caracter_obc_quantidade'];
            $totais[9]  += $registro['caracter_obc_valor'];
            $totais[10] += $registro['mensagem_prioritaria_quantidade'];
            $totais[11] += $registro['mensagem_prioritaria_valor'];
            $totais[12] += $registro['macro_quantidade'];
            $totais[13] += $registro['macro_valor'];
            $totais[14] += $registro['def_grupo_quantidade'];
            $totais[15] += $registro['def_grupo_valor'];
            $totais[16] += $registro['alarme_panico_quantidade'];
            $totais[17] += $registro['alarme_panico_valor'];
            $totais[18] += $registro['mensagem_grupo_quantidade'];
            $totais[19] += $registro['mensagem_grupo_valor'];
            $totais[20] += $registro['prior_grupo_quantidade'];
            $totais[21] += $registro['prior_grupo_valor'];
            $totais[22] += $registro['transf_mct_quantidade'];
            $totais[23] += $registro['transf_mct_valor'];
            $totais[24] += $registro['desativ_reat_quantidade'];
            $totais[25] += $registro['desativ_reat_valor'];
            $totais[26] += $registro['qmass_quantidade'];
            $totais[27] += $registro['qmass_valor'];
            $totais[28] += $registro['macro_ac_quantidade'];
            $totais[29] += $registro['macro_ac_valor'];
            $totais[30] += $registro['qtweb_quantidade'];
            $totais[31] += $registro['qtweb_valor'];
            $totais[32] += $registro['perm_ac_quantidade'];
            $totais[33] += $registro['perm_ac_valor'];
            $totais[34] += $registro['inc_exc_ac_quantidade'];
            $totais[35] += $registro['inc_exc_ac_valor'];
            if(!isset($conditions['Transportadora.codigo'])){
                $totais[36] += $registro['posicao_adicional_quantidade'];
                $totais[37] += $registro['posicao_adicional_valor'];
                $totais[38] += $registro['inc_exc_buonny_quantidade'];
                $totais[39] += $registro['inc_exc_buonny_valor'];
                $totais[40] += $registro['total'];
                $totais[41] += $registro['total_buonny'];
                $totais[42] += $registro['total_tributo'];
            }else{
                $totais[36] += $registro['inc_exc_buonny_quantidade'];
                $totais[37] += $registro['inc_exc_buonny_valor'];                
                $totais[38] += $registro['total_buonny'];
                $totais[39] += $registro['total_tributo'];
                unset($totais[40]);
                unset($totais[41]);
                unset($totais[42]);
            }


            //$linha .= '"'. AppModel::dbDateToDate($registro['data']) . '";';
            echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
            
        }
        $linha  = '"";"";"";"";';
        foreach($totais as $tot){            
            $linha .= '"'.number_format($tot,2,',','.').'";';
        }
        echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
        exit;
    }

    function gerar_pedido(){
        $filtros = $this->Filtros->controla_sessao($this->data, 'AutotracFaturamento');
        $fields = array(
                'AutotracFaturamento.codigo_transportadora as codigo_documento',
                'Transportadora.codigo as codigo_transportadora',
                'SUM(AutotracFaturamento.ass_basica_quantidade) as ass_basica_quantidade',
                'SUM(AutotracFaturamento.ass_basica_valor) as ass_basica_valor',
                'SUM(AutotracFaturamento.mensagem_quantidade) as mensagem_quantidade',
                'SUM(AutotracFaturamento.mensagem_valor) as mensagem_valor',
                'SUM(AutotracFaturamento.caracter_quantidade) as caracter_quantidade',
                'SUM(AutotracFaturamento.caracter_valor) as caracter_valor',
                'SUM(AutotracFaturamento.comando_alerta_quantidade) as comando_alerta_quantidade',
                'SUM(AutotracFaturamento.comando_alerta_valor) as comando_alerta_valor',
                'SUM(AutotracFaturamento.caracter_obc_quantidade) as caracter_obc_quantidade',
                'SUM(AutotracFaturamento.caracter_obc_valor) as caracter_obc_valor',
                'SUM(AutotracFaturamento.mensagem_prioritaria_quantidade) as msg_prioritaria_quantidade',
                'SUM(AutotracFaturamento.mensagem_prioritaria_valor) as msg_prioritaria_valor',
                'SUM(AutotracFaturamento.macro_quantidade) as macro_quantidade',
                'SUM(AutotracFaturamento.macro_valor) as macro_valor',
                'SUM(AutotracFaturamento.def_grupo_quantidade) as def_grupo_quantidade',
                'SUM(AutotracFaturamento.def_grupo_valor) as def_grupo_valor',
                'SUM(AutotracFaturamento.alarme_panico_quantidade) as alarme_panico_quantidade',
                'SUM(AutotracFaturamento.alarme_panico_valor) as alarme_panico_valor',
                'SUM(AutotracFaturamento.mensagem_grupo_quantidade) as mensagem_grupo_quantidade',
                'SUM(AutotracFaturamento.mensagem_grupo_valor) as mensagem_grupo_valor',
                'SUM(AutotracFaturamento.prior_grupo_quantidade) as prior_grupo_quantidade',
                'SUM(AutotracFaturamento.prior_grupo_valor) as prior_grupo_valor',
                'SUM(AutotracFaturamento.transf_mct_quantidade) as transf_mct_quantidade',
                'SUM(AutotracFaturamento.transf_mct_valor) as transf_mct_valor',
                'SUM(AutotracFaturamento.desativ_reat_quantidade) as desativ_reat_quantidade',
                'SUM(AutotracFaturamento.desativ_reat_valor) as desativ_reat_valor',
                'SUM(AutotracFaturamento.qmass_quantidade) as qmass_quantidade',
                'SUM(AutotracFaturamento.qmass_valor) as qmass_valor',
                'SUM(AutotracFaturamento.macro_ac_quantidade) as macro_ac_quantidade',
                'SUM(AutotracFaturamento.macro_ac_valor) as macro_ac_valor',
                'SUM(AutotracFaturamento.qtweb_quantidade) as qtweb_quantidade',
                'SUM(AutotracFaturamento.qtweb_valor) as qtweb_valor',
                'SUM(AutotracFaturamento.perm_ac_quantidade) as perm_ac_quantidade',
                'SUM(AutotracFaturamento.perm_ac_valor) as perm_ac_valor',
                'SUM(AutotracFaturamento.inc_exc_ac_quantidade) as inc_exc_ac_quantidade',
                'SUM(AutotracFaturamento.inc_exc_ac_valor) as inc_exc_ac_valor',
                'SUM(AutotracFaturamento.posicao_adicional_quantidade) as posicao_adicional_quantidade',
                'SUM(AutotracFaturamento.posicao_adicional_valor) as posicao_adicional_valor',
                'SUM(AutotracFaturamento.inc_exc_buonny_quantidade) as inc_exc_buonny_quantidade',
                'SUM(AutotracFaturamento.inc_exc_buonny_valor) as inc_exc_buonny_valor',
                'SUM(AutotracFaturamento.total) as total',
                'SUM(AutotracFaturamento.total_buonny) as total_buonny',
                'SUM(AutotracFaturamento.total_tributo) as total_tributo',
            );
        $group = array('AutotracFaturamento.codigo_transportadora', ' Transportadora.codigo');
        unset($filtros['arquivo']);
        $faturamentos = $this->AutotracFaturamento->find(
            'all', array(
                'fields'     => $fields,
                'conditions' => $filtros,
                'group'      => $group)
            );
        foreach($faturamentos as $faturamento){            
            $faturamento = $faturamento[0];
            if(!empty($faturamento['codigo_transportadora'])){                
                $dado_pedido = array('Pedido' => array(
                        'codigo_cliente_pagador' => $faturamento['codigo_transportadora'],
                        'mes_referencia'         => $filtros['mes_referencia'],
                        'ano_referencia'         => $filtros['ano_referencia'],
                        'manual'                 => 2
                    ));
                $pedido = $this->Pedido->incluir($dado_pedido);                
                if($pedido){                    
                    $dado_item_pedido = array('ItemPedido' => array(
                            'codigo_pedido'  => $this->Pedido->id,
                            'codigo_produto' => Produto::AUTOTRAC,
                            'quantidade'     => 1,
                            'valor_total'    => $faturamento['total_tributo']
                            
                        ));
                    $item_pedido = $this->ItemPedido->incluir($dado_item_pedido);                    
                    
                    if($item_pedido){
                        $conditions = $filtros;
                        $conditions['codigo_transportadora'] = $faturamento['codigo_transportadora'];
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::ASSINATURA_BASICA   , $faturamento['ass_basica_quantidade']     , $faturamento['ass_basica_valor']);        
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::MENSAGEM            , $faturamento['mensagem_quantidade']       , $faturamento['mensagem_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::CARACTER            , $faturamento['caracter_quantidade']       , $faturamento['caracter_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::COMANDO_ALERTA      , $faturamento['comando_alerta_quantidade'] , $faturamento['comando_alerta_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::CARACTER_OBC        , $faturamento['caracter_obc_quantidade']   , $faturamento['caracter_obc_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::MENSAGEM_PRIORITARIA, $faturamento['msg_prioritaria_quantidade'], $faturamento['msg_prioritaria_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::MACRO               , $faturamento['macro_quantidade']          , $faturamento['macro_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::DEF_GRUPO           , $faturamento['def_grupo_quantidade']      , $faturamento['def_grupo_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::ALARME_PANICO       , $faturamento['alarme_panico_quantidade']  , $faturamento['alarme_panico_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::MENSAGEM_GRUPO      , $faturamento['mensagem_grupo_quantidade'] , $faturamento['mensagem_grupo_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::PRIOR_GRUPO         , $faturamento['prior_grupo_quantidade']    , $faturamento['prior_grupo_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::TRANSF_MCT          , $faturamento['transf_mct_quantidade']     , $faturamento['transf_mct_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::DESATIV_REAT        , $faturamento['desativ_reat_quantidade']   , $faturamento['desativ_reat_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::QMASS               , $faturamento['qmass_quantidade']          , $faturamento['qmass_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::MACRO_AC            , $faturamento['macro_ac_quantidade']       , $faturamento['macro_ac_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::PERM_AC             , $faturamento['perm_ac_quantidade']        , $faturamento['perm_ac_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::INCLUSAO_EXCLUSAO_AC, $faturamento['inc_exc_ac_quantidade']     , $faturamento['inc_exc_ac_valor']);
                        $this->incluir_detalhe_pedido($this->ItemPedido->id, Servico::POSICAO_ADICIONAL   , $faturamento['inc_exc_buonny_quantidade'] , $faturamento['inc_exc_buonny_valor']);                                                    
                    }
                }
            }
        } 
        echo '--' ;
        $this->redirect(array('controller'=>'autotrac_faturamentos', 'action' => 'index'));      
    }
    private function incluir_detalhe_pedido($codigo_item_pedido, $servico, $quantidade, $valor){
        $dado_detalhe_item_pedido = array('DetalheItemPedidoManual' => array(
                'codigo_item_pedido' => $codigo_item_pedido,
                'codigo_servico'     => $servico,
                'quantidade'         => $quantidade,
                'valor'              => $valor
            ));        
        $this->DetalheItemPedidoManual->incluir($dado_detalhe_item_pedido);
    }

    function excecao() {
        $this->pageTitle = 'Exceções Autotrac';
        $this->data['AutotracExcecao'] = $this->Filtros->controla_sessao($this->data,'AutotracExcecao');
    }
    
    function excecao_listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data,'AutotracExcecao');
        $conditions = $this->AutotracExcecao->converteFiltroEmCondition($filtros);
        $this->paginate['AutotracExcecao'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Cliente.razao_social',
        );

        $clientes_excecoes = $this->paginate('AutotracExcecao');
        $this->set(compact('clientes_excecoes'));
    }
    
    function incluir_excecao() {
        $this->pageTitle = 'Incluir Exceções Autotrac';
        if($this->RequestHandler->isPost()) {
            if ($this->AutotracExcecao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'excecao'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function excluir_excecao($codigo_cliente) {
        if ($this->AutotracExcecao->excluir($codigo_cliente)) {
            $this->BSession->setFlash('delete_success');
            $this->redirect(array('action' => 'excecao'));
        } else {
            $this->BSession->setFlash('delete_error');
            $this->redirect(array('action' => 'excecao'));
        }
    }
}
