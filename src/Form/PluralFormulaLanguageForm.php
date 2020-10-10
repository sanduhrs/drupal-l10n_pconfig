<?php

namespace Drupal\l10n_pconfig\Form;

use Drupal\Component\Gettext\PoHeader;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\l10n_pconfig\DrupalOrgPluralFormula;
use Drupal\l10n_pconfig\DrupalOrgPluralFormulaInterface;
use Drupal\language\ConfigurableLanguageInterface;
use Drupal\locale\PluralFormulaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Alters the Drupal language module language forms.
 *
 * @package Drupal\l10n_pconfig\Form
 */
class PluralFormulaLanguageForm implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The plural formula service.
   *
   * @var \Drupal\locale\PluralFormulaInterface|\Drupal\locale\PluralFormulaStringInterface
   */
  protected $pluralFormulaService;

  /**
   * The Drupal.org plural formulae.
   *
   * @var \Drupal\l10n_pconfig\DrupalOrgPluralFormulaInterface
   */
  protected $drupalOrgPluralFormula;

  /**
   * Constructs a new PluralFormulaLanguageForm.
   *
   * @param \Drupal\locale\PluralFormulaInterface|\Drupal\locale\PluralFormulaStringInterface $plural_formula
   *   The plural formula service.
   * @param \Drupal\l10n_pconfig\DrupalOrgPluralFormulaInterface $drupalorg_plural_formula
   *   The Drupal.org plural formulae.
   */
  public function __construct(PluralFormulaInterface $plural_formula, DrupalOrgPluralFormulaInterface $drupalorg_plural_formula) {
    $this->pluralFormulaService = $plural_formula;
    $this->drupalOrgPluralFormula = $drupalorg_plural_formula;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('locale.plural.formula'),
      $container->get('l10n_pconfig.drupalorg_plural_formula')
    );
  }

  /**
   * Alters the configurable language entity edit and add form.
   *
   * @param array $form
   *   The form definition array for the configurable language entity.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function form(array &$form, FormStateInterface $form_state) {
    // Build a list of examples we have with existing languages.
    $formulas = $this->drupalOrgPluralFormula->getDrupalPluralFormulae();
    $predefined = LanguageManager::getStandardLanguageList();
    $types = [];
    foreach ($formulas as $langcode => $plural_formula) {
      if (isset($predefined[$langcode])) {
        $types[$plural_formula][] = $predefined[$langcode][0];
      }
    }
    $examples = [];
    foreach ($types as $plural_formula => $languages) {
      $examples[] = t('<strong>@formula</strong> used by %languages', array('@formula' => $plural_formula, '%languages' => join(', ', $languages)));
    }

    // Pick previous plural formula if editing an existing language.
    $plural_formula = '';
    if (isset($form['langcode']['#value'])) {
      $editingLangcode = $form['langcode']['#value'];
      $plural_formula = $this->pluralFormulaService->getFormulaString($editingLangcode);
    }

    // Add text field to enter the plural formula.
    $form['custom_language']['plural_formula'] = array(
      '#type' => 'textfield',
      '#title' => t('Plural formula'),
      '#description' => $this->t('The plural formula for this language in the format used in .po files. Either check a pre-existing .po file or see <a href="http://translate.sourceforge.net/wiki/l10n/pluralforms">the wordforge plural forms list</a>. Some known examples:'),
      '#default_value' => $plural_formula,
      '#maxlength' => 255,
    );
    $form['custom_language']['plural_formula_examples'] = [
      '#theme' => 'item_list',
      '#items' => $examples,
      '#wrapper_attributes' => ['class' => ['description']],
    ];
    $form['custom_language']['submit']['#weight'] = 50;
    // Buttons are different if adding or editing a language. We need validation
    // on both cases.
    $form['predefined_submit']['#submit'][] = self::class . '::submitPredefinedLanguage';

    $form['custom_language']['submit']['#validate'][] = self::class . '::validateFormula';

    $form['#entity_builders'][] = self::class . '::languageEntityBuilder';
  }

  /**
   * Entity builder for the configurable language type form with lingotek options.
   *
   * @param string $entity_type
   *   The entity type.
   * @param \Drupal\language\ConfigurableLanguageInterface $language
   *   The language object.
   * @param array $form
   *   The form definition array for the configurable language entity.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @see l10n_pconfig_form_language_admin_add_form_alter()
   * @see l10n_pconfig_form_language_admin_edit_form_alter()
   */
  public static function languageEntityBuilder($entity_type, ConfigurableLanguageInterface $language, array &$form, FormStateInterface $form_state) {
    // We need to check if the value exists, as we are enabling by default those
    // predefined languages in Drupal.
    if ($form_state->hasValue('plural_formula')) {
      // This means we submitted the custom form.
      $value = $form_state->getValue('plural_formula');
      self::updatePluralFormula($language->id(), $value);
    }
    else {
      $langcode = $form_state->getValue('predefined_langcode');
      $formulas = \Drupal::service('l10n_pconfig.drupalorg_plural_formula')->getDrupalPluralFormulae();
      if (isset($formulas[$langcode])) {
        self::updatePluralFormula($langcode, $formulas[$langcode]);
      }
      else {
        \Drupal::messenger()->addWarning(t('Plural formula cannot be automatically determined for the language added. Please <a href=":language-edit">edit the language</a> and specify the plural formula manually.', [':language-edit' => \Drupal::urlGenerator()->generateFromRoute('entity.configurable_language.edit_form', ['configurable_language' => $langcode])]));
      }
    }
  }

  /**
   * Custom language; check the validitiy of the plural formula given.
   */
  public static function validateFormula($form, &$form_state) {
    if (!$form_state->isValueEmpty('plural_formula')) {
      $value = $form_state->getValue('plural_formula');
      // This is tricky, but the best we can do unless refactoring PoHeader.
      $header = new PoHeader('xxxx');
      try {
        $parsed = $header->parsePluralForms($value);
        if (!is_array($parsed)) {
          $form_state->setErrorByName('plural_formula', t('Incorrect plural formula format. Please check your sources again.'));
        }
      }
      catch (\Exception $e) {
        $form_state->setErrorByName('plural_formula', t('Exception parsing plural formula format. Please check your sources again.'));
      }
    }
    else {
      $form_state->setErrorByName('plural_formula', t('Empty plural formula. Please fill this value.'));
    }
  }

  /**
   * Predefined language, if we also know about the plural formula, set that too.
   */
  public static function submitPredefinedLanguage(array &$form, FormStateInterface $form_state) {
    $langcode = $form_state->getValue('predefined_langcode');
    $formulas = \Drupal::service('l10n_pconfig.drupalorg_plural_formula')->getDrupalPluralFormulae();
    if (isset($formulas[$langcode])) {
      self::updatePluralFormula($langcode, $formulas[$langcode]);
    }
    else {
      \Drupal::messenger()->addWarning(t('Plural formula cannot be automatically determined for the language added. Please <a href=":language-edit">edit the language</a> and specify the plural formula manually.', [':language-edit' => \Drupal::urlGenerator()->generateFromRoute('entity.configurable_language.edit_form', ['configurable_language' => $langcode])]));
    }
  }

  /**
   * Helper function to update plural formula to given value for the $langcode.
   */
  protected static function updatePluralFormula($langcode, $plural_formula) {
    // This is tricky, but the best we can do unless refactoring PoHeader.
    $header = new PoHeader('xxx');
    $parsed = $header->parsePluralForms($plural_formula);
    [$nplurals, $newPlural] = $parsed;

    /** @var \Drupal\locale\PluralFormula $pluralFormulaService */
    $pluralFormulaService = \Drupal::service('locale.plural.formula');
    $pluralFormulaService->setPluralFormulaString($langcode, $plural_formula)
      ->setPluralFormula($langcode, $nplurals, $newPlural);
  }

}
