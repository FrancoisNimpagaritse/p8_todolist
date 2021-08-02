Projet 8 - Améliorez une application existante de ToDo & Co
===========================================================

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/44794a8896c24a59a8ba56219678bcca)](https://www.codacy.com/gh/FrancoisNimpagaritse/p8_todolist/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=FrancoisNimpagaritse/p8_todolist&amp;utm_campaign=Badge_Grade)

Dans mon parcours de Développeur d'application PHP/Symfony chez OpenClassrooms j'ai travaillé sur le projet: Améliorez une application existante de ToDo & Co. 

Informations sur l'environnement et outils utilisés durant le développement
--------------------------------------------------------------------------- 
    * PHP 7.2.8
    * Symfony 5.2
    * Composer
    * MySQL 5.7 
    * PHPUnit 8.5
    * Codacy
    * Blackfire

Installation
-------------- 

    1. Clonez ou téléchargez le repository GitHub dans le dossier voulu :
    git clone https://github.com/FrancoisNimpagaritse/p8_todolist
    2. Configurez vos variables d'environnement tel que la connexion à la base de données dans le fichier .env.local qui devra être crée à la racine du projet en réalisant une copie du fichier .env et env.test

    3. Téléchargez et installez les dépendances de l'application avec Composer :

        composer install

    4. Créez la base de données taper la commande ci-dessous (et les refaire avec l'option --test)en vous plaçant dans le répertoire du projet :

        php bin/console doctrine:database:create
    
    5. Créez les différentes tables de la base de données en appliquant les migrations (et les refaire avec l'option --test):
        php bin/console doctrine:migrations:migrate

    6. Charger les données de tests avec la commande (et les refaire avec l'option --test):

        php bin/console doctrine:fixtures:load

    7. L'application est installé, vous pouvez commencer à travailler dessus !

Bon travail
-------------