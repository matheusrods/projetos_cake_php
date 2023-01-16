<?php 
/**
 * Elemento generico que renderiza apenas options de um combo sem a tag "select"
 * Para usa-lo, é necessario passar os seguintes parametros:
 * 
 * @param array $dados Array com os dados que serão populados no combo
 * @param string $empty String contendo o texto do primeiro item do combo (Selecione...))
 * 
 * Caso $empty não seja passado, assume-se o valor default.
 * 
 */
    if(empty($selecione)) {
        $selecione = "Selecione uma opção";
    }
        
    echo $this->Html->tag('option', $selecione);
    foreach($dados as $valor => $label) {
        echo $this->Html->tag('option', $label, array('value' => $valor));
    }
?>