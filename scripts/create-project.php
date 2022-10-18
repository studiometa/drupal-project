<?php

use Drupal\Component\Utility\Crypt;

require __DIR__ . '/functions/autoload.php';

use function Studiometa\DrupalInstaller\{updateFile, readFile, runCommands};

$name = basename( dirname( __DIR__ ) );

if ( readFile( 'README.md' )[0] !== "# Drupal project\n" ) {
	echo "\nProject already created.";
	die(PHP_EOL);
}

updateFile(
	'package.json',
	[
		1 => sprintf( "  \"name\": \"%s\",", $name ),
		2 => "  \"version\": \"0.0.0\",",
	]
);

updateFile(
	'.ddev/config.yaml',
	[
		0 => sprintf( 'name: %s', $name ),
	]
);

updateFile(
	'README.md',
	[
		0 => sprintf( "# %s", $name ),
		7 => sprintf( 'git clone git@gitlab.com:studiometa/%s.git', $name ),
	]
);

runCommands(
	'Removing unwanted files',
	[
		'rm -rf .github',
	]
);

runCommands(
	'Initialize Git repository',
	[
		'git init',
		'git branch -m master',
    'git add README.md',
		'git commit -m "Premier commit"',
		'git flow init -d',
		'git flow feature start initialisation',
		'git add .',
		'git commit -m "Initialise Drupal"',
	]
);

runCommands(
	'Install Drupal',
	[
		'cp .env.example .env',
	]
);

updateFile(
  '.env',
	[
    3  => sprintf( 'APP_HOST=%s.ddev.site', $name ),
		4  => 'APP_ENV=local',
		5  => 'APP_DEBUG=true',
		6  => 'APP_CACHE=false',
		7  => 'APP_SSL=true',
		40  => sprintf( 'HAS_SALT="%s"', Crypt::randomBytesBase64(55)),
  ]
);

runCommands(
  'Start ddev',
  [
    'ddev auth ssh',
    'ddev start',
  ]
);

runCommands(
  'Create database',
  [
    sprintf(
      'ddev drush site:install standard -y --locale="fr" --account-name="studiometa" --account-mail="agence@studiometa.fr" --site-mail="agence@studiometa.fr" --account-pass="motdepasse" --site-name="%s"',
      $name
    ),
  ]
);

runCommands(
  'Export & Commit basic configuration',
  [
    'ddev drush config:export -y',
    'git add ./config/sync/*',
    'git commit -m "Initialise la configuration"',
  ]
);

runCommands(
  'Enable core modules',
  [
    // @todo Add activation of core/modules.
    'ddev drush en -y admin_toolbar admin_toolbar_tools admin_toolbar_search',
    'ddev drush en -y field_group',
    'ddev drush en -y pathauto redirect',
    ]
  );

runCommands(
  'Enable themes admin and default',
  [
    'ddev drush theme:enable studiometa',
    'ddev drush theme:enable gin',
    'ddev drush config-set system.theme admin gin -y',
    'ddev drush config-set system.theme default studiometa -y',
    'ddev drush en -y gin_toolbar gin_login',
    'ddev drush config-set gin.settings classic_toolbar horizontal -y',
  ]
);

runCommands(
  'Enable contrib modules',
  [
    'ddev drush en -y metatag',
    'ddev drush en -y paragraphs',
    'ddev drush en -y paragraphs_asymmetric_translation_widgets',
    'ddev drush en -y editor_advanced_link twig_tweak',
  ]
);

runCommands(
  'Commit basic custom configuration (with modules and themes)',
  [
    'ddev drush config:export -y',
    'git add ./config/sync/*',
    'git commit -m "Ajoute la configuration des modules utiles"',
  ]
);

runCommands(
  'Enable custom modules',
  [
    'ddev drush en -y studiometa studiometa_twig_extensions studiometa_display_currentbranch',
  ]
);

runCommands(
  'Commit basic custom configuration',
  [
    'ddev drush config:export -y',
    'git add ./config/sync/*',
    'git commit -m "Ajoute la configuration des modules custom"',
  ]
);

runCommands(
  'Open your new website in your default navigator',
  [
    sprintf( 'open https://%s.ddev.site', $name ),
  ]
);

echo PHP_EOL;
