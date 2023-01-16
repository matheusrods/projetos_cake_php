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
$ithealth_element_name = 'select2_group_credenciado';

 /**
  * Configuração base para funcionamento do componente
  */
$arrIthealthElementConfig = array(
    'legend' => 'Credenciado',
    'select2CodigoCredenciado' => array(
        'strFieldName' => 'Fornecedor.codigo',
        'arrOptions' => array(),
        'mixSelectedValue' => null,
        'arrAttributes' => array(),
    ),
    'select2CodigoDocumentoCredenciado' => array(
        'strFieldName' => 'Fornecedor.codigo_documento',
        'arrOptions' => array(),
        'mixSelectedValue' => null,
        'arrAttributes' => array(),
    ),
    'select2RazaoSocialCredenciado' => array(
        'strFieldName' => 'Fornecedor.razao_social',
        'arrOptions' => array(),
        'mixSelectedValue' => null,
        'arrAttributes' => array(),
    ),
    'inputNomeFantasiaCredenciado' => array(
        'strFieldName' => 'Fornecedor.nome',
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
        </div>
    </div>        
    <div class="row-fluid inline">
        <div class="span2">
            <?=  $ithealth->select2CodigoCredenciado(
                $arrIthealthElementConfig['select2CodigoCredenciado']['strFieldName'], 
                $arrIthealthElementConfig['select2CodigoCredenciado']['arrOptions'],
                $arrIthealthElementConfig['select2CodigoCredenciado']['mixSelectedValue'],
                $arrIthealthElementConfig['select2CodigoCredenciado']['arrAttributes']
            ); ?> 
        </div>
        <div class="span2">
            <?=  $ithealth->select2CodigoDocumentoCredenciado(
                $arrIthealthElementConfig['select2CodigoDocumentoCredenciado']['strFieldName'], 
                $arrIthealthElementConfig['select2CodigoDocumentoCredenciado']['arrOptions'],
                $arrIthealthElementConfig['select2CodigoDocumentoCredenciado']['mixSelectedValue'],
                $arrIthealthElementConfig['select2CodigoDocumentoCredenciado']['arrAttributes']
            ); ?> 
        </div>
        <div class="span4">
            <?=  $ithealth->select2RazaoSocialCredenciado(
                $arrIthealthElementConfig['select2RazaoSocialCredenciado']['strFieldName'], 
                $arrIthealthElementConfig['select2RazaoSocialCredenciado']['arrOptions'],
                $arrIthealthElementConfig['select2RazaoSocialCredenciado']['mixSelectedValue'],
                $arrIthealthElementConfig['select2RazaoSocialCredenciado']['arrAttributes']
            ); ?> 
        </div>

        <div class="span4">
            <?=  $ithealth->inputNomeFantasiaCredenciado(
                $arrIthealthElementConfig['inputNomeFantasiaCredenciado']['strFieldName'], 
                $arrIthealthElementConfig['inputNomeFantasiaCredenciado']['mixSelectedValue'],
                $arrIthealthElementConfig['inputNomeFantasiaCredenciado']['arrAttributes']
            ); ?> 
        </div>
    </div>
    <div class="row-fluid inline">
        <div class="span3">
            <div class="control-group">   
                <?= $ithealth->input('quantos_dias', array('label' => 'Quantidade de Dias de Pagamento', 'type' => 'text','readonly' => true)); ?>
                <img src="/portal/img/loading.gif" style="display: none;" id="quantos_dias_img_loading" />
            </div>
        </div>
    </div>
    <?php $this->addScript($this->Buonny->link_js('moment.min')) ?>     