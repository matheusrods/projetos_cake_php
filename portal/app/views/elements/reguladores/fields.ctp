<ul class="nav nav-tabs">
  <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
  <?php if ($edit_mode): ?><li><a href="#outros-enderecos" data-toggle="tab">Outros Endereços</a></li><?php endif; ?>
  <?php if ($edit_mode): ?><li><a href="#contatos" data-toggle="tab">Contatos</a></li><?php endif; ?>
  <?php if ($edit_mode): ?><li><a href="#regioes" data-toggle="tab">Regiões</a></li><?php endif; ?>
</ul>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('nome', array('class' => 'input-xxlarge', 'label' => 'Razão Social')); ?>
    <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium cpf_cnpj just-number', 'label' => 'CNPJ/CPF', 'placeholder' => 'CNPJ/CPF', 'maxlength' => 18)); ?>
</div>
<div class="tab-content">
	
  <div class="tab-pane active" id="gerais">
        <?php echo $this->element('reguladores_enderecos/fields') ?>
	</div>
	
  <?php if ($edit_mode): ?>
    <div class="tab-pane" id="outros-enderecos">
      <?php echo $this->element('reguladores/outros_enderecos') ?>
    </div>
  <?php endif; ?>  

  <?php if ($edit_mode): ?>
    <div class="tab-pane" id="contatos">
      <?php echo $this->element('reguladores_contatos/contatos') ?>
    </div>
  <?php endif; ?>  

  <?php if ($edit_mode): ?>
    <div class="tab-pane" id="regioes">
      <?php echo $this->element('reguladores_regioes/regioes') ?>
    </div>
  <?php endif; ?> 
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
  jQuery(document).ready(function(){
    $("#ReguladorCodigoDocumento").blur(function(){        
        cnpj = $("#ReguladorCodigoDocumento").val( );
        $("#ReguladorCodigoDocumento").val(cnpj.replace(/\D/g,""));
    });

    codigo_regulador = jQuery("#ReguladorCodigo").val();
    $(document).on("click", ".dialog", function(e) {
      e.preventDefault();
      open_dialog(this, "Endereço", 960);
    });

    var div = jQuery("#endereco-regulador");
    bloquearDiv(div);
    div.load(baseUrl + "reguladores_enderecos/listar/" + codigo_regulador + "/" + Math.random() );  
})');?>