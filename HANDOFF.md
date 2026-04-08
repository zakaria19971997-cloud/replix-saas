# HANDOFF — Déploiement Stackposts sur Railway

**Date de mise à jour :** 2026-04-08  
**Session :** brave-herschel  
**Branche courante :** `claude/brave-herschel` (mergée dans `main`)

---

## Ce qui a été fait dans cette session

### Diagnostic
- Les 3 services Railway (MySQL, laravel-app, whatsapp-server) avaient status SUCCESS au niveau Railway
- `/health.php` → 200 OK ✓  
- WhatsApp server `/` → `{"status":"success"}` ✓  
- Laravel app `/` → 302 → `/installer/` → **302 en boucle** ✗

### Problèmes identifiés et corrigés

| # | Problème | Fichier modifié | Fix |
|---|----------|-----------------|-----|
| 1 | Boucle infinie `/` ↔ `/installer/` | `nginx.conf` | Ajout `location ^~ /installer` avec `root /var/www/html` |
| 2 | Pas de fichier `.env` au démarrage | `startup.sh` | Génération `.env` depuis les env vars Railway au boot |
| 3 | Router installer ne matche pas `/installer/` | `installer/routes.php` | Enregistrement des routes sur `/`, `/installer`, `/installer/` |
| 4 | `public/index.php` ignorait les env vars Railway | `public/index.php` | Ajout `getenv('APP_INSTALLED')` en priorité sur le fichier `.env` |
| 5 | Queue worker absent | `supervisord.conf` | Ajout `[program:queue-worker]` |
| 6 | `installer/cache/` inexistant (Blade compiler) | `Dockerfile` | Création + chmod du dossier |
| 7 | Champs DB vides dans l'installer | `step3.blade.php` | Pré-remplissage depuis `getenv('DB_HOST/DATABASE/USERNAME')` |
| 8 | `APP_INSTALLED=true` non persisté après install | `installer/routes.php` + Railway env | Appel API Railway post-install + ajout `RAILWAY_API_TOKEN` |

### Commits pushés sur `main`
```
460fef2  merge: resolve nginx.conf conflict, keep root+timeout approach
c3e93da  fix: installer routing loop, .env generation, queue worker
```

---

## État actuel du déploiement

**Railway build en cours** au moment de ce handoff (status `INITIALIZING` → `BUILDING`).

- **MySQL** : ✅ Running  
- **whatsapp-server** : ✅ Running (`https://whatsapp-server-production-36a5.up.railway.app`)  
- **laravel-app** : 🔄 En cours de redéploiement (`https://laravel-app-production-fc07.up.railway.app`)

### Variables Railway configurées (laravel-app)
- `APP_KEY` ✅ défini  
- `DB_HOST=mysql.railway.internal` ✅  
- `APP_INSTALLED=false` ← À changer à `true` APRÈS avoir passé l'installer  
- `RAILWAY_API_TOKEN` ✅ ajouté (utilisé par l'installer pour persister `APP_INSTALLED=true`)  
- `WA_SERVER_URL` ✅ défini  
- `SESSION_DRIVER=file`, `CACHE_STORE=file` ✅

---

## Prochaines étapes

### 1. Attendre la fin du build Railway (~5-10 min)
Vérifier via API :
```bash
curl -s -X POST https://backboard.railway.app/graphql/v2 \
  -H "Authorization: Bearer 4ea73c51-e0cf-45a7-84bf-89f99e9ce1f4" \
  -H "Content-Type: application/json" \
  -d '{"query": "query { service(id: \"9e67ad29-2657-4138-be78-035dd5c07cbc\") { serviceInstances { edges { node { latestDeployment { status } } } } } }"}'
```
Ou tester directement : `https://laravel-app-production-fc07.up.railway.app/installer/`

### 2. Lancer l'installer
Accéder à **`https://laravel-app-production-fc07.up.railway.app/installer/`**

Remplir le formulaire (Step 3 - les champs DB sont pré-remplis) :
- **Purchase code** : [code d'achat Stackposts]
- **Database Host** : `mysql.railway.internal` (pré-rempli)
- **Database Name** : `replix` (pré-rempli)
- **Database Username** : `replix_user` (pré-rempli)
- **Database Password** : `Replix@2024!Secure` (à saisir manuellement)
- **Site Name**, **Timezone**, **Admin Email/Username/Password** : à remplir

### 3. Après l'installation réussie
L'installer appellera automatiquement l'API Railway pour passer `APP_INSTALLED=true`.  
Un redéploiement sera déclenché automatiquement.

Si ça ne se fait pas automatiquement, mettre à jour manuellement via Railway dashboard :
`APP_INSTALLED=true` dans les variables du service `laravel-app`.

### 4. Vérifications post-install
- [ ] `https://laravel-app-production-fc07.up.railway.app/` → page d'accueil
- [ ] `https://laravel-app-production-fc07.up.railway.app/admin` → tableau de bord admin
- [ ] Queue worker actif (logs supervisord)
- [ ] WhatsApp QR code fonctionnel

---

## Blocages potentiels

### Purchase code Stackposts
L'installer vérifie le code d'achat via `https://stackposts.com/api/marketplace/install`.  
**Sans un code valide, l'installation échoue.** Il faut le code d'achat du produit Stackposts.

### Redéploiement après APP_INSTALLED=true
Si le redéploiement automatique via l'API Railway ne se déclenche pas, forcer via :
- Railway Dashboard → laravel-app → Deployments → Redeploy
- Ou modifier une variable Railway pour forcer un nouveau déploiement

---

## Références rapides

| Ressource | Valeur |
|-----------|--------|
| Railway Project ID | `76e38591-fcdd-4515-8aca-7c143b87fc35` |
| Railway Env ID | `e0af2def-64e3-4f63-b880-b2bf24c52df7` |
| laravel-app Service ID | `9e67ad29-2657-4138-be78-035dd5c07cbc` |
| whatsapp-server Service ID | `3331b863-5e6a-4e14-ae54-b67ed07bac9d` |
| Railway API Token | `4ea73c51-e0cf-45a7-84bf-89f99e9ce1f4` |
| GitHub Repo | `zakaria19971997-cloud/replix-saas` |
| Laravel URL | `https://laravel-app-production-fc07.up.railway.app` |
| WhatsApp URL | `https://whatsapp-server-production-36a5.up.railway.app` |
