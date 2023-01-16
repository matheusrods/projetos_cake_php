<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Cotacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cotacao', 'element_name' => 'cotacoes'), 'divupdate' => '.form-procurar')) ?>
      <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'placeholder' => 'Nº da cotação', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->Form->input('data_de', array('type' => 'text', 'class' => 'input-small data', 'placeholder' => 'Data de', 'label' => false)); ?>
        <?php echo $this->Form->input('data_ate', array('type' => 'text', 'class' => 'input-small data', 'placeholder' => 'até', 'label' => false)); ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'placeholder' => 'Cliente', 'label' => false)) ?>  
      </div>        
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        setup_datepicker();
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cotacao/element_name:cotacoes/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "cotacoes/listagem/" + Math.random());
        }
        
    });', false);
?>