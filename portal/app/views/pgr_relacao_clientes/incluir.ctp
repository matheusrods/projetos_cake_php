<?php echo $this->BForm->create('TPrclPgrRelacaoCliente', array('url' => array('controller' => 'pgr_relacao_clientes', 'action' => 'incluir'))); ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('prcl_pgr_codigo', array('empty' => 'Selecione o PGR','options' => $pgr,'class' => 'input-small', 'label' => 'PGR')); ?>
    <?php echo $this->BForm->input('prcl_ttra_codigo', array('empty' => 'Selecione o campo','options' => $tipo_transporte,'class' => 'input-medium', 'label' => 'Tipo do Tranporte')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->Buonny->input_codigo_cliente($this, 'prcl_embarcador_codigo', 'Embarcador', true, 'TPrclPgrRelacaoCliente' ); ?>
    <?php echo $this->Buonny->input_codigo_cliente($this, 'prcl_transportador_codigo', 'Transportador', true, 'TPrclPgrRelacaoCliente' ); ?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>