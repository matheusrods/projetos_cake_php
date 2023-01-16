<?php if(!empty($lista_clientes_grupo)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código Cliente</th>
                <th>Razão Social</th>
                <th>Nome Fantasia</th>
                <th>Bairros</th>
                <th>Cidade</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lista_clientes_grupo as $dados) : ?>
                <tr>
                    <td><?php echo $dados['Unidade']['codigo'];?></td>
                    <td><?php echo $dados['Unidade']['razao_social'];?></td>
                    <td><?php echo $dados['Unidade']['nome_fantasia'];?></td>
                    <td><?php echo $dados['ClienteEndereco']['bairro'];?></td>
                    <td><?php echo $dados['ClienteEndereco']['cidade'];?></td>
                    <td><?php echo $dados['ClienteEndereco']['estado_abreviacao'];?></td>
                    <td>
                        <?php echo $this->Html->link('', array('action' => 'lista_hospitais_emergencia', $dados['Cliente']['codigo'], $dados['Unidade']['codigo']), array('class' => 'icon-home', 'title' => 'Hospitais de Emergência')); ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
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
    <?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    