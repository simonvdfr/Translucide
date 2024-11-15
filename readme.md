# [CMS Translucide](https://translucide.net) - Léger et puissant

[![jQuery](https://img.shields.io/badge/Licence-MIT-green.svg)]()
[![jQuery](https://img.shields.io/badge/PHP-7.2_to_8.2-lightgrey.svg?colorB=8892bf)](http://php.net/)
[![jQuery](https://img.shields.io/badge/MySQL-5.7-lightgrey.svg?colorB=f29111)](https://www.mysql.fr/)
[![jQuery](https://img.shields.io/badge/MariaDB-10.4-lightgrey.svg?colorB=f29111)](https://mariadb.org/)
[![Knacss](https://img.shields.io/badge/Knacss-Fork-red.svg?colorB=cf381c)](https://github.com/simonvdfr/KNACSS)
[![jQuery](https://img.shields.io/badge/jQuery-3.7-blue.svg?colorB=78cff5)](https://jquery.com/)
[![jQuery](https://img.shields.io/badge/jQuery_UI-1.13.2-yellow.svg?colorB=faa523)](http://jqueryui.com/)
[![jQuery](https://img.shields.io/badge/FortAwesome-4.7.0-lightgrey.svg?colorB=1e9f75)](https://github.com/FortAwesome/Font-Awesome)

## À qui s'adresse ce CMS ?

Ce CMS est à destination des développeurs et développeuses, intégrateurs et intégratrices, graphistes qui connaissent les bases du HTML et CSS. Connaitre un peu le PHP est un plus. Vous pouvez demander une démo technique avec le formulaire de contact sur le site [Translucide.net](https://translucide.net/#contact).

Techniquement, il est plus simple de l'installer sur un hébergement mutualisé en ligne si vous ne maîtrisez pas l'installation en local sur votre ordinateur d'Apache, PHP, MySQL.

## Installation
- Décompressez les fichiers du site sur votre FTP et avec votre navigateur allez à l'adresse où se trouve `index.php` (Attention, si vous copiez-collez ces fichiers, pensez à copier également les fichiers cachés)
- Entrez les infos de connexion à la base de données (vous devez avoir une base de données créée au préalable), sélectionnez votre thème de site (graphisme) et enfin entrez le mail et le mot de passe qui serviront à administrer le site au lancement
- Copiez-collez le dossier theme 'default' et renommez-le

Après, au besoin, vous pouvez éditer manuellement le fichier `config.php`, généré à la suite de l'installation. Il contient les informations uniques et cruciales au bon fonctionnement du CMS :
- modifiez la valeur de `$GLOBALS['theme']` avec le nom de votre thème
- modifiez les valeurs de `$GLOBALS['domain']` avec les noms de votre site local et de votre site en ligne
- modifier les valeurs de `$GLOBALS['db*']` hors dev local aves les infos de connexion de la BDD de votre hébergeur
- décommentez les valeurs de `$GLOBALS['add_content']` dont vous aurez besoin (product, article, event, video, media, page). Ils correspondent aux types de contenus. Vous pouvez en ajouter d'autres personnalisés. Attention, il faut également les activer en les sélectionnant dans le ou les profils d'administration concernés en mode édition (seront visibles après reconnexion)
- décommentez les valeurs de `$GLOBALS['toolbox']` dont vous aurez besoin (titres, mise en forme...). Ils apparaitront dans une barre d'outils lors de l'édition d'un contenu.

### Prérequis de configuration de PHP
Dans la configuration de PHP (php.ini) short_open_tag doit être en On `short_open_tag = On`. Il faut que les extensions PHP suivantes soient installées : php-mbstring, php-mysql, php-curl et php-gd.

Le CMS est utilisé depuis plusieur années avec les versions PHP de la 7.2 à la 8.1, où il semble intégralement fonctionnel.
Nous n'avons pas relevé de dysfonctionnements sous PHP 8.2 et 8.3 mais il peut subsister des anomalies que nous vous invitons à nous partager si vous en rencontrez.

### Configurations spécifiques
#### Couleurs sur les textes
Dans le fichier `config.php`, vous pouvez activer les couleurs dans les outils d'édition (variable `$GLOBALS['toolbox']`), après il faut dire combien de class vous avez dans votre `style.css` dans la variable `$GLOBALS['Nbcolor'] = 2;`
Pour 2 class comme dans l'exemple il faut dans votre fichier `style.css` :
~~~~
.color-1 { color: red; }
.color-2 { color: blue; }
~~~~

## Premier pas
- Descendez tout en bas du site, au centre doit apparaître un bouton avec une clé, cliquez sur cette clé et un pop up de connexion apparaitra. Connectez-vous avec l'identifiant et mot de passe choisis précédemment lors de l'installation
- Une fois connecté, descendez tout en bas du site, sur la gauche doit apparaître un bouton avec un petit crayon pour éditer la page courante, au survol de ce dernier un autre bouton `+` apparaît pour ajouter une page. Choisissez l'onglet correspondant à votre type de contenu, choisissez la template souhaitée dans le menu déroulant et saisissez le nom de la page.
- Une fois la page créée (vous lui avez donné un titre et choisi une template), vous pouvez l'éditer en cliquant en bas à gauche sur le crayon d'édition.
- Pour créer une nouvelle template, ajoutez un fichier php dans le dossier `tpl` de votre thème. Créez la trame avec la structure html de votre choix et ajoutez les contenus éditables à l'aide des fonctions existantes (cf. plus loin). Ajoutez le contenu directement sur le site en mode édition, ceci alimentera directement la BDD.
- Normalement après votre installation vous êtes redirigé vers la page d'accueil déjà créée et non activée (pensez à l'activer pour rendre votre site visible). Cette page d'accueil avec normalement la template `home` a la particularité d'avoir comme permalien `index` pour être défini comme page de défaut quand on tape le nom de domaine de votre site sans URL spécifique.

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
- Lorsque vous cliquez sur l'icône du crayon dans le header une boîte s'ouvre vous proposant les pages pas encore présentes dans le menu.
- Un clic sur le `+` vous permet d'ajouter l'élément au menu, ou sinon vous pouvez faire un drag and drop
- Une fois dans le menu, au survol d'un élément une zone en pointillés apparaît au-dessus pour pouvoir le déplacer en drag and drop et une croix pour pouvoir le supprimer.
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

	- Si vous ajoutez la class `end` au UL qui contient la class `module`, le bouton d'ajout de modules se positionnera en bas de la liste des modules, et les nouveaux modules ajoutés s'ajouteront à la fin et non au début.

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
Vous pouvez fermer le site dans une tranche horaire définit dans la variable `$GLOBALS['offline'] = '20:00-06:00 +1 day';`. dans cet exemple le site serra fermer entre 20h et 6h du matin. C'est la page 503 qui sera chargée.

## Version bêta de l'intégration de ecoIndex
Pour permettre de prendre conscience de l'impact environnemental de l'intégration des contenus dans le CMS j'ai intégré de façon simplifiée la note ecoIndex.
L'idée est qu'en un minimum de code Javascript (pour un minimum de dettes techniques) d'obtenir une mesure se rapprochant le plus possible de l'ecoIndex original. J'utilise la méthode de calcul originale sans modification.
Le Javascript audite la taille de la DOM, le nombre de fichiers et leur poids. Ces 2 derniers éléments étant parfois moins fiables, les résultats peuvent légèrement différer des mesures ecoIndex originales.
Pour activer la fonction dans l'administration (mesure lors de la sauvegarde de vos modifications) il faut mettre `$GLOBALS['ecoindex'] = true;` dans le fichier `config.php`

Les fonctions de calcul viennent de [GreenIT-Analysis](https://github.com/cnumr/GreenIT-Analysis/).
`Copyright (C) 2019 didierfred@gmail.com / GNU Affero General Public License AGPL v3`

L'algorithme [EcoIndex](https://www.ecoindex.fr/comment-ca-marche/) est sous [Licence Creative Commons CC-By-NC-ND](https://creativecommons.org/licenses/by-nc-nd/2.0/fr/)

## En quoi Translucide est plus écoconçu ?
Le CMS est fait dans une démarche d'écoconception, toujours perfectible. En numérique le service le plus écoconçu est celui qui n'existe pas, qui n'a donc pas d'impact.

Je voulais un CMS plus simple d'approche que Wordpress, plus facilement customisable, plus léger, plus rapide à l'exécution, qui va plus à l'essentiel, le tout avec le moins de dépendance possible. L'objectif est d'avoir un système léger, avec une arborescence simple, qui peut rendre un maximum de services.

### PHP
PHP est un langage de script plutôt efficient, même si ce n'est pas le meilleur. Par contre, c'est le plus répandu et disponible sur la plupart des hébergements grand public. Il est maintenu, ne bouge pas trop. Son ancienneté pour moi est un gage de qualité et sa longévité est impressionnante (je l'utilise depuis plus de 20 ans). Ce n'est pas une technologie trop éphémère, elle ne subit pas trop les effets de mode.

Le CMS se concentre sur les fonctionnalités de base de PHP et si possible celles qui consomment peu de ressources, sans aller dans la sur-optimisation. PHP concentre 50 % du code du CMS et sert surtout à faire des actions d'appel aux données ou leur sauvegarde. PHP n'est généralement pas le facteur limitant en termes de performances. C'est plus les bases de données qui peuvent être un problème quand il y a de fortes charges.

### Mysql / MariaDB
Pour l'affichage d'une page classique (les modèles de page les plus répandus sur un site) il y a seulement 2 requêtes à la base de données, une pour rapatrier le contenu et une autre pour l'entête et pied de page.
La base de données est composée de 4 tables.
- Celle des contenus.
- Une pour les méta données (typiquement les contenus de l'entête et pied de page.
- Une autre pour les tags s'ils sont utilisés (pour des filtrages des actualités par exemple).
- Enfin une table d'utilisateurs (pour administrer le site).
Le tout est optimisé pour ne pas grossir trop vite, un site classique pouvant tenir facilement dans moins de 1 Mo de base de données sans complexité pour atteindre les données.

### Javascript
Historiquement, la plus grosse dépendance du CMS est JQuery. C'est une librairie puissante pour faire des requêtes Ajax et avoir une simplification des sélecteurs.

C'est une librairie qui change peu, stable et éprouvée (je l'utilise depuis 20 ans). Contenue dans 30ko compressés, elle permet une souplesse dans le développement pour un poids très contenu.

Un objectif à long terme est de la supprimer en front.

En mode édition, elle permet une manipulation du contenu. jQuery UI est également présenté, pour rendre plus confortables les fonctions d'autocomplétion et de modal. J'ai développé toutes les fonctionnalités d'un éditeur Wysiwyg sans la lourdeur des librairies existantes. Aussi, pour connaître les enjeux et n'avoir aucune dépendance à des systèmes qui ne font que s'alourdir et présenter trop de fonctionnalités inutilisées.

Globalement, la dette technique est très faible et ne nécessite pas de mise à jour, car nous n'avons quasiment pas de dépendances comparé à d'autres systèmes qui aggloméraient les développements externes. On peut se concentrer sur les besoins de nos clients et ne pas passer du temps à juste continuer à faire fonctionner ce qui fonctionne déjà. Ici, nous faisons globalement au maximum une révision annuelle pour le suivi des versions de PHP et Jquery.

Enfin un front en fichier JavaScript est utilisé pour des fonctions de base, tel l'affichage de messages d'erreur, du multilingue, la gestion de cookies, le lazyloading, l'affichage mobile et le lancement du mode édition, le tout pour 10ko (4ko compressé).

### CSS
Un seul fichier en front pour le style du site. Basé sur Knacss, en version allégée. Il contient donc un reste, un système basique de grilles, des conditions de responsive et une librairie d'icônes. Dernièrement, nous avons ajouté des Class pour parfaire l'accessibilité avec un mode contraste renforcé (issu d'un plugin réalisé par Access42). Le tout dans moins de 25ko (6ko compressés). En mode édition, un cas spécifique est chargé.

### Poids et arborescence
Le moteur du site tient dans très peu de fichiers (moins de 50), il pèse 1 Mo avec le thème par défaut, et pourtant gère le multilingue, permet d'éditer le contenu en direct, sans administration complexe. Il propose aussi les outils de base pour un bon SEO, pour optimiser les images et contrôler l'accessibilité du contenu, ceci sans plugins externes.

Le CMS est composé de :
- une API qui, à travers principalement des requêtes Ajax, permet l'ajout, la sauvegarde et l'affichage de contenus.
- un dossier avec le thème qui contient les modèles de page type. Ces modèles font appel à des fonctions de l'API pour créer des zones éditables types (textes, images).
- un fichier Index qui va chercher le contenu et l'affiche en fonction des URL.

Un site de base comprend donc 5 requêtes HTTP : 
- la page HTML (5 à 10ko),
- le CSS (6ko),
- 2 JavaScript (34ko),
- une police d'icônes (20ko). 
Pour 70ko et 5 requêtes de fichiers, 2 requêtes à la base de données MySQL, sans image, nous pouvons servir un site, éditable par les clients.

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

Ce cadre contraint permet aussi de contenir le niveau d'écoconception sans trop de débordement.

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
Autant que possible ! Nous utilisons <a href="https://www.browserstack.com">BrowserStack</a> pour faire nos tests sur plus de 15 plateformes différentes (combinaison de systèmes d'exploitation et navigateurs web) pour que nos sites puissent fonctionner sur des périphériques qui ont plus de 10 ans.
