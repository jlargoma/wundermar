<style type="text/css">
    .not-padding {
        padding: 0;
    }

    .min-h50 {
        min-height: 40px;
    }

    .Alta {
        background-color: #f0513c;
        color: white;
    }

    .Media {
        background-color: #127bbd;
        color: white;

    }

    .Baja {
        background-color: #91b85d;
        color: white;

    }

    .sub {
        background-color: orange;
        min-width: 33.33%;
        min-height: 20px !important;

    }

    .end, .start {
        opacity: 0.50;
    }

    .fc-day.fc-sun.fc-widget-content.fc-other-month.fc-first {
        cursor: pointer;
    }

    body {
        padding-top: 20px;
    }

    /**** FULL CALENDAR ****/

    /* Header
    ------------------------------------------------------------------------*/
    .fc-header td {
        white-space: nowrap;
    }

    .fc-header-left {
        width: 25%;
        text-align: left;
    }

    .fc-header-center {
        text-align: center;
    }

    .fc-header-right {
        width: 25%;
        text-align: right;
    }

    .fc-header-title {
        display: inline-block;
        vertical-align: top;
    }

    .fc-header-title h2 {
        margin-top: 0;
        white-space: nowrap;
    }

    .fc .fc-header-space {
        padding-left: 10px;
    }

    .fc-header .fc-button {
        margin-bottom: 1em;
        vertical-align: top;
    }

    /* buttons edges butting together */
    .fc-header .fc-button {
        margin-right: -1px;
    }

    .fc-header .fc-corner-right,
        /* non-theme */
    .fc-header .ui-corner-right {
        /* theme */
        margin-right: 0;
        /* back to normal */
    }

    /* button layering (for border precedence) */
    .fc-header .fc-state-hover, .fc-header .ui-state-hover {
        z-index: 2;
    }

    .fc-header .fc-state-down {
        z-index: 3;
    }

    .fc-header .fc-state-active, .fc-header .ui-state-active {
        z-index: 4;
    }

    /* Content
    ------------------------------------------------------------------------*/
    .fc-content {
        clear: both;
    }

    .fc-view {
        width: 100%;
        /* needed for view switching (when view is absolute) */
        overflow: hidden;
    }

    /* Cell Styles
    ------------------------------------------------------------------------*/
    .fc-widget-header,
        /* <th>, usually */
    .fc-widget-content {
        /* <td>, usually */
        border: 1px solid #ddd;
    }

    .fc-state-highlight {
        /* <td> today cell */
        /* TODO: add .fc-today to <th> */
        background: #fcf8e3;
    }

    .fc-cell-overlay {
        /* semi-transparent rectangle while dragging */
        background: #bce8f1;
        opacity: .3;
        filter: alpha(opacity=30);
        /* for IE */
    }

    /* Buttons
    ------------------------------------------------------------------------*/
    .fc-button {
        position: relative;
        display: inline-block;
        padding: 0 .6em;
        overflow: hidden;
        height: 1.9em;
        line-height: 1.9em;
        white-space: nowrap;
        cursor: pointer;
    }

    .fc-state-default {
        /* non-theme */
        border: 1px solid;
    }

    .fc-state-default.fc-corner-left {
        /* non-theme */
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .fc-state-default.fc-corner-right {
        /* non-theme */
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    /*
        Our default prev/next buttons use HTML entities like ‹ › « »
        and we'll try to make them look good cross-browser.
    */
    .fc-text-arrow {
        margin: 0 .1em;
        font-size: 2em;
        font-family: "Courier New", Courier, monospace;
        vertical-align: baseline;
        /* for IE7 */
    }

    .fc-button-prev .fc-text-arrow, .fc-button-next .fc-text-arrow {
        /* for ‹ › */
        font-weight: bold;
    }

    /* icon (for jquery ui) */
    .fc-button .fc-icon-wrap {
        position: relative;
        float: left;
        top: 50%;
    }

    .fc-button .ui-icon {
        position: relative;
        float: left;
        margin-top: -50%;
        *margin-top: 0;
        *top: -50%;
    }

    /*
      button states
      borrowed from twitter bootstrap (http://twitter.github.com/bootstrap/)
    */
    .fc-state-default {
        background-color: #f5f5f5;
        background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
        background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
        background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
        background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
        background-repeat: repeat-x;
        border-color: #e6e6e6 #e6e6e6 #bfbfbf;
        border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
        color: #333;
        text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .fc-state-hover, .fc-state-down, .fc-state-active, .fc-state-disabled {
        color: #333333;
        background-color: #e6e6e6;
    }

    .fc-state-hover {
        color: #333333;
        text-decoration: none;
        background-position: 0 -15px;
        -webkit-transition: background-position 0.1s linear;
        -moz-transition: background-position 0.1s linear;
        -o-transition: background-position 0.1s linear;
        transition: background-position 0.1s linear;
    }

    .fc-state-down, .fc-state-active {
        background-color: #cccccc;
        background-image: none;
        outline: 0;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .fc-state-disabled {
        cursor: default;
        background-image: none;
        opacity: 0.65;
        filter: alpha(opacity=65);
        box-shadow: none;
    }

    /* Global Event Styles
    ------------------------------------------------------------------------*/
    .fc-event {
        border: 1px solid #3a87ad;
        /* default BORDER color */
        background-color: #3a87ad;
        /* default BACKGROUND color */
        color: #fff;
        /* default TEXT color */
        font-size: .85em;
        cursor: default;
    }

    a.fc-event {
        text-decoration: none;
    }

    a.fc-event, .fc-event-draggable {
        cursor: pointer;
    }

    .fc-rtl .fc-event {
        text-align: right;
    }

    .fc-event-inner {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .fc-event-time, .fc-event-title {
        padding: 0 1px;
    }

    .fc .ui-resizable-handle {
        display: block;
        position: absolute;
        z-index: 99999;
        overflow: hidden;
        /* hacky spaces (IE6/7) */
        font-size: 300%;
        /* */
        line-height: 50%;
        /* */
    }

    /* Horizontal Events
    ------------------------------------------------------------------------*/
    .fc-event-hori {
        border-width: 1px 0;
        margin-bottom: 1px;
    }

    .fc-ltr .fc-event-hori.fc-event-start, .fc-rtl .fc-event-hori.fc-event-end {
        border-left-width: 1px;
        border-top-left-radius: 3px;
        border-bottom-left-radius: 3px;
    }

    .fc-ltr .fc-event-hori.fc-event-end, .fc-rtl .fc-event-hori.fc-event-start {
        border-right-width: 1px;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
    }

    /* resizable */
    .fc-event-hori .ui-resizable-e {
        top: 0 !important;
        /* importants override pre jquery ui 1.7 styles */
        right: -3px !important;
        width: 7px !important;
        height: 100% !important;
        cursor: e-resize;
    }

    .fc-event-hori .ui-resizable-w {
        top: 0 !important;
        left: -3px !important;
        width: 7px !important;
        height: 100% !important;
        cursor: w-resize;
    }

    .fc-event-hori .ui-resizable-handle {
        _padding-bottom: 14px;
        /* IE6 had 0 height */
    }

    /* Reusable Separate-border Table
    ------------------------------------------------------------*/
    table.fc-border-separate {
        border-collapse: separate;
    }

    .fc-border-separate th, .fc-border-separate td {
        border-width: 1px 0 0 1px;
    }

    .fc-border-separate th.fc-last, .fc-border-separate td.fc-last {
        border-right-width: 1px;
    }

    .fc-border-separate tr.fc-last th, .fc-border-separate tr.fc-last td {
        border-bottom-width: 1px;
    }

    .fc-border-separate tbody tr.fc-first td, .fc-border-separate tbody tr.fc-first th {
        border-top-width: 0;
    }

    /* Month View, Basic Week View, Basic Day View
    ------------------------------------------------------------------------*/
    .fc-grid th {
        text-align: center;
    }

    .fc .fc-week-number {
        width: 22px;
        text-align: center;
    }

    .fc .fc-week-number div {
        padding: 0 2px;
    }

    .fc-grid .fc-day-number {
        float: right;
        padding: 0 2px;
    }

    .fc-grid .fc-other-month .fc-day-number {
        opacity: 0.3;
        filter: alpha(opacity=30);
        /* for IE */
        /* opacity with small font can sometimes look too faded
           might want to set the 'color' property instead
           making day-numbers bold also fixes the problem */
    }

    .fc-grid .fc-day-content {
        clear: both;
        padding: 2px 2px 1px;
        /* distance between events and day edges */
    }

    /* event styles */
    .fc-grid .fc-event-time {
        font-weight: bold;
    }

    /* right-to-left */
    .fc-rtl .fc-grid .fc-day-number {
        float: left;
    }

    .fc-rtl .fc-grid .fc-event-time {
        float: right;
    }

    /* Agenda Week View, Agenda Day View
    ------------------------------------------------------------------------*/
    .fc-agenda table {
        border-collapse: separate;
    }

    .fc-agenda-days th {
        text-align: center;
    }

    .fc-agenda .fc-agenda-axis {
        width: 50px;
        padding: 0 4px;
        vertical-align: middle;
        text-align: right;
        white-space: nowrap;
        font-weight: normal;
    }

    .fc-agenda .fc-week-number {
        font-weight: bold;
    }

    .fc-agenda .fc-day-content {
        padding: 2px 2px 1px;
    }

    /* make axis border take precedence */
    .fc-agenda-days .fc-agenda-axis {
        border-right-width: 1px;
    }

    .fc-agenda-days .fc-col0 {
        border-left-width: 0;
    }

    /* all-day area */
    .fc-agenda-allday th {
        border-width: 0 1px;
    }

    .fc-agenda-allday .fc-day-content {
        min-height: 34px;
        /* TODO: doesnt work well in quirksmode */
        _height: 34px;
    }

    /* divider (between all-day and slots) */
    .fc-agenda-divider-inner {
        height: 2px;
        overflow: hidden;
    }

    .fc-widget-header .fc-agenda-divider-inner {
        background: #eee;
    }

    /* slot rows */
    .fc-agenda-slots th {
        border-width: 1px 1px 0;
    }

    .fc-agenda-slots td {
        border-width: 1px 0 0;
        background: none;
    }

    .fc-agenda-slots td div {
        height: 20px;
    }

    .fc-agenda-slots tr.fc-slot0 th, .fc-agenda-slots tr.fc-slot0 td {
        border-top-width: 0;
    }

    .fc-agenda-slots tr.fc-minor th, .fc-agenda-slots tr.fc-minor td {
        border-top-style: dotted;
    }

    .fc-agenda-slots tr.fc-minor th.ui-widget-header {
        *border-top-style: solid;
        /* doesn't work with background in IE6/7 */
    }

    /* Vertical Events
    ------------------------------------------------------------------------*/
    .fc-event-vert {
        border-width: 0 1px;
    }

    .fc-event-vert.fc-event-start {
        border-top-width: 1px;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }

    .fc-event-vert.fc-event-end {
        border-bottom-width: 1px;
        border-bottom-left-radius: 3px;
        border-bottom-right-radius: 3px;
    }

    .fc-event-vert .fc-event-time {
        white-space: nowrap;
        font-size: 10px;
    }

    .fc-event-vert .fc-event-inner {
        position: relative;
        z-index: 2;
    }

    .fc-event-vert .fc-event-bg {
        /* makes the event lighter w/ a semi-transparent overlay  */
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        opacity: .25;
        filter: alpha(opacity=25);
    }

    .fc .ui-draggable-dragging .fc-event-bg,
        /* TODO: something nicer like .fc-opacity */
    .fc-select-helper .fc-event-bg {
        display: none \9;
        /* for IE6/7/8. nested opacity filters while dragging don't work */
    }

    /* resizable */
    .fc-event-vert .ui-resizable-s {
        bottom: 0 !important;
        /* importants override pre jquery ui 1.7 styles */
        width: 100% !important;
        height: 8px !important;
        overflow: hidden !important;
        line-height: 8px !important;
        font-size: 11px !important;
        font-family: monospace;
        text-align: center;
        cursor: s-resize;
    }

    .fc-agenda .ui-resizable-resizing {
        /* TODO: better selector */
        _overflow: hidden;
    }

    /* CUSTOM */
    body .calendar {
        margin-bottom: 20px;
    }

    body .calendar .fc-header {
        margin-bottom: 10px;
    }

    body .calendar .fc-header .fc-button-effect {
        display: none;
    }

    body .calendar .fc-header .fc-header-left .fc-button, body .calendar .fc-header .fc-header-right .fc-button {
        background: none;
        border: none;
    }

    body .calendar .fc-header .fc-header-left .fc-button.fc-button-prev, body .calendar .fc-header .fc-header-right .fc-button.fc-button-prev {
        background: center center !important;
        text-indent: -9999px;
        width: 36px;
        height: 24px;
        padding: 0;
        opacity: 0.3;
        filter: alpha(opacity=30);
    }

    body .calendar .fc-header .fc-header-left .fc-button.fc-button-prev:hover, body .calendar .fc-header .fc-header-right .fc-button.fc-button-prev:hover {
        opacity: 1;
        filter: alpha(opacity=100);
    }

    body .calendar .fc-header .fc-header-left .fc-button.fc-button-next, body .calendar .fc-header .fc-header-right .fc-button.fc-button-next {
        text-indent: -9999px;
        background: url(../../img/styler/arrow-right.png) center center !important;
        width: 36px;
        height: 24px;
        padding: 0;
        opacity: 0.3;
        filter: alpha(opacity=30);
    }

    body .calendar .fc-header .fc-header-left .fc-button.fc-button-next:hover, body .calendar .fc-header .fc-header-right .fc-button.fc-button-next:hover {
        opacity: 1;
        filter: alpha(opacity=100);
    }

    body .calendar .fc-header .fc-header-left .fc-button.fc-state-default, body .calendar .fc-header .fc-header-right .fc-button.fc-state-default {
        box-shadow: none;
        text-shadow: none;
        background: #f5f5f5;
    }

    body .calendar .fc-header .fc-header-left .fc-button.fc-state-active, body .calendar .fc-header .fc-header-right .fc-button.fc-state-active {
        background: #34495e;
        color: #fff;
        box-shadow: none;
    }

    body .calendar .fc-header .fc-header-left .fc-button .fc-button-inner, body .calendar .fc-header .fc-header-right .fc-button .fc-button-inner {
        background: none;
        border: none;
        color: #bbb;
        font-weight: 300;
        font-family: 'Roboto', Helvetica, sans-serif;
        text-transform: uppercase;
        font-size: 18px;
    }

    body .calendar .fc-header .fc-header-left .fc-button .fc-button-inner .fc-button-content, body .calendar .fc-header .fc-header-right .fc-button .fc-button-inner .fc-button-content {
        line-height: 48px;
        -webkit-transition: All 0.5s ease;
        -moz-transition: All 0.5s ease;
        -o-transition: All 0.5s ease;
        -ms-transition: All 0.5s ease;
        transition: All 0.5s ease;
    }

    body .calendar .fc-header .fc-header-left .fc-button:hover .fc-button-inner, body .calendar .fc-header .fc-header-right .fc-button:hover .fc-button-inner {
        color: #333;
    }

    body .calendar .fc-header .fc-header-left .fc-button.fc-state-active .fc-button-inner, body .calendar .fc-header .fc-header-right .fc-button.fc-state-active .fc-button-inner {
        color: #333;
        font-weight: 400;
    }

    body .calendar .fc-header .fc-header-title h2 {
        font-family: 'Roboto', Helvetica, sans-serif;
    }

    body .calendar .fc-content .fc-state-highlight {
        background: #f5f5f5;
    }

    body .calendar .fc-content .fc-event {
        border-radius: 50%;
        width: 50px;
        height: 50px;
        background: #5bc0de;
        border: rgba(0, 0, 0, 0.1) solid 1px;
    }

    body .calendar .fc-content .fc-view-month table thead th {
        border: none;
    }

    body .calendar .fc-content .fc-view-month table tbody tr td.fc-widget-content {
        border: #000 solid 1px;
        background: #f5f5f5;
        margin: 3px 3px;
        padding: 10px;
    }

    body .calendar .fc-content .fc-view-month table tbody tr td .fc-day-number {
        font-size: 24px;
        font-weight: 300;
        font-family: 'Roboto', Helvetica, sans-serif;
        margin-bottom: 10px;
    }

    body .calendar .fc-content .fc-view-month .fc-event-skin {
        background: #2abf9e;
        border: none;
        border-radius: 0;
        line-height: 1.3;
    }

    body .calendar .fc-content .fc-view-month .fc-event-skin .fc-event-inner {
        margin: 3px;
        width: auto;
    }

    body .calendar .fc-content .fc-view-month .fc-event-skin .fc-event-time {
        font-weight: 600;
        margin-left: 3px;
        text-transform: uppercase;
    }

    body .calendar .fc-content .fc-view-month .fc-event-skin .fc-event-title {
        margin: 3px;
        line-height: 1;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-days thead th {
        border: none;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-days tbody tr td {
        border: none;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-days tbody tr td.fc-widget-content {
        border: #fff solid 2px;
        background: #f5f5f5;
        margin: 3px 3px;
        padding: 10px;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-days tbody tr td.fc-state-highlight {
        background: #ddd;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-allday thead th {
        border: none !important;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-slots tr th.fc-agenda-axis {
        border: none !important;
        background: #fff;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-slots tr td.fc-widget-content {
        background: none;
        border: #fff solid 2px;
        border-bottom-width: 1px;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-slots tr.fc-minor {
        border-top: none;
    }

    body .calendar .fc-content .fc-view-agendaWeek table.fc-agenda-slots tr.fc-minor td.fc-widget-content {
        border-top: none;
        border-bottom-width: 2px;
    }

    body .calendar .fc-content .fc-border-separate tr.fc-last th, body .calendar .fc-content .fc-border-separate tr.fc-last td {
        border: none;
    }

    /*!
     * FullCalendar v1.6.1 Stylesheet
     * Docs & License: http://arshaw.com/fullcalendar/
     * (c) 2013 Adam Shaw
     */
    .fc {
        direction: ltr;
        text-align: left;
    }

    .fc table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    html .fc, .fc table {
        font-size: 1em;
    }

    .fc td, .fc th {
        padding: 0;
        vertical-align: top;
    }
</style>

<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES");
use \Carbon\Carbon;
?>
<div class="col-md-12 m-b-20">
    <div class="Premium col-md-2 text-center col-xs-2 text-white m-l-5">PREMIUM</div>
    <div class="Alta col-md-2 text-center col-xs-2 text-white m-l-5">ALTA</div>
    <div class="Media col-md-2 text-center col-xs-2 text-white m-l-5">MEDIA</div>
    <div class="Baja col-md-2 text-center col-xs-2 text-white m-l-5">BAJA</div>
</div>

<div style="clear: both;"></div>
<?php $dateAux = $startYear->copy(); ?>
<?php for ($j = 1; $j <= $diff; $j++): ?>
    <?php
        $startMonth = $dateAux->copy()->startOfMonth();
        $endMonth   = $dateAux->copy()->endOfMonth();
        $countDays  = $endMonth->diffInDays($startMonth);
    ?>
    <div id="calendar" class="col-xs-12 col-md-3">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="calendar fc fc-ltr">
                    <div class="col-md-2">
                        <?php echo ucfirst($startMonth->copy()->formatLocalized('%b %Y')) ?>
                    </div>

                    <div class="fc-content" style="position: relative; min-height: 1px;">
                        <div class="fc-view fc-view-month fc-grid" style="position: relative; min-height: 1px;" unselectable="on">
                            <table class="fc-border-separate" style="width:100%;border: 1px solid black" cellspacing="0">
                                <thead>

                                    <?php $dayMonth = $startMonth;?>
                                    <?php for ($i = 1; $i <= $countDays + 1; $i++): ?>
                                    <?php if($dayMonth->format('w') == 0 && $i == 1): ?>
                                        <?php for ($h = 1; $h <= 6; $h++): ?>
                                            <th class="fc-day-header fc-mon fc-widget-header" style="width: 100px;">
                                                &nbsp;
                                            </th>
                                        <?php endfor ?>
                                    <?php elseif ($dayMonth->format('w') != 1 && $i == 1): ?>
                                        <?php for ($h = 1; $h <= $dayMonth->format('w') - 1; $h++): ?>
                                            <th class="fc-day-header fc-mon fc-widget-header" style="width: 100px;">
                                                &nbsp;
                                            </th>
                                        <?php endfor ?>
                                    <?php endif ?>
                                    <?php
                                        $daySeasons = \App\Seasons::where('start_date', '<=', $dayMonth)
                                                          ->where('finish_date', '>=', $dayMonth)
                                                          ->get();
                                        $status = "";
                                        if (count($daySeasons) > 0)
                                        {
                                            foreach ($daySeasons as $daySeason)
                                    {
                                        $nameSeason  = $daySeason->typeSeasons->name;
                                        $startseason = $daySeason->start_date;
                                        $endseason   = $daySeason->finish_date;
                                        switch ($daySeason->type)
                                        {
                                            case 1:
                                                $status = "Alta";
                                                break;
                                            case 2:
                                                $status = "Media";
                                                break;
                                            case 3:
                                                $status = "Baja";
                                                break;
                                            case 4:
                                                $status = "Premium";
                                                break;
                                            default:
                                                $status = "";
                                                break;
                                        }
                                    }
                                        } else
                                        {
                                            $status      = "";
                                            $nameSeason  = "";
                                            $startseason = "";
                                            $endseason   = "";
                                        }
                                    ?>
                                        <th class="fc-day-header fc-mon fc-widget-header <?php echo $status ?>"
                                            style="width: 100px;">
                                            <span style="font-size: 11px">
                                                <?php echo $dayMonth->format('d') ?> <br>
                                            <!-- <?php echo ucfirst($dayMonth->formatLocalized('%a')) ?> -->
                                            </span>
                                        </th>

                                    <?php $dayMonth->addDay() ?>

                                    <?php if ($dayMonth->format('D') == "Mon"): ?>
                                        </tr>
                                    <?php endif ?>
                                <?php endfor ?>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $dateAux->addMonth(); ?>
    <?php if ($j == 4 || $j == 8): ?>
        <div style="clear: both;"></div>
    <?php endif ?>
<?php endfor;?>


