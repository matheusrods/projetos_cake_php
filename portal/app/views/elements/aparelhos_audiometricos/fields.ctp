<div class='.well'>	
	<?php
	if(isset($data['codigo'])){
		echo $this->BForm->hidden('codigo', array('value' =>  !empty($data['codigo'])? $data['codigo'] : '') );
	}
	if(isset($data['codigo_cliente'])){
		// debug('opa');
        echo $this->BForm->hidden('codigo_cliente', array('value' =>  !empty($data['codigo_cliente'])? $data['codigo_cliente'] : '')); 
    } 
	?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('descricao', array('label' => 'Nome do Aparelho (*)', 'class' => 'input-xxlarge')); ?>	
		<?php if(empty($this->passedArgs)): ?>
			<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
		<?php else: ?>
			<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
		<?php endif;  ?>
	</div>  	
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('fabricante', array('label' => 'Fabricante', 'class' => 'input-xxlarge')); ?>	
	</div>  

	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('data_afericao', array('label' => 'Data de Aferição (*)', 'class' => 'input data', 'type'=>'text')); ?>
		<?php echo $this->BForm->input('data_proxima_afericao', array('label' => 'Data da Próxima Aferição', 'class' => 'input data', 'type'=>'text')); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('empresa_afericao', array('label' => 'Empresa Aferição', 'class' => 'input-xxlarge')); ?>
	</div>
	<div class='row-fluid'>
		<?php echo $this->BForm->input('aparelho_padrao', array('type' => 'checkbox', 'label' => 'Aparelho Padrão', 'class' => 'checkbox inline input-xlarge')); ?>
		<?php echo $this->BForm->input('disponivel_empresas', array('type' => 'checkbox', 'label' => 'Disponível para todas as empresas', 'class' => 'checkbox inline input-xlarge')); ?>
		<?php echo $this->BForm->input('resultado_multiplo_5', array('type' => 'checkbox', 'label' => 'Resultado em valores múltiplos de 5', 'class' => 'checkbox inline input-xlarge')); ?>
	</div>

	<div class='row-fluid'>
		<?php echo $this->BForm->input('codigo_unidade', array('label' => 'Unidades', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'TODAS','options' => $unidades)); ?> 
	</div>
	
	<?php echo $this->BForm->hidden('AparelhoAudioResultado.codigo', array('value' =>  !empty($this->data['AparelhoAudioResultado']['codigo'])? $this->data['AparelhoAudioResultado']['codigo'] : '')); ?>

	<div class='row-fluid inline'>
		<h5>Limite de Resposta do Audiômetro</h5>
		<table class="text-center table table-striped">
	        <thead>
	            <tr>
		            <th class="input-large">KHz</th>
		            <th class="input-mini">.25</th>
		            <th class="input-mini">.50</th>
		            <th class="input-mini">1</th>
		            <th class="input-mini">2</th>
		            <th class="input-mini">3</th>
		            <th class="input-mini">4</th>
		            <th class="input-mini">6</th>
		            <th class="input-mini">8</th>
	            </tr>
	        </thead>
	        <tbody>
	        	<tr>
	                <td class="input-large">Ausência de Resposta V.A. em (dB)</td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_va_025khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_va_050khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_va_1khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_va_2khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_va_3khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_va_4khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_va_6khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_va_8khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	            </tr>
	            <tr>
	                <td class="input-large">Ausência de Resposta V.O. em (dB)</td>
	                <td>&nbsp;</td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_vo_050khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_vo_1khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_vo_2khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_vo_3khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td><?=$this->BForm->input('AparelhoAudioResultado.ausencia_resposta_vo_4khz', array('label' => false, 'class' => 'input-mini')); ?></td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr>
	        </tbody>
		</table>
	</div>
</div>
<?php echo $javascript->codeblock(
	'jQuery(document).ready(function() { 
		setup_mascaras(); 
		setup_datepicker(); 
		setup_time();
	});'); ?>