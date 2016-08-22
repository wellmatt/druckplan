<?php
$testdata = Array(
	Array("label"=>"Series1","data"=>10),
	Array("label"=>"Series2","data"=>30),
	Array("label"=>"Series3","data"=>90),
	Array("label"=>"Series4","data"=>70),
	Array("label"=>"Series5","data"=>80),
	Array("label"=>"Series6","data"=>110),
);
$testdata = json_encode($testdata);
?>

<script type="text/javascript">
	$(function() {
		var data = <?php echo $testdata;?>;
		var placeholder = $("#placeholder_test1");
		$.plot(placeholder, data, {
			series: {
				pie: {
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 1,
						formatter: labelFormatter,
						background: {
							opacity: 0.8
						}
					}
				}
			},
			legend: {
				show: false
			}
		});
	});
	function labelFormatter(label, series) {
		return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
	}
</script>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Test Beispiel</h3>
	  </div>
	  <div class="panel-body">
		  <div id="placeholder_test1" style="width:600px;height:300px"></div>
		  test
	  </div>
</div>