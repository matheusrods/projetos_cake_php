<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-xlarge">Código</th>
            <th class="input-xxlarge">Descrição</th>
            <th style="width:13px"></th>
                   
        </tr>
    </thead>
    <tbody>
        <?php $class = null?>

        <?php foreach($produtos as $produto): ?>
        <tr>
            <td><?php echo $produto['TTtraTipoTransporte']['ttra_codigo'] ?></td>
            <td><?php echo $produto['TTtraTipoTransporte']['ttra_descricao'] ?></td>
            <td>
                <?php echo $this->Html->link('', array('action' => 'editar', $produto['TTtraTipoTransporte']['ttra_codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
            </td>   
           
            
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

