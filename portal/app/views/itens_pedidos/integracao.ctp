<p>
    As opções abaixo são para processamento do faturamento do mês atual das operações do mês anterior.
</p>
<?php echo $this->BForm->create('ItemPedido', array('url' => array('controller' => 'itens_pedidos', 'action' => 'integracao'))); ?>
	<?php echo $this->BForm->hidden('mes') ?>
	
	<div class="form-actions">
		<?php echo $this->BForm->submit('Carregar', array('div' => false, 'class' => 'btn btn-primary', 'name' => 'data[Submit][type]')); ?>
		<?php echo $this->BForm->submit('Reverter', array('div' => false, 'class' => 'btn btn-warning', 'name' => 'data[Submit][type]')); ?>
	</div>

	<?php if (isset($_SESSION['Auth']['Usuario']['integrar_com_naveg']) && $_SESSION['Auth']['Usuario']['integrar_com_naveg'] == 1): ?>

		<div class="form-actions">
			<?php echo $this->BForm->submit('Integrar', array('div' => false, 'class' => 'btn btn-primary', 'name' => 'data[Submit][type]')); ?>
		</div>

		<div class="form-actions">
			<?php echo $this->BForm->input('mes', array('div' => false, 'type' => 'select', 'options' => $meses, 'class' => 'input-small', 'label' => 'Selecione o período', 'default' => date('m'))); ?>
			<?php echo $this->BForm->input('ano', array('div' => false, 'type' => 'select', 'options' => $anos, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
			<?php echo $this->BForm->submit('Integrar Pedidos Manuais', array('class' => 'btn btn-primary', 'name' => 'data[Submit][type]')); ?>
		</div>

	<?php endif; ?>
<?php echo $this->BForm->end() ?>

<div class="col-sm-8">
	<h3 class="modal-title" id="gridSystemModalLabel">Clientes aguardando confirmação</h3>
</div>

<div class = 'form-procurar'>
	<?= $this->element('/filtros/integracao_faturamento') ?>
</div>

<div class='lista'></div>

<script type="text/javascript">
  	
  	// SOLUCAO PARA EVITAR DUPLO CLIQUES NA HORA DE INTEGRAR, CARREGAR E REVERTER
    jQuery.fn.preventDoubleSubmit = function() {
		jQuery(this).submit(function() {
			if (this.beenSubmitted){
				return false;
			} else {
				this.beenSubmitted = true;				
			}
		});
    };

    $(document).ready(function() { 
		jQuery('#ItemPedidoIntegracaoForm').preventDoubleSubmit();
    });
</script>