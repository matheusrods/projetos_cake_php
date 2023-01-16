<?php
class ProprietariosController extends AppController {

  public $name = 'Proprietarios';
  public $uses = array('Proprietario');


  public function beforeFilter(){
    parent::beforeFilter();
    $this->BAuth->allow("buscar");
  } 

  public function index() {
    $this->pageTitle = 'Proprietário';
    $filtros = $this->Filtros->controla_sessao($this->data, 'Proprietario');
      //$filtros = $this->Filtros->controla_sessao($this->data, $this->ArtigoCriminal->name);
    $this->data = $filtros;  

  }

  public function editar($codigo=null) {
    $this->loadModel('ProprietarioContato');
    $nao_salva ='';
    $this->pageTitle = 'Atualizar Proprietário';
    $cep_endereco = array();
    if (!empty($this->data)) {
      $this->data['Proprietario']['codigo_documento'] = preg_replace('/\W/', '', $this->data['Proprietario']['codigo_documento'] );
      for ($ind = 0; $ind < count($this->data['ProprietarioContato']['codigo']); $ind++) {
        $contatos[$ind] = array( 
          'ProprietarioContato' => array(
              // 'codigo'    => $this->data['ProprietarioContato']['codigo'][$ind],
              'nome'    => $this->data['ProprietarioContato']['nome'][$ind],
              'codigo_tipo_contato'    => $this->data['ProprietarioContato']['tipo'][$ind],
              'codigo_tipo_retorno'    => $this->data['ProprietarioContato']['tipo_retorno'][$ind],
              'descricao'    => $this->data['ProprietarioContato']['contato'][$ind]
              )
          );
        }

        // debug( @$contatos );die;

       if (@$contatos) {
        $this->ProprietarioContato->atualiza($this->data['Proprietario']['codigo'], $contatos );
        $this->set(compact('contatos'));         
       }
      $codigo = $this->data['Proprietario']['codigo'];
      $ProprietarioEndereco  =& ClassRegistry::init('ProprietarioEndereco');
      $Documento  =& ClassRegistry::init('Documento');
      $resultado_validacpf = $this->Proprietario->buscaDocumento($this->data['Proprietario']['codigo_documento']);
      $resultado_validacpf2 = $this->Proprietario->documentoValido();
      $resultado_validacpf3 =$Documento->existeCadastro($this->data['Proprietario']['codigo_documento']);
      if ($resultado_validacpf==NULL and $resultado_validacpf2==1 and 
       $resultado_validacpf3==NULL) {


       $this->data2['Documento']['codigo'] = $this->data['Proprietario']['codigo_documento'];
       if (strlen($this->data2['Documento']['codigo'])==11 ) {
        $this->data2['Documento']['tipo'] =0;
      }else{  

        $this->data2['Documento']['tipo'] =1;
      }
               // hardcode pois o informações tambem está 
      $this->data2['Documento']['codigo_pais']=1;
      $this->data2['Documento']['data_inclusao'] = date('Ymd H:i:s');
      $this->data2['Documento']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo_usuario_inclusao'];

    }

    $ProprietarioContato  =  &ClassRegistry::init('ProprietarioContato');
    //debug($contatos);
    //debug($this->data);die();


    
   $this->data['ProprietarioContato']['codigo_tipo_contrato'] = $this->data['ProprietarioContato']['tipo'];
   //elimina sempre o ultimo elemento
   $count_array = count($this->data['ProprietarioContato']['nome']) - 1;
   for ($i=0;$i< $count_array;$i++) {
      $this->data['ProprietarioContato']['codigo_proprietario'][$i] = $this->data['Proprietario']['codigo'];
      if ($this->data['ProprietarioContato']['tipo'][$i]=='' or
          $this->data['ProprietarioContato']['tipo_retorno'][$i]=='' ){
         $nao_salva = 1 ;
      } 
   }
   
   $this->data['ProprietarioContato']['codigo_tipo_contato'] = $this->data['ProprietarioContato']['tipo']; 
   $codigo_end = $ProprietarioEndereco->buscaCodigoEndereco($this->data['Proprietario']['codigo']);
   if(isset($codigo_end[0]['ProprietarioEndereco']['codigo'])){
    $this->data['ProprietarioEndereco']['codigo'] = $codigo_end[0]['ProprietarioEndereco']['codigo'];
  }
 
  if ($this->data['ProprietarioEndereco']['codigo']!=''){
   $result_end = $ProprietarioEndereco->atualizar($this->data);
  }else{
    //die('aki parouuuu');die();
    // hardcode conforme esta no informaçoes teleconsult
    $this->data['ProprietarioEndereco']['codigo_tipo_contato'] = 1 ;
    $this->data ['ProprietarioEndereco']['codigo_proprietario'] = $this->data['Proprietario']['codigo'];
    //debug($this->data);die();
    $result_end = $ProprietarioEndereco->incluir($this->data);
  }  

  if ($this->Proprietario->atualizar($this->data) and $result_end) { 
     //debug($this->data['ProprietarioContato_certo']);die();  
     // verifica se existe o tipo ou tipo retorno vazio 
      $existe_tipo_vazio = 0;
      $existe_tiporetorno_vazio =0;
      if (isset($this->data['ProprietarioContato_certo']['tipo'])) {
        for($i=0;$i<count($this->data['ProprietarioContato_certo']['tipo']);$i++){
            //print $this->data['ProprietarioContato_certo']['tipo'][$i]."<br>";
            if ($this->data['ProprietarioContato_certo']['tipo'][$i]==''){
                  $existe_tipo_vazio = 1;
            }
            if ($this->data['ProprietarioContato_certo']['tipo_retorno'][$i]==''){ 
              $existe_tiporetorno_vazio = 1;
            }
        }
       } 
      //print $existe_tipo_vazio.'or'.$existe_tiporetorno_vazio;
      //die();
      if ($existe_tiporetorno_vazio==1 or $existe_tipo_vazio==1) {
          $this->BSession->setFlash('save_error');
          if ($this->data['ProprietarioEndereco']['codigo_endereco']!=''){
             $endereco = &ClassRegistry::init('ProprietarioEndereco')->find('all',array('conditions'=>array('codigo_proprietario'=>$codigo)));
             $busca_codigo_end = &ClassRegistry::init('Endereco')->find('all',array('conditions'=>array('codigo'=>$endereco[0]['ProprietarioEndereco']['codigo_endereco'])));
             $busca_cep_end    =  &ClassRegistry::init('EnderecoCep')->find('all',array('conditions'=>array('codigo'=>@$busca_codigo_end[0]['Endereco']['codigo_endereco_cep'])));

             $cep_endereco  = $busca_cep_end[0]['EnderecoCep']['cep'];

             $this->data  = $this->Proprietario->carregarParaEdicao($codigo);
             $VEndereco  =  &ClassRegistry::init('VEndereco');
             @$end = $VEndereco->listarParaComboPorCodigo($endereco[0]['ProprietarioEndereco']['codigo_endereco']);
             $this->set('cep_endereco',$cep_endereco);
             $this->set('combo',$end);
             @$this->set('end',$endereco[0]);
         }
      }else{
        $this->BSession->setFlash('save_success');
        $this->redirect(array('action' => 'editar', $codigo));
      }   
 } else {
  
   $this->BSession->setFlash('save_error');
   if ($this->data['ProprietarioEndereco']['codigo_endereco']!=''){
       $endereco = &ClassRegistry::init('ProprietarioEndereco')->find('all',array('conditions'=>array('codigo_proprietario'=>$codigo)));
       $busca_codigo_end = &ClassRegistry::init('Endereco')->find('all',array('conditions'=>array('codigo'=>$endereco[0]['ProprietarioEndereco']['codigo_endereco'])));
       $busca_cep_end    =  &ClassRegistry::init('EnderecoCep')->find('all',array('conditions'=>array('codigo'=>@$busca_codigo_end[0]['Endereco']['codigo_endereco_cep'])));

       $cep_endereco  = $busca_cep_end[0]['EnderecoCep']['cep'];

       $this->data  = $this->Proprietario->carregarParaEdicao($codigo);
       $VEndereco  =  &ClassRegistry::init('VEndereco');
       @$end = $VEndereco->listarParaComboPorCodigo($endereco[0]['ProprietarioEndereco']['codigo_endereco']);
       $this->set('cep_endereco',$cep_endereco);
       $this->set('combo',$end);
       @$this->set('end',$endereco[0]);
   }
   
 }
}else {
  $ProprietarioContato  = &ClassRegistry::init('ProprietarioContato');
  $contatos = $ProprietarioContato->carregarParaEdicao($codigo);
  // debug( $contatos )
  $this->set('contatos', $contatos );
  $endereco = &ClassRegistry::init('ProprietarioEndereco')->find('all',array('conditions'=>array('codigo_proprietario'=>$codigo)));
  @$busca_codigo_end = &ClassRegistry::init('Endereco')->find('all',array('conditions'=>array('codigo'=>$endereco[0]['ProprietarioEndereco']['codigo_endereco'])));
  $busca_cep_end    =  &ClassRegistry::init('EnderecoCep')->find('all',array('conditions'=>array('codigo'=>@$busca_codigo_end[0]['Endereco']['codigo_endereco_cep'])));

  if( !empty($busca_cep_end[0]['EnderecoCep']['cep']))
    @$cep_endereco  = $busca_cep_end[0]['EnderecoCep']['cep'];
  $this->data  = $this->Proprietario->carregarParaEdicao($codigo);
  $VEndereco  =  &ClassRegistry::init('VEndereco');
  @$end = $VEndereco->listarParaComboPorCodigo($endereco[0]['ProprietarioEndereco']['codigo_endereco']);
  $this->set('cep_endereco',$cep_endereco);
  $this->set('combo',$end);
  @$this->set('end',$endereco[0]);
  $this->data['ProprietarioEndereco'] = $endereco[0]['ProprietarioEndereco'];

  $this->data['ProprietarioEndereco']['endereco_cep'] = $cep_endereco;
}
$this->data['Proprietario']['codigo_documento'] = COMUM::formatarDocumento($this->data['Proprietario']['codigo_documento']);
$this->incluirCarregarCombos();
}



function incluir() {
  $this->pageTitle = 'Incluir Proprietario';
  if($this->RequestHandler->isPost()) {
    unset($this->data['Proprietario']['codigo']);
    $this->data['Proprietario']['codigo_documento'] = str_replace(array('.','-','/'), '',$this->data['Proprietario']['codigo_documento']);
    
    $resultado_validacpf = $this->Proprietario->buscaDocumento($this->data['Proprietario']['codigo_documento']);
    $resultado_validacpf2 = $this->Proprietario->documentoValido();
    $Documento  =& ClassRegistry::init('Documento');
    $resultado_validacpf3 =$Documento->existeCadastro($this->data['Proprietario']['codigo_documento']);
    $this->data2['Documento']['codigo'] = $this->data['Proprietario']['codigo_documento'];
    if (strlen($this->data2['Documento']['codigo'])==11 ) {
      $this->data2['Documento']['tipo'] =0;
    }else{
      $this->data2['Documento']['tipo'] =1;
    }
    // hardcode pois o informações tambem está 
    // data2 objeto Documento
    $this->data2['Documento']['codigo_pais']=1;
    $this->data2['Documento']['data_inclusao'] = date('Ymd H:i:s');
    $this->data2['Documento']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo_usuario_inclusao'];
    
    // data objeto Proprietario
    $this->data['Proprietario']['data_inclusao'] = date('Ymd H:i:s');
    $this->data['Proprietario']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo_usuario_inclusao'];
    
    if ($resultado_validacpf==NULL and $resultado_validacpf2==1 and 
      $resultado_validacpf3==NULL) {
      $Documento->atualizar($this->data2);  
  }
  
  
  for ($i=0;$i<count($this->data['ProprietarioContato']['tipo_retorno']);$i++) {
    $contatos[$i] = array( 
      'ProprietarioContato' => array(
          'nome'                    => $this->data['ProprietarioContato']['nome'][$i],
          'codigo_tipo_contato'     => $this->data['ProprietarioContato']['tipo'][$i],
          'codigo_tipo_retorno'     => $this->data['ProprietarioContato']['tipo_retorno'][$i],
          'descricao'               => $this->data['ProprietarioContato']['contato'][$i]
          )
      );
  }
  
  $result = $this->Proprietario->incluir($this->data);

  $this->data['ProprietarioEndereco']['codigo_proprietario'] = $this->Proprietario->buscaCodigoProprietario($this->data['Proprietario']['codigo_documento']);
  $this->data['ProprietarioEndereco']['data_inclusao'] = date('Ymd H:i:s');
  $this->data['ProprietarioEndereco']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo_usuario_inclusao'];
    // hardcode conforme esta no informaçoes teleconsult
  $this->data['ProprietarioEndereco']['codigo_tipo_contato'] = 1;
  $ProprietarioEndereco  =& ClassRegistry::init('ProprietarioEndereco');
  $result_end = $ProprietarioEndereco->incluir($this->data['ProprietarioEndereco']);
  
    //data objeto Contato
  $dContatos = array();
  $codigo_proprietario = $this->Proprietario->buscaCodigoProprietario($this->data['Proprietario']['codigo_documento']);

  if( !empty($contatos)){
    $ProprietarioContato  =  &ClassRegistry::init('ProprietarioContato');
    $ProprietarioContato->atualiza( $codigo_proprietario, $contatos );
  }
  if ($result and $result_end ) {
    $this->BSession->setFlash('save_success');
    $this->redirect(array('action' => 'index')); 
  } else {
    $this->BSession->setFlash('save_error');
  }
}  
  $this->set('combo',array());
$this->incluirCarregarCombos();
}

function incluirCarregarCombos(){
  $this->loadModel('TipoContato');
  $this->loadModel('TipoRetorno');
  $tipoContato = $this->TipoContato->listar();
  $tipoRetorno = $this->TipoRetorno->listar();
  $this->set(compact("tipoContato","tipoRetorno"));
}


function excluir($codigo) {

  if(!$this->Proprietario->deletaProprietario($codigo)  
    )
    $this->BSession->setFlash('delete_error');
  else
    $this->redirect(array('action' => 'index'));
}



public function listagem() {
  $this->layout = 'ajax';
  $filtros = $this->Filtros->controla_sessao($this->data, 'Proprietario');
  $conditions = $this->Proprietario->converteFiltroEmCondition($filtros);
  $this->paginate['Proprietario'] = array(
    'conditions' => $conditions,
    'limit' => 50,
    'order' => 'Proprietario.codigo'
    );
  $proprietarios = $this->paginate('Proprietario');
  $this->set(compact('proprietarios')); 
} 

public function buscar($proprietario){
    $proprietario = preg_replace('/\W/', '', $proprietario);
    $retorno = $this->Proprietario->buscaProprietario($proprietario);
    //debug($retorno);die();
    echo json_encode($retorno);
    exit;
}



}
