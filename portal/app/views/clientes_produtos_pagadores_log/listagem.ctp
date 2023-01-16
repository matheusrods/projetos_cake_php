<div class="text-group">
<?php if(isset($preencher)):?>
    <div class="alert">
        Favor informar os dados acima.
    </div>
<?php else:?>
<?php if(isset($clientes_produtos_pagadores_log) && !empty($clientes_produtos_pagadores_log)): ?>
<table class='table table-striped'>
    <thead>
        <th>Cliente Pagador</th>
        <th>Data Alteração</th>
        <th>Produto</th> 
        <th>Usuário</th> 
        <th>Ação no Sistema</th> 
    </thead>
    <tbody>
        <?php foreach ($clientes_produtos_pagadores_log as $cliente_produto_pagador_log): ?>
                <tr>
                    <td><?= $cliente_produto_pagador_log['ClienteProdutoPagadorLog']['codigo_cliente_pagador']; ?></td>
                    <td><?= $cliente_produto_pagador_log['ClienteProdutoPagadorLog']['data_inclusao']; ?></td>
                    <td><?= $cliente_produto_pagador_log['Produto']['descricao']; ?></td>
                    <td><?= $cliente_produto_pagador_log['Usuario']['apelido'] ?></td>
                    <td><?= ($cliente_produto_pagador_log['ClienteProdutoPagadorLog']['acao_sistema'] == 0) ? 'INSERIDO' : (($cliente_produto_pagador_log['ClienteProdutoPagadorLog']['acao_sistema'] == 1) ? 'EDITADO' : 'EXCLUIDO') ?></td>
                </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php else:?>
    <div class="alert">
        Nenhum dado encontrado.
    </div>
<?php endif; ?>
<?php endif; ?>
</div>