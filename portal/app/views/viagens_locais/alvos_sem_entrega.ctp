<?php 
	if(isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export'){
		header('Content-type: application/vnd.ms-excel'); 
	    header(sprintf('Content-Disposition: attachment; filename="%s"', basename('alvos_sem_entrega.csv')));
	 	header('Pragma: no-cache');

		echo iconv('UTF-8', 'ISO-8859-1', '"Alvo";"Qtd. SMs";'."\n");
	    
	    foreach($dados as $dado){
	    	$linha  = '"'.$dado[0]['refe_descricao'].'";'; 
	    	$linha .= '"'.$dado[0]['qtd_viag_codigo_sm'].'";'; 
	 		$linha .= "\n";
	        echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
	    }
	 	exit;
 }
 ?>
	<div class='well'>
	<?php echo $this->BForm->create('TVlocViagemLocal', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens_locais', 'action' => 'alvos_sem_entrega'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_periodo($this, 'TVlocViagemLocal') ?>
			<?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'TVlocViagemLocal') ?>
		</div>
		<div class="row-fluid inline">
		    <span class="label label-info">Classe Alvos</span>
            <span class='pull-right'>
                <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("classe_referencia")')) ?>
                <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("classe_referencia")')) ?>
            </span>
            <div id='classe_referencia'>
				<?php echo $this->BForm->input('cref_codigo', array('label'=>false, 'options'=>$classes_referencia, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
			</div>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
</div>
<?php if (!empty($dados)): ?>
	<div class="well">
		<span class="pull-right">
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
		</span>
	</div>
	<table class='table table-striped'>
		<thead>
			<th>Alvo</th>
			<th class='numeric'>Qtd.SMs</th>
		</thead>
		<tbody>
			<?php $total_alvos = 0 ?>
			<?php $total_sms = 0 ?>
			<?php foreach ($dados as $dado): ?>
				<?php $total_alvos ++ ?>
				<?php $total_sms += $dado[0]['qtd_viag_codigo_sm'] ?>
				<tr>
					<td><?= $dado[0]['refe_descricao'] ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado['0']['qtd_viag_codigo_sm'], array('places' => 0, 'nozero' => true)) ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td class='numeric'><?= $this->Buonny->moeda($total_alvos, array('places' => 0, 'nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($total_sms, array('places' => 0, 'nozero' => true)) ?></td>
			</tr>
		</tfoot>
	</table>
<?php endif; ?>