<div class="well">
  <?php echo $bajax->form('PontuacoesStatusCriterio', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PontuacoesStatusCriterio', 'element_name' => 'pontuacoes_status_criterios'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      
      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'PontuacoesStatusCriterio') ?>
     
      <?php echo $this->BForm->input('codigo_criterio',array('label' => false, 'empty' => 'Selecione um Critério','options' => $criterios,'class'=>'input-large' ));?>

      <?php echo $this->BForm->input('codigo_seguradora',array('label' => false, 'empty' => 'Selecione uma Seguradora','options' => $seguradora,'class'=>'input-large'));?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php
echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		atualizaListaPontuacoes(); 
    $("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:PontuacoesStatusCriterio/element_name:pontuacoes_status_criterios/" + Math.random())
		});
	});', false);



