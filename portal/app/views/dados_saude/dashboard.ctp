		<nav class="navbar navbar-default">
			<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Menu</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
						</button>
						<a class="navbar-brand logo" href="/"> <img src="/portal/img/todosbem/logo.png" alt="logomarma"> </a>
					</div>
					<div class="navbar-collapse collapse">
						<ul class="nav navbar-nav navbar-right">
							<li class="active hidden-sm hidden-md"><a href="/">Home</a></li>
							<li class="dropdown ">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"> 
									Minha Conta <span class="caret"></span>
								</a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="#">Mudar Senha</a></li>
									<li class="divider"></li>
									<li class="dropdown-header hidden-xs">Configurações</li>
									<li><a href="#">Minhas Preferências</a></li>
									<li><a href="/portal/usuarios/logout">Sair do Sistema</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</nav>
		<div class="container">
			<div class="col-md-3 col-sm-3 col-xs-12">
				<div class="box">
					<div class="avatar">
						<?php if(isset($Usuarios_info['UsuariosDados']['avatar']) && !empty($Usuarios_info['UsuariosDados']['avatar'])) : ?>
							<img class="img-thumbnail" src="/portal/files/avatar/<?php echo $Usuarios_info['UsuariosDados']['avatar']; ?>" alt="Foto de Perfil">
						<?php else : ?>
							<img class="img-thumbnail" src="/portal/img/todosbem/avatar.jpg" alt="Foto de Perfil">
						<?php endif; ?>
						<a href="javascript:void(0)" onclick="manipula_modal('modal_avatar', 1);" class="btn btn-default btn-xs btn-upload-float">alterar foto de perfil</a>
					</div>
					<div class="perfil">
						<div class="nome">
							<p><strong><?php echo $usuario_info['Usuario']['nome']; ?></strong> <br> <?php echo $Usuarios_info['UsuariosDados']['idade']; ?><br></p>
						</div>
						<ul class="menu-sidebar">
							<li><a href="#" class="noborder">Conteúdo de Saúde</a></li>
							<li><a href="#" class="noborder">Plano de Saúde</a></li>
							<li><a href="#" class="noborder">Seu Resultado</a></li>
							<li><a href="#" class="noborder">Resultado dos Checkups</a></li>
							<li><a href="#" class="noborder">Minhas Preferências</a></li>
							<Li><a href="/portal/usuarios/logout" class="noborder">Sair do Sistema</a></li>
							</ul>						
						</div>					
					</div>
				</div>

				<div class="col-md-9 col-sm-9 col-xs-12">

					<?php if(isset($todos_questionarios['questionarios']) && count($todos_questionarios['questionarios'])) : ?>
						<p style="font-size: 1.3em;">
							<strong><?php echo $usuario_info['Usuario']['nome']; ?></strong>, você não terminou de responder todos os check-ups
						</p>
						<div class="box">
							<div class="row">
								<div class="col-md-12">
									<p class="heading1">Percentual de checkups respondidos: <span> <?php echo number_format((100 / $todos_questionarios['qtd_questionarios']) * $todos_questionarios['qtd_respondidos'], 0); ?>%</span></p>
									<div class="progress">
										<div class="progress-bar" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo  (100 / $todos_questionarios['qtd_questionarios']) * $todos_questionarios['qtd_respondidos']; ?>%">
											<span class="sr-only"><?php echo (100 / $todos_questionarios['qtd_questionarios']) * $todos_questionarios['qtd_respondidos']; ?>.0%Complete</span>
										</div>
									</div>
									<p class="str">
										Você respondeu <strong><?php echo $todos_questionarios['qtd_respondidos']; ?></strong> check-ups de um total de <strong><?php echo $todos_questionarios['qtd_questionarios']; ?></strong>
									</p>
								</div>
								<div class="col-md-3" style="display:none">
									<a href="javascript:void(0);" class="btn btn-star"> Responder Todos <span class="arrow"></span>
									</a>
								</div>
							</div>
						</div>
						<br />

						<p style="font-size: 1.6em;">Sequência dos Checkups</p>
						<div class="box" style="height: 240px !important; padding: 15px;">
							<div class="" style="opacity: 1; display: block; padding: 15px;">
								<div id="myCarousel" class="carousel slide">
									<div class="carousel-inner" style="width: 775px; left: 0px; display: block; min-height: 180px;">
										<div class="item active">
											<?php foreach($todos_questionarios['questionarios'] as $key => $questionario) { 
												if($key > 0 && $key % 5 == 0) echo '</div><div class="item">';
												?>
												<div class="pull-left" style="width: 155px;">
													<?php if(isset($questionario['Questionario']['habilitado']) && ($questionario['Questionario']['habilitado'] == '1')) : ?>
														<div class=" text-center activeSurvey">
															<a data-toggle="tooltip" data-placement="top" href="/portal/questionarios/responder_questionario/<?php echo $questionario['Questionario']['codigo']; ?>" data-toggle="tooltip" title="Fazer o Checkup"> 
																<img style="width: 100px" src="/portal/files/icone/<?php echo $questionario['Questionario']['icone']; ?>" border="0">
															</a> 
															<a data-toggle="tooltip" data-placement="top" href="/portal/questionarios/responder_questionario/<?php echo $questionario['Questionario']['codigo']; ?>" data-toggle="tooltip" title="Fazer o Checkup" >
																<p><?php echo $questionario['Questionario']['descricao']; ?></p>
															</a> 
															<a data-toggle="tooltip" data-placement="top" href="/portal/questionarios/responder_questionario/<?php echo $questionario['Questionario']['codigo']; ?>" class="btn btn-success" data-original-title="" title="">Fazer agora</a>												
														</div>
													<?php elseif(isset($questionario['Questionario']['habilitado']) && $questionario['Questionario']['habilitado'] == '0') : ?>
														<div class=" disabled noclick noclickresultado text-center">
															<a href="javascript:void(0);" class="noclick">
																<img style="width: 100px" src="/portal/files/icone/<?php echo $questionario['Questionario']['icone']; ?>" border="0">
															</a>
															<a href="javascript:void(0);" class="noclick">
																<p><?php echo $questionario['Questionario']['descricao']; ?></p>
															</a>
														</div>
													<?php else : ?>
														<div class=" text-center">
															<a href="javascript:void(0);" class="disabled noclick"  data-toggle="tooltip" title="Resultado"  onclick="exibe_resultados_questionarios(<?php echo $questionario['Questionario']['codigo']; ?>);">
																<img style="width: 100px" src="/portal/files/icone/<?php echo $questionario['Questionario']['icone']; ?>" border="0">
															</a>
															<a href="javascript:void(0);" onclick="exibe_resultados_questionarios(<?php echo $questionario['Questionario']['codigo']; ?>);" class="btn-history" data-toggle="tooltip" title="Resultado" >
																<p><?php echo $questionario['Questionario']['descricao']; ?></p>
															</a>
															<a href="/portal/questionarios/responder_questionario/<?php echo $questionario['Questionario']['codigo']; ?>" class="btn btn-danger">
																REFAZER 
															</a>
														</div>												
													<?php endif; ?>
												</div>
												<?php } ?>
											</div>
										</div>
										<!-- Carousel nav -->
										<a class="carousel-control aleft" href="#myCarousel" data-slide="prev">&lsaquo;</a>
										<a class="carousel-control aright" href="#myCarousel" data-slide="next">&rsaquo;</a>
									</div>
									<div class="owl-controls clickable" style="display: none;">
										<div class="owl-pagination"
										><div class="owl-page">
										<span class=""></span>
									</div>
								</div>
								<div class="owl-buttons">
									<div class="owl-prev"></div>
									<div class="owl-next"></div>
									
								</div>
							</div>
						</div>
					</div>
					<br />				
				<?php endif; ?>
				
				<div class="row">
					<div class="col-md-6">
						<div class="box box_medium">
							<p class="box_heading">
								<span class="icon-dados-antropometricos"></span> Peso e Altura
							</p>
							<div class="box-content">
								<div class="row">
									<div class="col-md-4 col-xs-4 col-sm-3">
										<div class="box-content" style="margin-left:20px">
											<span class="lb">ALTURA:</span><br>
											<span class="lb">PESO:</span>  
										</div>
									</div>
									<?php if(isset($Usuarios_imc) && !empty($Usuarios_imc)) : ?>
										<div class="col-md-4 col-xs-4 col-sm-3">
											<div class="box-content">
												<span id="userHeight" class="str-weight"><?php echo str_replace(".", "", number_format($Usuarios_imc[0]['UsuariosImc']['altura'], 2, '.', '')); ?> cm</span> 
												<br> 
												<span id="userWeight" class="str-weight"><?php echo (int) $Usuarios_imc[0]['UsuariosImc']['peso']; ?> kg</span>
											</div>
										</div>
									<?php else : ?>
										<div class="col-md-4 col-xs-4 col-sm-3">
											<div class="box-content">
												<span id="userHeight" class="str-weight">-</span> 
												<br> 
												<span id="userWeight" class="str-weight">-</span>
											</div>
										</div>																		
									<?php endif; ?>
								</div>

								<?php if(isset($Usuarios_imc) && !empty($Usuarios_imc)) : ?>
									<?php $imc = number_format($Usuarios_imc[0]['UsuariosImc']['peso'] / ($Usuarios_imc[0]['UsuariosImc']['altura'] * $Usuarios_imc[0]['UsuariosImc']['altura']), 1); ?>
									<div class="col-md-3 col-xs-4 col-sm-3">
										<div class="box-content">
											<p>
												<span class="lb">IMC:</span>
												<br> 
												<strong class="str_imc <?php echo ($imc > 25) ? 'bad' : 'good'; ?>"><?php echo $imc; ?></strong>
											</p>
										</div>
									</div>
									<p id="userImcMsg"  class="center label_status <?php echo ($imc > 25) ? 'bad' : 'good'; ?>" >
										<?php if($imc < 17) : ?>
											Muito Abaixo do Peso
										<?php elseif(($imc > 17) && ($imc < 18.5)) : ?>
											Abaixo do Peso
										<?php elseif(($imc >= 18.5) && ($imc < 24.99)) : ?>
											Peso Normal
										<?php elseif(($imc >= 25) && ($imc < 29.99)) : ?>
											Acima do Peso
										<?php elseif(($imc >= 30) && ($imc < 34.99)) : ?>
											Obesidade 1
										<?php elseif(($imc >= 35) && ($imc < 39.99)) : ?>
											Obesidade 2 (severa)
										<?php elseif(($imc >= 40)) : ?>
											Obesidade 3 (mórbida)
										<?php endif; ?>
									</p>
								<?php endif; ?>
							</div>
						</div>
						<div class="box_base">
							<a href="javascript:void(0);" onclick="manipula_modal('modal_historico_imc', 1);" class="btn-history">
								<span class="glyphicon glyphicon-time"></span> Histórico
							</a>
							<a href="javascript:void(0);" onclick="manipula_modal('modal_imc', 1);" class="bold btn-add">
								Adicionar Peso e Altura! <span class="arrow"></span>
							</a>
						</div>	                	
					</div>
					<div class="col-md-6">
						<div class="box box_medium">
							<p class="box_heading">
								<span class="icon-medicamentos"></span> Medicamentos em uso
							</p>
							<div class="box-content" id="lista_medicamentos">

								<?php if(isset($Usuarios_medicamento) && count($Usuarios_medicamento)) : ?>
									<div class="row m-padding10">
										<div class="col-md-12" style="font-size: 11px; max-height: 159px; overflow-y: auto;">
											<ul class="listDrugsInUse" style="">
												<?php foreach($Usuarios_medicamento as $key => $campo) : ?>
													<li>
														<a href="javascript:void(0);" class="label label-danger" onclick="remove_medicamento('<?php echo $campo['Medicamento']['codigo']; ?>', this);"><span class="glyphicon glyphicon-remove" style="color: #FFF;"></span></a>
														<?php echo $campo['Medicamento']['descricao']; ?> - <?php echo $campo['Medicamento']['principio_ativo']; ?>   (<?php echo $campo['Medicamento']['posologia']; ?>)
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
								<?php else : ?>
									<div class="row m-padding10">
										<div class="col-md-4">
											<div class="icon-drungs"></div>
										</div>
										<div class="col-md-8">
											<div class="box-content paddingR20">
												<p class="str_clean">Se você faz uso ou já fez regular de algum medicamento, cadastre-o no link abaixo</p>
											</div>
										</div>
									</div>								
								<?php endif; ?>
							</div>
						</div>
						<div class="box_base">
							<a href="javascript:void(0)" onclick="manipula_modal('modal_medicamento', 1);" class="bold btn-add">Adicionar ou Remover Medicamentos! <span class="arrow"></span></a>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-4">
						<div class="box box_medium">
							<p class="box_heading"> <span class="icon-colesterol"></span> Colesterol </p>
							<div class="box-content">
								<div class="row m-padding10 ">
									<div class="col-md-12 col-xs-12">
										<div class="box-content">
											<?php if(isset($Usuarios_colesterol) && count($Usuarios_colesterol)) : ?>
												<div class="row m-padding10 ">
													<div class="col-md-4 col-xs-6">
														<div class="lb upper paddingL20">Total:</div>
														<div class="lb upper paddingL20">HDL:</div>
														<div class="lb upper paddingL20">LDL:</div>
														<div class="lb upper paddingL20">Tri.:</div>
													</div>
													<div class="col-md-4 col-xs-3">
														<div id="cTotal" class="lb_value text-right"><?php echo $Usuarios_colesterol[0]['UsuariosColesterol']['total']; ?></div>
														<div id="cHdl" class="lb_value text-right"><?php echo $Usuarios_colesterol[0]['UsuariosColesterol']['hdl']; ?></div>
														<div id="cLdl" class="lb_value text-right"><?php echo $Usuarios_colesterol[0]['UsuariosColesterol']['ldl']; ?></div>
														<div id="cTri" class="lb_value text-right"><?php echo $Usuarios_colesterol[0]['UsuariosColesterol']['triglicerideos']; ?></div>
													</div>
													<div class="col-md-3 col-xs-3 zerar">
														<div class="lb_small">mg/dL</div>
														<div class="lb_small">mg/dL</div>
														<div class="lb_small">mg/dL</div>
														<div class="lb_small">mg/dL</div>
													</div>
												</div>
											<?php else : ?>
												<p class="str_clean">informe aqui os dados de Colesterol Total, HDL, LDL e Triglicerídeos</p>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="box_base">
							<a class="btn-history" href="javascript:void(0);"  onclick="manipula_modal('modal_historico_colesterol', 1);">
								<span class="glyphicon glyphicon-time"></span> Histórico
							</a>
							<a href="javascript:void(0);" onclick="manipula_modal('modal_colesterol', 1);" class="bold btn-add">
								Adicionar Dados! <span class="arrow"></span>
							</a>
						</div>
					</div>
					<div class="col-md-4">
						<div class="box box_medium">
							<p class="box_heading"> <span class="icon-circunferencia-abdominal"></span> Abdominal </p>
							<div class="box-content">
								<div class="row m-padding10">
									<div class="col-md-12 col-xs-6" style="">
										<span class="lb paddingL10">MEDIDA:</span>
									</div>
									<div class="col-md-12 col-xs-6">
										<?php if(isset($Usuarios_abdominal) && count($Usuarios_abdominal)) : ?>
											<span class="user_measure paddingL10" id="measure_value"> <?php echo $Usuarios_abdominal[0]['UsuariosAbdominal']['largura']; ?></span> <span class="user_measure_small">cm</span>
										<?php else : ?>
											<p class="str_clean">Informe as medidas abdominais.</p>
										<?php endif; ?>
									</div>
								</div>
								<br />
							</div>
						</div>
						<div class="box_base">
							<a class="btn-history" href="javascript:void(0);" onclick="manipula_modal('modal_historico_abdominal', 1);">
								<span class="glyphicon glyphicon-time"></span> Histórico
							</a>
							<a class="bold btn-add" onclick="manipula_modal('modal_abdominal', 1);">
								Adicionar Dados! <span class="arrow"></span>
							</a>
						</div>
					</div>
					<div class="col-md-4">
						<div class="box box_medium">
							<p class="box_heading"> <span class="icon-psa"></span> PSA </p>
							<div class="box-content">
								<?php if(isset($Usuarios_psa) && count($Usuarios_psa)) : ?>
									<div style="margin: 10px: border: 1px solid #f2f2f2;" class="row"> 
										<div class="col-md-12 col-xs-12"> 
											<span class="lb paddingL10">PSA Total:</span>
											<br>
											<span class="lb_value paddingL10" id="measure_value"> 
												<?php echo $Usuarios_psa[0]['UsuariosPsa']['psa_total']; ?>
											</span> <span class="user_measure_small">ng/mL</span>
										</div>
									</div> 
									<div class="row"> 
										<div class="col-md-12 col-xs-12">
											<span class="lb paddingL10">PSA Livre:</span>
											<br>
											<span class="lb_value paddingL10" id="measure_value"> 
												<?php echo $Usuarios_psa[0]['UsuariosPsa']['psa_livre']; ?>
											</span> <span class="user_measure_small">ng/mL</span>
										</div>
									</div>
								<?php else : ?>
									<div class="row">
										<div class="col-md-12 col-xs-12">
											<div class="box-content">
												<p class="str_clean">Informe os dados de PSA Total e PSA Livre</p>
											</div>
										</div>
									</div>							
								<?php endif; ?>
							</div>
						</div>
						<div class="box_base">
							<a href="javascript:void(0);" onclick="manipula_modal('modal_historico_psa', 1);" class="btn-history">
								<span class="glyphicon glyphicon-time"></span> Histórico
							</a>
							<a href="javascript:void(0);" onclick="manipula_modal('modal_psa', 1);" class="bold btn-add">
								Adicionar Dados! <span class="arrow"></span>
							</a>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="box box_medium">
							<p class="box_heading"> <span class="icon-heart-rate"></span> Pressão Arterial </p>
							<?php if(isset($Usuarios_pressao_arterial) && count($Usuarios_pressao_arterial)) : ?>
								<div class="box-content">
									<div class="row paddingL10 m-padding10">
										<div class="col-md-7 col-xs-7">
											<span class="lb upper">Frequência cardiaca:</span>
										</div>
										<div class="col-md-5 col-xs-5 center paddingT20">
											<div class='good'>
												<span class="str_heartrate"><?php echo $Usuarios_pressao_arterial[0]['UsuariosPressaoArterial']['frequencia_cardiaca']; ?></span> <span class="bpm">bpm</span>
											</div>
										</div>
									</div>
									<hr />
									<div class="row paddingT10 paddingL10 m-padding10">
										<div class="col-md-7 col-xs-7">
											<span class="lb upper">Pressão Arterial:</span>
										</div>
										<div class="col-md-5 col-xs-5 center">
											<span class="str_blood_pressure" id="blood_pressure"><?php echo $Usuarios_pressao_arterial[0]['UsuariosPressaoArterial']['pressao_arterial_auto']; ?>/<?php echo $Usuarios_pressao_arterial[0]['UsuariosPressaoArterial']['pressao_arterial_baixo']; ?></span>
										</div>
									</div>
								</div>
							<?php else : ?>
								<div class="row m-padding10">
									<div class="col-md-3">
										<div class="icon-heart-rate-monitor"></div>
									</div>
									<div class="col-md-9">
										<div class="box-content paddingR20">
											<p class="str_clean">
												Informe sua <strong>Frequência cardíaca</strong> por minuto e/ou <strong>Pressão Arterial</strong>, caso tenha feito essas medições.
											</p>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<div class="box_base">
							<a class="btn-history" href="javascript:void(0);"  onclick="manipula_modal('modal_historico_pressao_arterial', 1);">
								<span class="glyphicon glyphicon-time"></span> Histórico
							</a>
							<a href="javascript:void(0);" onclick="manipula_modal('modal_pressao_arterial', 1);" class="bold btn-add">
								Adicionar Dados! <span class="arrow"></span>
							</a>
						</div>
					</div>
					<div class="col-md-6">
						<div class="box box_medium">
							<p class="box_heading"> <span class="icon-glucose"></span>  Glicose e Hemoglobina Glicada </p>
							
							<?php if(isset($Usuarios_glicose) && count($Usuarios_glicose)) : ?>
								<div class="row paddingL10">
									<div class="col-md-6 col-xs-6">
										<span class="lb upper">Glicose:</span>
									</div>
									<div class="col-md-6 col-xs-6">
										<span class="user_glicose" id="glicose">
											<?php echo $Usuarios_glicose[0]['UsuariosGlicose']['glicose']; ?></span> <span class="user_measure_small ">mg/dl
										</span>
									</div>
								</div>
								<hr>
								<div class="row paddingL10">
									<div class="col-md-6 col-xs-8">
										<span class="lb upper">Hemoglobina glicada:</span>
									</div>
									<div class="col-md-6 col-xs-4">
										<span class="user_hemoglobina" id="hemoglobina">
											<?php echo $Usuarios_glicose[0]['UsuariosGlicose']['hemoglobina_glicada']; ?>
										</span> 
										<span class="user_measure_small">%</span>
									</div>
								</div>						
							<?php else : ?>
								<div class="box-content">
									<div class="row">
										<div class="col-md-12">
											<div class="box-content paddingR20">
												<p class="str_clean">
													Caso tenha feito algum exame recente, informe sua <strong>Glicose</strong> e/ou <strong>Hemoglobina glicada</strong>.
												</p>
											</div>
										</div>
									</div>			
								</div>						
							<?php endif; ?>
						</div>
						<div class="box_base">
							<a class="btn-history" href="javascript:void(0);"  onclick="manipula_modal('modal_historico_glicose', 1);">
								<span class="glyphicon glyphicon-time"></span> Histórico
							</a>
							<a href="javascript:void(0);" onclick="manipula_modal('modal_glicose', 1);" class="bold btn-add">
								Adicionar Dados! <span class="arrow"></span>
							</a>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6" id="areaHealthPlan">
						<div class="box box_medium">
							<p class="box_heading"> <span class="icon-plan-health"></span> Plano de Saúde e Dependentes</p>
							<div class="box-content">
								<div class="row">
									<?php if(isset($Usuarios_plano_saude) && count($Usuarios_plano_saude)) : ?>
										<div class="col-md-12 col-xs-12">
											<?php foreach($Usuarios_plano_saude as $key => $info_plano_saude) : ?>
												- <?php echo $info_plano_saude['UsuariosPlanoSaude']['descricao']; ?><br />
											<?php endforeach; ?>
										</div>
									<?php else : ?>
										<div class="col-md-12 col-xs-12">
											<div id="msgNotPlanHealth">
												<div class="row">
													<div class="col-md-3 hidden-xs col-sm-3 paddingL30">
														<span class="icon-family"></span>
													</div>
													<div class="col-md-9 col-sm-12 col-xs-12">
														<div class="box-content">
															<p class="lb lb-simple">
																Você pode adicionar Dependentes a sua conta ou convidar seus parentes para fazer parte do Lyn
																<br />
																Primeiro você deve inserir suas informações de Plano de Saúde.
															</p>
														</div>
													</div>
												</div>
											</div>
											<div id="msgPlanHealth"></div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="box_base">
							<a class="bold btn-add" href="javascript:void(0)" onclick="manipula_modal('modal_plano_saude', 1);">
								Adicione seu Plano de Saúde <span class="arrow"></span>
							</a>
						</div>
					</div>
					<div class="col-md-6">
						<div class="box box_medium">
							<p class="box_heading"> <span class="icon-doctor"></span> Meus Médicos </p>
							<div class="box-content">
								<div class="row myDoctors">
									<div class="col-md-12 col-xs-12">
										<div class="row">
											<?php if(isset($Usuarios_medico) && count($Usuarios_medico)) : ?>
												<div class="col-md-12 col-xs-12">
													<?php foreach($Usuarios_medico as $key => $info_medico) : ?>
														- <?php echo $info_medico['UsuariosMedico']['nome_medico']; ?><br />
													<?php endforeach; ?>
												</div>
											<?php else : ?>
												<div class="col-md-12 col-xs-12">
													<div id="msgNotDoctor">
														<div class="row">
															<div class="col-md-3 hidden-xs col-sm-3 paddingL30">
																<span class="icon-lg-doctor"></span>
															</div>
															<div class="col-md-9 col-sm-12 col-xs-12">
																<div class="box-content">
																	<p class="lb lb-simple">Você também pode adicionar as informações de contatos de seus médicos e profissionais de saúde</p>
																</div>
															</div>
														</div>
													</div>
													<div id="msgPlanHealth"></div>
												</div>										
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="box_base">
							<a class="bold btn-add" href="javascript:void(0)" onclick="manipula_modal('modal_medico', 1);">
								Adicionar Médico <span class="arrow"></span>
							</a>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_avatar">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosDados', array('type' => 'file' ,'url' => array('controller' => 'dados_saude', 'action' => 'upload_avatar')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> ATUALIZAR SUA FOTO DE PERFIL </h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-sm-4">
									<?php if(isset($Usuarios_info['UsuariosDados']['avatar']) && !empty($Usuarios_info['UsuariosDados']['avatar'])) : ?>
										<img class="img-thumbnail" src="/portal/files/avatar/<?php echo $Usuarios_info['UsuariosDados']['avatar']; ?>" alt="Foto de Perfil">
									<?php else : ?>
										<img class="img-thumbnail" src="/portal/img/todosbem/avatar.jpg" alt="Foto de Perfil">
									<?php endif; ?>
								</div>
								<div class="col-sm-8">
									<?php echo $this->BForm->input('UsuariosDados.avatar', array('type' => 'file', 'class' => 'input-xlarge', 'label' => 'Upload de Foto:')); ?>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="$(this).html('Aguardem, salvando dados...'); $('#UsuariosDadosDashboardForm').submit();">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>
					</div>
				</div>
			</div>		

			<div class="modal fade" id="modal_imc">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosImc', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_imc')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> ATUALIZE SEU PESO: </h4>
						</div>
						<div class="modal-body">
							<label><b>Seu Peso Atual: *</b></label>
							<?php echo $this->BForm->input('UsuariosImc.peso', array('class' => 'input-xsmall peso form-control right obrigatorio', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />

							<label><b>Altura: *</b></label>
							<?php echo $this->BForm->input('UsuariosImc.altura', array('class' => 'input-xsmall altura form-control obrigatorio', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_imc(this);">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>   	
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_historico_imc">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> HISTORICO PESAGEM: </h4>
						</div>
						<div class="modal-body">
							<table class="table table-striped">
								<thead>
									<tr>
										<td>Data</td>
										<td>Altura</td>
										<td>Peso</td>
										<td>IMC</td>
									</tr>
								</thead>
								<?php foreach($Usuarios_imc as $k => $info_imc) : ?>
									<tbody>
										<tr>
											<td><?php echo $info_imc['UsuariosImc']['data_medicao']; ?></td>
											<td><?php echo $info_imc['UsuariosImc']['altura']; ?></td>
											<td><?php echo $info_imc['UsuariosImc']['peso']; ?></td>
											<td><?php echo number_format($info_imc['UsuariosImc']['peso'] / ($info_imc['UsuariosImc']['altura'] * $info_imc['UsuariosImc']['altura']), 1); ?></td>
										</tr>					    			
									</tbody>
								<?php endforeach; ?>
							</table>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-warning" onclick="manipula_modal('modal_historico_imc', 0)">Fechar</a>
						</div>
					</div>
				</div>
			</div>	

			<div class="modal fade" id="modal_colesterol">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosColesterol', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_colesterol')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> ATUALIZE SEU COLESTEROL: </h4>
						</div>
						<div class="modal-body">
							<div class="col-sm-6">
								<label><b>TOTAL: *</b></label>
								<?php echo $this->BForm->input('UsuariosColesterol.total', array('class' => 'input-xsmall form-control right obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
								<br />

								<label><b>HDL: *</b></label>
								<?php echo $this->BForm->input('UsuariosColesterol.hdl', array('class' => 'input-xsmall form-control obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
								<br />				    		
							</div>
							<div class="col-sm-6">
								<label><b>LDL: *</b></label>
								<?php echo $this->BForm->input('UsuariosColesterol.ldl', array('class' => 'input-xsmall form-control obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
								<br />							
								
								<label><b>TRIGLICERÍDEOS: *</b></label>
								<?php echo $this->BForm->input('UsuariosColesterol.triglicerideos', array('class' => 'input-xsmall form-control obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
								<br />					    		
							</div>
							
							<div style="clear: both;"></div>
						</div>

						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_colesterol(this);">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>   	
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_historico_colesterol">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> HISTORICO COLESTEROL: </h4>
						</div>
						<div class="modal-body">
							<table class="table table-striped">
								<thead>
									<tr>
										<td>DATA</td>
										<td>TOTAL</td>
										<td>HDL</td>
										<td>LDL</td>
										<td>TRIGLICERIDEOS</td>
									</tr>
								</thead>
								<?php foreach($Usuarios_colesterol as $k => $info_colesterol) : ?>
									<tbody>
										<tr>
											<td><?php echo $info_colesterol['UsuariosColesterol']['data_medicao']; ?></td>
											<td><?php echo $info_colesterol['UsuariosColesterol']['total']; ?></td>
											<td><?php echo $info_colesterol['UsuariosColesterol']['hdl']; ?></td>
											<td><?php echo $info_colesterol['UsuariosColesterol']['ldl']; ?></td>
											<td><?php echo $info_colesterol['UsuariosColesterol']['triglicerideos']; ?></td>
										</tr>					    			
									</tbody>
								<?php endforeach; ?>
							</table>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-warning" onclick="manipula_modal('modal_historico_colesterol', 0)">Fechar</a>
						</div>
					</div>
				</div>
			</div>		

			<div class="modal fade" id="modal_abdominal">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosAbdominal', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_abdominal')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> ATUALIZE SUAS MEDIDAS ABDOMINAIS: </h4>
						</div>
						<div class="modal-body">
							<label><b>TOTAL: *</b></label>
							<?php echo $this->BForm->input('UsuariosAbdominal.largura', array('class' => 'input-xsmall form-control right obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />					    		
							
							<div style="clear: both;"></div>
						</div>

						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_abdominal(this);">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>   	
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_historico_abdominal">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> HISTORICO MEDIDAS ABDOMINAIS: </h4>
						</div>
						<div class="modal-body">
							<table class="table table-striped">
								<thead>
									<tr>
										<td>Data</td>
										<td>Largura da Cintura</td>
									</tr>
								</thead>
								<?php foreach($Usuarios_abdominal as $k => $info_abdominal) : ?>
									<tbody>
										<tr>
											<td><?php echo $info_abdominal['UsuariosAbdominal']['data_medicao']; ?></td>
											<td><?php echo $info_abdominal['UsuariosAbdominal']['largura']; ?></td>
										</tr>					    			
									</tbody>
								<?php endforeach; ?>
							</table>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-warning" onclick="manipula_modal('modal_historico_abdominal', 0)">Fechar</a>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_psa">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosPsa', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_psa')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> PSA Total e PSA Livre: </h4>
						</div>
						<div class="modal-body">
							<label><b>PSA TOTAL: *</b></label>
							<?php echo $this->BForm->input('UsuariosPsa.psa_total', array('class' => 'input-xsmall form-control right obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />	
							
							<label><b>PSA LIVRE: *</b></label>
							<?php echo $this->BForm->input('UsuariosPsa.psa_livre', array('class' => 'input-xsmall form-control right obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />												    		
							
							<div style="clear: both;"></div>
						</div>

						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_psa(this);">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>   	
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_historico_psa">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> HISTORICO : </h4>
						</div>
						<div class="modal-body">
							<table class="table table-striped">
								<thead>
									<tr>
										<td>DATA</td>
										<td>PSA Total</td>
										<td>PSA Livre</td>
									</tr>
								</thead>
								<?php foreach($Usuarios_psa as $k => $info_psa) : ?>
									<tbody>
										<tr>
											<td><?php echo $info_psa['UsuariosPsa']['data_medicao']; ?></td>
											<td><?php echo $info_psa['UsuariosPsa']['psa_total']; ?></td>
											<td><?php echo $info_psa['UsuariosPsa']['psa_livre']; ?></td>
										</tr>					    			
									</tbody>
								<?php endforeach; ?>
							</table>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-warning" onclick="manipula_modal('modal_historico_psa', 0)">Fechar</a>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_pressao_arterial">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosPressaoArterial', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_pressao_arterial')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> Frequência Cardíaca e Pressão Arterial: </h4>
						</div>
						<div class="modal-body">
							<label><b>FREQUÊNCIA CARDÍACA: *</b></label>
							<?php echo $this->BForm->input('UsuariosPressaoArterial.frequencia_cardiaca', array('class' => 'input-xsmall form-control right obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />
							
							<label><b>PRESSÃO ARTERIAL: *</b></label>
							<br />
							
							<?php echo $this->BForm->input('UsuariosPressaoArterial.pressao_arterial_auto', array('class' => 'input-small form-control right obrigatorio integer', 'label' => false, 'div' => false, 'style' => 'float: left; width: 90px; text-align: right;')); ?> 
							<span style="width: 20px; text-align: center; float: left; font-size: 25px;">/</span> 
							<?php echo $this->BForm->input('UsuariosPressaoArterial.pressao_arterial_baixo', array('class' => 'input-small form-control right obrigatorio integer', 'label' => false, 'div' => false, 'style' => 'float: left; width: 90px; text-align: right;')); ?>
							<br /><br />												    		
							
							<div style="clear: both;"></div>
						</div>

						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_pressao_arterial(this);">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>   	
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_historico_pressao_arterial">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> Histórico de Frequência Cardíaca e Pressão Arterial: </h4>
						</div>
						<div class="modal-body">
							<table class="table table-striped">
								<thead>
									<tr>
										<td>DATA</td>
										<td>FREQUÊNCIA CARDÍACA</td>
										<td>PRESSÃO ARTERIAL</td>
									</tr>
								</thead>
								<?php foreach($Usuarios_pressao_arterial as $k => $info_pressao_arterial) : ?>
									<tbody>
										<tr>
											<td><?php echo $info_pressao_arterial['UsuariosPressaoArterial']['data_medicao']; ?></td>
											<td><?php echo $info_pressao_arterial['UsuariosPressaoArterial']['frequencia_cardiaca']; ?></td>
											<td><?php echo $info_pressao_arterial['UsuariosPressaoArterial']['pressao_arterial_auto']; ?>/<?php echo $info_pressao_arterial['UsuariosPressaoArterial']['pressao_arterial_baixo']; ?></td>
										</tr>					    			
									</tbody>
								<?php endforeach; ?>
							</table>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-warning" onclick="manipula_modal('modal_historico_pressao_arterial', 0)">Fechar</a>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_glicose">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosGlicose', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_glicose')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> Glicose e Hemoglobina Glicada: </h4>
						</div>
						<div class="modal-body">
							<label><b>Glicose: *</b></label>
							<?php echo $this->BForm->input('UsuariosGlicose.glicose', array('class' => 'input-xsmall form-control right obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />
							
							<label><b>Hemoglobina Glicada: *</b></label>
							<?php echo $this->BForm->input('UsuariosGlicose.hemoglobina_glicada', array('class' => 'input-xsmall form-control right obrigatorio integer', 'label' => false, 'style' => 'width: 200px; text-align: right;')); ?>
							<br />														
							
							<div style="clear: both;"></div>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_glicose(this);">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>   	
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_historico_glicose">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> Histórico de Glicose e Hemoglobina Glicada: </h4>
						</div>
						<div class="modal-body">
							<table class="table table-striped">
								<thead>
									<tr>
										<td>Data</td>
										<td>Glicose</td>
										<td>Hemoglobina Glicada</td>
									</tr>
								</thead>
								<?php foreach($Usuarios_glicose as $k => $info_glicose) : ?>
									<tbody>
										<tr>
											<td><?php echo $info_glicose['UsuariosGlicose']['data_medicao']; ?></td>
											<td><?php echo $info_glicose['UsuariosGlicose']['glicose']; ?></td>
											<td><?php echo $info_glicose['UsuariosGlicose']['hemoglobina_glicada']; ?></td>
										</tr>					    			
									</tbody>
								<?php endforeach; ?>
							</table>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-warning" onclick="manipula_modal('modal_historico_glicose', 0)">Fechar</a>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_plano_saude">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosPlanoSaude', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_plano_saude')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> Descrição do seu Plano Saúde: </h4>
						</div>
						<div class="modal-body">
							<label><b>Plano Saúde: *</b></label>
							<?php echo $this->BForm->input('UsuariosPlanoSaude.descricao', array('class' => 'input-xsmall form-control right obrigatorio', 'label' => false, 'style' => 'width: 400px;')); ?>
							<br />
							
							<div class="inline_labels">
								<?php echo $this->BForm->input('UsuariosPlanoSaude.titular', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'options' => array('1' => ' Sou Titular', '0' => ' Sou Dependente '), 'type' => 'radio', 'onchange' => 'mostraDependente(this);')) ?>
							</div>

							<br />
							<div id="dependente" style="display: none;">
								<label><b>CPF do Titular:</b></label>
								<?php echo $this->BForm->input('UsuariosPlanoSaude.cpf_titular', array('class' => 'input-xsmall form-control right cpf', 'label' => false, 'style' => 'width: 400px;', 'maxlength' => '14')); ?>	
							</div>							
							
							<div style="clear: both;"></div>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_plano_saude(this);">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_medico">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosMedico', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'grava_medico')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> Meus Médicos: </h4>
						</div>
						<div class="modal-body">
							<label><b>Nome do Médico: *</b></label>
							<?php echo $this->BForm->input('UsuariosMedico.nome_medico', array('class' => 'input-xsmall form-control right obrigatorio', 'label' => false, 'style' => 'width: 90%; text-align: left;')); ?>
							<br />
							
							<label><b>Especialidade:</b></label>
							<?php echo $this->BForm->input('UsuariosMedico.codigo_especialidade', array('class' => 'input-xsmall form-control right', 'label' => false, 'style' => 'width: 90%; text-align: left;', 'options' => $especialidades)); ?>
							<br />
							
							<label><b>CRM:</b></label>
							<?php echo $this->BForm->input('UsuariosMedico.crm', array('class' => 'input-xsmall form-control right', 'label' => false, 'style' => 'width: 90%; text-align: left;')); ?>
							<br />

							<label><b>E-mail:</b></label>
							<?php echo $this->BForm->input('UsuariosMedico.email', array('class' => 'input-xsmall form-control right', 'label' => false, 'style' => 'width: 90%; text-align: left;')); ?>
							<br />
							
							<label><b>Telefone:</b></label>
							<?php echo $this->BForm->input('UsuariosMedico.telefone', array('class' => 'input-xsmall form-control right', 'label' => false, 'style' => 'width: 90%; text-align: left;')); ?>
							<br />																												
							<div style="clear: both;"></div>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_medico(this);">Confirmar</a>
						</div>
						<?php echo $this->BForm->end(); ?>   	
					</div>
				</div>
			</div>		

			<div class="modal fade" id="modal_medicamento">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<?php echo $this->BForm->create('UsuariosMedicamento', array('type' => 'post' ,'url' => array('controller' => 'dados_saude','action' => 'busca_medicamento')));?>
						<div class="modal-header">
							<h4 class="modal-title" id="gridSystemModalLabel"> Medicamentos em Uso: </h4>
						</div>
						<div class="modal-body">
							<label><b>Digite o nome do Medicamento:</b></label>
							<?php echo $this->BForm->input('UsuariosMedicamento.descricao', array('class' => 'input-xsmall form-control right js-farmaco', 'label' => false, 'style' => 'width: 90%; text-align: left;')); ?>
							<br />
							<div style="clear: both;"></div>
						</div>
						<div class="modal-footer">
							<a href="javascript:void(0);" class="btn btn-success" onclick="grava_medicamento(this);">Salvar</a>
						</div>
						<?php echo $this->BForm->end(); ?>   	
					</div>
				</div>
			</div>														

			<div class="modal fade" id="modal_resultado">
				<div class="modal-dialog modal-md" style="position: static;">
					<div class="modal-content">
						<div id="resultado" style="border:1px solid #000">
					<?/*<div class="modal-header">
						<h4 class="modal-title" id="gridSystemModalLabel">Resultado: <?php echo $checkups;?></h4>
					</div>
			    	<div class="modal-body">
			    		<table class="table table-striped">
			    			<thead>
				    			<tr>
				    				<td>Pergunta</td>
				    				<td>Resposta</td>
				    			</tr>
				    		</thead>
				    		<?php foreach($dados_questionarios as $k => $info_imc) : ?>
				    			<tbody>
					    			<tr>
					    				<td><?php echo $info_imc['Questao']['label']; ?></td>
					    				<td><?php echo $info_imc['Resposta']['label']; ?></td>
					    			</tr>					    			
				    			</tbody>
				    		<?php endforeach; ?>
				    	</table>
			    	</div>
			    	<div class="modal-footer">
			    		<a href="javascript:void(0);" class="btn btn-warning" onclick="manipula_modal('modal_resultado', 0)">Fechar</a>
			    	</div>*/?>
			    </div>
			</div>
		</div>
	</div>

	<style type="text/css">
		.carousel-control{
			width: 3% !important;
			font-size: 60px !important;
			color: #000 !important;
			top: 18% !important;
		}
		.carousel-control:hover{
			color: #000;
			opacity: 1;
		}
		.carousel-control.aright{
			right: -25px;
			left: auto;
		}
		.carousel-control.aleft{
			left: -25px;
		}
	</style>


	<?php echo $this->Javascript->codeBlock('
		$(function() {

			gatilhos();

			$(".carousel").carousel({
				interval: 10000
			});

		});		
		
		function mostraDependente(element) {
			if($(element).val() == "1") {
				$("#dependente").hide();	
			} else {
				$("#dependente").show();
			}
		}
		
		function gatilhos() {
		// modulo farmaco
			var timer;

			$("body").on("keyup", ".js-farmaco", function() {
				var este = $(this);
				var string = this.value;

				if(string != "") {
					$(".loader-gif").remove();
					este.after("<img src=\"/portal/img/loading.gif\" class=\"loader-gif\" />");
					este.parent().css("position", "relative");
					clearTimeout(timer); 

					timer = setTimeout(function() {

						$.ajax({
							url: baseUrl + "dados_saude/busca_medicamento/",
							type: "POST",
							dataType: "json",
							data: {string: string},
						})
						.done(function(response) {
							if(response) {
								$(".seleciona-farmaco").remove();
								var canvas = $("<div>", {class: "seleciona-farmaco"}).html(response);
								este.parent().append(canvas);

								$(".seleciona-farmaco .seleciona").each(function() {
									$(this).click(function(){
										$(this).parent().html("<img src=\"/portal/img/loading.gif\" class=\"loader-gif\" />");
										insereMedicamento($(this).attr("id"));
									})
								})
							}
						})
						.always(function() {
							$(".loader-gif").remove();
						});
					}, 1000);

				} else {
					$(".seleciona-farmaco").remove();
					$(".loader-gif").remove();
				}
			});
		}
		
		function insereMedicamento (id) {
			var id_medicamento = id.replace("med_", "");

			$.ajax({
				url: "/portal/dados_saude/carrega_medicamentos",
				type: "POST",
				dataType: "html",
				data: "medicamento=" + id_medicamento,
				beforeSend: function(retorno) {

				},
				success: function(retorno) {
					$("#lista_medicamentos").html(retorno);
				},
				complete: function() {
					$(".seleciona-farmaco").html("");
					manipula_modal("modal_medicamento", 0);
				}
			});	
		}
		
		function remove_medicamento(id_medicamento, elemento) {

			$.ajax({
				url: "/portal/dados_saude/remove_medicamento",
				type: "POST",
				dataType: "html",
				data: "codigo_medicamento=" + id_medicamento,
				beforeSend: function(retorno) {
					$(elemento).parent().fadeOut();
				},
				success: function(retorno) {

				},
				complete: function() {

				}
			});			
		}
		
		function grava_imc(element) {
			var form = $("#UsuariosImcDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosImcDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_imc",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}	
		
		function grava_colesterol(element) {
			var form = $("#UsuariosColesterolDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosColesterolDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_colesterol",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}	
		
		function grava_abdominal(element) {
			var form = $("#UsuariosAbdominalDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosAbdominalDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_abdominal",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}
		
		function grava_psa(element) {
			var form = $("#UsuariosPsaDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosPsaDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_psa",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}	
		
		function grava_pressao_arterial(element) {
			var form = $("#UsuariosPressaoArterialDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosPressaoArterialDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_pressao_arterial",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}
		
		function grava_glicose(element) {
			var form = $("#UsuariosGlicoseDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosGlicoseDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_glicose",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}
		
		function grava_plano_saude(element) {
			var form = $("#UsuariosPlanoSaudeDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosPlanoSaudeDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_plano_saude",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}	
		
		function grava_medico(element) {
			var form = $("#UsuariosMedicoDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosMedicoDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_medico",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}	

		function grava_medicamento(element) {
			var form = $("#UsuariosMedicamentoDashboardForm").serialize();
			var elemento = $(element).html();

			if(validaForm("UsuariosMedicamentoDashboardForm")) {
				$.ajax({
					url: "/portal/dados_saude/grava_medicamento",
					type: "POST",
					dataType: "json",
					data: form,
					beforeSend: function() {
						$(element).html("Aguardem, salvando dados...");
					},
					success: function(retorno) {
						if(retorno) {
							location.reload(); 
						}
					},
					complete: function() {
					// $(element).html(elemento);
					}
				});		
			}
		}		
		
		function validaForm(form) {

		// remove todas as validacoes!
			$(".label-danger").remove();

			var libera = true;
			$("form#" + form + " :input").each(function() {
				var input = $(this);

				if(input.hasClass("obrigatorio") && (input.val() == "")) {
					$(input).after("<label class=\"label label-danger\"> Campo obrigatório! </label>");
					libera = false;
				}
			});

			return libera;
		}		
		
		function manipula_modal(id, mostra) {
			if(mostra) {
				$(".modal").css("z-index", "-1");
				$("#" + id).css("z-index", "1050");
				$("#" + id).modal("show");
			} else {
				$("#" + id).css("z-index", "-1");
				$("#" + id).modal("hide");
			}
		}		

		function exibe_resultados_questionarios(codigo_questionario){
			$.ajax({
				url: "/portal/dados_saude/resultado_questionario",
				type: "POST",
				dataType: "html",
				data:{"codigo_questionario": codigo_questionario},
				success: function(data) {
					if(data.length > 0) {
						manipula_modal("modal_resultado", 1);
						$("#resultado").html(data);

					}
				},
				error: function() {
				},
				complete: function() {
				}
			});		
		}		
		'); ?>
