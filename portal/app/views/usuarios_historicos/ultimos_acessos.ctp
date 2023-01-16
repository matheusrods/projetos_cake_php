<?php if (count($usuarios_historicos)): ?>
    <div class="well">
        Usuario: <?= $usuarios_historicos[0]['Usuario']['apelido'] ?>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>IP</th>
                <th>Agente</th>
                <th>Login</th>
                <th>Mensagem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios_historicos as $usuario_historico): ?>
            <tr>
                <td><?= $usuario_historico['UsuarioHistorico']['data_inclusao'] ?></td>
                <td><?= $usuario_historico['UsuarioHistorico']['remote_addr'] ?></td>
                <td><?= $usuario_historico['UsuarioHistorico']['http_user_agent'] ?></td>
                <td><?= $usuario_historico['UsuarioHistorico']['fail'] ?></td>
                <td><?= $usuario_historico['UsuarioHistorico']['message'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <h2>Não houve nenhum registro de acesso nos últimos 7 dias</h2>
<?php endif ?>