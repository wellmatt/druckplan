<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'machine.class.php';

if ($_REQUEST['exec'] == "delete") {
    $del = new Machine($_REQUEST['delid']);
    $del->deleteSoft();
    echo "<script>window.location.href='index.php?page=libs/modules/machines/machine.overview.php';</script>";
}

if ($_REQUEST["exec"] == "save") {
    $array = [
        'name' => $_REQUEST["name"],
        'description' => $DB->escape(trim($_REQUEST["description"]))
    ];

    $machine = new Machine((int)$_REQUEST["id"], $array);
    $machine->save();
    $_REQUEST["id"] = $machine->getId();
}

$machine = new Machine($_REQUEST['id']);

?>
<!-- DataTables Editor -->
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/datatables.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/datatables.min.js"></script>

<script type="text/javascript" src="jscripts/datatableeditor/FieldType-autoComplete/editor.autoComplete.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.js"></script>
<!--<script type="text/javascript" src="jscripts/datatableeditor/FieldType-datetimepicker-2/editor.datetimepicker-2.js"></script>-->

<script type="text/javascript" src="jscripts/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="jscripts/ckeditor/config.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/ckeditor/skins/bootstrapck/editor.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-ckeditor/editor.ckeditor.js"></script>
<!-- /DataTables Editor -->

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
$quickmove->addItem('Zurück', 'index.php?page=libs/modules/machines/machine.overview.php', null, 'glyphicon-step-backward');
$quickmove->addItem('Speichern', '#', "$('#form').submit();", 'glyphicon-floppy-disk');
if ($machine->getId() > 0) {
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/machines/machine.edit.php&exec=delete&delid=" . $machine->getId() . "');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Maschine - <?php echo $machine->getTitle(); ?></h3>
    </div>
    <div class="panel-body" style="padding: 0;">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="form" id="form" method="post"
              class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">

            <div id="tabs" style="border: none;">
                <ul>
                    <li><a href="#tabs-1">Kopfdaten</a></li>
                    <li><a href="#tabs-2">Produktionsdaten</a></li>
                    <li><a href="#tabs-3">Arbeitsschritte</a></li>
                    <li><a href="#tabs-4">Arbeitszeiten</a></li>
                    <li><a href="#tabs-5">Qualifizierte Nuter/Gruppen</a></li>
                    <li><a href="#tabs-6">Materialien</a></li>
                    <li><a href="#tabs-7">Sperrzeiten</a></li>
                </ul>
                <div id="tabs-1">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title" id="title"
                                   value="<?php echo $machine->getTitle(); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Beschreibung</label>
                        <div class="col-sm-10">
                    <textarea class="form-control" name="description"
                              id="description"><?php echo $machine->getDescription(); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Interface URL</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="interfaceurl" id="interfaceurl"
                                   value="<?php echo $machine->getInterfaceurl(); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Typ</label>
                        <div class="col-sm-10 form-text"><?php echo $machine->getTypename();?></div>
                    </div>
                </div>
                <div id="tabs-2">
                    <p>TODO</p>
                </div>
                <div id="tabs-3">
                    <div class="table-responsive" style="margin: -7px -1px -7px -1px;">
                        <table id="worksteps_datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Aufschlag (min)</th>
                                    <th>Abschlag (min)</th>
                                    <th>Erforderlich</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div id="tabs-4">
                    <div class="table-responsive" style="margin: -7px -1px -7px -1px;">
                        <table id="worktimes_datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Wochentag</th>
                                    <th>Beginn</th>
                                    <th>Ende</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div id="tabs-7">
                    <div class="table-responsive" style="margin: -7px -1px -7px -1px;">
                        <table id="locktimes_datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Hinweis</th>
                                    <th>Beginn</th>
                                    <th>Ende</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script language="JavaScript">
    $(function () {
        var editor = CKEDITOR.replace('description', {
            // Define the toolbar groups as it is a more accessible solution.
            toolbarGroups: [
                {name: 'clipboard', groups: ['clipboard', 'undo']},
                {name: 'editing', groups: ['find', 'selection', 'spellchecker']},
                {name: 'links'},
                {name: 'insert'},
                {name: 'tools'},
                {name: 'others'},
                '/',
                {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align']},
                {name: 'styles'},
                {name: 'colors'}
            ]
            // Remove the redundant buttons from toolbar groups defined above.
            //removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
        });
        $( "#tabs" ).tabs();
    });
</script>

<script type="text/javascript" language="javascript" class="init">
    var worktimes_editor; // use a global for the submit and return data rendering in the examples
    var worktimes_table; // use global for table

    $(document).ready(function() {

        worktimes_editor = new $.fn.dataTable.Editor( {
            ajax: {
                url: 'libs/basic/datatables/machines.worktimes.php',
                data: {
                    "machine": <?php echo $machine->getId();?>
                }
            },
            table: "#worktimes_datatable",
            fields: [
                {
                    name: 'machine',
                    type: "hidden",
                    default: '<?php echo $machine->getId();?>'
                },
                {
                    label: 'Wochentag',
                    name: 'weekday',
                    type: "select"
                },
                {
                    label: 'Beginn',
                    name: 'start',
                    type: "datetime",
                    format: 'HH:mm'
                },
                {
                    label: 'Ende',
                    name: 'end',
                    type: "datetime",
                    format: 'HH:mm'
                }
            ],
            i18n: {
                create: {
                    button: "Neu",
                    title:  "Hinzufügen",
                    submit: "Speichern"
                },
                edit: {
                    button: "Bearbeiten",
                    title:  "Bearbeiten",
                    submit: "Aktualisieren"
                },
                remove: {
                    button: "Löschen",
                    title:  "Löschen",
                    submit: "Entfernen",
                    confirm: {
                        _: "Sollen wirklich %d Einträge gelöscht werden?",
                        1: "Soll dieser Eintrag wirklich gelöscht werden?"
                    }
                }
            }
        } );

        // Activate an inline edit on click of a table cell
        $('#worktimes_datatable').on( 'click', 'tbody td', function (e) {
            worktimes_editor.inline( this, {
                onBlur: 'submit'
            } );
        } );

        worktimes_table = $('#worktimes_datatable').DataTable( {
            dom: "<'row'<'col-sm-12'Btr>>",
            ajax: {
                url: 'libs/basic/datatables/machines.worktimes.php',
                data: {
                    "machine": <?php echo $machine->getId();?>
                }
            },
            paging: false,
            searching: false,
            columns: [
                { data: 'weekday', orderable: true },
                { data: 'start', orderable: false },
                { data: 'end', orderable: false },
            ],
            select: true,
            buttons: [
                { extend: "create", editor: worktimes_editor },
                { extend: "edit",   editor: worktimes_editor },
                { extend: "remove", editor: worktimes_editor }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
            }
        } );
    } );
</script>
<script type="text/javascript" language="javascript" class="init">
    var locktimes_editor; // use a global for the submit and return data rendering in the examples
    var locktimes_table; // use global for table

    $(document).ready(function() {

        locktimes_editor = new $.fn.dataTable.Editor( {
            ajax: {
                url: 'libs/basic/datatables/machines.locktimes.php',
                data: {
                    "machine": <?php echo $machine->getId();?>
                }
            },
            table: "#locktimes_datatable",
            fields: [
                {
                    name: 'machine',
                    type: "hidden",
                    default: '<?php echo $machine->getId();?>'
                },
                {
                    label: 'Hinweis',
                    name: 'note',
                    type: "text"
                },
                {
                    label: 'Beginn',
                    name: 'start',
                    type: "datetime",
                    format: 'DD.MM.YYYY HH:mm'
                },
                {
                    label: 'Ende',
                    name: 'end',
                    type: "datetime",
                    format: 'DD.MM.YYYY HH:mm'
                }
            ],
            i18n: {
                create: {
                    button: "Neu",
                    title:  "Hinzufügen",
                    submit: "Speichern"
                },
                edit: {
                    button: "Bearbeiten",
                    title:  "Bearbeiten",
                    submit: "Aktualisieren"
                },
                remove: {
                    button: "Löschen",
                    title:  "Löschen",
                    submit: "Entfernen",
                    confirm: {
                        _: "Sollen wirklich %d Einträge gelöscht werden?",
                        1: "Soll dieser Eintrag wirklich gelöscht werden?"
                    }
                }
            }
        } );

        // Activate an inline edit on click of a table cell
        $('#locktimes_datatable').on( 'click', 'tbody td', function (e) {
            locktimes_editor.inline( this, {
                onBlur: 'submit'
            } );
        } );

        locktimes_table = $('#locktimes_datatable').DataTable( {
            dom: "<'row'<'col-sm-12'Btr>>",
            ajax: {
                url: 'libs/basic/datatables/machines.locktimes.php',
                data: {
                    "machine": <?php echo $machine->getId();?>
                }
            },
            paging: false,
            searching: false,
            columns: [
                { data: 'note', orderable: false },
                { data: 'start', orderable: true },
                { data: 'end', orderable: true },
            ],
            select: true,
            buttons: [
                { extend: "create", editor: locktimes_editor },
                { extend: "edit",   editor: locktimes_editor },
                { extend: "remove", editor: locktimes_editor }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
            }
        } );
    } );
</script>


<script type="text/javascript" language="javascript" class="init">
    var worksteps_editor; // use a global for the submit and return data rendering in the examples
    var worksteps_table; // use global for table

    $(document).ready(function() {

        worksteps_editor = new $.fn.dataTable.Editor( {
            ajax: {
                url: 'libs/basic/datatables/machines.worksteps.php',
                data: {
                    "machine": <?php echo $machine->getId();?>
                }
            },
            table: "#worksteps_datatable",
            fields: [
                {
                    name: 'machine',
                    type: "hidden",
                    default: '<?php echo $machine->getId();?>'
                },
                {
                    label: 'Name',
                    name: 'title',
                    type: "text"
                },
                {
                    label: 'Erforderlich',
                    name: 'essential',
                    type:  "checkbox",
                    options: [
                        { label: "Ja", value: 1 }
                    ],
                    separator: '',
                    unselectedValue: 0
                },
                {
                    label: 'Aufschlag (min)',
                    name: 'timeadded',
                    type: "text"
                },
                {
                    label: 'Abschlag (min)',
                    name: 'timesaving',
                    type: "text"
                },
            ],
            i18n: {
                create: {
                    button: "Neu",
                    title:  "Hinzufügen",
                    submit: "Speichern"
                },
                edit: {
                    button: "Bearbeiten",
                    title:  "Bearbeiten",
                    submit: "Aktualisieren"
                },
                remove: {
                    button: "Löschen",
                    title:  "Löschen",
                    submit: "Entfernen",
                    confirm: {
                        _: "Sollen wirklich %d Einträge gelöscht werden?",
                        1: "Soll dieser Eintrag wirklich gelöscht werden?"
                    }
                }
            }
        } );

        worksteps_editor
            .on( 'postCreate postRemove', function () {
                // After create or edit, a number of other rows might have been effected -
                // so we need to reload the table, keeping the paging in the current position
                worksteps_table.ajax.reload( null, false );
            } )
            .on( 'initCreate', function () {
                // Enable order for create
                worksteps_editor.field( 'readingOrder' ).enable();
            } )
            .on( 'initEdit', function () {
                // Disable for edit (re-ordering is performed by click and drag)
                worksteps_editor.field( 'readingOrder' ).disable();
            } );

        // Activate an inline edit on click of a table cell
        $('#worksteps_datatable').on( 'click', 'tbody td', function (e) {
            worksteps_editor.inline( this, {
                onBlur: 'submit'
            } );
        } );

        worksteps_table = $('#worksteps_datatable').DataTable( {
            dom: "<'row'<'col-sm-12'Btr>>",
            ajax: {
                url: 'libs/basic/datatables/machines.worksteps.php',
                data: {
                    "machine": <?php echo $machine->getId();?>
                }
            },
            paging: false,
            searching: false,
            columns: [
                { data: 'sequence', orderable: false },
                { data: 'title', orderable: false },
                { data: 'timeadded', orderable: false },
                { data: 'timesaving', orderable: false },
                { data: 'essential', orderable: false },
            ],
            rowReorder: {
                dataSrc: 'sequence',
                editor:  worksteps_editor
            },
            select: true,
            buttons: [
                { extend: "create", editor: worksteps_editor },
                { extend: "edit",   editor: worksteps_editor },
                { extend: "remove", editor: worksteps_editor }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
            }
        } );
    } );
</script>