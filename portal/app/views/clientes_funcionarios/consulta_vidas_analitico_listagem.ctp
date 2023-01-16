<?php if (!$listagem): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>	
    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>  
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <th class="input-medium">Nome</th>
            <th class="input-medium">CPF</th>
            <th class="input-medium">Unidade</th>
            <th class="input-medium">Setor</th>
            <th class="input-medium">Cargo</th>
            <th class="input-mini">Status</th>            
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listagem as $dados): ?>            
            <?php 
            if($dados[0]['ativo'] > 0)
            {
                $status = "ATIVO";
            }
            if($dados[0]['inativo'] > 0)
            {
                $status = "INATIVO";
            }
            if($dados[0]['ferias'] > 0)
            {
                $status = "FERIAS";
            }            
            if($dados[0]['afastado'] > 0)
            {
                $status = "AFASTADO";
            }            
            ?>
            <tr>
	            <td class="input-medium"><?php echo $dados[0]['nome'];?></td>
	            <td class="input-medium"><?php echo $dados[0]['cpf'];?></td>
	            <td class="input-medium"><?php echo $dados[0]['nome_fantasia'];?></td>
	            <td class="input-medium"><?php echo $dados[0]['descricao'];?></td>
	            <td class="input-medium"><?php echo $dados[0]['cargo'];?></td>
	            <td class="input-mini"><?php echo $status;?></td>                            
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['GrupoEconomicoCliente']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <?php
    if($this->Paginator->params['paging']['GrupoEconomicoCliente']['count'] > 50)
    {
    ?>    
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?php
    }
    ?>        
    <?php echo $this->Js->writeBuffer();?>
<?php endif ?>