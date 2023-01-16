<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
    <?php if (isset($fichas) && !empty($fichas)) : ?>


    <div class="well">
        <strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social']; ?><br />
        <strong>Produto Atual: </strong><?php echo $produto; ?><br />
        <strong>Migrar para Produto: </strong><?php echo $produtonovo['Produto']['descricao']; ?>
    </div>

    <div class='actionbar-right'>
        <?php echo $html->link('Migrar cliente para '.$produtonovo['Produto']['descricao'], array('controller' => 'clientes_produtos', 'action' => 'cadastrar_valores_servicos_do_produto',$produtonovo['Produto']['codigo'],$codigo_cliente), array('class' => 'btn btn-success', 'title' => 'Migrar cliente para '.$produtonovo['Produto']['descricao'], 'onclick' => 'return open_dialog(this, "Cadastrar Valores dos Serviços do Produto", 960)')) ?>
    </div>

    <table class="table table-striped">
        
        <thead>
            <tr>
                <th>Tipo de Profissional</th>
                <th>Motorista</th>
                <th>CPF</th>
                <th>Data de Inclusão</th>
                <th>Validade</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($fichas as $ficha): ?>
            <tr id="<?php echo $ficha['Ficha']['codigo']; ?>">
                <td><?php echo $ficha['ProfissionalTipo']['descricao']; ?></td>
                <td><?php echo $ficha['ProfissionalLog']['nome']; ?></td>
                <td><?php echo $buonny->documento($ficha['ProfissionalLog']['codigo_documento']); ?></td>
                <td><?php echo $ficha['Ficha']['data_inclusao']; ?></td>
                <td><?php echo $ficha['Ficha']['data_validade']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php // PARA PAGINACAO
            echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>


    <?php endif; ?>
    <?php echo $this->Js->writeBuffer(); ?>
