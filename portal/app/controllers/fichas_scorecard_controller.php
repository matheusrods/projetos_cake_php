<?php
App::import('Component', array('StringView', 'Mailer.Scheduler'));
class FichasScorecardController extends AppController {
    public $name = 'FichasScorecard';
    public $layout = 'default';
    public $helpers = array('Paginator','Highcharts');
    public $components = array('Filtros', 'Fichas','Session'); 
    public $uses = array('ClienteProdutoServico','VeiculoBiTrem','VeiculoCarreta','ParametroScore','TipoRetorno', 'Usuario', 'FichaScorecard', 'FichaScorecardRetorno', 'ProfissionalTipo', 'EnderecoEstado', 
            'TipoCnh', 'TipoContato', 'VEndereco', 'Profissional', 'ProfissionalEndereco', 'ProfissionalContato', 'Proprietario',  'ProprietarioEndereco',
            'ProprietarioContato', 'Tecnologia','Endereco' ,  'VeiculoCor', 'VeiculoFabricante', 'VeiculoModelo', 'EnderecoCidade', 'CargaTipo',
            'CargaValor', 'FichaScorecardQuestao', 'Status', 'Veiculo', 'FichaScorecardQuestaoResp', 'Cliente', 'Seguradora',
            'FichaScProfContatoLog', 'FichaScorecardVeiculo', 'FichaScorecardStatus','Produto','ProfissionalLog','Alerta',
            'EmbarcadorTransportador', 'FichaScVeicPropContatoLog','LogAtendimento','FichaScorecardLog','TipoOperacao' ,'Uperfil'
    );
  
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'carregar_profissional_contatos', 'listagem_finalizadas', 'carregar_proprietario_contatos', 
            'copia_profissional_contatos', 'excluir_vinculo_profissional', 'relatorios_gerenciais',
            'estatisticas_relatorio_gerencial', 'relatorio_vinculo_excluido', 'listagem_relatorio_vinculo_excluido',
            'exclusao_log_faturamento', 'recuperar_numero_liberacao'
        ));
    }

    function index_fichas_finalizadas(){
        $this->Filtros->limpa_sessao('FichaScorecard');
        $filtros            = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $this->pageTitle    = 'Pesquisas Finalizadas - ScoreCard';        
        if( FichaScorecard::ENVIA_EMAIL_SCORECARD === FALSE ){
            $classificacao_tlc = array( 2 => 'PERFIL ADEQUADO AO RISCO', 7 => 'PERFIL INSUFICIENTE', 8 => 'PERFIL DIVERGENTE');
        } else {
            $classificacao_tlc  = $this->ParametroScore->find('list');    
        }
        $lista_seguradora   = $this->Seguradora->find('list');        
        $this->data['FichaScorecard']['data_inicial']   = date("d/m/Y");
        $this->data['FichaScorecard']['data_final']     = date("d/m/Y");
        $this->set(compact('classificacao_tlc','lista_seguradora'));
    }
    
    function listagem_finalizadas(){
        $this->layout = 'ajax';
        $this->FichaScorecardRetorno = ClassRegistry::init('FichaScorecardRetorno');
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if( !empty($filtros['data_inicial']) && !empty($filtros['data_final']) ){            
            $filtros['FichaScorecard.codigo_status'] = FichaScorecardStatus::FINALIZADA;
            $this->paginate['FichaScorecard'] = $this->FichaScorecard->busca_ficha_finalizadas($filtros); 
            $dados = $this->paginate('FichaScorecard');
            foreach ($dados as $contatos){
                $emails = $this->FichaScorecardRetorno->listagemContatosEmails($contatos['FichaScorecard']['codigo']);              
                foreach ($emails as $mail){
                    if(!isset($stringEmails[$contatos['FichaScorecard']['codigo']])){
                        $stringEmails[$contatos['FichaScorecard']['codigo']] = $mail['FichaScorecardRetorno']['descricao'];
                    }else{
                        $stringEmails[$contatos['FichaScorecard']['codigo']] = $stringEmails[$contatos['FichaScorecard']['codigo']].'; '.$mail['FichaScorecardRetorno']['descricao'];
                    }
                }              
            }
            $count = $this->FichaScorecard->find('count');
            $this->set(compact('dados','count','stringEmails'));
        }
    }

    public function findClienteProdutoServico($dataFicha) {
        ClassRegistry::init('Servico');
        $clienteProdutoServico = ClassRegistry::init('ClienteProdutoServico');
        $clienteProdutoServico->bindLazy();

        return $clienteProdutoServico->find('first', array(
            'fields' => array(
                'ClienteProdutoServico.codigo_cliente_pagador',
                'ClienteProdutoServico.validade',
                'ClienteProdutoServico.tempo_pesquisa'
            ),
            'conditions' => array(
                'ClienteProduto.codigo_cliente' => $dataFicha['codigo_cliente'],
                'ClienteProdutoServico.codigo_profissional_tipo' => $dataFicha['codigo_profissional_tipo'],
                'ClienteProdutoServico.codigo_servico' => Servico::CADASTRO_DE_FICHA,
                'ClienteProduto.codigo_produto' => $dataFicha['codigo_produto']
            )
        ));
    }

    function incluir_log_faturamento($data, $profissionalExisteNoBanco = true ){
        
        $data['Ficha'] = $data['FichaScorecard'];
        unset($data['FichaScorecard']);
        unset($data['Ficha']['codigo_usuario']);
        $authUsuario    = $this->authUsuario;
        $data['Ficha']['codigo_usuario_inclusao'] = $authUsuario['Usuario']['codigo'];
        $data['Ficha']['codigo_status'] = Status::EM_PESQUISA; 
        $data['Ficha']['ativo'] = 1; 
        if( isset($data['codigo_cliente']) ){
            $data['Ficha']['codigo_cliente']         = $data['codigo_cliente'];
            $data['Ficha']['codigo_cliente_pagador'] = $data['codigo_cliente'];
        } else {
            $data['Ficha']['codigo_cliente_pagador'] = $data['Ficha']['codigo_cliente'];       
        }      
        $data['Ficha']['codigo_produto'] = Produto::SCORECARD; 
        // $data['Ficha']['codigo_profissional_tipo'] = 1;
        $clienteProdutoServico   = ClassRegistry::init('Ficha')->findClienteProdutoServico($data['Ficha']);  
        $this->loadModel('FichaScorecard');
        $codigo_ficha_scorecard2 = $this->FichaScorecard->busca_ultima_ficha_cliente($data['Ficha']['codigo_cliente']);
        $faturamento['codigo_ficha_scorecard'] = $codigo_ficha_scorecard2[0][0]['ultima_ficha'];
        $codigo_ficha_scorecard  = $faturamento['codigo_ficha_scorecard'];//$codigo_ficha;
        $faturamento['codigo_profissional']  =$data['Profissional']['codigo']; 
        $this->data['Ficha']['codigo_profissional_log'] =$faturamento['codigo_profissional'];
        $this->loadModel('Veiculo');
        $veiculo_codigo = '';
        if(isset($data['FichaScorecardVeiculo']['0']['Veiculo']['placa'])){
            $veiculo_codigo  = $this->Veiculo->bucaVeiculoPorPlaca($data['FichaScorecardVeiculo']['0']['Veiculo']['placa']);
        }
        if (isset($data['FichaScorecardVeiculo']['1']['Veiculo']['placa'])){
            $carreta_codigo  = $this->Veiculo->bucaVeiculoPorPlaca($data['FichaScorecardVeiculo']['1']['Veiculo']['placa']);
        }
        if (isset($data['FichaScorecardVeiculo']['2']['Veiculo']['placa'])){
            $bitrem_codigo   = $this->Veiculo->bucaVeiculoPorPlaca($data['FichaScorecardVeiculo']['2']['Veiculo']['placa']);
        }

        $codigos_veiculo = array(isset($veiculo_codigo['Veiculo']) && isset($veiculo_codigo['Veiculo']['codigo']) ? $veiculo_codigo['Veiculo']['codigo'] : NULL);
        if (isset($carreta_codigo['Veiculo']['codigo'])){
            $codigos_veiculo[] = '';
            $codigos_veiculo[] = isset($carreta_codigo['Veiculo']['codigo']) ? $carreta_codigo['Veiculo']['codigo'] : NULL; 
        }
         if (isset($bitrem_codigo['Veiculo']['codigo'])){
            $codigos_veiculo[] = '';
            $codigos_veiculo[] = isset($bitrem_codigo['Veiculo']['codigo']) ? $bitrem_codigo['Veiculo']['codigo'] : NULL; 
        }

        $data['Ficha']['placa'] = isset($data['FichaScorecardVeiculo']['0']['Veiculo']['placa']) ? $data['FichaScorecardVeiculo']['0']['Veiculo']['placa'] : NULL;
        if(isset($data['Ficha']['placa_carreta'])){
            $data['Ficha']['placa_carreta'] = isset($data['FichaScorecardVeiculo']['1']['Veiculo']['placa']) ? $data['FichaScorecardVeiculo']['1']['Veiculo']['placa']: NULL;
        }
        $data['Profissional']['codigo_profissional_tipo'] = isset($data['Ficha']['codigo_profissional_tipo']) ? $data['Ficha']['codigo_profissional_tipo'] : NULL;
        if ($data['Profissional']['codigo_profissional_tipo']==''){
            $data['Profissional']['codigo_profissional_tipo'] = $data['Ficha']['FichaScorecard']['codigo_profissional_tipo'];
        }

        $codigo_profissional = ClassRegistry::init('Profissional')->field('codigo', array('codigo_documento'=>preg_replace('/\D/', '', $data['Profissional']['codigo_documento'])));
        return ClassRegistry::init('LogFaturamentoTeleconsult')->gerarFaturamentoFichaScorecard($data, $clienteProdutoServico, $codigo_ficha_scorecard, $codigo_profissional, $codigos_veiculo, $profissionalExisteNoBanco);
    }

    function incluir() {
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $this->data['FichaScorecard']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            $this->data['FichaScorecard']['codigo_usuario'] = $this->authUsuario['Usuario']['codigo'];
            $cliente = $this->Cliente->carregar($this->authUsuario['Usuario']['codigo_cliente']);  
            $this->data['Cliente']['codigo_documento']      = $cliente['Cliente']['codigo_documento'];
            $this->data['Cliente']['razao_social']          = $cliente['Cliente']['razao_social'];            
        }
        if($this->RequestHandler->isPost()) {
            $codigo_profissional = $this->Profissional->field('codigo', array('codigo_documento'=>preg_replace('/\D/', '', $this->data['Profissional']['codigo_documento'])));
            $this->data['FichaScorecard']['origem_ficha'] = 'W';
            $this->data['FichaScorecard']['codigo_produto'] = Produto::SCORECARD;
            if( isset($this->data['FichaScorecardRetorno'])){
                foreach ( $this->data['FichaScorecardRetorno'] as $key => $dados_retorno ){
                    $this->data['FichaScorecardRetorno'][$key]['nome'] = Inflector::humanize( mb_strtolower ( $dados_retorno['nome'] , "UTF-8" ));
                }
            }
            if($this->validarFicha()){
                $this->salvarFicha(true);                
                $profissionalExisteNoBanco = !empty($codigo_profissional);
                $this->incluir_log_faturamento($this->data, $profissionalExisteNoBanco );   
                $tipo_operacao = ( $profissionalExisteNoBanco ? TipoOperacao::TIPO_OPERACAO_ATUALIZACAO: TipoOperacao::TIPO_OPERACAO_CADASTRO );
                if($tipo_operacao==''){
                   $tipo_operacao = TipoOperacao::TIPO_OPERACAO_CADASTRO ;
                }
                if ($this->data['Profissional']['codigo']==''){
                    $cpf_replace = str_replace('-','',str_replace('.','',$this->data['Profissional']['codigo_documento']));
                    $this->data['Profissional']['codigo'] = $this->FichaScorecard->buscaCodigoProfissionalCodDoc($cpf_replace);
                }
                //Grava Log de Atendimento
                $dados_log_atendimento  = array(
                    'LogAtendimento'   => array(
                    'codigo_produto'        => Produto::SCORECARD,
                    'codigo_profissional'   => $this->data['Profissional']['codigo'],
                    'codigo_profissional_tipo' => $this->data['FichaScorecard']['codigo_profissional_tipo'],
                    'codigo_tipo_operacao' => $tipo_operacao,
                    'data_inicio' => date('Ymd H:i:s')
                ));
                $this->LogAtendimento->incluir( $dados_log_atendimento );
                $this->data = array();
                $this->BSession->setFlash('save_success'); 
                $this->redirect(array('action' => 'incluir'));
            // } else { 
                // $this->BSession->setFlash('save_error');
            }
        }

        $this->Fichas->carregarCombos();
        if(!empty($this->data['FichaScorecard']['codigo_cliente'])) {
            $embarcador_transportador = $this->EmbarcadorTransportador->dadosPorCliente( $this->data['FichaScorecard']['codigo_cliente'] );
            $embarcadores   = $embarcador_transportador['embarcadores'];
            $transportadores = $embarcador_transportador['transportadores'];
            $this->set(compact('embarcadores', 'transportadores'));
        }

    }
    
    function editar($codigo_ficha_scorecard) {
        $authUsuario    = $this->authUsuario;
          
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {
            $cliente = $this->Cliente->carregar($authUsuario['Usuario']['codigo_cliente']);  
            $this->data['FichaScorecard']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
            $this->data['FichaScorecard']['codigo_usuario'] = $authUsuario['Usuario']['codigo'];
            $this->data['Cliente']['codigo_documento'] = $cliente['Cliente']['codigo_documento'];
            $this->data['Cliente']['razao_social'] = $cliente['Cliente']['razao_social'];
        }
        
        if($this->RequestHandler->isPost()) {
            $this->data['FichaScorecard']['codigo'] = $codigo_ficha_scorecard;
            $valido = $this->validarFicha();            
            if($valido){
                $this->salvarFicha();
                //log Atendimento 
                $this->LogAtendimento =& ClassRegistry::init('LogAtendimento');
                $this->data['FichaScorecard']['codigo_produto'] = Produto::SCORECARD;
                $this->data['Profissional']['codigo'] =  $cod_profissional_log['ProfissionalLog']['codigo_profissional'];
                $this->LogAtendimento->gravaLogAtendimentoFichaScorecard($this->data,false,TipoOperacao::TIPO_OPERACAO_ATUALIZACAO);
            
                $this->BSession->setFlash('save_success');
                //$this->redirect(array('controller'=>'fichas_status_criterios', 'action' => 'resultados_pesquisa'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else{
            $this->salva_lista_contato($codigo_ficha_scorecard);
            //log Atendimento             
            $situacao = $this->FichaScorecard->find('first',array('conditions'=>array('codigo'=>$codigo_ficha_scorecard)));
            $cod_profissional_log = $this->ProfissionalLog->find('first',array('conditions'=>array('ProfissionalLog.codigo'=>$situacao['FichaScorecard']['codigo_profissional_log'])));
            $this->LogAtendimento =& ClassRegistry::init('LogAtendimento');
            $this->data['FichaScorecard']['codigo_produto'] = Produto::SCORECARD;
            $this->data['Profissional']['codigo'] =  $cod_profissional_log['ProfissionalLog']['codigo_profissional'];
            $this->LogAtendimento->gravaLogAtendimentoFichaScorecard($this->data,false,TipoOperacao::TIPO_OPERACAO_ATUALIZACAO);
            $this->Fichas->carregarDadosFicha($codigo_ficha_scorecard); 
            $this->redirect(array('controller'=>'fichas_status_criterios', 'action' => 'editar',$codigo_ficha_scorecard));      
        }
        $this->Fichas->carregarCombos();
        $this->set('codigo_ficha', $codigo_ficha_scorecard);
    }
    

    function aprovar($codigo_ficha_scorecard) {
        $authUsuario    = $this->authUsuario;      
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {
            $cliente = $this->Cliente->carregar($authUsuario['Usuario']['codigo_cliente']);  
            $this->data['FichaScorecard']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
            $this->data['FichaScorecard']['codigo_usuario'] = $authUsuario['Usuario']['codigo'];
            $this->data['Cliente']['codigo_documento']      = $cliente['Cliente']['codigo_documento'];
            $this->data['Cliente']['razao_social']          = $cliente['Cliente']['razao_social'];
        }
        if($this->RequestHandler->isPost()) {
            $this->data['FichaScorecard']['codigo'] = $codigo_ficha_scorecard;
            $this->autoRender = false;
            $valido = $this->validarFicha();
            if($valido){
                $this->salvarFicha();
                //log Atendimento 
                $situacao   = $this->FichaScorecard->find('first',array('conditions'=>array('codigo'=>$codigo_ficha_scorecard)));
                $cod_profissional_log = $this->ProfissionalLog->find('first',array('conditions'=>array('ProfissionalLog.codigo'=>$situacao['FichaScorecard']['codigo_profissional_log'])));
                $this->LogAtendimento =& ClassRegistry::init('LogAtendimento');
                $this->data['FichaScorecard']['codigo_produto'] = Produto::SCORECARD;
                $this->data['Profissional']['codigo'] =  $cod_profissional_log['ProfissionalLog']['codigo_profissional'];
                $this->LogAtendimento->gravaLogAtendimentoFichaScorecard($this->data,false,TipoOperacao::TIPO_OPERACAO_ATUALIZACAO);
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }         
            $this->redirect(array('controller'=>'fichas_status_criterios', 'action' => 'aprovar',$codigo_ficha_scorecard));
        } else {

            $this->salva_lista_contato($codigo_ficha_scorecard);
            $this->Fichas->carregarDadosFicha($codigo_ficha_scorecard); 
            $this->BSession->setFlash('save_success');
            $this->redirect(array('controller'=>'fichas_status_criterios', 'action' => 'aprovar',$codigo_ficha_scorecard));    
        }
        $this->Fichas->carregarCombos();
        $this->set('codigo_ficha', $codigo_ficha_scorecard);
    }

    private function validarFicha(){        
        $valido = 1;        
                //Implementação do SLA  e Implementação do data de validade pegar da configuração teleconsult conforme regra passada pelo Nelson Ota 15/04/2014 . Fazendo pelo produto 2 .
        $dados_sla = $this->ClienteProdutoServico->obterParametrosDoServico($this->data['FichaScorecard']['codigo_cliente'],Produto::SCORECARD, 1, $this->data['FichaScorecard']['codigo_profissional_tipo']);
        
        if (empty($dados_sla['ClienteProdutoServico']['tempo_pesquisa'])){
           $dados_sla['ClienteProdutoServico']['tempo_pesquisa'] = null;
        }

        if(empty($dados_sla['ClienteProdutoServico']['validade'])){
           $this->data['FichaScorecard']['data_validade'] =  date('Y-m-d');
        }else{
             $this->data['FichaScorecard']['data_validade'] = date("d/m/Y H:i:s",strtotime(date("Y-m-d", strtotime(date('Y-m-d'))) . " +".$dados_sla['ClienteProdutoServico']['validade']." month"));
        
        }

        $this->data['FichaScorecard']['tempo_sla'] = $dados_sla['ClienteProdutoServico']['tempo_pesquisa'];
        $valido &= $this->FichaScorecard->saveAll($this->data['FichaScorecard'], array('validate' => 'only'));
        
        // if ($this->data['Profissional']['codigo_documento']!='') {
        //   $valido &= $this->FichaScorecardRetorno->validarContatos($this->data['FichaScorecardRetorno']);
        // }

        // if ($this->data['Profissional']['cidade_naturalidade_profissional']!=''){
        //     $this->EnderecoCidade = & ClassRegistry::init('EnderecoCidade');
        //     $cidade = $this->EnderecoCidade->carrega_cidade_nome($this->data['Profissional']['cidade_naturalidade_profissional']);
        //     $this->data['Profissional']['codigo_endereco_cidade_naturalidade'] = $cidade['EnderecoCidade']['codigo'];
        // }

        $profissional_em_pesquisa = $this->FichaScorecard->validaPorCPF($this->data['Profissional']['codigo_documento'],$this->data['FichaScorecard']['codigo_profissional_tipo']);
        $valido &= $profissional_em_pesquisa;
        if( $profissional_em_pesquisa == FALSE ){            
            $this->FichaScorecard->invalidate('codigo_profissional_tipo','Profissional em pesquisa');
        }
        $valido &= $this->Profissional->validarDadosFicha($this->data['Profissional'], $this->ProfissionalTipo->precisaDadosCNH($this->data['FichaScorecard']['codigo_profissional_tipo']));
        $valido &= $this->ProfissionalEndereco->validarDados($this->data['ProfissionalEndereco']);
        $valido &= $this->ProfissionalContato->validarContatosFichaScoreCard($this->data['ProfissionalContato']);
        
        if( $this->data['FichaScorecard']['codigo_profissional_tipo'] == 1 ){//Se carreteiro valido carga
           $valido &= $this->FichaScorecard->validaCamposCarreteiro($this->data);
           $valido &= $this->FichaScorecard->validaOrigemDestino($this->data);
        }
        if($this->data['FichaScorecard']['codigo_profissional_tipo'] != 2){
            if($this->data['FichaScorecard']['codigo_profissional_tipo'] != 1){
                   $outros_pro = '';
                   $outros_pro = $this->FichaScorecard->buscaPorCPF($this->data['Profissional']['codigo_documento'],$this->data['FichaScorecard']['codigo_profissional_tipo'],$this->data['FichaScorecard']['codigo_cliente']);
                   if($outros_pro['Cliente']['total'] > 0){
                    $valido &= $this->FichaScorecard->validaCamposOutros($outros_pro['Cliente']['total']);
                   }
            }
        }
        
        //Validando veículos 
        //Forçando sempre 'S' a pedido do Camarinho
        $erro_possui = 0;
        $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] ='';
        $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] =''; 
        $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] =''; 
        if ($this->data['FichaScorecard']['codigo_profissional_tipo'] != 10 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 9 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 7 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 8 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 6 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 5){
            //Validando Veículo
            if($this->data['FichaScorecard']['codigo_profissional_tipo'] == 1){
                $this->data['FichaScorecardVeiculo']['possui_veiculo'] ='S';
                $this->data['FichaScorecardVeiculo']['0']['Veiculo']['veiculo_sn'] = 'S';
            }
            
            if ($this->data['FichaScorecardVeiculo']['possui_veiculo']==''){
               
               $valido &= $this->FichaScorecard->valida_possui_veiculo();
               $this->FichaScorecardVeiculo->validationErrors['Veiculo'] = $this->FichaScorecardQuestaoResp->validarDados($this->data['FichaScorecardQuestaoResposta']);
               //$this->BSession->setFlash('erro_veiculo_fichascorecard');
               $erro_possui = 1;
               $valido = 0;
            } 

            if ($this->data['FichaScorecardVeiculo']['possui_veiculo']=='S'){
                $valido &= $this->erro_veiculo($valido);
                $tem_erro_veiculo =$valido; 
                //Possui Carreta ? 
                if ($this->data['FichaScorecardVeiculo']['1']['Veiculo']['veiculo_sn']==''){
                    $erro_possui = 1;
                    $valido = 0;
                }

                if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['veiculo_sn']=='S') {
                   $valido &= $this->erro_carreta($valido); 
                   $tem_erro_carreta = $valido;
                }
                //debug($valido);die();
                
                if ($this->data['FichaScorecardVeiculo']['1']['Veiculo']['veiculo_sn']=='S'){
                   //Possui Bitrem ?  
                    if ($this->data['FichaScorecardVeiculo']['2']['Veiculo']['veiculo_sn']==''){
                     //$this->BSession->setFlash('erro_bitrem_fichascorecard');
                     //debug('entrou');
                     $erro_possui = 1; 
                     $valido = 0;
                    }
                    if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['veiculo_sn']=='S') {
                       $valido &= $this->erro_bitrem($valido); 
                       $tem_erro_bitrem = $valido; 

                    }

                  
                }
             
        }

            if ($this->data['FichaScorecard']['codigo_profissional_tipo'] != 10 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 9 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 7 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 8 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 6 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 5){
             
                if ($this->data['FichaScorecardVeiculo']['possui_veiculo']=='S'){   
                        //Validando Veículo 
                          if ($this->data['FichaScorecardVeiculo']['0']['Veiculo']['placa']==''){
                             $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('placa','Placa obrigatória');
                          }
                          
                          if($this->data['FichaScorecardVeiculo']['0']['EnderecoCidade']['cidade_emplacamento']==''){
                          }

                          if($this->data['FichaScorecardVeiculo']['0']['EnderecoCidade']['cidade_emplacamento']==''){
                             $this->FichaScorecardVeiculo->validationErrors[0]['EnderecoCidade'] = array('cidade_emplacamento' => 'Cidade obrigatória');
                          }else if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['codigo_cidade_emplacamento']==''){
                            $this->FichaScorecardVeiculo->validationErrors[0]['EnderecoCidade'] = array('cidade_emplacamento' => 'Cidade inválida');
                          }

                          if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['chassi'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('chassi','Chassi obrigatório');
                          }
                          $carac_renavam = strlen ($this->data['FichaScorecardVeiculo']['0']['Veiculo']['renavam']);
                          if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['renavam'] == ''){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('renavam','Renavam obrigatório');
                          }else if( $carac_renavam != 9 && $carac_renavam != 11){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('renavam','Renavam precisa ter 9 ou 11 caracteres');
                          }
                          
                          if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['codigo_veiculo_cor'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('codigo_veiculo_cor','Cor obrigatória');
                          }
                          if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['ano_fabricacao'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('ano_fabricacao','Ano Fabricação obrigatório');
                          }
                          if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['ano'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('ano','Ano obrigatório');
                          }  
                          if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['codigo_veiculo_fabricante'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('codigo_veiculo_fabricante','Fabricante obrigatório');
                          }   
                          if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['codigo_veiculo_modelo'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('codigo_veiculo_modelo','Modelo obrigatório');
                          }
                          if ($this->data['FichaScorecardVeiculo'][1]['Veiculo']['veiculo_sn']=='S') {

                              if (!empty($this->data['FichaScorecardVeiculo']['0']['Veiculo']['placa']) && !empty($this->data['FichaScorecardVeiculo']['1']['Veiculo']['placa'])){
                                  
                                  if(strtoupper($this->data['FichaScorecardVeiculo']['0']['Veiculo']['placa'])==strtoupper($this->data['FichaScorecardVeiculo']['1']['Veiculo']['placa'])){
                                     
                                    $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('placa','Placa do Veículo não pode ser igual a Placa da Carreta.');
                                  }
                              
                              }
                          } 
                          if($this->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn']=='S'){  
                              if (!empty($this->data['FichaScorecardVeiculo']['0']['Veiculo']['placa']) && !empty($this->data['FichaScorecardVeiculo']['2']['Veiculo']['placa'])){
                                
                                  if(strtoupper($this->data['FichaScorecardVeiculo']['0']['Veiculo']['placa'])==strtoupper($this->data['FichaScorecardVeiculo']['2']['Veiculo']['placa'])){
                                    
                                    $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('placa','Placa do Veículo não pode ser igual a Placa do Bitrem.');
                                  }
                              
                              }
                          } 
                          if ($this->data['FichaScorecardVeiculo'][1]['Veiculo']['veiculo_sn']=='S') {
                              if (!empty($this->data['FichaScorecardVeiculo']['0']['Veiculo']['renavam']) && !empty($this->data['FichaScorecardVeiculo']['1']['Veiculo']['renavam'])){
                                  
                                  if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['renavam']==$this->data['FichaScorecardVeiculo']['1']['Veiculo']['renavam']){
                                     
                                    $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('renavam','Renavam do Veículo não pode ser igual ao Renavam da Carreta.');
                                  }
                              
                              }
                           }
                          if($this->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn']=='S'){   
                              if (!empty($this->data['FichaScorecardVeiculo']['0']['Veiculo']['renavam']) && !empty($this->data['FichaScorecardVeiculo']['2']['Veiculo']['renavam'])){
                                
                                  if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['renavam']==$this->data['FichaScorecardVeiculo']['2']['Veiculo']['renavam']){
                                    
                                    $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('renavam','Renavam do Veículo não pode ser igual ao Renavam do Bitrem.');
                                  }
                              
                              } 
                          } 
                        
                        $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->validationErrors;
                }      
                
                if($this->data['FichaScorecardVeiculo'][1]['Veiculo']['veiculo_sn']=='S'){

                        // Validar Carreta 
                          if ($this->data['FichaScorecardVeiculo']['1']['Veiculo']['placa']==''){
                             $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('placa','Placa obrigatória');
                          }
                          if($this->data['FichaScorecardVeiculo']['1']['EnderecoCidade']['cidade_emplacamento']==''){
                             $this->FichaScorecardVeiculo->validationErrors[1]['EnderecoCidade'] = array('cidade_emplacamento' => 'Cidade obrigatória');
                          }else if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['codigo_cidade_emplacamento']==''){
                            $this->FichaScorecardVeiculo->validationErrors[1]['EnderecoCidade'] = array('cidade_emplacamento' => 'Cidade inválida');
                          }

                          if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['chassi'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('chassi','Chassi obrigatório');
                          }
                          $carac_renavam = strlen ($this->data['FichaScorecardVeiculo']['1']['Veiculo']['renavam']);
                          if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['renavam'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('renavam','Renavam obrigatório');
                          }else if( $carac_renavam != 9 && $carac_renavam != 11){
                            $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('renavam','Renavam precisa ter 9 ou 11 caracteres');
                          }
                         
                          if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['codigo_veiculo_cor'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('codigo_veiculo_cor','Cor obrigatória');
                          }
                          if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['ano_fabricacao'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('ano_fabricacao','Ano Fabricação obrigatório');
                          }
                          if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['ano'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('ano','Ano obrigatório');
                          }  
                          if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['codigo_veiculo_fabricante'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('codigo_veiculo_fabricante','Fabricante obrigatório');
                          }   
                          if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['codigo_veiculo_modelo'] ==''){
                            $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('codigo_veiculo_modelo','Modelo obrigatório');
                          } 

                          if($this->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn']=='S'){
                                if (!empty($this->data['FichaScorecardVeiculo']['1']['Veiculo']['placa']) && !empty($this->data['FichaScorecardVeiculo']['2']['Veiculo']['placa'])){
                                        //debug('entrou 2');
                                        //debug($this->data['FichaScorecardVeiculo']['1']['Veiculo']['placa']);
                                        //debug($this->data['FichaScorecardVeiculo']['2']['Veiculo']['placa']);
                                      if(strtoupper($this->data['FichaScorecardVeiculo']['1']['Veiculo']['placa'])==strtoupper($this->data['FichaScorecardVeiculo']['2']['Veiculo']['placa'])){
                                        //debug('entrou');
                                        $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('placa','Placa da Carreta não pode ser igual a Placa do Bitrem.');
                                      }
                                  
                                } 
                                
                                if (!empty($this->data['FichaScorecardVeiculo']['1']['Veiculo']['renavam']) && !empty($this->data['FichaScorecardVeiculo']['2']['Veiculo']['renavam'])){
                                    
                                      if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['renavam']==$this->data['FichaScorecardVeiculo']['2']['Veiculo']['renavam']){
                                        
                                        $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('renavam','Renavam da Carreta não pode ser igual ao Renavam do Bitrem.');
                                      }
                                  
                                }
                        }      


                        $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->validationErrors;
                }

                // Validar Bitrem VeiculoBiTrem
                if($this->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn']=='S'){ 
                      if ($this->data['FichaScorecardVeiculo']['2']['Veiculo']['placa']==''){
                         $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('placa','Placa obrigatória');
                      }
                      if($this->data['FichaScorecardVeiculo']['2']['EnderecoCidade']['cidade_emplacamento']==''){
                         $this->FichaScorecardVeiculo->validationErrors[2]['EnderecoCidade'] = array('cidade_emplacamento' => 'Cidade obrigatória');
                      }else if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['codigo_cidade_emplacamento']==''){
                        $this->FichaScorecardVeiculo->validationErrors[2]['EnderecoCidade'] = array('cidade_emplacamento' => 'Cidade inválida');
                      }


                      if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['chassi'] ==''){
                        $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('chassi','Chassi obrigatório');
                      }
                      $carac_renavam = strlen ($this->data['FichaScorecardVeiculo']['2']['Veiculo']['renavam']);
                      if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['renavam'] ==''){
                        $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('renavam','Renavam obrigatório');
                      }else if( $carac_renavam != 9 && $carac_renavam != 11){
                        $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('renavam','Renavam precisa ter 9 ou 11 caracteres');
                      }
                     
                      if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['codigo_veiculo_cor'] ==''){
                        $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_cor','Cor obrigatória');
                      }
                      if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['ano_fabricacao'] ==''){
                        $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('ano_fabricacao','Ano Fabricação obrigatório');
                      }
                      if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['ano'] ==''){
                        $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('ano','Ano obrigatório');
                      }  
                      if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['codigo_veiculo_fabricante'] ==''){
                        $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_fabricante','Fabricante obrigatório');
                      }   
                      if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['codigo_veiculo_modelo'] ==''){
                        $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_modelo','Modelo obrigatório');
                      }  

                    $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->validationErrors;
                }   

                //Validando Proprietarios

                if( empty($this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento']) || empty($this->data['FichaScorecardVeiculo'][0]['Proprietario']['nome_razao_social']) ){
                    $this->FichaScorecardVeiculo->validationErrors[0]['Proprietario'] = $this->Proprietario->validationErrors;
                }
                if ($this->data['FichaScorecardVeiculo']['0']['ProprietarioEndereco']['codigo_endereco']==''){
                   $this->FichaScorecardVeiculo->validationErrors[0]['ProprietarioEndereco'] = $this->ProprietarioEndereco->validationErrors;                   
                }
                if( empty($this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento']) || empty($this->data['FichaScorecardVeiculo'][1]['Proprietario']['nome_razao_social']) ){
                    $this->FichaScorecardVeiculo->validationErrors[1]['Proprietario']       = $this->Proprietario->validationErrors;
                }
                if ($this->data['FichaScorecardVeiculo']['1']['ProprietarioEndereco']['codigo_endereco']==''){
                    $this->FichaScorecardVeiculo->validationErrors[1]['ProprietarioEndereco'] = $this->ProprietarioEndereco->validationErrors;
                }
                if( empty($this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento']) || empty($this->data['FichaScorecardVeiculo'][2]['Proprietario']['nome_razao_social']) ){
                    $this->FichaScorecardVeiculo->validationErrors[2]['Proprietario']       = $this->Proprietario->validationErrors;
                }
                if ($this->data['FichaScorecardVeiculo']['2']['ProprietarioEndereco']['codigo_endereco']==''){
                    $this->FichaScorecardVeiculo->validationErrors[2]['ProprietarioEndereco'] = $this->ProprietarioEndereco->validationErrors;
                }


                if($this->data['FichaScorecardVeiculo']['possui_veiculo']=='S')  {
                    $this->data['FichaScorecardVeiculo'][0]['ProprietarioContato'] = isset($this->data['FichaScorecardVeiculo'][0]['ProprietarioContato']) ? $this->data['FichaScorecardVeiculo'][0]['ProprietarioContato'] : NULL;
                    $valido &= $this->ProprietarioContato->validarContatosFichaScoreCard($this->data['FichaScorecardVeiculo'][0]['ProprietarioContato']); 
                    $this->FichaScorecardVeiculo->validationErrors[0]['ProprietarioContato']  = $this->ProprietarioContato->validationErrors;
                    if( empty($this->data['FichaScorecardVeiculo'][0]['ProprietarioEndereco']['numero'] ) ){
                        $this->FichaScorecardVeiculo->validationErrors[0]['ProprietarioEndereco']['numero']  = 'Informe o número';
                    }
                }
                if($this->data['FichaScorecardVeiculo'][1]['Veiculo']['veiculo_sn']=='S'){
                    $this->data['FichaScorecardVeiculo'][1]['ProprietarioContato'] = isset($this->data['FichaScorecardVeiculo'][1]['ProprietarioContato']) ? $this->data['FichaScorecardVeiculo'][1]['ProprietarioContato'] : NULL;
                    $valido &= $this->ProprietarioContato->validarContatosFichaScoreCard($this->data['FichaScorecardVeiculo'][1]['ProprietarioContato']); 
                    $this->FichaScorecardVeiculo->validationErrors[1]['ProprietarioContato']  = $this->ProprietarioContato->validationErrors;
                }
                
                 if($this->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn']=='S'){
                    $this->data['FichaScorecardVeiculo'][2]['ProprietarioContato'] = isset($this->data['FichaScorecardVeiculo'][2]['ProprietarioContato']) ? $this->data['FichaScorecardVeiculo'][2]['ProprietarioContato'] : NULL;
                    $valido &= $this->ProprietarioContato->validarContatosFichaScoreCard($this->data['FichaScorecardVeiculo'][2]['ProprietarioContato']); 
                    $this->FichaScorecardVeiculo->validationErrors[2]['ProprietarioContato']  = $this->ProprietarioContato->validationErrors;
                }  
            } 
        }   
        
        if ($this->data['FichaScorecardVeiculo']['possui_veiculo']=='N'){
            unset($this->data['FichaScorecardVeiculo'][0]);
            unset($this->data['FichaScorecardVeiculo'][1]);
            unset($this->data['FichaScorecardVeiculo'][2]);
            $this->data['FichaScorecardVeiculo']['possui_veiculo'] ='N';
        }
        
        if (isset($this->data['FichaScorecardVeiculo'][1]['Veiculo']['veiculo_sn']) && $this->data['FichaScorecardVeiculo'][1]['Veiculo']['veiculo_sn'] == 'N' ) {
            unset($this->data['FichaScorecardVeiculo'][1]);
            unset($this->data['FichaScorecardVeiculo'][2]); 
            $this->data['FichaScorecardVeiculo'][1]['Veiculo']['veiculo_sn'] = 'N';
        }
        
        if (isset($this->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn']) && $this->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn'] == 'N' ){
            unset($this->data['FichaScorecardVeiculo'][2]); 
            $this->data['FichaScorecardVeiculo'][2]['Veiculo']['veiculo_sn'] = 'N';
        }
        
        $valido &= $this->FichaScorecardQuestaoResp->validarDados($this->data['FichaScorecardQuestaoResposta']);

        if ($this->data['FichaScorecard']['codigo_profissional_tipo'] != 10 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 9 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 7 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 8 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 6 and $this->data['FichaScorecard']['codigo_profissional_tipo'] != 5){
            if ($erro_possui==1 and $valido==0){
                if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['veiculo_sn']!='S' and $this->data['FichaScorecardVeiculo']['1']['Veiculo']['veiculo_sn']=='S') {
                    $this->BSession->setFlash('erro_bitrem_fichascorecard');
                }elseif($this->data['FichaScorecardVeiculo']['1']['Veiculo']['veiculo_sn']!='S' and $this->data['FichaScorecardVeiculo']['possui_veiculo']!=''){
                    $this->BSession->setFlash('erro_carreta_fichascorecard');
               }elseif($this->data['FichaScorecardVeiculo']['possui_veiculo']==''){
                  $this->BSession->setFlash('erro_veiculo_fichascorecard');
               }else{   
                  $this->BSession->setFlash('save_error');
               }
            }
        } else {
            if ($valido==0){
                $this->BSession->setFlash('save_error');
            }
        }
        return $valido;   
    }


    private function salvarFicha($nova = false){        
        $profissional_logs = $this->Profissional->salvarProfissionalScorecard( $this->data, TRUE );
        if( !empty($this->data['FichaScorecard']['codigo_embarcador']) ) {
            $this->data['FichaScorecard']['codigo_cliente_embarcador'] = $this->data['FichaScorecard']['codigo_embarcador'];
        }
        if( !empty($this->data['FichaScorecard']['codigo_transportador']) ) {
            $this->data['FichaScorecard']['codigo_cliente_transportador'] = $this->data['FichaScorecard']['codigo_transportador'];
        }        
        $this->data['FichaScorecard']['codigo_profissional_log'] = $profissional_logs['ProfissionalLog'];
        $this->data['FichaScorecard']['codigo_profissional_endereco_log'] = $profissional_logs['ProfissionalEnderecoLog'];
        if( $nova ){
            $this->data['FichaScorecard']['codigo_status'] = FichaScorecardStatus::CADASTRADA;
            $this->data['FichaScorecard']['ativo'] = 1;
        }
        $this->FichaScorecard->save($this->data['FichaScorecard'], array('validate' => false));
        $codigo_ficha_scorecard = $this->FichaScorecard->id;
        $this->FichaScorecardRetorno->salvar($this->data['FichaScorecardRetorno'], $codigo_ficha_scorecard);
        $this->FichaScorecardQuestaoResp->salvar($this->data['FichaScorecardQuestaoResposta'], $codigo_ficha_scorecard);
        $this->FichaScProfContatoLog->salvar($profissional_logs['ProfissionalContatoLog'], $codigo_ficha_scorecard);
        if(!$nova){
            $this->FichaScorecardVeiculo->excluir($codigo_ficha_scorecard);
        }
        if (in_array($this->data['FichaScorecard']['codigo_profissional_tipo'], array(ProfissionalTipo::CARRETEIRO, ProfissionalTipo::AGREGADO, ProfissionalTipo::FUNCIONARIO_MOTORISTA, ProfissionalTipo::PROPRIETARIO))) {
            unset($this->data['FichaScorecardVeiculo']['possui_veiculo']);
            foreach($this->data['FichaScorecardVeiculo'] as $key=>$fichaScorecardVeiculo){
                if(!empty($fichaScorecardVeiculo['Veiculo']['placa'])){
                    $proprietario_logs = $this->Proprietario->salvarProprietarioScorecard($fichaScorecardVeiculo, TRUE );
                    $veiculo_log = $this->Veiculo->salvarVeiculoScorecard($fichaScorecardVeiculo, TRUE );
                    $codigo_ficha_scorecard_veiculo = $this->FichaScorecardVeiculo->salvar($key, $veiculo_log, $proprietario_logs, $codigo_ficha_scorecard);
                    $this->FichaScVeicPropContatoLog->salvar($proprietario_logs['ProprietarioContatoLog'], $codigo_ficha_scorecard_veiculo);
                }
            }
        } 
    }

    public function salva_lista_contato($codigo_ficha_scorecard) {
         $this->FichaScorecardRetorno->salvar($this->data['FichaScorecardRetorno'], $codigo_ficha_scorecard);
    }

    public function novo_contato_retorno($key){
        $this->layout   = false;
        $tipo_retorno = $this->TipoRetorno->find('list', array('conditions' => array('cliente' => true)));
        $tipo = "retorno";
        $model = "FichaScorecardRetorno";
    
        $this->set(compact('tipo_retorno', 'key', 'tipo', 'model'));
        $this->render('/elements/fichas_scorecard/incluir_linha_contato');
    }

    public function novo_contato_profissional($key, $index='' ){
        $tipo_retorno_fixo = 0;
        if(!empty($this->params['named']['tipo_retorno_fixo']))
            $tipo_retorno_fixo = $this->params['named']['tipo_retorno_fixo'];
        $this->layout = false;
        if( $index === 'profissional_contato')
            $tipo_retorno = $this->TipoRetorno->listar();
        else
            $tipo_retorno = $this->TipoRetorno->find('list', array('conditions' => array('profissional' => true)));
        $tipo_contato = $this->TipoContato->listarParFichaScorecard();
        $tipo  = "profissional";
        $model = "ProfissionalContato"; 
        $this->set(compact('tipo_retorno', 'tipo_contato', 'key', 'tipo', 'model', 'tipo_retorno_fixo'));
        $this->render('/elements/fichas_scorecard/incluir_linha_contato');
    }



    public function novo_contato_proprietario($key, $index){
        $this->layout   = false;
        $tipo_retorno = $this->TipoRetorno->find('list', array('conditions' => array('proprietario' => true)));
        $tipo_contato = $this->TipoContato->listarParFichaScorecard();
        $tipo = "proprietario";
        $model = "FichaScorecardVeiculo.{$index}.ProprietarioContato";
    
        $this->set(compact('tipo_retorno', 'tipo_contato', 'key', 'tipo', 'model'));
        $this->render('/elements/fichas_scorecard/incluir_linha_contato');
    }
    
    public function carregar_por_cpf( $codigo_documento, $codigo_profissional_tipo, $codigo_cliente = null ){
        if ( $codigo_profissional_tipo == 1 ){
            $retorno = $this->FichaScorecard->buscaPorCPFCarreteiro( $codigo_documento, $codigo_profissional_tipo );
            if( $retorno['Carreteiro']['total'] == 0 ){
                $retorno = $this->FichaScorecard->buscaPorCPF( $codigo_documento, $codigo_profissional_tipo, $codigo_cliente );
            }
        }else{
            $retorno = $this->FichaScorecard->buscaPorCPF( $codigo_documento, $codigo_profissional_tipo, $codigo_cliente );
        }
        echo json_encode($retorno);exit;
    }
    
    public function fichas_a_pesquisar(){
        $this->pageTitle = 'Fichas a Pesquisar Scorecard';  
        //Limpar a sessao
        $this->Filtros->limpa_sessao('FichaScorecard');
        //Habilitar permissões aco pesquisa
        $_SESSION['FiltrosFichaScorecard']['action'] = 'fichas_a_pesquisar';
        $tipos_profissional = $this->Fichas->listProfissionalTipoAutorizado();
        $lista_seguradora   = $this->Seguradora->find('list');
        $fichas_a_pesquisar = TRUE;
        $status_ficha      = array( 
            FichaScorecardStatus::A_PESQUISAR => FichaScorecardStatus::descricao(FichaScorecardStatus::A_PESQUISAR), 
            FichaScorecardStatus::EM_PESQUISA => FichaScorecardStatus::descricao(FichaScorecardStatus::EM_PESQUISA), 
            FichaScorecardStatus::PENDENTE    => FichaScorecardStatus::descricao(FichaScorecardStatus::PENDENTE) 
        );
        $data['FichaScorecard']['codigo_status'] = $status_ficha;
        $this->Session->write('fichas_a_pesquisar', $fichas_a_pesquisar );
        $data['FichaScorecard']['codigo_tipo_profissional'] = key($tipos_profissional);
        $this->Filtros->controla_sessao($data, 'FichaScorecard');
        $this->set(compact('tipos_profissional','lista_seguradora', 'fichas_a_pesquisar', 'status_ficha'));  
    }
    
    public function fichas_a_aprovar(){
        $this->Filtros->limpa_sessao('FichaScorecard');
        $this->pageTitle = 'Fichas a Aprovar Scorecard';
        $tipos_profissional = $this->Fichas->listProfissionalTipoAutorizado();
        
        $lista_seguradora   = $this->Seguradora->find('list');    
        $status_ficha       = array( 
            FichaScorecardStatus::A_APROVAR  => FichaScorecardStatus::descricao( FichaScorecardStatus::A_APROVAR ), 
            FichaScorecardStatus::EM_APROVACAO  => FichaScorecardStatus::descricao( FichaScorecardStatus::EM_APROVACAO ) 
        );    
        $data['FichaScorecard']['gerente']          = false;
        $data['FichaScorecard']['codigo_status']    = $status_ficha;
        $data['FichaScorecard']['codigo_tipo_profissional'] = key($tipos_profissional);
        $this->Session->write('fichas_a_pesquisar', FALSE );
        $this->Filtros->controla_sessao($data, 'FichaScorecard');
        $this->set(compact('tipos_profissional','lista_seguradora', 'status_ficha'));
    }
    
    public function todas_fichas(){ 
        $this->pageTitle = 'Fichas em Análise';  

        $_SESSION['FiltrosFichaScorecard']['action'] = 'todas_fichas';
        $todas_fichas = TRUE;

        $tipos_profissional = $this->Fichas->listProfissionalTipoAutorizado();
        $lista_seguradora = $this->Seguradora->find('list');
        $status_ficha      = array( 
            FichaScorecardStatus::RENOVADA => FichaScorecardStatus::descricao(FichaScorecardStatus::RENOVADA), 
            FichaScorecardStatus::A_APROVAR => FichaScorecardStatus::descricao(FichaScorecardStatus::A_APROVAR), 
            FichaScorecardStatus::PENDENTE  => FichaScorecardStatus::descricao(FichaScorecardStatus::PENDENTE) 
        );
        $this->set(compact('tipos_profissional','lista_seguradora','status_ficha'));
        $data['FichaScorecard']['gerente'] = false;
        $data['FichaScorecard']['codigo_tipo_profissional'] = key($tipos_profissional);
        $this->Session->write('todas_fichas', $todas_fichas );
        $this->Filtros->controla_sessao($data, 'FichaScorecard');
    }
    
    public function fichas_clientes() {
        ClassRegistry::init('Produto');
        $produtos = isset($this->data)
            ? ClassRegistry::init('ClienteProduto')->listaProdutosTLCS($this->data['FichaScorecard']['codigo_cliente'])
            : array();
        $this->set(compact('produtos'));
        
        if ($this->data['FichaScorecard']['codigo_produto'] == Produto::SCORECARD) {
            $ficha = $this->FichaScorecard->buscaPorCPF($this->data['FichaScorecard']['codigo_documento']);
            $modelName = 'FichaScorecard';
        } else {
            $profissional = ClassRegistry::init('Profissional')->buscaPorCPF($this->data['FichaScorecard']['codigo_documento']);
            $ficha = ClassRegistry::init('Ficha')->obterUltimaFichaProfissional($this->data['FichaScorecard']['codigo_cliente'], $profissional['Profissional']['codigo'], $this->data['FichaScorecard']['codigo_produto']);
            $modelName = 'Ficha';
        }
        if (isset($ficha[$modelName])) {
            $encontrou = true;
            $status = ClassRegistry::init('Status')->obtemDescricao($ficha[$modelName]['codigo_status']);
            $observacao = $ficha[$modelName]['observacao'];
        } else
            $encontrou = false;
        $this->set(compact('encontrou', 'ficha', 'status', 'observacao'));
    }
    
    public function listar_fichas(){
        $this->layout = 'ajax';  
        $filtros                        = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $this->ClienteProdutoServico    = ClassRegistry::init('ClienteProdutoServico');
        $this->Produto                  = ClassRegistry::init('Produto');
        if( $this->Session->read('fichas_a_pesquisar' ) === TRUE ) {
            $filtros['codigo_status'] = array( FichaScorecardStatus::A_PESQUISAR, FichaScorecardStatus::EM_PESQUISA, FichaScorecardStatus::PENDENTE );
            $action = FichaScorecardStatus::A_PESQUISAR;
        } else {
            $filtros['codigo_status'] = array( FichaScorecardStatus::A_APROVAR, FichaScorecardStatus::EM_APROVACAO); 
            $action = FichaScorecardStatus::A_APROVAR;
        }
        $params             = $this->FichaScorecard->parametros_fichas_a_pesquisar($filtros);
        $totalforaprazo     = $this->FichaScorecard->find('count',array('conditions'=>array('DATEDIFF(mi,DATEADD(MI,-90,getdate()),[FichaScorecard].[data_inclusao]) <'=>'0',$params['conditions'])));
        $totaldentroprazo   = $this->FichaScorecard->find('count',array('conditions'=>array('DATEDIFF(mi,DATEADD(MI,-90,getdate()),[FichaScorecard].[data_inclusao]) >'=>'0',$params['conditions'])));
        $cliente_produto_servico = $this->ClienteProdutoServico->produtosEServicos(29126, Produto::SCORECARD, Servico::CADASTRO_DE_FICHA);
        $tempo_pesquisa     = empty($cliente_produto_servico) ? 0 : $cliente_produto_servico[0]['ClienteProdutoServico']['tempo_pesquisa'];  
        $count              = $this->FichaScorecard->find('count',array('conditions'=>$params['conditions'],'order'=>$params['order']));
        $this->paginate['FichaScorecard'] = $params;
        $listar             = $this->paginate('FichaScorecard');
        $tem_permissao_liberacao    = $this->BAuth->temPermissao($this->authUsuario['Usuario']['codigo_uperfil'], array('controller' => 'FichasScorecard', 'action' => 'liberar_ficha')) ? 'S' : 'N';
        $tem_permissao_visualizacao = $this->BAuth->temPermissao($this->authUsuario['Usuario']['codigo_uperfil'], array('controller' => 'FichasStatusCriterios', 'action' => 'resultado_ficha')) ? 'S' : 'N';
        $this->set(compact('count','listar','tem_permissao_liberacao','tem_permissao_visualizacao','totaldentroprazo','totalforaprazo', 'tempo_pesquisa', 'action'));

    }
    
    public function liberar_ficha($codigo_ficha, $situacao='pesquisa') {
        $this->FichaScorecard->liberarResponsavelFicha($codigo_ficha,$situacao); 
        $this->redirect($this->referer());
    }
    
    public function fichas_log(){
        $this->pageTitle = 'Log de Alteração Fichas Scorecard'; 
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecardLog');
        $tipos_profissional = $this->Fichas->listProfissionalTipoAutorizado();
        $statuses = ClassRegistry::init('FichaScorecardStatus')->descricoes;
        if( $filtros['data_inclusao_inicio'] ){
            $this->data['FichaScorecardLog'] = $filtros;
        } else{
            $this->data['FichaScorecardLog']['data_inclusao_inicio'] = date('d/m/Y');
            $this->data['FichaScorecardLog']['data_inclusao_fim']    = date('d/m/Y');   
        }
        $this->set(compact('tipos_profissional', 'statuses'));
    }
    
    public function listar_fichas_log(){
        $this->layout  = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecardLog');
        $params  = $this->FichaScorecardLog->parametros_fichas_a_pesquisar($filtros);
        $this->paginate['FichaScorecardLog'] = $params;
        $listar = $this->paginate('FichaScorecardLog'); 
        $this->set(compact('listar'));
    }

    public function fichas_a_renovar($codigo_cliente = null) {
        $filtros             = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if($codigo_cliente == null)
            $codigo_cliente = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : null;
        if(!$this->RequestHandler->isPost()){
          //Fixo  pois foi pedido em reunião com Camarinho e Janaína
          $this->data['FichaScorecard']['dias_renovacao']=40;
        }
        if($this->RequestHandler->isPost()){
            if(isset($_POST['excluir']))
                $codigos = implode(',',$_POST['excluir']);
            else
                $codigos = '0';

          //debug($filtros);die();
            $this->FichaScorecard->gravar_renovacao($this->data['FichaScorecard']['contato'],$this->data['FichaScorecard']['email'], $codigos,$filtros['dias_renovacao'],$filtros['codigo_cliente']);
            $this->BSession->setFlash('save_success');
        }
        if(isset($this->authUsuario['Usuario']['codigo_cliente']) && !empty($this->authUsuario['Usuario']['codigo_cliente']) && $this->authUsuario['Usuario']['codigo_cliente'] != '' ) {
            $this->set('isCliente','1');
        }

        $tot_fichas = $this->FichaScorecard->verificarRenovacaoMes($codigo_cliente);
        $this->pageTitle = 'Fichas a Renovar Scorecard';
    }
   
    public function listar_fichas_a_renovar(){
        $this->layout      = 'ajax';
        $filtros             = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if(isset($this->authUsuario['Usuario']['codigo_cliente']) && !empty($this->authUsuario['Usuario']['codigo_cliente']) && $this->authUsuario['Usuario']['codigo_cliente'] != '' ) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            $this->set('isCliente','1');
        }
        $codigo_cliente = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : null;

        if(isset($filtros['dias_renovacao'])) {
            $dias_renovacao = $filtros['dias_renovacao'];
        } else  {
            $dias_renovacao = 40;
        }

        $auth_user = $this->BAuth->user();
                
        //$tot_fichas = $this->FichaScorecard->verificarRenovacaoMes($codigo_cliente);
        $dados = $this->FichaScorecard->listarFichasARenovar($codigo_cliente,$dias_renovacao, $auth_user['Usuario']['codigo_cliente']);

        if(count($dados) == 0){
            $this->set('jaRenovou', true);
            $this->set('fichas',array());
            $this->set('codigo_cliente',$codigo_cliente);
        } else {
            $this->set('jaRenovou', false);                        

            $this->set('fichas',$dados);
            $this->set('codigo_cliente',$codigo_cliente);

            $data_pesquisa = date('Y-m',mktime(0, 0, 0, date('m')+1, 1, date('Y')));
            list($ano,$mes) = explode('-',$data_pesquisa);
            $ultimo_dia = date('d',mktime(0, 0, 0, $mes+1, 1, $ano)-1);
         
            $dias =  $dias_renovacao +7;
            $dt_ini = date('Y-m-d', strtotime("+7 days",strtotime(date('Y-m-d')))); //date('Y-m-d');//$data_pesquisa . '-01';
            $dt_fim = date('Y-m-d', strtotime("+".$dias."days",strtotime(date('Y-m-d')))); //$data_pesquisa . '-' . $ultimo_dia;


            $this->set('dt_ini', date('d/m/Y', strtotime($dt_ini)));
            $this->set('dt_fim', date('d/m/Y', strtotime($dt_fim)));
        }
    }

    public function excluir_vinculo_profissional(){
        if( !empty($this->data['FichaScorecard']['excluir'])){
            $dadosExclusao = array();
            $dadosAlertaEmail = array();
            foreach ($this->data['FichaScorecard']['excluir'] as $key=> $codigo_ficha ){
                $conditions  = array('FichaScorecard.codigo' => $codigo_ficha );
                $dados_ficha = $this->FichaScorecard->carregarFichaCompleta( $conditions );
                $codigo_cliente             = $dados_ficha['FichaScorecard']['codigo_cliente'];
                $codigo_profissional        = $dados_ficha['ProfissionalLog']['codigo_profissional'];
                $codigo_profissional_tipo   = $dados_ficha['FichaScorecard']['codigo_profissional_tipo'];
                $dadosAlertaEmail[$codigo_cliente][] = array(
                    'codigo_cliente'    => $codigo_cliente,
                    'razao_social'      => $dados_ficha['Cliente']['razao_social'],
                    'codigo_documento'  => $dados_ficha['ProfissionalLog']['codigo_documento'],
                    'nome'              => $dados_ficha['ProfissionalLog']['nome']
                );
                $dadosExclusao[] = array( 
                    'codigo_cliente' => $codigo_cliente, 
                    'codigo_profissional' => $codigo_profissional,
                    'codigo_profissional_tipo' => $codigo_profissional_tipo
                );
            }
            if( $this->FichaScorecard->excluirVinculoProfissional( $dadosExclusao ) ){
                foreach ($dadosAlertaEmail as $codigo_cliente => $dados_email ) {
                    $this->StringView = new StringViewComponent();
                    $this->StringView->set(compact('dados_email'));
                    $content = $this->StringView->renderMail('email_scorecard_vinculo_excluido', 'default');
                    $alerta = array(
                        'Alerta' => array(
                            'codigo_cliente' => $codigo_cliente,
                            'descricao' => "EXCLUSAO DE VINCULO",
                            'descricao_email' => $content,
                            'codigo_alerta_tipo' => FichaScorecard::EXCLUSAO_VINCULO,
                            'model' =>  'FichaScorecard',
                            'foreign_key' => NULL,
                        ),
                    );
                    $this->Alerta->incluir( $alerta );
                }
                $this->BSession->setFlash('delete_success');
                $this->redirect(array('action' => 'excluir_vinculo'));
            } else {
                $this->BSession->setFlash('delete_error');
            }
        }
    }

    public function relatorio_vinculo(){
        $this->pageTitle = 'Relatório Vínculo';
        $tipos_profissionais = $this->Fichas->listProfissionalTipoAutorizado();
        if( !empty($this->authUsuario['Usuario']['codigo_cliente']) ) {
            $this->data['Cliente']['razao_social'] = $this->authUsuario['Usuario']['nome'];
        }        
        $this->data['FichaScorecard']['data_inicial'] = date('d/m/Y');
        $this->data['FichaScorecard']['data_final']    = date('d/m/Y');
        $this->set(compact('tipos_profissionais'));
    }
     
    public function listagem_relatorio_vinculo() {
        $this->layout = 'ajax';
        $filtros      = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if( !empty($filtros['codigo_cliente']) ) {
            if( isset($filtros['base_cnpj']) && $filtros['base_cnpj'] ){
                $this->Cliente  = ClassRegistry::init('Cliente');
                $codigo_cliente = $filtros['codigo_cliente'];
                $dados_cliente  = $this->Cliente->carregar( $codigo_cliente );
                $filtros['codigo_cliente'] = $this->Cliente->codigosMesmaBaseCNPJ( $codigo_cliente );
            }
        }
        if( !empty($this->authUsuario['Usuario']['codigo_cliente']) ) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $conditions   = $this->FichaScorecard->converteFiltroEmConditionParaFichas($filtros);
        array_push($conditions, array('FichaScorecard.ativo <>' => 3) );
        $this->paginate['FichaScorecard'] = array(
            'conditions' => $conditions,
            'consulta_fichas' => true,
        );
        $listar = $this->paginate('FichaScorecard');
        $this->set(compact('listar'));
    }

    public function excluir_vinculo(){
        $this->pageTitle = 'Excluir Vinculo Cliente x Profissional';        
        if( !empty($this->authUsuario['Usuario']['codigo_cliente']))
            $this->data['Cliente']['razao_social'] = $this->authUsuario['Usuario']['nome'];
        $this->data['FichaScorecard'] = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if( empty($this->data['FichaScorecard']['data_inicial']) || $this->data['FichaScorecard']['data_final'] ){
            $this->data['FichaScorecard']['data_inicial'] = date('d/m/Y');
            $this->data['FichaScorecard']['data_final']   = date('d/m/Y');            
        }
    }

    public function listagem_excluir_vinculo() {
        $this->layout = 'ajax';  
        $this->ProfissionalLog  =& ClassRegistry::init('ProfissionalLog');
        $this->ProfissionalTipo =& ClassRegistry::init('ProfissionalTipo');
        $this->Cliente      =& ClassRegistry::init('Cliente');        
        $filtros  = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if(isset($this->authUsuario['Usuario']['codigo_cliente']) && !empty($this->authUsuario['Usuario']['codigo_cliente']) ) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        $this->layout = 'ajax';
        $filtros['relatorio_vinculo'] = FALSE; 
        $conditions = $this->FichaScorecard->converteFiltroEmCondition($filtros);
        //Pegar vinculos com ficha ativa   
        $conditions['FichaScorecard.ativo'] = 1;
        $dbo   = $this->FichaScorecard->getDataSource();
        $query = $this->FichaScorecard->find('sql', array(
                'fields' => array('MAX(FichaScorecard.codigo) as cod'),
                'group'  => array('ProfissionalLogX.codigo_profissional', 'FichaScorecard.codigo_cliente'),
                'joins'  => array(
                    array(
                        "table"  => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
                        "alias"  => "ProfissionalLogx",
                        "type"    => "LEFT",
                        "conditions"=> array("ProfissionalLogx.codigo = FichaScorecard.codigo_profissional_log")
                    ),
                )
            )
        );    
       
        $this->paginate['FichaScorecard'] = array(
             'conditions'   => $conditions,
             'limit'  => 50,
             'order'  => 'ProfissionalLog.nome ASC',
             'fields' => array(  
                'FichaScorecard.codigo_cliente',
                'FichaScorecard.codigo_profissional_tipo',
                'MAX(CONVERT(VARCHAR(20), FichaScorecard.data_inclusao,  20)) as data_inclusao',
                'MAX(CONVERT(VARCHAR(20), FichaScorecard.data_validade,  20)) as data_validade',
                'MAX(FichaScorecard.codigo) as codigo_ficha',
                'FichaScorecard.codigo_status',
                'FichaScorecard.total_pontos',
                'Score.nivel',
                'Score.valor',
                'Cliente.razao_social',
                'FichaScorecard.ativo',
                'ProfissionalLog.nome',
                'ProfissionalLog.codigo_documento',
                'ProfissionalLog.codigo_profissional',
                'ProfissionalTipo.descricao'
                ),
            'joins' => array( 
                array(
                    "table"  => $this->ProfissionalLog->databaseTable.'.'.$this->ProfissionalLog->tableSchema.'.'.$this->ProfissionalLog->useTable,
                    "alias"  => "ProfissionalLog",
                    "type"    => "INNER",
                    "conditions"=> array("ProfissionalLog.codigo = FichaScorecard.codigo_profissional_log")
                ),
                array(
                    "table"  => $this->ProfissionalTipo->databaseTable.'.'.$this->ProfissionalTipo->tableSchema.'.'.$this->ProfissionalTipo->useTable,
                    "alias"  => "ProfissionalTipo",
                    "type"    => "INNER",
                    "conditions"=> array("ProfissionalTipo.codigo = FichaScorecard.codigo_profissional_tipo")
                ),
                array(
                    "table"  => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
                    "alias"  => "Cliente",
                    "type"    => "INNER",
                    "conditions"=> array("Cliente.codigo = FichaScorecard.codigo_cliente")
                ),
                array( 
                    "table"   => "(".$query.")",
                    "type"      => "INNER",
                    "alias"   => 'fichaMAX',
                    "conditions"    => 'fichaMAX.cod = FichaScorecard.codigo'
                ),        
                array(
                        "table"  => "dbTeleconsult.informacoes.parametros_score",
                        "alias"  => "Score",
                        "type"    => "LEFT",
                        "conditions"=> array("Score.codigo = FichaScorecard.codigo_parametro_score")
                    )
            ),
            'group' => array(  
                'FichaScorecard.codigo_cliente',
                'FichaScorecard.codigo_profissional_tipo',
                'FichaScorecard.codigo_status',
                'FichaScorecard.total_pontos',
                'Score.nivel',
                'Score.valor',
                'Cliente.razao_social',
                'FichaScorecard.ativo',
                'ProfissionalLog.nome',
                'ProfissionalLog.codigo_documento',
                'ProfissionalLog.codigo_profissional',
                'ProfissionalTipo.descricao'
            )
        );
        $listar = $this->paginate('FichaScorecard');
        $this->set(compact('listar'));
        
    }

    public function processar_renovacao() {
        $this->pageTitle = 'Processar Renovação';
        if($this->RequestHandler->isPost()) {
            Comum::execInBackground(ROOT . '/cake/console/cake -app '. ROOT . DS . 'app scorecard_renovacao processar');
        }
        return true;
    }


    public function consulta(){        
        $this->loadModel('EmbarcadorTransportador');
        $this->pageTitle = 'Consulta Profissional';
        $dados_pesquisa  = array();
        $this->Filtros->limpa_sessao('FichaScorecard');
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $this->Fichas->carregarCombos();
        $authUsuario= $this->authUsuario;
        $this->set(compact('authUsuario'));        
        if( isset( $authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']) && $authUsuario['Usuario']['codigo_cliente'] != '' ){
            $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
            $razao_social   = $authUsuario['Usuario']['nome'];
            $cnpj           = $authUsuario['Usuario']['codigo_documento'];            
        } elseif( !empty($filtros['codigo_cliente'] ) ) {
            $codigo_cliente = $filtros['codigo_cliente'];
            $usuarios = $this->Usuario->listaPorClienteList($codigo_cliente);   
            $this->set(compact('usuarios'));
            $usuario = $this->Usuario->carregar( $filtros['codigo_usuario'] );
            $this->data['Usuario'] = $usuario['Usuario'];
        } else {
            $codigo_cliente = NULL;
        }
        if( $codigo_cliente ) {
            $embarcador_transportador = $this->EmbarcadorTransportador->dadosPorCliente( $codigo_cliente );
            $embarcadores   = $embarcador_transportador['embarcadores'];
            $transportadores = $embarcador_transportador['transportadores'];
            $this->set(compact('transportadores', 'embarcadores'));
            $dados_cliente   = $this->Cliente->carregar( $codigo_cliente );
            $this->data['Cliente'] = $dados_cliente['Cliente'];
        }
        if ($this->RequestHandler->isPost()) {
            $classificacao  = $this->ParametroScore->find('all', array('order by valor desc'));
            $dados_consulta = $this->data['FichaScorecard'];
            $dados_profissional = $this->Profissional->buscaPorCPF( $dados_consulta['codigo_documento']);
            $dados_consulta['codigo_profissional'] = $dados_profissional['Profissional']['codigo'];
            $this->validaCamposConsulta();
            if( empty($this->FichaScorecard->validationErrors) ){
                $this->CargaTipo  = ClassRegistry::init('CargaTipo');
                $this->FichaStatusCriterio  = ClassRegistry::init('FichaStatusCriterio');
                $tipo_carga       = $this->CargaTipo->carregar( $filtros['codigo_carga_tipo'] );
                $retorno_consulta = $this->FichaScorecard->consultaStatusMotorista( $dados_consulta );
                $this->pageTitle  = '';
                $pneus_pontuacao  = array('Ouro' => 5,'Prata'=> 4, 'Bronze' => 3, 'Cobre'  => 2, 'Latao'  => 1);                
                $ficha_status_criterios = $this->FichaStatusCriterio->find('all', array('conditions' =>array('codigo_ficha'=>$retorno_consulta['ultima_ficha']['FichaScorecard']['codigo'] )));
                $campos_insuficientes   = $this->FichaStatusCriterio->retornaCamposInsuficientesFicha( $ficha_status_criterios );

                App::import('Component', array('StringView', 'Mailer.Scheduler'));
                $this->StringView = new StringViewComponent();
                $this->Scheduler  = new SchedulerComponent();
                $envio_email_scorecard = FichaScorecard::ENVIA_EMAIL_SCORECARD;
                $this->StringView->set(compact('retorno_consulta', 'dados_consulta', 'filtros', 'tipo_carga', 'ultima_ficha', 'classificacao', 'pneus_pontuacao', 'campos_insuficientes', 'dados_cliente', 'envio_email_scorecard'));
                $content = $this->StringView->renderMail('email_scorecard_consulta', 'default');
                $options = array(
                    'from' => 'portal@buonny.com.br',
                    'sent' => null,
                    'to'   => 'tid@ithealth.com.br',
                    'subject' => 'Retorno de Consulta',
                );
                $retorno = $this->Scheduler->schedule($content, $options) ? true: false;
                $this->set(compact('retorno_consulta', 'dados_consulta', 'filtros', 'tipo_carga', 'ultima_ficha', 'classificacao', 'pneus_pontuacao', 'campos_insuficientes'));
            }
        }

        $this->data['FichaScorecard'] = $filtros;
        $this->set(compact('codigo_cliente', 'razao_social', 'dados_cliente','cnpj'));
    }

    public function validaCamposConsulta(){
        
        if(!$this->data['FichaScorecard']['codigo_cliente'])
            $this->FichaScorecard->invalidate('codigo_cliente','Informe o cliente');        
        
        if(!$this->data['FichaScorecard']['codigo_documento'])
            $this->FichaScorecard->invalidate('codigo_documento','Informe o profissional');
        
        if( $this->data['FichaScorecard']['codigo_cliente'] && empty($this->data['FichaScorecard']['placa_veiculo']) && $this->FichaScorecard->verificaObrigatoriedadeDaPlaca($this->data['FichaScorecard']['codigo_cliente']) )
            $this->FichaScorecard->invalidate('placa_veiculo','Informe a placa');
        
        if (empty($this->data['FichaScorecard']['codigo_carga_tipo']))
            $this->FichaScorecard->invalidate('codigo_carga_tipo','Informe o tipo da Carga');        

        if (empty($this->data['FichaScorecard']['cidade_origem']))
            $this->FichaScorecard->invalidate('cidade_origem','Informe a Cidade Origem');        

        if (empty($this->data['FichaScorecard']['cidade_destino']))
            $this->FichaScorecard->invalidate('cidade_destino','Informe a Cidade Destino');        

        if( empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($this->data['FichaScorecard']['codigo_usuario']) )
            $this->FichaScorecard->invalidate('codigo_usuario','Informe o usuário');

    }
 
    public function log_faturamento() {
        $this->pageTitle = 'Operações de Faturamento';
        $this->TipoOperacao = ClassRegistry::init('TipoOperacao');
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if( empty($filtros) ){
            $this->data['FichaScorecard']['data_inicial'] = date('d/m/Y');
            $this->data['FichaScorecard']['data_final']   = date('d/m/Y');
        }else{
            $this->data['FichaScorecard'] = $filtros;
        }
        $this->Fichas->carregarCombos();
        $tipo_operacao= $this->TipoOperacao->find('list',array('fields'=>array('descricao'),'order'=>array('descricao')));
        $this->set(compact('tipo_operacao'));
    }

    public function log_faturamento_listagem(){
        
        $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');            
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');    
        
        if(!empty($filtros['codigo_transportador']))
            $filtros['codigo_cliente'] = $filtros['codigo_transportador'];
        
        if(!empty($filtros['codigo_embarcador']))
            $filtros['codigo_cliente'] = $filtros['codigo_embarcador'];  
        
        $conditions = $this->LogFaturamentoTeleconsult->logFaturamentoScorecard( $filtros, true );    
        if( !empty($this->params['named']) )
            $conditions['page'] = !empty($this->params['named']) ? $this->params['named'] : NULL;      
        
        $this->paginate['LogFaturamentoTeleconsult'] = $conditions;
        $log_faturamento = $this->paginate('LogFaturamentoTeleconsult');        
        $this->set(compact('log_faturamento'));
    }


    public function exclusao_log_faturamento( $codigo_log_faturamento ){
        $this->pageTitle = 'Exclusão Log de Faturamento';
        $this->set(compact('codigo_log_faturamento'));  
        $this->loadModel('LogFaturamentoTeleconsult');
        $this->loadModel('LogFaturamentoExcluido');  
        $this->data['LogFaturamentoExcluido']['codigo_usuario_exclusao'] = $this->authUsuario['Usuario']['codigo'];
        if($this->RequestHandler->isPost()){
            if( !empty($this->data['LogFaturamentoTeleconsult']['codigo']) && !empty($this->data['LogFaturamentoExcluido']['codigo_usuario_exclusao']) && !empty($this->data['LogFaturamentoExcluido']['motivo_exclusao']) ){
                $this->LogFaturamentoTeleconsult->excluiLogFaturamentoScorecard( $this->data );
                $this->redirect(array('action' => 'log_faturamento'));
            } else {                
                $this->LogFaturamentoExcluido->invalidate('motivo_exclusao','Informe o motivo da exclusão');
            }
        }
    }

    public function carrega_combos_relatorio_sla() {
        $this->TipoProfissional = ClassRegistry::init('TipoProfissional');
        $this->TipoRelacionamento = ClassRegistry::init('TipoRelacionamento');
        $operacoes = array();
        $operacoes['cadastro'] = 'CADASTRO';
        $operacoes['atualizacao'] = 'ATUALIZAÇÃO';
        $operacoes['renovacao_automatica'] = 'RENOVAÇÃO AUTOMÁTICA';
        $tipos_profissional = $this->TipoProfissional->find('list', array('order' => 'TipoProfissional.descricao'));
        $meses = array(
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        );
        $this->set('meses',$meses);
        $this->set(compact('tipos_profissional', 'operacoes'));
    }

 
    public function relatorio_sla() {
        $this->pageTitle = 'SLA Scorecard';
        $resposta ='';
        $ano = isset($this->data['FichaScorecard']['ano_referencia']) && !empty($this->data['FichaScorecard']['ano_referencia']) ? $this->data['FichaScorecard']['ano_referencia'] : date('Y');
        $mes = isset($this->data['FichaScorecard']['mes_referencia']) && !empty($this->data['FichaScorecard']['mes_referencia']) ? $this->data['FichaScorecard']['mes_referencia'] : date('m');
        $this->set('ano_referencia',$ano);
        $this->set('mes_referencia',$mes);
        if ($this->RequestHandler->isPost()) {
            $this->Cliente  = ClassRegistry::init('Cliente');
            $codigo_cliente = $this->data['FichaScorecard']['codigo_cliente'];
            $dados_cliente  = $this->Cliente->carregar( $codigo_cliente );
            if( $this->data['FichaScorecard']['base_cnpj'] ){
                $codigo_cliente  = $this->Cliente->codigosMesmaBaseCNPJ( $codigo_cliente );
            }
            $this->set('cliente',$dados_cliente);
            $resposta = new stdClass;
            $anoMesAtual = date('Ym');
            $anoMesRelatorio = $ano . $mes;
            $success = true;
            if ($anoMesRelatorio > $anoMesAtual) {
                $success = false;
            }
            if (!$codigo_cliente) {
                $success = false;
            }
            if ($success) {
                $conditions = array();
                if (!empty($this->data['FichaScorecard']['tipo_operacao'])) {
                    switch (strtolower($this->data['FichaScorecard']['tipo_operacao'])) {
                        case 'cadastro':
                            $tipoOperacao = explode(',', TipoOperacao::TIPO_OPERACAO_CADASTRO);
                            break;
                        case 'atualizacao':
                            $tipoOperacao = explode(',', TipoOperacao::TIPO_OPERACAO_ATUALIZACAO);
                            break;
                        case 'renovacao_automatica':
                            $tipoOperacao = explode(',', TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA);
                            break;
                        default:
                            break;
                    }
                    if (count($tipoOperacao) > 0) {
                        $conditions['LogFaturamento.codigo_tipo_operacao'] = $tipoOperacao;
                    }
                } else {
                    $tipoOperacao = implode(',', array(
                                TipoOperacao::TIPO_OPERACAO_CADASTRO,
                                TipoOperacao::TIPO_OPERACAO_ATUALIZACAO,
                                TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA
                            ));

                    $conditions['LogFaturamento.codigo_tipo_operacao'] = explode(',', $tipoOperacao);
                }
                if (isset($this->data['FichaScorecard']['profissional']) && !empty($this->data['FichaScorecard']['profissional'])) {
                    $conditions['FichaScorecard.codigo_profissional_tipo'] = $this->data['FichaScorecard']['profissional'];
                }
                ini_set('max_execution_time', 0);
                set_time_limit(0);
                $tempos = array();
                $tempos[] = microtime(true);
                $fichas = $this->FichaScorecard->listaFichasComTempo($codigo_cliente, $mes, $ano, $conditions);
                $tempos[] = microtime(true);
                if (is_array($fichas)) {
                    $success = true;
                }
                $calculado = $this->FichaScorecard->calcularPorcentagem($fichas);
                $tempos[] = microtime(true);

                $periodoInicial = mktime(0, 0, 0, $mes, 1, $ano) * 1000;
                if ($anoMesRelatorio < $anoMesAtual) {
                    $periodoFinal = mktime(23, 59, 59, $mes + 1, 0, $ano) * 1000;
                } else {
                    $periodoFinal = time() * 1000;
                }
                $FichaScorecardStatus = &ClassRegistry::init('FichaScorecardStatus');
                $analise = array();
                foreach($fichas as $ficha){
                    if( $ficha['FichaScorecard']['codigo_status'] == FichaScorecardStatus::FINALIZADA ){                        
                        if( FichaScorecard::ENVIA_EMAIL_SCORECARD ){
                            $ficha['ProfissionalStatus']['descricao'] = $ficha['ProfissionalStatus']['nivel'];
                        }else{
                            $ParametroScore = &ClassRegistry::init('ParametroScore');
                            $ficha['ProfissionalStatus']['descricao'] = $ParametroScore->deparaStatusTeleconsult($ficha['ProfissionalStatus']['codigo']);
                        }
                    } else {
                        $ficha['ProfissionalStatus']['descricao'] = FichaScorecardStatus::descricao( FichaScorecardStatus::EM_PESQUISA );
                    }
                    $analise[] = $ficha;
                }
                $resposta->geracao = time() * 1000;
                $resposta->periodo = array($periodoInicial, $periodoFinal);
                $resposta->fichas = $analise;
                $resposta->tempos = $calculado;
                $resposta->bench = $tempos;
                $resposta->dados_cliente = $dados_cliente['Cliente'];
            }
            $resposta->success = $success;
        }
        $this->carrega_combos_relatorio_sla();
        $this->Fichas->carregarCombos();
        $this->set('filtros',$this->data);
        $this->set('resposta',$resposta);
    }

    public function consulta_fichas(){     
        $this->loadModel('ProfissionalTipo');
        $status = ClassRegistry::init('FichaScorecardStatus')->descricoes;
        $this->pageTitle = 'Consulta Fichas Scorecard';
        $this->data['FichaScorecard']['data_inicial'] = date('d/m/Y');
        $this->data['FichaScorecard']['data_final']   = date('d/m/Y');
        $filtros['FichaScorecard'] = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $this->data = $filtros;
        $tipos_profissionais = $this->ProfissionalTipo->find('list');      
        $this->set(compact('tipos_profissionais','status'));
    }
    
    public function listagem_fichas_scorecard(){
        $FichaScorecardStatus = &ClassRegistry::init('FichaScorecardStatus');
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if( isset($filtros['codigo_tipo_profissional']) ){
            unset($filtros['codigo_tipo_profissional']);
        }
        $conditions = $this->FichaScorecard->converteFiltroEmConditionParaFichas($filtros);
        $this->paginate['FichaScorecard'] = array(
            'conditions' => $conditions,
            'consulta_fichas' => true,
        );
        $status = ClassRegistry::init('FichaScorecardStatus')->descricoes;
        $fichasScorecard = $this->paginate('FichaScorecard');
        $this->set(compact('fichasScorecard','count', 'status'));
    }

   public function listagem_log_consultas() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $this->FichaScorecardStatus      = ClassRegistry::init('FichaScorecardStatus');
        $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');    
        $conditions = $this->LogFaturamentoTeleconsult->converteFiltroEmConditionLogConsultas( $filtros );
        $conditions['LogFaturamentoTeleconsult.codigo_produto'] = 134;        
        $fields = array(
            'LogFaturamentoTeleconsult.codigo', 'Cliente.codigo', 'Cliente.nome_fantasia', 'Usuario.apelido', 
            'TipoOperacao.descricao', 'Usuario.nome','ProfissionalTipo.descricao', 'Profissional.codigo_documento', 
            'LogFaturamentoTeleconsult.data_inclusao', 'LogFaturamentoTeleconsult.placa', 'LogFaturamentoTeleconsult.placa_carreta', 
            'LogFaturamentoTeleconsult.placa_veiculo_bitrem','EnderecoCidadeOrigem.descricao', 'EnderecoCidadeDestino.descricao', 
            'CargaTipo.descricao', 'TipoOperacao.codigo','EnderecoEstadoDestino.descricao', 'EnderecoEstadoOrigem.descricao'
        );
        $joins = array(
                array(
                    "table"  => $this->FichaScorecard->databaseTable.'.'.$this->FichaScorecard->tableSchema.'.'.$this->FichaScorecard->useTable,
                    "alias"  => "FichaScorecard",
                    "type"    => "LEFT",
                    "conditions"=> array("LogFaturamentoTeleconsult.codigo_ficha_scorecard = FichaScorecard.codigo")
                ),  
                array(
                    "table"  => $this->ProfissionalTipo->databaseTable.'.'.$this->ProfissionalTipo->tableSchema.'.'.$this->ProfissionalTipo->useTable,
                    "alias"  => "ProfissionalTipo",
                    "type"    => "LEFT",
                    "conditions"=> array("ProfissionalTipo.codigo = FichaScorecard.codigo_profissional_tipo")
                ),  
                array(
                    "table"  => $this->Profissional->databaseTable.'.'.$this->Profissional->tableSchema.'.'.$this->Profissional->useTable,
                    "alias"  => "Profissional",
                    "type"    => "LEFT",
                    "conditions"=> array("Profissional.codigo = LogFaturamentoTeleconsult.codigo_profissional")
                ),  
                array(
                    "table"  => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
                    "alias"  => "Cliente",
                    "type"    => "LEFT",
                    "conditions"=> array("Cliente.codigo = LogFaturamentoTeleconsult.codigo_cliente")
                ),
                array(
                    "table"  => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
                    "alias"  => "Usuario",
                    "type"    => "LEFT",
                    "conditions"=> array("Usuario.codigo = LogFaturamentoTeleconsult.codigo_usuario_inclusao")
                ),
                array(
                    "table"  => $this->TipoOperacao->databaseTable.'.'.$this->TipoOperacao->tableSchema.'.'.$this->TipoOperacao->useTable,
                    "alias"  => "TipoOperacao",
                    "type"    => "LEFT",
                    "conditions"=> array("TipoOperacao.codigo = LogFaturamentoTeleconsult.codigo_tipo_operacao")
                ),
                array(
                    "table"  => $this->EnderecoCidade->databaseTable.'.'.$this->EnderecoCidade->tableSchema.'.'.$this->EnderecoCidade->useTable,
                    "alias"  => "EnderecoCidadeOrigem",
                    "type"    => "LEFT",
                    "conditions"=> array("EnderecoCidadeOrigem.codigo = LogFaturamentoTeleconsult.codigo_endereco_cidade_origem")
                ),
                array(
                    "table"  => $this->EnderecoEstado->databaseTable.'.'.$this->EnderecoEstado->tableSchema.'.'.$this->EnderecoEstado->useTable,
                    "alias"  => "EnderecoEstadoOrigem",
                    "type"    => "LEFT",
                    "conditions"=> array("EnderecoEstadoOrigem.codigo = EnderecoCidadeOrigem.codigo_endereco_estado")
                ),
                array(
                    "table"  => $this->EnderecoCidade->databaseTable.'.'.$this->EnderecoCidade->tableSchema.'.'.$this->EnderecoCidade->useTable,
                    "alias"  => "EnderecoCidadeDestino",
                    "type"    => "LEFT",
                    "conditions"=> array("EnderecoCidadeDestino.codigo = LogFaturamentoTeleconsult.codigo_endereco_cidade_destino")
                ),
                array(
                    "table"  => $this->EnderecoEstado->databaseTable.'.'.$this->EnderecoEstado->tableSchema.'.'.$this->EnderecoEstado->useTable,
                    "alias"  => "EnderecoEstadoDestino",
                    "type"    => "LEFT",
                    "conditions"=> array("EnderecoEstadoDestino.codigo = EnderecoCidadeDestino.codigo_endereco_estado")
                ),
                array(
                    "table"  => $this->CargaTipo->databaseTable.'.'.$this->CargaTipo->tableSchema.'.'.$this->CargaTipo->useTable,
                    "alias"  => "CargaTipo",
                    "type"    => "LEFT",
                    "conditions"=> array("CargaTipo.codigo = LogFaturamentoTeleconsult.codigo_carga_tipo")
                ),
        );
        $this->paginate['LogFaturamentoTeleconsult'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'joins' => $joins,
            'order' => 'LogFaturamentoTeleconsult.data_inclusao'
        );
        $status = ClassRegistry::init('FichaScorecardStatus')->descricoes;
        $fichasScorecard = $this->paginate('LogFaturamentoTeleconsult');
        $this->set(compact('fichasScorecard','count', 'status'));
        
    }

    public function codigo_ultima_ficha_profissional( $cpf_profissional ){ //+++
        $this->FichaScorecard->bindModel(array('belongsTo' => array('ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log'))));
        $ficha = $this->FichaScorecard->find('first', 
        array(
            'fields'     => array('FichaScorecard.codigo'),
            'conditions' => array('ProfissionalLog.codigo_documento' => $cpf_profissional),
            'order'      => 'FichaScorecard.codigo DESC'
        ));
        echo json_encode($ficha['FichaScorecard']['codigo']);exit;
    }

    public function estatisticas_relatorio_gerencial( $codigo_usuario=NULL, $dia = NULL ){
        $this->layout = "new_window";
        $this->pageTitle = 'Estatística';
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $filtros['dia'] = $dia;
        $mes    = str_pad($filtros['tipo_mes'], 2, '0', STR_PAD_LEFT);
        $meses  = comum::listMeses();
        if( $dia ){
            $dia   = str_pad($filtros['dia'], 2, '0', STR_PAD_LEFT);
            $data  = $dia.'/'.$mes.'/'.$filtros['ano'];
        } else {
            $data  = $meses[$filtros['tipo_mes']].'/'.$filtros['ano'];
        }
        $filtros['codigo_usuario'] = $codigo_usuario;
        if( $codigo_usuario ){
            $dados = $this->FichaScorecard->detalhamento_relatorio_gerencial( $filtros );
        }else{
            $dados = $this->FichaScorecard->detalhamento_relatorio_gerencial_total( $filtros );            
        }
        $this->set(compact('dados', 'data', 'codigo_usuario'));
    }

    public function relatorios_gerenciais_atualizacoes_por_hora(){
        $this->pageTitle = 'Atualizações de Cadastros por Hora';
        $this->Filtros->limpa_sessao('FichaScorecard');
        $this->data['FichaScorecard']['data']           = date("d/m/Y");
        $this->data['FichaScorecard']['hora_inicio']    = date("h:00");
        $this->data['FichaScorecard']['hora_termino']   = '23:59';        
        $this->data['FichaScorecard']['tipo_busca'] = 3;
        $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
    }
    public function relatorios_gerenciais_cadastros(){
        $this->pageTitle = 'Cadastros e Atualizações';
        $this->Filtros->limpa_sessao('FichaScorecard');
        $this->data['FichaScorecard']['ano'] = date('Y');
        $this->data['FichaScorecard']['tipo_mes'] = date('m');
        $this->data['FichaScorecard']['tipo_busca'] = 2;
        $anos  = Comum::listAnos( date("Y")-2 );
        $meses = Comum::listMeses();
        $tipo_profissional = $this->Fichas->listProfissionalTipoAutorizado();
        $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $this->set(compact('anos', 'meses', 'tipo_profissional'));
    }    
    public function relatorios_gerenciais_pesquisas(){
        $this->Filtros->limpa_sessao('FichaScorecard');
        $this->pageTitle = 'Pesquisas realizadas';
        $this->data['FichaScorecard']['tipo_busca'] = 1;
        $this->data['FichaScorecard']['ano'] = date('Y');
        $this->data['FichaScorecard']['tipo_mes'] = date('m');
        $this->data['FichaScorecard']['tipo_busca'] = 1;
        $anos  = Comum::listAnos( date("Y")-2 );
        $meses = Comum::listMeses();
        $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $tipo_profissional = $this->Fichas->listProfissionalTipoAutorizado();
        $this->set(compact('anos', 'meses', 'tipo_profissional'));
    }    

    public function relatorios_gerenciais_scorecard(){
        $filtros            = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $tipo_busca         = $filtros['tipo_busca'];
        $data               = (!empty($filtros['data'])             ? preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3-$2-$1$4", $filtros['data']) : NULL);
        $hora_inicio        = (!empty($filtros['hora_inicio'])      ? $filtros['hora_inicio']       : NULL);
        $hora_termino       = (!empty($filtros['hora_termino'])     ? $filtros['hora_termino']      : NULL);
        $tipo_origem        = (isset($filtros['tipo_origem'])       ? $filtros['tipo_origem']       : NULL);
        $tipo_profissional  = (isset($filtros['tipo_profissional']) ? $filtros['tipo_profissional'] : NULL);
        $mes                = (isset($filtros['tipo_mes'])          ? $filtros['tipo_mes']          : NULL);
        $ano                = (isset($filtros['ano'])               ? $filtros['ano']               : NULL);
        $apelido            = (isset($filtros['usuario'])           ? $filtros['usuario']           : NULL);
        switch ($tipo_busca) {
            case 1:
                $dados = $this->FichaScorecard->relatorio_pesquisa_atualizacoes( $mes, $ano, $apelido, $tipo_profissional ); 
                break;
            case 2:
                $dados = $this->FichaScorecard->relatorio_pesquisa_cadastro( $mes, $ano, $apelido, $tipo_profissional, $tipo_origem );
                break;            
            case 3:                
                $dados = $this->FichaScorecard->relatorio_pesquisa_cadastro_tipo_horas( $data, $hora_inicio, $hora_termino, $tipo_origem );
                break;            
        }
        $this->set(compact('dados','tipo_busca','filtro_mes','filtro_ano' ,'anos', 'meses', 'head_title', 'eixo_x', 'series'));
    }

    //visualizar liberacao_ficha
    function visualizar_ficha_em_pesquisa($codigo_uperfil){
        $tem_acesso_pesquisa_ficha  = 1;
        $data = $this->Uperfil->listaPermissoesFichaScorecard($codigo_uperfil);
        
        //tem acesso
        return $data;
    }

    function erro_veiculo ($valido) {
        if ($this->data['FichaScorecardVeiculo']['possui_veiculo']=='S'){
            
            if ($this->data['FichaScorecardVeiculo']['0']['Veiculo']['placa']==''){
                                 $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->VeiculoBiTrem->invalidate('placa','Placa obrigatória');
                                 $valido &= 0;
                              }
            if($this->data['FichaScorecardVeiculo']['0']['EnderecoCidade']['cidade_emplacamento']==''){
                                 $this->FichaScorecardVeiculo->validationErrors[0]['EnderecoCidade'] = $this->VeiculoBiTrem->invalidate('cidade_emplacamento','Cidade obrigatória');
                                 $valido &= 0;
                              }
            if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['chassi'] ==''){
                                $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->VeiculoBiTrem->invalidate('chassi','Chassi obrigatório');
                                $valido &= 0;
                              }
            if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['renavam'] ==''){
                                $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->VeiculoBiTrem->invalidate('renavam','Renavam obrigatório');
                                $valido &= 0;
                              }
            

            //if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['codigo_veiculo_tecnologia'] ==''){
                     //     $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->Veiculo->invalidate('codigo_veiculo_tecnologia','Tecnologia obrigatória');
                       //       $valido &= 0;
                       //      }


            if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['codigo_veiculo_cor'] ==''){
                                $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_cor','Cor obrigatória');
                                $valido &= 0;
                              }
            if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['ano_fabricacao'] ==''){
                                $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->VeiculoBiTrem->invalidate('ano_fabricacao','Ano Fabricação obrigatório');
                                $valido &= 0;
                              }
            if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['ano'] ==''){
                                $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->VeiculoBiTrem->invalidate('ano','Ano obrigatório');
                                $valido &= 0;
                              }  
            if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['codigo_veiculo_fabricante'] ==''){
                                $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_fabricante','Fabricante obrigatório');
                                $valido &= 0;
                              }   
            if($this->data['FichaScorecardVeiculo']['0']['Veiculo']['codigo_veiculo_modelo'] ==''){
                                $this->FichaScorecardVeiculo->validationErrors[0]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_modelo','Modelo obrigatório');
                                $valido &= 0;
                              }  
            $tem_veiculo = 'S';
            //Proprietário
            
            $valido &= $this->Proprietario->validarDados($this->data['FichaScorecardVeiculo'][0]['Proprietario'], $tem_veiculo);
            
            $valido &= $this->ProprietarioEndereco->validarDados($this->data['FichaScorecardVeiculo'][0]['ProprietarioEndereco'], $tem_veiculo);
            
            //$valido &= $this->ProprietarioContato->validarContatos($this->data['FichaScorecardVeiculo'][0]['ProprietarioContato']);
            //debug($valido);
         }

            return $valido;
    }

    function erro_bitrem($valido){

          if ($this->data['FichaScorecardVeiculo']['2']['Veiculo']['veiculo_sn']=='S'){
                
                if ($this->data['FichaScorecardVeiculo']['2']['Veiculo']['placa']==''){
                                 $this->FichaScorecardVeiculo->validationErrors['2']['Veiculo'] = $this->VeiculoBiTrem->invalidate('placa','Placa obrigatória');
                                 $valido &= 0;
                              }
                if($this->data['FichaScorecardVeiculo']['2']['EnderecoCidade']['cidade_emplacamento']==''){
                                     $this->FichaScorecardVeiculo->validationErrors[2]['EnderecoCidade'] = $this->VeiculoBiTrem->invalidate('cidade_emplacamento','Cidade obrigatória');
                                     $valido &= 0;
                                  }
                if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['chassi'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('chassi','Chassi obrigatório');
                                    $valido &= 0;
                                  }
                if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['renavam'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('renavam','Renavam obrigatório');
                                    $valido &= 0;
                                  }
                //if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['codigo_veiculo_tecnologia'] ==''){
                                    //$this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->Veiculo->invalidate('codigo_veiculo_tecnologia','Tecnologia obrigatória');
                                  //  $valido &= 0;
                                 // }
                if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['codigo_veiculo_cor'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_cor','Cor obrigatória');
                                    $valido &= 0;
                                  }
                if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['ano_fabricacao'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('ano_fabricacao','Ano Fabricação obrigatório');
                                    $valido &= 0;
                                  }
                if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['ano'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('ano','Ano obrigatório');
                                    $valido &= 0;
                                  }  
                if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['codigo_veiculo_fabricante'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_fabricante','Fabricante obrigatório');
                                    $valido &= 0;
                                  }   
                if($this->data['FichaScorecardVeiculo']['2']['Veiculo']['codigo_veiculo_modelo'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[2]['Veiculo'] = $this->VeiculoBiTrem->invalidate('codigo_veiculo_modelo','Modelo obrigatório');
                                    $valido &= 0;
                                  }   
                $tem_veiculo = 'S';
                //Proprietário
                $valido &= $this->Proprietario->validarDados($this->data['FichaScorecardVeiculo'][2]['Proprietario'], $tem_veiculo);
                
                $valido &= $this->ProprietarioEndereco->validarDados($this->data['FichaScorecardVeiculo'][2]['ProprietarioEndereco'], $tem_veiculo);
                
                //$valido &= $this->ProprietarioContato->validarContatos($this->data['FichaScorecardVeiculo'][2]['ProprietarioContato']);
                                  


            } 
            return $valido;
    }


    function erro_carreta ($valido) {
       if ($this->data['FichaScorecardVeiculo']['1']['Veiculo']['veiculo_sn']=='S'){
               
                if ($this->data['FichaScorecardVeiculo']['1']['Veiculo']['placa']==''){
                                 $this->FichaScorecardVeiculo->validationErrors['1']['Veiculo'] = $this->VeiculoCarreta->invalidate('placa','Placa obrigatória');
                                 $valido &= 0;
                              }
                if($this->data['FichaScorecardVeiculo']['1']['EnderecoCidade']['cidade_emplacamento']==''){
                                     $this->FichaScorecardVeiculo->validationErrors[1]['EnderecoCidade'] = $this->VeiculoCarreta->invalidate('cidade_emplacamento','Cidade obrigatória');
                                     $valido &= 0;
                                  }
                if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['chassi'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('chassi','Chassi obrigatório');
                                    $valido &= 0;
                                  }
                if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['renavam'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('renavam','Renavam obrigatório');
                                    $valido &= 0;
                                  }
                //if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['codigo_veiculo_tecnologia'] ==''){
                //          $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->Veiculo->invalidate('codigo_veiculo_tecnologia','Tecnologia obrigatória');
                //          $valido &= 0;
                 //     }
                if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['codigo_veiculo_cor'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('codigo_veiculo_cor','Cor obrigatória');
                                    $valido &= 0;
                                  }
                if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['ano_fabricacao'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('ano_fabricacao','Ano Fabricação obrigatório');
                                    $valido &= 0;
                                  }
                if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['ano'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('ano','Ano obrigatório');
                                    $valido &= 0;
                                  }  
                if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['codigo_veiculo_fabricante'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('codigo_veiculo_fabricante','Fabricante obrigatório');
                                    $valido &= 0;
                                  }   
                if($this->data['FichaScorecardVeiculo']['1']['Veiculo']['codigo_veiculo_modelo'] ==''){
                                    $this->FichaScorecardVeiculo->validationErrors[1]['Veiculo'] = $this->VeiculoCarreta->invalidate('codigo_veiculo_modelo','Modelo obrigatório');
                                    $valido &= 0;
                                  }   
                $tem_veiculo = 'S';
                //Proprietário
                $valido &= $this->Proprietario->validarDados($this->data['FichaScorecardVeiculo'][1]['Proprietario'], $tem_veiculo);
                
                $valido &= $this->ProprietarioEndereco->validarDados($this->data['FichaScorecardVeiculo'][1]['ProprietarioEndereco'], $tem_veiculo);
                
                //$valido &= $this->ProprietarioContato->validarContatos($this->data['FichaScorecardVeiculo'][1]['ProprietarioContato']);
                
                
                return $valido;
        }
    }

    function historico_ocorrencia( $codigo_ficha ){
        $this->loadModel('ProfissionalNegativacao');
        $this->loadModel('VeiculoOcorrencia');
        $this->loadModel('ProfNegativacaoCliente');
        $this->Fichas->carregarDadosFicha( $codigo_ficha );
        $ocorrencia_profissional = $this->ProfissionalNegativacao->historicoOcorrencia( array('Profissional.codigo' => $this->data['Profissional']['codigo_profissional'] ) );
        $ocorrencia_profissional_cliente = $this->ProfNegativacaoCliente->historicoOcorrenciaPorCliente($this->data['Profissional']['codigo_profissional']);
        $ocorrencia_veiculo = array();
        $ocorrencia_proprietario = array();        
        if( isset($this->data['FichaScorecardVeiculo']) ){
            $veiculos = $this->data['FichaScorecardVeiculo'];
            unset($veiculos['possui_veiculo']);
            foreach( $veiculos as $key => $veiculo ){
                if(!empty($veiculo['Veiculo']['codigo_veiculo'])){
                    $ocorrencia_veiculo[$key] = $this->VeiculoOcorrencia->historicoOcorrencia(array('VeiculoOcorrencia.codigo_veiculo'=> $veiculo['Veiculo']['codigo_veiculo']));
                    $ocorrencia_proprietario[$key] = $this->ProfissionalNegativacao->historicoOcorrencia(array('Profissional.codigo_documento'=> $veiculo['Proprietario']['codigo_documento']));
                }
            }
        }
        $this->set(compact('ocorrencia_profissional', 'ocorrencia_veiculo', 'ocorrencia_proprietario', 'ocorrencia_profissional_cliente'));
    }

    function historico_socioeco($codigo_prof,$codigo_documento,$codigo_propr_vei=null,$cpf_propr_vei=null,
                                $codigo_prop_car=null,$cpf_propr_car=null,$codigo_prop_bi=null,$cpf_propr_bi=null){
          
           $this->loadModel('ProfissionalTelecheque');
           $this->loadModel('ProprietarioTelecheque');
           $this->loadModel('ProfissionalSerasa');
           $this->loadModel('ProprietarioSerasa');
           

           $this->data['Profissional']['codigo_documento']  =   $codigo_documento;
           $consulta_serasa_socio['Profissional']['codigo'] =   $codigo_prof;
           $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] =$codigo_propr_vei;
           $this->data['FichaScorecardVeiculo'][0]['Proprietario']['codigo_documento'] =$cpf_propr_vei;
           $consulta_serasa_socio['Proprietario']['carreta']['codigo'] = (!isset($codigo_propr_car))?'':$codigo_propr_car;
           $this->data['FichaScorecardVeiculo'][1]['Proprietario']['codigo_documento'] = $cpf_propr_car;
           $consulta_serasa_socio['Proprietario']['bitrem']['codigo'] = (!isset($codigo_prop_bi))?'':$codigo_prop_bi;
           $this->data['FichaScorecardVeiculo'][2]['Proprietario']['codigo_documento'] = $cpf_propr_bi;
           $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] = $codigo_propr_vei;
           $consulta_serasa_socio['Proprietario']['carreta']['codigo']  = $codigo_prop_car;
           $consulta_serasa_socio['Proprietario']['bitrem']['codigo']   = $codigo_prop_bi;
           
           // Telecheque Motorista
           $consulta_serasa_socio['consulta']['Telecheque']['Profissinal'] = $this->ProfissionalTelecheque->find('all',
                                                                                    array(  'conditions' => array( 'codigo_profissional' =>  $consulta_serasa_socio['Profissional']['codigo'] ),
                                                                                            'order'   => array('' =>'codigo desc')
                                                                                    )
                                                                                ); 

           // Telecheque Proprietario Veiculo
            $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Veiculo'] = $this->ProprietarioTelecheque->find('all',
                    array(  'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] ),
                            'order'   => array( '' =>'codigo desc')
                    )
                );
           
            // Telecheque Proprietario Carreta
                $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Carreta'] = $this->ProprietarioTelecheque->find('all',
                    array(  'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['carreta']['codigo'] ),
                            'order'   => array( '' =>'codigo desc')
                    )
                );

           // Telecheque Proprietario Bitrem
              if ($consulta_serasa_socio['Proprietario']['bitrem']['codigo'] !=''){ 
                    $consulta_serasa_socio['consulta']['Telecheque']['Proprietario']['Bitrem'] = $this->ProprietarioTelecheque->find('all',
                        array(  'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['bitrem']['codigo'] ),
                                'order'   => array( '' =>'codigo desc')
                        )
                    );
              }  
              
             //Profissional Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Profissional'] = $this->ProfissionalSerasa->find('all',
                    array(  'conditions' => array( 'codigo_profissional' => $consulta_serasa_socio['Profissional']['codigo'] ),
                            'order' => array(''=>'ProfissionalSerasa.codigo desc'))
                );
            
                //Proprietario Veiculo Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Veiculo'] = $this->ProprietarioSerasa->find('all',
                               array(   'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['veiculo']['codigo'] ),
                    'order' => array(''=>'ProprietarioSerasa.codigo desc'))
                );
            
            
             //Proprietario Carreta Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Carreta'] = $this->ProprietarioSerasa->find('all',
                               array(   'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['carreta']['codigo'] ),
                    'order' => array(''=>'ProprietarioSerasa.codigo desc'))
                );
                
             //Proprietario Bitrem Serasa Historico
                $consulta_serasa_socio['consulta']['Serasa']['Proprietario']['Bitrem'] = $this->ProprietarioSerasa->find('all',
                               array(   'conditions' => array( 'codigo_proprietario' => $consulta_serasa_socio['Proprietario']['bitrem']['codigo'] ),
                    'order' => array(''=>'ProprietarioSerasa.codigo desc'))
                );    
        
            $this->set(compact('consulta_serasa_socio'));
    }  


    function historico_rma( $codigo_documento, $codigo_cliente = NULL, $codigo_embarcador = null, $codigo_transportador = null) {
        $this->TOrmaOcorrenciaRma = ClassRegistry::init('TOrmaOcorrenciaRma');
        $filtros = array( 'pfis_cpf' => $codigo_documento );
        $conditions = $this->TOrmaOcorrenciaRma->converteFiltrosEmConditions( $filtros );
        $rmas = $this->TOrmaOcorrenciaRma->rmaPorProfissional( $conditions );
        $this->set(compact('rmas'));
    }
    
    function emailConsultaProfissional($codigo_ficha,$codigo_faturamento) {//
        try{ 
             //Reenviar Email Consulta
            $this->LoadModel("VeiculoLog");
            App::import('Component', array('StringView', 'Mailer.Scheduler'));
            $this->StringView = new StringViewComponent();
            $this->Scheduler  = new SchedulerComponent();
            $dados_email = $this->FichaScorecard->buscarDadosEmailResultado($codigo_ficha);
            $dados_email[0]['FichaScorecard']['nivel'] = $dados_email[0]['ParametroScore']['nivel'];
            $dados_email[0]['FichaScorecard']['valor'] = $dados_email[0]['ParametroScore']['valor'];
            $dados_email[0]['Profissional']['nome'] = $dados_email[0]['ProfissionalLog']['nome'];
            
            foreach ($dados_email[0]['FichaScorecardVeiculo'] as $dados_vei) {
              //Veiculo
              if ($dados_vei['tipo']==0){
                $dados_email[0]['Veiculo']['placa'] = strtoupper($this->VeiculoLog->obterPlacaVeiculoPeloCodigoVeiculoLog($dados_vei['codigo_veiculo_log']));
                 
              }
              //Carreta
              if ($dados_vei['tipo']==1){
                $dados_email[0]['Carreta']['placa'] = strtoupper($this->VeiculoLog->obterPlacaVeiculoPeloCodigoVeiculoLog($dados_vei['codigo_veiculo_log']));

              }
              //Bitrem   
              if ($dados_vei['tipo']==2){
                $dados_email[0]['Bitrem']['placa'] = strtoupper($this->VeiculoLog->obterPlacaVeiculoPeloCodigoVeiculoLog($dados_vei['codigo_veiculo_log']));

              } 
           
            }
            $dados = $dados_email[0];
            $this->LoadModel('LogFaturamentoTeleconsult');
            $faturamento = $this->LogFaturamentoTeleconsult->log_faturamento_por_codigo($codigo_faturamento);
            $tipo_operacao  = ClassRegistry::init('TipoOperacao');      
            
            $status   = $tipo_operacao->find('first',array('conditions'=>array('TipoOperacao.codigo'=>$faturamento[0]['LogFaturamentoTeleconsult']['codigo_tipo_operacao'])));
            $this->data['observacao'] = $status['TipoOperacao']['mensagem'] . '. ' .$status['TipoOperacao']['observacao'];
            $codigo_log_faturamento = $codigo_faturamento;
            $this->StringView->set('codigo_log_faturamento',isset($codigo_log_faturamento));
            $this->StringView->set(compact('dados','status'));
            $content = $this->StringView->renderMail('email_scorecard_consulta', 'default');
            $options = array(
                'from' => 'portal@buonny.com.br',
                'sent' => null,
                'to'   => 'danilo.campoi@buonny.com.br',
                'subject' => 'Reenvio Resultado Consulta - Ficha ScoreCard', 
            );
            $retorno = $this->Scheduler->schedule($content, $options) ? true: false;
            $this->redirect(array('action' => 'index_fichas_finalizadas'));  
         }catch(Exception $e){
            echo "Exceção pega Controller : Scorecard (Função : emailConsultaProfissional) ->",  $e->getMessage(), "\n";
        }

    } 

    function indexLogRenovacao(){
        $this->pageTitle = 'Log Renovação Scorecard';
        $lista_tipo_profissional = $this->Fichas->listProfissionalTipoAutorizado();
        // $filtros = $this->Filtros->controla_sessao($this->data,'RenovacaoAutomatica');
        // $this->data['RenovacaoAutomatica'] = $filtros;
        $this->data['LogRenovacao']['data_inicial'] = date("d/m/Y");
        $this->data['LogRenovacao']['data_final'] = date("d/m/Y");
        $this->set(compact('lista_tipo_profissional'));
   } 

    function listagemLogRenovacao(){
      try{
            $this->layout = 'ajax';
            $filtros = $this->Filtros->controla_sessao($this->data, 'LogRenovacao');
            $this->LoadModel("RenovacaoAutomatica");
            $conditions = $this->RenovacaoAutomatica->converteFiltroEmCondition($filtros);
            $listagemLogRenovacao = $this->RenovacaoAutomatica->listagem($conditions);
            $this->paginate['RenovacaoAutomatica'] = $listagemLogRenovacao;
            $this->paginate['RenovacaoAutomatica']['limit'] = 50;
            $dadosLogRenovacao = $this->paginate('RenovacaoAutomatica');
            $this->set(compact('dadosLogRenovacao'));         
        } catch(Exception $e) {
            echo "Exceção pega Controller : Scorecard (Função : listagemLogRenovacao) ->",  $e->getMessage(), "\n";
        }  
    }

    function logFaturamentoExcel(){
        $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');      
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');        
        if(!empty($filtros['codigo_transportador']))
            $filtros['codigo_cliente'] = $filtros['codigo_transportador'];
        if(!empty($filtros['codigo_embarcador']))
            $filtros['codigo_cliente'] = $filtros['codigo_embarcador']; 
        $conditions = $this->LogFaturamentoTeleconsult->logFaturamentoScorecard( $filtros, true );
        $log_faturamento = $this->LogFaturamentoTeleconsult->find('all',array('order'=>$conditions['order'],'conditions' => $conditions['conditions'], 'fields'=>$conditions['fields'],'joins'=>$conditions['joins']));
        header(sprintf('Content-Disposition: attachment; filename="%s";', basename('log_operacoes.csv'))); 
        header("Content-Type: application/vnd.ms-excel");
        header('Pragma: no-cache');     
        echo utf8_decode("RazãoSocial;CadastradoPor;Operação;Profissional;CPF;Categoria;Data;NúmeroConsulta;Placa;Carreta;BiTrem;Carga;Origem;Destino\n"); 
        //debug($log_faturamento);die();
        $linha = '';
        foreach($log_faturamento as $log){
            $linha = '';
            $linha .=utf8_decode($log[0]['codigo_cliente'].' - '.$log[0]['razao_social']).";";
            $linha .=utf8_decode($log[0]['usuario']).";";
            $linha .=utf8_decode($log[0]['tipo_operacao']).";";
            $linha .=utf8_decode($log[0]['profissional']).";";
            $linha .=utf8_decode(COMUM::formatarDocumento($log[0]['cpf'])).";";
            $linha .=utf8_decode($log[0]['profissional_tipo']).";";
            $linha .=utf8_decode($log[0]['data_inclusao']).";";
            $existe_perfiladequado = stristr($log[0]['observacao'],'PERFIL ADEQUADO AO RISCO');
            if ($existe_perfiladequado!=''){ 
                $linha .=utf8_decode($log[0]['num_consulta']).";";
            }else{
                $linha .= " ;";
            } 
            $linha .=utf8_decode(COMUM::formatarPlaca(strtoupper($log[0]['placa']))).";";
            $linha .=utf8_decode(COMUM::formatarPlaca(strtoupper($log[0]['carreta']))).";";
            $linha .=utf8_decode(COMUM::formatarPlaca(strtoupper($log[0]['bitrem']))).";";
            $linha .=utf8_decode($log[0]['carga_tipo_descricao']).";";
            if (trim($log[0]['endereco_origem']) =='-' or trim($log[0]['endereco_origem']) =='0' or trim($log[0]['endereco_origem']) =='-0') {
                $linha .= " ;";
            }else{
                $linha .=utf8_decode($log[0]['endereco_origem']).";";
            }
            if (trim($log[0]['endereco_destino']) =='-' or trim($log[0]['endereco_destino']) =='0' or trim($log[0]['endereco_destino']) =='-0') {
                $linha .= " ;";
            }else{ 
                $linha .=utf8_decode($log[0]['endereco_destino']).";";
            }
            echo $linha."\n";
        }  
        die();
    }  

    function log_consultas(){
        $this->pageTitle = 'Log Faturamento Scorecard';
        $this->Filtros->limpa_sessao('FichaScorecard');
        $this->data['FichaScorecard']['data_inicial'] = date("d/m/Y");
        $this->data['FichaScorecard']['data_final']   = date("d/m/Y");
        $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        $tipos_profissional = $this->Fichas->listProfissionalTipoAutorizado();
        $tipos_operacoes = $this->TipoOperacao->listaTodosTiposOperacao();
        $tipos_faturamento = $this->TipoOperacao->listCustoSemCusto();
        $this->set(compact('tipos_faturamento','tipos_profissional','tipos_operacoes'));
    }  

    public function carregar_profissional_contatos( $codigo_profissional ){
        $this->layout = 'ajax';
        $this->TipoRetorno = ClassRegistry::init('TipoRetorno');
        $this->TipoContato = ClassRegistry::init('TipoContato');
        if( $codigo_profissional ){
            $contatos_profissional = $this->Profissional->carregarContatos( $codigo_profissional );
            if( $contatos_profissional ){
                $i = 1;
                $contatos = array();
                foreach ($contatos_profissional as $key => $contato ) {
                    if( empty($contatos[0]) && ($contato['codigo_tipo_retorno'] == TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA) ){
                        $contatos[0] = $contato;
                        continue; 
                    }
                    $contatos[$i] = $contato;
                    $i++;
                }
            }
            if( empty($contatos[0])){
                $contatos[0] = array(
                    'nome' => NULL, 
                    'codigo_tipo_contato' => NULL, 
                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA, 
                    'descricao' => NULL
                );
            }
            ksort($contatos);
            $this->data['ProfissionalContato'] = $contatos;
        }
        $tipo_retorno_profissional  = $this->TipoRetorno->listar();
        $tipo_contato               = $this->TipoContato->listarParFichaScorecard();
        $this->set(compact('tipo_retorno_profissional', 'tipo_contato'));
    }

    public function carregar_proprietario_contatos( $codigo_ficha, $index ){
        $this->layout = 'ajax';
        $this->Fichas->carregarDadosFicha( $codigo_ficha );
        $tipo_retorno_proprietario  = $this->TipoRetorno->listar();
        $tipo_contato               = $this->TipoContato->listarParFichaScorecard();
        $this->set(compact('tipo_retorno_proprietario', 'tipo_contato', 'index'));
    }
   
    public function copia_profissional_contatos( $index = 0 ){
        $this->layout = 'ajax';
        $this->data['FichaScorecardVeiculo'][$index]['ProprietarioContato'] = array();
        if( !empty($this->data['Motorista'][$index]['proprietario']) ){
            $this->data['FichaScorecardVeiculo'][$index]['ProprietarioContato'] = $this->data['ProfissionalContato'];            
            if( isset($this->data['FichaScorecardVeiculo']) ){
                foreach ($this->data['FichaScorecardVeiculo'] as $chav => $dados_veic ) {
                    if( isset($dados_veic['ProprietarioContato']) && is_array($dados_veic['ProprietarioContato']) ){
                        foreach ($dados_veic['ProprietarioContato'] as $key => $dados_contatos ) {
                            if( isset($dados_contatos['codigo_tipo_retorno']) && ($dados_contatos['codigo_tipo_retorno']==5) ){
                                if( isset($this->data['FichaScorecardVeiculo'][$chav]['ProprietarioContato'][$key] )) {
                                    $this->data['FichaScorecardVeiculo'][$chav]['ProprietarioContato'][$key]['codigo_tipo_retorno'] = 1;
                                }                            
                            }
                        }
                    }
                }
            }
        }
        $tipo_retorno_proprietario  = $this->TipoRetorno->listar();
        $tipo_contato               = $this->TipoContato->listarParFichaScorecard();
        $this->set(compact('tipo_retorno_proprietario', 'tipo_contato', 'index'));
    }

    public function relatorio_vinculo_excluido(){
        $this->pageTitle = 'Relatório Vínculo Excluídos';
        $tipos_profissionais = $this->Fichas->listProfissionalTipoAutorizado();
        if( !empty($this->authUsuario['Usuario']['codigo_cliente']) ) {
            $this->data['Cliente']['razao_social'] = $this->authUsuario['Usuario']['nome'];
        }        
        $this->data['FichaScorecard']['data_alteracao_inicial'] = date('d/m/Y');
        $this->data['FichaScorecard']['data_alteracao_final']   = date('d/m/Y');
        $this->set(compact('tipos_profissionais'));
    }
     
    public function listagem_relatorio_vinculo_excluido() {
        $this->layout = 'ajax';
        $filtros    = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        unset($filtros['data_inicial']);
        unset($filtros['data_final']);
        if( !empty($filtros['codigo_cliente']) ) {
            if( isset($filtros['base_cnpj']) && $filtros['base_cnpj'] ){
                $this->Cliente  = ClassRegistry::init('Cliente');
                $codigo_cliente = $filtros['codigo_cliente'];
                $dados_cliente  = $this->Cliente->carregar( $codigo_cliente );
                $filtros['codigo_cliente'] = $this->Cliente->codigosMesmaBaseCNPJ( $codigo_cliente );
            }
        }
        if( empty($filtros['codigo_cliente']) && !empty($this->authUsuario['Usuario']['codigo_cliente']) ) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        $conditions = $this->FichaScorecard->converteFiltroEmConditionParaFichas($filtros);        
        array_push($conditions, array('FichaScorecard.ativo' => 2) );
        $this->paginate['FichaScorecard'] = array(
            'conditions' => $conditions,
            'consulta_fichas' => true,
        );
        $listar = $this->paginate('FichaScorecard');
        $this->set(compact('listar'));
    }


    public function log_faturamento_excluido() {
        $this->pageTitle = 'Operações de Faturamento';
        $this->TipoOperacao = ClassRegistry::init('TipoOperacao');
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');
        if( empty($filtros) ){
            $this->data['FichaScorecard']['data_inicial'] = date('d/m/Y');
            $this->data['FichaScorecard']['data_final']   = date('d/m/Y');
        }else{
            $this->data['FichaScorecard'] = $filtros;
        }
        $this->Fichas->carregarCombos();
        $tipo_operacao= $this->TipoOperacao->find('list',array('fields'=>array('descricao'),'order'=>array('descricao')));
        $this->set(compact('tipo_operacao'));
    }

    public function log_faturamento_excluido_listagem(){        
        // $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');            
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaScorecard');        
        if(!empty($filtros['codigo_transportador']))
            $filtros['codigo_cliente'] = $filtros['codigo_transportador'];        
        if(!empty($filtros['codigo_embarcador']))
            $filtros['codigo_cliente'] = $filtros['codigo_embarcador'];
        $this->Produto = ClassRegistry::init('Produto');
        $this->LogFaturamentoExcluido = ClassRegistry::init('LogFaturamentoExcluido');        
        $this->Servico = ClassRegistry::init('Servico');
        $this->TipoOperacao = ClassRegistry::init('TipoOperacao');
        $this->ProfissionalTipo = ClassRegistry::init('ProfissionalTipo');
        $this->Cliente = ClassRegistry::init('Cliente');
        $this->Usuario = ClassRegistry::init('Usuario');
        $this->Profissional = ClassRegistry::init('Profissional');
        $this->Veiculo = ClassRegistry::init('Veiculo');
        $this->EnderecoCidade = ClassRegistry::init('EnderecoCidade');
        $this->EnderecoEstado = ClassRegistry::init('EnderecoEstado');
        $this->CargaTipo = ClassRegistry::init('CargaTipo');
        $corte_periodo = 10;

        $fields = array(
            "LogFaturamentoExcluido.codigo as codigo",
            "LogFaturamentoExcluido.motivo_exclusao as motivo_exclusao",
            "LogFaturamentoExcluido.data_exclusao as data_exclusao",
            "Cliente.razao_social as razao_social",
            "CASE WHEN FichaScorecard.codigo_score_manual = 2 THEN 'Adequado' 
              WHEN FichaScorecard.codigo_score_manual = 7 THEN 'Insuficiente' 
              WHEN FichaScorecard.codigo_score_manual = 8 THEN 'Divergente' 
            END AS status_manual",
            "ParametroScore.nivel as classificacao_motorista",
            "LogFaturamentoExcluido.codigo_cliente as codigo_cliente",
            "Usuario.apelido as usuario",
            "TipoOperacao.descricao as tipo_operacao",
            "Profissional.nome as profissional",
            "Profissional.codigo_documento as cpf",
            "SUBSTRING(CONVERT(VARCHAR,LogFaturamentoExcluido.data_inclusao, 120), 1, {$corte_periodo}) AS data_inclusao",
            "LogFaturamentoExcluido.numero_liberacao as num_consulta",
            "LogFaturamentoExcluido.placa as placa",
            "LogFaturamentoExcluido.placa_carreta as carreta",
            "LogFaturamentoExcluido.placa_veiculo_bitrem as bitrem",
            "(select DESCRICAO from {$this->CargaTipo->databaseTable}.{$this->CargaTipo->tableSchema}.{$this->CargaTipo->useTable}  a
             where a.codigo = LogFaturamentoExcluido.codigo_carga_tipo)  AS carga_tipo_descricao",
            "(select descricao from {$this->ProfissionalTipo->databaseTable}.{$this->ProfissionalTipo->tableSchema}.{$this->ProfissionalTipo->useTable} a where a.codigo = LogFaturamentoExcluido.codigo_profissional_tipo ) as profissional_tipo",
            "EnderecoCidadeDestino.descricao + '-' + EnderecoEstadoDestino.descricao as endereco_destino",
            "EnderecoCidadeOrigem.descricao + '-' + EnderecoEstadoOrigem.descricao as endereco_origem",
            "LogFaturamentoExcluido.observacao as observacao"
        );

        $this->LogFaturamentoExcluido->bindModel(array('belongsTo' => array(
            'Produto' => array('foreignKey' => 'codigo_produto'),
            'TipoOperacao' => array('foreignKey' => 'codigo_tipo_operacao'),
            'Servico' => array('foreignKey' => false, 'conditions' => 'TipoOperacao.codigo_servico = Servico.codigo'),
            'ProfissionalTipo' => array('foreignKey' => 'codigo_profissional_tipo'),
            'Cliente' => array('foreignKey' => 'codigo_cliente'),
            'Usuario' => array('foreignKey' => 'codigo_usuario_exclusao'),
            'Profissional' => array('foreignKey' => 'codigo_profissional'),
            'FichaScorecard' => array('foreignKey' => false, 'conditions' => 'FichaScorecard.codigo = LogFaturamentoExcluido.codigo_ficha_scorecard'),
            'ParametroScore' => array('foreignKey' => false, 'conditions' => 'ParametroScore.codigo = FichaScorecard.codigo_parametro_score'),
            'EnderecoCidadeOrigem' => array('className' => 'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_origem'),
            'EnderecoCidadeDestino' => array('className' => 'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_destino'),
            'EnderecoEstadoOrigem' => array('className' => 'EnderecoEstado', 'foreignKey' => false, 'conditions' => 'EnderecoCidadeOrigem.codigo_endereco_estado = EnderecoEstadoOrigem.codigo'),
            'EnderecoEstadoDestino' => array('className' => 'EnderecoEstado', 'foreignKey' => false, 'conditions' => 'EnderecoCidadeDestino.codigo_endereco_estado = EnderecoEstadoDestino.codigo'),
        )), false);

        $conditions = array();
        $conditions['LogFaturamentoExcluido.codigo_produto'] = array(Produto::SCORECARD);
        if(isset($filtros['codigo_cliente']) && $filtros['codigo_cliente'] > 0) {
            $conditions['LogFaturamentoExcluido.codigo_cliente'] = $filtros['codigo_cliente'];
        }
        if(isset($filtros['cpf']) && !empty($filtros['cpf'])) {
            $conditions['Profissional.codigo_documento'] = preg_replace('/[^\d]+/', '', $filtros['cpf']);
        }
        if(isset($filtros['placa']) && !empty($filtros['placa'])) {
            $filtros['placa'] = trim(str_replace('-','',$filtros['placa']));
            $conditions[] = array(
            'OR'=>array(
                'LogFaturamentoExcluido.placa'=> $filtros['placa'],
                'LogFaturamentoExcluido.placa_carreta'=> $filtros['placa'],
                'LogFaturamentoExcluido.placa_veiculo_bitrem'=> $filtros['placa']
            ));
        }
        if(isset($filtros['usuario']) && !empty($filtros['usuario'])) {
            $conditions['Usuario.apelido like '] = '%'.$filtros['usuario'].'%';
        }
        if(isset($filtros['tipos']) && !empty($filtros['tipos'])) {
            $conditions['TipoOperacao.cobrado'] = $filtros['tipos'] == 1 ? 0 : 1;
        }
        if(isset($filtros['tipo_operacao']) && !empty($filtros['tipo_operacao'])) {
            $conditions['TipoOperacao.codigo'] = $filtros['tipo_operacao'];
        }
        if(isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])) {
            $conditions['LogFaturamentoExcluido.data_inclusao >= '] = implode('-',array_reverse(explode('/',$filtros['data_inicial']))) . ' 00:00:00';
        }
        if(isset($filtros['data_final']) && !empty($filtros['data_final'])) {
            $conditions['LogFaturamentoExcluido.data_inclusao <= '] = implode('-',array_reverse(explode('/',$filtros['data_final']))) . ' 23:59:59';
        }
        if(isset($filtros['num_consulta']) && !empty($filtros['num_consulta'])) {
            $conditions['LogFaturamentoExcluido.numero_liberacao'] = $filtros['num_consulta'];
        }
        $order = 'LogFaturamentoExcluido.codigo DESC';
        $limit = 50;
        if( !empty($this->params['named']) )
            $conditions['page'] = !empty($this->params['named']) ? $this->params['named'] : NULL;      

        $this->paginate['LogFaturamentoExcluido'] = compact('fields', 'conditions', 'group', 'order', 'limit');
        $log_faturamento = $this->paginate('LogFaturamentoExcluido');
        $this->set(compact('log_faturamento'));
    }


    public function recuperar_numero_liberacao( $codigo_log_faturamento ){
        $this->pageTitle = 'Recuperar número de liberação';
        $this->set(compact('codigo_log_faturamento'));
        $this->loadModel('LogFaturamentoTeleconsult');
        $this->loadModel('LogConsultaStatusProfSc');        
        $numero_liberacao = '';
        if($this->RequestHandler->isPost()){
            $log_faturamento = $this->LogFaturamentoTeleconsult->carregar( $codigo_log_faturamento );
            $data = array(
                'LogConsultaStatusProfSc' => array(
                    'codigo_tipo_operacao'   =>$log_faturamento['LogFaturamentoTeleconsult']['codigo_tipo_operacao'] ,
                    'codigo_log_faturamento' =>$log_faturamento['LogFaturamentoTeleconsult']['codigo']
                )
            );
            if( !$this->LogConsultaStatusProfSc->incluir( $data ) ){
                $this->BSession->setFlash('save_error');
            } else {
                $numero_liberacao = $this->data['LogFaturamentoTeleconsult']['codigo'];
                $this->BSession->setFlash('save_success'); 
            }     
        }
        $this->set(compact('numero_liberacao'));
    }
}
?>