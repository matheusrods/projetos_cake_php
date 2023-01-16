<?php $indice = 0;?>
<?php foreach ($objetos as $objeto): ?> 
    <?php $indice++ ?>
    <?php $chave = (empty($indice_pai) ? "" : $indice_pai.".").$indice ?>
    <li id=<?= "key".$chave ?>>
        <?php if (false && !empty($objeto['ObjetoAcl']['aco_string'])): ?>
            <?= $this->BForm->input($objeto['ObjetoAcl']['aco_string'],array("type"=>"checkbox","label"=> $objeto['ObjetoAcl']['descricao'])); ?>
        <?php else: ?>
            <?= $objeto['ObjetoAcl']['descricao'] ?>
        <?php endif; ?>
        <?php if (!empty($objeto['children'])): ?>
            <ul>
                <?php echo $this->element('uperfis/listagem', array('objetos' => $objeto['children'], 'indice_pai' => $chave)) ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?> 