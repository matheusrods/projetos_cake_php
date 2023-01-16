<div class='well'>
	<?php echo $bajax->form('Pmps', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Pmps', 'element_name' => 'pmps'), 'divupdate' => '.form-procurar', 'callback' => 'atualizaListaPmps')) ?>
    	<div class="row-fluid inline">
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Pmps'); ?>
            </div>
            <div>
                <?php echo $this->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidades', 'class' => 'input-xlarge', 'options' => $data_lista_unidades, 'empty' => 'Todos')) ?>
            </div>
    	</div> 
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Pmps/element_name:pmps/" + Math.random());
        });
    });
</script>
