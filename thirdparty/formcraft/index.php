<?php


if (!isset($_SESSION)) {
	session_start();
}
// if (!isset($_SESSION['username']))
// {
// 	header( 'Location: login.php' );
// }


require_once('config.fc.php');

$query = ORM::for_table('add_table')->where('application','mailchimp')->find_one();


if (!($query))
{
    	// Create Table(s)
	$query = ORM::for_table('add_table')->create(); $query->application = 'mailchimp'; $query->save();
	$query = ORM::for_table('add_table')->create(); $query->application = 'campaign'; $query->save();
	$query = ORM::for_table('add_table')->create(); $query->application = 'aweber'; $query->save();
}

$myrows = ORM::for_table('builder')->find_many();
$mysub = ORM::for_table('submissions')->order_by_desc('id')->find_many();
$mysubr = ORM::for_table('submissions')->where('seen','1')->find_many();


?>

<!DOCTYPE html>
<html>
<head>
	<title>FormCraft</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link href='bootstrap/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
	<link href='css/font-awesome/css/font-awesome.css' rel='stylesheet' type='text/css'>
	<link href='css/style.css' rel='stylesheet' type='text/css'>
	<link href='css/boxes.css' rel='stylesheet' type='text/css'>
	<link href='datepicker/css/datepicker.css' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>

</head>

<body>
	<a class='logout' href='login.php?logout=true'>logout</a>

	<div class="ffcover_add">


		<div id="title_div">	
			<h1>FormCraft</h1>
			<a class='docs_title' href='http://ncrafts.net/formcraft/docs/table-of-contents/' target='_blank' style='right: 16.5%'>Complete Online Guide</a>
			<a class='docs_title' href='documentation.html' target='_blank' style='right: 6.5%'>Documentation</a>
			<a class='docs_title' href='http://ncrafts.net' target='_blank' style='right: 1.5%'>nCrafts</a>
		</div>




		<form class="modal hide fade" id='new_form' action='javascript:submit_new_form();' style='width: 640px'>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Add Form</h3>
			</div>
			<div class="modal-body">

				<label class='label_radio circle-ticked' >
					<input type='radio' value='new' checked name='type_form'><div class='label_div' style='background: #fff'>New Form</div>
				</label>
				<label class='label_radio circle-ticked' style='margin-left: 25px'>
					<input type='radio' value='duplicate' name='type_form' id='rand_aa'><div class='label_div' style='background: #fff'>Duplicate</div>
				</label>




				<select name='duplicate' style='height: 30px' id='rand_a'>
					<?php foreach ($myrows as $row) {
						?>
						<option value='<?php echo $row['id']; ?>'><?php echo $row['name']; ?></option>
						<?php } ?>

					</select>


					<label class='label_radio circle-ticked' style='margin-left: 40px' >
						<input type='radio' value='import' name='type_form' id='rand_b'><div class='label_div' style='background: #fff'>Import</div>
					</label>


					<span class='import btn btn-success fileupload-cover'>
						<input id="fileupload" class='import import_field fileupload' type="file" name="files[]" data-url="file-upload/server/php/" multiple style='width: 84px; height: 24px; margin: 0px;'>
						<span style='position: absolute; left: 0px; right: 0px; text-align: center; top: 0px; font-size: 12px; line-height: 30px' id='import_field_label'>
							Upload Form
						</span>
					</span>
					<input type='hidden' id='import_form' name='import_form' val=''>



					<hr>



					<label for='form_name_1'>Name of Form</label>
					<input id='form_name_1' name='name' type="text" class="input-small" autofocus placeholder='Site Feedback' style='width: 220px'>

					<br><br>
					<label for='form_desc_1'>Description (optional)</label>
					<textarea id='form_desc_1' name='desc' style='width: 220px' rows='4'></textarea>
					<br><br>


				</div>
				<div class="modal-footer">
					<span class='response_ajax'></span>
					<a href="#" class="btn" data-dismiss="modal">Close</a>
					<button type="submit" id='submit_new_btn' class="btn btn-success"><i class='icon-plus icon-white'></i> Add Form</button>
				</div>
			</form>






			<?php 
			$saw['today'] = 0;
			$saw['month'] = 0;

			foreach ($mysub as $key => $row) 
			{

				$dt = date_parse($row['added']);
				$date = date_parse(date('d M Y (H:m)'));

				if ($dt['month']==$date['month'] && $dt['day']==$date['day'] && $dt['year']==$date['year'])
				{
					$saw['today']++;
				}
				if ($dt['month']==$date['month'] && $dt['year']==$date['year'])
				{
					$saw['month']++;
				}
			} 

			?>



			<ul class="nav nav-pills" style='margin-top: 60px'>
				<li class='active'><a href="#home" data-toggle="tab">Home</a></li>
				<li><a href="#forms" data-toggle="tab">Forms</a></li>
				<li><a href="#submissions" data-toggle="tab">Submissions <span style='color: green'>(<?php echo sizeof($mysub)-sizeof($mysubr); ?>)</span></a></li>
				<li><a href="#files" data-toggle="tab">File Manager</a></li>
				<li><a href="#add" data-toggle="tab">Add-Ons</a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="home">

					<div class='charts'>

						<select id='stats_select'>
							<option value='all'>All Forms</option>
							<?php
							foreach ($myrows as $row) {
								?>
								<option value='<?php echo $row['id']; ?>'><?php echo $row['name']; ?></option>
								<?php } ?>

							</select>

							<div id="chart_div" class='chart_js'></div>
						</div>
					</div>
					<div class="tab-pane" id="forms">		

						<div class='group_cover'>

							<a class='btn btn-success' id='new_form_pop' data-toggle='modal' href='#new_form' style='margin-left: 10px; margin-bottom: 10px; font-weight: bold; font-size: 15px; padding: 10px 20px'><i class='icon-plus icon-white'></i> Add Form</a>

							<div id='existing_forms'>
								<div class='subs_wrapper'>
									<table style='table-layout: fixed' class='table table-hover table-striped' id='ext'>
										<thead>
											<tr>
												<th width='3.5%' style='text-align: center;'>ID</th>
												<th width='26%'>Name of Form</th>
												<th width='23%'>Description</th>
												<th width='12%' style='text-align: center'>Shortcode</th>
												<th width='7%' style='text-align: center'>Views</th>
												<th width='7%' style='text-align: center'>Submissions</th>
												<th width='12%' style='text-align: center'>Date Added</th>
												<th width='9.5%' style='text-align: center'>Options</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($myrows as $row) {
												?>
												<tr id='<?php echo $row['id']; ?>'>
													<td class='row_click' style='text-align: center'><?php echo $row['id']; ?></td>

													<td class='row_click'><a class='rand' href='builder.php?id=<?php echo $row['id']; ?>'><?php echo $row['name']; ?></a><input class="rand2" style="width: 110px; display:none; margin-right: 6px" type="text" value="<?php echo $row['name']; ?>"><a class='btn edit_btn' title='Edit Form Name' id='edit_<?php echo $row['id']; ?>'>edit</a><a class='btn save_btn' id='edit_<?php echo $row['id']; ?>' type='submit'>save</a></td>

													<td class='row_click row_description'><a  class='rand'><?php echo $row['description']; ?></a></td>

													<td style='text-align: center; border-right: 1px solid #eee'>[formcraft id='<?php echo $row['id']; ?>']</td>
													<td class='row_click' style='text-align: center'><?php echo $row['views']; ?></td>
													<td class='row_click' style='text-align: center'><?php echo $row['submits']; ?></td>
													<td class='row_click'><?php echo $row['added']; ?></td>
													<td style='text-align: center'>


														<a href='php/exportOne.php?id=<?php echo $row['id']; ?>' target='_blank' title='Export all submissions for this form' class='export_alt'><i class='icon-share-alt icon-2x'></i> </a>
														<a class='delete-row' data-loading-text='...' data-complete-text="<i class='icon-ok icon-2x'></i>" id='delete_<?php echo $row['id']; ?>' title='Delete this form'><i class='icon-trash icon-2x'></i>
														</a>
													</td>
												</tr>
												<?php } ?>

											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="submissions">			
							<div class='group_cover'>
								<div style='border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 10px'>
									<span class='stat'>
										<span class='unr_msg' id='unr_ind'><?php echo sizeof($mysub)-sizeof($mysubr); ?>
										</span> unread&nbsp;&nbsp;
										<span class='tot_msg' id='tot_ind'><?php echo sizeof($mysub); ?>
										</span> total	
									</span>
									<span class='stat'>
										<span class='unr_msg'><?php echo $saw['today']; ?>
										</span> new today&nbsp;&nbsp;
										<span class='tot_msg'><?php echo $saw['month']; ?>
										</span> new this month
									</span>
									<a class='btn btn-success' id='export' style='margin-left: 30px' title='Export all submissions data to CSV' target='_blank' href='php/exportAll.php'>
										Export Data to CSV
									</a>
								</div>

								<div id='subs_c' >

									<table style='table-layout: fixed' class='table-sub table table-hover' id='subs'>
										<thead>
											<tr>
												<th width="5%" title='Click to sort'>ID</th>
												<th width="10%" title='Click to sort'>Read</th>
												<th width="20%" title='Click to sort'>Date</th>
												<th width="30%" title='Click to sort'>Form Name</th>
												<th width="25%" title='Click to sort'>Message</th>
												<th width="10%" title='Click to sort'>Options</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($mysub as $key=>$row) {
												$std= "style='padding: 4px 8px; margin: 0; vertical-align: top'";

												$new = json_decode($row['content'],1);

												$row_id = $row['form_id'];

												$query = ORM::for_table('builder')->find_one($row_id);

												$name = $query->name;


												?>

												<tr id='sub_<?php echo $row['id']; ?>' class='<?php if ($row['seen']=='1') {echo 'row_shade';}?>'>
													<td style='text-align: center'><?php echo $row['id']; ?></td>
													<td style='text-align: center' id='rd_<?php echo $row['id']; ?>'><?php if($row['seen']) {echo 'Read';} else {echo 'Unread';} ?></td>
													<td style='text-align: center'><?php echo $row['added']; ?></td>
													<td><?php if (!(empty($name))) {echo $name;} else {echo '(form deleted)';}?></td>
													<td style='text-align: center'>
														<button class='btn view_mess' id='upd_<?php echo $row['id']; ?>' data-toggle='modal' data-target='#view_modal'>View</button>

													</td>
													<td style='text-align: center'>
														<i class='icon-trash icon-2x view_mess' id='del_<?php echo $row['id']; ?>' title='Delete message'></i>&nbsp;
														<i class='icon-bookmark-empty icon-2x view_mess' id='read_<?php echo $row['id']; ?>' title='Mark as unread'></i>
													</td>
												</tr>
												<?php } ?>

											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="tab-pane" id="files">				
								<?php

// 								$url = $path.'/file-upload/server/php/index.php';
// 								$ch = curl_init($url);
// 								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 								$read = curl_exec($ch);
// 								curl_close($ch);
// 								$read = json_decode($read, 1);
								?>



								<div class='group_cover'>

									<span class='stat' style='border: none'>
										<span class='unr_msg' id='unr_ind'><?php echo sizeof($read['files'])?>
										</span> files&nbsp;&nbsp;
									</span>

									<div id='files_c' >
										<div class='subs_wrapper'>

											<table style='table-layout: fixed' class='table-sub table table-hover' id='files_manager_table'>
												<thead>
													<tr>
														<td width="5%">#</td>
														<td width="20%">Name</td>
														<td width="10%">Size</td>
														<td width="59%">Url</td>
														<td width="6%">Delete</td>
													</tr>
												</thead>
												<tbody>
													<?php
													foreach ($read['files'] as $key => $value) 
													{
														?>

														<tr>
															<td><?php echo $key+1 ?></td>
															<td><?php echo $value['name']; ?></td>
															<td><?php echo round(($value['size']/1024),2); ?> KB</td>
															<td><a href='<?php echo $value['url']; ?>' target='_blank'><?php echo $value['url']; ?></a></td>
															<td><a class='delete_from_manager' style='width: 38px' data-loading-text='...' data-url='<?php echo $value['url']; ?>' data-complete-text='<i class="icon-ok icon-2x"></i>' id='del_fm_<?php echo $key+1 ?>'><i class='icon-trash icon-2x'></i></a></td>
														</tr>
														<?php } ?>

													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>



								<div class="tab-pane" id="add">
									<?php



									require('addon.php');

									if (defined('FORMCRAFT_ADD'))
									{
										formcraft_add_content();
									}
									else
									{




										?>
										<br>
										<div style='width: 100%; margin: auto auto; text-align: center; font-size: 24px; color: #666; font-weight: 300; line-height: 132%'>
											Install <a href='http://wordpress.org/plugins/formcraft-add-on-pack/' target='_blank'>FormCraft Add-On Pack</a><br>
											<span style='font-size: 18px'>
												(MailChimp, AWeber and Campaign Monitor integration)</span>
											</div>

											<?php }

											?>



										</div>	

									</div>



								</div><!-- End of Cover -->
								<?php

								
								foreach ($mysub as $key=>$row) {
									$mess[$key] = '';
									$std  = "style='padding: 4px 8px; margin: 0; vertical-align: top; width: 30%; display: inline-block'";
									$std2 = "style='padding: 4px 8px; margin: 0; vertical-align: top; width: 60%; display: inline-block'";

									$new = json_decode($row['content'],1);
									$att = 1;

									foreach ($new as $value)
									{
										if ( !(empty($value['type'])) && !($value['type']=='captcha') && !($value['label']=='files') && !($value['type']=='hidden') && !($value['label']=='divider') )
										{
											if ( ($value['type']=='radio' || $value['type']=='check' || $value['type']=='matrix' || $value['type']=='stars' || $value['type']=='smiley') && (empty($value['value'])) )
											{
												$mess[$key] .= "";
											}
											else
											{
												$mess[$key] .= "<li><span $std><strong>$value[label] </strong></span><span $std2>$value[value]</span></li>";
											}
										}
										else if ($value['label']=='files') 
										{
											$mess[$key] .= "<li><span $std><strong>Attachment($att) </strong></span><a href='$value[value]' target='_blank' $std2>$value[value]</a></li>";
											$att ++;
										}
										else if ($value['label']=='divider') 
										{
											$mess[$key] .= "<hr>$value[value]<hr>";
											$att ++;
										}
										else if ($value['type']=='hidden' && $value['label']=='location') 
										{
											$mess[$key] .= "<div class='location_show'>$value[value]</div>";
											$att ++;
										}

									}


									$message[$key] = 
									'<ul>
									'.$mess[$key].'
								</ul>';
								$row_id = $row['form_id'];


								$mysub2 = ORM::for_table('builder')->find_one($row_id);


								?>

								<span style='display: none' id='upd_name_<?php echo $row['id']; ?>'><?php if (!(empty($name))) {echo $name;} else {echo '(form deleted)';}?></span>
								<span style='display: none' id='upd_text_<?php echo $row['id']; ?>'><p><?php echo $location.$message[$key]; ?></p></span>


								<?php
							}
							
							?>




							<div class='hid modal hide fade' id='view_modal' aria-hidden="true">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style='margin-right: 15px;margin-top: 8px'>×</button>
								<div id='print_area'>
									<div class="modal-header">
										<h3 class="myModalLabel"></h3>
									</div>
									<div class="modal-body" id='vm_body'>
										<p></p>
									</div>
								</div>
								<div class="modal-footer">
									<button value="Print Div" class='btn btn-primary' onclick="PrintElem('#print_area')" />Print
								</button>
								<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

							</div>
						</div>



						<script src='js/jquery.min.js'></script>
						<script src='https://www.google.com/jsapi'></script>
						<script src='js/form_index.js'></script>
						<script src='datepicker/js/datepicker.js'></script>
						<script src='ui/js/jquery-ui-1.9.2.custom.min.js'></script>
						<script src='datatables/media/js/jquery.dataTables.min.js'></script>
						<script src='bootstrap/js/bootstrap.min.js'></script>
						<script src='libraries/js_libraries.js'></script>
						<script src='file-upload/js/jquery.iframe-transport.js'></script>
						<script src='file-upload/js/vendor/jquery.ui.widget.js'></script>
						<script>
							function PrintElem(elem)
							{
								Popup($(elem).html());
							}

							function Popup(data) 
							{
								var mywindow = window.open('', 'my div', 'height=400,width=600');
								mywindow.document.write('<html><title>FormCraft Submission</title>');
								mywindow.document.write('<body>');
								mywindow.document.write(data);
								mywindow.document.write('</body></html>');

								mywindow.print();
								mywindow.close();

								return true;
							}
						</script>


						<script>

							jQuery(document).ready(function () {

								if ((document.domain=='ncrafts.net') || (document.domain=='www.ncrafts.net'))
								{

									setTimeout(function() 
									{
										jQuery('#new_form_pop').trigger('click');
									},10);

								}

							});


							jQuery(function () {
								jQuery('#myTab a:last').tab('show');
							});

						</script>
						<script>
							jQuery(document).ready(function()
							{
								jQuery('.fc_addbtn').click(function(){
									var temp = this.id.split('_');
									var temp_val = jQuery('#add_'+temp[1]).val();
									var temp_app = jQuery('#add_'+temp[1]).attr('app');
									var temp_id = this.id;
									var id = jQuery('#'+temp_id).parents('.add_span_cover').attr('id');
									jQuery('#'+id+' .response').slideUp('fast');

									jQuery(this).text(' . . . ');
									jQuery.ajax({
										url: 'addon.php',
										type: "POST",
										data: 'action=formcraft_add_update&app='+temp_app+'&code='+temp_val,
										success: function (response) {
											if (response=='saved')
											{
												jQuery('#'+temp_id).text('Save');
												var id = jQuery('#'+temp_id).parents('.add_span_cover').attr('id');
												jQuery('#'+id+' .op_div').css({'opacity':'.3'});
												jQuery('#'+id+' .addon_nc').slideUp('fast');
												jQuery('#'+id+' .addon_c').slideDown('fast');
												jQuery('#'+id+' .response').slideUp('fast');
												jQuery('#'+id+' .response').html('');
											}
											else
											{
												jQuery('#'+temp_id).text('Retry');
												var id = jQuery('#'+temp_id).parents('.add_span_cover').attr('id');
												jQuery('#'+id+' .op_div').css({'opacity':'1'});
												jQuery('#'+id+' .addon_nc').slideDown('fast');
												jQuery('#'+id+' .addon_c').slideUp('fast');
												jQuery('#'+id+' .response').html(response);
												jQuery('#'+id+' .response').slideDown('fast');
											}
										},
										error: function (response) {
											if (response=='saved')
											{
												jQuery('#'+temp_id).text('Save');
											}
											else
											{
												jQuery('#'+temp_id).text('Retry');
											}
										}
									});
});
});
</script>

</body>
</html>