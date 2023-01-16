<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<div class='actionbar-right'>
	<?= $html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Nova Proposta')) ?>
</div>
<br/>
<?php if(!empty($eventos_compostos)):?>
	<table class="table table-striped">
		<thead>
			<th>Evento</th>
			<th>Cliente</th>
			<th>Sequencial</th>
			<th class="numeric">Abrangência (Minutos)</th>
			<th>&nbsp;</th>
		</thead>
		<tbody>
			<?php foreach ($eventos_compostos as $key => $evento_composto) :?>
				<tr>
					<td><?= $evento_composto['TEcomEventoComposto']['ecom_descricao']?></td>
					<td><?= DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($evento_composto['TEcomEventoComposto']['ecom_pess_oras_codigo']).' - '.$evento_composto['TPjurPessoaJuridica']['pjur_razao_social']?></td>
					<td><?= ($evento_composto['TEcomEventoComposto']['ecom_sequencial'] == 'S') ? 'Sim' : 'Não'; ?></td>
					<td class="numeric"><?= $evento_composto['TEcomEventoComposto']['ecom_minutos_abrangencia'] ?></td>
					<td class="numeric">
						<?= $this->Html->link('', array('action' => 'visualizar',$evento_composto['TEcomEventoComposto']['ecom_codigo'] , rand()), array('title' => 'Visualizar', 'class' => 'icon-eye-open')) ?>
						<?php
							//se o status atual for ativo gera a opcao para inativar e quando inativo para ativar
							if($evento_composto['TEcomEventoComposto']['ecom_status'] == 'S'):?>
								<?= $this->Html->link('', array('action' => 'editar',$evento_composto['TEcomEventoComposto']['ecom_codigo'] , rand()), array('title' => 'Editar', 'class' => 'icon-edit')) ?>
							    <?= $this->Html->link('', array('action' => 'inativar_ativar',$evento_composto['TEcomEventoComposto']['ecom_codigo'],'N' , rand()), array('title' => 'Inativar', 'class' => 'icon-random'));?>
								<span class="badge badge-empty badge-success" title="Ativo"></span>
							<?php else:?>
								<?= $this->Html->link('', array('action' => 'inativar_ativar',$evento_composto['TEcomEventoComposto']['ecom_codigo'],'S' , rand()), array('title' => 'Ativar', 'class' => 'icon-random'));?>
								<span class="badge badge-empty badge-important" title="Inativo"></span>
							<?php endif;?>					
					</td>
				</tr>		
			<?php endforeach;?>	
		</tbody>
	</table>	
	<div class='row-fluid'>
	    <div class='numbers span6'>
	        <?php 
	        echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	        <?php echo $this->Paginator->numbers(); ?>
	        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	    </div>
	    <div class='counter span6'>
	        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	    </div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
	<div class="alert alert-warning">Não foi encontrado nenhum registro.</div>
<?php endif;?>	