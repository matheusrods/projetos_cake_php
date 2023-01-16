<style type="text/css">
	.ui-datepicker-trigger{
		cursor: pointer;
	}

	.modal-backdrop.fade.in{
		z-index: 1500;
	}
</style>

<?php echo $this->BForm->create('Gpra', array('url' => array('controller' => 'prevencao_riscos_ambientais', 'action' => 'editar', $codigo_matriz, $codigo_cliente), 'type' => 'post')); ?>

<?php if(isset($this->data['Gpra']['codigo'])) { ?>
	<?php echo $this->BForm->hidden('codigo'); ?>
<?php } ?>

<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente)); ?>
<?php echo $this->BForm->hidden('data_inicio_vigencia', array('id' => 'DataInicioVigencia')); ?>
<?php echo $this->BForm->hidden('codigo_medico', array('value' => $this->data['Gpra']['codigo_medico'])); ?>

<div class="well">
	<div class="row-fluid">
		<div class="span4">
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Codigo do cliente:</label>
					<span><strong><?php echo $codigo_cliente ?></strong></span>
				</div>	
			</div>
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Unidade:</label>
					<span><strong><?php echo $dados_cliente['Cliente']['razao_social'] ?></strong></span>
				</div>	
			</div>
			<div class="row-fluid">
				<div class="span12">
					<label>Bairro:</label>
					<span><strong><?php echo $dados_cliente['ClienteEndereco']['bairro'] ?></strong></span>
				</div>	
			</div>
		</div>
		<div class="span4">
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Cidade:</label>
					<span><strong><?php echo $dados_cliente['ClienteEndereco']['cidade'] ?></strong></span>
				</div>	
			</div>	
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Estado:</label>
					<span><strong><?php echo $dados_cliente['ClienteEndereco']['estado_descricao'] ?></strong></span>
				</div>	
			</div>
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Qnt Funcionarios:</label>
					<span><strong><?php echo $dados_cliente['Cliente']['qnt_func'] ?></strong></span>
				</div>
			</div>
		</div>
		<div class="span4">
			<div class="row-fluid">
				<div class="span12 margin-bottom-10">
					<label>Data de início de vigência:</label>
					<span>
					<strong class="data-inicio-vigencia">
						<?php if(!empty($this->data['Gpra']['data_inicio_vigencia'])) echo $this->data['Gpra']['data_inicio_vigencia']; ?>
					</strong>
				</div>	
			</div>
			<div class="row-fluid">
				<div class="span12">
					<label>Profissional responsável:</label>
					<span>
						<strong >
							<?php echo $profissionais[$this->data['Gpra']['codigo_medico']]; ?>
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

<?php if(!empty($this->data['PrevencaoRiscoAmbiental'])) { ?>
	<?php foreach ($this->data['PrevencaoRiscoAmbiental'] as $key => $dado) { ?>
		<div class="well">
			<div class="row-fluid">
				<div class="span3">
					<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.'.$key.'.responsavel', array('label' => 'Responsável:')); ?>
				</div>
				<div class="span3">
					<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.'.$key.'.data_inicial', array('type' => 'text', 'label' => 'Data inicial (conclusão da ação):', 'class' => 'data input-small')); ?>
				</div>	
				<div class="span3">
					<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.'.$key.'.data_final', array('type' => 'text', 'label' => 'Data final (conclusão da ação):', 'class' => 'data input-small')); ?>
				</div>
				<div class="span3">
					<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.'.$key.'.status', array('label' => 'Status:', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span3">
					<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.'.$key.'.codigo_setor', array('label' => '*Setor:', 'empty' => 'Selecione', 'options' => $setores, 'required' => true)); ?>
				</div>
				<div class="span6">
                    <?php if(!empty($this->data['PrevencaoRiscoAmbiental'][$key]['acao'])) : ?>
					<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.'.$key.'.acao', array('label' => 'Ação (antigo):', 'class' => 'input-xxlarge', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
                    <?php endif; ?>
				</div>
			</div>
            <div class="row-fluid">
                <div class="span9">
                    <?php echo $this->BForm->input('PrevencaoRiscoAmbiental.'.$key.'.codigo_tipo_acao', array('label' => '* Ação:', 'empty' => 'Selecione', 'options' => $data_tipo_acoes, 'required' => true, 'class' => 'input-xxlarge')); ?>
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
	<?php echo $this->Html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'gerenciar_ppra', $codigo_matriz), array('class' => 'btn btn-default')) ?>
</div>

<div class="memory hide">
	<div class="well">
		<div class="row-fluid">
			<div class="span3">
				<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.Xx.responsavel', array('label' => 'Responsável:', 'disabled' => true)); ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.Xx.data_inicial', array('type' => 'text', 'label' => 'Data inicial (conclusão da ação):', 'class' => 'data-class input-small', 'disabled' => true)); ?>
			</div>	
			<div class="span3">
				<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.Xx.data_final', array('type' => 'text', 'label' => 'Data final (conclusão da ação):', 'class' => 'data-class input-small', 'disabled' => true)); ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.Xx.status', array('label' => 'Status:', 'options' => array(1 => 'Ativo', 0 => 'Inativo'), 'disabled' => true)); ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3">
				<?php echo $this->BForm->input('PrevencaoRiscoAmbiental.Xx.codigo_setor', array('label' => '*Setor:', 'empty' => 'Selecione', 'options' => $setores, 'disabled' => true, 'required' => true)); ?>
			</div>
            <div class="span6">
                <?php echo $this->BForm->input('PrevencaoRiscoAmbiental.Xx.codigo_tipo_acao', array('label' => '* Ação:', 'empty' => 'Selecione', 'options' => $data_tipo_acoes, 'disabled' => true, 'required' => true, 'class' => 'input-xxlarge')); ?>
            </div>
            <div class="span3 padding-top-35 text-right">
                <button type="button" class="btn btn-danger js-remove-acao" data-toggle="tooltip" title="Remove esta ação"><i class="icon-minus icon-white"></i></button>
            </div>
        </div>
	</div>	
</div>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_datepicker(); 
	});
	') ?>


<script type="text/javascript">
	$(document).ready(function() {
		
		
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