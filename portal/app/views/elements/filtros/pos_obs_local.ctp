<div class='well'>
    <?php echo $bajax->form('PosObsLocal', array(
        'autocomplete' => 'off',
        'divupdate'    => '.form-procurar',
        'url'          => array(
            'controller'     => 'filtros',
            'action'         => 'filtrar',
            'model'          => 'PosObsLocal',
            'element_name'   => 'pos_obs_local',
            $codigo_cliente
        )
    )); ?>

    <strong>Código: </strong><span id="PosObsLocalCodigoSpan"><?= $codigo_cliente ?: 'Não informado' ?></span>
    <br>
    <strong>Cliente: </strong><span id="PosObsLocalClienteName"><?= $nome_empresa ?: 'Não informado' ?></span>

    <div id='filtros' style="margin-top: 10px;">
        <?php echo $this->element('pos_obs_local/fields_filtros'); ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        <?php echo $html->link(
            'Voltar',
            array('controller' => 'pos_obs_local', 'action' => 'index', $codigo_cliente),
            array('class' => 'btn')
        ); ?>
        <?php echo $this->BForm->end(); ?>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        jQuery("#limpar-filtro").click(function() {
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(`${baseUrl}/filtros/limpar/model:PosObsLocal/element_name:pos_obs_local/${<?= $codigo_cliente ?>}/${Math.random()}`)
        });

        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(`${baseUrl}pos_obs_local/listagem/${<?= $codigo_cliente ?>}/${Math.random()}`);
        }

        atualizaLista();
    });
</script>