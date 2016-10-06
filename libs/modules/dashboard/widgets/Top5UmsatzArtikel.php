<?php // Top 5 Umsatzstärkste Artikel // DONE


$data = new Widget(Widget::TYPE_TOP5BESTARTICLES);
$data = $data->getResult();

$testdata = json_encode($data);
?>

<script type="text/javascript">
	$(function() {
		var data = <?php echo $testdata;?>;
		var placeholder = $("#TOP5BESTARTICLES");
		$.plot(placeholder, data, {
			series: {
				pie: {
					show: true,
					radius: 1,
					label: {
						show: true,
						radius: 2/4,
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
		return "<div style='font-size:7pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
	}
</script>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Top 5 Umsatzstärksten Artikel</h3>
	  </div>
	  <div class="panel-body">
		  <div id="TOP5BESTARTICLES" style="align:center;width:300px;height:300px"></div>

	  </div>
</div>