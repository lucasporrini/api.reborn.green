# Description: Script pour faire un git pull
git pull origin main
# ecrire dans webhooks.log 
echo "git pull origin main - le $(date)" >> /var/www/html/webhooks.log