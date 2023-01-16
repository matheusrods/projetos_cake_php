<?php
    if( !empty($profissional_log)) :
        echo $paginator->options(array('update' => 'div.lista'));
        $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?=$this->Paginator->sort('Nome', 'nome') ?></th>
            <th><?=$this->Paginator->sort('Cod. Documento', 'codigo_documento') ?></th>
            <th><?=$this->Paginator->sort('Usuário alteração', 'apelido') ?></th>
            <th><?=$this->Paginator->sort('Data alteração', 'data_inclusao') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($profissional_log as $profissional): ?>
        <tr>
            <td><?=$profissional['ProfissionalLog']['nome'] ?></td>            
            <td><?=$buonny->documento($profissional['ProfissionalLog']['codigo_documento']) ?></td>
            <td><?=$profissional['Usuario']['apelido'] ?></td>
            <td><?=$profissional['ProfissionalLog']['data_inclusao'] ?></td>
            <td><?=$html->link('', array('controller' => 'profissionais_logs', 'action' => 'visualizar_profissional_log', $profissional['ProfissionalLog']['codigo']), array('class' => 'icon-eye-open', 'title' => 'Visualizar')) ?></td>
        </tr>
        <?php endforeach; ?>
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
<?php endif;?>