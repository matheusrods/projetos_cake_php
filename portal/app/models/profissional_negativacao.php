<?php

class ProfissionalNegativacao extends AppModel {

	var $name = 'ProfissionalNegativacao';
	var $tableSchema = 'informacoes';
	var $databaseTable = 'dbTeleconsult';
	var $useTable = 'profissional_negativacao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
  var $validate = array(
    'codigo_profissional' => array(
      'notEmpty' => array(
        'rule' => 'notEmpty',
        'message' => 'Informe o código profissional',
        ),
      'isUnique' => array(
        'rule' => 'isUnique',
        'message' => 'Documento já cadastrado'
        ),

      ),
    'codigo_negativacao' => array(
      array(
        'rule' => 'notEmpty',
        'message' => 'Informe o código negativação',
        )
      ),
    'observacao' => array(
      array(
        'rule' => 'notEmpty',
        'message' => 'Favor preencha o campo Observação',
        )
      )
    );



  public function valida_criterio_nome($check) {
    if (!isset($this->data[$this->name]['nome']) || $this->data[$this->name]['nome'] == NULL )
      return false;
      //$codigo = $this->data[$this->name]['codigo'];
    $descricao = $check['nome'];
    return !$this->find('all',array('conditions'=> array('descricao'=>$descricao)));
  }



  public function carregarParaEdicao ($codigo) {
    $dados = $this->read(null, $codigo);
    return $dados;
  }


  public function existenegativacao ($codigo) {
    if (empty($codigo)) {
      return false;
    }
    return $this->find('count', array(
      'conditions' => array(
        'ProfissionalNegativacao.codigo_profissional' => $codigo,
        'ProfissionalNegativacao.codigo_negativacao <>' => 33
        )
      )) > 0;



  } 

  public function converteFiltroEmCondition($dados) {
    $condition = array(); 
    if (isset($dados['cpf']) && !empty($dados["cpf"])) {
      $condition["codigo_documento LIKE"] = preg_replace('/\D/', '', $dados["cpf"])."%";
    }
    if (isset($dados['nomedoprofissional']) && !empty($dados["nomedoprofissional"])) {
      $condition["nome LIKE"] = "%".$dados["nomedoprofissional"]."%";
    }

    return $condition; 
  }

  public function historicoOcorrencia($conditions){      
      $this->bindModel(array('belongsTo' => array(
           'Profissional' => array('foreignKey' => false, 'conditions'=>array('Profissional.codigo = ProfissionalNegativacao.codigo_profissional')),  
           'TipoNegativacao' => array('foreignKey' => false, 'conditions'=>array('TipoNegativacao.codigo = ProfissionalNegativacao.codigo_negativacao')),
           'Usuario' => array('foreignKey' => false, 'conditions' => array('Usuario.codigo =  ProfissionalNegativacao.codigo_usuario_inclusao')),
      )));      
      $fields = array(
        "ProfissionalNegativacao.data_inclusao", "TipoNegativacao.descricao","Profissional.nome", "Profissional.codigo_documento",
        "ProfissionalNegativacao.observacao","ProfissionalNegativacao.data_inclusao", "Usuario.apelido"
      );      
      return $this->find('all', compact('conditions', 'fields'));    
  }
}
?>