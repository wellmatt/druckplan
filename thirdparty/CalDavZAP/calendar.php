<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
error_reporting(-1);
ini_set('display_errors', 1);
//chdir('../../');
//require_once("config.php");
//require_once("libs/basic/mysql.php");
//require_once("libs/basic/globalFunctions.php");
//require_once("libs/basic/user/user.class.php");
//require_once("libs/basic/groups/group.class.php");
//require_once("libs/basic/clients/client.class.php");
//require_once("libs/basic/translator/translator.class.php");
//require_once 'libs/basic/countries/country.class.php';
//require_once 'libs/basic/cachehandler/cachehandler.class.php';
//require_once 'thirdparty/phpfastcache/phpfastcache.php';
session_start();

//$DB = new DBMysql();
//$DB->connect($_CONFIG->db);
//
//Global $_USER;
//$_USER = new User();
//$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

?>

<!DOCTYPE html>
<html lang="en" manifest="cache.manifest">
<head>
    <title>CalDavZAP</title>
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--
    CalDavZAP - the open source CalDAV Web Client
    Copyright (C) 2011-2015
        Jan Mate <jan.mate@inf-it.com>
        Andrej Lezo <andrej.lezo@inf-it.com>
        Matej Mihalik <matej.mihalik@inf-it.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
    -->
    <link rel="stylesheet" href="css/jquery-ui.custom.css" type="text/css" />
    <link rel="stylesheet" href="css/spectrum.custom.css" type="text/css" />
    <link rel="stylesheet" href="css/default.css" type="text/css" />
    <link rel="stylesheet" href="css/fullcalendar.css" type="text/css" />
    <link rel="stylesheet" href="css/default_integration.css" type="text/css" />
    <script src="cache_handler.js" type="text/javascript"></script>
    <script src="lib/jquery-2.1.4.min.js" type="text/javascript"></script>
    <script src="lib/jquery.browser.js" type="text/javascript"></script>
    <script src="lib/jquery.autosize.js" type="text/javascript"></script>
    <script src="lib/jquery-ui-1.11.4.custom.js" type="text/javascript"></script>
    <script src="lib/jquery.quicksearch.js" type="text/javascript"></script>
    <script src="lib/jquery.placeholder-1.1.9.js" type="text/javascript"></script>
    <script src="lib/jshash-2.2_sha256.js" type="text/javascript"></script>
    <script src="lib/spectrum.js" type="text/javascript"></script>
    <script src="lib/fullcalendar.js" type="text/javascript"></script>
    <script src="config.js" type="text/javascript"></script>
    <script src="common.js" type="text/javascript"></script>
    <script src="webdav_protocol.js" type="text/javascript"></script>
    <script src="localization.js" type="text/javascript"></script>
    <script src="interface.js" type="text/javascript"></script>
    <script src="vcalendar_rfc_regex.js" type="text/javascript"></script>
    <script src="resource.js" type="text/javascript"></script>
    <script src="vcalendar.js" type="text/javascript"></script>
    <script src="vtodo.js" type="text/javascript"></script>
    <script src="lib/rrule.js" type="text/javascript"></script>
    <script src="data_process.js" type="text/javascript"></script>
    <script src="main.js" type="text/javascript"></script>
    <script src="forms.js" type="text/javascript"></script>
    <script src="timezones.js" type="text/javascript"></script>
    <script language="JavaScript">
        var globalNetworkCheckSettings={
            href: 'http://contilas2.mein-druckplan.de/sabre/server.php/principals/',
            timeOut: 90000,
            lockTimeOut: 10000,
            checkContentType: true,
            settingsAccount: true,
            delegation: true,
            additionalResources: [],
            hrefLabel: null,
            forceReadOnly: null,
            ignoreAlarms: false,
            backgroundCalendars: []
        };

        $(function() {
            globalLoginUsername='<?php echo $_SESSION["login"];?>';
            globalLoginPassword='<?php echo $_SESSION["password"];?>';
            loadConfig();
        });
    </script>
</head>
<body>
<div id="cacheDialog">
    <div id="cacheDialogText">newer version detected!</div>
    <div id="cacheDialogButtonWrapper">
        <input id="cacheDialogButton" type="button" value="refresh" onclick="window.location.reload()"/>
    </div>
</div>
<div id="MainLoader">
    <div id="MainLoaderInner" class="loaderInfo">Loading ...</div>
    <div class="loader"></div>
</div>
<div id="AlertDisabler"></div>
<div id="alertBox">
    <h1 id="alertsH">Alerts</h1>
    <div id="alertBoxContent"></div>
    <input id="alertButton" type="button" value="Clear Alerts" onclick="clearAlertEvents();" />
</div>
<div class="integration_d">
    <div id="intCaldav" title="calendar" onclick="checkForApplication('CalDavZAP');">
        <img class="int_error" src="images/error_badge.svg" alt="error" />
    </div>
    <div id="intCaldavTodo" title="todo" onclick="checkForApplication('CalDavTODO');">
        <img class="int_error" src="images/error_badge.svg" alt="error" />
    </div>
    <div class="intBlank"></div>
    <div id="intRefresh" title="refresh" onclick="reloadResources();"></div>
    <div class="intBlank"></div>
    <div id="intLogout" title="logout" onclick="logout();"></div>
</div>
<div class="System" id="SystemCalDavZAP">
    <div id="CalDavZAPPopup">
        <div id="CalDavZAPPopupColor"></div>
        <table id="CalDavZAPPopupTable">
            <tr>
                <td colspan="2" class="header multiline" data-type="name"></td>
            </tr>
            <tr>
                <td class="label" data-type="location_txt">location</td>
                <td class="value" data-type="location"></td>
            </tr>
            <tr>
                <td class="label" data-type="from_txt">from</td>
                <td class="value" data-type="from"></td>
            </tr>
            <tr>
                <td class="label" data-type="to_txt">to</td>
                <td class="value" data-type="to"></td>
            </tr>
            <tr>
                <td class="label" data-type="status_txt">status</td>
                <td class="value" data-type="status"></td>
            </tr>
            <tr>
                <td class="label" data-type="avail_txt">availability</td>
                <td class="value" data-type="avail"></td>
            </tr>
            <tr>
                <td class="label" data-type="type_txt">privacy</td>
                <td class="value" data-type="type"></td>
            </tr>
            <tr>
                <td class="label" data-type="priority_txt">priority</td>
                <td class="value" data-type="priority"></td>
            </tr>
            <tr>
                <td class="label" data-type="calendar_txt">calendar</td>
                <td class="value" data-type="calendar"></td>
            </tr>
            <tr>
                <td class="label" data-type="url_txt">url</td>
                <td class="value" data-type="url"></td>
            </tr>
            <tr>
                <td class="label" data-type="note_txt">note</td>
                <td class="value multiline" data-type="note"></td>
            </tr>
        </table>
    </div>
    <div id="EventDisabler"></div>
    <div class="update_d" style="display: none;">
        <div class="update_h"></div>
    </div>
    <div class="headers" id="resourceCalDAV_h">
        <span class="resourceCalDAV_text" data-type="resourcesCalDAV_txt">Resources</span>
        <img src="images/add_cal_white.svg" alt="Enable all calendars" title="Enable all calendars" data-type="addAll" class="addRemoveAll addRemoveAllCalDAV" onclick="enableAll()" />
        <img src="images/remove_cal_white.svg" alt="Disable all calendars" title="Disable all calendars" data-type="removeAll" class="addRemoveAll addRemoveAllCalDAV" onclick="disableAll()" />
        <img id="showUnloadedCalendars" src="images/delegation.svg" alt="Subscribe" title="Subscribe" onclick="showUnloadedCollections('event');" />
        <input id="loadUnloadedCalendars" type="button" value="save" onclick="loadAdditionalCollections('event');" style="margin-top:4px;margin-left:6px;" />
        <input id="loadUnloadedCalendarsCancel" type="button" value="cancel" onclick="cancelUnloadedCollections('event');" style="margin-top:4px;margin-right:6px;float:right;" />
    </div>
    <div id="ResourceCalDAVList">
        <div id="ResourceCalDAVListTemplate" style="display: none;">
            <div class="resourceCalDAV_header"><input type="checkbox"></div>
            <div class="resourceCalDAV_item"></div>
        </div>
    </div>
    <div id="timezoneWrapper">
        <label data-type="txt_timezonePicker" for="timezonePicker">Timezone:</label>
        <div id="timezoneSelectDiv">
            <select id="timezonePicker" name="timezonePicker" data-type="timezonesPicker"></select>
        </div>
    </div>
    <div class="headers" id="main_h">
        <input id="ResourceCalDAVToggle" type="image" src="images/resources.svg" alt="Show/Hide Resources" />
        <div id="main_h_placeholder"></div>
        <img id="eventFormShower" src="images/new_item.svg" alt="Add event" />
    </div>
    <div id="searchForm">
        <img alt="Search Form" src="images/search.svg" style="position: inline; margin-top: 4px; margin-left: 8px; vertical-align: top;" />
        <div class="searchContainer">
            <input type="text" value="" placeholder="Search" data-type="PH_CalDAVsearch" id="searchInput" style="margin-top: 3px; vertical-align: top;" />
        </div>
        <img alt="Search Reset"  id="reserButton" onclick="$('#searchInput').val('');$('#searchInput').keyup();" src="images/reset_b.svg" style="position: absolute; margin-top: 5px; right: 9px; vertical-align: top; cursor: pointer; visibility: hidden;" />
    </div>
    <div id="CalendarLoader">
        <div class="loaderInfo">Calendaring ...</div>
        <div class="loader"></div>
    </div>
    <div id='main'>
        <div id='calendar'></div>
    </div>
    <div id="CAEvent">
        <div class="saveLoader">
            <div class="saveLoaderInfo"></div>
            <div class="loader"></div>
        </div>
        <div id="repeatConfirmBox">
            <h1 data-type="repeat_event">Repeat event confirmation</h1>
            <div id="repeatConfirmBoxContent"></div>
            <div id="repeatConfirmBoxQuestion"></div>
            <input id='editAll' type="button" value="All events" /><br />
            <input id='editFuture' type="button" value="All future events" /><br />
            <input id='editOnlyOne' type="button" value="This event only"/><br />
            <input type="button" data-type="closeRepeat" value="Close" onclick="$('#repeatConfirmBoxContent').html(''); $('#repeatConfirmBox').css('visibility', 'hidden'); $('#EventDisabler').fadeOut(globalEditorFadeAnimation);" />
        </div>
        <div id="event_details_template">
            <div id="eventColor"></div>
            <div id="eventDetailsContainer">
                <table id="eventDetailsTable">
                    <tr>
                        <th colspan="3" class="headerContainer">
                            <div class="formNav prev" title="show previous event" data-type="event_prev_nav"><img src="images/arrow_prev.svg" alt="previous"/></div>
                            <textarea class="header" data-type="name" placeholder="Name" name="name" id="name"></textarea>
                            <div class="formNav next" title="show next event" data-type="event_next_nav"><img src="images/arrow_next.svg" alt="next"/></div>
                        </th>
                    </tr>
                    <tr>
                        <td class="label"><label data-type="location" for="location">location:</label></td>
                        <td colspan="2"><input class="long" data-type="PH_location" type="text" placeholder="Location" name="location" id="location" /></td>
                    </tr>
                    <tr>
                        <td class="label"><label data-type="all_day" for="allday">all-day: </label></td>
                        <td colspan="2"><input class="long" type="checkbox" name="allday" id="allday" /></td>
                    </tr>
                    <tr>
                        <td class="label"><label data-type="from" for="date_from">from: </label></td>
                        <td>
                            <input class="date small" type="text" data-type="PH_date_from" placeholder="Date from" id="date_from" name="date_from" />
                            <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                        </td>
                        <td id="time_from_cell">
                            <input class="time small" type="text" data-type="PH_time_from" placeholder="Time from" id="time_from" name="time_from" />
                            <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label data-type="to" for="date_to">to: </label></td>
                        <td>
                            <input class="date small" type="text" data-type="PH_date_to" placeholder="Date to" id="date_to" name="date_to" />
                            <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                        </td>
                        <td id="time_to_cell">
                            <input class="time small" type="text" data-type="PH_time_to" placeholder="Time to" id="time_to" name="time_to" />
                            <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                        </td>
                    </tr>
                    <tr class="timezone_row">
                        <td class="label"><label data-type="txt_timezone" for="timezone">timezone: </label></td>
                        <td colspan="2" data-size="full">
                            <select class="long" data-type="timezones" name="timezone" id="timezone"></select>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label data-type="repeat" for="repeat">repeat: </label></td>
                        <td colspan="2" data-size="full">
                            <select class="long" name="repeat" id="repeat">
                                <option data-type="repeat_no-repeat" value="no-repeat">No repeat</option>
                                <option data-type="repeat_DAILY" value="DAILY">Daily</option>
                                <option data-type="repeat_BUSINESS" value="BUSINESS">Every business day</option>
                                <option data-type="repeat_WEEKEND" value="WEEKEND">Every weekend</option>
                                <option data-type="repeat_WEEKLY" value="WEEKLY">Weekly</option>
                                <option data-type="repeat_TWO_WEEKLY" value="TWO_WEEKLY">Bi-weekly</option>
                                <option data-type="repeat_MONTHLY" value="MONTHLY">Monthly</option>
                                <option data-type="repeat_YEARLY" value="YEARLY">Yearly</option>
                                <option data-type="repeat_CUSTOM_WEEKLY" value="CUSTOM_WEEKLY">Custom weekly</option>
                                <option data-type="repeat_CUSTOM_MONTHLY" value="CUSTOM_MONTHLY">Custom monthly</option>
                                <option data-type="repeat_CUSTOM_YEARLY" value="CUSTOM_YEARLY">Custom yearly</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="repeat_interval" style="display: none;">
                        <td class="label repeat"><label data-type="repeat_type" for="repeat_interval_detail">Every </label></td>
                        <td style="position: relative;">
                            <input class="small" type="text" data-type="PH_type_Interval" placeholder="Interval" name="end" id="repeat_interval_detail" />
                            <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                        </td>
                        <td>
                            <span class="infoSpan" style="padding-left: 4px;" data-type="txt_interval">days</span>
                        </td>
                    </tr>
                    <tr id="week_custom" style="display: none;">
                        <td class="label repeat"><label data-type="week_custom_txt">On </label></td>
                        <td colspan="2">
                            <table class="customTable customTableWeek">
                                <tr>
                                    <td data-type="0" data-text="SU" class="firstCol">Su</td>
                                    <td data-type="1" data-text="MO">Mo</td>
                                    <td data-type="2" data-text="TU">Tu</td>
                                    <td data-type="3" data-text="WE">We</td>
                                    <td data-type="4" data-text="TH">Th</td>
                                    <td data-type="5" data-text="FR">Fr</td>
                                    <td data-type="6" data-text="SA" class="lastCol">Sa</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr id="month_custom1" style="display: none;">
                        <td class="label"><label data-type="month_custom1_txt"></label></td>
                        <td data-size="half">
                            <select id="repeat_month_custom_select" class="small">
                                <option value="every" data-type="month_custom_every">Every</option>
                                <option value="first" data-type="month_custom_first">First</option>
                                <option value="second" data-type="month_custom_second">Second</option>
                                <option value="third" data-type="month_custom_third">Third</option>
                                <option value="fourth" data-type="month_custom_fourth">Fourth</option>
                                <option value="fifth" data-type="month_custom_fifth">Fifth</option>
                                <option value="last" data-type="month_custom_last">Last</option>
                                <option value="custom" data-type="month_custom_custom">Custom</option>
                            </select>
                        </td>
                        <td style="position: relative;" data-size="half">
                            <select id="repeat_month_custom_select2" class="small">
                                <option value="SU" data-type="0">Sunday</option>
                                <option value="MO" data-type="1">Monday</option>
                                <option value="TU" data-type="2">Tuesday</option>
                                <option value="WE" data-type="3">Wednesday</option>
                                <option value="TH" data-type="4">Thursday</option>
                                <option value="FR" data-type="5">Friday</option>
                                <option value="SA" data-type="6">Saturday</option>
                                <option value="DAY" data-type="month_custom_month">Day of the month</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="month_custom2" style="display: none;">
                        <td class="label repeat"><label data-type="month_custom2_txt">On days</label></td>
                        <td colspan="2">
                            <table class="customTable customTableMonth">
                                <tr>
                                    <td data-type="1" class="firstCol">1</td>
                                    <td data-type="2">2</td>
                                    <td data-type="3">3</td>
                                    <td data-type="4">4</td>
                                    <td data-type="5">5</td>
                                    <td data-type="6">6</td>
                                    <td data-type="7" class="lastCol">7</td>
                                </tr>
                                <tr>
                                    <td data-type="8" class="firstCol">8</td>
                                    <td data-type="9">9</td>
                                    <td data-type="10">10</td>
                                    <td data-type="11">11</td>
                                    <td data-type="12">12</td>
                                    <td data-type="13">13</td>
                                    <td data-type="14" class="lastCol">14</td>
                                </tr>
                                <tr>
                                    <td data-type="15" class="firstCol">15</td>
                                    <td data-type="16">16</td>
                                    <td data-type="17">17</td>
                                    <td data-type="18">18</td>
                                    <td data-type="19">19</td>
                                    <td data-type="20">20</td>
                                    <td data-type="21" class="lastCol">21</td>
                                </tr>
                                <tr>
                                    <td data-type="22" class="firstCol">22</td>
                                    <td data-type="23">23</td>
                                    <td data-type="24">24</td>
                                    <td data-type="25">25</td>
                                    <td data-type="26">26</td>
                                    <td data-type="27">27</td>
                                    <td data-type="28" class="lastCol">28</td>
                                </tr>
                                <tr>
                                    <td data-type="29" class="firstCol">29</td>
                                    <td data-type="30">30</td>
                                    <td data-type="31">31</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr id="year_custom2" style="display: none;">
                        <td class="label"><label data-type="year_custom2"></label></td>
                        <td data-size="half">
                            <select id="repeat_year_custom_select1" class="small">
                                <option value="every" data-type="year_custom_every">Every</option>
                                <option value="first" data-type="year_custom_first">First</option>
                                <option value="second" data-type="year_custom_second">Second</option>
                                <option value="third" data-type="year_custom_third">Third</option>
                                <option value="fourth" data-type="year_custom_fourth">Fourth</option>
                                <option value="fifth" data-type="year_custom_fifth">Fifth</option>
                                <option value="last" data-type="year_custom_last">Last</option>
                                <option value="custom" data-type="year_custom_custom">Custom</option>
                            </select>
                        </td>
                        <td style="position: relative;" data-size="half">
                            <select id="repeat_year_custom_select2" class="small">
                                <option value="SU" data-type="0">Sunday</option>
                                <option value="MO" data-type="1">Monday</option>
                                <option value="TU" data-type="2">Tuesday</option>
                                <option value="WE" data-type="3">Wednesday</option>
                                <option value="TH" data-type="4">Thursday</option>
                                <option value="FR" data-type="5">Friday</option>
                                <option value="SA" data-type="6">Saturday</option>
                                <option value="DAY" data-type="year_custom_month">Day of the month</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="year_custom1" style="display: none;">
                        <td class="label repeat"><label data-type="year_custom1">Every</label></td>
                        <td colspan="2">
                            <table class="customTable customTableMonth">
                                <tr>
                                    <td data-type="1" class="firstCol">1</td>
                                    <td data-type="2">2</td>
                                    <td data-type="3">3</td>
                                    <td data-type="4">4</td>
                                    <td data-type="5">5</td>
                                    <td data-type="6">6</td>
                                    <td data-type="7" class="lastCol">7</td>
                                </tr>
                                <tr>
                                    <td data-type="8" class="firstCol">8</td>
                                    <td data-type="9">9</td>
                                    <td data-type="10">10</td>
                                    <td data-type="11">11</td>
                                    <td data-type="12">12</td>
                                    <td data-type="13">13</td>
                                    <td data-type="14" class="lastCol">14</td>
                                </tr>
                                <tr>
                                    <td data-type="15" class="firstCol">15</td>
                                    <td data-type="16">16</td>
                                    <td data-type="17">17</td>
                                    <td data-type="18">18</td>
                                    <td data-type="19">19</td>
                                    <td data-type="20">20</td>
                                    <td data-type="21" class="lastCol">21</td>
                                </tr>
                                <tr>
                                    <td data-type="22" class="firstCol">22</td>
                                    <td data-type="23">23</td>
                                    <td data-type="24">24</td>
                                    <td data-type="25">25</td>
                                    <td data-type="26">26</td>
                                    <td data-type="27">27</td>
                                    <td data-type="28" class="lastCol">28</td>
                                </tr>
                                <tr>
                                    <td data-type="29" class="firstCol">29</td>
                                    <td data-type="30">30</td>
                                    <td data-type="31">31</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr id="year_custom3" style="display: none;">
                        <td class="label repeat"><label data-type="year_custom3">Of</label></td>
                        <td colspan="2">
                            <table class="customTable customTableYear">
                                <tr>
                                    <td data-type="0" data-text="jan" class="firstCol">Jan</td>
                                    <td data-type="1" data-text="feb">Feb</td>
                                    <td data-type="2" data-text="mar">Mar</td>
                                    <td data-type="3" data-text="apr" class="lastCol">Apr</td>
                                </tr>
                                <tr>
                                    <td data-type="4" data-text="may" class="firstCol">May</td>
                                    <td data-type="5" data-text="jun">Jun</td>
                                    <td data-type="6" data-text="jul">Jul</td>
                                    <td data-type="7" data-text="aug" class="lastCol">Aug</td>
                                </tr>
                                <tr>
                                    <td data-type="8" data-text="sep" class="firstCol">Sep</td>
                                    <td data-type="9" data-text="oct">Oct</td>
                                    <td data-type="10" data-text="nov">Nov</td>
                                    <td data-type="11" data-text="dec" class="lastCol">Dec</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr id="repeat_details" style="display: none;">
                        <td class="label repeat"><label data-type="repeat_end"  for="repeat_end_details">end: </label></td>
                        <td data-size="half">
                            <select class="small" id="repeat_end_details">
                                <option data-type="repeat_details_on_date" value="on_date">on date</option>
                                <option data-type="repeat_details_after" value="after">occurrences</option>
                                <option data-type="repeat_details_never" value="never">never</option>
                            </select>
                        </td>
                        <td style="position: relative;">
                            <input class="date small" type="text" data-type="PH_until_date" placeholder="Date until" name="end" id="repeat_end_date" />
                            <input style="display: none;" class="small" type="text" data-type="PH_repeat_count" placeholder="Repeat count" name="end" id="repeat_end_after" />
                            <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label data-type="status" for="status">status: </label></td>
                        <td colspan="2" data-size="full">
                            <select class="long" name="status" id="status">
                                <option data-type="STATUS_NONE" value="NONE">undefined</option>
                                <option data-type="STATUS_TENTATIVE" value="TENTATIVE">tentative</option>
                                <option data-type="STATUS_CONFIRMED" value="CONFIRMED">confirmed</option>
                                <option data-type="STATUS_CANCELLED" value="CANCELLED">canceled</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="row_avail">
                        <td class="label"><label data-type="txt_avail" for="avail">availability: </label></td>
                        <td colspan="2" data-size="full">
                            <select class="long" data-type="avail" name="avail" id="avail">
                                <option data-type="BUSY_AVAIL" value="busy">busy</option>
                                <option data-type="FREE_AVAIL" value="free">free</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="row_type">
                        <td class="label"><label data-type="txt_type" for="type">privacy: </label></td>
                        <td colspan="2" data-size="full">
                            <select class="long" data-type="type" name="type" id="type">
                                <option data-type="PUBLIC_TYPE" value="public">public</option>
                                <option data-type="CONFIDENTIAL_TYPE" value="confidential">confidential</option>
                                <option data-type="PRIVATE_TYPE" value="private">private</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><label data-type="priority" for="priority">priority: </label></td>
                        <td colspan="2" data-size="full">
                            <select class="long " name="priority" id="priority">
                                <option data-type="priority_none" value="0">none</option>
                                <option data-type="priority_low" value="9">low</option>
                                <option data-type="priority_medium" value="5">medium</option>
                                <option data-type="priority_high" value="1">high</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="calendarLine">
                        <td class="label"><label data-type="event_calendar" for="event_calendar">calendar: </label></td>
                        <td colspan="2" data-size="full">
                            <select class="long R_calendar" name="calendar" id="event_calendar">
                                <option data-type="choose_calendar" value="choose">Select a calendar</option>
                            </select>
                        </td>
                    </tr>
                    <tr data-id="1">
                        <td class="label"><label data-type="alert" for="alert">alert: </label></td>
                        <td colspan="2" data-size="full">
                            <select class="long alert" name="alert_type" data-id="1" id="alert">
                                <option data-type="alert_none" value="none">none</option>
                                <option data-type="alert_message" value="message">message</option>
                            </select>
                        </td>
                    </tr>
                    <tr data-id="1" class="alert_details" style="display: none;">
                        <td class="label"></td>
                        <td colspan="2" data-size="full">
                            <select class="long alert_message_details" name="alert_details" data-id="1">
                                <option data-type="on_date" value="on_date">on date</option>
                                <option data-type="weeks_before" value="weeks_before">weeks before</option>
                                <option data-type="days_before" value="days_before">days before</option>
                                <option data-type="hours_before" value="hours_before">hours before</option>
                                <option data-type="minutes_before" value="minutes_before">minutes before</option>
                                <option data-type="seconds_before" value="seconds_before">seconds before</option>
                                <option data-type="weeks_after" value="weeks_after">weeks after</option>
                                <option data-type="days_after" value="days_after">days after</option>
                                <option data-type="hours_after" value="hours_after">hours after</option>
                                <option data-type="minutes_after" value="minutes_after">minutes after</option>
                                <option data-type="seconds_after" value="seconds_after">seconds after</option>
                            </select>
                        </td>
                    </tr>
                    <tr data-id="1" class="alert_message_date" style="display: none;">
                        <td class="label"></td>
                        <td>
                            <input class="small before_after_input" type="text" data-type="PH_before_after_alert" placeholder="Value" data-id="1" style="display: none;" />
                            <input class="date small message_date_input" type="text" data-type="PH_alarm_date" placeholder="Alarm date" data-id="1" />
                            <div class="invalidWrapper"><img data-type="invalidSmall" data-id="1" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                        </td>
                        <td>
                            <input class="time small message_time_input" type="text" data-type="PH_alarm_time" placeholder="Alarm time" data-id="1" />
                            <div class="invalidWrapper"><img data-type="invalidSmall" data-id="1" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                        </td>
                    </tr>
                    <tr id="url_tr">
                        <td class="label"><label data-type="txt_url_EVENT" for="url_EVENT">url:</label></td>
                        <td colspan="2"><input class="long" data-type="url_EVENT" type="text" placeholder="url" name="url_EVENT" id="url_EVENT" /></td>
                    </tr>
                    <tr id="note_tr">
                        <td class="label"><label data-type="note" for="note">note: </label></td>
                        <td colspan="2">
                            <textarea class="long" name="note" data-type="PH_note" placeholder="Note" id="note" rows="2" cols="20"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <input id="show" type="hidden" value=""/>
                            <input id="uid" type="hidden" value=""/>
                            <input id="etag" type="hidden" value=""/>
                            <input id="repeatCount" type="hidden" value=""/>
                            <input id="repeatEvent" type="hidden" value=""/>
                            <input id="recurrenceID" type="hidden" value=""/>
                            <input id="futureStart" type="hidden" value=""/>
                            <input id="vcalendarHash" type="hidden" value=""/>
                            <input id="vcalendarUID" type="hidden" value=""/>
                            <input id="saveButton" type="submit" value="Save" data-type="save" onclick="updateEventFormDimensions(true);$('#CAEvent .saveLoader').show();save();" />
                            <input id="editButton" type="button" value="Edit" data-type="edit" onclick="startEditModeEvent();" />
                            <input id="duplicateButton" type="button" value="Duplicate" data-type="duplicate" onclick="duplicateEvent('')" />
                            <input id="editOptionsButton" type="button" value="edit repeat" data-type="editOptions" />
                            <input id="resetButton" type="button" value="Reset" data-type="reset" />
                            <input id="closeButton" type="button" value="Cancel" data-type="cancel" />
                            <input id="deleteButton" type="button" value="Delete" data-type="delete" onclick="updateEventFormDimensions(true);$('#CAEvent .saveLoader').show();deleteEvent();" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="System" id="SystemCalDavTODO">
    <div class="update_d" style="display: none;">
        <div class="update_h"></div>
    </div>
    <div class="headers" id="resourceCalDAVTODO_h">
        <span class="resourceCalDAVTODO_text" data-type="resourcesCalDAV_txt">Resources</span>
        <img src="images/add_cal_white.svg" alt="Enable all calendars" title="Enable all calendars" data-type="addAll" class="addRemoveAll addRemoveAllCalDAVTODO" onclick="enableAllTodo()" />
        <img src="images/remove_cal_white.svg" alt="Disable all calendars" title="Disable all calendars" data-type="removeAll" class="addRemoveAll addRemoveAllCalDAVTODO" onclick="disableAllTodo()" />
        <img id="showUnloadedCalendarsTODO" src="images/delegation.svg" alt="Subscribe" title="Subscribe" onclick="showUnloadedCollections('todo');" />
        <input id="loadUnloadedCalendarsTODO" type="button" value="save" onclick="loadAdditionalCollections('todo');" style="margin-top:4px;margin-left:6px;" />
        <input id="loadUnloadedCalendarsTODOCancel" type="button" value="cancel" onclick="cancelUnloadedCollections('todo');" style="margin-top:4px;margin-right:6px;float:right;" />
    </div>
    <div id="ResourceCalDAVTODOList">
        <div id="ResourceCalDAVTODOListTemplate" style="display: none;">
            <div class="resourceCalDAVTODO_header"><input type="checkbox"></div>
            <div class="resourceCalDAVTODO_item"></div>
        </div>
    </div>
    <div id="timezoneWrapperTODO">
        <label data-type="txt_timezonePicker" for="timezonePickerTODO">Timezone:</label>
        <div id="timezoneTodoSelectDiv">
            <select id="timezonePickerTODO" name="timezonePickerTODO" data-type="timezonesPicker"></select>
        </div>
    </div>
    <div class="headers" id="main_h_TODO">
        <input id="ResourceCalDAVTODOToggle" type="image" src="images/resources.svg" alt="Show/Hide Resources" />
        <div id="mainTODO_h_placeholder"></div>
        <img id="eventFormShowerTODO" src="images/new_item.svg" alt="Add todo" />
    </div>
    <div id="searchFormTODO">
        <img alt="Search Form" src="images/search.svg" style="position: inline; margin-top: 4px; margin-left: 8px; vertical-align: top;" />
        <div class="searchContainer">
            <input type="text" value="" placeholder="Search" data-type="PH_CalDAVTODOsearch" id="searchInputTODO" style="margin-top: 3px; vertical-align: top;" />
        </div>
        <img alt="Search Reset" id="resetButtonTODO" onclick="$('#searchInputTODO').val('');$('#searchInputTODO').keyup();" src="images/reset_b.svg" style="position: absolute; margin-top: 5px; right: 9px; vertical-align: top; cursor: pointer; visibility: hidden;" />
    </div>
    <div id="TodoDisabler"></div>
    <div id="CalendarLoaderTODO">
        <div class="loaderInfo">Calendaring ...</div>
        <div class="loader"></div>
    </div>
    <div id='mainTODO'>
        <div id='todoList'></div>
    </div>
    <div class="headers" id="todoForm_h">
        <span class="resourceCalDAV_text" data-type="todo_txt">Todo</span>
    </div>
    <div id="todoLoader">
        <div class="saveLoader">
            <div class="saveLoaderInfo"></div>
            <div class="loader"></div>
        </div>
    </div>
    <div id="todoColor"></div>
    <div id="todoForm">
        <div id="CATodo">
            <div id="repeatConfirmBoxTODO">
                <div id="repeatConfirmBoxContentTODO"></div>
                <div id="repeatConfirmBoxQuestionTODO"></div>
                <input id='editAllTODO' type="button" value="All events" /><br />
                <input id='editFutureTODO' type="button" value="All future events" /><br />
                <input id='editOnlyOneTODO' type="button" value="This event only"/><br />
            </div>
            <div id="todo_details_template">
                <div id="todoDetailsContainer">
                    <table id="todoDetailsTable">
                        <tr>
                            <th colspan="3" class="headerContainer">
                                <div class="formNav prev top" title="show previous todo" data-type="todo_prev_nav"><img src="images/arrow_prev.svg" alt="todo prev"/></div>
                                <div class="formNav prev bottom" title="show previous uncompleted todo" data-type="todo_prev_uncompleted_nav"><img src="images/arrow_prev_red.svg" alt="todo prev incomplete"/></div>
                                <textarea class="header" data-type="name_TODO" placeholder="Name" name="name" id="nameTODO"></textarea>
                                <div class="formNav next top" title="show next todo" data-type="todo_next_nav"><img src="images/arrow_next.svg" alt="todo next"/></div>
                                <div class="formNav next bottom" title="show next uncompleted todo" data-type="todo_next_uncompleted_nav"><img src="images/arrow_next_red.svg" alt="todo next incomplete"/></div>
                            </th>
                        </tr>
                        <tr id="location_row_TODO">
                            <td class="label"><label data-type="location" for="location_TODO">location:</label></td>
                            <td colspan="2"><input class="long" data-type="PH_location" type="text" placeholder="Location" name="location_TODO" id="location_TODO" /></td>
                        </tr>
                        <tr>
                            <td class="label"><label data-type="type_TODO" for="todo_type">type: </label></td>
                            <td colspan="2" data-size="full">
                                <select class="long" name="todo_type" id="todo_type">
                                    <option data-type="todo_type_none" value="none">Simple</option>
                                    <option data-type="todo_type_start" value="start">With start time</option>
                                    <option data-type="todo_type_due" value="due">With due time</option>
                                    <option data-type="todo_type_both" value="both">With both start and due times</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="dateTrFromTODO">
                            <td class="label"><label data-type="date_from_TODO" for="date_fromTODO">fom: </label></td>
                            <td>
                                <input class="date small" data-type="PH_date_from_TODO" type="text" placeholder="Date from" id="date_fromTODO" name="date_fromTODO" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                            <td>
                                <input class="time small" data-type="PH_time_from_TODO" type="text" placeholder="Time from" id="time_fromTODO" name="time_fromTODO" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                        </tr>
                        <tr class="dateTrToTODO">
                            <td class="label"><label data-type="date_to_TODO" for="date_toTODO">to: </label></td>
                            <td>
                                <input class="date small" data-type="PH_date_to_TODO" type="text" placeholder="Date to" id="date_toTODO" name="date_toTODO" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                            <td>
                                <input class="time small" data-type="PH_time_to_TODO" type="text" placeholder="Time to" id="time_toTODO" name="time_toTODO" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                        </tr>
                        <tr class="timezone_rowTODO">
                            <td class="label"><label data-type="txt_timezoneTODO" for="timezoneTODO">timezone: </label></td>
                            <td colspan="2" data-size="full">
                                <select class="long" data-type="timezonesTODO" name="timezoneTODO" id="timezoneTODO"></select>
                            </td>
                        </tr>
                        <tr id="percent_complete_TODO">
                            <td class="label"><label data-type="percent_complete_TODO" for="percenteCompleteValue">Complete </label></td>
                            <td colspan="2">
                                <div style="float: left; width: 203px; margin-left: 7px; margin-top: 3px; margin-bottom: 3px;'" id="percentageSlider"></div>
                                <input type="text" class="verySmall" style="margin-left: 13px; float: left;" id="percenteCompleteValue"/>
                                <div class="invalidWrapper"><img data-type="invalidVerySmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                                <label style="margin-left: -14px; float: left;">%</label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><label data-type="status_TODO" for="statusTODO">status: </label></td>
                            <td colspan="2" data-size="full">
                                <select class="long" name="statusTODO" id="statusTODO">
                                    <option data-type="STATUS_NEEDS-ACTION_TODO" value="NEEDS-ACTION">needs action</option>
                                    <option data-type="STATUS_IN-PROCESS_TODO" value="IN-PROCESS">in progress</option>
                                    <option data-type="STATUS_COMPLETED_TODO" value="COMPLETED">completed</option>
                                    <option data-type="STATUS_CANCELLED_TODO" value="CANCELLED">cancelled</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="completedOnTr" style="display: none;">
                            <td class="label"><label data-type="PH_completedOn" for="completedOnDate">completed on: </label></td>
                            <td>
                                <input class="date small" data-type="PH_completedOnDate" type="text" placeholder="Completed on date" id="completedOnDate" name="completedOnDate" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                            <td>
                                <input class="time small" data-type="PH_completedOnTime" type="text" placeholder="Completed on time" id="completedOnTime" name="completedOnTime" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                        </tr>
                        <tr id="repeat_row_TODO">
                            <td class="label"><label data-type="repeat" for="repeat">repeat: </label></td>
                            <td colspan="2" data-size="full">
                                <select class="long" name="repeat_TODO" id="repeat_TODO">
                                    <option data-type="repeat_no-repeat" value="no-repeat">No repeat</option>
                                    <option data-type="repeat_DAILY" value="DAILY">Daily</option>
                                    <option data-type="repeat_BUSINESS" value="BUSINESS">Every business day</option>
                                    <option data-type="repeat_WEEKEND" value="WEEKEND">Every weekend</option>
                                    <option data-type="repeat_WEEKLY" value="WEEKLY">Weekly</option>
                                    <option data-type="repeat_TWO_WEEKLY" value="TWO_WEEKLY">Bi-weekly</option>
                                    <option data-type="repeat_MONTHLY" value="MONTHLY">Monthly</option>
                                    <option data-type="repeat_YEARLY" value="YEARLY">Yearly</option>
                                    <option data-type="repeat_CUSTOM_WEEKLY" value="CUSTOM_WEEKLY">Custom weekly</option>
                                    <option data-type="repeat_CUSTOM_MONTHLY" value="CUSTOM_MONTHLY">Custom monthly</option>
                                    <option data-type="repeat_CUSTOM_YEARLY" value="CUSTOM_YEARLY">Custom yearly</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="repeat_interval_TODO" style="display: none;">
                            <td class="label repeat"><label data-type="repeat_type" for="repeat_interval_detail_TODO">Every </label></td>
                            <td style="position: relative;">
                                <input class="small" type="text" data-type="PH_type_Interval" placeholder="Interval" name="end" id="repeat_interval_detail_TODO" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                            <td>
                                <span class="infoSpan" style="padding-left: 4px;" data-type="txt_interval">days</span>
                            </td>
                        </tr>
                        <tr id="week_custom_TODO" style="display: none;">
                            <td class="label repeat"><label data-type="week_custom_txt">On </label></td>
                            <td colspan="2">
                                <table class="customTable customTableWeek">
                                    <tr>
                                        <td data-type="0" data-text="SU" class="firstCol">Su</td>
                                        <td data-type="1" data-text="MO">Mo</td>
                                        <td data-type="2" data-text="TU">Tu</td>
                                        <td data-type="3" data-text="WE">We</td>
                                        <td data-type="4" data-text="TH">Th</td>
                                        <td data-type="5" data-text="FR">Fr</td>
                                        <td data-type="6" data-text="SA" class="lastCol">Sa</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr id="month_custom1_TODO" style="display: none;">
                            <td class="label"><label data-type="month_custom1_txt"></label></td>
                            <td data-size="half">
                                <select id="repeat_month_custom_select_TODO" class="small">
                                    <option value="every" data-type="month_custom_every">Every</option>
                                    <option value="first" data-type="month_custom_first">First</option>
                                    <option value="second" data-type="month_custom_second">Second</option>
                                    <option value="third" data-type="month_custom_third">Third</option>
                                    <option value="fourth" data-type="month_custom_fourth">Fourth</option>
                                    <option value="fifth" data-type="month_custom_fifth">Fifth</option>
                                    <option value="last" data-type="month_custom_last">Last</option>
                                    <option value="custom" data-type="month_custom_custom">Custom</option>
                                </select>
                            </td>
                            <td style="position: relative;" data-size="half">
                                <select id="repeat_month_custom_select2_TODO" class="small">
                                    <option value="SU" data-type="0">Sunday</option>
                                    <option value="MO" data-type="1">Monday</option>
                                    <option value="TU" data-type="2">Tuesday</option>
                                    <option value="WE" data-type="3">Wednesday</option>
                                    <option value="TH" data-type="4">Thursday</option>
                                    <option value="FR" data-type="5">Friday</option>
                                    <option value="SA" data-type="6">Saturday</option>
                                    <option value="DAY" data-type="month_custom_month">Day of the month</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="month_custom2_TODO" style="display: none;">
                            <td class="label repeat"><label data-type="month_custom2_txt">On days</label></td>
                            <td colspan="2">
                                <table class="customTable customTableMonth">
                                    <tr>
                                        <td data-type="1" class="firstCol">1</td>
                                        <td data-type="2">2</td>
                                        <td data-type="3">3</td>
                                        <td data-type="4">4</td>
                                        <td data-type="5">5</td>
                                        <td data-type="6">6</td>
                                        <td data-type="7" class="lastCol">7</td>
                                    </tr>
                                    <tr>
                                        <td data-type="8" class="firstCol">8</td>
                                        <td data-type="9">9</td>
                                        <td data-type="10">10</td>
                                        <td data-type="11">11</td>
                                        <td data-type="12">12</td>
                                        <td data-type="13">13</td>
                                        <td data-type="14" class="lastCol">14</td>
                                    </tr>
                                    <tr>
                                        <td data-type="15" class="firstCol">15</td>
                                        <td data-type="16">16</td>
                                        <td data-type="17">17</td>
                                        <td data-type="18">18</td>
                                        <td data-type="19">19</td>
                                        <td data-type="20">20</td>
                                        <td data-type="21" class="lastCol">21</td>
                                    </tr>
                                    <tr>
                                        <td data-type="22" class="firstCol">22</td>
                                        <td data-type="23">23</td>
                                        <td data-type="24">24</td>
                                        <td data-type="25">25</td>
                                        <td data-type="26">26</td>
                                        <td data-type="27">27</td>
                                        <td data-type="28" class="lastCol">28</td>
                                    </tr>
                                    <tr>
                                        <td data-type="29" class="firstCol">29</td>
                                        <td data-type="30">30</td>
                                        <td data-type="31">31</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr id="year_custom2_TODO" style="display: none;">
                            <td class="label"><label data-type="year_custom2"></label></td>
                            <td data-size="half">
                                <select id="repeat_year_custom_select1_TODO" class="small">
                                    <option value="every" data-type="year_custom_every">Every</option>
                                    <option value="first" data-type="year_custom_first">First</option>
                                    <option value="second" data-type="year_custom_second">Second</option>
                                    <option value="third" data-type="year_custom_third">Third</option>
                                    <option value="fourth" data-type="year_custom_fourth">Fourth</option>
                                    <option value="fifth" data-type="year_custom_fifth">Fifth</option>
                                    <option value="last" data-type="year_custom_last">Last</option>
                                    <option value="custom" data-type="year_custom_custom">Custom</option>
                                </select>
                            </td>
                            <td style="position: relative;" data-size="half">
                                <select id="repeat_year_custom_select2_TODO" class="small">
                                    <option value="SU" data-type="0">Sunday</option>
                                    <option value="MO" data-type="1">Monday</option>
                                    <option value="TU" data-type="2">Tuesday</option>
                                    <option value="WE" data-type="3">Wednesday</option>
                                    <option value="TH" data-type="4">Thursday</option>
                                    <option value="FR" data-type="5">Friday</option>
                                    <option value="SA" data-type="6">Saturday</option>
                                    <option value="DAY" data-type="year_custom_month">Day of the month</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="year_custom1_TODO" style="display: none;">
                            <td class="label repeat"><label data-type="year_custom1">Every</label></td>
                            <td colspan="2">
                                <table class="customTable customTableMonth">
                                    <tr>
                                        <td data-type="1" class="firstCol">1</td>
                                        <td data-type="2">2</td>
                                        <td data-type="3">3</td>
                                        <td data-type="4">4</td>
                                        <td data-type="5">5</td>
                                        <td data-type="6">6</td>
                                        <td data-type="7" class="lastCol">7</td>
                                    </tr>
                                    <tr>
                                        <td data-type="8" class="firstCol">8</td>
                                        <td data-type="9">9</td>
                                        <td data-type="10">10</td>
                                        <td data-type="11">11</td>
                                        <td data-type="12">12</td>
                                        <td data-type="13">13</td>
                                        <td data-type="14" class="lastCol">14</td>
                                    </tr>
                                    <tr>
                                        <td data-type="15" class="firstCol">15</td>
                                        <td data-type="16">16</td>
                                        <td data-type="17">17</td>
                                        <td data-type="18">18</td>
                                        <td data-type="19">19</td>
                                        <td data-type="20">20</td>
                                        <td data-type="21" class="lastCol">21</td>
                                    </tr>
                                    <tr>
                                        <td data-type="22" class="firstCol">22</td>
                                        <td data-type="23">23</td>
                                        <td data-type="24">24</td>
                                        <td data-type="25">25</td>
                                        <td data-type="26">26</td>
                                        <td data-type="27">27</td>
                                        <td data-type="28" class="lastCol">28</td>
                                    </tr>
                                    <tr>
                                        <td data-type="29" class="firstCol">29</td>
                                        <td data-type="30">30</td>
                                        <td data-type="31">31</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr id="year_custom3_TODO" style="display: none;">
                            <td class="label repeat"><label data-type="year_custom3">Of</label></td>
                            <td colspan="2">
                                <table class="customTable customTableYear">
                                    <tr>
                                        <td data-type="0" data-text="jan" class="firstCol">Jan</td>
                                        <td data-type="1" data-text="feb">Feb</td>
                                        <td data-type="2" data-text="mar">Mar</td>
                                        <td data-type="3" data-text="apr" class="lastCol">Apr</td>
                                    </tr>
                                    <tr>
                                        <td data-type="4" data-text="may" class="firstCol">May</td>
                                        <td data-type="5" data-text="jun">Jun</td>
                                        <td data-type="6" data-text="jul">Jul</td>
                                        <td data-type="7" data-text="aug" class="lastCol">Aug</td>
                                    </tr>
                                    <tr>
                                        <td data-type="8" data-text="sep" class="firstCol">Sep</td>
                                        <td data-type="9" data-text="oct">Oct</td>
                                        <td data-type="10" data-text="nov">Nov</td>
                                        <td data-type="11" data-text="dec" class="lastCol">Dec</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr id="repeat_details_TODO" style="display: none;">
                            <td class="label repeat"><label data-type="repeat_end"  for="repeat_end_details_TODO">end: </label></td>
                            <td data-size="half">
                                <select class="small" id="repeat_end_details_TODO">
                                    <option data-type="repeat_details_on_date" value="on_date">on date</option>
                                    <option data-type="repeat_details_after" value="after">occurrences</option>
                                    <option data-type="repeat_details_never" value="never">never</option>
                                </select>
                            </td>
                            <td style="position: relative;">
                                <input class="date small" type="text" data-type="PH_until_date" placeholder="Date until" name="end" id="repeat_end_date_TODO" />
                                <input style="display: none;" class="small" type="text" data-type="PH_repeat_count" placeholder="Repeat count" name="end" id="repeat_end_after_TODO" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                        </tr>
                        <tr class="row_typeTODO">
                            <td class="label"><label data-type="txt_typeTODO" for="typeTODO">privacy: </label></td>
                            <td colspan="2" data-size="full">
                                <select class="long" data-type="typeTODO" name="typeTODO" id="typeTODO">
                                    <option data-type="PUBLIC_TYPE_TODO" value="public">public</option>
                                    <option data-type="CONFIDENTIAL_TYPE_TODO" value="confidential">confidential</option>
                                    <option data-type="PRIVATE_TYPE_TODO" value="private">private</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="label"><label data-type="priority_TODO" for="priority_TODO">priority: </label></td>
                            <td colspan="2" data-size="full">
                                <select class="long " name="priority_TODO" id="priority_TODO">
                                    <option data-type="priority_TODO_none" value="0">none</option>
                                    <option data-type="priority_TODO_low" value="9">low</option>
                                    <option data-type="priority_TODO_medium" value="5">medium</option>
                                    <option data-type="priority_TODO_high" value="1">high</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="calendarLineTODO">
                            <td class="label"><label data-type="calendar_TODO" for="todo_calendar">calendar: </label></td>
                            <td colspan="2" data-size="full">
                                <select class="long R_calendar" name="calendar" id="todo_calendar">
                                    <option data-type="choose_calendar_TODO" value="choose">Select a calendar</option>
                                </select>
                            </td>
                        </tr>
                        <tr data-id="1">
                            <td class="label"><label data-type="alert_TODO" for="alertTODO">alert: </label></td>
                            <td colspan="2" data-size="full">
                                <select class="long alertTODO" name="alert_typeTODO" data-id="1" id="alertTODO">
                                    <option data-type="alert_none_TODO" value="none">none</option>
                                    <option data-type="alert_message_TODO" value="message">message</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="alert_detailsTODO" style="display: none;" data-id="1">
                            <td class="label"></td>
                            <td colspan="2" data-size="full">
                                <select class="long alert_message_detailsTODO" name="alert_detailsTODO" data-id="1">
                                    <option data-type="on_dateTODO" value="on_date">on date</option>
                                    <option data-type="weeks_beforeTODO" value="weeks_before">weeks before</option>
                                    <option data-type="days_beforeTODO" value="days_before">days before</option>
                                    <option data-type="hours_beforeTODO" value="hours_before">hours before</option>
                                    <option data-type="minutes_beforeTODO" value="minutes_before">minutes before</option>
                                    <option data-type="seconds_beforeTODO" value="seconds_before">seconds before</option>
                                    <option data-type="weeks_afterTODO" value="weeks_after">weeks after</option>
                                    <option data-type="days_afterTODO" value="days_after">days after</option>
                                    <option data-type="hours_afterTODO" value="hours_after">hours after</option>
                                    <option data-type="minutes_afterTODO" value="minutes_after">minutes after</option>
                                    <option data-type="seconds_afterTODO" value="seconds_after">seconds after</option>
                                </select>
                            </td>
                        </tr>
                        <tr data-id="1" class="alert_message_dateTODO" style="display: none;">
                            <td class="label"></td>
                            <td>
                                <input data-id="1" class="small before_after_inputTODO" type="text" data-type="PH_before_after_alert_TODO" placeholder="Value" style="display: none;" />
                                <input data-id="1" class="date small message_date_inputTODO" type="text" data-type="PH_alarm_date_TODO" placeholder="Alarm Date" name="message_dateTODO" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" data-id="1" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                            <td>
                                <input data-id="1" class="time small message_time_inputTODO" type="text" data-type="PH_alarm_time_TODO" placeholder="Alarm time" name="message_timeTODO" />
                                <div class="invalidWrapper"><img data-type="invalidSmall" style="display: none;" src="images/error_b.svg" alt="invalid" /></div>
                            </td>
                        </tr>
                        <tr id="url_trTODO">
                            <td class="label"><label data-type="txt_url_TODO" for="url_TODO">url:</label></td>
                            <td colspan="2"><input class="long" data-type="url_TODO" type="text" placeholder="url" name="url_TODO" id="url_TODO" /></td>
                        </tr>
                        <tr id="note_trTODO">
                            <td class="label"><label data-type="note_TODO" for="noteTODO">note: </label></td>
                            <td colspan="2">
                                <textarea class="long" name="noteTODO" data-type="PH_note_TODO" placeholder="Note" id="noteTODO" rows="2" cols="20"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <input id="todoInEdit" type="hidden" value="false"/>
                                <input id="showTODO" type="hidden" value=""/>
                                <input id="uidTODO" type="hidden" value=""/>
                                <input id="repeatCountTODO" type="hidden" value=""/>
                                <input id="repeatTodo" type="hidden" value=""/>
                                <input id="recurrenceIDTODO" type="hidden" value=""/>
                                <input id="futureStartTODO" type="hidden" value=""/>
                                <input id="vcalendarHashTODO" type="hidden" value=""/>
                                <input id="vcalendarUIDTODO" type="hidden" value=""/>
                                <input id="etagTODO" type="hidden" value=""/>
                                <input id="saveTODO" type="submit" value="Save" onclick="$('#todoInEdit').val('false');updateTodoFormDimensions(true);$('#todoLoader').show();saveTodo();" />
                                <input id="editTODO" type="button" value="Edit" onclick="startEditModeTodo();"/>
                                <input id="duplicateTODO" type="button" value="Duplicate" data-type="duplicate" onclick="duplicateEvent('TODO')" />
                                <input id="editOptionsButtonTODO" type="button" value="edit repeat" data-type="editOptionsTODO" />
                                <input id="resetTODO" type="button" value="Reset" />
                                <input id="closeTODO" type="button" value="Cancel" />
                                <input id="deleteTODO" data-type="delete" type="button" value="Delete" onclick="$('#todoInEdit').val('false');updateTodoFormDimensions(true);$('#todoLoader').show();deleteTodo();" />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

