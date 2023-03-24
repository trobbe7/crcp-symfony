<br/>
<p align="center">
  <a href="https://github.com/trobbe7/crcp-symfony">
    <img src="https://team-crcp.com/assets/img/logo.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">CRCP</h3>

  <p align="center">
    Suivi de productivité pour conseillers
    <br/>
    <br/>
  </p>
</p>

![Contributeurs](https://img.shields.io/github/contributors/trobbe7/crcp-symfony?color=dark-green) ![Issues](https://img.shields.io/github/issues/trobbe7/crcp-symfony) 

## Table Of Contents

* [Framework](#framework)
* [Démarrer](#getting-démarrer)
  * [Pré-requis](#pré-requis)
  * [Installation](#installation)
* [Contribution](#contribution)
* [Auteur](#auteur)

## Framework

Cette application a été réalisée avec Symfony 6.2.

## Démarrer

Ceci est un exemple de comment héberger ce programme en local.

### Pré-requis

Voici la liste des dépendances nécessaires pour le bon fonctionnement du programme :

* npm

```sh
npm install npm@latest -g
```

* composer

```sh
npm install --save composer
```

### Installation

1. Cloner le repo

```sh
git clone https://github.com/trobbe7/crcp-symfony.git
```

2. Installer les packages Composer

```sh
composer install
```

3. Editer les variables nécessaire (database, ...)

```sh
./.env
```


4. Créer le schéma de la Doctrine (insertion du SQL nécessaire dans la database)

```sh
php bin/console doctrine:schema:create
```

5. Lancer le serveur (port au choix, le serveur doit pointer vers le dossier public)

```sh
php -S localhost:8080 -t public
```

## Contribution



### Créer une Pull Request

1. Cloner le repo
2. Créer une nouvelle branche (`git checkout -b new/new`)
3. Commit vos nouveautés (`git commit -m 'Ajout d'une fonctionnalité'`)
4. Push la branche (`git checkout -b new/new`)
5. Ouvrir une pull request

## Auteur

* **Tom ROBBE**  - [Github](https://github.com/trobbe7)

