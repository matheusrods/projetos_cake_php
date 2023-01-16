<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'perifericos_similares', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novo Similaridade'));?>
</div>
<table class='table table-striped tablesorter'>
    <thead>
        <tr>
            <th>Periférico</th>
            <th>Similar</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($perifericos as $periferico): ?>
        <tr>
            <td><?= $periferico['TPpadPerifericoPadrao']['ppad_descricao'] ?></td>
            <td><?= $periferico['TPpadPerifericoPadraoSimilar']['ppad_descricao'] ?></td>
            <td><?php echo $html->link('', array('controller' => 'perifericos_similares', 'action' => 'excluir', $periferico['TPesiPerifericoSimilar']['pesi_codigo']), array('class' => 'icon-trash', 'title' => 'Excluir objeto'), 'Confirma exclusão?'); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>