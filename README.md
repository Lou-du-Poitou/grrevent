# projetweb-l1
**ProjetWeb L1 - Informatique GROUPE INF09**  
Cette application permet de planifier et de référencer des événements. Les événements publiés sur le site peuvent ensuite être ajoutés par les utilisateurs. Les utilisateurs peuvent aussi se suivre les uns les autres et ainsi se tenir informés des nouveaux événements publiés.

> ## Répertoires :

- `/actions/*` : *Actions sur la base de données*
- `/class/*` : *Class utilisables Entités/Utilitaires/Autres*
- `/config/*` : *Fichiers de configurations Constantes/Base de données*
- `/elements/*` : *Composants HTML*
- `/public/*` : *Fichiers public Photos/Icônes*
- `/style/*` : *Feuilles de style*
- `/*` : *Pages accessibles*

> ## Sécurité

- `XSS` : *Attention aux injections de script, les class telles qui User/Event échappent les caractères HTML avec la méthode getHTML()*
- `Uploads` : *Continuer de vérifier et filtrer les fichiers téléchargés afin d'éviter l'injection de scripts malveillants, voir la class UploadFile*
- `Cookies` : *Attention à conserver une configuration sécurisée des cookies, notamment pour les sessions, voir la class Session*
- `CSRF` : *Continuer d'utiliser les token anti-CSRF sur les formulaires qui exploitent la session de l'utilisateur, voir la class CSRFToken*

***/!\ Les fichiers sont documentés, merci de lire cette documentation et de respecter les conventions mises en place.***
