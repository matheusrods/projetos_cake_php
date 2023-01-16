<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
        	Olá, <?=$dados['Cliente']['nome_fantasia']?>, tudo bem? Como você está?
        	<br /><br />
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">          
            <p>  
            Fizemos mudanças em alguns procedimentos e preciso explicar uma muito importante para você, para que a nossa parceria continue funcionando bem.<br>
            Até hoje, você estava acostumado a receber um e-mail nosso, todo primeiro dia do mês, com uma planilha anexa com a base de dados. Você sempre preencheu essa planilha e nos devolveu até o dia 3, certo?<br>
            Então, isso mudou. Mas para melhor! Você vai perceber que a coisa está mais simples, funcional e rápida.<br>
            Agora, o nosso e-mail não vai mais ter essa planilha. No lugar dela, você precisará apenas clicar <a href="http://<?=$host?>/portal/funcionarios/index_percapita/<?=$dados['Cliente']['codigo']?>">neste link</a>. Dessa maneira, acessará o sistema automaticamente, fará todas as alterações necessárias (como entrada e saída de funcionários, alterações cadastrais...) e salvará.<br>
            Assim, já teremos todas as informações que precisamos para que, no dia 5, o faturamento seja gerado diretamente.<br>
            Bem mais fácil, né? <br>
            Nós gostaríamos de pedir que você desse atenção com carinho a esse procedimento, porque, caso ele não seja feito corretamente, teremos de gerar esse faturamento com as informações que já temos, ok? E, aí, qualquer correção ou compensação ficará apenas para o mês seguinte.<br><br>
            
            <?php 
            if( $link ){
                ?>
                A planilha de exames complementares está disponível <a href='<?=$link?>'>neste link</a>.<br><br>
                <?php  
            }
            ?>

            Se tiver qualquer dúvida, não pense duas vezes antes de entrar em contato pelos nossos canais de atendimento:<br>
            E-MAIL<br>
            relacionamento@rhhealth.com.br<br>
            suportesistema@rhhealth.com.br<br>
            </p>
            <p>
            TELEFONE <br>
            (11) 5079-2550<br>
            (11)5079-2521<br>
            <br><br>
            Obrigado por entender o novo procedimento. Lembrando sempre que é assim que continuamos evoluindo e entregando a melhor experiência possível.<br>
            <br><br>
            Boa semana!<br>
            Um abraço,<br>
            RH Health
            </p>
        </td>
    </tr>       
    <tr>
        <td style="font-size:12px;">
        	<br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="http://www.rhhealth.com.br" target="_blank">www.rhhealth.com.br</a><br />
        </td>
    </tr>
</table>