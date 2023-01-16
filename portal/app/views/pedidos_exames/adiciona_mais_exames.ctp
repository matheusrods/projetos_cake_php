	    <table class="table table-striped" style="border: 2px solid #EFEFEF;">
	        <thead>
	            <tr>
		            <th class="input-xxlarge">Exame</th>
		            <th class="input-xlarge">Tipo</th>
		            <th class="input-small" style="text-align: center;">Retirar</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php foreach ($dados_exames as $key => $exame): ?>
            		<?php if(!isset($exame['valor'])): ?>
						<?php $color = '#FFDBDB'; ?>
						<?php $msg_assinatura = '<div class="help-block error-message" style="font-size:12px; color:#b94a48;">Exame não tem ASSINATURA NO CONTRATO!</div>'; ?>
					<?php elseif(empty($exame['fornecedores'])): ?>
						<?php $color = '#FFDBDB'; ?>
						<?php $msg_assinatura = '<div class="help-block error-message" style="font-size:12px; color:#b94a48;">Exame não tem CREDENCIADO!</div>'; ?>
					<?php else:  ?>
						<?php $color = ''; ?>
						<?php $msg_assinatura = ''; ?>
					<?php endif; ?>	  
		            <tr>
		                <td class="input-xlarge" style="background: <?php echo $color; ?>;">
		                	<?php echo $key; ?> - <?php echo $exame['descricao'].' '.$msg_assinatura; ?>
		                </td>
		                <td class="input-xlarge" style="background: <?php echo $color; ?>;">
		                	<?php if(isset($exame['tipo']) && ($exame['tipo'] == '1')) : ?>
		                		<?php echo $this->BForm->input('codigo_tipos_exames_pedidos', array('label' => false, 'class' => 'form-control uf input-large', 'style' => 'text-transform: uppercase;', 'default' => (isset($exame['tipo']) && $exame['tipo'] ? $exame['tipo'] : NULL) , 'options' => $lista_tipos_exames_pcmso, 'disabled' => 'disabled')); ?>
		                	<?php else : ?>
		                		<?php echo $this->BForm->input('codigo_tipos_exames_pedidos', array('label' => false, 'class' => 'form-control uf input-large', 'style' => 'text-transform: uppercase;', 'default' => (isset($exame['tipo']) && $exame['tipo'] ? $exame['tipo'] : '') , 'options' => $lista_tipos_exames_outro, 'onchange' => 'atualiza_tipo(this, '. $key .');')); ?>
		                	<?php endif; ?>
		                </td>
		                <td class="input-medium" style="background: <?php echo $color; ?>; text-align: center;">
		                	<a href="javascript:void(0);" onclick="removeExame(<?php echo $key; ?>, <?php echo $codigo_cliente_funcionario; ?>, this); " class="icon-trash"></a>
		                </td>
		            </tr>		            
	        	<?php endforeach ?>
	    	</tbody>
	    </table>