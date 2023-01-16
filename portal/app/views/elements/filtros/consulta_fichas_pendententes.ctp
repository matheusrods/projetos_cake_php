<div class="well">
  <?php echo $bajax->form('Ficha', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Ficha', 'element_name' => 'consulta_fichas_pendententes'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      
      <?php echo $this->BForm->input('codigo_ficha',array('label' => false,'type' => 'text','class' => 'input-medium', 'placeholder' => 'Código Ficha')) ?>

      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'Ficha') ?>
     
      <?php echo $this->BForm->input('codigo_seguradora',array('label' => false, 'empty' => 'Selecione uma Seguradora','options' => $lista_seguradora,'class'=>'input-large' ));?>

      <?php echo $this->BForm->input('codigo_tipo_profissional',array('label' => false, 'empty' => 'Selecione uma Categoria','options' => $tipos_profissional,'class'=>'input-large' ));?>

      <!--Caso  seja  necessario utilizar novamente
        <?/*php echo $this->BForm->input('produto_codigo',array('label' => false, 'empty' => 'Selecione um Produto','options' => $produto_descricao,'class'=>'input-large' ));*/?>
      -->
      <?php echo $this->BForm->hidden('pagina', array('value'=>1)); ?>
      <?php echo $this->BForm->input('codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium', 'placeholder' => 'CPF')) ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		atualizaListaConsultaFichasPendentes(); 

    $("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:Ficha/element_name:consulta_fichas_pendententes/" + Math.random())
		});
	});', false);
?>