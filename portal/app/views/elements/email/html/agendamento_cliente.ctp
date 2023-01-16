<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
            Confirmação de agendamento de exame(s)
            <br /><br />
        	Olá <strong><?php echo $dados['nome']; ?></strong>, tudo bem?
        	<br /><br />
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
           Estamos entrando em contato para informar que o(s) exame(s) do funcionário <b><?php echo $dados['funcionario_nome']; ?></b> está(ão) agendado(s).<br /><br />
           Os detalhes seguem abaixo:<br /><br />
        </td>
    </tr>
        <?php foreach($dados['dados_exames'] as $item => $exame): 
            $hora = str_pad($exame['hora'],4,"0",STR_PAD_LEFT);
        ?>
            <tr>
                <td style="font-size:12px;">
                    <!-- se exame agendado -->
                        <?php
                        $exame['tipo_atendimento'] = (isset($exame['tipo_atendimento'])) ? $exame['tipo_atendimento'] : 0;
                        ?>
                        <?php if(!empty($exame['data']) && !empty($exame['hora']) && $exame['tipo_atendimento'] == 1): ?> 
                                <b>Exame:</b> <?=$exame['exame']?> <br />
                                <b>Exame ocupacional:</b> <?=$exame['tipo_ocupacional']; ?> <br />
                                <b>Credenciado:</b> <?=$exame['empresa_nome']?> <br />
                                <b>Endereço do credenciado:</b> <?=$exame['empresa_endereco']?> <br />
                                <b>Data:</b> <?=$exame['data']?> <br />
                                <b>Horário: </b><?php echo substr($hora, 0,2).':'.substr($hora,-2);?> <br />

                        <?php else: ?>

                                <b>Exame:</b> <?=$exame['exame']?> <br />
                                <b>Credenciado:</b> <?=$exame['empresa_nome']?> <br />
                                <b>Endereço do credenciado:</b> <?=$exame['empresa_endereco']?> <br />
                            <?php  if(!empty($exame['horario_fornecedor'])): ?>
                                    <b>Horário de atendimento</b><br />
                                <?php foreach($exame['horario_fornecedor'] as $hora => $horario): 
                                        $hora_ini = str_pad($horario['FornecedorHorario']['de_hora'],4,"0",STR_PAD_LEFT);
                                        $hora_fim = str_pad($horario['FornecedorHorario']['ate_hora'],4,"0",STR_PAD_LEFT);
                                ?>
                                        <?php echo ($horario['FornecedorHorario']['dias_semana']).' - '.substr($hora_ini, 0,2).':'.substr($hora_ini,-2);?> até <?php echo substr($hora_fim, 0,2).':'.substr($hora_fim,-2); ?> <br />
                                <?php endforeach;?>
                            <?php   endif; ?>

                            <?php if(!empty($exame['data'])): ?>
                                <br />
                                <b>Data de agendamento:</b> <?=$exame['data']?> <br />
                                <b>Horário: </b>Ordem de chegada <br />
                            <?php endif; ?>


                           <br />OBS: o atendimento é feito por ordem de chegada <br /><br />
                        <?php endif; ?>
                    </td>
                </tr>
        <?php endforeach; ?> 
        <tr>
            <td style="font-size:12px;"> 
                <br />
                 O material para o seu atendimento está anexo nesta mensagem.<br /><br />
                Obrigado!<br /><br />
            </td>
        </tr>       
        <tr>
            <td style="font-size:12px;">
                <br />
                Um abraço<br />
                <b>Equipe RH Health</b><br />
                Tel. 0800-591-0286<br />
                <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a><br />
            </td>
        </tr>
</table>