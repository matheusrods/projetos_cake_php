<?php
class CidCnaeController extends AppController {
    public $name = 'CidCnae';
    var $uses = array( 'CidCnae',
    	'Cid',
    	'Cnae');
    

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->pageTitle = 'CID - CNAE';
        $this->data['CidCnae'] = $this->Filtros->controla_sessao($this->data, $this->CidCnae->name);
    }


    public function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->CidCnae->name);
        $conditions = $this->CidCnae->converteFiltroEmCondition($filtros);
        
        $fields = array('CidCnae.codigo', 'CidCnae.codigo_cid', 'CidCnae.codigo_cnae', 
                        'Cid.codigo_cid10','Cid.descricao','Cnae.cnae','Cnae.descricao','CidCnae.ativo');
        $order = 'CidCnae.codigo';

        $this->paginate['CidCnae'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order
        );

        $cid_cnae = $this->paginate('CidCnae');

        $this->set(compact('cid_cnae'));    
    }

    public function incluir() {
        $this->pageTitle = 'Incluir CID e CNAE';

        if($this->RequestHandler->isPost()) {

            $dados = $this->valida_cid_cnae($this->data['CidCnae']['codigo_cid10'], $this->data['CidCnae']['cnae']);

            
            if(!empty($this->data['CidCnae']['codigo_cid']) && !empty($this->data['CidCnae']['codigo_cnae'])){


                if ($this->CidCnae->incluir($this->data)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index', 'controller' => 'cid_cnae'));
                } 
                else {
                    $this->BSession->setFlash('save_error');
                }

            } else {
                if(!empty($dados['erro_cid'])){
                    $this->CidCnae->invalidate('codigo_cid10', $dados['erro_cid']);   
                }
                
                if(!empty($dados['erro_cnae'])){
                    $this->CidCnae->invalidate('cnae', $dados['erro_cnae']); 
                }

                $this->BSession->setFlash('save_error');
            }
        } 
    }

    public function editar() {
        $this->pageTitle = 'Editar CID e CNAE'; 

        if($this->RequestHandler->isPost()) {

            $dados = $this->valida_cid_cnae($this->data['CidCnae']['codigo_cid10'], $this->data['CidCnae']['cnae'], $this->data['CidCnae']['codigo']);
            
            if(!empty($this->data['CidCnae']['codigo_cid']) && !empty( $this->data['CidCnae']['codigo_cnae'])){
     
            
                if ($this->CidCnae->atualizar($this->data)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index', 'controller' => 'cid_cnae'));
                } 
                else {
                    $this->BSession->setFlash('save_error');
                }
            } else {
                if(!empty($dados['erro_cid'])){
                    $this->CidCnae->invalidate('codigo_cid10', $dados['erro_cid']);   
                }
                
                if(!empty($dados['erro_cnae'])){
                    $this->CidCnae->invalidate('cnae', $dados['erro_cnae']); 
                }

                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[0])) {            
           $this->data  = $this->CidCnae->carregar($this->passedArgs[0]);

        }        
    }

    public function atualiza_status($codigo, $status){
		$this->layout = 'ajax';

		$this->data['CidCnae']['codigo'] = $codigo;
		$this->data['CidCnae']['ativo'] = ($status == 0) ? 1 : 0;

		if ($this->CidCnae->atualizar($this->data, false)) {   
		    echo 1;
		} else {
		    echo 0;
		}
		$this->render(false,false);
    }


    public function valida_cid_cnae ($cid, $cnae, $codigo = null){
        $codigo_cid = "";
        $codigo_cnae = "";
        $dados = array();

        if(!empty($cid) && !empty($cnae)) {

            $codigo_cid = $this->CidCnae->retorna_codigo_cid($cid);
            $codigo_cnae = $this->CidCnae->retorna_codigo_cnae($cnae);

            if(empty($codigo_cid) || empty($codigo_cnae)){

                if(empty($codigo_cid)){
                   $dados['erro_cid'] =  'CID Inválido!';
                }     

                if(empty($codigo_cnae)){
                    $dados['erro_cnae'] = 'CNAE Inválido!';
                }
            } else {
                //Verifica se já existe registro com este cid e cnae
                if($this->CidCnae->cnae_cid_unico($codigo_cid, $codigo_cnae, $codigo)){
                    
                    $this->data['CidCnae']['codigo_cid'] = $codigo_cid;
                    $this->data['CidCnae']['codigo_cnae'] = $codigo_cnae;
                } else {

                    $dados['erro_cnae'] = 'CID e CNAE já cadastrados!';
                }

            }

        } else {

            if(empty($cid)){
                $dados['erro_cid'] = 'Informe o CID!';
            }                

            if(empty($cnae)){
                $dados['erro_cnae'] = 'Informe o CNAE!';
            }
        }

        return $dados;
    }
    
}