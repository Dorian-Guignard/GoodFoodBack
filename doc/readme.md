#### GOODFOOD ####




### LISTE DES ETAPES BACK ###

# 1: FAIT #

INSTALER SYMFONY ET SES COMPOSANTS

composer create-project symfony/skeleton goodfood
cd goodfood
composer require webapp
mv goodfood/* goodfood/.* .
composer require symfony/security-bundle


composer require twig
composer require maker
composer require annotations
composer require symfony/asset
composer require --dev symfony/profiler-pack
composer require --dev symfony/debug-bundle
composer require --dev symfony/var-dumper
composer require symfony/orm-pack
composer require symfony/form
composer require symfony/validator
composer require security-csrf


# 2: FAIT #

CREATION DE LA BDD VIA ADMINER

# 3: FAIT #

CONFIGURATION DU .env

db name goodfood

user admin

password admin

# 4: FAIT #

CREATION DES ENTITY ET DES RELATIONS (! SAUF POUR LES TABLES USER ET ROLE QUI SERONT AJOUTEES PENDANT LA CONFIGURATION DE LA PARTIE SECURITE)

student@teleporter:/var/www/html/apo/projet-12-recettes-healthy-back$ bin/console make:entity

 Class name of the entity to create or update (e.g. VictoriousPizza):
 > Recipe
 Your entity already exists! So let's add some new fields!

 New property name (press <return> to stop adding fields):
 > category

 Field type (enter ? to see all types) [string]:
 > relation
 What class should this entity be related to?:
 > Category

What type of relationship is this?
 ------------ ---------------------------------------------------------------------- 
  Type         Description                                                           
 ------------ ---------------------------------------------------------------------- 
  ManyToOne    Each Recipe relates to (has) one Category.                            
               Each Category can relate to (can have) many Recipe objects            
                                                                                     
  OneToMany    Each Recipe can relate to (can have) many Category objects.           
               Each Category relates to (has) one Recipe                             
                                                                                     
  ManyToMany   Each Recipe can relate to (can have) many Category objects.           
               Each Category can also relate to (can also have) many Recipe objects  
                                                                                     
  OneToOne     Each Recipe relates to (has) exactly one Category.                    
               Each Category also relates to (has) exactly one Recipe.               
 ------------ ---------------------------------------------------------------------- 

 Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
 > ManyToOne

 Is the Recipe.category property allowed to be null (nullable)? (yes/no) [yes]:
 > no

 Do you want to add a new property to Category so that you can access/update Recipe objects from it - e.g. $category->getRecipes()? (yes/no) [yes]:
 > yes

 A new property will also be added to the Category class so that you can access the related Recipe objects from it.

 New field name inside Category [recipes]:
 > 

 Do you want to activate orphanRemoval on your relationship?
 A Recipe is "orphaned" when it is removed from its related Category.
 e.g. $category->removeRecipe($recipe)
 
 NOTE: If a Recipe may *change* from one Category to another, answer "no".

 Do you want to automatically delete orphaned App\Entity\Recipe objects (orphanRemoval)? (yes/no) [no]:
 > no

 updated: src/Entity/Recipe.php
 updated: src/Entity/Category.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > 


           
  Success! 
           

 Next: When you're ready, create a migration with php bin/console make:migration
 
student@teleporter:/var/www/html/apo/projet-12-recettes-healthy-back$ php bin/console make:migration

# 5:

REMPLISAGE DE LA BDD AVEC DES DONNEES CREATION D UN DOC SQL REPRNANT LES INSTRUCTIONS SQL POUR RECREER LA BDD

# 6:

REALISER LE CRUD VIA make:crud

# 7: 

TEST DU CRUD

lancer un server php:
php -S 0.0.0.0:8000 -t public

# 8:

CONFIGURER L API POUR UN PREMIER ENVOI AUX DEV FRONT







