<style type="text/css">
	.ui-datepicker-trigger{
		cursor: pointer;
	}

	.modal-backdrop.fade.in{
		z-index: 1500;
	}
</style>

<?php echo $this->BForm->create('Grupo', array('url' => array('controller' => 'cronogramas_acoes', 'action' => 'editar', $codigo_cliente_matriz, $codigo_cliente_unidade))); ?>

<div class="well">
	<div class="row-fluid">
		<div class="span4">
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Codigo do cliente:</label>
					<span><strong><?php echo $data_cliente[0]['codigo'] ?></strong></span>
				</div>	
			</div>
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Unidade:</label>
					<span><strong><?php echo $data_cliente[0]['razao_social'] ?></strong></span>
				</div>	
			</div>
			<div class="row-fluid">
				<div class="span12">
					<label>Bairro:</label>
					<span><strong><?php echo $data_cliente[0]['bairro'] ?></strong></span>
				</div>	
			</div>
		</div>
		<div class="span4">
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Cidade:</label>
					<span><strong><?php echo $data_cliente[0]['cidade'] ?></strong></span>
				</div>	
			</div>	
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Estado:</label>
					<span><strong><?php echo $data_cliente[0]['estado'] ?></strong></span>
				</div>	
			</div>
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Qnt Funcionarios:</label>
					<span><strong><?php echo $data_cliente[0]['quantidade_funcionario'] ?></strong></span>
				</div>
			</div>
		</div>
		<div class="span4">
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Data de início de vigência:</label>
					<span>
					<strong class="data-inicio-vigencia">
						<?php echo (AppModel::dbDateToDate($data_cliente[0]['data_inicio_vigencia']) ?: '-');?>
					</strong>
				</div>	
			</div>
			<div class="row-fluid">
				<div class="span12">
					<label></label>
					<span>
						<strong >

						</strong>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="text-right margin-bottom-10">
	<button type="button" class="btn btn-success js-add-acao" data-toggle="tooltip" title="Adidionar nova ação"><i class="icon-plus icon-white"></i></button>
</div>

<?php if(!empty($this->data['CronogramaAcao']) && is_array($this->data['CronogramaAcao'])) { ?>
	<?php foreach ($this->data['CronogramaAcao'] as $key => $dado) { ?>
		<div class="well">
            <?php echo $this->BForm->hidden('CronogramaAcao.'.$key.'.codigo_cliente_matriz', array('value' => $codigo_cliente_matriz)); ?>
            <?php echo $this->BForm->hidden('CronogramaAcao.'.$key.'.codigo_cliente_unidade', array('value' => $codigo_cliente_unidade)); ?>
            <div class="row-fluid">
				<div class="span3">
					<?php echo $this->BForm->input('CronogramaAcao.'.$key.'.responsavel', array('type' => 'text', 'label' => 'Responsável:')); ?>
				</div>
				<div class="span3">
					<?php echo $this->BForm->input('CronogramaAcao.'.$key.'.data_inicial', array('type' => 'text', 'label' => 'Data inicial (conclusão da ação):', 'class' => 'data input-small')); ?>
				</div>	
				<div class="span3">
					<?php echo $this->BForm->input('CronogramaAcao.'.$key.'.data_final', array('type' => 'text', 'label' => 'Data final (conclusão da ação):', 'class' => 'data input-small')); ?>
				</div>
				<div class="span3">
					<?php echo $this->BForm->input('CronogramaAcao.'.$key.'.status', array('label' => 'Status:', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span3">
					<?php echo $this->BForm->input('CronogramaAcao.'.$key.'.codigo_setor', array('label' => '* Setor:', 'empty' => 'Selecione..', 'options' => $data_setores, 'required' => true)); ?>
				</div>
				<div class="span6">
					<?php echo $this->BForm->input('CronogramaAcao.'.$key.'.codigo_tipo_acao', array('label' => '* Ação:', 'empty' => 'Selecione..', 'options' => $data_tipo_acoes, 'required' => true)); ?>
				</div>
				<div class="span3 padding-top-35 text-right">
					<button type="button" class="btn btn-danger js-remove-acao" data-toggle="tooltip" title="Remove esta ação"><i class="icon-minus icon-white"></i></button>
				</div>
			</div>	
		</div>
	<?php } ?>
<?php } ?>

<div class="js-added-actions">
</div>

<div class="form-actions">
	<?php echo $this->BForm->button('Salvar', array('class' => 'btn btn-primary')) ?>
	<?php echo $this->Html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_pcmso', $codigo_cliente_matriz), array('class' => 'btn btn-default')) ?>
</div>

<div class="memory hide">
	<div class="well">
        <?php echo $this->BForm->hidden('CronogramaAcao.Xx.codigo_cliente_matriz', array('value' => $codigo_cliente_matriz, 'disabled' => true)); ?>
        <?php echo $this->BForm->hidden('CronogramaAcao.Xx.codigo_cliente_unidade', array('value' => $codigo_cliente_unidade, 'disabled' => true)); ?>
		<div class="row-fluid">
			<div class="span3">
				<?php echo $this->BForm->input('CronogramaAcao.Xx.responsavel', array('type' => 'text', 'label' => 'Responsável:', 'disabled' => true)); ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('CronogramaAcao.Xx.data_inicial', array('type' => 'text', 'label' => 'Data inicial (conclusão da ação):', 'class' => 'data-class input-small', 'disabled' => true)); ?>
			</div>	
			<div class="span3">
				<?php echo $this->BForm->input('CronogramaAcao.Xx.data_final', array('type' => 'text', 'label' => 'Data final (conclusão da ação):', 'class' => 'data-class input-small', 'disabled' => true)); ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('CronogramaAcao.Xx.status', array('label' => 'Status:', 'options' => array(1 => 'Ativo', 0 => 'Inativo'), 'disabled' => true)); ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3">
				<?php echo $this->BForm->input('CronogramaAcao.Xx.codigo_setor', array('label' => '* Setor:', 'empty' => 'Selecione..', 'options' => $data_setores, 'disabled' => true, 'required' => true)); ?>
			</div>
			<div class="span6">
				<?php echo $this->BForm->input('CronogramaAcao.Xx.codigo_tipo_acao', array('label' => '* Ação:', 'empty' => 'Selecione..', 'options' => $data_tipo_acoes, 'disabled' => true, 'required' => true)); ?>
			</div>
			<div class="span3 padding-top-35 text-right">
				<button type="button" class="btn btn-danger js-remove-acao" data-toggle="tooltip" title="Remove esta ação"><i class="icon-minus icon-white"></i></button>
			</div>
		</div>	
	</div>	
</div>

<?php echo $this->BForm->end(); ?>

<script type="text/javascript">
	$(document).ready(function() {
        setup_datepicker();

        var i = '<?php echo ((isset($key))? ($key+1) : 0) ?>'
		$('body').on('click', '.js-add-acao', function(event) {
			var html = $('.memory').html().replace(/Xx/g, i).replace(/disabled="disabled"/g, '').replace(/data-class/g, 'data');
			$('.js-added-actions').append(html);
			$('[data-toggle="tooltip"]').tooltip();
			i++;
			setup_datepicker(); 
		});

		$('body').on('click', '.js-remove-acao', function() {
			$(this).parents('.well').remove();
			i--;
		});

	});
</script>