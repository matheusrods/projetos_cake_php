<div class='well'>	
	<div id="filtros">
		<?php echo $this->Bajax->form('MSmitinerario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MSmitinerario', 'element_name' => 'itinerarios_sms_por_cliente'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_periodo($this, 'MSmitinerario') ?>
			<?php echo $this->Buonny->input_cliente_tipo($this, 0, $clientes_tipos) ?>
	        <?php echo $this->BForm->input('encerrada', array('class' => 'input-large', 'options' => $status, 'label' => false, 'empty' => 'Todos Status')); ?>
		</div>		
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MSmitinerario/element_name:itinerarios_sms_por_cliente/" + Math.random())
        });
        atualizaListaItinerariosSm();
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });*/
    });', false);
?>