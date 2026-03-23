<?php
/**
 * Stub i18n.php required by Codeception's Gherkin loader.
 *
 * Codeception 4.x has a bug where it resolves the behat/gherkin i18n file
 * path incorrectly (3 parent dirs instead of 1), landing at the project root.
 * This stub prevents the fatal error. We don't use Gherkin/BDD tests.
 *
 * @see https://github.com/Codeception/Codeception/issues/6607
 */
return require __DIR__ . '/vendor/behat/gherkin/i18n.php';
