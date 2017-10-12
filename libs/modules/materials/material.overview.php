<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'material.class.php';

?>
<!-- DataTables Editor -->
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/datatables.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/datatables.min.js"></script>

<script type="text/javascript" src="jscripts/datatableeditor/FieldType-autoComplete/editor.autoComplete.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.js"></script>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-datetimepicker-2/editor.datetimepicker-2.js"></script>
<!-- /DataTables Editor -->

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Materialien
            <span class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                        Neu
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?page=libs/modules/materials/material.paper.edit.php">Papier</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?page=libs/modules/materials/material.roll.edit.php">Rolle</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?page=libs/modules/materials/material.printingplate.edit.php">Druckplatte</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?page=libs/modules/materials/material.tool.edit.php">Werkzeug</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?page=libs/modules/materials/material.finish.edit.php">Lack</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?page=libs/modules/materials/material.packing.edit.php">Verpackung</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Import</a></li>
                    </ul>
                </div>
            </span>
        </h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filter</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Typ</label>
                    <div class="col-sm-10">
                        <select name="type" id="type" class="form-control">
                            <option value="0" selected>- Alle -</option>
                            <?php
                            $types = Material::getTypeArray();
                            foreach ($types as $item) {
                                echo '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="datatable">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Typ</th>
                <th>Beschreibung</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Typ</th>
                <th>Beschreibung</th>
                <th></th>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript" language="javascript" class="init">
    var editor; // use a global for the submit and return data rendering in the examples
    var table; // use global for table

    $(document).ready(function() {

        $('#type').change(function(){
            console.log(this.value);
            table.ajax.reload();
        });

        table = $('#datatable').DataTable( {
            dom: "<'row'<'col-sm-4'l><'col-sm-4'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
            ajax: {
                url: 'libs/basic/datatables/materials.php',
                data: function (d) {
                    return {
                        "type": $('#type').val()
                    };
                }
            },
            order: [[ 1, 'desc' ]],
            columns: [
                { data: "id", orderable: true },
                { data: "name", orderable: true },
                { data: "typename", orderable: true },
                { data: "description", orderable: false },
                { data: "type", orderable: false, visible: false },
            ],
            select: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
            }
        } );

        $("#datatable").on('click', 'tbody td', function(){
            var data = table.row( this ).data();
            var id = data.id;
            var type = parseInt(data.type);
            var file = '';
            switch (type){
                case 1:
                    file = 'material.paper.edit.php';
                    break;
                case 2:
                    file = 'material.roll.edit.php';
                    break;
                case 3:
                    file = 'material.printingplate.edit.php';
                    break;
                case 4:
                    file = 'material.tool.edit.php';
                    break;
                case 5:
                    file = 'material.finish.edit.php';
                    break;
                case 6:
                    file = 'material.packing.edit.php';
                    break;
            }
            if (file != '')
                document.location='index.php?page=libs/modules/materials/'+file+'&id='+id;
        });
    } );
</script>