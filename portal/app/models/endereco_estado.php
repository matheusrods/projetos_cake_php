<?php
class EnderecoEstado extends AppModel {
    var $name = 'EnderecoEstado';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'endereco_estado';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

    const ARGENTINA = 29;
    const URUGUAI = 30;
    const PARAGUAI = 31;
    const CHILE = 32;

   public function bindPais() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoPais' => array(
                    'className'  => 'EnderecoPais',
                    'foreignKey' => false,
                    'conditions' => 'EnderecoEstado.codigo_endereco_pais = EnderecoPais.codigo'
                ),
            ),
        ), false);
    }

    public function combo() {
        $estados = $this->find('list');
        return $estados;
    }

    public function comboPorPais( $codigo_pais = FALSE ) {
        if( $codigo_pais )
            $conditions = array('codigo_endereco_pais' => $codigo_pais );
        $order   = array('descricao');
        $estados = $this->find('list',compact('order', 'conditions'));
        return $estados;
    }

    function retorna_estados(){
      $estados = $this->find('list', array('conditions' => array('codigo_endereco_pais' => '1'), 'fields' => array('codigo', 'abreviacao')));
      return $estados;
    }
}
?>