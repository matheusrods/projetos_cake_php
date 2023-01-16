<?php 
    // echo $paginator->options(array('update' => 'div.lista')); 
    // $total_paginas = $this->Paginator->numbers();
?>
<?php if(!empty($fornecedores)):?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th>CNPJ</th>
            <th>Estado</th>
            <th>Cidade</th>
            <th style="width:60px;"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fornecedores as $fornecedor): ?>
	        <tr>
	            <td class="input-mini"><?= $fornecedor['Fornecedor']['codigo'] ?></td>
	            <td><?php echo $fornecedor['Fornecedor']['razao_social'] ?></td>
	            <td><?php echo $fornecedor['Fornecedor']['nome'] ?></td>
	            <td><?php echo $buonny->documento($fornecedor['Fornecedor']['codigo_documento']) ?></td>
	            <td><?php echo $fornecedor['FornecedorEndereco']['estado_descricao'] ?></td>
	            <td><?php echo $fornecedor['FornecedorEndereco']['cidade'] ?></td>
	            <td style="width:60px; text-align: center;">
					<?= $html->link('', array('action' => 'agenda_por_exame', $fornecedor['Fornecedor']['codigo']), array('class' => 'icon-wrench', 'title' => 'Editar')) ?>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Js->writeBuffer(); ?>