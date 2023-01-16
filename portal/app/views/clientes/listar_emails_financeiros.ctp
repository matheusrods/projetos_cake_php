<table class="table table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Data de Inclusão</th>
            <th style="width:13px"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($emails as $value): ?>
        <tr>
            <td class="input"><?php echo $value['ClienteContato']['nome'] ?></td>
            <td class="input"><?php echo $value['ClienteContato']['descricao'] ?></td>
            <td class="input"><?php echo substr($value['ClienteContato']['data_inclusao'], 0, 10) ?></td>
            <td><?php echo $html->link('', array('action' => 'atualizar_email_financeiro', $value['ClienteContato']['codigo']), array('class' => 'icon-edit btn-modal', 'title' => 'Atualizar Email Financeiro', 'onclick' => "return open_dialog(this, 'Alterar email',640)")) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>