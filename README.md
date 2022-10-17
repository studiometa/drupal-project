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
