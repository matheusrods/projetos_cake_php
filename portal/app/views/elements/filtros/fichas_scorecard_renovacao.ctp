<?php if( !isset($authUsuario['Usuario']['codigo_cliente']) ) : ?>
<div class="well">  
  <?php echo $bajax->form('RenovacaoAutomatica', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar','model' => 'RenovacaoAutomatica', 'element_name' => 'fichas_scorecard_renovacao'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'RenovacaoAutomatica') ?>
      <?php echo $this->BForm->input("Cliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>true)) ?> 
      <?php echo  $this->BForm->input('dias_renovacao', array('class' => 'input-small just-number numeric', 'type' => 'text','placeholder' =>'Qtd. Dias','label'=>false )); ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php endif; ?>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard') )) ?>
<?php echo $this->Buonny->link_js('comum') ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
    var div = jQuery("div.lista");
    bloquearDiv(div);
    div.load(baseUrl + "/renovacoes_automaticas/listar_fichas_a_renovar/" + Math.random());
    $("#RenovacaoAutomaticaCodigoCliente").blur(function(){
      setup_cliente( $("#RenovacaoAutomaticaCodigoCliente") );
    });  
    jQuery("#limpar-filtro").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RenovacaoAutomatica/element_name:fichas_scorecard_renovacao/" + Math.random())
    });
});', false);?>