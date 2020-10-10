<?php

namespace Drupal\Tests\l10n_pconfig\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests that plural formula is editable in the language form.
 *
 * @group l10n_pconfig
 */
class PluralFormulaLanguageFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['locale', 'language', 'l10n_pconfig'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    // User to add and remove language.
    $admin_user = $this->drupalCreateUser(['administer languages', 'access administration pages']);
    $this->drupalLogin($admin_user);
  }

  public function testPredefinedLanguage() {
    $this->drupalGet('admin/config/regional/language/add');

    $edit = [
      'predefined_langcode' => 'de',
    ];
    $this->drupalPostForm(NULL, $edit, 'Add language');
    $this->assertSession()->pageTextContains('The language German has been created and can now be used.');

    $this->clickLink('Edit', 1);

    $this->assertSession()->fieldValueEquals('name', 'German');
    $this->assertSession()->fieldValueEquals('formula', 'nplurals=2; plural=(n!=1);');

    /** @var \Drupal\locale\PluralFormulaInterface|\Drupal\locale\PluralFormulaStringInterface $pluralFormulaService */
    $pluralFormulaService = \Drupal::service('locale.plural.formula');
    $this->assertEquals('nplurals=2; plural=(n!=1);', $pluralFormulaService->getFormulaString('de'));
    $this->assertEquals(2, $pluralFormulaService->getNumberOfPlurals('de'));
    $this->assertEquals([1 => 0, 'default' => 1], $pluralFormulaService->getFormula('de'));
  }

  public function testPredefinedLanguageWithNoSuggestedPlural() {
    $this->drupalGet('admin/config/regional/language/add');

    $edit = [
      'predefined_langcode' => 'ku',
    ];
    $this->drupalPostForm(NULL, $edit, 'Add language');
    $this->assertSession()->pageTextContains('Plural formula cannot be automatically determined for the language added. Please edit the language and specify the plural formula manually.');
    $this->assertSession()->linkByHrefExists('admin/config/regional/language/edit/ku');
    $this->assertSession()->pageTextContains('The language Kurdish has been created and can now be used.');

    $this->clickLink('Edit', 1);

    $this->assertSession()->fieldValueEquals('name', 'Kurdish');
    $this->assertSession()->fieldValueEquals('formula', '');

    /** @var \Drupal\locale\PluralFormulaInterface|\Drupal\locale\PluralFormulaStringInterface $pluralFormulaService */
    $pluralFormulaService = \Drupal::service('locale.plural.formula');
    $this->assertEquals('', $pluralFormulaService->getFormulaString('ku'));
    $this->assertEquals(2, $pluralFormulaService->getNumberOfPlurals('ku'));
    $this->assertEquals(FALSE, $pluralFormulaService->getFormula('ku'));

    $edit = [
      'plural_formula' => 'nplurals=2; plural=(n != 1);',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save language');

    $this->clickLink('Edit', 1);

    $this->assertSession()->fieldValueEquals('name', 'Kurdish');
    $this->assertSession()->fieldValueEquals('formula', 'nplurals=2; plural=(n != 1);');

    $pluralFormulaService->reset();
    $this->assertEquals('nplurals=2; plural=(n != 1);', $pluralFormulaService->getFormulaString('ku'));
    $this->assertEquals(2, $pluralFormulaService->getNumberOfPlurals('ku'));
    $this->assertEquals([1 => 0, 'default' => 1], $pluralFormulaService->getFormula('ku'));
  }

  public function testCustomLanguage() {
    $this->drupalGet('admin/config/regional/language/add');

    $edit = [
      'predefined_langcode' => 'custom',
      'langcode' => 'ah-ES',
      'label' => 'Andalûh',
      'plural_formula' => 'nplurals=2; plural=(n!=1);',
    ];
    $this->drupalPostForm(NULL, $edit, 'Add custom language');
    $this->assertSession()->pageTextContains('The language Andalûh has been created and can now be used.');

    $this->clickLink('Edit', 1);

    $this->assertSession()->fieldValueEquals('name', 'Andalûh');
    $this->assertSession()->fieldValueEquals('formula', 'nplurals=2; plural=(n!=1);');

    /** @var \Drupal\locale\PluralFormulaInterface|\Drupal\locale\PluralFormulaStringInterface $pluralFormulaService */
    $pluralFormulaService = \Drupal::service('locale.plural.formula');
    $this->assertEquals('nplurals=2; plural=(n!=1);', $pluralFormulaService->getFormulaString('ah-ES'));
    $this->assertEquals(2, $pluralFormulaService->getNumberOfPlurals('ah-ES'));
    $this->assertEquals([1 => 0, 'default' => 1], $pluralFormulaService->getFormula('ah-ES'));
  }

  public function testCustomLanguageWithWrongFormula() {
    $this->drupalGet('admin/config/regional/language/add');

    $edit = [
      'predefined_langcode' => 'custom',
      'langcode' => 'ah-ES',
      'label' => 'Andalûh',
      'plural_formula' => 'invalid-plural-formula=BB',
    ];
    $this->drupalPostForm(NULL, $edit, 'Add custom language');
    $this->assertSession()->pageTextContains('Incorrect plural formula format. Please check your sources again.');
    $this->assertSession()->pageTextNotContains('The language Andalûh has been created and can now be used.');
  }

}
