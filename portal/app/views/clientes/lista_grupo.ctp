<?php if(!empty($clientes)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <?php if(empty($referencia_modulo)): ?>
        <div class='actionbar-right'>
            <?php echo $html->link('Configurações', array('controller' => 'clientes', 'action' => 'configuracao_grupo_economico_padrao', $cliente_principal['codigo'],$referencia, $terceiros), array('class' => 'btn btn-warning', 'title' => 'Configurações')); ?>

            <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array( 'controller' => $this->name, 'action' => 'incluir', $codigo_cliente, $referencia, $terceiros), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Filial'));?>
        </div>  
    <?php endif; ?>

    <?php //echo $paginator->options(array('update' => 'div.lista')); ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini">Código</th>
                <th>Razão Social</th>
                <th>Nome Fantasia</th>
                <th class="input-medium">CNPJ</th>
                <?php if(!empty($referencia_modulo)): ?>
                    <?php if($referencia_modulo == "funcionarios"): ?>
                        <th class='numeric'>Matrículas</th>
                    <?php elseif($referencia_modulo == "grupos_homogeneos"): ?>
                        <th>Qtd Grupos Homogêneos</th>
                    <?php endif; ?>
                <?php endif; ?>
                <th class="input-mini">Ações</th>
            </tr>
        </thead>
        <tbody>
            <tbody>
                <?php foreach($clientes as $cliente) :?>
                    <tr>
                        <td class="input-mini"><?php echo $cliente['Cliente']['codigo'] ?></td>
                        <td title="<?= $cliente['Cliente']['razao_social'] ?>"><div class='truncate input-xlarge'><?= $cliente['Cliente']['razao_social'] ?></div></td>
                        <td title="<?= $cliente['Cliente']['nome_fantasia'] ?>"><div class='truncate input-xlarge'><?= $cliente['Cliente']['nome_fantasia'] ?></div></td>
                        <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>

                        <?php if(!empty($referencia_modulo)): ?>

                            <?php if($referencia_modulo == "funcionarios"): ?>
                                <td class='numeric'><?php echo $cliente['0']['qtd_funcionario'] ?></td>
                            <?php elseif($referencia_modulo == "grupos_homogeneos"): ?>
                                <td><?php echo $cliente['0']['qtd_grupo_homogeneo'] ?></td>
                            <?php endif; ?>
                        <?php endif; ?>

                        <td class="input-mini">
                            <?php if(empty($referencia_modulo)): ?>
                                <?php echo $html->link('', array('controller' => 'clientes', 'action' => 'editar', $cliente['Cliente']['codigo'], $cliente['GrupoEconomico']['codigo_cliente'], $referencia, 'null', $terceiros), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                            <?php elseif($referencia_modulo == "funcionarios"): ?>
                                <?php echo $html->link('', array('controller' => 'funcionarios', 'action' => 'index', $cliente['Cliente']['codigo'], $referencia), array('class' => 'icon-wrench', 'title' => 'Funcionários')) ?>
                            <?php elseif($referencia_modulo == "grupos_homogeneos"): ?>
                                <?php echo $html->link('', array('controller' => 'grupos_homogeneos', 'action' => 'index', $cliente['Cliente']['codigo'], $referencia), array('class' => 'icon-wrench', 'title' => 'Grupos Homogêneos')) ?>
                            <?php endif; ?>
                            &nbsp;
                            <?php if(is_null($referencia_modulo)) echo $this->Html->image($cliente[0]['imagem'], array('class' => 'ajuste-cadeado js-bloquear pointer', 'data-codigo' => $cliente['GrupoEconomicoCliente']['codigo'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>    
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['GrupoEconomicoCliente']['count']; ?></td>
                </tr>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php if($referencia == "implantacao" || $referencia == "sistema"): ?>
    <div class='form-actions well'>
        <?php echo $html->link('Voltar para Estrutura', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $codigo_cliente, $referencia, $terceiros), array('class' => 'btn')); ?>
    </div>
<?php endif; ?>

<?php echo $this->Js->writeBuffer(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('.js-bloquear').click(function(event) {
            var este = $(this);
            var memory = este.attr('src');
            este.attr('src', baseUrl + 'img/default.gif');
            $.ajax({
                url: baseUrl + 'grupos_economicos_clientes/bloqueia',
                type: 'POST',
                dataType: 'json',
                data: {codigo: este.attr('data-codigo')},
            })
            .done(function(response) {
                switch(response) {
                    case 0: 
                    este.attr('src', baseUrl + 'img/cadeado_aberto.png');
                    break;

                    case 1:
                    este.attr('src', baseUrl + 'img/cadeado_fechado.png');
                    break

                    default:
                    este.attr('src', memory);
                    break;
                }
            });
        });
    });

</script>