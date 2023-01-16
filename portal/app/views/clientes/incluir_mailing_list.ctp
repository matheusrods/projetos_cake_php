
<div class="well">

    <strong><?php echo count($emails_existentes); ?> email(s) não cadastrado(s) do total de <?php echo $dados; ?></strong>
    <br />
    Emails já cadastrados para a lista de email <strong><?php echo strtoupper($lista_de_email); ?></strong> na base de dados do <span style="color:red"><strong>E-MARKETER</strong></span>    
    <br /><br />

    <?php foreach($emails_existentes as $value): ?>

        <?php utf8_encode(highlight_string($value));  ?><br />

    <?php endforeach; ?>

</div>

<div class="form-actions">    
    <?= $html->link('Voltar', array('action' => 'exportar_mailing'), array('class' => 'btn')); ?>    
</div>