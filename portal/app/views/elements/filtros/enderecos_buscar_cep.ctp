<div class='well'>
  <?php echo $bajax->form('VEndereco', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'VEndereco', 'element_name' => 'enderecos_buscar_cep', 'searcher' => $input_id), 'divupdate' => '.form-procurar-cep')) ?>
  	<div class="row-fluid inline">
	    <?php echo $this->BForm->input('endereco', array('class' => 'input-large', 'placeholder' => 'Endereço', 'label' => false)) ?>
      <?php echo $this->BForm->input('bairro', array('class' => 'input-large', 'placeholder' => 'Bairro', 'label' => false)) ?> 
      <?php echo $this->BForm->input('cidade_search', array('class' => 'input-large ui-autocomplete-input-cidade','placeholder' => 'Cidade', 'label' => false)) ?>
      <?php echo $this->BForm->hidden('endereco_codigo_cidade', array('id'=>'endereco_codigo_cidade')) ?>
      <?php echo $this->BForm->hidden('endereco_codigo_estado', array('id'=>'endereco_codigo_estado')) ?>
  	</div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar-cep"));
            jQuery(".form-procurar-cep").load(baseUrl + "/filtros/limpar/model:VEndereco/element_name:enderecos_buscar_cep/searcher:'.$input_id.'/" + Math.random())
        });        
        atualizaListaCeps("'.$input_id.'");
    });', false);
?>
<?php echo $this->Javascript->codeBlock("
  $(document).ready(function() {
    jQuery('.ui-autocomplete-input-cidade').click(function(){
      $(function() {
        $('.ui-autocomplete-input-cidade').autocomplete({        
          source: baseUrl + 'enderecos/autocompletar/',
          focus: function(){return false;},
          minLength: 3,
          select: function( event, ui ) {
            content = $(this).parent();
            codigo_cidade   = ui.item.value;
            cidade_nome     = ui.item.label;
            codigo_estado   = ui.item.uf_value;
            codigo_pais     = ui.item.codigo_pais;
            $('#endereco_codigo_cidade').val(codigo_cidade);
            $('#endereco_codigo_estado').val(codigo_estado);
            $(this).val( cidade_nome );
            return false;
          }});
      });
  });  
});", false);?>