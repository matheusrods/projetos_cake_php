<?php echo $this->Html->charset(); ?>
<title>.:buonny Credit - Online</title>
<?php echo $this->Buonny->link_css('twiter/bootstrap'); ?>
<?php echo $this->Buonny->link_css('superfish/superfish'); ?>
<?php echo $this->Buonny->link_css('twiter/bootstrap-responsive'); ?>
<?php echo $this->Buonny->link_css('app'); ?>
<?php echo $this->Buonny->link_css('promocoes_buonnycredit'); ?>
<div id="dia_do_amigo">	
    <div><?php echo $html->image("buonnycredit/amigo.png", array("alt" => "Dia do Amigo Buonny Credit", "title" => "Dia do Amigo Buonny Credit", "url" => "http://www.buonnycredit.com.br", "target" => "_blank")); ?></div>
	<div>
		<p>Você é um amigo a quem dedicamos nossos maiores esforços para vê-lo sempre satisfeito e feliz com nosso trabalho. Para comemorarmos juntos preparamos um presente especial para você.</p>
		<p class="italic">Cadastre-se abaixo para utilização do Buonny Credit e ganhe uma consulta gratuita de CPF ou CNPJ.</p>
	</div>
	<div>
		<?php echo $this->BForm->create('BeneficiadoPromocao', array('url' => array('controller' => 'promocoes', 'action' => 'cadastro_buonnycredit'), 'autocomplete' => 'off'))?> 
			<?php echo $this->BForm->input('Usuario.apelido', array('class' => 'input-medium', 'placeholder' => 'Usuário', 'label' => false, 'type' => 'text')); ?>
			<?php echo $this->BForm->input('Usuario.senha', array('class' => 'input-medium', 'placeholder' => 'Senha', 'label' => false, 'type' => 'password')); ?>
			<?php echo $this->BForm->submit('Cadastrar', array('div' => false)); ?>
			<p class="<?= $info['class'] ?>"><?= $info['text'] ?></p>
		<?php echo $this->BForm->end(); ?>
	</div>
	<p class="italic">Utilize usuário e senha do TELECONSULT</p>
	<p class="obs">* Promoção especial para os primeiros 200 clientes que se cadastrarem em nosso site para utilização do crédito até 20 de Setembro de 2012.</p>
	<p class="contact">
		<strong>Contato:</strong> 11 5079-2525 ou <?php echo $this->Html->link('credit@buonny.com.br', 'mailto:credit@buonny.com.br'); ?><br />
		<strong>Horário de atendimento:</strong> segunda a sexta das 8:00 ás 18:00
	</p>
</div>
<div id="fotter_dia_do_amigo">
	<p>
		<?php echo $html->image("buonnycredit/credit.png", array("alt" => "Buonny... Sempre ao seu lado!!! - Contato 11 3443-2525 - www.buonnycredit.com.br", "title" => "Buonny... Sempre ao seu lado!!! - Contato 11 3443-2525 - www.buonnycredit.com.br", "class" => "lg_buonny_credit", "url" => "http://www.buonnycredit.com.br")); ?>
		<?php echo $html->image("buonnycredit/serasa.png", array("alt" => "Distribuidor Autorizado Serasa Experian", "title" => "Distribuidor Autorizado Serasa Experian", "class" => "lg_serasa_experian", "url" => "http://www.serasaexperian.com.br")); ?>
	</p>
</div>