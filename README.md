# 20230420, 
# Merged subscription plan for all segment branch into ms-v2 base code

# Multi-Service Branch 2021-11-08

This branch is creating to write the code for all-in-one (multi-segments).

#Project's Technical summary

1) Laravel version : 9 (see composer.json — older notes claiming 5.6 were stale)
2) PHP version : >=8.1
3) Database : MySQL (legacy) → PostgreSQL (Neon.tech) for deployment


# Project configuration has divided into 2 parts
  A) Vehicle Based/Taxi  (service completes by vehicle)
  B) Helper Based/ Handyman (service completes by person)
  
  
  
# Modules has covered as listed below
1) Taxi Company
2) Driver agency
3) Corporate
4) Discount
5) Referral system
6) Role & permission with area wise
7)  

# Maintenance

The old `/clear-cache`, `/queue-start`, and `/queue-restart` web routes were removed.
Run these from the shell instead:

```bash
php artisan cache:clear      # clear application cache
php artisan route:clear      # clear compiled routes
php artisan view:clear       # clear compiled blades
php artisan config:clear     # clear cached config
php artisan queue:restart    # restart queue workers
php artisan queue:work       # run queue worker locally (production: Render worker service)
```

Health check for uptime monitoring: `GET /health` (returns 200).


 

