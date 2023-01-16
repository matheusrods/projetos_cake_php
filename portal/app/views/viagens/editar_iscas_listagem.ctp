<?php if(!empty($sm) && $permissao):?>
	<div class='actionbar-right'>
		<?= $html->link('Incluir', array('action' => 'incluir_iscas_sm',$sm['TViagViagem']['viag_codigo_sm']), array('onclick' => 'return open_dialog(this, "Incluir Iscas", 500)', 'class' => 'btn btn-success', 'title' => 'Incluir Iscas') );?>
	</div>
	<br/>
	<div class="well">
	<strong>SM : </strong><?= $sm['TViagViagem']['viag_codigo_sm']?>
	<strong> Transportador : </strong><?= $transportador['TPjurPessoaJuridica']['pjur_razao_social']?> 
	<?php if(!empty($embarcador)): ?>
		<strong> Embarcador : </strong><?= $embarcador['TPjurPessoaJuridica']['pjur_razao_social']?> 
	<?php endif;?>
	</div>
	<?php if(!empty($terminais)):?>
		<table class='table table-striped table-bordered' style='max-width:none;white-space:nowrap'>
			<thead>
				<th>Tecnologia</th>
				<th>Terminal</th>
				<th class="input-mini numeric">&nbsp;</th>
			</thead>
			<tbody>
				<?php foreach ($terminais as $key => $terminal):?>
					<tr>
						<td><?= $terminal['TTecnTecnologia']['tecn_descricao']?></td>
						<td><?= $terminal['TTermTerminal']['term_numero_terminal']?></td>
						 <td class="numeric">
						 	<?php if($terminal['TVterViagemTerminal']['vter_precedencia'] != 11):?>
		   						<?= $html->link('', array('action' => 'editar_iscas_sm',$sm['TViagViagem']['viag_codigo_sm'],$terminal['TVterViagemTerminal']['vter_codigo']), array('onclick' => 'return open_dialog(this, "Editar Iscas", 500)', 'class' => 'icon-edit', 'title' => 'Editar Iscas') );?>
		   						<?= $this->Html->link('', array('action' => 'excluir_iscas_sm',$terminal['TVterViagemTerminal']['vter_codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash'), 'Confirma exclusão?') ?>
	   						<?php endif;?>
	   					</td>
					</tr>	
				<?php endforeach ?>
			</tbody>
		</table>
	<?php else:?>
		<div class="alert alert-warning">SM não possui iscas</div>
	<?php endif;?>		
<?php else:?>
	<div class="alert alert-warning">SM finalizada ou não existe para este cliente</div>
<?php endif;?>	