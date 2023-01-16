<?php
 
class RelatorioFichasScorecardController extends AppController {
    public $name = 'RelatorioFichasScorecard';
    public $components = array('Filtros','RequestHandler');
    public $uses = array('Usuario' ,'FichaScorecard','Cliente','Recebsm', 'ClienteProdutoServico2');
   
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array( 
           '*'
       )); 
    }


  private function __jasperConsulta( $condicao, $nome_relatorio, $filtros, $tipo_impressao, $codigo_cliente ) {
    require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
    $RelatorioWebService = new RelatorioWebService();
    $parametros = array( 'CONDICOES' => $condicao, 'FILTROS' => $filtros, 'CODIGOCLIENTELOGADO' => $codigo_cliente );
    header(sprintf('Content-Disposition: attachment; filename="%s"', basename( $nome_relatorio.'.pdf' )));
    $url = $RelatorioWebService->executarRelatorio( '/reports/Teleconsult2/'.$nome_relatorio, $parametros,'pdf');
    header('Pragma: no-cache');
    header('Content-type: application/pdf');
    echo $url;die;
  }

  public function gera_demonstrativo( ) {
    $data_inicial   = isset($this->data['Cliente']['data_inicial'])   ? preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $this->data['Cliente']['data_inicial']) : NULL;
    $data_final     = isset($this->data['Cliente']['data_final'])     ? preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $this->data['Cliente']['data_final'])   : NULL;
    $usuario        = $this->BAuth->user();
    if( isset($usuario['Usuario']['codigo_cliente'])){
      $this->data['Cliente']['codigo_cliente'] = $usuario['Usuario']['codigo_cliente'];
    }
    $codigo_cliente = isset($this->data['Cliente']['codigo_cliente']) ? $this->data['Cliente']['codigo_cliente'] : NULL;
    if ( $data_inicial && $data_final && $codigo_cliente ) {
      $condicoes[]  = " faturamento.codigo_produto = 134  ";
      $condicoes[]  = " faturamento.codigo_usuario_inclusao <> 1 ";
      $condicoes[]  = " tipo_operacao.cobrado = 1 ";
      $condicoes[]  = sprintf(' (faturamento.codigo_cliente = %s OR faturamento.codigo_cliente_pagador = %s)',$codigo_cliente, $codigo_cliente);
      $data_inicio  = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00',  $data_inicial );
      $data_termino = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59', $data_final);
      $condicoes[]  = sprintf(" faturamento.data_inclusao between '%s' and '%s' ",$data_inicial, $data_final);        
      $filtros      = 'Período de: ' . preg_replace('/(\d{4})-(\d{2})-(\d{2}) .*/','$3/$2/$1', $data_inicio) . ' até ' . preg_replace('/(\d{4})-(\d{2})-(\d{2}) .*/','$3/$2/$1', $data_final);
      $condicao     = ' WHERE ' . implode($condicoes, ' AND');
      $tipo_impressao = 'PDF';
      $retorno = $this->__jasperConsulta( $condicao, 'demonstrativo_servico_scorecard', $filtros, $tipo_impressao, $codigo_cliente );
    }
  }

    function index_log_serasa() {
      $this->pageTitle = "Logs Consulta BCB";
      $this->data['Usuario']['data_inclusao_inicio']  = date('d/m/Y');
      $this->data['Usuario']['data_inclusao_fim']     = date('d/m/Y');
    }      


    function listagem_consulta_serasa(){
          $this->pageTitle = "Consulta BCB";
          $this->layout = 'ajax';
          
          $filtros = $this->Filtros->controla_sessao($this->data, 'Usuario');
          $this->set(compact('filtros','count'));
    }
     
    function listagem(){          
      $this->pageTitle = "Logs Consulta BCB";
      $this->layout = 'ajax';
      $filtros = $this->Filtros->controla_sessao($this->data, 'Usuario');      
      if ( !empty($filtros['codigo_documento'] )) {
        $conditions['ConsultaBCB.codigo_documento'] = preg_replace('/\D/', '', $filtros['codigo_documento']);
      }
      if ( isset($filtros['novaconsulta']) && $filtros['novaconsulta'] == 1 ) {
        $conditions['LogConsultaBCB.nova_consulta'] = 1;
      }
      if ( isset($filtros['novaconsulta']) && $filtros['novaconsulta'] == 2 ) {
        $conditions['LogConsultaBCB.nova_consulta <>'] = 1; 
      }
      if( !empty($filtros['usuario']) )
        $conditions['Usuario.apelido LIKE'] = '%'.$filtros['usuario'].'%'; 

      $conditions['LogConsultaBCB.data BETWEEN ? AND ?']=array(
        preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $filtros['data_inclusao_inicio']) ,
        preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $filtros['data_inclusao_fim'])
      );
      $fields = array('LogConsultaBCB.codigo',
        'RHHealth.publico.ufn_formata_cpf(ConsultaBCB.codigo_documento) as codigo_documento',
        'case when LogConsultaBCB.nova_consulta = 1 then "Sim" else "Não" end as nova_consulta',
        'LogConsultaBCB.data','Usuario.apelido');
      $joins = array(
        array(
          "table"     => 'dbbcb.bcb.consulta',
          "alias"     => "ConsultaBCB",
          "type"      => "INNER",
          "conditions"=> array("Usuario.codigo = ConsultaBCB.codigo_usuario")
        ),
        array(
          "table"     => 'dbbcb.bcb.log_consulta',
          "alias"     => "LogConsultaBCB",
          "type"      => "INNER",
          "conditions"=> array("ConsultaBCB.codigo = LogConsultaBCB.codigo_consulta")
        ),
      );
      $this->paginate['Usuario'] = array(
        'fields' => $fields,
        'conditions' => $conditions,
        'joins' => $joins,
        'limit' => 50,
        'order' => 'LogConsultaBCB.data DESC'
      );
      $consultabcb = $this->paginate('Usuario');
      $count = $this->Usuario->find('count');
      $this->set(compact('consultabcb','count'));
    }  


    function index_ct(){
          $_SESSION['filtros_ct'] =''; 
          $this->pageTitle = "Demonstrativos CT SCORECARD";
          $filtros = $this->Filtros->controla_sessao($this->data,'DemonstrativosCT');
          $_SESSION['filtros_ct'] = $filtros;
          $filtros['geraPdf'] = '';
          if ($filtros['geraPdf']=='S'){
             $filtros['geraPdf'] = '';
             $this->data['DemonstrativosCT']['geraPdf'] = '';
             $this->gera_ct($filtros);
          }  
          $this->data['DemonstrativosCT'] = $filtros;
          $this->data['DemonstrativosCT']['geraPdf'] = '';
          //Libera Impressão de CT

          if(@$this->params['form']['liberar_ct']=='Gerar CT'){
            
            $this->FichaScorecard = ClassRegistry::init('FichaScorecard');
            $data_baixa_inicio  = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $filtros['data_baixa_inicio']);
            $data_baixa_fim = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $filtros['data_baixa_fim']);
            $data_inclusao_inicio = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $filtros['data_inclusao_inicio']);
            $data_inclusao_fim = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $filtros['data_inclusao_fim']);
            $cpf = str_replace('-','',str_replace('.','',$filtros['codigo_documento']));
            if (!empty($filtros['codigo_cliente'])){    
                  $condition['Cliente.codigo'] = $filtros['codigo_cliente'];
             }
            if (!empty($filtros['codigo_documento'])) {
                  $condition['Profissional.codigo_documento'] = $cpf;
             }
            if (!empty($filtros['data_baixa_inicio']) OR 
                 !empty($filtros['data_baixa_fim'] )) {
                 $condition["FichaScorecard.data_validade between '".$data_baixa_inicio."' and '".$data_baixa_fim."' and '1'="]=1;
             }
            if (!empty($filtros['data_inclusao_inicio']) OR 
                 !empty($filtros['data_inclusao_fim'] )) {
                 $condition["FichaScorecard.data_inclusao between '".$data_inclusao_inicio."' and '".$data_inclusao_fim."' and '1'="] =1;
             }
                  
            if (!empty($this->data['DemonstrativosCT']['codigo_documento_ct_inicial']) OR 
                 !empty($this->data['DemonstrativosCT']['codigo_documento_ct_fim'] )) {
                   $condition["Fat.codigo between '".$filtros['codigo_documento_ct_inicial']."' and '".$filtros['codigo_documento_ct_fim']."' and '1'="]=1;
             }
                  
            if (!empty($this->data['DemonstrativosCT']['ano'])) {
                 $condition["YEAR(FichaScorecard.data_inclusao)"]=$filtros['ano'];
             }
             

            $dados=$this->FichaScorecard->relatorio_listagem_ct();
            if(!isset($condition)){
               $condition= array();
            }
            $condition['Fat.codigo_tipo_operacao'] = 11; // Só Cadastro;  
            $condition['FichaScorecard.codigo_status'] = 7; //Finalizada;
            $condition['FichaScorecard.codigo_profissional_tipo <>'] = 1; //Diferente de Carreteiro 
            $condition['FichaScorecard.bloquear_resultado_cliente'] = 0; //Permissão para gerar a CT
            $listagem_ct  = $this->FichaScorecard->find('all',array('fields' => $dados['fields'],
                                                                    'joins'  => $dados['joins'],    
                                                                    'conditions' => $condition));
            
            foreach ($listagem_ct as $ct_dados) {
               $dados['codigo'] = $ct_dados[0]['codigo'];
               $dados['bloquear_resultado_cliente'] =1;
               $this->FichaScorecard->save($dados);    

            }
            
            
          } 

           //Excluir Impressão de CT

          if(@$this->params['form']['desabilitar_ct']=='Excluir CT'){
            
            $this->FichaScorecard = ClassRegistry::init('FichaScorecard');
            $data_baixa_inicio  = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $filtros['data_baixa_inicio']);
            $data_baixa_fim = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $filtros['data_baixa_fim']);
            $data_inclusao_inicio = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $filtros['data_inclusao_inicio']);
            $data_inclusao_fim = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $filtros['data_inclusao_fim']);
            $cpf = str_replace('-','',str_replace('.','',$filtros['codigo_documento']));
            if (!empty($filtros['codigo_cliente'])){    
                  $condition['Cliente.codigo'] = $filtros['codigo_cliente'];
             }
            if (!empty($filtros['codigo_documento'])) {
                  $condition['Profissional.codigo_documento'] = $cpf;
             }
            if (!empty($filtros['data_baixa_inicio']) OR 
                 !empty($filtros['data_baixa_fim'] )) {
                 $condition["FichaScorecard.data_validade between '".$data_baixa_inicio."' and '".$data_baixa_fim."' and '1'="]=1;
             }
            if (!empty($filtros['data_inclusao_inicio']) OR 
                 !empty($filtros['data_inclusao_fim'] )) {
                 $condition["FichaScorecard.data_inclusao between '".$data_inclusao_inicio."' and '".$data_inclusao_fim."' and '1'="] =1;
             }
                  
            if (!empty($this->data['DemonstrativosCT']['codigo_documento_ct_inicial']) OR 
                 !empty($this->data['DemonstrativosCT']['codigo_documento_ct_fim'] )) {
                   $condition["Fat.codigo between '".$filtros['codigo_documento_ct_inicial']."' and '".$filtros['codigo_documento_ct_fim']."' and '1'="]=1;
             }
                  
            if (!empty($this->data['DemonstrativosCT']['ano'])) {
                 $condition["YEAR(FichaScorecard.data_inclusao)"]=$filtros['ano'];
             }
             

            $dados=$this->FichaScorecard->relatorio_listagem_ct();
            if(!isset($condition)){
               $condition= array();
            }
            $condition['Fat.codigo_tipo_operacao'] = 11; // Só Cadastro;  
            $condition['FichaScorecard.codigo_status'] = 7; //Finalizada;
            $condition['FichaScorecard.codigo_profissional_tipo <>'] = 1; //Diferente de Carreteiro 
            $condition['FichaScorecard.bloquear_resultado_cliente'] = 1; //Permissão para excluir a CT
            $listagem_ct  = $this->FichaScorecard->find('all',array('fields' => $dados['fields'],
                                                                    'joins'  => $dados['joins'],    
                                                                    'conditions' => $condition));
            foreach ($listagem_ct as $ct_dados) {
               $dados['codigo'] = $ct_dados[0]['codigo'];
               $dados['bloquear_resultado_cliente'] =0;  
               $this->FichaScorecard->save($dados);    

            }
            
            
          } 


    }

    function index_consulta_serasa() {
       $this->pageTitle = "BCB";
       //$this->redirect('http://informacoes.buonny.com.br/bcb/autenticacao/login');
       
    }

    function index() {
        $this->pageTitle = "Demonstrativos de Serviço SCORECARD";
        $this->data['Cliente']['data_inicial'] = date('d/m/Y');
        $this->data['Cliente']['data_final']   = date('d/m/Y');
    }
    
    function listagem_ct(){
       $this->title = "Demonstrativos CT SCORECARD";
       $filtros = $this->Filtros->controla_sessao($this->data, 'DemonstrativosCT');
       $this->FichaScorecard = ClassRegistry::init('FichaScorecard');
       $data_baixa_inicio  = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $filtros['data_baixa_inicio']);
       $data_baixa_fim = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $filtros['data_baixa_fim']);
       $data_inclusao_inicio = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $filtros['data_inclusao_inicio']);
       $data_inclusao_fim = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $filtros['data_inclusao_fim']);
       $cpf = str_replace('-','',str_replace('.','',$filtros['codigo_documento']));
       if (!empty($filtros['codigo_cliente'])){    
            $condition['Cliente.codigo'] = $filtros['codigo_cliente'];
       }
       if (!empty($filtros['codigo_documento'])) {
            $condition['Profissional.codigo_documento'] = $cpf;
       }
       if (!empty($filtros['data_baixa_inicio']) OR 
           !empty($filtros['data_baixa_fim'] )) {
           $condition["FichaScorecard.data_validade between '".$data_baixa_inicio."' and '".$data_baixa_fim."' and '1'="]=1;
       }
       if (!empty($filtros['data_inclusao_inicio']) OR 
           !empty($filtros['data_inclusao_fim'] )) {
           $condition["FichaScorecard.data_inclusao between '".$data_inclusao_inicio."' and '".$data_inclusao_fim."' and '1'="] =1;
       }
            
       if (!empty($this->data['DemonstrativosCT']['codigo_documento_ct_inicial']) OR 
           !empty($this->data['DemonstrativosCT']['codigo_documento_ct_fim'] )) {
             $condition["Fat.codigo between '".$filtros['codigo_documento_ct_inicial']."' and '".$filtros['codigo_documento_ct_fim']."' and '1'="]=1;
       }
            
       if (!empty($this->data['DemonstrativosCT']['ano'])) {
           $condition["YEAR(FichaScorecard.data_inclusao)"]=$filtros['ano'];
       }
       

      $dados=$this->FichaScorecard->relatorio_listagem_ct();
      if(!isset($condition)){
         $condition= array();
      }
      $condition['Fat.codigo_tipo_operacao'] = 11; // Só Cadastro;  
      $condition['FichaScorecard.codigo_status'] = 7; //Finalizada;
      $condition['FichaScorecard.codigo_profissional_tipo <>'] = 1; //Diferente de Carreteiro 
      $condition['FichaScorecard.bloquear_resultado_cliente'] = 1; //Permissão para gerar a CT
      $this->paginate['FichaScorecard'] = array(
              'fields' => $dados['fields'],
              'joins'  => $dados['joins'],    
              'conditions' => $condition,
              'limit' => 50,
              'order' => 'FichaScorecard.codigo desc'
               );
      $listagem_ct = $this->paginate('FichaScorecard');
      $this->set(compact('listagem_ct')); 
        
    }

    public function gera_ct_ficha($codigo_ficha){
           $this->title = "Demonstrativos CT SCORECARD";
           $this->layout = 'ajax';
           $this->FichaScorecard = ClassRegistry::init('FichaScorecard');
           $query=$this->FichaScorecard->relatorio_demonstrativo_ct(); 
           $condition = "AND FichaScorecard.codigo='".$codigo_ficha."'";//11 pq é cadastro de motorista 
           $condition .=" AND Fat.codigo_tipo_operacao = 11 "; // só cadastro 
           $condition .=" AND FichaScorecard.codigo_status = 7 "; //Finalizada;
           $condition .=" AND FichaScorecard.codigo_profissional_tipo <> 1"; //Diferente de Carreteiro 
           $query = $query." ".$condition; 
           header(sprintf('Content-Disposition: attachment; filename="%s"', basename('demonstrativo_ct_scorecard.pdf')));
           header('Pragma: no-cache');   
           require_once APP . 'vendors' . DS . 'buonny' . DS . 'Jasper.php';
           $clienteJasper = new Jasper();
           $clienteJasper->credenciais();
           $caminhoDoArquivo = 'nomeDoRelatorio.pdf';
           $pastaDoRelatorio = '/reports/Teleconsult2/';
           $nomeDoRelatorio  = 'FichaCt';
           $parametros       = array("QUERY" => $query);
           $resultado = $clienteJasper->printReport($pastaDoRelatorio, $nomeDoRelatorio, 'PDF', $parametros);
           //file_put_contents($caminhoDoArquivo, $resultado); salva arquivo
           echo $resultado;
           exit; 
           
    }
     
    public function gera_ct() {
        $this->data['RelatorioFichasScorecard'] = $_SESSION['filtros_ct'];
        $this->data['DemonstrativosCT'] = $_SESSION['filtros_ct'];
        unset($_SESSION['filtros_ct']);
        $this->title = "Demonstrativos CT SCORECARD";
        $this->layout = 'ajax';
        $condition = " ";
          
           $this->FichaScorecard = ClassRegistry::init('FichaScorecard');
           $query=$this->FichaScorecard->relatorio_demonstrativo_ct(); 
           @$data_baixa_inicio    = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $this->data['RelatorioFichasScorecard']['data_baixa_inicio']);
           @$data_baixa_fim       = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $this->data['RelatorioFichasScorecard']['data_baixa_fim']);
           @$data_inclusao_inicio = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $this->data['RelatorioFichasScorecard']['data_inclusao_inicio']);
           @$data_inclusao_fim    = preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $this->data['RelatorioFichasScorecard']['data_inclusao_fim']);
           @$cpf = str_replace('-','',str_replace('.','',$this->data['RelatorioFichasScorecard']['codigo_documento']));
           if (!empty($this->data['DemonstrativosCT']['codigo_cliente'])){
               $condition = '  AND Cliente.codigo ='.$this->data['DemonstrativosCT']['codigo_cliente'];
           
           } 
            if (!empty($this->data['DemonstrativosCT']['codigo_documento'])) {
               $condition .= ' AND Profissional.codigo_documento ='.$cpf;
            }
            if (!empty($this->data['DemonstrativosCT']['data_baixa_inicio']) OR 
                !empty($this->data['DemonstrativosCT']['data_baixa_fim'] )) {
              $condition .= "  AND Ficha.data_validade between '".$data_baixa_inicio."' and '".$data_baixa_fim."'";
            }
            if (!empty($this->data['DemonstrativosCT']['data_inclusao_inicio']) OR 
                !empty($this->data['DemonstrativosCT']['data_inclusao_fim'] )) {
              $condition .= "  AND Ficha.data_inclusao between '".$data_inclusao_inicio."' and '".$data_inclusao_fim."'";
            }
            
            if (!empty($this->data['DemonstrativosCT']['codigo_documento_ct_inicial']) OR 
                !empty($this->data['DemonstrativosCT']['codigo_documento_ct_fim'] )) {
              $condition .= "  AND Fat.codigo between '".$this->data['DemonstrativosCT']['codigo_documento_ct_inicial']."' and '".$this->data['RelatorioFichasScorecard']['codigo_documento_ct_fim']."'";
            }
            
            if (!empty($this->data['DemonstrativosCT']['ano'])) {
              $condition .= "  AND YEAR(Ficha.data_inclusao) = '".$this->data['DemonstrativosCT']['ano']."'";
            }

      
        $condition .=" AND Fat.codigo_tipo_operacao = 11 "; // só cadastro 
        $condition .=" AND FichaScorecard.codigo_status = 7 "; //Finalizada;
        $condition .=" AND FichaScorecard.codigo_profissional_tipo <> 1"; //Diferente de Carreteiro 
           
        $query = $query." ".$condition; 
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('demonstrativo_ct_scorecard.pdf')));
        header('Pragma: no-cache');   
        require_once APP . 'vendors' . DS . 'buonny' . DS . 'Jasper.php';
        $clienteJasper = new Jasper();
        $clienteJasper->credenciais();
        $caminhoDoArquivo = 'nomeDoRelatorio.pdf';
        $pastaDoRelatorio = '/reports/Teleconsult2/';
        $nomeDoRelatorio  = 'FichaCt';
        $parametros       =  array("QUERY" => $query);
        $resultado = $clienteJasper->printReport($pastaDoRelatorio, $nomeDoRelatorio, 'PDF', $parametros);
        //file_put_contents($caminhoDoArquivo, $resultado); salva arquivo
        echo $resultado;
        exit;      
        
    }    
}
