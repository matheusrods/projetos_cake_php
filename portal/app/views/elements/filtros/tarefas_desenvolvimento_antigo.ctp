<div class="well">
  <?php echo $bajax->form('TarefaDesenvolvimento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TarefaDesenvolvimento', 'element_name' => 'tarefas_desenvolvimento'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      
          
    <?php echo $this->BForm->input('codigo_usuario_inclusao',array('label' => false, 'empty' => 'Usuário','options' => $nome_usuario,'class'=>'input-small','value'=> $nome_usuario));?>


     <?php echo $this->Buonny->input_periodo($this,'TarefaDesenvolvimento') ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php
echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_datepicker();
    atualizaListaTarefaDesenvolvimento();    


    $("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TarefaDesenvolvimento/element_name:tarefas_desenvolvimento/" + Math.random())
		});
	});', false);



