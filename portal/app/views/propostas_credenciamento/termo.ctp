<html>
	<head>
		<link href="/portal/css/bootstrap_3_3_5.min.css" rel="stylesheet" />
	</head>
	<body>
		<div style="width: 700px; margin: 0 auto; font-size: 12px;">
			<div style="width: 100%; text-align: center;">
				<br />
				<img src="/portal/img/logo-rhhealth.png" border="0" />
				<br /><br />
				<h3>PROPOSTA DE CREDENCIAMENTO</h3><br />
			</div>
			
			<table class="table table-bordered" style="width: 700px; font-size: 14px;">
				<thead>
					<tr class="active">
						<td style="text-align: center; font-weight: bold;" colspan="2">DADOS DA EMPRESA</td>
					</tr>			
				</thead>
				<tbody>
					<tr>
						<td style="font-weight: bold;">Razão Social:</td>
						<td><?php echo $proposta_credenciamento['PropostaCredenciamento']['razao_social']; ?></td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Nome Fantasia:</td>
						<td><?php echo $proposta_credenciamento['PropostaCredenciamento']['nome_fantasia']; ?></td>
					</tr>
					<tr>
						<td style="font-weight: bold;">CNPJ:</td>
						<td><?php echo Comum::formatarDocumento($proposta_credenciamento['PropostaCredenciamento']['codigo_documento']); ?></td>
					</tr>			
				</tbody>
			</table>
			
			<br />
			<?php foreach($proposta_endereco as $key => $endereco) : ?>
				<table class="table table-bordered" style="width: 700px; font-size: 14px;">
					<thead>
						<tr class="active">
							<td style="text-align: center; font-weight: bold;" colspan="4">ENDEREÇO UNIDADE N. <?php echo $key + 1; ?></td>
						</tr>			
					</thead>
					<tbody>
						<tr>
							<td style="font-weight: bold;">Endereco:</td>
							<td colspan="3"><?php echo $endereco['PropostaCredEndereco']['logradouro']; ?>, <?php echo $endereco['PropostaCredEndereco']['numero']; ?> <?php echo (trim($endereco['PropostaCredEndereco']['complemento']) != "") ? " - " . $endereco['PropostaCredEndereco']['complemento'] : ""; ?></td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Bairro:</td>
							<td><?php echo $endereco['PropostaCredEndereco']['bairro']; ?></td>
							
							<td style="font-weight: bold;">Cidade:</td>
							<td><?php echo $endereco['PropostaCredEndereco']['cidade']; ?></td>
						</tr>
						<tr>							
							<td style="font-weight: bold;">Estado:</td>
							<td><?php echo $endereco['PropostaCredEndereco']['estado']; ?></td>
							
							<td style="font-weight: bold;">Cep:</td>
							<td><?php echo $endereco['PropostaCredEndereco']['cep']; ?></td>							
						</tr>			
					</tbody>
				</table>
			<?php endforeach; ?>
			
			<br />
			<table class="table table-bordered" style="width: 700px; font-size: 14px;">
				<thead>
					<tr class="active">
						<td style="text-align: center; font-weight: bold;" colspan="3">CONTATOS E FUNCIONAMENTO</td>
					</tr>			
				</thead>
				<tbody>
					<tr>
						<td colspan="2"><b>Responsável Técnico:</b></td>
						<td>
							<?php echo $proposta_credenciamento['PropostaCredenciamento']['responsavel_tecnico_nome']; ?> (<?php echo $list_conselhos[$proposta_credenciamento['PropostaCredenciamento']['codigo_conselho_profissional']]; ?> / <?php echo $proposta_credenciamento['PropostaCredenciamento']['responsavel_tecnico_conselho_uf']; ?>: <?php echo $proposta_credenciamento['PropostaCredenciamento']['responsavel_tecnico_numero_conselho']; ?>)
						</td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Responsável Administrativo: </td>
						<td colspan="2"><?php echo $proposta_credenciamento['PropostaCredenciamento']['responsavel_administrativo']; ?></td>
					</tr>
					
					<tr>
						<td style="font-weight: bold;">Telefones: </td>
						<td colspan="3">
							<?php if(trim($proposta_credenciamento['PropostaCredenciamento']['telefone']) != "") : ?>
								<b>Fixo: </b> <?php echo Comum::formatarTelefone($proposta_credenciamento['PropostaCredenciamento']['telefone']); ?>
							<?php endif; ?>
							
							<?php if(trim($proposta_credenciamento['PropostaCredenciamento']['celular']) != "") : ?>
								<b>Celular: </b> <?php echo Comum::formatarTelefone($proposta_credenciamento['PropostaCredenciamento']['celular']); ?>
							<?php endif; ?>
							
							<?php if(trim($proposta_credenciamento['PropostaCredenciamento']['fax']) != "") : ?>
								<b>Fax: </b> <?php echo Comum::formatarTelefone($proposta_credenciamento['PropostaCredenciamento']['fax']); ?>
							<?php endif; ?>
						</td>						
					</tr>
					<tr>
						<td style="font-weight: bold;">E-mail:</td>
						<td colspan="3"><?php echo $proposta_credenciamento['PropostaCredenciamento']['email']; ?></td>
					</tr>
					<tr>
						<td style="font-weight: bold;">Horario de Atendimento:</td>
						<td colspan="3">
							<?php foreach($horarios as $key => $hora) : ?>
								- 
								<?php echo substr(sprintf("%04s", $hora['Horario']['de_hora']), 0, 2); ?>:<?php echo substr(sprintf("%04s", $hora['Horario']['de_hora']), 2, 2); ?>  
								até 
								<?php echo substr(sprintf("%04s", $hora['Horario']['ate_hora']), 0, 2); ?>:<?php echo substr(sprintf("%04s", $hora['Horario']['ate_hora']), 2, 2); ?> 
								(<?php echo $hora['Horario']['dias_semana']; ?>)<br />
							<?php endforeach; ?>
						</td>
					</tr>	
					
					<tr>
						<td colspan="4">
							<b>Tipo de Atendimento:</b><br />
							( <b><?php echo $proposta_credenciamento['PropostaCredenciamento']['tipo_atendimento'] == '1' ? 'X' : ' &nbsp;&nbsp;'; ?></b> ) Hora Marcada &nbsp;&nbsp;&nbsp;&nbsp; ( <b><?php echo $proposta_credenciamento['PropostaCredenciamento']['tipo_atendimento'] == '0' ? 'X' : ' &nbsp;&nbsp; '; ?></b> ) Ordem de Chagada
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<b>Possui disponibilidade para utilização ao Portal RHHealth (acesso via web)?</b><br /> 
							( <b><?php echo $proposta_credenciamento['PropostaCredenciamento']['acesso_portal'] == '1' ? 'X' : ' &nbsp;&nbsp; '; ?></b> ) SIM &nbsp;&nbsp;&nbsp;&nbsp; ( <b><?php echo $proposta_credenciamento['PropostaCredenciamento']['acesso_portal'] == '0' ? 'X' : ' &nbsp;&nbsp; '; ?></b> ) NÃO
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<b>Faz todos os exames em um único local?</b><br /> 
							( <b><?php echo $proposta_credenciamento['PropostaCredenciamento']['exames_local_unico'] == '1' ? 'X' : ' &nbsp;&nbsp; '; ?></b> ) SIM &nbsp;&nbsp;&nbsp;&nbsp; ( <b><?php echo $proposta_credenciamento['PropostaCredenciamento']['exames_local_unico'] == '0' ? 'X' : ' &nbsp;&nbsp; '; ?></b> ) NÃO
						</td>
					</tr>																						
				</tbody>
			</table>
			
			<br />
			<table class="table table-bordered" style="width: 700px; font-size: 14px;">
				<thead>
					<tr class="active">
						<td style="text-align: center; font-weight: bold;" colspan="3">CORPO CLINICO - MÉDICOS QUE REALIZAM EXAMES CLÍNICOS</td>
					</tr>			
				</thead>
				<tbody>			
					<?php foreach($medicos as $key => $medico) : ?>
						<tr>
							<td colspan="2"><?php echo $medico['medico']['nome']; ?></td>
							<td><?php echo $list_conselhos[$medico['medico']['codigo_conselho_profissional']]; ?><?php echo $medico['medico']['conselho_uf'] ? " / " . $medico['medico']['conselho_uf'] : ""; ?>: <?php echo $medico['medico']['numero_conselho']; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<br />
			<table class="table table-bordered" style="width: 700px; font-size: 14px;">
				<thead>
					<tr class="active">
						<td style="text-align: center; font-weight: bold;" colspan="4">RELAÇÃO DE SERVIÇOS PROPOSTOS:</td>
					</tr>
				</thead>
				<tbody>			
					<?php foreach($servicos as $key => $produto) : ?>
						<?php if($key == 'E') : ?>
							<tr class=active">
								<td style="text-align: center; font-weight: bold;" colspan="4">Exames Complementares:</td>
							</tr>
						<?php elseif($key == 'G') : ?>
							<tr class=active">
								<td style="text-align: center; font-weight: bold;" colspan="4" class="alert">Serviços de Engenharia:</td>
							</tr>
						<?php endif;?>
						<tr class=active>
							<td>Exame</td>
							<td>Tempo Liberação</td>
							<td>Valor Proposto</td>
							<td>Contra-proposta</td>
							<td>Valor Fechado</td>
						</tr>
						<?php foreach($produto as $k => $servico) : ?>
							<tr>
								<td><?php echo $servico['descricao']; ?></td>
								<td><?php 
								
								$tempo_liberacao = array(
									'' => "Selecionar",
									1 => "Liberação imediata",
									2 => "1h",
									3 => "2h",
									4 => "3h",
									5 => "4h",
									6 => "5h",
									7 => "6h",
									8 => "7h",
									9 => "8h",
									10 => "9h",
									11 => "10h",
									12 => "11h",
									13 => "12h",
									14 => "13h",
									15 => "14h",
									16 => "15h",
									17 => "16h",
									17 => "17h",
									19 => "18h",
									20 => "19h",
									21 => "20h",
									22 => "21h",
									23 => "22h",
									24 => "23h",
									25 => "24h",
									26 => "1 dias",
									27 => "2 dias",
									28 => "3 dias",
									29 => "4 dias",
									30 => "5 dias",
									31 => "6 dias",
									32 => "7 dias",
									33 => "8 dias",
									34 => "9 dias",
									35 => "10 dias",
									36 => "11 dias",
									37 => "12 dias",
									37 => "13 dias",
									39 => "14 dias",
									40 => "15 dias",
									41 => "16 dias",
									42 => "17 dias",
									43 => "18 dias",
									44 => "19 dias",
									45 => "20 dias",
									46 => "21 dias",
									47 => "22 dias",
									48 => "23 dias",
									49 => "24 dias",
									50 => "25 dias",
									51 => "26 dias",
									52 => "27 dias",
									53 => "28 dias",
									54 => "29 dias",
									55 => "30 dias",
									56 => "31 dias",
									57 => "32 dias",
									58 => "33 dias",
									59 => "34 dias",
									60 => "35 dias",
									61 => "36 dias",
									62 => "37 dias",
									63 => "38 dias",
									64 => "39 dias",
									65 => "40 dias",
									66 => "41 dias",
									67 => "42 dias",
									68 => "43 dias",
									69 => "44 dias",
									70 => "45 dias",
									71 => "46 dias",
									72 => "47 dias",
									73 => "48 dias",
									74 => "49 dias",
									75 => "50 dias",
									76 => "51 dias",
									77 => "52 dias",
									78 => "53 dias",
									79 => "54 dias",
									80 => "55 dias",
									81 => "56 dias",
									82 => "57 dias",
									83 => "58 dias",
									84 => "59 dias",			
									85 => "60 dias"			
								);	

								echo $tempo_liberacao[$servico['liberacao']]; ?>															
							</td>
								<td style="text-align: right;"><?php echo ((trim($servico['valor_proposto']) != '') && ($servico['valor_proposto']) != '0,00') ? ("R$ " . number_format($servico['valor_proposto'], 2, ',', '.')) : '-'; ?></td>
								<td style="text-align: right;"><?php echo ((trim($servico['valor_contra_proposta']) != '') && ($servico['valor_contra_proposta']) != '0,00') ? ("R$ " . number_format($servico['valor_contra_proposta'], 2, ',', '.')) : '-'; ?></td>
								<td style="text-align: right;"><?php echo ((trim($servico['valor']) != '') && ($servico['valor']) != '0,00') ? "R$ " . $servico['valor'] : '-'; ?></td>
							</tr>
						<?php endforeach;?>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<br />
			<?php if(count($bancos)) : ?>
				<table class="table table-bordered" style="width: 700px; font-size: 14px;">
					<thead>
						<tr class="active">
							<td style="text-align: center; font-weight: bold;" colspan="2">INFORMAÇÕES PARA PAGAMENTO</td>
						</tr>			
					</thead>
					<tbody>
						<tr>
							<td style="font-weight: bold;">Banco:</td>
							<td><?php echo key($bancos) . " - " . current($bancos); ?></td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Agência:</td>
							<td><?php echo $proposta_credenciamento['PropostaCredenciamento']['agencia'] ? $proposta_credenciamento['PropostaCredenciamento']['agencia']: ''; ?></td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Número Conta:</td>
							<td>
								<?php echo $proposta_credenciamento['PropostaCredenciamento']['numero_conta'] ? $proposta_credenciamento['PropostaCredenciamento']['numero_conta'] : ''; ?>
								<?php if($proposta_credenciamento['PropostaCredenciamento']['numero_conta']) : ?>
									- <?php echo $proposta_credenciamento['PropostaCredenciamento']['tipo_conta'] == '1' ? '(Conta Pessoa Jurídica)' : '(Conta Pessoa Física)' ?>
								<?php endif; ?> 
							</td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Favorecido:</td>
							<td><?php echo $proposta_credenciamento['PropostaCredenciamento']['favorecido']; ?></td>
						</tr>					
					</tbody>
				</table>			
			<?php endif; ?>	
			
			<br />
			<table class="table table-bordered" style="width: 700px; font-size: 14px;">
				<thead>
					<tr class="active">
						<td style="text-align: center; font-weight: bold;" colspan="3">DOCUMENTOS IMPORTANTES PARA ELABORAÇÃO DE CONTRATO:</td>
					</tr>			
				</thead>
				<tbody>			
					<?php foreach($documentos as $key => $doc) : ?>
						<tr>
							<td>( <b><?php echo $doc['TipoDocumento']['obrigatorio'] == '1' ? ' X ' : ' &nbsp;&nbsp; '?></b> )  </td>
							<td colspan="2"><?php echo $doc['TipoDocumento']['descricao']; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<br />
			<h3>Instruções de Faturamento:</h3>
			
			1. Em cumprindo a NR-7 a 3ª. via do ASO: <br /><br />
			
			- Prontuário Médico (original) e Laudo dos Exames complementares (cópia) realizados, deverão ser encaminhados à RH Health para o devido arquivamento, sob a responsabilidade legal do médico coordenador do PCMSO;<br /> 
			
			- Os documentos físicos deverão ser encaminhados juntamente com a Nota Fiscal para a sede da RH Health aos cuidados do Setor de Contas;<br />
			
			
			2. A entrega do faturamento deverá ocorrer até o dia 10 de cada mês e o pagamento será efetuado (somente com o recebimento dos documentos físicos) entre os dias 20 e 30 do mês subsequente.<br /><br />
			
			3. Pedimos descriminar por meio de relatório os funcionários atendidos, empresa correspondente e exames realizados e cobrados na NF para agilização do processo de pagamento.<br /><br /><br />			
			
			<br />
			<br />
			<br />__________________________________<br />
			<b style="font-size: 14px;"><?php echo $proposta_credenciamento['PropostaCredenciamento']['responsavel_administrativo']; ?></b><br />
			<span style="font-size: 14px;"><?php echo $proposta_credenciamento['PropostaCredenciamento']['razao_social']; ?></span>	
			<br />
			<br />
			<br />
			<br />			
			
			À PARTIR DO PREENCHIMENTO, ASSINATURA E RECEBIMENTO DESTA, FICA AUTORIZADO A INSERÇÃO NO SISTEMA DA RH HEALTH E A LIBERAÇÃO DO ATENDIMENTO NA CLÍNICA / CONSULTÓRIO.
		
		</div>
		<?php echo $this->Javascript->codeBlock('window.print();'); ?>
	</body>
</html>