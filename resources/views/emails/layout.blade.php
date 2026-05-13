<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Grand Palais')</title>
  <style>
    /* Inline styles for maximum compatibility */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { 
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
      background-color: #FAF8F5; 
      color: #0f172a; 
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }
    .wrapper { 
      max-width: 640px; 
      margin: 0 auto; 
      background-color: #ffffff; 
      box-shadow: 0 4px 50px rgba(0,0,0,0.03);
    }
    
    /* Header */
    .header {
      padding: 50px 20px;
      text-align: center;
      background-color: #ffffff;
      border-bottom: 1px solid #f8fafc;
    }
    .logo-container {
      margin-bottom: 15px;
    }
    .logo-img {
      height: 60px;
      width: auto;
    }
    .header-tagline {
      font-size: 10px;
      letter-spacing: 0.4em;
      text-transform: uppercase;
      color: #b45309;
      font-weight: 800;
    }

    /* Hero */
    .hero {
      padding: 60px 48px;
      text-align: center;
      background-color: #ffffff;
    }
    .hero-label {
      display: inline-block;
      font-size: 10px;
      font-weight: 800;
      color: #b45309;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      margin-bottom: 24px;
      border-bottom: 1px solid #b45309;
      padding-bottom: 4px;
    }
    .hero-title {
      font-family: 'Georgia', serif;
      font-size: 38px;
      color: #0f172a;
      margin-bottom: 16px;
      font-weight: 400;
      line-height: 1.2;
    }
    .hero-subtitle {
      font-size: 15px;
      color: #64748b;
      max-width: 440px;
      margin: 0 auto;
      font-weight: 300;
    }

    /* Content Area */
    .content {
      padding: 0 48px 60px;
    }
    .text-p {
      font-size: 15px;
      color: #334155;
      margin-bottom: 30px;
      line-height: 1.8;
    }
    
    /* Card/Details */
    .premium-card {
      background-color: #f8fafc;
      border: 1px solid #f1f5f9;
      border-radius: 16px;
      padding: 32px;
      margin-bottom: 32px;
    }
    
    /* Buttons */
    .btn-container {
      text-align: center;
      margin-bottom: 50px;
    }
    .btn-gold {
      display: inline-block;
      background-color: #b45309;
      color: #ffffff !important;
      padding: 20px 50px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      box-shadow: 0 10px 20px rgba(180, 83, 9, 0.15);
      transition: all 0.3s ease;
    }

    /* Footer */
    .footer {
      padding: 60px 48px;
      background-color: #0f172a;
      text-align: center;
      color: #ffffff;
    }
    .footer-logo {
      font-size: 16px;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      color: #ffffff;
      margin-bottom: 24px;
      font-weight: bold;
      font-family: 'Georgia', serif;
    }
    .footer-links {
      margin-bottom: 30px;
    }
    .footer-link {
      font-size: 11px;
      color: rgba(255, 255, 255, 0.6);
      text-decoration: none;
      margin: 0 15px;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }
    .footer-copy {
      font-size: 10px;
      color: rgba(255, 255, 255, 0.4);
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    @media only screen and (max-width: 600px) {
      .content { padding-left: 24px; padding-right: 24px; }
      .hero { padding-left: 24px; padding-right: 24px; }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <!-- Header -->
    <div class="header">
      <div class="logo-container">
        <img src="{{ $message->embed(public_path('images/logo-premium.png')) }}" alt="Grand Palais" class="logo-img">
      </div>
      <div class="header-tagline">HÃ´tel de Prestige â MAD¢ Paris</div>
    </div>

    @yield('content')

    <!-- Footer -->
    <div class="footer">
      <div class="footer-logo">Grand Palais</div>
      <div class="footer-links">
        <a href="#" class="footer-link">RÃ©servations</a>
        <a href="#" class="footer-link">Services</a>
        <a href="#" class="footer-link">Conciergerie</a>
      </div>
      <div class="footer-copy">
        &copy; {{ date('Y') }} Grand Palais Palace â MAD” 1 Avenue du Grand Palais, 75008 Paris
      </div>
    </div>
  </div>
</body>
</html>
