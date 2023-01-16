<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?= $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?= $this->Paginator->sort('Nome', 'nome') ?></th>
            <th colspan="2"><?= $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($corretoras as $corretora): ?>
        <tr>
            <td class="input-mini">
				<?= $corretora['Corretora']['codigo'] ?>
			</td>
            <td>
				<?= $corretora['Corretora']['nome'] ?>
			</td>
            <td>
				<?= $buonny->documento($corretora['Corretora']['codigo_documento']) ?>
			</td>
            <td class="pagination-centered">
				<?php 
				if(!$corretora[0]['acesso']) {
					echo $this->BMenu->linkOnClick('', 
								array('controller' => 'corretoras', 'action' => 'criar_acesso', $corretora['Corretora']['codigo']), 
								array('escape' => false, 'class' => 'icon-upload', 'title' => 'Criar Acesso Sitema de Vendas', 'onclick' => "return open_dialog(this, 'Exportar Corretora', 600)")); 
				}
				?>
				<?php if($destino == "usuarios"): ?>
					<?= $html->link('', array('controller' => 'usuarios', 'action' => 'por_corretora', $corretora['Corretora']['codigo']), array('class' => 'icon-wrench', 'title' => 'Usuários da Corretora')) ?>
				<?php else: ?>	
					<?= $html->link('', array('action' => 'editar', $corretora['Corretora']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
				<?php endif; ?>
			</td>
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