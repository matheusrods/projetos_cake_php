<?php
if (isset($minha_configuracao) && $minha_configuracao = "minha_configuracao") {
    $element_name = "usuario_minha_configuracao";
    $minha_configuracao = "minha_configuracao";
    $url_limpa_cache = "jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Usuario/element_name:usuarios_minha_configuracao/". $action ."/' + Math.random()) ";
} else {
    $element_name = "usuarios";
    $minha_configuracao = "null";
    $url_limpa_cache = "jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Usuario/element_name:usuarios/". $action ."/' + Math.random()) ";
}
?>


<div class='well'>
    <?php echo $bajax->form('Usuario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Usuario', 'element_name' => $element_name, $action ), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?= $this->element('filtros/usuario_por_cliente_filtros') ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php

echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        atualizaListaUsuarios2();
        jQuery('#limpar-filtro').click(function(){
            bloquearDiv(jQuery('.form-procurar'));
            ". $url_limpa_cache ."            
        });
        
        function atualizaListaUsuarios2() {
            var div = jQuery('div.lista');
            bloquearDiv(div);
            
            var minha_config = '{$minha_configuracao}';
            if ( minha_config == 'minha_configuracao') {
                div.load(baseUrl + 'usuarios/listagem/{$minha_configuracao}/' + Math.random());
            } else {
                div.load(baseUrl + 'usuarios/listagem/' + Math.random());      
            }
        
         }
    });", false);
?>
