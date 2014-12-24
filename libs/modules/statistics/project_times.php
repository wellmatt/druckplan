<?php
require_once('libs/modules/timekeeping/timekeeper.class.php');
?>
<div class="box1" style="width:1300px;">

    <form action="index.php?page=<?=$_REQUEST['page']?>" target="_blank" method="post">

        <p><button type="submit">Markierte Einträge als CSV exportieren&nbsp;&raquo;</button></p>

        <input type="hidden" name="exec" value="projectTimes" />
        <input type="hidden" name="subExec" value="projectTimesExportCSV" />
        <input type="hidden" name="projecttimesexport" value="1" />

        <table style="width: 100%">

            <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Auftrag</th>
                <th>Kunde</th>
                <th>Ticket</th>
                <th>Buchungen</th>
                <th>Zeitaufwand</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0; foreach(Order::getOrderWithTickets() as $order) : ?>
                <?php
                $timeStats = Timekeeper::getOrderTimes($order->getId());
                if(0 == count($timeStats)) continue;
                $secondsTotal = 0;
                $i++;
                ?>
                <tr class="<?=getRowColor($i)?>">
                    <td><input type="checkbox" name="orderIds[]" value="<?= $order->getId() ?>" checked="checked"></td>
                    <td><a title="Zur Auftragsbearbeitung wechseln"
                           href="index.php?page=libs/modules/calculation/order.php&exec=edit&id=<?=$order->getId()?>&step=4">
                            <strong><?= $order->getTitle() ?></strong>
                        </a>
                    </td>
                    <td><?= $order->getCustomer()->getNameAsLine() ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php foreach($timeStats as $stat) : ?>
                    <?php
                    $secondsTotal += $stat['seconds'];
                    $commentsArray = explode('|', $stat['projectComments']);
                    $commentsHtml = '';
                    foreach($commentsArray as $c) {
                        $commentsHtml .= '&bull; ' . $c . '&#10;';
                    }
                    ?>
                    <tr class="<?=getRowColor($i)?>">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><?= $stat['tkt_title'] ?></td>
                        <td><abbr style="cursor: help; border-bottom: 1px dotted #555;" title="<?= $commentsHtml ?>"><?= $stat['ticketcount'] ?> Buchungen</abbr></td>
                        <td><?= formatSeconds($stat['seconds']) ?></td>
                    </tr>

                <?php endforeach; ?>
                <tr class="<?=getRowColor($i)?>">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="2" style="text-align: right;"><em>Gesamt-Zeitaufwand für dieses Projekt:&nbsp;&nbsp;</em></td>
                    <td><?= formatSeconds($secondsTotal); ?></td>
                </tr>
                <tr class="<?=getRowColor($i)?>">
                    <td colspan="6">&nbsp;</td>
                </tr>
            <?php endforeach; ?>

            </tbody>

        </table>

        <p><button type="submit">Markierte Einträge als CSV exportieren&nbsp;&raquo;</button></p>

    </form>




</div>