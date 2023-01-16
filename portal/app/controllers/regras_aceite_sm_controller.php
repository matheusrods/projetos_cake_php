<?php
class RegrasAceiteSmController extends AppController {
    public $uses = array(
        'TRacsRegraAceiteSm',
        'TPracPerifericoRacs',
        'TRatvRegraAceiteTipoVeiculo');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('*');
    }

    public $helpers = array('BForm');

    function list_validade($codigo_cliente) {
        $this->loadModel('Cliente');
        $this->loadModel('TPjurPessoaJuridica');
        $regras_aceite_sm = array();
        if (!empty($codigo_cliente)) {
            $cliente = $this->Cliente->carregar($codigo_cliente);       
            if ($cliente) {
                $pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
                if ($pjur) {
                    $regras_aceite_sm = $this->TRacsRegraAceiteSm->listValidade($pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
                }
            }
        }
        die(json_encode($regras_aceite_sm));
    }

    function listar($pjur_pess_oras_codigo) {
        $conditions = $this->TRacsRegraAceiteSm->converteFiltrosEmConditions(array('racs_pjur_pess_oras_codigo' => $pjur_pess_oras_codigo));
        $racs = $this->TRacsRegraAceiteSm->listar($conditions);        
        $this->set(compact('racs'));
    }

     public function nova_linha($key, $element_name){
        $this->layout   = false;
        $model = "TCcvaCdChecklistValido";
        $titulo = 'Alvos';
        $this->set(compact( 'key', 'model', 'titulo'));
        $this->render('/elements/incluir_linhas_alvos');
    }

    function incluir($pjur_pess_oras_codigo) {
        App::Import('Component',array('DbbuonnyGuardian')); 
        $this->loadModel('TCcvaCdChecklistValido');     
        $this->pageTitle = 'Regras Aceite Sm';
        if (!empty($this->data)) {
            $sem_erro = NULL;
            $this->data['TRacsRegraAceiteSm']['racs_nao_permite_sm_concorrente'] = 0;
            $this->data['TRacsRegraAceiteSm']['racs_pjur_pess_oras_codigo'] = $pjur_pess_oras_codigo;
            if(empty($this->data['TRacsRegraAceiteSm']['racs_escolta_velada']) || $this->data['TRacsRegraAceiteSm']['racs_escolta_velada'] == false) {
                $this->data['TRacsRegraAceiteSm']['racs_qtd_escolta_velada'] = null;
            }
            if(empty($this->data['TRacsRegraAceiteSm']['racs_escolta_armada']) || $this->data['TRacsRegraAceiteSm']['racs_escolta_armada'] == false) {
                $this->data['TRacsRegraAceiteSm']['racs_qtd_escolta_armada'] = null;
            }
            if ($this->TRacsRegraAceiteSm->incluir($this->data)) {
                $sem_erro = TRUE;
            } else {
                $this->BSession->setFlash('save_error');
                $sem_erro = FALSE;
            }
          
            if($sem_erro == TRUE && isset($this->data['TCcvaCdChecklistValido'])) {
                $novaid = $this->TRacsRegraAceiteSm->id;
                foreach ($this->data['TCcvaCdChecklistValido'] as $key => $valor) {
                    if(!empty($valor['cvva_refe_codigo'])) {
                        $array_para_incluir = array('TCcvaCdChecklistValido' => array('ccva_refe_codigo' => $valor['cvva_refe_codigo'] ,
                                                                                            'ccva_racs_codigo' =>  $novaid ));
                        if($this->TCcvaCdChecklistValido->incluir($array_para_incluir)) {
                            $sem_erro = TRUE;
                        } else {
                            $sem_erro = FALSE;
                        }
                    }
                }
            }else {
                $this->BSession->setFlash('save_error');
                $sem_erro = FALSE;
            }


            if($sem_erro == TRUE) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'Clientes', 'action' => 'editar_configuracao', $this->data['TRacsRegraAceiteSm']['codigo_cliente']) );
            }else {
                $this->BSession->setFlash('save_error');

            }

        }
        $this->data['TRacsRegraAceiteSm']['codigo_cliente'] = DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($pjur_pess_oras_codigo);
        $this->carregarCombos();
    }

    function editar($racs_codigo, $pjur_pess_oras_codigo) {
        App::Import('Component',array('DbbuonnyGuardian'));
        $this->loadModel('TCcvaCdChecklistValido');
        
        $this->TCcvaCdChecklistValido->bindModel(array('hasOne'=>array('TRefeReferencia'=>array('class'=>'TRefeReferencia', 'foreignKey'=>false , 'conditions' => 'TRefeReferencia.refe_codigo = TCcvaCdChecklistValido.ccva_refe_codigo'))));
        $conditions = array('TCcvaCdChecklistValido.ccva_racs_codigo' => $racs_codigo);
        $listaContatos = $this->TCcvaCdChecklistValido->find('all', array('conditions' => $conditions));
        $sem_erro = NULL;
        if (!empty($this->data['TRacsRegraAceiteSm']['racs_codigo'])) {
            if(empty($this->data['TPracPerifericoRacs']['prac_ppad_codigo'])){
                $this->TPracPerifericoRacs->excluirPorRacs($this->data['TRacsRegraAceiteSm']['racs_codigo']);
            }
            if(empty($this->data['TRatvRegraAceiteTipoVeiculo']['ratv_codigo'])){
                $this->TRatvRegraAceiteTipoVeiculo->excluirPorRacs($this->data['TRacsRegraAceiteSm']['racs_codigo']);
            }
            if ($valor_nova_regra = $this->TRacsRegraAceiteSm->atualizar($this->data)) {
                $sem_erro = TRUE;
            } else {
                $sem_erro = FALSE;

            }

            if($sem_erro == TRUE) {
                $conditions = array('ccva_racs_codigo' => $racs_codigo);
                $limpa_alvos = $this->TCcvaCdChecklistValido->find('all',compact('conditions'));
              
                foreach ($limpa_alvos as $key => $value) {
                    if(!empty($value['TCcvaCdChecklistValido']['ccva_codigo'])) {
                        $this->TCcvaCdChecklistValido->excluir($value['TCcvaCdChecklistValido']['ccva_codigo']);
                    }
                }
                if(isset($this->data['TCcvaCdChecklistValido'])){
                    foreach ($this->data['TCcvaCdChecklistValido'] as $key => $valor) {
                        if(!empty($valor['cvva_refe_codigo'])) {
                            $array_para_incluir = array('TCcvaCdChecklistValido' => array('ccva_refe_codigo' => $valor['cvva_refe_codigo'],
                                                                                          'ccva_racs_codigo' => $this->data['TRacsRegraAceiteSm']['racs_codigo']));
                            
                            if($this->TCcvaCdChecklistValido->incluir($array_para_incluir)) {
                                 $sem_erro = TRUE;
                            } else {
                                 $sem_erro = FALSE;
                            }
                        }
                    }
                }    
            }else {
                $this->BSession->setFlash('save_error');
                $sem_erro = FALSE;
            }

          if($sem_erro == TRUE) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'Clientes', 'action' => 'editar_configuracao', $this->data['TRacsRegraAceiteSm']['codigo_cliente']) );
            }else {
                $this->BSession->setFlash('save_error');
            }

        } else {
            $this->TRacsRegraAceiteSm->bindModel(array('hasMany' => array('TPracPerifericoRacs' => array('foreignKey' => 'prac_racs_codigo'))));
            $this->TRacsRegraAceiteSm->bindModel(array('hasMany' => array('TRatvRegraAceiteTipoVeiculo' => array('foreignKey' => 'ratv_racs_codigo'))));
            
            $this->data = $this->TRacsRegraAceiteSm->carregar($racs_codigo);
            
            $this->data['TPracPerifericoRacs'] = array('prac_ppad_codigo' => Set::extract($this->data['TPracPerifericoRacs'], '/prac_ppad_codigo'));
            $this->data['TRatvRegraAceiteTipoVeiculo'] = array('ratv_tvei_codigo' => Set::extract($this->data['TRatvRegraAceiteTipoVeiculo'], '/ratv_tvei_codigo'));
        }
        $this->set(compact('racs_codigo', 'pjur_pess_oras_codigo', 'listaContatos'));
        $this->data['TRacsRegraAceiteSm']['codigo_cliente'] = DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($pjur_pess_oras_codigo);
        foreach ($listaContatos as $key => $checklist) {
            $this->data['TCcvaCdChecklistValido'][$key]['cvva_refe_codigo_visual'] =  $checklist['TRefeReferencia']['refe_descricao'];
            $this->data['TCcvaCdChecklistValido'][$key]['cvva_refe_codigo'] =  $checklist['TRefeReferencia']['refe_codigo'];
        }
        $this->carregarCombos();
    }

    function carregarCombos() {
        $this->loadModel('TEstaEstado');
        $this->loadModel('TTtraTipoTransporte');
        $this->loadModel('TPpadPerifericoPadrao');
        $this->loadModel('TProdProduto');
        $this->loadModel('TTveiTipoVeiculo');

        $esta_codigos = $this->TEstaEstado->find('list');
        $ttra_codigos = $this->TTtraTipoTransporte->find('list');                
        $ppad_codigos = $this->TPpadPerifericoPadrao->listComSimilares();
        $tvei_codigos = $this->TTveiTipoVeiculo->find('list');  
        $produtos = $this->TProdProduto->listar();
        $this->set(compact('esta_codigos', 'ttra_codigos', 'ppad_codigos', 'produtos', 'tvei_codigos'));
    }

    function excluir($racs_codigo) {
        die($this->TRacsRegraAceiteSm->excluir($racs_codigo));
    }
}
?>