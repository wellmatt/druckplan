<?/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once('libs/modules/paymentterms/paymentterms.class.php');
require_once('libs/modules/deliveryterms/deliveryterms.class.php');
require_once('libs/modules/businesscontact/address.class.php');
require_once('libs/modules/collectiveinvoice/collectiveinvoice.class.php');
require_once('libs/modules/collectiveinvoice/orderposition.class.php');
require_once 'libs/modules/warehouse/warehouse.class.php';
require_once('libs/modules/documents/document.class.php');
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/associations/association.class.php';
require_once 'personalization.class.php';
require_once 'personalization.item.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;
// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

if ($_REQUEST["pdffile"])
{
    $file = '../../../docs/personalization/'.$_REQUEST["pdffile"];
}
?>

<!DOCTYPE html>
<html dir="ltr" mozdisallowselectionprint moznomarginboxes>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="google" content="notranslate">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PDF.js viewer</title>


    <link rel="stylesheet" href="../../../jscripts/pdfjs/web/viewer.css"/>

    <script src="../../../jscripts/pdfjs/web/compatibility.js"></script>



    <!-- This snippet is used in production (included from viewer.html) -->
    <link rel="resource" type="application/l10n" href="../../../jscripts/pdfjs/web/locale/locale.properties"/>
    <script src="../../../jscripts/pdfjs/web/l10n.js"></script>
    <script src="../../../jscripts/pdfjs/build/pdf.js"></script>



    <script src="../../../jscripts/pdfjs/web/debugger.js"></script>
    <script src="../../../jscripts/pdfjs/web/viewer.js"></script>


    <script>
        var DEFAULT_URL = '<?php echo $file;?>';
        PDFJS.imageResourcesPath = '../../../jscripts/pdfjs/web/images/';
        PDFJS.workerSrc = '../../../jscripts/pdfjs/build/pdf.worker.js';
        PDFJS.cMapUrl = '../../../jscripts/pdfjs/web/cmaps/';
    </script>

</head>

<body tabindex="1" class="loadingInProgress">
<div id="outerContainer">

    <div id="sidebarContainer">
        <div id="toolbarSidebar">
            <div class="splitToolbarButton toggled">
                <button id="viewThumbnail" class="toolbarButton group toggled" title="Show Thumbnails" tabindex="2" data-l10n-id="thumbs">
                    <span data-l10n-id="thumbs_label">Thumbnails</span>
                </button>
                <button id="viewOutline" class="toolbarButton group" title="Show Document Outline" tabindex="3" data-l10n-id="outline">
                    <span data-l10n-id="outline_label">Document Outline</span>
                </button>
                <button id="viewAttachments" class="toolbarButton group" title="Show Attachments" tabindex="4" data-l10n-id="attachments">
                    <span data-l10n-id="attachments_label">Attachments</span>
                </button>
            </div>
        </div>
        <div id="sidebarContent">
            <div id="thumbnailView">
            </div>
            <div id="outlineView" class="hidden">
            </div>
            <div id="attachmentsView" class="hidden">
            </div>
        </div>
    </div>  <!-- sidebarContainer -->

    <div id="mainContainer">
        <div class="findbar hidden doorHanger hiddenSmallView" id="findbar">
            <label for="findInput" class="toolbarLabel" data-l10n-id="find_label">Find:</label>
            <input id="findInput" class="toolbarField" tabindex="91">
            <div class="splitToolbarButton">
                <button class="toolbarButton findPrevious" title="" id="findPrevious" tabindex="92" data-l10n-id="find_previous">
                    <span data-l10n-id="find_previous_label">Previous</span>
                </button>
                <div class="splitToolbarButtonSeparator"></div>
                <button class="toolbarButton findNext" title="" id="findNext" tabindex="93" data-l10n-id="find_next">
                    <span data-l10n-id="find_next_label">Next</span>
                </button>
            </div>
            <input type="checkbox" id="findHighlightAll" class="toolbarField" tabindex="94">
            <label for="findHighlightAll" class="toolbarLabel" data-l10n-id="find_highlight">Highlight all</label>
            <input type="checkbox" id="findMatchCase" class="toolbarField" tabindex="95">
            <label for="findMatchCase" class="toolbarLabel" data-l10n-id="find_match_case_label">Match case</label>
            <span id="findResultsCount" class="toolbarLabel hidden"></span>
            <span id="findMsg" class="toolbarLabel"></span>
        </div>  <!-- findbar -->

        <div id="secondaryToolbar" class="secondaryToolbar hidden doorHangerRight">
            <div id="secondaryToolbarButtonContainer">
                <button id="secondaryPresentationMode" class="secondaryToolbarButton presentationMode visibleLargeView" title="Switch to Presentation Mode" tabindex="51" data-l10n-id="presentation_mode">
                    <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
                </button>

                <button id="secondaryOpenFile" class="secondaryToolbarButton openFile visibleLargeView" title="Open File" tabindex="52" data-l10n-id="open_file">
                    <span data-l10n-id="open_file_label">Open</span>
                </button>

                <button id="secondaryPrint" class="secondaryToolbarButton print visibleMediumView" title="Print" tabindex="53" data-l10n-id="print">
                    <span data-l10n-id="print_label">Print</span>
                </button>

                <button id="secondaryDownload" class="secondaryToolbarButton download visibleMediumView" title="Download" tabindex="54" data-l10n-id="download">
                    <span data-l10n-id="download_label">Download</span>
                </button>

                <a href="#" id="secondaryViewBookmark" class="secondaryToolbarButton bookmark visibleSmallView" title="Current view (copy or open in new window)" tabindex="55" data-l10n-id="bookmark">
                    <span data-l10n-id="bookmark_label">Current View</span>
                </a>

                <div class="horizontalToolbarSeparator visibleLargeView"></div>

                <button id="firstPage" class="secondaryToolbarButton firstPage" title="Go to First Page" tabindex="56" data-l10n-id="first_page">
                    <span data-l10n-id="first_page_label">Go to First Page</span>
                </button>
                <button id="lastPage" class="secondaryToolbarButton lastPage" title="Go to Last Page" tabindex="57" data-l10n-id="last_page">
                    <span data-l10n-id="last_page_label">Go to Last Page</span>
                </button>

                <div class="horizontalToolbarSeparator"></div>

                <button id="pageRotateCw" class="secondaryToolbarButton rotateCw" title="Rotate Clockwise" tabindex="58" data-l10n-id="page_rotate_cw">
                    <span data-l10n-id="page_rotate_cw_label">Rotate Clockwise</span>
                </button>
                <button id="pageRotateCcw" class="secondaryToolbarButton rotateCcw" title="Rotate Counterclockwise" tabindex="59" data-l10n-id="page_rotate_ccw">
                    <span data-l10n-id="page_rotate_ccw_label">Rotate Counterclockwise</span>
                </button>

                <div class="horizontalToolbarSeparator"></div>

                <button id="toggleHandTool" class="secondaryToolbarButton handTool" title="Enable hand tool" tabindex="60" data-l10n-id="hand_tool_enable">
                    <span data-l10n-id="hand_tool_enable_label">Enable hand tool</span>
                </button>

                <div class="horizontalToolbarSeparator"></div>

                <button id="documentProperties" class="secondaryToolbarButton documentProperties" title="Document Properties…" tabindex="61" data-l10n-id="document_properties">
                    <span data-l10n-id="document_properties_label">Document Properties…</span>
                </button>
            </div>
        </div>  <!-- secondaryToolbar -->

        <div class="toolbar">
            <div id="toolbarContainer">
                <div id="toolbarViewer">
                    <div id="toolbarViewerLeft">
                        <button id="sidebarToggle" class="toolbarButton" title="Toggle Sidebar" tabindex="11" data-l10n-id="toggle_sidebar">
                            <span data-l10n-id="toggle_sidebar_label">Toggle Sidebar</span>
                        </button>
                        <div class="toolbarButtonSpacer"></div>
                        <button id="viewFind" class="toolbarButton group hiddenSmallView" title="Find in Document" tabindex="12" data-l10n-id="findbar">
                            <span data-l10n-id="findbar_label">Find</span>
                        </button>
                        <div class="splitToolbarButton">
                            <button class="toolbarButton pageUp" title="Previous Page" id="previous" tabindex="13" data-l10n-id="previous">
                                <span data-l10n-id="previous_label">Previous</span>
                            </button>
                            <div class="splitToolbarButtonSeparator"></div>
                            <button class="toolbarButton pageDown" title="Next Page" id="next" tabindex="14" data-l10n-id="next">
                                <span data-l10n-id="next_label">Next</span>
                            </button>
                        </div>
                        <label id="pageNumberLabel" class="toolbarLabel" for="pageNumber" data-l10n-id="page_label">Page: </label>
                        <input type="number" id="pageNumber" class="toolbarField pageNumber" value="1" size="4" min="1" tabindex="15">
                        <span id="numPages" class="toolbarLabel"></span>
                    </div>
                    <div id="toolbarViewerRight">
                        <button id="presentationMode" class="toolbarButton presentationMode hiddenLargeView" title="Switch to Presentation Mode" tabindex="31" data-l10n-id="presentation_mode">
                            <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
                        </button>

                        <button id="openFile" class="toolbarButton openFile hiddenLargeView" title="Open File" tabindex="32" data-l10n-id="open_file">
                            <span data-l10n-id="open_file_label">Open</span>
                        </button>

                        <button id="print" class="toolbarButton print hiddenMediumView" title="Print" tabindex="33" data-l10n-id="print">
                            <span data-l10n-id="print_label">Print</span>
                        </button>

                        <button id="download" class="toolbarButton download hiddenMediumView" title="Download" tabindex="34" data-l10n-id="download">
                            <span data-l10n-id="download_label">Download</span>
                        </button>
                        <a href="#" id="viewBookmark" class="toolbarButton bookmark hiddenSmallView" title="Current view (copy or open in new window)" tabindex="35" data-l10n-id="bookmark">
                            <span data-l10n-id="bookmark_label">Current View</span>
                        </a>

                        <div class="verticalToolbarSeparator hiddenSmallView"></div>

                        <button id="secondaryToolbarToggle" class="toolbarButton" title="Tools" tabindex="36" data-l10n-id="tools">
                            <span data-l10n-id="tools_label">Tools</span>
                        </button>
                    </div>
                    <div class="outerCenter">
                        <div class="innerCenter" id="toolbarViewerMiddle">
                            <div class="splitToolbarButton">
                                <button id="zoomOut" class="toolbarButton zoomOut" title="Zoom Out" tabindex="21" data-l10n-id="zoom_out">
                                    <span data-l10n-id="zoom_out_label">Zoom Out</span>
                                </button>
                                <div class="splitToolbarButtonSeparator"></div>
                                <button id="zoomIn" class="toolbarButton zoomIn" title="Zoom In" tabindex="22" data-l10n-id="zoom_in">
                                    <span data-l10n-id="zoom_in_label">Zoom In</span>
                                </button>
                            </div>
                  <span id="scaleSelectContainer" class="dropdownToolbarButton">
                     <select id="scaleSelect" title="Zoom" tabindex="23" data-l10n-id="zoom">
                         <option id="pageAutoOption" title="" value="auto" selected="selected" data-l10n-id="page_scale_auto">Automatic Zoom</option>
                         <option id="pageActualOption" title="" value="page-actual" data-l10n-id="page_scale_actual">Actual Size</option>
                         <option id="pageFitOption" title="" value="page-fit" data-l10n-id="page_scale_fit">Fit Page</option>
                         <option id="pageWidthOption" title="" value="page-width" data-l10n-id="page_scale_width">Full Width</option>
                         <option id="customScaleOption" title="" value="custom"></option>
                         <option title="" value="0.5" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 50 }'>50%</option>
                         <option title="" value="0.75" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 75 }'>75%</option>
                         <option title="" value="1" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 100 }'>100%</option>
                         <option title="" value="1.25" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 125 }'>125%</option>
                         <option title="" value="1.5" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 150 }'>150%</option>
                         <option title="" value="2" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 200 }'>200%</option>
                         <option title="" value="3" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 300 }'>300%</option>
                         <option title="" value="4" data-l10n-id="page_scale_percent" data-l10n-args='{ "scale": 400 }'>400%</option>
                     </select>
                  </span>
                        </div>
                    </div>
                </div>
                <div id="loadingBar">
                    <div class="progress">
                        <div class="glimmer">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <menu type="context" id="viewerContextMenu">
            <menuitem id="contextFirstPage" label="First Page"
                      data-l10n-id="first_page"></menuitem>
            <menuitem id="contextLastPage" label="Last Page"
                      data-l10n-id="last_page"></menuitem>
            <menuitem id="contextPageRotateCw" label="Rotate Clockwise"
                      data-l10n-id="page_rotate_cw"></menuitem>
            <menuitem id="contextPageRotateCcw" label="Rotate Counter-Clockwise"
                      data-l10n-id="page_rotate_ccw"></menuitem>
        </menu>

        <div id="viewerContainer" tabindex="0">
            <div id="viewer" class="pdfViewer"></div>
        </div>

        <div id="errorWrapper" hidden='true'>
            <div id="errorMessageLeft">
                <span id="errorMessage"></span>
                <button id="errorShowMore" data-l10n-id="error_more_info">
                    More Information
                </button>
                <button id="errorShowLess" data-l10n-id="error_less_info" hidden='true'>
                    Less Information
                </button>
            </div>
            <div id="errorMessageRight">
                <button id="errorClose" data-l10n-id="error_close">
                    Close
                </button>
            </div>
            <div class="clearBoth"></div>
            <textarea id="errorMoreInfo" hidden='true' readonly="readonly"></textarea>
        </div>
    </div> <!-- mainContainer -->

    <div id="overlayContainer" class="hidden">
        <div id="passwordOverlay" class="container hidden">
            <div class="dialog">
                <div class="row">
                    <p id="passwordText" data-l10n-id="password_label">Enter the password to open this PDF file:</p>
                </div>
                <div class="row">
                    <input type="password" id="password" class="toolbarField" />
                </div>
                <div class="buttonRow">
                    <button id="passwordCancel" class="overlayButton"><span data-l10n-id="password_cancel">Cancel</span></button>
                    <button id="passwordSubmit" class="overlayButton"><span data-l10n-id="password_ok">OK</span></button>
                </div>
            </div>
        </div>
    </div>  <!-- overlayContainer -->

</div> <!-- outerContainer -->
<div id="printContainer"></div>
<div id="mozPrintCallback-shim" hidden>
    <style>
        @media print {
            #printContainer div {
                page-break-after: always;
                page-break-inside: avoid;
            }
        }
    </style>
    <style scoped>
        #mozPrintCallback-shim {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 9999999;

            display: block;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.5);
        }
        #mozPrintCallback-shim[hidden] {
            display: none;
        }
        @media print {
            #mozPrintCallback-shim {
                display: none;
            }
        }

        #mozPrintCallback-shim .mozPrintCallback-dialog-box {
            display: inline-block;
            margin: -50px auto 0;
            position: relative;
            top: 45%;
            left: 0;
            min-width: 220px;
            max-width: 400px;

            padding: 9px;

            border: 1px solid hsla(0, 0%, 0%, .5);
            border-radius: 2px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);

            background-color: #474747;

            color: hsl(0, 0%, 85%);
            font-size: 16px;
            line-height: 20px;
        }
        #mozPrintCallback-shim .progress-row {
            clear: both;
            padding: 1em 0;
        }
        #mozPrintCallback-shim progress {
            width: 100%;
        }
        #mozPrintCallback-shim .relative-progress {
            clear: both;
            float: right;
        }
        #mozPrintCallback-shim .progress-actions {
            clear: both;
        }
    </style>
    <div class="mozPrintCallback-dialog-box">
        <!-- TODO: Localise the following strings -->
        Preparing document for printing...
        <div class="progress-row">
            <progress value="0" max="100"></progress>
            <span class="relative-progress">0%</span>
        </div>
        <div class="progress-actions">
            <input type="button" value="Cancel" class="mozPrintCallback-cancel">
        </div>
    </div>
</div>

</body>
</html>
