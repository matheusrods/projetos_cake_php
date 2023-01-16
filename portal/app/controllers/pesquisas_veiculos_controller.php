<?php
class PesquisasVeiculosController extends AppController {
    public $name = 'PesquisasVeiculos';
    public $layout = 'default';
    public $helpers = array('Paginator');
    public $components = array('Filtros', 'Session'); 
    public $uses = array(
            'PesquisaVeiculo',
            'Veiculo', 
            'Cliente',
            'TVeicVeiculo',
            'VeiculoProprietario',
            'VeiculoCor',
            'TPjurPessoaJuridica',
            'TVembVeiculoEmbarcador',
            'TVtraVeiculoTransportador',
            'TTveiTipoVeiculo',
            'TVtecVersaoTecnologia',
            'TEstaEstado',
            'TCidaCidade',
            'TMveiMarcaVeiculo',
            'TMvecModeloVeiculo',
            'Profissional',
            'VEndereco' 
        );   
    function listagem($status){         
        $filtros = $this->Filtros->controla_sessao($this->data, 'PesquisaVeiculo');         
        if(empty( $filtros['codigo_status'])){            
            if( $this->Session->read('veiculos_a_pesquisar') === TRUE ) {
                $filtros['codigo_status'] = array( PesquisaVeiculo::CADASTRADA, PesquisaVeiculo::PESQUISA );
                $filtros['pesquisa'] = true;
                $filtros['finaliza'] = false;
            } else if( $this->Session->read('veiculos_finalizados') === TRUE ) {            
                $filtros['codigo_status'] = array( PesquisaVeiculo::APROVADA, PesquisaVeiculo::REPROVADA);                 
                $filtros['pesquisa'] = false;
                $filtros['finaliza'] = true;
            }
        }       
        $this->layout = 'ajax';          
        
        $params  = $this->PesquisaVeiculo->parametros_veiculos_a_pesquisar($filtros);
        
        $this->paginate['PesquisaVeiculo'] = $params;
        $listar = $this->paginate('PesquisaVeiculo');
        $status = $this->PesquisaVeiculo->getStatus();
        $pesquisa = $this->Session->read('veiculos_a_pesquisar');        
        $this->set(compact('listar','status','tempo_pesquisa','pesquisa'));        
    }
    function listagem_pesquisa(){        
        if( $this->Session->read('veiculos_finalizados' ) === TRUE ) {
            $this->Filtros->limpa_sessao('PesquisaVeiculo');
        }
        $this->data['PesquisaVeiculo'] = $this->Filtros->controla_sessao($this->data, $this->PesquisaVeiculo->name);          
        $status = array( 
            PesquisaVeiculo::CADASTRADA => 'Cadastrada',
            PesquisaVeiculo::PESQUISA   => 'Em Pesquisa'            
        );    
        $data['PesquisaVeiculo']['codigo_status'] = $status;
        $this->Session->write('veiculos_a_pesquisar', true );
        $this->Session->write('veiculos_finalizados', false );
        $this->set(compact('status'));
        $this->render('index');
    }
    function listagem_finaliza(){    
        if( $this->Session->read('veiculos_a_pesquisar' ) === TRUE ) {
            $this->Filtros->limpa_sessao('PesquisaVeiculo');
        }
        $this->data['PesquisaVeiculo'] = $this->Filtros->controla_sessao($this->data, $this->PesquisaVeiculo->name);            
        $status = array( 
            PesquisaVeiculo::APROVADA  => 'Aprovada',
            PesquisaVeiculo::REPROVADA => 'Reprovada'            
        );    
        $data['PesquisaVeiculo']['codigo_status'] = $status;
        $this->Session->write('veiculos_a_pesquisar', false );
        $this->Session->write('veiculos_finalizados', true );
        $this->set(compact('status'));
        $this->render('index');
    }   
    function alterar($codigo_cliente,$placa,$codigo_ficha){
        $this->pageTitle = 'Pesquisar Veículos';
        $exibe_cobranca = TRUE;

        $isOperacional  = $this->BAuth->temPermissao(
            $this->authUsuario['Usuario']['codigo_uperfil'],
            'obj_veiculo-altera-frota'
        );
        $this->Cliente->bindModel(array('hasOne' => array(
            'ClienteEndereco' => array(
                'foreignKey' => 'codigo_cliente',
                'conditions' => array('codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL)
            ),
            'VEndereco' => array(
                'foreignKey' => false,
                'conditions' => 'VEndereco.endereco_codigo = ClienteEndereco.codigo_endereco'
            )
        )));
        $cliente = $this->Cliente->carregar($codigo_cliente);
        if($this->Session->read('veiculos_a_pesquisar' ) === TRUE){
            if($this->RequestHandler->isPost()) {
                $this->data['Usuario'] = $this->authUsuario['Usuario'];
                $dados['PesquisaVeiculo'] = $this->data['PesquisaVeiculo'];
                $dados['PesquisaVeiculo']['codigo_usuario_em_aprovacao'] = $this->authUsuario['Usuario']['codigo'];
                
                if($this->PesquisaVeiculo->atualizar($dados)){
                    $this->BSession->setFlash('save_success');
                    if( $this->Session->read('veiculos_finalizados' ) === TRUE )
                        $this->redirect(array('controller' => 'pesquisas_veiculos', 'action' => 'listagem_finaliza'));
                    else
                        $this->redirect(array('controller' => 'pesquisas_veiculos', 'action' => 'listagem_pesquisa'));
                    
                } else {
                    $this->BSession->setFlash('save_error');                
                }

            } else {
                $authUsuario = $this->authUsuario;     
                $dados = array('PesquisaVeiculo' => array(
                                'codigo' => $codigo_ficha,
                                'codigo_usuario_em_pesquisa' => $authUsuario['Usuario']['codigo'],
                                'codigo_status' => PesquisaVeiculo::PESQUISA
                            )
                    );
                $this->PesquisaVeiculo->atualizar($dados);  
            }
        }
        $this->TVeicVeiculo->bindModel(array(
            'belongsTo' => array(
                'TPjurPessoaJuridica' => array(
                    'class'     => 'TPjurPessoaJuridica',
                    'foreignKey'=> 'veic_pess_oras_codigo_propri')
            )
        ));

        $this->data = $this->TVeicVeiculo->buscaPorPlaca($placa,NULL,TRUE);
        $this->data['Cliente']['codigo'] = $codigo_cliente;
        $proprietario_endereco_combo = array();
        $veiculo = $this->Veiculo->buscaPorPlaca($placa);
        $veiculo_proprietario = $this->VeiculoProprietario->porVeiculo($veiculo['Veiculo']['codigo']);
    
        $this->data['Veiculo'] = $veiculo ? $veiculo['Veiculo'] : NULL;
    
        $this->VeiculoProprietario->bindModel(array('hasOne' => array(
            'ProprietarioEndereco' => array(
                'foreignKey' => false,
                'conditions' => array(
                    'ProprietarioEndereco.codigo_proprietario = Proprietario.codigo',
                    
                ),
                'order' => 'ProprietarioEndereco.codigo DESC'
            ),
            'VEndereco' => array(
                'foreignKey' => false,
                'conditions' => 'VEndereco.endereco_codigo = ProprietarioEndereco.codigo_endereco'
            ),
        )));

        $veiculo_proprietario = $this->VeiculoProprietario->porVeiculoTipoPreferencial($veiculo['Veiculo']['codigo'],TipoContato::TIPO_CONTATO_COMERCIAL);
        $this->data['Proprietario'] = $veiculo_proprietario['Proprietario'];
        $this->data['ProprietarioEndereco'] = $veiculo_proprietario['ProprietarioEndereco'];
        $this->data['VEndereco'] = $veiculo_proprietario['VEndereco'];
        $this->data['ProprietarioEndereco']['endereco_cep'] = $this->data['VEndereco']['endereco_cep'];
        
        $dataCor = $this->VeiculoCor->buscaPorDescricao($this->data['TVeicVeiculo']['veic_cor']);
        $this->data['VeiculoCor']   = ($dataCor)?$dataCor['VeiculoCor']:NULL;

        $this->TPjurPessoaJuridica->bindTPessPessoa();
        
        $cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);

        if($cliente_pjur['TPessPessoa']['pess_tipo'] == TPessPessoa::TRANSPORTADOR){
            $this->data['TVtraVeiculoTransportador']['vtra_tran_pess_oras_codigo'] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
            $this->data['TVtraVeiculoTransportador']['vtra_tvco_codigo'] = 1;
        } else {
            $this->data['TVembVeiculoEmbarcador']['vemb_emba_pjur_pess_oras_codigo'] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
            $this->data['TVembVeiculoEmbarcador']['vemb_tvco_codigo'] = 1;
        }

        $this->data['TPessPessoa']['pess_tipo'] = $cliente_pjur['TPessPessoa']['pess_tipo'];

        if( $veiculo['Veiculo']['codigo_motorista_default'] ){
            $dataMotorista      = $this->Profissional->carregar($veiculo['Veiculo']['codigo_motorista_default']);
            if($dataMotorista){
                $this->data['Veiculo']['motorista']         = $dataMotorista['Profissional']['codigo_documento'];
                $this->data['Veiculo']['nome_motorista']    = $dataMotorista['Profissional']['nome'];
                $this->data['Veiculo']['codigo_motorista']  = $dataMotorista['Profissional']['codigo'];
            }
        }

        $v_embarcador = $this->TVembVeiculoEmbarcador->buscaVembarcador($this->data['TVeicVeiculo']['veic_oras_codigo'],$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],TRUE);
        if( $v_embarcador ){
            $this->data['TVembVeiculoEmbarcador']['vemb_tip_cliente']               = $v_embarcador['TVembVeiculoEmbarcador']['vemb_tip_cliente'];
            $this->data['TVembVeiculoEmbarcador']['vemb_tvco_codigo']               = $v_embarcador['TVembVeiculoEmbarcador']['vemb_tvco_codigo'];
            $this->data['TVembVeiculoEmbarcador']['vemb_refe_codigo_origem']        = $v_embarcador['TRefeReferencia']['refe_codigo'];
            $this->data['TVembVeiculoEmbarcador']['vemb_refe_codigo_origem_visual'] = $v_embarcador['TRefeReferencia']['refe_descricao'];
            $exibe_cobranca = FALSE;

        }

        $v_transportador = $this->TVtraVeiculoTransportador->buscaVtransportador($this->data['TVeicVeiculo']['veic_oras_codigo'],$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],TRUE);
        if( $v_transportador ){
            $this->data['TVtraVeiculoTransportador']['vtra_tip_cliente']               = $v_transportador['TVtraVeiculoTransportador']['vtra_tip_cliente'];
            $this->data['TVtraVeiculoTransportador']['vtra_tvco_codigo']               = $v_transportador['TVtraVeiculoTransportador']['vtra_tvco_codigo'];
            $this->data['TVtraVeiculoTransportador']['vtra_refe_codigo_origem']        = $v_transportador['TRefeReferencia']['refe_codigo'];
            $this->data['TVtraVeiculoTransportador']['vtra_refe_codigo_origem_visual'] = $v_transportador['TRefeReferencia']['refe_descricao'];
            $exibe_cobranca = FALSE;
        }  

        $enderecos = (isset($this->data['VEndereco']['endereco_cep']) ? $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']) : array());
        $cliente_transportador = ($this->data['TPessPessoa']['pess_tipo'] == TPessPessoa::TRANSPORTADOR);                
        $PesquisaVeiculo = $this->PesquisaVeiculo->carregar($codigo_ficha);  

        $this->data['PesquisaVeiculo'] = $PesquisaVeiculo['PesquisaVeiculo'];                
        $transportador_default = $this->Cliente->carregar($this->data['PesquisaVeiculo']['codigo_cliente_transportador']);

        $this->data['PesquisaVeiculo']['transportador_default'] = $transportador_default['Cliente']['razao_social'];
        
        $pesquisa = $this->Session->read('veiculos_a_pesquisar');        
        $this->set(compact('cliente','exibe_cobranca','authUsuario','isOperacional','enderecos','cliente_transportador','pesquisa'));
    }    
}