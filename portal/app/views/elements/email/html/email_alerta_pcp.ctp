<h3>Aviso de <?=$dado[0]['stem_descricao'];?></h3>
<p><b>Motivo:</b><?=$dado[0]['matr_descricao'];?></p>
<p><b>Alvo:</b><?= (!empty($dado['TRefeReferencia']['refe_descricao']) ? $dado['TRefeReferencia']['refe_descricao'] : $dado['TIpcpInformacaoPcp']['ipcp_loja']);?></p>
<p><b>Rota :</b><?=$dado['TIpcpInformacaoPcp']['ipcp_rota'];?></p>
<?php if(!empty($dado['TVeicVeiculo']['veic_placa'])):?>
	<p><b>Placa :</b><?= $dado['TVeicVeiculo']['veic_placa'];?></p>
<?php endif;?>	
<?php if(!empty($dado['TViagViagem']['viag_codigo_sm'])):?>
	<p><b>SM :</b><?= $dado['TViagViagem']['viag_codigo_sm'];?></p>
<?php endif;?>
<p><b>Janela Inicial:</b> <?=$dado['TIpcpInformacaoPcp']['ipcp_janela_inicial'];?></p>
<p><b>Janela Final:</b> <?=$dado['TIpcpInformacaoPcp']['ipcp_janela_final'];?></p>
<p>Atenciosamente,</p>
<p>Buonny Projetos e Servi&ccedil;os</p>
