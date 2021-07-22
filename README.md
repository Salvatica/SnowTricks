# Projet 6 : OpenClassrooms
Développez de A à Z le site communautaire SnowTricks

Sur mon parcours de Développeur PHP/symfony, il m'a été demandé de créer un site collaboratif en PHP/Symfony, sans utiliser de bundle.

## Description du projet 
Le projet est de développer un site professionnel, il se décompose en deux groupes de pages :

les page utilisateurs seront uniquement accessibles aux utilisateurs s'étant inscrits au préalable sur le site.
les pages permettants aux visiteurs de naviguer sur le site.

## le site se décompose comme suit 

-  une page accueil montrant toutes les figures
-  une page figure avec le descriptif et la catégorie
-  une page permettant aux utilisateurs connectés d'ajouter, modifier, supprimer une figure, une image ou une vidéo.
-  une page permettant d'ajouter ou supprimer son commentaire
-  une page inscription
-  une page connexion/déconnexion
-  une page oublie de mot de passe

En fonction des divers statuts des utilisateurs, l'accès sera restreint :

Le visiteur n'a pas de prérequis :

il pourra :
- lire la description des figures
- lire les commentaires

l'utilisateur devra être inscrit : 
il pourra (en plus des actions du visiteur):
-  créer, modifier, supprimer une figure
-  ajouter et supprimer son commentaire
-  se déconnecter

## Partie développement
Le site a été créé en PHP 8 et SYMFONY 5
utilisation de TWIG
Utilisation de bootstrap 4

## Exigences
Vous devez avoir un serveur web et installer PHP8, composer et Mysql 5.7

## Installation

```bash
git clone https://github.com/Salvatica/SnowTricks
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate
php bin/console doctrine:fixtures:load
```

## Vérification
Le code a été vérifié par CODACY

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0191ddcb277d4145be39928a2526ae1f)](https://app.codacy.com/gh/Salvatica/SnowTricks?utm_source=github.com&utm_medium=referral&utm_content=Salvatica/SnowTricks&utm_campaign=Badge_Grade_Settings)
