<div class='well'>
    <?php echo $bajax->form('Cat', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cat', 'element_name' => 'cat'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('cat/fields_filtros') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cat/element_name:cat/" + Math.random())
        });
        
        function atualizaListaCat() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "cat/listagem/"+  Math.random());
        }
        atualizaListaCat()

    });', false);

?>