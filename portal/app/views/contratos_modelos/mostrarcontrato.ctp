<div class="container">
<?php
    if (isset($texto)) {
        pr($texto['ContratoModelo']['modelo']);
    } else {
        pr('Modelo não definido');
    }
?>
</div>
<div>
    <?php echo $html->link('voltar', array('action' => 'index'), array('class' => 'btn')) ?>
</div>
