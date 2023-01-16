<!DOCTYPE html>
<html>
<head>
<title>Sincronização de Alvos</title>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>
</head>

<body>
<heade>
<h4>Sincronização de Alvos</h4>
</heade>

<div id="content">
	<h5>Total de Registros <?= $total ?></h5>
	<form>
		<input name="qtd" type="text" value="100" /><br />
		<input name="submit" type="button" value="Disparar Porcessos" /><br />
	</form>

	<ul class="lista"></ul>
</div>

<foot>
	<p>

		Processos executados em paralelo.
	</p>
</foot>
<script type="text/javascript">
	$(function(){
		var total = <?= $total ?>;

		$('form input[name$="submit"]').click(function(){
			var qtd = $('form input[name$="qtd"]');
			var paginas = parseInt(total/qtd.val());
			var ul_lista = $('ul.lista');

			if(total%qtd.val())
				paginas++;

			for (var i=1;i <= paginas; i++){
				ul_lista.append('<li class="'+i+'">Processo '+i+' Iniciado.</li>')
				sincroniza(	i,qtd.val());

			}
			
			return false;
		});

		function sincroniza(pagina,tamanho){
			var li = $('ul.lista li.'+pagina);
			
			$.ajax({
				url: 'http://<?= $_SERVER['SERVER_NAME']?>/portal/clientes_terceiro/sincroniza/'+pagina+'/'+tamanho+'/'+ Math.random(),
				dataType: 'html',
				beforeSend: function(){
					animate(li);
				},
				success: function(data){
					//ul_lista.append('<li>Processo '+pagina+' Finalizado com sucesso!</li>');
					li.html('Processo '+pagina+' Finalizado com sucesso!');
					li.stop();
					li.css({'opacity':''});
				},
				error: function(){
					//ul_lista.append('<li>Processo '+pagina+' Falhou.</li>')
					li.html('Processo '+pagina+' Falhou!');
					li.stop();
					li.css({'opacity':''});
				},
			});
			
		}

		function animate(li){
			li.fadeToggle(700,function(){
				animate(li);
			});
		}

	});
</script>
