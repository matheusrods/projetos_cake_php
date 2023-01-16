 <div class="well">
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id="filtros">

		<?php 			
			echo $this->Bajax->form('AutotracFaturamento', 
				array(
					'autocomplete' => 'off', 
					'url'          => array(
										'controller'   => 'filtros', 
										'action'       => 'filtrar', 
										'model'        => 'AutotracFaturamento', 
										'element_name' => 'autotrac_faturamento'
									), 
					'divupdate'    => '.form-procurar'
				)
			) ?>
		<div class="row-fluid inline">			
			<?php 				
				echo $this->BForm->input('mes_referencia', 
						array(
							'class' => 'input-medium',
							'label' => 'Mes', 
							'options' => $mes_referencia, 
							'empty' => 'Selecione'  
						)
					); ?>
			<?php echo $this->BForm->input('ano_referencia', 
						array(
							'class' => 'input-medium',
							'label' => 'Ano', 
							'options' => $ano_referencia, 
							'empty' => 'Selecione'
						)
					); ?>		
		</div>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<?php 
if(!empty($this->data['AutotracFaturamento'])){
	echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ atualizaListaAutotracFaturamento(); });', false); 
}
echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){    	    	
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AutotracFaturamento/element_name:autotrac_faturamento/" + Math.random())
            jQuery(".lista").empty();
        });		
    });
	function atualizaListaAutotracFaturamento() {	
		var div = jQuery("div.lista");
		bloquearDiv(div);		
		div.load(baseUrl + "autotrac_faturamentos/listagem/" + Math.random());
	}
', false);
?>