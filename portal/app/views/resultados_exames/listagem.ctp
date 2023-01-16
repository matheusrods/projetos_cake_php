<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<?php if(!empty($resultado_exames)):?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Número Pedido</th>
            <th>Cliente</th>
            <th>Funcionário</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($resultado_exames as $pedido): ?>
        <tr>
            <td><?php echo $pedido['PedidoExame']['codigo'] ?></td>
            <td><?php echo $pedido['Cliente']['razao_social'] ?></td>
            <td><?php echo $pedido['Funcionario']['nome'] ?></td>
            <td>
                <?php 
                if($pedido['PedidoExame']['count'] > 0) {
                echo $this->Html->link('', array('action' => 'imprimir_relatorio',  $pedido['PedidoExame']['codigo']), array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Imprimir relatório de Autiometria', 'style' => 'margin-left: 5px;'));
                } else {
                echo '<span class="icon-print opacity" data-toggle="tooltip" title="Opção indisponível"></span>' ;
                }      
                 ?>
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
<?php echo $this->Javascript->codeBlock("
 $(document).ready(function() {
        $('[data-toggle=\"tooltip\"]').tooltip();
    });
"); ?>