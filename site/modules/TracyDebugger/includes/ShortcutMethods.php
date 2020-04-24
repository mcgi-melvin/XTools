<?php

use Tracy\Debugger;

/**
 * short alias methods for lazy typists :)
 */

function tracyUnavailable() {
    if(!\TracyDebugger::getDataValue('enabled') || !\TracyDebugger::allowedTracyUsers() || !class_exists('\TD')) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * TD::debugAll() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('debugAll') && in_array('debugAll', $this->data['enabledShortcutMethods'])) {
    function debugAll($var, $title = NULL, array $options = NULL) {
        if(tracyUnavailable()) return false;
        return TD::debugAll($var, $title, $options);
    }
}

/**
 * TD::barDump() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('barDump') && in_array('barDump', $this->data['enabledShortcutMethods'])) {
    function barDump($var, $title = NULL, array $options = NULL) {
        if(tracyUnavailable()) return false;
        return TD::barDump($var, $title, $options);
    }
}

/**
 * TD::barDump() shortcut with live dumping.
 * @tracySkipLocation
 */
if(!function_exists('barDumpLive') && in_array('barDumpLive', $this->data['enabledShortcutMethods'])) {
    function barDumpLive($var, $title = NULL, array $options = NULL) {
        if(tracyUnavailable()) return false;
        return TD::barDumpLive($var, $title, $options);
    }
}

/**
 * TD::dump() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('dump') && in_array('dump', $this->data['enabledShortcutMethods'])) {
    function dump($var, $title = NULL, array $options = NULL, $return = FALSE) {
        if(tracyUnavailable()) return false;
        return TD::dump($var, $otions, $return);
    }
}

/**
 * TD::timer() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('timer') && in_array('timer', $this->data['enabledShortcutMethods'])) {
    function timer($name = NULL) {
        if(tracyUnavailable()) return false;
        return TD::timer($name);
    }
}

/**
 * TD::fireLog() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('fireLog') && in_array('fireLog', $this->data['enabledShortcutMethods'])) {
    function fireLog($message = NULL) {
        if(tracyUnavailable()) return false;
        return TD::fireLog($message);
    }
}

/**
 * TD::addBreakpoint() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('addBreakpoint') && in_array('addBreakpoint', $this->data['enabledShortcutMethods'])) {
    function addBreakpoint($name = null, $enforceParent = null) {
        if(tracyUnavailable() || !class_exists('\Zarganwar\PerformancePanel\Register')) return false;
        return TD::addBreakpoint($name, $enforceParent);
    }
}

/**
 * TD::templateVars() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('templateVars') && in_array('templateVars', $this->data['enabledShortcutMethods'])) {
    function templateVars($vars) {
        if(tracyUnavailable()) return false;
        return TD::templateVars($vars);
    }
}


/**
 * really short alias methods for really lazy typists :)
 */

/**
 * TD::debugAll() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('da') && in_array('da', $this->data['enabledShortcutMethods'])) {
    function da($var, $title = NULL, array $options = NULL) {
        if(tracyUnavailable()) return false;
        return TD::debugAll($var, $title, $options);
    }
}

/**
 * TD::barDump() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('bd') && in_array('bd', $this->data['enabledShortcutMethods'])) {
    function bd($var, $title = NULL, array $options = NULL) {
        if(tracyUnavailable()) return false;
        return TD::barDump($var, $title, $options);
    }
}

/**
 * TD::barDump() shortcut with live dumping.
 * @tracySkipLocation
 */
if(!function_exists('bdl') && in_array('bdl', $this->data['enabledShortcutMethods'])) {
    function bdl($var, $title = NULL, array $options = NULL) {
        if(tracyUnavailable()) return false;
        return TD::barDumpLive($var, $title, $options);
    }
}

/**
 * TD::dump() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('d') && in_array('d', $this->data['enabledShortcutMethods'])) {
    function d($var, $title = NULL, array $options = NULL, $return = FALSE) {
        if(tracyUnavailable()) return false;
        return TD::dump($var, $title, $options, $return);
    }
}

/**
 * TD::log() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('l') && in_array('l', $this->data['enabledShortcutMethods'])) {
    function l($message, $priority = 'info') {
        if(tracyUnavailable()) return false;
        return TD::log($message, $priority);
    }
}

/**
 * TD::timer() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('t') && in_array('t', $this->data['enabledShortcutMethods'])) {
    function t($name = NULL) {
        if(tracyUnavailable()) return false;
        return TD::timer($name);
    }
}

/**
 * TD::fireLog() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('fl') && in_array('fl', $this->data['enabledShortcutMethods'])) {
    function fl($message = NULL) {
        if(tracyUnavailable()) return false;
        return TD::fireLog($message);
    }
}

/**
 * TD::addBreakpoint() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('bp') && in_array('bp', $this->data['enabledShortcutMethods'])) {
    function bp($name = null, $enforceParent = null) {
        if(tracyUnavailable() || !class_exists('\Zarganwar\PerformancePanel\Register')) return false;
        return TD::addBreakpoint($name, $enforceParent);
    }
}

/**
 * TD::templateVars() shortcut.
 * @tracySkipLocation
 */
if(!function_exists('tv') && in_array('tv', $this->data['enabledShortcutMethods'])) {
    function tv($vars) {
        if(tracyUnavailable()) return false;
        return TD::templateVars($vars);
    }
}