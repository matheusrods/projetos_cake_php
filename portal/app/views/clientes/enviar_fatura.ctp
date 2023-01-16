<?php if (!$em_execucao): ?>
    <p>
        Clicando no botão abaixo você irá iniciar o processo de envio dos links de <strong>Nota Fiscal + Boleto</strong> para os clientes do faturamento do mês selecionado.
    </p>
<?php echo $this->BForm->create('RetornoNf', array('url' => array('controller' => 'clientes', 'action' => 'enviar_fatura'))); ?>
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('mes', array('class' => 'input-medium', 'options' => $meses, 'label' => 'Mês')); ?>
      <?php echo $this->BForm->input('ano', array('class' => 'input-small', 'options' => $anos, 'label' => 'Ano')); ?>
    </div>
	<div class="form-actions">
	  <?php echo $this->BForm->submit('Enviar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	</div>
<?php echo $this->BForm->end() ?>
    </div>
<?php else: ?>
    <p>
        O sistema está trabalhando no envio dos links para os clientes.
    </p>
<?php endif; ?>