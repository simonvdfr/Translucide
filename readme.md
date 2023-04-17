# [CMS Translucide](https://www.translucide.net) - Léger et puissant

[![jQuery](https://img.shields.io/badge/Licence-MIT-green.svg)]()
[![jQuery](https://img.shields.io/badge/PHP->=7.2-lightgrey.svg?colorB=8892bf)](http://php.net/)
[![jQuery](https://img.shields.io/badge/MySQL-5.7-lightgrey.svg?colorB=f29111)](https://www.mysql.fr/)
[![jQuery](https://img.shields.io/badge/MariaDB-10.4-lightgrey.svg?colorB=f29111)](https://mariadb.org/)
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
- décommentez les valeurs de `$GLOBALS['add_content']` dont vous aurez besoin (product, article, event, video, media, page). Ils correspondent aux types de contenus. Vous pouvez en ajouter d'autres personnalisés. Attention, il faut également les activer en les sélectionnant dans le ou les profils d'administration concernés en mode édition (seront visibles après reconnexion).
- décommentez les valeurs de `$GLOBALS['toolbox']` dont vous aurez besoin (titres, mise en forme...). Ils apparaitront dans une barre d'outils lors de l'édition d'un contenu.

### Prérequis de configuration de PHP
Dans la configuration de PHP (php.ini) short_open_tag doit être en On `short_open_tag = On`. Il faut que les extensions PHP suivantes soient installées : php-mbstring, php-mysql, php-curl et php-gd.

Le CMS a été testé sur les versions PHP de la 7.2 à la 8.0, où il semble intégralement fonctionnel.
Le CMS est en cours de mise à jour pour fonctionner sur PHP 8.1 et 8.2 qui peuvent comporter des anomalies.

### Configurations spécifiques
#### Couleurs sur les textes
Dans le fichier `config.php`, vous pouvez activer les couleurs dans les outils d'édition (variable `$GLOBALS['toolbox']`), après il faut dire combien de class vous avez dans votre `style.css` dans la variable `$GLOBALS['Nbcolor'] = 2;`
Pour 2 class comme dans l'exemple il faut dans votre fichier `style.css` :
~~~~
.color-1 { color: red; }
.color-2 { color: blue; }
~~~~

## Premier pas
- Descendez tout en bas du site, sur la gauche doit apparaître un bouton avec un petit crayon pour éditer la page courante, au survol de ce dernier un autre bouton `+` apparaît pour ajouter une page. Choisissez l'onglet correspondant à votre type de contenu, choisissez la template souhaitée dans le menu déroulant et saisissez le nom de la page.
- Une fois la page créée (vous lui avez donné un titre et une template), vous pouvez l'éditer en cliquant en bas à gauche sur le crayon d'édition.
- Pour créer une nouvelle template, ajoutez un fichier php dans le dossier `tpl` de votre thème. Créez la trame avec la structure html de votre choix et ajoutez les contenus éditables à l'aide des fonctions existantes (cf. plus loin). Ajoutez le contenu directement sur le site en mode édition, ceci alimentera directement la BDD.
- Normalement après votre installation vous êtes redirigé vers la page d'accueil déjà créer et non activé (pensez à l'activer pour rendre votre site visible). Cette page d'accueil avec normalement la template `home` a la particularité d'avoir comme permalien `index` pour être défini comme page de défaut quand on tape le nom de domaine de votre site sans URL spécifique.

## Raccourcis clavier (sur Chrome)
- <kbd>ctrl + e</kbd> Lance le mode édition

### Raccourcis en mode édition
Pour qu'il soit fonctionnel vous devez modifier dans `config.php` la variable `$GLOBALS['shortcut'] = true;`

- <kbd>ctrl + s</kbd> Sauvegarde les changements
- <kbd>ctrl + q</kbd> Change le niveau d'activation de la page
- <kbd>ctrl + z</kbd> Annule la dernière action dans les blocs textes

Note : pour un site accessible (RGAA) vous devez désactiver les raccourcis clavier dans `config.php` avec `$GLOBALS['shortcut'] = false;`

## Utilisation de l'éditeur du menu de navigation
- Lorsque vous modifiez du contenu qui se trouve dans `<header>` ou `<footer>`, ça le modifie pour toutes les pages du site.
- Lorsque vous passez la souris dans le header une boîte s'ouvre vous proposant les pages pas encore présentes dans le menu.
- Un clic sur le `+` vous permet d'ajouter l'élément au menu, ou sinon vous pouvez faire un drag&drop
- Une fois dans le menu, au survol d'un élément une zone en pointillés apparaît au-dessus pour pouvoir le déplacer en drag&drop.
- Si vous saisissez un élément et que vous le glissez dans la boîte d'ajout, ça se transforme en poubelle, pour supprimer l’élément du menu.

## Fonctions pour rendre éditables des zones
- `text("nom-de-la-zone" [, array("default" => "Texte par défaut", "global" => true, "class" => "meta number readonly", "placeholder" => "Insérer ici le contenu de votre article", "lazy" => true)])`
	- global : Cet argument permet d'avoir un contenu qui se retrouve à plusieurs endroits du site (il n'est pas rattaché exclusivement à la page en cours)
	- class :
		- L'ajout de la class `meta` fait que la donnée est ajoutée à la table `meta` dans la base de donnée. ceci afin par exemple de faire des recherches complexes (jointure entre la table `content` et `meta`)
		- La class `number` permet de limiter la saisie à des chiffres uniquement
		- La class `editable-hidden` permet d'afficher le contenu en mode édition et de le cacher en mode vue
	- placeholder : permet de mettre un texte plus parlant que le nom de la zone pour l'utilisateur qui aura à ajouter des contenus
	- lazy : les images qui seront ajoutées depuis la médiathèque dans cette zone éditable auront l'option qui permet de charger en lazyloading (chargement de l'image uniquement si elle est visible dans l'écran)
	- A noter qu'il existe des alias à cette fonction qui permettent de créer rapidement des blocs editable avec un tag spécifique comme `h1("nom-de-la-zone", "class")`, `h2()`, `h3()`, `span()`

- `media("nom-de-la-zone" [,'100x100'])` L'argument final est optionnel, il force une taille
	- On peut ajouter plus d'arguments `media("nom-de-la-zone", array("dir" => "product/1/", "size" => "300", "class" => "fl mal", "lazy" => true))`. Ici par exemple on spécifie un dossier destination pour le media (dir), une taille (size), une classe (class), ou encore que l'image se chargera en lazyloading
	- Pour modifier la taille de la zone de téléchargement du média, allez dans le fichier `lucide.css` et modifiez les valeurs `min-width` et `max-width` des class `.lucide .editable-media`

- `bg()` A placer dans un `<div>` ou autres pour rendre l'image de fond éditable (vous pouvez également demander que l'image de fond se charge en lazyloading)

- `input("nom-de-la-zone" [, array("type" => "hidden", "autocomplete" => ["variable 1", "variable 2"])])` créé un champ qui s'affiche uniquement en mode édition et qui permet de stocker des variables éditables. Ici `autocomplete` permet d'avoir une suggestion de valeur lors de la saisie

- `module("nom-du-module")` 
	- Permet la création de plusieurs blocs de contenus éditables au format identique sur une même page
	- Avant la zone du module, mettez `<? $module = module("nom-du-module"); ?>` : le nom du module servira d'id plus loin
	- La zone de module aura par exemple la structure suivante (attention, chaque fonction doit contenir le nom du module en préfixe) :
	
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

## Page 404 & 503
Vous pouvez customiser les pages 404 (not found) & 503 (Service Unavailable) en créant une page avec comme permalien `404` ou `503`

## Option fermeture du site
Vous pouvez fermer le site dans une tranche horaire définit dans la variable `$GLOBALS['offline'] = '20:00-06:00 +1 day';`. dans cet exemple le site serra fermer entre 20h et 6h du matin. C'est la page 503 qui serra charger.

## Version bêta de l'intégration de ecoIndex
Pour permettre de prendre conscience de l'impact environnemental de l'intégration des contenus dans le CMS j'ai intégré de façon simplifier la note ecoIndex.
L'idée est en un minimum de code Javascript (pour un minimum de dettes techniques) d'obtenir une mensure se rapprochant le plus possible de l'ecoIndex original. J'utilise la méthode de calcul original sans modification.
Le Javascript audite la taille de la DOM, le nombre de fichiers et leur poids. Ces 2 derniers éléments étant parfois moins fiables, les résultats peuvent légèrement différer des mesures ecoIndex originales.
Pour activer la fonction dans l'administration (mesure lors de la sauvegarde de vos modifications) il faut 
`$GLOBALS['ecoindex'] = true;` dans le fichier `config.php`

Les fonctions de calcul viennent de [GreenIT-Analysis](https://github.com/cnumr/GreenIT-Analysis/).
`Copyright (C) 2019 didierfred@gmail.com / GNU Affero General Public License AGPL v3`

L'algorithme [EcoIndex](http://www.ecoindex.fr/quest-ce-que-ecoindex/) est sous [Licence Creative Commons CC-By-NC-ND](https://creativecommons.org/licenses/by-nc-nd/2.0/fr/)

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
Autant que possible ! Nous utilisons <a href="https://www.browserstack.com">BrowserStack</a> pour faire nos tests.
