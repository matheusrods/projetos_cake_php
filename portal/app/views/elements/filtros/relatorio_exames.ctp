<div class='well'>
	<h5><?= $this->Html->link((!empty($this->data['Exame']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<?php echo $bajax->form('Exame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Exame', 'element_name' => 'relatorio_exames'), 'divupdate' => '.form-procurar')) ?>
		
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'Exame'); ?>

			<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedor','Exame');?>

			<?php echo $this->BForm->input('data_inicio', array('label' => "Início", 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 

        	<?php echo $this->BForm->input('data_fim', array('label' => "Fim", 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-posicao', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){

		setup_datepicker(); 
    	atualizaLista();

        function atualizaLista() {
            var div = jQuery(".lista");
			bloquearDiv(div);
			div.load(baseUrl + "exames/relatorio_exames_listagem/" + Math.random());
        }

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

		jQuery("#limpar-filtro-posicao").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Exame/element_name:relatorio_exames/" + Math.random())
        });

    });', false);
?>
<?php if (!empty($this->data['Exame']['codigo_cliente'])): ?>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>

 