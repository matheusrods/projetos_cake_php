<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('ConselhoProfissional', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ConselhoProfissional', 'element_name' => 'conselho_classe'), 'divupdate' => '.form-procurar')) ?>
      <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>   
      </div> 
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Medico/element_name:medicos/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "medicos/listagem_conselho_classe/" + Math.random());
        }
        
    });', false);
?>