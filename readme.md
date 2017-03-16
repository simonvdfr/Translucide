# [CMS Translucide](http://www.translucide.net) - Léger et puissant

J'ai créé le CMS Translucide pour répondre à certains besoins personnels que j'ai en tant que développeur et intégrateur, mais aussi pour permettre aux clients de modifier plus simplement leur site sans casser le travail graphique fait en amont.

En clair je voulais un CMS plus simple d'approche que Wordpress, plus facilement customisable, plus léger, plus rapide à l'exécution, qui va plus à l'essentiel, le tout avec le moins de dépendance possible (uniquement jQuery, jQuery UI, Font Awesome). Le moteur du site tient en très peu de fichiers, et pourtant il fait déjà pas mal de choses.

## Installation
Décompresser les fichiers du site sur votre FTP et avec votre navigateur aller à l'adresse où se trouve `index.php`
Entrer les infos de connexion à la base de données, sélectionner votre template de site (graphisme) et enfin entrer le mail et le mot de passe qui servira à administrer le site au lancement.

Après, au besoin, vous pouvez éditer manuellement le fichier `config.php`, générer à la suite de l'installation. Il contient les informations uniques et cruciales au bon fonctionnement du CMS.

## Premier pas
- Descendez tout en bas du site, sur la droite doit apparaître un bouton `+` pour ajouter une page
- Une fois la page créée (vous lui avez donné un titre et une template, idéalement `home`), vous pouvez l'éditer en cliquant en bas à droite sur le crayon d'édition.

## Raccourcis clavier
- <kbd>⇧</kbd> force l'affichage du bouton édition en bas à droite

### Raccourcis en mode édition
- <kbd>ctrl + s</kbd> sauvegarde les changements
- <kbd>ctrl + z</kbd> annule la dernière action dans les blocs textes

## Utilisation de l'éditeur du menu de navigation
- Lorsque vous modifiez du contenu qui se trouve dans <header> ou <footer>, ça le modifie pour toutes les pages du site.
- Lorsque vous passez la souris dans le header une boîte s'ouvre vous proposant les pages pas encore présentes dans le menu.
- Un clic sur le `+` vous permet d'ajouter l'élément au menu, ou sinon vous pouvez faire un drag&drop
- Une fois dans le menu, au survol d'un élément une zone en pointillés apparaît au-dessus pour pouvoir le déplacer en drag&drop.
- Si vous saisissez un élément et que vous le glissez dans la boîte d'ajout, ça se transforme en poubelle, pour supprimer l’élément du menu.

## Balises maison
- `text()`
- `img('nom-de-la-zone' [,'100x100'])` L'argument final et optionnel, il force une taille
- `bg()` A placer dans un <div> ou autres pour rendre l'image de fond éditable
....

## Gestion du multilingue
Le CMS est prévu pour accueillir des traductions mais tout n'est pas fini/testé. Globalement les traductions de l'interface du CMS se trouvent dans le fichier `api/translation.php`

## Configuration du système de connexion tierce (Facebook, Google, Yahoo, Hotmail/Outlook)
Bientôt ... :)

## Wordpress VS Translucide
J'ai tenté de passer un site que j'avais fait sous Wordpress sous Translucide avec les mêmes fonctionnalités et aspect visuel et le résultat était plutôt édifiant.
Pour le même site on est passé de plus de 1000 fichiers avec Wordpress à moins de 100 images comprises pour Translucide. Et de 50 mégaoctet à 5 mégaoctet pour la version CMS Translucide. Niveau temps d'exécution en général on diviste par 2 le temps de chargement d'une page est ceci sans utiliser de système de cache spécifique. Combattons l'obésiciel ensemble :)


## FAQ

### Mais où est l'administration ?
Je voulais aussi en finir avec les interfaces d'administration qui je trouve est un frein pour les utilisateurs finaux. Pourquoi ne pas modifier directement les contenus directement dans le site ?

### Pourquoi on ne peut pas tout modifier avec l'éditeur live ?
Pour bien faire la différence entre le travail de développeur|graphiste|intégrateur et les besoins d'édition du client au final.
Pour ça j'ai créé un moteur de site qui permet d'éditer en live les contenus, on modifie directement le site tel qu'on le voie en tant que visiteur. Mais on ne peut pas forcément modifier la position, la taille de tous les blocs de contenus.

#### Pourquoi ?
Parce que le CMS est plus fait à la base pour des développeurs débutants, intégrateurs, graphistes, qui peuvent modifier directement le HTML pour faire des mises en page complexes, et après l'utilisateur final modifie les contenus.

Peut-être qu'un jour je coderais un builder qui permet de tout modifier, mais le temps à coder ça, et l'utilisation qui en sera faite au final n'est pas forcément très rentable et le rendu ne sera jamais aussi impressionnant et propre que si c'est fait en dur dans le code HTML.

### Comment modifier les contenus
Pour passer en mode édition il faut cliquer en bas à droite sur l'icône avec le crayon. S'il n'est pas visible descendez jusqu'en bas du site ou appuyez sur la touche <kbd>⇧</kbd> du clavier pour le faire apparaître.

### Comment se connecter au système d'édition ?
Si vous avez entré ce qu'il faut dans la configuration vous pouvez utiliser votre compte Facebook, Google ou autres. Plus besoin de retenir un mot de passe d'administration en plus.
Sinon utilisez le compte admin créé lors de l'installation ou ajoutez un compte utilisateur avec le gestionnaire d'utilisateur en haut à gauche de la barre qui apparaît en mode édition.

### Comment changer ou ajouter une image
On peut drag&drop les images dans le gestionnaire de média (icône média dans la barre d'outil lorsque l'on clique dans un bloc texte) ou dans les contenus dans les zones prédéfinies.

### C'est customisable ?
Nativement le CMS fait pas mal de choses, et ajouter une fonctionnalité est plutôt simple. J'ai codé le tout en procedural, avec peu de fichiers pour ne pas chercher des heures ou se trouve les choses.
Vous pouvez ajouter tout ce que vous voulez dans le dossier `theme` qui contient votre template visuel. 
Le dossier `plugin` est plus fait pour des fonctions génériques pas forcément liées spécifiquement à votre template. En général on fait un simple `include` du fichier contenu dans `plugin`.

### À qui s'adresse ce CMS ?
Le processus est plus fait pour qu'un graphiste|intégrateur ou/et un développeur web construisent une template simple en HTML et y intégrent les 4/5 balises de contenu type là où ils veulent que ça soit éditable. Le CMS fait le reste pour que l'utilisateur final puisse modifier en live son site avec le moins de clic possible.

### Est-il multiplate-forme et responsive ?
Autant que possible ! J'utilise <a href="https://www.browserstack.com"><img src="http://img4.hostingpics.net/pics/737313browserstacklogo.png" alt="browserstack"></a> pour faire mes tests.