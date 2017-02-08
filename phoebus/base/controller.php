<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strPhoebusLiveURL = 'addons.palemoon.org';
$strPhoebusDevURL = 'dev.addons.palemoon.org';
$strPhoebusURL = $strPhoebusLiveURL;
$strPhoebusSiteName = 'Pale Moon - Add-ons';
$strPhoebusVersion = '1.5.0a1';
$boolDebugMode = false;

$strPaleMoonID = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';
$strFirefoxID = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';
$strThunderbirdID = '{3550f703-e582-4d05-9a08-453d09bdfdc6}';
$strSeaMonkeyID = '{92650c4d-4b8e-4d2a-b7eb-24ecf4f6b63a}';
$strApplicationID = $strPaleMoonID;

$strMinimumApplicationVersion = '27.0.0';
$strFirefoxVersion = '27.9';
$strFirefoxOldVersion = '24.9';

$strRequestComponent = funcHTTPGetValue('component');
$arrayArgsComponent = preg_grep('/^component=(.*)/', explode('&', parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)));
$strRequestPath = funcHTTPGetValue('path');

$strApplicationPath = $_SERVER['DOCUMENT_ROOT'] . '/phoebus/';
$strComponentsPath = $strApplicationPath . 'components/';
$strModulesPath = $strApplicationPath . 'modules/';
$strGlobalLibPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/';

$arrayComponents = array(
    'site' => $strApplicationPath . 'base/website.php',
    'aus' => $strComponentsPath . 'aus/aus.php',
    'download' => $strComponentsPath . 'download.php',
    'integration' => $strComponentsPath . 'integration/integration.php',
    'discover' => $strComponentsPath . 'discover/discover.php',
);

$arrayModules = array(
    'dbExtensions' => $strModulesPath . 'db/extensions.php',
    'dbThemes' => $strModulesPath . 'db/themes.php',
    'dbLangPacks' => $strModulesPath . 'db/langPacks.php',
    'dbSearchPlugins' => $strModulesPath . 'db/searchPlugins.php',
    'dbAUSExternals' => $strModulesPath . 'db/ausExternals.php',
    'dbSiteExternals' => $strModulesPath . 'db/siteExternals.php',
    'dbExtCategories' => $strModulesPath . 'db/extCategories.php',
    'readManifest' => $strModulesPath . 'funcReadManifest.php',
    'processContent' => $strModulesPath . 'funcProcessContent.php',
    'vc' => $strGlobalLibPath . 'nsIVersionComparator.php',
    'smarty' => $strGlobalLibPath . 'smarty/Smarty.class.php'
);

// ============================================================================

// == | Main | ================================================================

if ($_SERVER['SERVER_NAME'] == $strPhoebusDevURL) {
    $boolDebugMode = true;
    $strPhoebusURL = $strPhoebusDevURL;
    if (file_exists('./.git/HEAD')) {
        $_strGitHead = file_get_contents('./.git/HEAD');
        $_strGitSHA1 = file_get_contents('./.git/' . substr($_strGitHead, 5, -1));
        $_strGitBranch = substr($_strGitHead, 16, -1);
        $strPhoebusSiteName = 'Phoebus Development - Version: ' . $strPhoebusVersion . ' - ' .
            'Branch: ' . $_strGitBranch . ' - ' .
            'Commit: ' . $_strGitSHA1;
    }
    else {
        $strPhoebusSiteName = 'Phoebus Development - Version: ' . $strPhoebusVersion;
    }
    error_reporting(E_ALL);
    ini_set("display_errors", "on");
}

// Deal with unwanted entry points
if ($_SERVER['REQUEST_URI'] == '/') {
    $strRequestComponent = 'site';
    $strRequestPath = '/';
}
elseif ((count($arrayArgsComponent) > 1) || ($strRequestComponent != 'site' && $strRequestPath != null)) {
    funcSendHeader('404');
    exit();
}

// Load component based on strRequestComponent
if ($strRequestComponent != null) {
    if (array_key_exists($strRequestComponent, $arrayComponents)) {
        require_once($arrayComponents[$strRequestComponent]);
    }
    elseif ($strRequestComponent == '43893') {
        require_once($arrayModules['readManifest']);
        funcSendHeader('text');
        var_dump(funcReadManifest('extension', 'adblock-latitude', true, true, true, true, true));
    }
    else {
        funcError($strRequestComponent . ' is an unknown component');
    }
}
else {
    funcError('You did not specify a component');
}

// ============================================================================
?>