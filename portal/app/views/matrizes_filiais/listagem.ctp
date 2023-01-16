<?php 
    echo $this->Paginator->options(array('update' => '.lista')); 
?>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir', 'controller' => 'MatrizesFiliais'), array('class' => 'btn btn-success', 'escape' => false )); ?>
</div>

<?php 
$indice_matriz = 0;
$indice_produto = 0;
$matriz_anterior = null;
$filial_anterior = null;
?>

<table class="table">
    <thead>
        <tr>
            <th class='input-mini'>Código</th>
            <th class='input-large'>Matriz</th>
            <th class='input-mini'>Código</th>
            <th class='input-large'>Filial</th>
            <th class='input-medium'>Produto</th>
            <th class='input-mini'>Código</th>
            <th class='input-medium'>Pagador</th>
            <th class="acoes input-mini" colspan="3">Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($matriz_filial as $matriz_filial): ?>
        <?php if(!($matriz_anterior == $matriz_filial['ClienteMatriz']['codigo'] && $filial_anterior == $matriz_filial['ClienteFilial']['codigo'])) : ?>
        <?php $indice_matriz++; 
		?>
		<tr id="matriz_filial-<?php echo $indice_matriz; ?>" class="expand" produto="<?php echo $indice_matriz; ?>" style='cursor:pointer'>
            <td class = "first"><i class="icon-chevron-right"></i>
            	<?= $matriz_filial['ClienteMatriz']['codigo'] ?></td>
			<td><?= substr($matriz_filial['ClienteMatriz']['razao_social'],0,30) ?></td>
            <td><?= $matriz_filial['ClienteFilial']['codigo'] ?></td>
			<td><?= substr($matriz_filial['ClienteFilial']['razao_social'],0,30) ?></td>
			<td></td>
            <td></td>
            <td></td>
            <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'matrizes_filiais', 'action' => 'incluir', $matriz_filial['MatrizFilial']['codigo']), array('escape' => false, 'class' => 'icon-plus evt-incluir-matriz-filial', 'title' => 'Incluir Produto e Cliente Pagador')); ?></td>
            <td></td>
            <td>
            	<a class="icon-trash evt-excluir-matriz-pagador" href="#" onclick="excluirTodos(<?php echo $matriz_filial['MatrizFilial']['codigo'];?>)"></a>
            </td>
            
        </tr>
        <?php 
        $matriz_anterior 	= $matriz_filial['ClienteMatriz']['codigo']; 
        $filial_anterior = $matriz_filial['ClienteFilial']['codigo'];
        ?>
    	<?php endif; ?>
        <?php if (isset($matriz_filial['MatrizProdutoPagador']['codigo']) && !empty($matriz_filial['MatrizProdutoPagador']['codigo'])): ?>
            <?php $indice_produto++; ?>
            <tr id="produto-<?php echo $indice_matriz; ?>" class="collapsed produto-<?php echo $indice_produto; ?> child-matriz-<?php echo $indice_matriz; ?>">
            	<td></td>
            	<td></td>
            	<td></td>
            	<td></td>
                <td><?php echo $matriz_filial['Produto']['descricao']; ?></td>
                <td><?php echo $matriz_filial['ClientePagador']['codigo']; ?></td>
                <td><?php echo $matriz_filial['ClientePagador']['razao_social']; ?></td>
                <td></td>
                <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'matrizes_produtos_pagadores', 'action' => 'editar', $matriz_filial['MatrizProdutoPagador']['codigo']), array('escape' => false, 'class' => 'icon-edit', 'title' => 'Editar Pagador')); ?></td>
                <td class='action-icon'>
					<a class="icon-trash evt-excluir-produto-pagador" href="#" onclick="excluirProdutoPagador(<?php echo $matriz_filial['MatrizProdutoPagador']['codigo'];?>)"></a>
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
        		if( confirm('Você tem certeza que deseja remover esta combinação Matriz Filial?') )
        			location.href = '/portal/matrizes_filiais/remover/' + codigo;
        		else
        			return false;
        	}

			function excluirProdutoPagador(codigo){
        		if( confirm('Você tem certeza que deseja remover produto e pagador?') )
        			location.href = '/portal/matrizes_produtos_pagadores/excluir/' + codigo;
        		else
        			return false;
        	}


		$(function() {
            $('tr a').click(function(){
                window.location = $(this).attr('href');
                return false;
            });

            $('tr').click(function(){
                $('.matriz_filial-'+$(this).attr('produto')).toggle();
                
                if($(this).find('i.icon-chevron-down').length > 0){
                	$('.child-matriz-'+$(this).attr('produto')).hide();
                    $(this).find('i').addClass('icon-chevron-right');
                    $(this).find('i').removeClass('icon-chevron-down');
                }else{
                	$('.child-matriz-'+$(this).attr('produto')).show();
                    $(this).find('i').addClass('icon-chevron-down');
                    $(this).find('i').removeClass('icon-chevron-right');
                }

                return false;
            });
    	});
        
    ");

?>