<div class='form-procurar'>
    <?= $this->element(
        'filtros/pos_obs_local_listagem_clientes',
        array(
            'codigo_cliente' => $codigo_cliente,
            'nome_empresa'   => $nome_empresa,
            'is_admin'       => $is_admin
        )
    ) ?>
</div>
<div class='lista'></div>