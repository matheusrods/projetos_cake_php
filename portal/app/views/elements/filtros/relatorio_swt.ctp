<div class='well'>
  	<?php echo $bajax->form('PosSwtFormRespondido', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PosSwtFormRespondido', 'element_name' => 'relatorio_swt'), 'divupdate' => '.form-procurar')) ?>
        
        <div class="row-fluid inline">           
            <?php echo $this->Buonny->input_ge_unidades_cargos_setores($this, 'PosSwtFormRespondido', $unidades, $setores); ?>
    	</div>
         <div class="row-fluid inline">
            <?php 
                echo $this->BForm->input('cliente_opco', array('label' => 'Opco', 'class' => 'input-medium','options' => $cliente_opco, 'empty' => 'Selecione')); 
                echo $this->BForm->input('cliente_bu', array('label' => 'Business Unit', 'class' => 'input-medium','options' => $cliente_bu, 'empty' => 'Selecione')); 
                echo $this->BForm->input('id_walk_talk', array('type' => 'text', 'class' => 'input-mini',  'label' => 'ID Walk & Talk'));
                echo $this->BForm->input('observador', array('label' => 'Observador', 'class' => 'input-medium','options' => $observador, 'empty' => 'Selecione')); 
            ?>
         </div>

  	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
  	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  	<?php echo $this->BForm->end() ?>

<?php
    echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

        setup_datepicker();
        listagem();

		function listagem(){
            var div = jQuery(".lista");
            bloquearDiv(div);
            div.load(baseUrl + "swt/listagem_relatorio_swt/" + Math.random());
        }

		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PosSwtFormRespondido/element_name:relatorio_swt/" + Math.random())
        
            listagem();
        });

	});', false);
?>

</div>