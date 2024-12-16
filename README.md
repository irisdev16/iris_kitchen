##Déploiement

###Serveur
- Acheter un serveur (dédié, VPS, mutualisé...) sur une plateforme comme OVH.
- Se connecter en SSH pour accéder au serveur distant (Linux) et exécuter des lignes de commandes dans le terminal du serveur.
- Installer PHP, Apache, Composer, MySQL, etc en ligne de commandes et configurer orrectement le serveur (ce que fait MAMP en local).

### Installation du projet Symfony sur le serveur
- Se connecter en SSH dans le dossier du serveur qui est "publique" (le dossier configuré pour être ciblé comme racine pour le web).
- Récupérer le lien du projet â jour sur Github et faire un Git Clone vers cette url dans le dossier du serveur.
- Exécuter la ligne de commandes 'composer install" pour installer sur le serveur toutes les dépendances PHP du projet (Symfony, Doctrine etc) dans le dossier vendor (non veresionné avec git).
- Copier le .env et le coller en .env.local en modifiant la variable d'environnement de la BDD pour mettre les infos du serveur de BBD fourni par OVH.
- Modifier la variable d'environnement APP_ENV pour la passer en "PROD". Cela permet de faire fonctionner le projet en mode production (optimisation des cahces, donc du chargement etc).
- Re-créez le schéma de base de données avec "php bin/console:doctrine:migrations:migrate".
- Videz les caches avec "php bon/console cache:clear --env=prod --no-debug".

###Nom de domaine
- S'assurer que Apache est configuré pour pointer directement dans le dossier public de Symfony
- Acheter un nom de domaine avec un certificat SSL (pour avoir un HTTP plus sécurisé qui s'appelle : HTTPS)
- Relier le nom de domaine à l'adresse IP du serveur
