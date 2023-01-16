<div class="well">
  <?php echo $bajax->form('PontuacoesStatusCriterio', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'pontuacoes_status_criterios'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('razao_social', array('class' => 'input-large', 'placeholder' => 'Cliente', 'label' => false, 'type' => 'text')) ?>
      <?php echo $this->BForm->input('descricao', array('class' => 'input-medium', 'placeholder' => 'Critérios', 'label' => false)) ?>
       <?php echo $this->BForm->input('nome', array('class' => 'input-large', 'placeholder' => 'Seguradora', 'label' => false)) ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php
echo $this->Javascript->codeBlock("$(document).ready(function() { atualizaListaPontuacoes(); });", false);
?>


