<?php /* Não gera o bottão quando clica na ordenação */
      if (!$isAjax): ?>
    <div class='actionbar-right'>
        <?= $html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Perfil')) ?>
    </div>
<?php endif; ?>
<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>

<div class="lista">
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
	<table class="table table-striped">
    	<thead>
    	   <tr>
                <th><?php echo $this->Paginator->sort('Descrição','Uperfil.descricao');?></th>
    			<th><?php echo $this->Paginator->sort('Tipo Perfil','Uperfil.codigo_tipo_perfil');?></th>
                <th><?php echo $this->Paginator->sort('Data criação','Uperfil.data_inclusao');?></th>
                <th><?php echo $this->Paginator->sort('Usuário inclusão','Usuario.nome');?></th>
                <th style="width:13px"></th>
    			<th style="width:13px"></th>
                <th style="width:13px"></th>
    			<?php if($perfil_interno):?>
                    <th style="width:13px"></th>
               <?php endif;?>
               <th style="width:13px"></th>
           </tr>
    	</thead>
    	<?php foreach ($uperfis as $uperfil):?>
    	<tr>
            <td><?php echo $uperfil['Uperfil']['descricao']; ?>&nbsp;</td>
    		<td><?php echo $uperfil['TipoPerfil']['descricao']; ?>&nbsp;</td>
            <td><?php echo $uperfil['Uperfil']['data_inclusao']; ?>&nbsp;</td>
            <td><?php echo $uperfil['Usuario']['nome']; ?>&nbsp;</td>
            <td>
                <?php echo $this->Html->link('', array('controller' => 'usuarios', 'action' => 'por_perfil', $uperfil['Uperfil']['codigo']), array('class' => 'icon-eye-open', 'title'=>'Visualizar perfil')); ?>
            </td>
    		<?php if($perfil_interno):?>
                <td>
                    <?php echo $this->Html->link('', array('action' => 'excluir', $uperfil['Uperfil']['codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash', 'title'=>'Excluir perfil')) ?>
                </td>
            <?php endif;?>
            <td>
                <?php echo $this->Html->link('', array('action' => 'editar', $uperfil['Uperfil']['codigo']), array('class' => 'icon-edit', 'title'=>'Alterar perfil')); ?>
            </td>
            <td>
                <?php echo $this->Html->link('', array('action' => 'permissoes_perfil', $uperfil['Uperfil']['codigo']), array('class' => 'icon-wrench', 'title'=>'Alterar permissões')); ?>
            </td>
            
    		<td><?php echo $this->Html->link('', array('action' => 'listagem_log', $uperfil['Uperfil']['codigo']), array('class' => 'icon-info-sign', 'title'=>'Log do perfil')); ?></td>
    	</tr>
        <?php endforeach; ?>
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
</div>
<?php echo $this->Js->writeBuffer(); ?>
