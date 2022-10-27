# Drupal project

## Installation

Cloner le dépôt :

```bash
git clone <%= repository %>
```

Installer les dépendances nécessaires :

```bash
# Installer les dépendances Composer avec PHP 8.0
php8.0 $(which composer) install

# Installer les dépendances NPM avec Node 16
nvm use 16
npm install
```

## Développement

### Commandes disponibles

#### NPM

| Commande | Description |
|-|-|
| `npm run dev` | Démarre le serveur de compilation des fichiers SCSS et JS du thème. |
| `npm run build` | Build les fichiers SCSS, JS et Vue du thème. |
| `npm run lint` | Lint les fichiers SCSS, JS, Vue et Twig du thème avec ESLint, Stylelint et Prettier. |
| `npm run lint:scipts` | Lint les fichiers JS et Vue du thème avec ESLint et Prettier. |
| `npm run lint:styles` | Lint les fichiers SCSS et Vue du thème avec Stylelint et Prettier. |
| `npm run lint:templates` | Lint les fichiers Twig avec Prettier. |
| `npm run fix` | Formate les fichiers SCSS, JS, Vue et Twig du thème avec ESLint, Stylelint et Prettier. |
| `npm run fix:scipts` | Formate les fichiers JS et Vue du thème avec ESLint et Prettier. |
| `npm run fix:styles` | Formate les fichiers SCSS et Vue du thème avec Stylelint et Prettier. |
| `npm run fix:templates` | Formate les fichiers Twig du thème Prettier. |


#### Composer

| Commande | Description |
|-|-|
| `composer phpcs` | Lint les fichiers PHP du thème et des modules customs |
| `composer phpstan` | Analyse de manière statiques les fichiers PHP du thème et des modules customs |
| `vendor/bin/phpcbf` | Corrige les erreurs de linter des différents fichiers, les même que les commandes `phpcs` et `phpstan` |


### Ajouter des modules

Pour ajouter des extensions tiers, utilisez Composer. Par exemple, pour ajouter le plugin [Search API](https://www.drupal.org/project/search_api), vous pouvez procéder comme suit :

```bash
composer require drupal/search_api
```

#### Activer les modules avec drush

```bash
drush en search_api
```

---
## Modules Custom Studio Meta

- `studiometa_blocks_manager` pour la gestion des blocks customs.
- `studiometa_display_currentbranch` pour afficher la branche git courante sur les preproductions uniquement.
- `studiometa_pages_manager` pour la gestion des pages de configuration customs.
- `studiometa_paragraphs_builder_manager` pour la gestion des blocks du page-builder et des paragraphs concernés.
- `studiometa_twig_extensions` pour la gestion des extensions Twig.

---
## Twig

- [@studiometa/ui](ui.studiometa.dev) est utilisé sur le projet

```php
// Pour importer un atom présent dans le theme (fallback sur le package si non existant)
{% include '@ui/atoms/Button/Button.twig' %}
// Pour importer un atom du package @studiometa/ui
{% include '@ui-pkg/atoms/Button/Button.twig' %}
```

- [@studiometa/twig-toolkit](https://github.com/studiometa/twig-toolkit) est configuré pour le projet.
- Les fichiers de templates de block suivent la **nommenclature suivante**: pour `HeroBlock.php` le template sera `block--meta-hero.html.twig`.
- Ajouter en début de chaque fichier .twig un commentaire docblock comprenant toutes les variables disponibles dans ce fichier.

```php
{#
/**
 * @file
 * Name of the File
 *
 * @param {string} title
 * @param {string} description
 * @param {array} image
 */
#}
```

#### Filtres Twig

```php
// Clean une variable des balises pouvant provoquer des attaques XSS
{{ var|meta_sanitize }}
```
