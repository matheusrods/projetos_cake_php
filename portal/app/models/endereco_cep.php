<?php
class EnderecoCep extends AppModel {

    var $name = 'EnderecoCep';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'endereco_cep';
    var $primaryKey = 'codigo';
    var $displayField = 'cep';
    var $actsAs = array('Secure');

    public function bindPais() {
      $this->bindModel(array(
        'belongsTo' => array(
          'EnderecoPais' => array(
            'className'  => 'EnderecoPais',
            'foreignKey' => false,
            'conditions' => 'EnderecoCep.codigo_endereco_pais = EnderecoPais.codigo'
          ),
        ),
      ),false);
    }

    function verificaCepExiste($cep, $codigo_estado, $incluir_se_nao_existir=TRUE){
        $codigo_cep = $this->find('first',
          array('fields' => 'EnderecoCep.codigo',
                'conditions' => array('EnderecoCep.cep' => $cep)));
        if(!empty($codigo_cep)){
          $codigo_cep = $codigo_cep['EnderecoCep']['codigo'];
        } else {
            $this->EnderecoEstado = ClassRegistry::init('EnderecoEstado');
            $codigo_pais = $this->EnderecoEstado->find('first',
              array('conditions' => array('EnderecoEstado.codigo' => $codigo_estado),
                    'fields'     => 'EnderecoEstado.codigo_endereco_pais'));

            $dados = array('codigo_endereco_pais'    => $codigo_pais['EnderecoEstado']['codigo_endereco_pais'],
                           'cep'                     => $cep,
                           'data_inclusao'           => date('Y-m-d H:i:s'),
                           'codigo_usuario_inclusao' => $_SESSION['Auth']['Usuario']['codigo']);

            if( $incluir_se_nao_existir === TRUE )
              parent::incluir($dados);
            $codigo_cep = $this->id;
        }
        return $codigo_cep;
    }

    function retornaCepPorCodigo($codigo){
      $cep = $this->find('first',array(
        'conditions' => array('codigo' => $codigo)
      ));
      return $cep;
    }
}

?>
