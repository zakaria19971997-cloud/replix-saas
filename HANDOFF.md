# HANDOFF.md — Replix SaaS Deployment

> Dernière mise à jour : 2026-04-08
> Auteur : Claude Sonnet 4.6 (session de déploiement)

---

## État actuel

| Composant | Statut | URL |
|---|---|---|
| **laravel-app** | Online | https://laravel-app-production-fc07.up.railway.app |
| **whatsapp-server** | Online | https://whatsapp-server-production-36a5.up.railway.app |
| **MySQL 8.0** | Online | Interne uniquement |
| **GitHub repo** | Actif | https://github.com/zakaria19971997-cloud/replix-saas |

---

## Infrastructure

### Railway
- **Project ID** : `76e38591-fcdd-4515-8aca-7c143b87fc35`
- **Environment ID (production)** : `e0af2def-64e3-4f63-b880-b2bf24c52df7`
- **API Token** : `4ea73c51-e0cf-45a7-84bf-89f99e9ce1f4`

| Service | ID Railway |
|---|---|
| laravel-app | `9e67ad29-2657-4138-be78-035dd5c07cbc` |
| whatsapp-server | `3331b863-5e6a-4e14-ae54-b67ed07bac9d` |
| MySQL | `16aa182e-b9e3-41a9-a537-9f35a74896d0` |

### Base de données MySQL
- **Host interne** : `mysql.railway.internal`
- **Port** : `3306`
- **Database** : `replix`
- **User** : `replix_user`
- **Password** : `Replix@2024!Secure`
- **Root password** : `ReplixRoot@2024!`

### GitHub
- **Repo** : `zakaria19971997-cloud/replix-saas`
- **Branche** : `main`
- **Token** : stocké dans le credential store Windows (username: `zakaria19971997-cloud`)

---

## Structure du projet

```
Mywhatsapp/
├── Install/                    ← Laravel 11 (PHP 8.2)
│   ├── Dockerfile              ← Build Docker pour Railway
│   ├── railway.toml            ← Config Railway (healthcheck /health.php)
│   ├── .env                    ← Config locale (ne pas committer)
│   ├── .env.example            ← Template pour Railway
│   ├── public/
│   │   └── health.php          ← Endpoint healthcheck (pas de DB)
│   ├── bootstrap/
│   │   └── app.php             ← trustProxies(*) ajouté pour Railway
│   └── docker/
│       ├── nginx.conf          ← Nginx : route /installer + app Laravel
│       ├── supervisord.conf    ← php-fpm + nginx (queue workers désactivés)
│       └── startup.sh          ← storage:link + supervisord
└── whatsapp_server/            ← Node.js 20 (Baileys/WhatsApp)
    ├── Dockerfile
    ├── railway.toml
    └── config.js               ← Lit les env vars (DB_HOST, etc.)
```

---

## Variables d'environnement Railway (laravel-app)

```env
APP_NAME=Stackposts
APP_ENV=production
APP_KEY=base64:K+Kk11GWjZct7ICQfOBSyikbBrXPqFpmbBpqW7tMrZs=
APP_URL=https://laravel-app-production-fc07.up.railway.app
APP_INSTALLED=false        ← Passer à true après installation
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=replix
DB_USERNAME=replix_user
DB_PASSWORD=Replix@2024!Secure

SESSION_DRIVER=file        ← file avant installation, database après
CACHE_STORE=file           ← file avant installation, database après
SESSION_SECURE_COOKIE=false
TRUSTED_PROXIES=*
QUEUE_CONNECTION=database

THEME_FRONTEND=waziper
THEMES_DIR=resources/themes
```

---

## Prochaine étape OBLIGATOIRE — Installer l'application

**L'application n'est PAS encore installée.** La base de données est vide (aucune table).

### Étape 1 — Lancer l'installateur web
Aller sur : **https://laravel-app-production-fc07.up.railway.app/installer/**

Remplir le formulaire :
- **Purchase code** : le code Envato/StackPosts de la licence
- **Site name** : nom du site
- **Database host** : `mysql.railway.internal`
- **Database name** : `replix`
- **Database username** : `replix_user`
- **Database password** : `Replix@2024!Secure`
- **Admin email / username / password** : à votre choix

L'installateur va :
1. Vérifier la licence sur stackposts.com
2. Créer toutes les tables (migrations)
3. Créer le compte admin
4. Mettre `APP_INSTALLED=true` dans `.env`

### Étape 2 — Après installation, mettre à jour les variables Railway

Dans Railway → laravel-app → Variables, changer :
```
SESSION_DRIVER=database
CACHE_STORE=database
APP_INSTALLED=true
```

### Étape 3 — Réactiver les queue workers

Mettre à jour `Install/docker/supervisord.conf` pour rajouter :
```ini
[program:laravel-queue]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
numprocs=2
process_name=%(program_name)s_%(process_num)02d
stderr_logfile=/var/log/supervisor/laravel-queue.err.log
stdout_logfile=/var/log/supervisor/laravel-queue.out.log
```

### Étape 4 — Connecter le serveur WhatsApp

Dans l'admin Replix → Settings → API Integration → WhatsApp Unofficial :
- **URL** : `https://whatsapp-server-production-36a5.up.railway.app`
- Activer et sauvegarder

---

## Problèmes rencontrés et solutions appliquées

| # | Problème | Cause | Fix |
|---|---|---|---|
| 1 | `composer install` exit 1 | Extensions PHP manquantes (`intl`, `curl`, `dom`, `xml`, `fileinfo`) | Ajout dans Dockerfile |
| 2 | `composer install` exit 1 | `wikimedia/composer-merge-plugin` cherche `modules/*/composer.json` non copiés | `COPY . .` avant composer |
| 3 | `artisan package:discover` échoue | Pas de `.env` pendant le build | `--no-scripts` sur composer install |
| 4 | `bootstrap/cache` inexistant | Dossier absent du repo | `mkdir -p` dans Dockerfile |
| 5 | Vite build échoue | Thème `waziper` → chemin complet requis | `--theme=guest/waziper --theme=app/pico` |
| 6 | Healthcheck échoue | `/` requiert la DB (`options` table) | Endpoint `/health.php` statique |
| 7 | DB inaccessible | `DB_HOST=${{MySQL.MYSQL_HOST}}` non résolu | `DB_HOST=mysql.railway.internal` |
| 8 | `ERR_TOO_MANY_REDIRECTS` | `SESSION_DRIVER=database` sans tables + proxy HTTPS | `SESSION_DRIVER=file` + `trustProxies(*)` |
| 9 | `/installer/` boucle infinie | nginx sert depuis `public/` mais l'installer est dans `installer/` | Règle nginx `location ^~ /installer` → `alias /var/www/html/installer` |

---

## MCPs configurés (`~/.claude/mcp.json`)

```json
{
  "mcpServers": {
    "github":  { "GITHUB_PERSONAL_ACCESS_TOKEN": "<voir credential store Windows>" },
    "vercel":  { "VERCEL_TOKEN": "<voir Railway/Vercel dashboard>" },
    "railway": { "RAILWAY_API_TOKEN": "<voir HANDOFF section Infrastructure>" }
  }
}
```

---

## Déployer un changement de code

```bash
cd c:\Users\USER\Desktop\Mywhatsapp
git add .
git commit -m "description"
git push origin main
# Railway redéploie automatiquement depuis GitHub
```
