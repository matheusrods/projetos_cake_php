<?php

class EnderecoCidade extends AppModel {

    var $name = 'EnderecoCidade';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'endereco_cidade';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');
    var $belongsTo = array(
        'EnderecoEstado' => array(
            'className' => 'EnderecoEstado',
            'foreignKey' => 'codigo_endereco_estado'
          )
        );

   public function bindPais() {
      $this->bindModel(array(
        'belongsTo' => array(
          'EnderecoPais' => array(
            'className'  => 'EnderecoPais',
            'foreignKey' => false,
            'conditions' => 'EnderecoEstado.codigo_endereco_pais = EnderecoPais.codigo'
          ),
        ),
      ),false);
    }

   public function bindCep() {
      $this->bindModel(array(
        'belongsTo' => array(
          'EnderecoCep' => array(
            'className'  => 'EnderecoCep',
            'foreignKey' => 'codigo_endereco_cep'
          ),
        ),
      ));
    }    

   public function combo($codigo_endereco_estado) {
        $estados = $this->find('list', array('order' => 'EnderecoCidade.descricao',
            'conditions' => array('EnderecoCidade.invalido' => 0,
                                  'EnderecoCidade.codigo_endereco_estado' => $codigo_endereco_estado)));

        return $estados;
   }

   public function combo_cidade($codigo_endereco_cidade) {
        $cidades = $this->find('first', array('order' => 'EnderecoCidade.descricao',
            'conditions' => array('EnderecoCidade.invalido' => 0,
                                  'EnderecoCidade.codigo' => $codigo_endereco_cidade)));
        
        return $cidades;
   }

    public  function carrega_cidade_nome($nome) {
        $nome = explode('-',$nome);
        $cidades = $this->find('first', array('fields' => 'EnderecoCidade.codigo,EnderecoCidade.codigo_endereco_estado',
            'conditions' => array('EnderecoCidade.invalido' => 0,
                                  'EnderecoCidade.descricao' => $nome[0])));
        return $cidades;     
    }
    public function carregar_cidade_nome_completo($nome, $fields = array()) {
      $nome = utf8_decode($nome);
      return $this->find('first', array('conditions' => array('EnderecoCidade.descricao' => $nome), 'fields' => $fields));

    }

}

?>