<?php
/**
 * Componente para pesquisa de nota fiscal por uma chave 
 * 
 */


/**
 * Nome deste elemento
 */
$ithealth_element_name = 'chave_rastreamento_nfe_api';

/**
 * Configuração base para funcionamento do componente
*/
$arrIthealthElementConfig = array(
    
    'input_chave_rastreamento' => array(
        
        // nome do campo
        'strFieldName' => 'chave_rastreamento',

        // atributos mais comuns 
        'arrAttributes' => array(
            'style'=>'width:100%', 
            'label' => 'Chave Rastreamento <abbr title="Chave para rastreamento de Nota Fiscal"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>', 
            'type' => 'text',
            'class' => ' event_on_chave_rastreamento_nfe_api'  // event_on_<nome elemento> é usado pelo javascript para determinar ações
        ),
    ),
);


// Se estiver recebendo novas configurações na implementação do componente então faz o merge
if(isset($ithealth_element_config))
{
    if(!is_array($ithealth_element_config)){
        throw new Exception(sprintf("Configuração do elemento ItHealth %s inválida", $ithealth_element_name), 1);
    }
    
    $arrIthealthElementConfig = array_replace_recursive($arrIthealthElementConfig['input_chave_rastreamento'], array('input_chave_rastreamento' => $ithealth_element_config));
}

echo $ithealth->input(
    $arrIthealthElementConfig['input_chave_rastreamento']['strFieldName'], 
    $arrIthealthElementConfig['input_chave_rastreamento']['arrAttributes']
); 

// se existir, carrega o javascript relacionado a este elemento
echo $ithealth->loadHelperJs($ithealth_element_name);