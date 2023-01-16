									<div class="row m-padding10">
										<div class="col-md-12" style="font-size: 11px; max-height: 159px; overflow-y: auto;">
											<ul class="listDrugsInUse">
												<?php foreach($funcionario_medicamento as $key => $campo) : ?>
													<li>
														<a href="javascript:void(0);" class="label label-danger" onclick="remove_medicamento('<?php echo $campo['Medicamento']['codigo']; ?>', this);"><span class="glyphicon glyphicon-remove" style="color: #FFF;"></span></a>
														<?php echo $campo['Medicamento']['descricao']; ?> - <?php echo $campo['Medicamento']['principio_ativo']; ?>   (<?php echo $campo['Medicamento']['posologia']; ?>)
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>