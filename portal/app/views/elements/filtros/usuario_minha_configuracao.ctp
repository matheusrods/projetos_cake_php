<div class='well'>
    <?php echo $bajax->form('Usuario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Usuario', 'element_name' => "usuario_minha_configuracao", $action ), 'divupdate' => '.form-procurar')) ?>
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
            jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Usuario/element_name:usuario_minha_configuracao/". $action ."/' + Math.random())            
        });
        
        function atualizaListaUsuarios2() {
            var div = jQuery('div.lista');
            bloquearDiv(div);
            
            div.load(baseUrl + 'usuarios/listagem/minha_configuracao/' + Math.random());
        
         }
    });", false);
?>
