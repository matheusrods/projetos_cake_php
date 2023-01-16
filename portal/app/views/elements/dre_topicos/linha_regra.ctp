<tr class='tablerow-input <?php echo $modelo ? 'regra-modelo' : ''; ?>'>
	<td><?php echo $this->Form->input('DreTopicoRegra.'.$indice.'.ccusto', array('options'=>$ccusto, 'label'=>false, 'value'=>$ccusto_codigo, 'class'=>'ccusto', 'empty'=>'Selecione um Centro de Custo')); ?></td>
	<td><?php echo $this->Form->input('DreTopicoRegra.'.$indice.'.grflux', array('options'=>$grflux, 'label'=>false, 'value'=>$grflux_codigo, 'class'=>'grupo', 'empty'=>'Selecione um Grupo', 'onchange'=>'carregaSubgrupo(this)')); ?></td>
	<td>
		<?php echo $this->Form->hidden('DreTopicoRegra.'.$indice.'.sbflux_hidden', array('value'=>$sbflux_codigo)); ?>
		<?php echo $this->Form->input('DreTopicoRegra.'.$indice.'.sbflux', array('options'=>array(), 'label'=>false, 'value'=>$sbflux_codigo, 'class'=>'subgrupo', 'empty'=>'Selecione um Subgrupo')); ?>
	</td>
	<td><?php echo $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)',array('class' => 'btn', 'escape' => false, 'onclick' => "remove_regra(jQuery(this).parent().parent())")); ?></td>
</tr>