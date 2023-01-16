<table class='table table-striped'>
	<thead>
		<th>Tecnologia</th>
		<th class="numeric">Percentual Posicionando</th>
		<th class="numeric">Minimo Monitoramento</th>
		<td class="numeric">&nbsp;</td>
	</thead>
	<tbody>
		<?php foreach ($tecnologias as $tecnologia): ?>
			<tr>
				<td><?= $tecnologia['TCtecContaTecnologia']['ctec_descricao'] ?></td>					
				<td class="numeric"><?= $tecnologia['TCtecContaTecnologia']['ctec_percentual_posicionando'] ?></td>					
				<td class="numeric"><?= $tecnologia['TCtecContaTecnologia']['ctec_minimo_monitoramento'] ?></td>					
				<td>
					<?php echo $html->link('', array('controller' => 'StatusTecnologias', 'action' => 'editar_configuracao',$tecnologia['TCtecContaTecnologia']['ctec_codigo']), array('class' => 'icon-edit', 'title' => 'Alterar configuração')); ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>		
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
