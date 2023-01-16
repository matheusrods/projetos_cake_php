<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th colspan="2">Descrição</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($filiais as $filial): ?>
        <tr>
            <td class="input-mini">
                <?= $filial['EnderecoRegiao']['codigo'] ?>
            </td>
            <td>
                <?= $filial['EnderecoRegiao']['descricao'] ?>
            </td>
            <td class="pagination-centered">
                <?= $html->link('', array('controller' => 'usuarios', 'action' => 'por_filial', $filial['EnderecoRegiao']['codigo']), array('class' => 'icon-wrench', 'title' => 'Usuários da Filial')) ?> 
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<div class='row-fluid'>
</div>