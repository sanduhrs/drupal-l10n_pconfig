<?php

namespace Drupal\l10n_pconfig;

/**
 * A service providing a list of "Drupal languages" compiled from the list of
 * languages in drupal.org CVS on July 18th, 2007.
 *
 * @package Drupal\l10n_pconfig
 */
class DrupalOrgPluralFormula implements DrupalOrgPluralFormulaInterface {

  /**
   * {@inheritDoc}
   *
   * A list of "Drupal languages" compiled from the list of languages
   * in drupal.org CVS on July 18th, 2007.
   *
   * Plural information based on:
   *   - http://translate.sourceforge.net/wiki/l10n/pluralforms
   *   - our own CVS repository information from core translations
   *   - feedback from drupal.org users and translators: http://groups.drupal.org/node/5216
   */
  public function getDrupalPluralFormulae() {
    $default = 'nplurals=2; plural=(n!=1);';
    $one = 'nplurals=1; plural=0;';
    return [
      'af' => $default,
      'am' => 'nplurals=2; plural=(n > 1);',
      // Wordforge says nplurals=4!?
      'ar' => 'nplurals=6; plural=n==1 ? 0 : n==0 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 && n%100<=99 ? 4 : 5;',
      'ast' => $default,
      'bg' => $default,
      'bn' => $default,
      'bs' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
      // Wordforge has different rules!?
      'ca' => 'nplurals=2; plural=(n > 1);',
      // Wordforge has different rules!?
      'cs' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
      'cy' => 'nplurals=4; plural=( n==1 ? 0 : (n==2 ? 1 : (n!=8 && n!=11 ? 2 : 3)));',
      'da' => $default,
      'de' => $default,
      // Wordforge has nplurals=1, but this might fit us better.
      'dz' => $default,
      'el' => $default,
      'en-gb' => $default,
      'eo' => $default,
      'es' => $default,
      'et' => $default,
      'eu' => $default,
      // Wordforge has nplurals=1, but this might fit us better.
      'fa' => $default,
      'fi' => $default,
      'fil' => 'nplurals=2; plural=(n > 1);',
      'fo' => $default,
      'fr' => $default,
      'ga' => 'nplurals=5; plural=n==1 ? 0 : n==2 ? 1 : n<7 ? 2 : n<11 ? 3 : 4;',
      'gl' => $default,
      'gu' => $default,
      'gsw-berne' => $default,
      'he' => $default,
      'hi' => $default,
      'hr' => 'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;',
      'ht' => $default,
      // Wordforge has nplurals=1, but this fits Hungarians way better.
      'hu' => $default,
      'hy' => 'nplurals=2; plural=(n > 1);',
      // Wordforge has nplurals=1, but this might fit us better.
      'id' => $default,
      'is' => $default,
      'it' => $default,
      // Wordforge has nplurals=1, but this might fit us better.
      'ja' => $default,
      'ka' => $default,
      'km' => $default,
      'kn' => $default,
      'ko' => $default,
      'lt' => 'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2;',
      'lv' => 'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2;',
      'mk' => $default,
      'ml' => $default,
      'mn' => $default,
      'mr' => $default,
      'ms' => $default,
      'my' => $default,
      'nb' => $default,
      'ne' => $default,
      'nl' => $default,
      'nn' => $default,
      // The 'no' code is superceeded by nb and nn
      //'no' => array('Norwegian', $default),
      'ne' => $default,
      'pa' => $default,
      'pl' => 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
      'pt' => $default,
      // Wordforge has different rules!?
      'pt-br' => $default,
      'pt-pt' => $default,
      'ro' => 'nplurals=3; plural=n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2;',
      'ru' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
      'sco' => $default,
      'se' => $default,
      'sk' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2;',
      'sl' => 'nplurals=4; plural=(n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3);',
      'sq' => $default,
      // Wordforge has nplurals=4 here, and could be right, based on the .po file data?!?
      'sr' => 'nplurals=3; plural=(n%10==1 && n%100 !=11) ? 0 : ((n%10 >=2 && n%10 <= 4) && (n%100 < 10 || n%100 >= 20) ? 1 : 2);',
      'sv' => $default,
      'sw' => $default,
      'ta' => $default,
      'te' => $default,
      'th' => $default,
      'tr' => $one,
      'uk' => 'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;',
      'ur' => $default,
      'uz' => $default,
      'vi' => $one,
      // How are the two zh- variants are different? This is how it is set up on l.d.o and nobody complained.
      'zh-hans' => $default,
      'zh-hant' => $one,
      'xx-lolspeak' => $default,
    ];
  }

}
