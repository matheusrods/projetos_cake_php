<div class="well">
  <?php echo $bajax->form('FichaStatusCriterio', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaStatusCriterio', 'element_name' => 'resultados_pesquisa_cliente'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->BForm->hidden('pagina', array('value'=>1)); ?>
      <?php echo $this->BForm->input('codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium', 'placeholder' => 'CPF')) ?>      
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		atualizaListaResultadosPesquisaCliente(); 

    $("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaStatusCriterio/element_name:resultados_pesquisa_cliente/" + Math.random())
		});
	});', false);

?>




