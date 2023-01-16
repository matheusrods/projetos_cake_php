<?php if (isset($links['links']['opcaofat'])): ?>
    <p>Se voc&ecirc; deseja receber as Notas Fiscais, Demonstrativos e Boletos do faturamento somente por email, 
    <a href="<?php echo $links['links']['opcaofat'] ?>">clique aqui</a></p>
<?php endif; ?>
<p><em>* Para abrir os links corretamente, desative seu bloquedor de pop up</em></p>
<br>
<p>
<?php echo "Os links abaixo s&atilde;o referentes ao faturamento do m&ecirc;s ".$links['NotaFiscal']['mes_servicos']." para empresa<br>" ?>
<?php echo "<strong>".utf8_encode($links['Cliente']['razao_social'])." - ".$links['Cliente']['codigo']."</strong>" ?>
</p>
<p>
    <strong><?php echo "Nota Fiscal:" ?></strong><br>
    <a href="<?php echo $links['links']['nf'] ?>">Visualizar</a>
</p>

<?php if (isset($links['links']['demonstrativos']['001'])): ?>
    <p>
        <strong><?php echo "Demonstrativo de Servi&ccedil;os Per Capita: " ?></strong><br>
        <a href="<?php echo $links['links']['demonstrativos']['001'] ?>">Visualizar</a>
    </p>
<?php endif; ?>
<?php if (isset($links['links']['demonstrativos']['002'])): ?>
    <p>
        <strong><?php echo "Demonstrativo de Servi&ccedil;os Exames Complementares: " ?></strong><br>
        <a href="<?php echo $links['links']['demonstrativos']['002'] ?>">Visualizar</a>
    </p>
<?php endif; ?>

<?php if ($links['links']['boleto'] != null): ?>
    <p>
        <strong><?php echo "Boleto: " ?></strong><br>
        <?php echo $links['links']['boleto'] ?>
    </p>
<?php endif; ?>
<p>
    <a href="http://get.adobe.com/br/reader/" target="_blank" alt="Para visualiz&aacute;-la &eacute; necess&aacute;ria a instala&ccedil;&atilde;o do AcrobatReader. Voc&ecirc; pode fazer o download Clicando Aqui."><img src="http://portal.rhhealth.com.br/portal/img/logo_reader.jpg" title="Para visualiz&aacute;-la &eacute; necess&aacute;ria a instala&ccedil;&atilde;o do AcrobatReader. Voc&ecirc; pode fazer o download Clicando Aqui." border="0" /> Para visualiz&aacute;-la &eacute; necess&aacute;ria a instala&ccedil;&atilde;o do AcrobatReader. Voc&ecirc; pode fazer o download Clicando Aqui.</a>
</p>
<p>
    Este e-mail foi enviado automaticamente pelo Sistema de Faturamento RHHealth (NFS-e).
    Favor não responder. 
    Em caso de dúvidas, entre em contato com: ouvidoria@rhhealth.com.br
</p>