<?php
App::import('Model', 'ContatoRetornoBase');
App::import('Model', 'BSession');

class ProprietarioContato extends ContatoRetornoBase {

	public $name = 'ProprietarioContato';
	public $tableSchema = 'publico';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'proprietario_contato';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_proprietario_contato'));
    public $validate = array(
        'codigo_tipo_contato' => array(
           'notEmpty' => array(
                'rule'    => 'notEmpty',
                'message' =>'Tipo contato é obrigatório'
                )
            ),
        'codigo_tipo_retorno'=> array(
            'notEmpty' => array(
                'rule'    => 'notEmpty',
                'message' =>'Tipo retorno é obrigatório'
                )
            )  
    );

    public function atualizarMultiplos($data){
        $errors = array();
        foreach ($data as $key => $PropContato) {
            if(!$this->atualizar($PropContato))
                @$errors[$key] = $this->invalidFields();
        }

        if($errors){ 
            foreach ($errors as $key => $value) {
                $this->validationErrors['tipo'][$key] = @$value['codigo_tipo_contato'];
                $this->validationErrors['tipo_retorno'][$key] =@$value['codigo_tipo_retorno'];
            }
        }
    }   


    public function salvarProprietarioContatoScorecard($contatos, $codigo_proprietario, $origem_portal=FALSE ){
        $this->ProprietarioContatoLog = ClassRegistry::init('ProprietarioContatoLog');    
        if($origem_portal)
            $this->Behaviors->attach('Loggable', array('foreign_key' => 'codigo_proprietario_contato'));
        $this->deleteAll(array('codigo_proprietario'=>$codigo_proprietario));

        $proprietario_contato_logs = array('ProprietarioContatoLog'=>array());
        if (is_array($contatos) && count($contatos)>0) {
            foreach($contatos as $key=>$contato){
                if(!empty($contato['codigo_tipo_retorno'])){
                    $contato['codigo_proprietario'] = $codigo_proprietario;
                    $this->create();
                    $this->save($contato, array('validate'=>false));
                    $proprietario_contato_logs['ProprietarioContatoLog'][] = $this->ProprietarioContatoLog->id;
                }
            }
        }
        return $proprietario_contato_logs;
    }

    public function carregarParaEdicao ($codigo) {
      $dados = $this->find('all', array('conditions'=>array('ProprietarioContato.codigo_proprietario'=>$codigo)));
      return $dados;
    }

    public function carrega_contatos_proprietarios($codigo_proprietario){
        return $this->find('all',array('conditions'=> array('codigo_proprietario'=>$codigo_proprietario)));
    }

    public function atualiza($codigo_proprietario, $dados) {
        $this->query('begin transaction');
        try{
            $this->deleteAll( array('codigo_proprietario'=>$codigo_proprietario) );
            foreach ($dados as $key => $contato ) {
                if( !empty($contato['ProprietarioContato']['codigo_tipo_contato']) && !empty($contato['ProprietarioContato']['codigo_tipo_contato']) ){
                    $contato['ProprietarioContato']['codigo_proprietario'] = $codigo_proprietario;
                    if( !$this->incluir( $contato['ProprietarioContato'] ) ){
                        throw new Exception('Erro ao incluir contato');                
                    }
                }
            }
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
        }
        return false;
    }
}