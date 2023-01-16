<div class="well">
  <?php echo $bajax->form('EstatisticaInicioFim', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'EstatisticaInicioFim', 'element_name' => 'estatistica_inicio_fim_sm'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      
      <?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente', 'Cliente', false,'EstatisticaInicioFim') ?>
     
     <?php echo $this->Buonny->input_periodo($this,'EstatisticaInicioFim') ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php
echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_datepicker();
    atualizaListaEstatisticaInicioFim(); 
    
    


    $("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:EstatisticaInicioFim/element_name:estatistica_inicio_fim_sm/" + Math.random())
		});
	});', false);



