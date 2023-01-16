<?php
/*
 * Essa action renderiza options de um combo sem incluir a tag 'select'
 * No caso, estamos renderizando uma lista de SubTipos
 * 
 */
    echo $this->element('generico/combo_options', array(
        'dados' => $lista_sub_tipos
    ));
?>