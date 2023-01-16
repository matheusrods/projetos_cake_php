<?php echo $this->BForm->create('TOveiOcorrenciaVeiculo', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'veiculos', 'action' => 'solicitar_checklist'))); ?>
	
<?php if(!isset($ultimoChecklist)): ?>
	<div class="row-fluid inline">
		<?php if($authUsuario['Usuario']['codigo_cliente']): ?>
			<div class='well'>
				<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
				<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
			</div>
		<?php else: ?>
	    	<?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',true,'TOveiOcorrenciaVeiculo'); ?>
	    <?php endif; ?>
	    <?php echo $this->BForm->input('placa',array('class' => 'placa-veiculo input-mini')); ?>
    </div>
   	<?php echo $this->BForm->submit('Carregar', array('div' => false, 'class' => 'btn btn-primary')); ?>
<?php else: ?>
	<?php echo $this->BForm->hidden('codigo_cliente'); ?>
	<?php echo $this->BForm->hidden('placa'); ?>
	<?php echo $this->BForm->hidden('solicitar_checklist',array('value' => true)); ?>
	<div class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		<strong>Placa: </strong><?= Comum::formatarPlaca($dados['veiculo']['TVeicVeiculo']['veic_placa']) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('TCveiUltimoChecklist.cvei_data_cadastro',array('label' => 'Último Checklist','class' => 'input-medium','readonly' => true)); ?>
		<?php echo $this->BForm->input('TCveiUltimoChecklist.data_vencimento',array('label' => 'Vencimento do Checklist','class' => 'input-medium','readonly' => true)); ?>
		<div class="control-group input text">
			<label>&nbsp;</label>
			<?php if($vencimento == 1): ?>
				<span class="badge-empty badge badge-success" title="Checklist válido"></span>
			<?php elseif($vencimento == 2): ?>
				<span class="badge-empty badge badge-important" title="Checklist inválido"></span>
			<?php else: ?>
				<span class="badge-empty badge" title="Sem checklist"></span>
			<?php endif; ?>
		</div>

		<div class="control-group input text">
			<label>&nbsp;</label>
			<?php if($estatus == 1): ?>
				<span class="badge-empty badge badge-success" title="Checklist aprovado"></span>
			<?php elseif($estatus == 2): ?>
				<span class="badge-empty badge badge-important" title="Checklist reprovado"></span>
			<?php else: ?>
				<span class="badge-empty badge" title="Sem checklist"></span>
			<?php endif; ?>
		</div>
		<?php if(isset($sem_vppj)): ?>
			<div class="control-group error">
				<label>&nbsp;</label><div class="help-block"><?php echo $sem_vppj ?></div>
			</div>
		<?php endif; ?>
	</div>
   	<?php echo $this->Html->link('Solicitar', array(), array('onclick' => "return solicitar();", 'class' => 'btn btn-primary')); ?>
	<?php echo $this->Html->link('Voltar', array('controller' => 'veiculos','action' => 'solicitar_checklist'), array('class' => 'btn', 'escape' => false )); ?>
<?php endif; ?>
<?php 
	echo $this->Javascript->codeBlock("
		$(document).ready(function(){
			setup_mascaras();
		});		
	");
?>
<?php 
if(isset($dados) && !empty($dados)){
	echo $this->Javascript->codeBlock("
		function solicitar(){
			if(confirm('Deseja solicitar checklist para o veículo {$dados['veiculo']['TVeicVeiculo']['veic_placa']}')){
				$('#TOveiOcorrenciaVeiculoSolicitarChecklistForm').submit();
			}
			return false;
		}
	");
} ?>