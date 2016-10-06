<?php // Profit pro Monat // DONE
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

$data = new Widget(Widget::TYPE_PROFITPERMONTH);
$data = $data->getResult();


$data = json_encode($data);
?>

<script type="text/javascript">
    $(function() {
        var data = <?php echo $data;?>;
        var placeholder = $("#PROFITPERMONTH");
        $.plot(placeholder, [ data ], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.4,
                    align: "center"
                }
            },
            xaxis: {
                mode: "categories",
                tickLength: 0
            }
        });
    });
</script>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Profit pro Monat</h3>
    </div>
    <div class="panel-body">
        <div id="PROFITPERMONTH" style="width:400px;height:300px"></div>

    </div>
</div>