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
$monthNames = array(
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
);

$df_params = array(
    'type' => 'date',
    'dateFormat' => 'MY',
    'monthNames' => $monthNames,
    'minYear' => '2000',
    'maxYear' => date('Y'),
    //'label' => 'Periodo',
);

if (empty($name)) {
    $name = 'periodo';
}

if (isset($params)) {
    $params = array_merge($df_params, $params);
} else {
    $params = $df_params;
}

echo $this->Form->input($name, $params);