<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Processamento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Processamento', 'element_name' => 'processamentos'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('processamentos/fields_filtros') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        atualizaProcessamentos();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Processamento/element_name:processamentos/" + Math.random())
        });
        function atualizaProcessamentos() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "processamentos/listagem/" + Math.random());
        }

    });
</script>
