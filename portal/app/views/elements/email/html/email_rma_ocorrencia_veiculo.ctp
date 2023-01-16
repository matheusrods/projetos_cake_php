<html>
<head>
<title>RMA</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body lang="pt-BR" dir="ltr">
<center>
    <div align="justify" style="width: 80%; font: 10pt Verdana, Arial;">
        <?php echo $cliente ?> - SM: <?php echo $ocorrencia['TViagViagem']['viag_codigo_sm'] ?><br><br>
        Analisando o monitoramento do veiculo de vossa empresa, de placa <?php echo $ocorrencia['TVeicVeiculo']['veic_placa'] ?>, equipamento <?php echo $ocorrencia['TTermTerminal']['term_numero_terminal'] ?> e sistema <?php echo $ocorrencia['TTecnTecnologia']['tecn_descricao'] ?><?php echo (!empty($ocorrencia['TPessPessoa']['pess_nome']) ? ', conduzido pelo '.$ocorrencia['TPessPessoa']['pess_nome'] : '') ?>, identificamos a(s) seguinte(s) ocorrencia(s):<br><br>
        <b>Ocorrencia:</b> Sinal nao disponibilizado para o Buonny Sat<br><br>
        <b>Consequencia:</b> Impossibilidade da visualizacao do veiculo em sistema.<br><br>
        <b>Acao:</b> Intervenção imediata junto a empresa, no sentido de providenciar, a disponibilizacao do Sinal.<br><br>
        <b>Sugestao:</b> Antecipacao nestes casos, com espaço de tempo suficiente para nao comprometer o inicio das viagens. Certificacao previa deste quesito quando da emissão do formulario SM - Solicitacao de Monitoramento.<br><br><br><br>
        No aguardo de vossa apreciacao e na certeza de que providencias serao tomadas.<br><br><br>
        Atenciosamente.<br><br>
        <span style="color: blue; font-size: 12pt"><b>Buonny Sat</b></span><br>
        <?php echo ($usuario ? $usuario['Usuario']['nome'] : $ocorrencia['TOveiOcorrenciaVeiculo']['ovei_usuario_tratamento']) ?><br>
        Operador de Monitoramento<br>
        (11) 5079-2558<br>
        <a href="mailto:buonnysat@buonny.com.br">buonnysat@buonny.com.br</a>
    </div>
</center>
</body>
</html>