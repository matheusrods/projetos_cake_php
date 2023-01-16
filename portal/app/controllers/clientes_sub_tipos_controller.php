<?php
class ClientesSubTiposController extends AppController {
    public $name = 'ClientesSubTipos';
    var $uses = array('ClienteSubTipo');

    /**
     * Ação de Ajax que lista os sub tipos.
     * 
     * @param int $codigo_tipo 
     * 
     * @return Retorna uma view com um combo.
     */
    public function combo($codigo_tipo) {
        $this->layout = false;
        $lista_sub_tipos = $this->ClienteSubTipo->listaPorTipo($codigo_tipo);
        $this->set('lista_sub_tipos', $lista_sub_tipos);
    }
}

?>
