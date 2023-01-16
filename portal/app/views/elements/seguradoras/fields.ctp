<ul class="nav nav-tabs">
  <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
  <?php if ($edit_mode): ?><li><a href="#outros-enderecos" data-toggle="tab">Outros Endereços</a></li><?php endif; ?>
  <?php if ($edit_mode): ?><li><a href="#contatos" data-toggle="tab">Contatos</a></li><?php endif; ?>
</ul>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('nome', array('class' => 'input-xxlarge', 'label' => 'Razão Social')); ?>
    <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'label' => 'CNPJ / CPF')); ?>
</div>
<div class="tab-content">
	
  <div class="tab-pane active" id="gerais">
        <?php echo $this->element('seguradoras_enderecos/fields') ?>
	</div>
	
  <?php if ($edit_mode): ?>
    <div class="tab-pane" id="outros-enderecos">
      <?php echo $this->element('seguradoras/outros_enderecos') ?>
    </div>
  <?php endif; ?>  

  <?php if ($edit_mode): ?>
    <div class="tab-pane" id="contatos">
      <?php echo $this->element('seguradoras_contatos/contatos') ?>
    </div>
  <?php endif; ?>  
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('seguradoras.js')); ?>