<?php

namespace Drupal\l10n_pconfig;

/**
 * A service providing a list of "Drupal languages" with its plural formulae.
 *
 * @package Drupal\l10n_pconfig
 */
interface DrupalOrgPluralFormulaInterface {

  /**
   * Gets a list of known plural formulae keyed by langcode.
   *
   * @return array
   *   An array of known formulae keyed by langcode.
   */
  public function getDrupalPluralFormulae();

}
