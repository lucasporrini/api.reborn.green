# Se déplacer à la racine du projet
cd ./

# Ajouter la date dans les fichiers de log
date >> logs/auto/deploy.log

# Exécuter les commandes git et rediriger les sorties vers les fichiers de log
git pull origin main >> logs/auto/deploy.log

# Retour au répertoire initial si nécessaire
cd -