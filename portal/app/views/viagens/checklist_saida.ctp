<?php echo $this->BForm->create('TViagViagem', array('action' => 'post', 'url' => array('controller' => 'Viagens','action' => 'checklist',$cliente['Cliente']['codigo'],$viag_codigo)));?>

<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?php echo $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social'] ?>
		<strong>PLACA: </strong><?php echo $viagem['TVeicVeiculo']['veic_placa'] ?>&nbsp;&nbsp;
		<strong>Tipo: </strong><?php echo $viagem['TTveiTipoVeiculo']['tvei_descricao'] ?>&nbsp;&nbsp;

		<?php $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour')); ?>
		<?php if($viagem && date('Y-m-d H:i:s',Comum::dateToTimestamp($viagem['TUposUltimaPosicao']['upos_data_comp_bordo'])) >= $data_upos): ?>
			<span class="badge-empty badge badge-success" title="Posicionamento Normal"></span>
		<?php else: ?>
			<span class="badge-empty badge badge-empty" title="Sem Posicionamento"></span>
		<?php endif; ?>
	</div>
		
	<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM', 'readonly' => true)) ?>
	<?php echo $this->BForm->input('loadplans', array('class' => 'input-xxlarge', 'label' => 'LoadPlans', 'readonly' => true, 'value' => $this->data[0]['loadplans'])) ?>
	<?php echo $this->BForm->input('viag_valor_carga', array('class' => 'input-medium', 'label' => 'Valor total da Carga', 'readonly' => true)) ?>
	<?php echo $this->BForm->input('notasfiscais', array('class' => 'input-xxlarge', 'label' => 'Nota fiscal', 'readonly' => true,'value' => $this->data[0]['notasfiscais'])) ?>
	
	<?php echo $this->BForm->hidden('TRefeReferencia.refe_codigo') ?>
	<?php echo $this->BForm->input('TRefeReferencia.refe_descricao',array('readonly'=>true,'label'=>'CD')) ?>
	<? if (!empty($this->data['TCmatChecklistMotivoAtraso']['cmat_codigo'])) : ?>
		<?php echo $this->BForm->hidden('TCmatChecklistMotivoAtraso.cmat_codigo') ?>
		<?php echo $this->BForm->input('TCmatChecklistMotivoAtraso.cmat_descricao',array('readonly'=>true,'label'=>'Motivo de Atraso','class'=>'input-xxlarge')) ?>
	<? endif; ?>
</div>

<h4>Motorista</h4>
<div class="well">
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('Cliente.codigo',array('value' => $cliente['Cliente']['codigo'])) ?>
		<?php echo $this->BForm->hidden('Cliente.codigo_documento',array('value' => $cliente['Cliente']['codigo_documento'])) ?>
		<?php echo $this->BForm->hidden('Cliente.iniciar_por_checklist',array('value' => $cliente['Cliente']['iniciar_por_checklist'])) ?>
		<?php echo $this->BForm->hidden('TVeicVeiculo.veic_placa') ?>
		<?php echo $this->BForm->hidden('TViagViagem.viag_codigo') ?>
		<?php echo $this->BForm->hidden('TViagViagem.viag_codigo_sm') ?>
		<?php echo $this->BForm->hidden('TVveiViagemVeiculo.vvei_codigo') ?>
		<?php echo $this->BForm->hidden('TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo') ?>
		<?php echo $this->BForm->input('TPfisPessoaFisica.pfis_cpf', array('class' => 'input-medium formata-cpf', 'label' => 'CPF', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('TPessPessoa.pess_nome', array('class' => 'input-xlarge', 'label' => 'Nome', 'readonly' => true)) ?>
	</div>
	<div class='row-fluid-inline'>
	<?php echo $this->BForm->input('ProfissionalContato.telefone', array('value' => (isset($profissional_celular) ? $profissional_celular : ''), 'class' => 'input-medium', 'label' => 'Celular', 'readonly' => true)) ?>
	<?php echo $this->BForm->hidden('ProfissionalContato.telefone_atual', array('value' => (isset($profissional_celular) ? $profissional_celular : ''))) ?>
	</div>
</div>
	<br>
<?php echo $this->BForm->end() ?>
	<?php echo $html->link('Voltar', '#', array('class' => 'btn closeDialog', 'onclick' => 'window.close();')); ?>

<?php echo $this->Javascript->codeBlock('

	$(document).ready(function(){
		setup_mascaras();
	});', false);
?>