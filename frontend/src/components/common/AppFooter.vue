<template>
  <footer class="apple-footer">
    <div class="af-container">

      <!-- Top row: brand + copyright -->
      <div class="af-top">
        <div class="af-brand">
          <svg width="18" height="22" viewBox="0 0 814 1000" fill="currentColor">
            <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-43.4-150.3-104.9C157.3 747.5 90 567.7 90 414.6c0-215.7 140.8-330 279.2-330 74.9 0 137.2 51.7 182.1 51.7 42.8 0 110.7-54.8 195.5-54.8 31.4.1 119.8 4.9 162.3 71.4zm-85.4-195.4c34.1-40.2 57.3-96 57.3-151.8 0-7.8-.7-15.7-2.1-22.3-54.3 2.1-118.8 36.9-158.2 80.6-31.2 35.3-58.8 89.4-58.8 146.1 0 8.5 1.4 17 1.9 19.8 3.4.6 8.7 1.4 14.1 1.4 49.4 0 112.2-33.3 145.8-73.8z"/>
          </svg>
          <span class="af-name">Grand Palais</span>
        </div>
        <p class="af-copy">Copyright © 2026 Grand Palais Hôtel. Tous droits réservés.</p>
      </div>

      <div class="af-divider"></div>

      <!-- Link columns -->
      <div class="af-cols">
        <div class="af-col">
          <h4>Découvrir</h4>
          <ul>
            <li><router-link to="/">Accueil</router-link></li>
            <li><router-link to="/rooms">Chambres &amp; Suites</router-link></li>
            <li><router-link to="/services">Services</router-link></li>
            <li><router-link to="/about">À propos</router-link></li>
          </ul>
        </div>
        <div class="af-col">
          <h4>Mon compte</h4>
          <ul>
            <li><router-link to="/login">Connexion</router-link></li>
            <li><router-link to="/register">S'inscrire</router-link></li>
            <li><router-link to="/dashboard/reservations">Mes réservations</router-link></li>
            <li><router-link to="/dashboard">Mon profil</router-link></li>
          </ul>
        </div>
        <div class="af-col">
          <h4>Contact</h4>
          <ul>
            <li>1 Avenue du Palais, Paris</li>
            <li>+33 1 00 00 00 00</li>
            <li>contact@grandpalais.com</li>
            <li>Réception 24h/24</li>
          </ul>
        </div>
        <div class="af-col">
          <h4>Légal</h4>
          <ul>
            <li><span class="af-legal">Politique de confidentialité</span></li>
            <li><span class="af-legal">Conditions générales</span></li>
            <li><span class="af-legal">Mentions légales</span></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// No dynamic data needed — static footer
</script>

<style scoped>
.apple-footer {
  background: #f5f5f7;
  padding: 3rem 0 2rem;
  color: #1d1d1f;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display",
    "SF Pro Text", "Helvetica Neue", Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
}

.af-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* Top row */
.af-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  padding-bottom: 1.25rem;
}
.af-brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #1d1d1f;
}
.af-name {
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: -0.02em;
}
.af-copy {
  font-size: 0.75rem;
  color: #6e6e73;
}

.af-divider {
  height: 1px;
  background: rgba(0,0,0,0.1);
  margin-bottom: 1.75rem;
}

/* Columns */
.af-cols {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
}
@media (max-width: 720px) { .af-cols { grid-template-columns: 1fr 1fr; } }
@media (max-width: 400px) { .af-cols { grid-template-columns: 1fr; } }

.af-col h4 {
  font-size: 0.72rem;
  font-weight: 500;
  color: #1d1d1f;
  margin-bottom: 0.75rem;
  letter-spacing: -0.01em;
}
.af-col ul { list-style: none; padding: 0; margin: 0; }
.af-col li {
  font-size: 0.72rem;
  color: #6e6e73;
  margin-bottom: 0.5rem;
  line-height: 1.5;
}
.af-col a {
  color: #6e6e73;
  text-decoration: none;
  transition: color 0.2s;
}
.af-col a:hover { color: #0071e3; }
.af-legal {
  cursor: pointer;
  color: #6e6e73;
  transition: color 0.2s;
}
.af-legal:hover { color: #0071e3; }
</style>
