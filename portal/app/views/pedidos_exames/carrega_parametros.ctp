<h4>Tipo de Exames do Pedido:</h4>
<span style="padding: 10px;"><input type="checkbox" <?php echo $parametros['exame_admissional'] == '1' ? 'checked="checked"' : ''; ?> disabled="disabled" /> Admissional</span>
<span style="padding: 10px;"><input type="checkbox" <?php echo $parametros['exame_periodico'] == '1' ? 'checked="checked"' : ''; ?> disabled="disabled" /> Periódico</span>
<span style="padding: 10px;"><input type="checkbox" <?php echo $parametros['exame_demissional'] == '1' ? 'checked="checked"' : ''; ?> disabled="disabled" /> Demissional</span>
<span style="padding: 10px;"><input type="checkbox" <?php echo $parametros['exame_mudanca'] == '1' ? 'checked="checked"' : ''; ?> disabled="disabled" /> Mudança de Riscos Ocupacionais</span>
<span style="padding: 10px;"><input type="checkbox" <?php echo $parametros['exame_monitoracao'] == '1' ? 'checked="checked"' : ''; ?> disabled="disabled" /> Monitoração Pontual</span>
<span style="padding: 10px;"><input type="checkbox" <?php echo $parametros['exame_retorno'] == '1' ? 'checked="checked"' : ''; ?> disabled="disabled" /> Retorno ao Trabalho</span>
<span style="padding: 10px;"><input type="checkbox" <?php echo $parametros['pontual'] == '1' ? 'checked="checked"' : ''; ?> disabled="disabled" /> Pontual</span>
<span style="padding: 10px;"><input type="checkbox" <?php echo $parametros['portador_deficiencia'] == '1' ? 'checked="checked"' : ''; ?> disabled="disabled" /> Avaliação Portador de Deficiência</span>
<br /><br>
<span style="padding: 10px;">
	Data de <?php echo ($parametros['exame_demissional'] == '1') ? 'Demissão' : 'Solicitação'; ?>: <?php echo $parametros['data_solicitacao']; ?>
</span>