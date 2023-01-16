<?php if (is_array($codigo_cliente)) : ?>
    <?php $codigo_cliente = implode(',', $codigo_cliente); ?>
<?php endif; ?>

<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form(
            'PosObsLocal',
            array(
                'autocomplete' => 'off',
                'divupdate'    => '.form-procurar',
                'url'          => array(
                    'controller'   => 'filtros',
                    'action'       => 'filtrar',
                    'model'        => 'PosObsLocal',
                    'element_name' => 'pos_obs_local_listagem_clientes',
                    $codigo_cliente
                )
            )
        ) ?>

        <div class="row-fluid inline">
            <?php

            if ($is_admin) {
                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Cliente');
                } else {
                    echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'PosObsLocal');
                }
            } else {
                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Cliente');
                } else {
                    echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                    echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_empresa}"));
                }
            }
            ?>
        </div>

        <?php if ($is_admin) : ?>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        <?php endif; ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<script>
    $(document).ready(function() {

        const codigo_cliente = "<?= $codigo_cliente ?: '' ?>";

        $('#ClienteCodigoCliente').on("change", function() {
            const codigo_cliente = $(this).val();
            atualizaListaCliente(codigo_cliente);
        });

        $("#limpar-filtro").click(function() {
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(
                `${baseUrl}filtros/limpar/model:PosObsLocal/element_name:pos_obs_local_listagem_clientes/${Math.random()}`
            )
        });

        function atualizaListaCliente(codigo_cliente) {
            var div = $("div.lista");
            bloquearDiv(div);
            div.load(
                `${baseUrl}pos_obs_local/listagem_clientes/${codigo_cliente}/${Math.random()}`
            );
        }
        atualizaListaCliente(codigo_cliente);
    });
</script>