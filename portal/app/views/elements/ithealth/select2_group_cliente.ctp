<?php
/**
 * Componente para pesquisas de um credenciado
 * 
 * os componentes se encontram em views/helpers/ithealth.php e o javascript em webroot/js/rhhealth/ithealthHelpers.js
 *
 * o php 5.3 não tem cast de variáveis nos métodos, os nomes de parâmetros são baseados na documentação do cake, porém incluído sigla de tipagem, ex quando array => $arr
 * 
 * ** strFieldName
 * string para definir o nome do seu componente e é requerido
 * ex. codigo_documento
 * 
 * aceita a model 
 * ex. 'Fornecedor.codigo_documento'
 * 
 * ** arrOptions
 * array de valores para os campos, se não oferecer o valor o campo vai funcionar e deve ficar vazio. Se usado com select2 recomendo passar um valor vazio ex. array(''=>'')
 * 
 * ** arrAttributes
 * array para definições de estilo do componente
 * 
 * ** mixSelectedValue
 * string, inteiro ou array com valor prédefinido. Mais usado por exemplo quando estiver na página de editar e precisa passar um valor localizado
 * 
 *  
 * Exemplo de implementação
 *   
 *  <div class="well">
 *          $arrElementConfig = array(
 *              'legend' => 'Credenciado',
 *              'select2CodigoCredenciado' => array(
 *                  'strFieldName' => 'codigo_fornecedor',
 *                  'arrOptions' => array( $this->data['NotaFiscalServico']['codigo_fornecedor'] => $this->data['NotaFiscalServico']['codigo_fornecedor']),
 *                  'mixSelectedValue' => null,
 *                  'arrAttributes' => array(),
 *              ),
 *              'select2CodigoDocumentoCredenciado' => array(
 *                  'strFieldName' => 'Fornecedor.codigo_documento',
 *                  'arrOptions' => array( $this->data['NotaFiscalServico']['codigo_fornecedor'] => $this->data['Fornecedor']['codigo_documento']),
 *                  'mixSelectedValue' => null,
 *                  'arrAttributes' => array(),
 *              ),
 *              'select2RazaoSocialCredenciado' => array(
 *                  'strFieldName' => 'Fornecedor.razao_social',
 *                  'arrOptions' => array( $this->data['NotaFiscalServico']['codigo_fornecedor'] => $this->data['Fornecedor']['razao_social']),
 *                  'mixSelectedValue' => null,
 *                  'arrAttributes' => array(),
 *              ),
 *              'inputNomeFantasiaCredenciado' => array(
 *                  'strFieldName' => 'Fornecedor.nome',
 *                  'mixSelectedValue' => $this->data['Fornecedor']['nome'],
 *                  'arrAttributes' => array(),
 *              ),
 *          );
 *
 *        echo $this->element('ithealth/select2_group_credenciado', array('ithealth_element_config' => $arrElementConfig ));
 *
 *    </div>
*/

/**
 * Nome deste elemento
 */
$ithealth_element_name = 'select2_group_cliente';

 /**
  * Configuração base para funcionamento do componente
  */
$arrIthealthElementConfig = array(
    'legend' => 'Cliente',
    'select2CodigoCliente' => array(
        'strFieldName' => 'Cliente.codigo',
        'arrOptions' => array(),
        'mixSelectedValue' => null,
        'arrAttributes' => array(),
    ),
    'select2CodigoDocumentoCliente' => array(
        'strFieldName' => 'Cliente.codigo_documento',
        'arrOptions' => array(),
        'mixSelectedValue' => null,
        'arrAttributes' => array(),
    ),
    'select2RazaoSocialCliente' => array(
        'strFieldName' => 'Cliente.razao_social',
        'arrOptions' => array(),
        'mixSelectedValue' => null,
        'arrAttributes' => array(),
    ),
    'inputNomeFantasiaCliente' => array(
        'strFieldName' => 'Cliente.nome',
        'mixSelectedValue' => null,
        'arrAttributes' => array(),
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
?>
    <div class="row-fluid inline">
        <div class="span12">

            <?php if(!empty($arrIthealthElementConfig['legend'])){
                echo '<p class="legend">'.$arrIthealthElementConfig['legend'].'<p>';
            }
            ?>
            
            <div class="row-fluid inline">

                <div class="span2">
                    <?=  $ithealth->select2CodigoCliente(
                         $arrIthealthElementConfig['select2CodigoCliente']['strFieldName'], 
                         $arrIthealthElementConfig['select2CodigoCliente']['arrOptions'],
                         $arrIthealthElementConfig['select2CodigoCliente']['mixSelectedValue'],
                         $arrIthealthElementConfig['select2CodigoCliente']['arrAttributes']
                    ); ?> 
                </div>
                <div class="span2">
                    <?=  $ithealth->select2CodigoDocumentoCliente(
                        $arrIthealthElementConfig['select2CodigoDocumentoCliente']['strFieldName'], 
                        $arrIthealthElementConfig['select2CodigoDocumentoCliente']['arrOptions'],
                        $arrIthealthElementConfig['select2CodigoDocumentoCliente']['mixSelectedValue'],
                        $arrIthealthElementConfig['select2CodigoDocumentoCliente']['arrAttributes']
                    ); ?> 
                </div>

                <div class="span4">
                    <?=  $ithealth->select2RazaoSocialCliente(
                        $arrIthealthElementConfig['select2RazaoSocialCliente']['strFieldName'], 
                        $arrIthealthElementConfig['select2RazaoSocialCliente']['arrOptions'],
                        $arrIthealthElementConfig['select2RazaoSocialCliente']['mixSelectedValue'],
                        $arrIthealthElementConfig['select2RazaoSocialCliente']['arrAttributes']
                    ); ?> 
                </div>

                <div class="span4">
                    <?=  $ithealth->inputNomeFantasiaCliente(
                        $arrIthealthElementConfig['inputNomeFantasiaCliente']['strFieldName'], 
                        $arrIthealthElementConfig['inputNomeFantasiaCliente']['mixSelectedValue'],
                        $arrIthealthElementConfig['inputNomeFantasiaCliente']['arrAttributes']
                    ); ?> 
                </div>

            </div>
        </div>
    </div>
    <?php $this->addScript($this->Buonny->link_js('moment.min')) ?>     