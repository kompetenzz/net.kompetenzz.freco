<?php
// This file declares an Angular module which can be autoloaded
// in CiviCRM. See also:
// \https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules/n
return [
  'js' => [
    'ang/crmFreco.js',
    'ang/crmFreco/*.js',
    'ang/crmFreco/*/*.js'
    ],
  'css' => [
    'ang/crmFreco.css',
  ],
  'partials' => [
    'ang/crmFreco',
  ],
  'requires' => [
    'crmUi',
    'crmUtil',
    'ngRoute'
  ],
  'settings' => [],
];
