<div class='form-procurar'> 
    <div class='well'>
        <?php echo $this->BForm->create('ClienteProduto', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes_produtos', 'action' => 'assinatura_cliente_para_cliente'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente2($this,array('input_name' => 'ClienteDe','placeholder' => 'Cliente','label' => 'De')); ?>
            <?php echo $this->Buonny->input_codigo_cliente2($this,array('input_name' => 'ClientePara','placeholder' => 'Cliente','label' => 'Para')); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php if (isset($cliente)): ?>
	<?php echo $this->BForm->create('ClienteProdutoServico2', array('type' => 'POST','autocomplete' => 'off', 'url' => array('controller' => 'clientes_produtos', 'action' => 'monta_copia_assinatura',$cliente['ClientePara']['Cliente']['codigo']))) ; ?>

	<div class='well'>
		<span class="label label-info">DE</span><br>
        <strong>Código: </strong><?= $cliente['ClienteDe']['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['ClienteDe']['Cliente']['razao_social'] ?>
    </div>
    <?php if( !empty($produtos) ) : ?>
    	<table class='table'>
			<thead>
				<tr>
					<th class='input-large'>Produto / Serviço</th>
					<th class='input-small numeric'>Valor</th>		
				</tr>
			</thead>
			<tbody>
				<?php foreach($produtos as $produto) : ?>
				<tr>
					<td colspan='2'> <strong>> <?php echo $produto['Produto']['descricao'] ?></strong> </td>
				</tr>
					<?php foreach($produto['ClienteProdutoServico2'] as $servico) : ?>
						<tr>
							<td> <?php echo $servico['Servico']['descricao']; ?> </td>
							<td class='numeric'><?= $this->BForm->input("ClienteProduto.{$produto['ClienteProduto']['codigo_produto']}.{$servico['codigo_servico']}", array('label' => false, 'input_name' => "dados.{$produto['ClienteProduto']['codigo_produto']}.{$servico['codigo_servico']}", 'class' => 'input-small numeric moeda valores', 'maxlength' => 14, 'value' => number_format($servico['valor'],2) )) ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	    <div class='well' style="padding-bottom: 0px;">
	    	<?php echo $this->Html->link('Copiar Assinatura', 'javascript:void(0)', array('class' => 'btn btn-success confirmar', 'data-toggle' => 'tooltip', 'style' => 'float:right','onclick' => "alerta_gravacao({$cliente['ClienteDe']['Cliente']['codigo']},{$cliente['ClientePara']['Cliente']['codigo']});")); ?>
	    	<span class="label label-info">PARA</span><br>
	        <strong>Código: </strong><?= $cliente['ClientePara']['Cliente']['codigo'] ?>
	        <strong>Cliente: </strong><?= $cliente['ClientePara']['Cliente']['razao_social'] ?>
		</div>
	<?php else : ?>
		<div class="alert alert-warning">Cliente selecionado não possui assinatura!</div>
	<?php endif; ?>

<?php endif ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() {
		setup_mascaras();
	});

	function alerta_gravacao(codigo_cliente_de, codigo_cliente_para){
		swal({
		  title: 'Atenção!',
		  text: 'Deseja copiar a assinatura do cliente ' + codigo_cliente_de + ' para o cliente ' + codigo_cliente_para + '?',
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Gravar Assinatura'
		}, function(result){
		 	if (result) {
				$('#ClienteProdutoServico2AssinaturaClienteParaClienteForm').submit();
			}
		});
	};

")
?>