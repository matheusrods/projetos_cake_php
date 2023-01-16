<?php $codigo_ficha = empty($codigo_ficha) ? '' : $codigo_ficha; 
//debug($sinistro);
//debug($this->data['Profissional']['codigo_documento']);
//debug($ocorrencia_carreta);
//debug($ocorrencia_bitrem);

if (!empty($this->data['Profissional']['codigo_documento'])){
?>

<table  class="table table-condensed table-striped" >
<tr><th style="background-color:#DBEAF9;" class="input-large" colspan="12" align="center" >Sinistro : <?php echo strtoupper(COMUM::formatarDocumento($this->data['Profissional']['codigo_documento'])); ?></th>
</tr>
<?php
if (!empty($sinistro)){
?>
<tr><th style="background-color:#DDDDDD" class="input-large" >SM</th> 
    <th style="background-color:#DDDDDD" class="input-large">Data</th>
    <th style="background-color:#DDDDDD" class="input-large">Embarcador</th>
    <th style="background-color:#DDDDDD" class="input-large">Transportador</th>
    <th style="background-color:#DDDDDD" class="input-large">Motorista</th> 
    <th style="background-color:#DDDDDD" class="input-large">Seguradora</th>
    <th style="background-color:#DDDDDD" class="input-large">Corretora</th>
    <th style="background-color:#DDDDDD" class="input-large">Natureza</th>
   

</tr>
    <?php foreach ($sinistro as $log) {
            print "<tr>";  
            print "<td>".$this->Buonny->codigo_sm($log['Sinistro']['sm'])."</td>";
            print "<td>".$log['Sinistro']['data_evento']."</td>";
            print "<td>".$log['Embarcador']['razao_social']."</td>";
            print "<td>".$log['Transportador']['razao_social']."</td>";
            print "<td>".$log['Profissional']['nome']."</td>";
            print "<td>".$log['Seguradora']['nome']."</td>";
            print "<td>".$log['Corretora']['nome']."</td>";
            print "<td>".$natureza[$log['Sinistro']['natureza']]."</td>";
            print "</tr>";
         }
}else {
       
        print "<tr>";  
        print "<td colsopan='5'>Profissional sem sinistro</td>";
        print "</tr>";
}
}
?>
</table>



