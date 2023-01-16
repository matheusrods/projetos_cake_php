
<style>
	<?php if($questionario['Questionario']['background']) : ?>
		body {
		    background: #000 url("/portal/files/background/<?php echo $questionario['Questionario']['background']; ?>") no-repeat fixed center center / cover ;
		    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif,sans-serif;
		}	
	<?php else : ?>
		body {
		    background: #000 url("/portal/files/background/background-01.jpg") no-repeat fixed center center / cover ;
		    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif,sans-serif;
		}
	<?php endif; ?>
	
	.check {
	    background: rgba(64, 69, 82, 0.4) none repeat scroll 0 0;
	    color: #fff;
	    cursor: pointer;
	    display: block;
	    font-size: 30px;
	    margin-bottom: 10px;
	    padding: 25px 20px;
	    z-index: 9999;
	}
	.theme-dark h2 {
	    color: #fff;
	    font-size: 50px;
	    text-shadow: 2px 2px 2px #636363;
	}	
	
	.app-canvas {
		width: 90%; margin: 0 auto;
	}
	.app-canvas input[type="radio"] {
  		display: none;
	}
	.app-canvas input[type="radio"] + div {
		height: 30px;
		width: 30px;
		display: inline-block;
		cursor: pointer;
		vertical-align: middle;
		background: #FFF;
		border: 1px solid #333333;
		border-radius: 100%;
	} 
	.app-canvas input[type="radio"] + div:hover {
		border-color: #333333;
	}
	.app-canvas input[type="radio"]:checked + div {
  		background: #3875D7;
  		border: 3px solid #FFF;
	}
	.app-canvas .js-botao-avancar {
	    background: #3875d7 none repeat scroll 0 0;
	    border: 2px solid #ccc;
	    color: #fff;
	    cursor: pointer;
	    float: right;
	    padding: 18px 42px;
	}
	.app-canvas .js-botao-avancar:after{
  		content: " ►";
	}
	.app-canvas .js-botao-voltar{
  		border: 1px solid #ccc;
  		float: left;
  		padding: 18px 42px;
  		color: #fff;
  		background: #D80D0D;
  		cursor: pointer;
	}
	.app-canvas .js-botao-voltar:before{
  		content: "◄ ";
	}
	.app-canvas .avancar{
  		display: none;
	}
	.app-canvas .js-bordered{
  		border: 2px solid #CCC;
	}
	.ajaxLoader{
  		display: none;
  		position: fixed;
  		top: 0;
  		left: 0;
  		bottom: 0;
  		right: 0;
  		background: url('/portal//img/load-gear.gif') center center no-repeat rgba(0,0,0,0.8);
  		z-index: 9999;
	}
	.ajaxLoader > div {
		position: absolute;
		width: 100%;
		top: 50%;
		color: #fff;
		text-align: center;
		text-transform: uppercase;
		font-weight: 600;
		margin-top: -50px;
	}
</style>

<div class="app-canvas hide" data_codigo_questionario="<?php echo $codigo_questionario ?>">
	<div class="margin-top-30">
		<div class="row-fluid">
			<div class="theme-dark titleH2">
		          <h2 id="question" style="font-size: 40px;"><?php echo $questaoInicial['LabelQuestao']['label'] ?></h2>
		     </div>
		</div>
	</div>
	<div class="application" data_codigo_questao="<?php echo $questaoInicial['Questao']['codigo'] ?>">
		<div class="row-fluid respostas">
			<div style="width: 70%; float: right;">
			
				<?php 
				if(isset($questaoInicial['Respostas']) && !empty($questaoInicial['Respostas'])): ?>
					<?php foreach ($questaoInicial['Respostas'] as $key => $resposta) : ?>
						<div class="check">
							<div class="caixa">
								<div class="col-md-10">
									<?php echo $resposta['Respostas'][0]['label'] ?>
								</div>
								<div class="col-md-2">
									<input type="radio" name="resposta" value="<?php echo $resposta['codigo'] ?>">
									<div class="js-radio"></div>							
								</div>
								<div style="clear: both;"></div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif;?>
			</div>
		</div>
		
		<div style="clear: both;"><br /><br /><br /></div>
		
		<div class="row-fluid avancar">
			<div class="span12">
				<?php if(!$primeira_questao) : ?>
					<div class="js-botao-voltar">
						Voltar
					</div>
				<?php endif;  ?>
				<div class="js-botao-avancar">
					Avançar
				</div>
			</div>
		</div>
	</div>
</div>

<div class="ajaxLoader">
	<div>Carregando</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		executa_questionario();

		var codigo = '<?php echo (!empty($pular_questao[0][0]['codigo']))? $pular_questao[0][0]['codigo'] : 0 ?>';
		var codigo_questao = '<?php echo (!empty($pular_questao[0][0]['codigo_questao']))? $pular_questao[0][0]['codigo_questao'] : 0 ?>';

		if(codigo > 0 && codigo_questao > 0) {
			$('.ajaxLoader').fadeIn();
			pula_primeira_questao(codigo, codigo_questao, function(response) {
				if(response) {
					$('.app-canvas').hide().removeClass('hide').fadeIn();
					view_quest();
				}
			});
		} else {
			$('.app-canvas').hide().removeClass('hide').fadeIn();
			view_quest();
		}

	});
</script>
