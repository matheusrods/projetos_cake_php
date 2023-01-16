<title>Hora Extra n&atilde;o autorizada</title>
<table width=80%>
  <tr>
    <th align="left">Usuario</th>
    <th align="left">IP</th>
    <th align="left">Data Ponto</th>
    <th align="left">Tipo de Registro</th>
    <th align="left">Hora <?= comum::trata_nome($ponto_eletronico['TipoPontoEletronico']['descricao_ponto_eletronico']);?> Configurada</th>
  </tr>
  <tr>        
    <td><?= $ponto_eletronico['Usuario']['apelido'] ?></td>   
    <td><?= $ponto_eletronico['PontoEletronico']['numero_ip'] ?></td>   
    <td><?= $ponto_eletronico['PontoEletronico']['data_ponto'] ?></td>   
    <td><?= comum::trata_nome($ponto_eletronico['TipoPontoEletronico']['descricao_ponto_eletronico']) ?></td>
    <td>
      <?php 
      if( $config_horario_trabalho['Usuario']['escala'] ):
        if( $ponto_eletronico['TipoPontoEletronico']['codigo'] == 1 ):
          echo substr($config_horario_trabalho['UsuarioEscala']['data_entrada'], 0, 16);
        else:
          echo substr($config_horario_trabalho['UsuarioEscala']['data_saida'], 0, 16);
        endif;
      else:
        if( $ponto_eletronico['TipoPontoEletronico']['codigo'] == 1 ):
          echo date('d/m/Y').' '.trim(substr($config_horario_trabalho['UsuarioExpediente']['entrada'], 0, 5));     
        else:          
          echo date('d/m/Y').' '.trim(substr($config_horario_trabalho['UsuarioExpediente']['saida'], 0, 5));     
        endif;
      endif;
      ?>    
    </td>
  </tr>
</table>
<br />