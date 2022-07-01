Module Orthodidacte
==================================
Ce module permet de faire un lien entre un cours Moodle, un groupe et un espace sur l'outil [Orthodidacte](https://www.orthodidacte.com/). 

Pré-requis
------------
- Moodle en version 3.7 ou plus récente.<br/>
  -> Tests effectués sur des versions 3.7 à 3.11.6 (tests sur des versions précédentes par encore effectués).<br/>
  -> Développement et tests en cours sur la version 4.

- Avoir pris contact avec Orthodidacte pour la mise en place du système d'authentification et avoir défini les espaces qui seront présents dans l'outil.

Installation
------------
1. Installation du plugin

- Avec git:
> git clone https://github.com/andurif/moodle-mod_orthodidacte.git mod/orthodidacte

- Téléchargement:
> Télécharger le zip depuis https://github.com/andurif/moodle-mod_orthodidacte/archive/refs/heads/main.zip, dézipper l'archive dans le dossier mod/ et renommez le "orthodidacte" si besoin ou installez-le depuis la page d'installation des plugins si vous possédeez les bons droits.

2. Aller sur la page de notifications pour finaliser l'installation du plugin.

3. Une fois l'installation terminée, plusieurs options d'administration sont à renseigner:

> Administration du site -> Plugins -> Modules d'activité -> Orthodidacte -> url

Ce réglage permet de fixer l'url fournie par Orthodidacte permettant l'appel au web service nécessaire à la génération du lien vers l'espace.

> Administration du site -> Plugins -> Modules d'activité -> Orthodidacte -> authprefix

Ce réglage permet d'indiquer le préfixe utilisé lors de l'appel au service Orthodidacte et permettant l'authentification.

> Administration du site -> Plugins -> Modules d'activité -> Orthodidacte -> parcoursitems

Ce réglage permet de lister les différents types de parcours présents dans Orthodidacte et disponibles à la sélection dans le formulaire de création de l'activité.<br/>
Une ligne correspond à un type de parcours avec un code et un libellé séparés par une barre virticale (ou pipe).

> Administration du site -> Plugins -> Modules d'activité -> Orthodidacte -> espaceitems

Ce réglage permet de lister les différents espaces présents dans Orthodidacte et disponibles à la sélection dans le formulaire de création de l'activité.<br/>
Une ligne correspond à un espace avec un code et un libellé séparés par une barre virticale (ou pipe).

Présentation / Principe
------------
Le formulaire de création permet de sélectionner à quel espace Orthodidacte (choix parmi ceux renseignés dans l'administration) et pour quel type de parcours les membres d'un groupe pourront accéder.<br/>
Attention, si votre cours ne possède aucun groupe il ne sera pas possible de créer une activité Orthodidacte.

Au moment de la consultation de l'activité, un appel à un web service propre à Orthodidacte est effectué (url renseignée dans l'administration) en fournissant des informations concernant l'utilisateur connecté, son groupe et l'espace demandé.
En réponse le web service générera et renverra une adresse vers laquelle rediriger l'utilisateur. L'utilisateur sera alors authentifié et connecté à Orthodidacte et accédera à l'espace demandé sur l'outil.

Le plugin utilise la notion d'apprenants et d'encadrants présents dans Orthodidacte.
Pour faire la différence entre ces deux populations nous avons décidés de nous baser sur l'adresse mail de l'utilisateur et de tester
la présence de la chaîne de caractères <strong>"@etu."</strong> dans l'adresse. Si cette chaîne est présente on considère l'utilisateur comme un apprenant et comme un encadrant dans les autres cas.<br/>
Si cette règle ne s'applique pas dans votre établissement vous pourrez modifier et personnaliser celle-ci dans le code du plugin:
```php
// view.php, l.96
$profile = (strpos($USER->email,"@etu.") === false) ? "encadrant" : "apprenant";

// classes/output/mobile.php, l.99
$profile = (strpos($USER->email,"@etu.") === false) ? "encadrant" : "apprenant";
```

A propos
------
<a href="https://www.uca.fr" target="_blank">Université Clermont Auvergne</a> - 2022.<br/>
