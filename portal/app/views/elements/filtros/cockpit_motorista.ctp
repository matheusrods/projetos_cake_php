<div class="well">
  <?php echo $bajax->form('CockpitMotorista', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'CockpitMotorista', 'element_name' => 'cockpit_motorista'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      
      <?php echo $this->BForm->input('cpf_rne', array('label' => false, 'class' => 'input-medium formata-rne', 'placeholder' => 'CPF /RNE')) ?>
     
     <?php echo $this->Buonny->input_periodo($this,'CockpitMotorista') ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php
echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_datepicker();
    setup_mascaras();
    atualizalistaCockpitMotorista(); 
    
    


    $("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:CockpitMotorista/element_name:cockpit_motorista/" + Math.random())
		});
	});', false);



