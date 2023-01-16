
<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'medico_padrao', $codigo_matriz,$referencia), 'type' => 'post')); ?>
	
	 <div class='well'>
        <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
        <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
    </div>
	
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_codigo_medico_readonly($this, 'codigo_medico_pcmso', 'Coord PCMSO', 'Coord PCMSO','Cliente', null, 'numero_conselho_pcmso', 'uf_conselho_pcmso', 'nome_medico_pcmso', 'cpf_medico_pcmso'); ?>
		
		<?php echo $this->BForm->input('numero_conselho_pcmso', array('style' => 'width: 80px;', 'label' => 'CRM', 'title' => ('CRM'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['numero_conselho'] : '')); ?>
		<?php echo $this->BForm->input('uf_conselho_pcmso', array('style' => 'width: 50px;', 'label' => 'UF', 'title' => ('UF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['conselho_uf']  : '')); ?>
		<?php echo $this->BForm->input('nome_medico_pcmso', array('style' => 'width: 260px;', 'label' => 'Nome do Médico', 'title' => ('NOME'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['nome']  : '')); ?>
		<?php echo $this->BForm->input('cpf_medico_pcmso', array( 'class' => 'input-medium cpf', 'label' => 'CPF do Médico', 'title' => ('CPF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['cpf']  : '')); ?>
	</div>
	<div class="form-actions">
	    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>

	    <?php if(!empty($referencia)): ?>
	        <?php if($referencia == 'implantacao_terceiros'): ?>
	            <?php echo $html->link('Voltar', array('action' => 'listagem_terceiros_unidades', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
	        <?php else:?>
	            <?php echo $html->link('Voltar', array('action' => 'index_unidades', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
	        <?php endif;?>
	    <?php else:?>
	        <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
	    <?php endif;?>
	</div>    
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>