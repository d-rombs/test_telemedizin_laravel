# Telemedizin Terminbuchungssystem

Ein modernes Terminbuchungssystem für Telemedizin-Anwendungen, das es Patienten ermöglicht, Termine mit Ärzten verschiedener Fachrichtungen zu buchen und zu verwalten.

## Funktionen

- **Arztsuche**: Suche nach Ärzten basierend auf Namen oder Fachrichtung
- **Terminbuchung**: Auswahl von verfügbaren Zeitfenstern für einen bestimmten Arzt
- **Terminverwaltung**: Anzeigen und Stornieren von gebuchten Terminen

## Nutzung von docker-compose
   ```
   docker-compose up -d
   ```
- Das Backend ist nun unter http://localhost:8001 verfügbar.
- Das Frontend ist nun unter http://localhost:3001 verfügbar.

## Technologien

### Frontend
- React
- TypeScript
- React Router
- Axios für API-Anfragen
- CSS für Styling

### Backend
- Laravel (PHP)
- MySQL Datenbank
- RESTful API

### Tests

# Nur Unit-Tests
   ```
docker-compose exec backend php artisan test --testsuite=Unit
   ```

# Nur Feature-Tests
   ```
docker-compose exec backend php artisan test --testsuite=Feature
   ```

