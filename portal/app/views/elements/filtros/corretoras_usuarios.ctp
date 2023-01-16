<div class='well'>
  <?php echo $bajax->form('Corretora', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Corretora', 'element_name' => 'corretoras_usuarios'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
		<?php echo $this->BForm->input('nome', array('class' => 'input-medium', 'placeholder' => 'Nome', 'label' => false)) ?>
		<?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaCorretoras("usuarios");
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Corretora/element_name:corretoras_usuarios/" + Math.random())
        });
    });', false);

?>
