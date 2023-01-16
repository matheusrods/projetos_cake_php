<div class="well">
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id="filtros">
		<?php echo $this->Bajax->form('TTesiTratativaEventoSistema', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TTesiTratativaEventoSistema', 'element_name' => 'tratativas_eventos_sistema'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline">
		  	<?php echo $this->BForm->input('tesi_espa_codigo', array('class' => 'input-xlarge', 'options' => $eventos, 'label' => false, 'empty' => 'Selecione o Evento' )); ?> 
			<?php echo $this->BForm->input('tesi_descricao', array('class' => 'input-xlarge','label' => false, 'placeholder' => 'Descrição' )); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end();?>
	</div>
</div>

<?php 
	
echo $this->Javascript->codeBlock('
	function atualizaListaTratativaEvento() {
		var div = jQuery("div.lista");
		bloquearDiv(div);	
		div.load(baseUrl + "tratativas_eventos_sistema/listagem/" + Math.random());
	}
	jQuery(document).ready(function(){ atualizaListaTratativaEvento(); });', false); 
	
?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){     	
    	setup_mascaras();  		
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TTesiTratativaEventoSistema/element_name:tratativas_eventos_sistema/" + Math.random())
            jQuery(".lista").empty();
        });
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
    });', false);
?>
