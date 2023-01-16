<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Medico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Medico', 'element_name' => 'assinatura_eletronica'), 'divupdate' => '.form-procurar')) ?>
      <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'placeholder' => 'Nome', 'label' => false)) ?>  
        <?php echo $this->BForm->input('codigo_conselho_profissional', array('class' => 'input-small', 'placeholder' => 'Conselho', 'label' => false, 'options' => $conselho_profissional,'empty' => 'Conselho', 'style' => 'width: 100px')) ?>  
        <?php echo $this->BForm->input('numero_conselho', array('class' => 'input-medium', 'placeholder' => 'Número do Conselho', 'label' => false)) ?>  
        <?php echo $this->BForm->input('conselho_uf', array('class' => 'input-small', 'placeholder' => 'Estado Conselho', 'label' => false, 'options' => $estado,'empty'=>'Estado' )) ?>
        <?php echo $this->BForm->input('assinatura_eletronica', array('class' => 'input-medium', 'label' => false, 'options' => array('0' => 'Não', '1' => 'Sim'), 'empty' => 'Assinatura eletrônica', 'default' => "", 'style' => 'width: 160px')); ?>  
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
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Medico/element_name:assinatura_eletronica/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "assinatura_eletronica/listagem/" + Math.random());
        }
        
    });', false);
?>