<?php
if( $codigo_cliente ):
    echo $paginator->options(array('update' => 'div.lista')); 
?>
    <div class='actionbar-right'>
        <?php
        echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', 
            array('action' => 'consulta_para_incluir', $codigo_cliente), 
            array('class' => 'btn btn-success', 'escape' => false)
        );
        ?>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini">
                <?if($transportador):?>
                    <?php echo $this->Paginator->sort('Código', 'ClienteEmbarcador.codigo') ?></th>
                <?else:?>
                    <?php echo $this->Paginator->sort('Código', 'ClienteTransportador.codigo') ?></th>
                <?endif;?>
                <?if($transportador):?>
                <th><?php echo $this->Paginator->sort('Embarcador', 'ClienteEmbarcador.razao_social') ?></th>
                <?else:?>
                <th><?php echo $this->Paginator->sort('Transportador', 'ClienteTransportador.razao_social') ?></th>
                <?endif;?>
                <th colspan="3">
                <?if($transportador):?>
                    <?php echo $this->Paginator->sort('Documento', 'ClienteEmbarcador.codigo_documento') ?>
                <?else:?>
                    <?php echo $this->Paginator->sort('Documento', 'ClienteTransportador.codigo_documento') ?>
                <?endif;?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($clientes as $key => $cliente):?>
            <?if($transportador):?>
            <tr>
                <td class="input-mini"><?php echo $cliente['ClienteEmbarcador']['codigo'] ?></td>
                <td><?php echo $cliente['ClienteEmbarcador']['razao_social'] ?></td>                
                <td><?php echo $buonny->documento($cliente['ClienteEmbarcador']['codigo_documento']) ?></td>
                <td class="pagination-centered">
                    <?if($cliente[0]['permite_edicao']):?>
                        <?= $html->link('', array('action' => 'editar_embarcador_transportador', $cliente['ClienteEmbarcador']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                    <?endif;?>
                </td>
                <td class="pagination-centered">
                    <?=$html->link('', array('action' => 'excluir_embarcador_transportador', $cliente['ClienteEmbarcador']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Relacionamento'), 'Confirma a exclusão?'); ?>
                </td>                
            </tr>
            <?else:?>
            <tr>
                <td class="input-mini"><?php echo $cliente['ClienteTransportador']['codigo'] ?></td>
                <td><?php echo $cliente['ClienteTransportador']['razao_social'] ?></td>                
                <td><?php echo $buonny->documento($cliente['ClienteTransportador']['codigo_documento']) ?></td>
                <td class="pagination-centered">
                <?if($cliente[0]['permite_edicao']):?>
                    <?= $html->link('', array('action' => 'editar_embarcador_transportador', $cliente['ClienteTransportador']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                <?endif;?>
                </td>
                <td class="pagination-centered">
                    <?=$html->link('', array('action' => 'excluir_embarcador_transportador', $cliente['ClienteTransportador']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Relacionamento'), 'Confirma a exclusão?'); ?>
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
<?endif;?>
