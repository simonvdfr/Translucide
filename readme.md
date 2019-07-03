# [CMS Translucide](http://www.translucide.net) - Léger et puissant

[![jQuery](https://img.shields.io/badge/Licence-MIT-green.svg)]()
[![jQuery](https://img.shields.io/badge/PHP-7.2-lightgrey.svg?colorB=8892bf)](http://php.net/)
[![jQuery](https://img.shields.io/badge/MySQL-5.7-lightgrey.svg?colorB=f29111)](https://www.mysql.fr/)
[![Knacss](https://img.shields.io/badge/Knacss-Fork-red.svg?colorB=cf381c)](https://github.com/simonvdfr/KNACSS)
[![jQuery](https://img.shields.io/badge/jQuery-3.3.1-blue.svg?colorB=78cff5)](https://jquery.com/)
[![jQuery](https://img.shields.io/badge/jQuery_UI-1.12.1-yellow.svg?colorB=faa523)](http://jqueryui.com/)
[![jQuery](https://img.shields.io/badge/FortAwesome-4.7.0-lightgrey.svg?colorB=1e9f75)](https://github.com/FortAwesome/Font-Awesome)

J'ai créé le CMS Translucide pour répondre à certains besoins personnels que j'ai en tant que développeur et intégrateur, mais aussi pour permettre aux clients de modifier plus simplement leur site sans casser le travail graphique fait en amont.

En clair je voulais un CMS plus simple d'approche que Wordpress, plus facilement customisable, plus léger, plus rapide à l'exécution, qui va plus à l'essentiel, le tout avec le moins de dépendance possible (uniquement jQuery, et pour l'administration jQuery UI & Font Awesome) et le plus éprouvé possible (php, mysql, jquery). Le moteur du site tient en très peu de fichiers, et pourtant il fait déjà pas mal de choses.

## Installation
- Décompressez les fichiers du site sur votre FTP et avec votre navigateur allez à l'adresse où se trouve `index.php` (Attention, si vous copiez-collez ces fichiers, pensez à copier également les fichiers cachés)
- Entrez les infos de connexion à la base de données, sélectionnez votre template de site (graphisme) et enfin entrez le mail et le mot de passe qui serviront à administrer le site au lancement.
- Copiez-collez le dossier theme 'default' et renommez-le

Après, au besoin, vous pouvez éditer manuellement le fichier `config.php`, généré à la suite de l'installation. Il contient les informations uniques et cruciales au bon fonctionnement du CMS :
	- modifiez la valeur de `$GLOBALS['theme']` avec le nom de votre thème
	- modifiez les valeurs de `$GLOBALS['domain']` avec les noms de votre site local et de votre site en ligne
	- modifier les valeurs de `$GLOBALS['db*']` hors dev local aves les infos de connexion de la BDD de votre hébergeur.
	- décommentez les valeurs de `$GLOBALS['add-content']` dont vous aurez besoin (product, article, event, video, media, page). Ils correspondent aux types de contenus. Vous pouvez en ajouter d'autres personnalisés. Attention, il faut également les activer en les sélectionnant dans le ou les profils d'administration concernés en mode édition (seront visibles après reconnexion).
	- décommentez les valeurs de `$GLOBALS['toolbox']` dont vous aurez besoin (titres, mise en forme...). Ils apparaitront dans une barre d'outils lors de l'édition d'un contenu.

Prérequis : dans la configuration de PHP short_open_tag doit être en On `short_open_tag = On`

## Premier pas
- Descendez tout en bas du site, sur la gauche doit apparaître un bouton avec un petit crayon pour éditer la page courante, au survol de ce dernier un autre bouton `+` apparaît pour ajouter une page. Choisissez l'onglet correspondant à votre type de contenu, choisissez la template souhaitée dans le menu déroulant et saisissez le nom de la page.
- Une fois la page créée (vous lui avez donné un titre et une template, idéalement `home`), vous pouvez l'éditer en cliquant en bas à gauche sur le crayon d'édition.
- Pour créer une nouvelle template, ajoutez un fichier php dans le dossier `tpl` de votre thème. Créez la trame avec la structure html de votre choix et ajoutez les contenus éditables à l'aide des fonctions existantes (cf. plus loin). Ajoutez le contenu directement sur le site en mode édition, ceci alimentera directement la BDD.

## Raccourcis clavier (sur Chrome)
- <kbd>⇧</kbd> force l'affichage du bouton édition en bas à gauche
- <kbd>ctrl + q</kbd>Lance le mode édition

### Raccourcis en mode édition
- <kbd>ctrl + s</kbd> Sauvegarde les changements
- <kbd>ctrl + q</kbd> Change le niveau d'activation de la page
- <kbd>ctrl + z</kbd> Annule la dernière action dans les blocs textes

## Utilisation de l'éditeur du menu de navigation
- Lorsque vous modifiez du contenu qui se trouve dans `<header>` ou `<footer>`, ça le modifie pour toutes les pages du site.
- Lorsque vous passez la souris dans le header une boîte s'ouvre vous proposant les pages pas encore présentes dans le menu.
- Un clic sur le `+` vous permet d'ajouter l'élément au menu, ou sinon vous pouvez faire un drag&drop
- Une fois dans le menu, au survol d'un élément une zone en pointillés apparaît au-dessus pour pouvoir le déplacer en drag&drop.
- Si vous saisissez un élément et que vous le glissez dans la boîte d'ajout, ça se transforme en poubelle, pour supprimer l’élément du menu.

## Fonctions pour rendre éditables des zones
- `text("nom-de-la-zone" [, array("default" => "Texte par défaut", "global" => true, "class" => "meta number readonly", "placeholder" => "")])`
	- global : Cet argument permet d'avoir un contenu qui se retrouve à plusieurs endroits du site (il n'est pas rattaché exclusivement à la page en cours)
	- class :
		- L'ajout de la class `meta` fait que la donnée est ajoutée à la table `meta` dans la base de donnée. ceci afin par exemple de faire des recherches complexes (jointure entre la table `content` et `meta`)
		- La class `number` permet de limiter la saisie à des chiffres uniquement
		- La class `editable-hidden` permet d'afficher le contenu en mode édition et de le cacher en mode vue
	- placeholder : permet de mettre un texte plus parlant que le nom de la zone pour l'utilisateur qui aura à ajouter des contenus

- `media("nom-de-la-zone" [,'100x100'])` L'argument final est optionnel, il force une taille
	- On peut ajouter plus d'arguments `media("nom-de-la-zone", array("dir" => "product/1/", "size" => "300", "class" => "fl mal"))`. Ici par exemple on spécifie un dossier destination pour le media (dir), une taille (size), ou encore une classe (class)
	- Pour modifier la taille de la zone de téléchargement du média, allez dans le fichier `lucide.css` et modifiez les valeurs `min-width` et `max-width` des class `.lucide .editable-media`

- `bg()` A placer dans un `<div>` ou autres pour rendre l'image de fond éditable

- `input("nom-de-la-zone" [, array("type" => "hidden", "autocomplete" => ["variable 1", "variable 2"])])` créé un champ qui s'affiche uniquement en mode édition et qui permet de stocker des variables éditables. Ici `autocomplete` permet d'avoir une suggestion de valeur lors de la saisie

- `module("nom-du-module")` 
	- Permet la création de plusieurs blocs de contenus éditables au format identique sur une même page
	- Avant la zone du module, mettez `<? $module = module("nom-du-module"); ?>` : le nom du module servira d'id plus loin
	- La zone de module aura par exemple la structure suivante (attention, chaque fonction doit contenir le nom du module) :
	
	~~~~
	<ul id="nom-du-module" class="module">
	<? foreach($module as $key => $val){ ?>
		<li>   			
			<h2><? txt('nom-du-module-nom-de-la-zone-titre-'.$key) ?></h2>
			<? media('nom-du-module-nom-de-la-zone-media-'.$key) ?>
			<? txt('nom-du-module-nom-de-la-zone-texte-'.$key) ?>    			
		</li>
	<? } ?>
	</ul> 	
	~~~~

## A faire à la mise en ligne
- minifiez fichiers js et css
- dans `config.php` :
	- modifiez la valeur de `$GLOBALS['scheme']` en `https://` hors dev si nécessaire
	- modifiez la valeur de `$GLOBALS['domain']` hors dev avec l'adresse définitive du site
	- modifiez les valeurs de `$GLOBALS['online']` en `true` (passe le site en `index, follow`)
	- modifiez l'email de contact dans `$GLOBALS['email_contact']` si le site contient un formulaire
	- modifiez la valeur de `$GLOBALS['min']` en `.min` hors dev si vous avez minifié les fichiers js et css 
	- modifiez la valeur de `$GLOBALS['google_analytics']` avec le code UA de Google Analytics si nécessaire

## Gestion du multilingue
Le CMS est prévu pour accueillir des traductions mais tout n'est pas fini/testé. Globalement les traductions de l'interface du CMS se trouvent dans le fichier `api/translation.php`

## Configuration du système de connexion tierce (Facebook, Google, Yahoo, Hotmail/Outlook)
Bientôt ... :)

## Wordpress VS Translucide
J'ai tenté de passer un site que j'avais fait sous Wordpress sous Translucide avec les mêmes fonctionnalités et aspect visuel et le résultat était plutôt édifiant.
Pour le même site on est passé de plus de 1000 fichiers avec Wordpress à moins de 100, images comprises, pour Translucide. Et de 50 mégaoctet à 5 mégaoctet pour la version CMS Translucide. Niveau temps d'exécution en général on divise par 2 le temps de chargement d'une page et ceci sans utiliser de système de cache spécifique. Combattons l'obésiciel ensemble :)

## FAQ

### Mais où est l'administration ?
Je voulais aussi en finir avec les interfaces d'administration qui je trouve est un frein pour les utilisateurs finaux. Pourquoi ne pas modifier les contenus directement dans le site ?

### Pourquoi on ne peut pas tout modifier avec l'éditeur live ?
Pour bien faire la différence entre le travail de développeur|graphiste|intégrateur et les besoins d'édition du client au final.
Pour ça j'ai créé un moteur de site qui permet d'éditer en live les contenus, on modifie directement le site tel qu'on le voit en tant que visiteur. Mais on ne peut pas forcément modifier la position, la taille de tous les blocs de contenus.

#### Pourquoi ?
Parce que le CMS est plus fait à la base pour des développeurs débutants, intégrateurs, graphistes, qui peuvent modifier directement le HTML pour faire des mises en page complexes, et après l'utilisateur final modifie les contenus.

Peut-être qu'un jour je coderai un builder qui permet de tout modifier, mais le temps à coder ça, et l'utilisation qui en sera faite au final n'est pas forcément très rentable et le rendu ne sera jamais aussi impressionnant et propre que si c'est fait en dur dans le code HTML.

### Comment modifier les contenus ?
Pour passer en mode édition il faut cliquer en bas à gauche sur l'icône avec le crayon. S'il n'est pas visible descendez jusqu'en bas du site ou appuyez sur la touche <kbd>⇧</kbd> du clavier pour le faire apparaître.

### Comment se connecter au système d'édition ?
Si vous avez entré ce qu'il faut dans la configuration vous pouvez utiliser votre compte Facebook, Google ou autres. Plus besoin de retenir un mot de passe d'administration en plus.
Sinon utilisez le compte admin créé lors de l'installation ou ajoutez un compte utilisateur avec le gestionnaire d'utilisateurs en haut à gauche de la barre qui apparaît en mode édition.

### Comment changer ou ajouter une image ?
On peut drag&drop les images dans le gestionnaire de média (icône média dans la barre d'outil lorsque l'on clique dans un bloc texte) ou dans les contenus dans les zones prédéfinies.

### C'est customisable ?
Nativement le CMS fait pas mal de choses, et ajouter une fonctionnalité est plutôt simple. J'ai codé le tout en procédural, avec peu de fichiers pour ne pas chercher des heures où se trouvent les choses.
Vous pouvez ajouter tout ce que vous voulez dans le dossier `theme` qui contient votre template visuel. 
Le dossier `plugin` est plus fait pour des fonctions génériques pas forcément liées spécifiquement à votre template. En général on fait un simple `include` du fichier contenu dans `plugin`.

### À qui s'adresse ce CMS ?
Le processus est plus fait pour qu'un graphiste|intégrateur ou/et un développeur web construisent une template simple en HTML et y intégrent les 4/5 balises de contenu type là où ils veulent que ça soit éditable. Le CMS fait le reste pour que l'utilisateur final puisse modifier en live son site avec le moins de clic possible.

### Est-il multiplate-forme et responsive ?
Autant que possible ! J'utilise <a href="https://www.browserstack.com"><img src="http://img4.hostingpics.net/pics/659921browserstacklogo.png" alt="browserstack"></a> pour faire mes tests.
