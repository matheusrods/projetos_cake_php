<div class='well'>
    <?php echo $bajax->form('ClienteImplantacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteImplantacao', 'element_name' => "clientes_implantacao_terceiros"), 'divupdate' => '.form-procurar')) ?>
        
        <?php echo $this->element('clientes_implantacao/fields_filtros_terceiros') ?>
        
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div> 

<script type="text/javascript">
    jQuery(document).ready(function(){
        
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "clientes_implantacao/implantation_list");

        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteImplantacao/element_name:clientes_implantacao_terceiros")
        });
    });
</script>