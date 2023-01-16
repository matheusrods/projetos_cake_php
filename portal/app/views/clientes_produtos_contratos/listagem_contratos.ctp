<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="left"><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th class="input-xxlarge"><?php echo $this->Paginator->sort('Nome', 'razao_social') ?></th>
            <th colspan="2" class="left"><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $cliente): ?>
        <tr>
            <td class="left"><?php echo $cliente['Cliente']['codigo'] ?></td>
            <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
            <td class="left"><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
            <td>
                <?php if ($destino == 'clientes'): ?>
                    <?= $html->link('', array('action' => 'editar', $cliente['Cliente']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                <?php elseif ($destino == 'clientes_configuracoes'): ?>
                    <?php echo $this->BMenu->linkOnClick('',array('controller' => 'Clientes', 'action' => 'editar_configuracao', $cliente['Cliente']['codigo'],rand()), array('class' => 'icon-wrench', 'title' => 'Configuração do Cliente')); ?>
                <?php elseif ($destino == 'clientes_operacoes'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_operacoes', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Operações deste Cliente')); ?>
                <?php elseif ($destino == 'clientes_relacionamentos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_relacionamentos', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Relacionamentos deste Cliente')); ?>
                <?php elseif ($destino == 'gerenciar_clientes_produtos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Produtos Cliente')); ?>                
                <?php elseif ($destino == 'clientes_demonstrativos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes', 'action' => 'demonstrativo_de_servico_buonnycredit', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Demonstrativo de Serviços BuonnyCredit')); ?>
                <?php elseif ($destino == 'clientes_usuarios'): ?>
                    <?php echo $html->link('', array('controller' => 'usuarios', 'action' => 'por_cliente', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Usuários do Cliente')); ?>
                <?php elseif ($destino == 'gerenciar_clientes_produtos_status'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos', 'action' => 'gerenciar', $cliente['Cliente']['codigo'], $financeiro = true), array('class' => 'icon-wrench', 'title' => 'Gerenciar Status dos Produtos de Clientes')); ?>                
                
                <?php elseif ($destino == 'clientes_produtos_contratos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos_contratos', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Contratos do Cliente')); ?>                
                <?php elseif ($destino == 'gerenciar_clientes_procuracoes'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_procuracoes', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Procurações do Cliente')); ?>                
                <?php elseif ($destino == 'gerenciar_clientes_produtos_descontos'): ?>
                    <?php echo $html->link('', array('controller' => 'clientes_produtos_descontos', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Concessão de Desconto')) ?>
                <?php else: ?>
                    <?php echo $html->link('', array('controller' => 'clientes_representantes', 'action' => 'gerenciar', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar representantes')); ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
    <tfoot>
    	<td>
			<strong>Total</strong>
		</td>
		<td>
			<strong><?php echo $total_contratos?>&nbsp;&nbsp;Cliente(s) </strong>
		</td>
		<td></td>
		<td></td>
    </tfoot>
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