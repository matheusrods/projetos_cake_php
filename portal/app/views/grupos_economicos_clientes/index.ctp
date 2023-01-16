<div id="cliente" class='well'>
	<strong>Grupo Econômico: </strong><?= $grupo_economico['GrupoEconomico']['descricao'] ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir', $this->passedArgs[0]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Cliente no Grupo Econômico'));?>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th class='input-mini'>Código</th>
            <th>Razão Social</th>
            <th style='width:13px'></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $cliente): 

            $matriz         = $grupo_economico['GrupoEconomico']['codigo_cliente'];
            $unidade_grupo = $cliente['GrupoEconomicoCliente']['codigo_cliente'];

            $exibir = '';
            if(($matriz != $unidade_grupo) || ($matriz == $unidade_grupo && $valida_exclusao_matriz)){
                $exibir = $html->link('', array('action' => 'excluir', $cliente['GrupoEconomicoCliente']['codigo'], $this->passedArgs[0]), array('class' => "icon-trash", 'title' => 'Excluir'), 'Confirma exclusão?');
            }
        ?>
            <tr>
            	<td class='input-mini'><?php echo $cliente['GrupoEconomicoCliente']['codigo_cliente'] ?></td>
                <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
                <td class="pagination-centered">
                    <?= $exibir ?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<div class='form-actions'>
	<?= $html->link('Voltar', array('controller' => 'grupos_economicos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>