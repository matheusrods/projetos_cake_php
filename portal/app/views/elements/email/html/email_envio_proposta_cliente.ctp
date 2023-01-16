<p>
Prezado <?=$nome_contato?>,
</p>

<p>
Segue abaixo a proposta solicitada, referente aos produtos:<br/>
<ul>
<?php foreach ($produtos as $key => $produto) {
    echo "<li>".$produto."<br/></li>";
}
?>
</ul>
</p>

<p>
Clique no link abaixo para visualiz&aacute;-la: <br/>
<a href="http://<?=$link_proposta?>">Visualizar Proposta Comercial</a>
</p>

<p>
Em caso de d&uacute;vidas estamos &agrave; sua disposi&ccedil;&atilde;o
</p>

<p>
Atenciosamente
</p>

<p>
Buonny - Depto. Comercial
</p>
<p>
    Este e-mail foi enviado automaticamente pelo Sistema Comercial Buonny.
    Favor não responder. 
    Em caso de dúvidas, entre em contato com: comercial@buonny.com.br
</p>
