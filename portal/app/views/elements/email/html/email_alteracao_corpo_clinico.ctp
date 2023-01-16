<h3>Olá, tudo bem?</h3>


A relação de médicos examinadores da unidade <b><?php echo $data['unidade']; ?></b> foi atualizada.<br />
Você pode encontrar mais detalhes clicando
<?php if($ambiente == Ambiente::SERVIDOR_PRODUCAO): ?>
    <a href="https://portal.rhhealth.com.br/portal/medicos/corpo_clinico" target="_blank">aqui</a>!
<?php elseif($ambiente == Ambiente::SERVIDOR_HOMOLOGACAO): ?>
    <a href="http://tstportal.buonny.com.br/portal/medicos/corpo_clinico" target="_blank">aqui</a>!
<?php else: ?>
    <a href="http://portal.localhost/portal/medicos/corpo_clinico" target="_blank">aqui</a>!
<?php endif; ?>

