<?php 
    echo $this->element('gerenciar_clientes_produtos/profissionais', array(
        'profissionais' => $servico_profissionais,
        'cliente_pagador' => $codigo_cliente
    ));
?>