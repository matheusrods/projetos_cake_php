<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Audiometria', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Audiometria', 'element_name' => 'selecionar_funcionario'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('selecionar_funcionario/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
      <?php echo $html->link('Voltar', array('controller' => 'audiometrias', 'action' => 'index'), array('class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaFuncionarios();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Audiometria/element_name:selecionar_funcionario/" + Math.random())
        });
        
        function atualizaFuncionarios() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
			div.load(baseUrl + "audiometrias/listagem_funcionarios/" + Math.random());
        }
        
    });', false);
?>