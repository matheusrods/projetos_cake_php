<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir',$pjur_codigo[0]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<?php if(empty($listagem)):?>
    <div class="alert">
    Nenhum Registro foi encontrado.
    </div>
<?php else:?>    
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Razão social</th>
                <th>Placa</th>
                <th>Loadplan</th>
                <th>Nota Fiscal</th>
                <th>CD</th>
                <th>Data do Carregamento</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listagem as $dado): ?>
            <tr>
                <td><?= $dado['TPjurPessoaJuridica']['pjur_razao_social']?></td>
                <td><?= COMUM::formatarPlaca($dado['TCpatCargasPatio']['cpat_placa_carreta']) ?></td>
                <td><?= $dado['TCpatCargasPatio']['cpat_loadplan'] ?></td>
                <td><?= $dado['TCpatCargasPatio']['cpat_nota'] ?></td>
                <td><?= $dado['TRefeReferencia']['refe_descricao'] ?></td>
                <td><?= substr($dado['TCpatCargasPatio']['cpat_data_carregamento'],0,10) ?></td>
                <td class="numeric"><?php echo $html->link('', array('controller' => 'cargas_patio', 'action' => 'editar', $dado['TCpatCargasPatio']['cpat_codigo']), array('class' => 'icon-edit', 'title' => 'Editar')); ?></td>
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