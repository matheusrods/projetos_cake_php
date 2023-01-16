<tr class="tablerow-input isca-item">
    <td>
        <?php echo $this->BForm->input("RecebsmIsca.{$key}.tecn_codigo", array('label' => false, 'empty' => 'Tecnologia','class' => 'input-medium', 'options' => $tecnologias)) ?>
    </td>
    <td>
        <?php echo $this->BForm->input("RecebsmIsca.{$key}.term_numero_terminal", array('label' => false, 'class' => 'input-medium')) ?>
    </td>
    <td>
        <?php if($key > 0): ?>
            <?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error remove-isca', 'escape' => false)); ?>
        <?php endif; ?>
    </td>
</tr>
