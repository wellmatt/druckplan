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