# Grand Palais - Système de Gestion Hôtelière Moderne

Ce projet est une plateforme complète de gestion hôtelière développée dans le cadre d'un Projet de Fin d'Études (PFE). Elle intègre des technologies modernes pour offrir une expérience premium aux clients et une gestion efficace pour les administrateurs.

## 🚀 Fonctionnalités Clés

- **Tableau de Bord Administratif** : Gestion des réservations, des chambres et des utilisateurs avec une interface moderne.
- **Visualisation 3D** : Modèle interactif 3D de l'hôtel pour la sélection des chambres en temps réel.
- **Concierge IA** : Intégration d'un agent intelligent via WhatsApp pour répondre aux besoins des clients.
- **Gestion des Réservations** : Flux complet de réservation, check-in et check-out.
- **Design Premium** : Interface utilisateur soignée avec Vue.js et CSS moderne.

## 🛠️ Stack Technique

- **Backend** : Laravel 10+ (PHP)
- **Frontend** : Vue.js 3 avec Vite
- **Base de données** : MySQL
- **IA** : Intégration Groq/OpenAI (Service Concierge)
- **3D** : Three.js / Modèles GLTF

## 📦 Installation

### Prérequis
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL (XAMPP/WAMP)

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone <url-du-depot>
   cd new-pfe
   ```

2. **Configuration Backend**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
   *Note : Configurez votre base de données dans le fichier `.env`.*

3. **Migrations et Données**
   ```bash
   php artisan migrate --seed
   ```

4. **Configuration Frontend**
   ```bash
   cd frontend
   npm install
   ```

## 🚀 Lancement

### Backend
```bash
php artisan serve
```

### Frontend
```bash
cd frontend
npm run dev
```

---
*Développé avec ❤️ pour le PFE.*
