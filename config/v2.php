<?php

return [
    /*
     * Inline "critical" first-screen CSS + non-blocking stylesheets (pages declare @section('v2crit', ...), files made by tools/make-critical-all.ps1).
     * OFF by default: in PageSpeed the plain blocking-CSS pages scored 98/98 (TBT 0 ms) while the critical + async mode scored 76-85 (TBT 460-800 ms),
     * because every late stylesheet forces a full style/layout pass inside the measured window. Without this switch the mode was chosen by file
     * timestamps, so an ordinary deploy could flip staging/production into the slower mode by accident.
     * Turn on only to experiment: V2_CRITICAL_CSS=true in .env, and re-measure with PageSpeed before keeping it.
     */
    'critical_css' => (bool) env('V2_CRITICAL_CSS', false),
];
