
<div class='form-procurar'>	
    <div class='well'>
	    <?php echo $this->BForm->create('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes', 'action' => 'utilizacao_de_servicos'))) ?>
	    <div class="row-fluid inline">
			<?php echo empty($authUsuario['Usuario']['codigo_cliente']) ? $this->Buonny->input_codigo_cliente($this) : $this->BForm->input('codigo_cliente', array('readonly' => true, 'class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')); ?>
	        <?php echo $this->BForm->input('codigo_produto', array('class' => 'input-medium', 'label' => false, 'options' => $produtos, 'empty' => 'Todos')); ?>
			<?php echo $this->BForm->input('mes_faturamento', array('label' => false, 'placeholder' => 'Mês', 'class' => 'input-medium', 'options' => $meses, 'title' => 'Mês de Faturamento')) ?>
	        <?php echo $this->BForm->input('ano_faturamento', array('label' => false, 'placeholder' => 'Ano','class' => 'input-mini numeric', 'title' => 'Ano de Faturamento')) ?>
	    </div>
	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	    <?php echo $this->BForm->end();?>
	</div>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ setup_datepicker(); });', false); ?>
</div>
<?php if (count($utilizacoes_assinatura)): echo $this->element('clientes/utilizacoes_de_servicos_assinatura_pagador', array('utilizacoes_assinatura' => $utilizacoes_assinatura)); ?>
<?php else:?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>	
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>