<?php
$testdata = Array(
    Array("label"=>"Auslastung","data"=>60),
    Array("label"=>"Freie Kapazität","data"=>40),


);
$testdata = json_encode($testdata);
?>

<script type="text/javascript">
    $(function() {
        var data = <?php echo $testdata;?>;
        var placeholder = $("#placeholder_machines");
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
        <h3 class="panel-title">Auslastung Produktion</h3>
    </div>
    <div class="panel-body">
        <div id="placeholder_machines" style="align:center;width:300px;height:300px"></div>

    </div>
</div>