<ul class="nav nav-tabs">
  <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
  <?php if ($edit_mode): ?><li><a href="#outros-enderecos" data-toggle="tab">Outros Endereços</a></li><?php endif; ?>
  <?php if ($edit_mode): ?><li><a href="#contatos" data-toggle="tab">Contatos</a></li><?php endif; ?>
</ul>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('nome', array('class' => 'input-xxlarge', 'label' => 'Razão Social')); ?>
    <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium cpf_cnpj', 'label' => 'CNPJ/CPF', 'placeholder' => 'CNPJ/CPF', 'maxlength' => 18)); ?>
</div>
<div class="tab-content">
	
  <div class="tab-pane active" id="gerais">
        <?php echo $this->element('prestadores_enderecos/fields') ?>
	</div>
	
  <?php if ($edit_mode): ?>
    <div class="tab-pane" id="outros-enderecos">
      <?php echo $this->element('prestadores/outros_enderecos') ?>
    </div>
  <?php endif; ?>  

  <?php if ($edit_mode): ?>
    <div class="tab-pane" id="contatos">
      <?php echo $this->element('prestadores_contatos/contatos') ?>
    </div>
  <?php endif; ?>  
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>
<?php  echo $this->Javascript->codeBlock('jQuery(document).ready(function(){

  
  $("#PrestadorCodigoDocumento").blur(function(){        
      cnpj = $("#PrestadorCodigoDocumento").val( );
      $("#PrestadorCodigoDocumento").val(cnpj.replace(/\D/g,""));
  });

  codigo_prestador = jQuery("#PrestadorCodigo").val();
  $(document).on("click", ".dialog", function(e) {
    e.preventDefault();
    open_dialog(this, "Endereço", 960);
  });
  var div = jQuery("#endereco-prestador");
  bloquearDiv(div);
  div.load(baseUrl + "prestadores_enderecos/listar/" + codigo_prestador + "/" + Math.random() );  
})');?>
