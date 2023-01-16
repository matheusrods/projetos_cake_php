<?php
/**
 * Componente para pesquisas de notas fiscais baseando-se na chave de rastreamento
 * 
 * Pesquisa por chaves cadastradas no banco a partir do décimo caracter
 * 
 * * Chave de rastreio é composta por 44 caracteres 
 * 
 * 
 * 
 */


/**
 * Nome deste elemento
 */
$ithealth_element_name = 'chave_rastreamento_nfe';

/**
 * Configuração base para funcionamento do componente
*/
$arrIthealthElementConfig = array(
    
    'input_chave_rastreamento' => array(
        'strFieldName' => 'chave_rastreamento',
        'arrAttributes' => array(
            'style'=>'width:100%', 
            'label' => 'Chave Rastreamento <abbr title="Chave para rastreamento de Nota Fiscal"><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>', 
            'type' => 'text',
            'class' => 'chave_rastreamento_nfe'
        ),
    ),
);


// Se estiver recebendo novas configurações na implementação do componente então faz o merge
if(isset($ithealth_element_config))
{
    if(!is_array($ithealth_element_config)){
        throw new Exception(sprintf("Configuração do elemento ItHealth %s inválida", $ithealth_element_name), 1);
    }

    $arrIthealthElementConfig = array_replace_recursive($arrIthealthElementConfig, $ithealth_element_config);
}

echo $ithealth->input(
    $arrIthealthElementConfig['input_chave_rastreamento']['strFieldName'], 
    $arrIthealthElementConfig['input_chave_rastreamento']['arrAttributes']
); 


echo $ithealth->loadHelperJs($ithealth_element_name);