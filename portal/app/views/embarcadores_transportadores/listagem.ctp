<?php 
    echo $this->Paginator->options(array('update' => '.lista')); 
?>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir', 'controller' => 'EmbarcadoresTransportadores'), array('class' => 'btn btn-success', 'escape' => false )); ?>
</div>

<?php 
$indice_embarcador = 0;
$indice_produto = 0;
$embarcador_anterior = null;
$transportador_anterior = null;
?>

<table class="table">
    <thead>
        <tr>
            <th class='input-mini'>Código</th>
            <th class='input-large'>Embarcador</th>
            <th class='input-mini'>Código</th>
            <th class='input-large'>Transportador</th>
            <th class='input-medium'>Produto</th>
            <th class='input-mini'>Código</th>
            <th class='input-medium'>Pagador</th>
            <th class="acoes input-mini" colspan="3">Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($embarcadores_transportadores as $embarcador_transportador): ?>
        <?php if(!($embarcador_anterior == $embarcador_transportador['ClienteEmbarcador']['codigo'] && $transportador_anterior == $embarcador_transportador['ClienteTransportador']['codigo'])) : ?>
        <?php $indice_embarcador++; 
		?>
		<tr id="embarcador-<?php echo $indice_embarcador; ?>" class="expand" produto="<?php echo $indice_embarcador; ?>" style='cursor:pointer'>
            <td class = "first"><i class="icon-chevron-right"></i>
            	<?= $embarcador_transportador['ClienteEmbarcador']['codigo'] ?></td>
			<td><?= substr($embarcador_transportador['ClienteEmbarcador']['razao_social'],0,30) ?></td>
            <td><?= $embarcador_transportador['ClienteTransportador']['codigo'] ?></td>
			<td><?= substr($embarcador_transportador['ClienteTransportador']['razao_social'],0,30) ?></td>
			<td></td>
            <td></td>
            <td></td>
            <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'embarcadores_transportadores', 'action' => 'incluir', $embarcador_transportador['EmbarcadorTransportador']['codigo']), array('escape' => false, 'class' => 'icon-plus evt-incluir-embarcador-transportador', 'title' => 'Incluir Produto e Cliente Pagador')); ?></td>
            <td></td>
            <td>
            	<a class="icon-trash evt-excluir-embarcador-pagador" href="#" onclick="excluirTodos(<?php echo $embarcador_transportador['EmbarcadorTransportador']['codigo'];?>)"></a>
            </td>
            
        </tr>
        <?php 
        $embarcador_anterior 	= $embarcador_transportador['ClienteEmbarcador']['codigo']; 
        $transportador_anterior = $embarcador_transportador['ClienteTransportador']['codigo'];
        ?>
    	<?php endif; ?>
        <?php if (isset($embarcador_transportador['ClienteProdutoPagador']['codigo']) && !empty($embarcador_transportador['ClienteProdutoPagador']['codigo'])): ?>
            <?php $indice_produto++; ?>
            <tr id="produto-<?php echo $indice_embarcador; ?>" class="collapsed produto-<?php echo $indice_produto; ?> child-embarcador-<?php echo $indice_embarcador; ?>">
            	<td></td>
            	<td></td>
            	<td></td>
            	<td></td>
                <td><?php echo $embarcador_transportador['Produto']['descricao']; ?></td>
                <td><?php echo $embarcador_transportador['ClientePagador']['codigo']; ?></td>
                <td><?php echo $embarcador_transportador['ClientePagador']['razao_social']; ?></td>
                <td></td>
                <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'embarcadores_transportadores', 'action' => 'incluir', $embarcador_transportador['EmbarcadorTransportador']['codigo'] ,$embarcador_transportador['ClienteProdutoPagador']['codigo']), array('escape' => false, 'class' => 'icon-edit evt-editar-cliente_pr', 'title' => 'Editar Pagador')); ?></td>
                <td class='action-icon'>
					<a class="icon-trash evt-excluir-produto-pagador" href="#" onclick="excluirProdutoPagador(<?php echo $embarcador_transportador['ClienteProdutoPagador']['codigo'];?>)"></a>
                </td>
            </tr>
        
    	<?php endif; ?>
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
<?php
echo $this->Javascript->codeBlock("
        $(document).ready(function() {        
        	$('.collapsed').hide();            
        });

		function excluirTodos(codigo){
    		if( confirm('Você tem certeza que deseja remover esta combinação Embarcador Transportador?') )
    			location.href = '/portal/embarcadores_transportadores/remover/' + codigo;
    		else
    			return false;
    	}

		function excluirProdutoPagador(codigo){
    		if( confirm('Você tem certeza que deseja remover produto e pagador?') )
    			location.href = '/portal/clientes_produtos_pagadores/excluir/' + codigo;
    		else
    			return false;
    	}

		$(function() {
            $('tr a').click(function(){
                window.location = $(this).attr('href');
                return false;
            });

            $('tr').click(function(){
                $('.embarcador-'+$(this).attr('produto')).toggle();
                
                if($(this).find('i.icon-chevron-down').length > 0){
                	$('.child-embarcador-'+$(this).attr('produto')).hide();
                    $(this).find('i').addClass('icon-chevron-right');
                    $(this).find('i').removeClass('icon-chevron-down');
                }else{
                	$('.child-embarcador-'+$(this).attr('produto')).show();
                    $(this).find('i').addClass('icon-chevron-down');
                    $(this).find('i').removeClass('icon-chevron-right');
                }

                return false;
            });
    	});
        
    ");

?>