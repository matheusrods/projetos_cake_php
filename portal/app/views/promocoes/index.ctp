<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'promocoes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novas Promoções'));?>
</div>
<table class='table table-striped'>
    <thead>
        <tr>
            <th class='input-mini'>Código</th>
            <th>Descrição</th>
            <th class='input-small'>Validade</th>
            <th class='input-mini'>Ativo</th>
            <th class='input-mini numeric'>Qtd.Disp.</th>
            <th class='input-small numeric'>Vr.Disp.</th>
            <th style='width:32px'></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($promocoes as $promocao): ?>
        <tr>
            <td><?php echo $promocao['Promocao']['codigo'] ?></td>
            <td><?php echo $promocao['Promocao']['nome'] ?></td>
            <td><?php echo substr($promocao['Promocao']['validade'],0,10) ?></td>
            <td><?php echo ($promocao['Promocao']['ativo'] ? 'Sim' : 'Não') ?></td>
            <td class='numeric'><?php echo $promocao['Promocao']['quantidade'] - $promocao['Promocao']['quantidade_utilizada'] ?></td>
            <td class='numeric'><?php echo $this->Buonny->moeda($promocao['Promocao']['valor'] - $promocao['Promocao']['valor_utilizado']) ?></td>
            <td>
                <?php echo $this->Html->link('', array('controller' => 'promocoes', 'action' => 'editar', $promocao['Promocao']['codigo']), array('class' => 'icon-edit', 'title' => 'editar')); ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>