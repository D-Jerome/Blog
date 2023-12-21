# Projet Open Classrooms de blog

Information du projet
Projet de la formation Développeur d'application - PHP / Symfony.
----------------------------------------------------------------------------------------
## Créez votre premier blog en PHP

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/509a2ed4471249838004b6aec1d100e7)](https://app.codacy.com/gh/D-Jerome/Blog/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

Voici les principales fonctionnalités disponibles suivant les différents statuts utilisateur:

* Le visiteur:

    * Visiter la page d'accueil et ouvrir les différents liens disponibles (compte GitHub, compte Linkedin).
    * Envoyer un message au créateur du blog.
    * Parcourir la liste des blogs et parcourir la liste de ses commentaires.

* Le visiteur (enregistré):

    * Prérequis: s'être enregistré via le formulaire d'inscription.
    * Accès aux mêmes fonctionnaités que le visiteur.
    * Ajout de commentaires et modifications des ses propres commentaires.

* L'editeur (enregistré):

    * Prérequis: s'être enregistré via le formulaire d'inscription et avoir les droits 'editor' donnés par l'administrateur.
    * Accès aux mêmes fonctionnaités que le visiteur (enregistré).
    * Ajout de Post et modifications des ses propres posts.

* Administrateur:

    * Prérequis: avoir le status administrateur.
    * Accès aux mêmes fonctionnalités que l'editeur.
    * Ajout/suppression/modification/Publication/dépublication ded posts.
    * Modification/Publication/Dépublication des commentaires.
----------------------------------------------------------------------------------------
### Informations

Un thème de base a été choisi pour réaliser ce projet, il s'agit du thème Bootstrap Clean-blog.

La version en ligne n'est pas encore disponible.

----------------------------------------------------------------------------------------
### Prérequis

Php ainsi que Composer doivent être installés sur votre serveur afin de pouvoir correctement lancé le blog.
Une base de données Mysql pour le stockage des données.
----------------------------------------------------------------------------------------
### Installation

* **Etape 1** : Cloner le Repositary sur votre serveur.

* **Etape 2** : Pour créer la base de données sur votre SGBD, il suffit d'importer le fichier bdd-query.sql. 

* **Etape 3** : Remplir le fichier Config/config.json avec les accès à votre BDD.

* **Etape 4** : Remplir le fichier Config/config.json avec les accès à votre compte email.

* **Etape 5** : Remplir le fichier Config/config.json avec le chemin de base votre site.

* **Etape 6** : Votre blog est désormais fonctionnel.

----------------------------------------------------------------------------------------
### Mise en place du compte administrateur

* **Etape 1** : Inscrivez-vous ( Role visiteur à l'inscription)

* **Etape 2** : Dans la base de données , exécutez la requete suivante afin de prendre les droits d'administrateur:
            " UPDATE TABLE user set role_id = 1 WHERE username = " Puis mettre votre identifiant créé précédement.

* **Etape 3** : Votre compte administrateur est désormais opérationnel.

----------------------------------------------------------------------------------------
## Librairies utilisées

    -Twig + Extra-bundle + intl-extra (Template de pages)
    -tinyMce (Mise en forme de texte (avec images, couleurs...))
    -phpmailer (transmission d'email)
----------------------------------------------------------------------------------------
## Auteur

Dubus Jérôme - Étudiant à Openclassrooms - Développeur d'application PHP/Symfony
