<?php
class AparelhosAudiometricosController extends AppController {
    public $name = 'AparelhosAudiometricos';
    var $uses = array('AparelhoAudiometrico',
        'Fornecedor',
        'FornecedorUnidade',
        'AparelhoAudioResultado');
    
    function index() {
        $this->pageTitle = 'Aparelhos Audiométricos';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AparelhoAudiometrico->name);       
        $conditions = $this->AparelhoAudiometrico->converteFiltroEmCondition($filtros);    

        $fields = array('AparelhoAudiometrico.codigo', 'AparelhoAudiometrico.descricao', 'AparelhoAudiometrico.fabricante','AparelhoAudiometrico.ativo');
        $order = 'AparelhoAudiometrico.descricao';

        $this->paginate['AparelhoAudiometrico'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'fields' => $fields, 
            'order' => $order
            );

        $aparelhos = $this->paginate('AparelhoAudiometrico');
        
        $this->set(compact('aparelhos'));

    }
    function carrega_combo(){
        $this->Fornecedor->bindModel(array(
           'belongsTo' => array(
               'Fornecedor' => array(
                   'alias' => 'Fornecedor',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'Fornecedor.codigo = FornecedorUnidade.codigo_fornecedor_unidade'
               )
           )
        ));

        $unidades = $this->Fornecedor->find('list',array(
                'conditions' => array('Fornecedor.ativo' => 2),
                'order' => 'Fornecedor.nome'
            )
        );

        $this->set(compact('unidades'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Aparelhos Audiometricos';
        $this->carrega_combo();

        $tela = "aparelhos_audiometricos";
        $this->set(compact('tela'));

        if($this->RequestHandler->isPost()) {
            try{
                $this->AparelhoAudiometrico->query('begin transaction');
            

                $this->data['AparelhoAudiometrico']['descricao'] = strtoupper($this->data['AparelhoAudiometrico']['descricao']);
                $this->data['AparelhoAudiometrico']['fabricante'] = strtoupper($this->data['AparelhoAudiometrico']['fabricante']);
                $this->data['AparelhoAudiometrico']['empresa_afericao'] = strtoupper($this->data['AparelhoAudiometrico']['empresa_afericao']);
                         
                if ($this->AparelhoAudiometrico->incluir($this->data)) {
                    
                    $codigo_aparelho_audiometrico = $this->AparelhoAudiometrico->id;
                    $this->data['AparelhoAudioResultado']['codigo_aparelho_audiometrico'] = $codigo_aparelho_audiometrico;

                    if ($this->AparelhoAudioResultado->incluir($this->data)) 
                            $this->BSession->setFlash('save_success');               
                        else
                            $this->BSession->setFlash('save_error');              
                }
                else{
                    $this->BSession->setFlash('save_error');
                }               
                $this->AparelhoAudiometrico->commit();
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'aparelhos_audiometricos'));
            } catch(Exception $e) {
                $this->AparelhoAudiometrico->rollback();
                return false;
            }
        }
    }
    
    function editar() {
        $this->pageTitle = 'Editar Aparelhos Audiometricos'; 
        $this->carrega_combo();

        $tela = "aparelhos_audiometricos";
        $this->set(compact('tela'));

        if($this->RequestHandler->isPost()) {

            try{
                $this->AparelhoAudiometrico->query('begin transaction');
            

                $this->data['AparelhoAudiometrico']['descricao'] = strtoupper($this->data['AparelhoAudiometrico']['descricao']);
                $this->data['AparelhoAudiometrico']['fabricante'] = strtoupper($this->data['AparelhoAudiometrico']['fabricante']);
                $this->data['AparelhoAudiometrico']['empresa_afericao'] = strtoupper($this->data['AparelhoAudiometrico']['empresa_afericao']);


                if ($this->AparelhoAudiometrico->atualizar($this->data)) {

                    $codigo_aparelho_audiometrico = $this->AparelhoAudiometrico->id;
                    $this->data['AparelhoAudioResultado']['codigo_aparelho_audiometrico'] = $codigo_aparelho_audiometrico;

                    $verifica_resultados = $this->AparelhoAudioResultado->find('first',array(
                        'conditions' => array('AparelhoAudioResultado.codigo_aparelho_audiometrico' => $codigo_aparelho_audiometrico)));

                    if(empty($verifica_resultados)){
                        if ($this->AparelhoAudioResultado->incluir($this->data)) 
                            $this->BSession->setFlash('save_success');               
                        else
                            $this->BSession->setFlash('save_error');
                    }
                    else{
                        if($this->AparelhoAudioResultado->atualizar($this->data))
                            $this->BSession->setFlash('save_success');
                        else
                            $this->BSession->setFlash('save_error');
                    }

                }
                else{
                    $this->BSession->setFlash('save_error');
                }               
                $this->AparelhoAudiometrico->commit();
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'aparelhos_audiometricos'));
            } catch(Exception $e) {
                $this->AparelhoAudiometrico->rollback();
                return false;
            }           
        }

        if (isset($this->passedArgs[0])) { 
        $this->AparelhoAudiometrico->bindModel(array(
           'belongsTo' => array(
               'AparelhoAudioResultado' => array(
                   'alias' => 'AparelhoAudioResultado',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'AparelhoAudiometrico.codigo = AparelhoAudioResultado.codigo_aparelho_audiometrico'
               )
           )
        ));

        $this->data = $this->AparelhoAudiometrico->find('first',array(
                'conditions' => array('AparelhoAudiometrico.codigo' => $this->passedArgs[0])));
        }        
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['AparelhoAudiometrico']['codigo'] = $codigo;
        $this->data['AparelhoAudiometrico']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->AparelhoAudiometrico->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
              
    }
}