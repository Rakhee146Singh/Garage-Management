## The Garage Project 

INSTAll:  composer create-project laravel/laravel new-project

## User Types: Admin,Owner,Mechanic,Customer

# use seeder for Admin 
  php artisan db:seed
  
## Database Table List
1. Country
2. State
3. City
4. Service Types
5. Garage
6. Garage User (pivot table)
7. Garage Service Type(pivot table)
8. User
9. User Service Type(pivot table)
10. Car
11. Car servicing
12. Car servicing jobs

# CRUD Api for:(Admin)
1. Country
2. State
3. City
4. Service Type 
5. User(Owner)

# CRUD Api for:(Owner)
1. User (Customer,Mechanic)
2. Garage
3. Car (Customer,Owner)
4. Car Service
5. Car Service Jobs

## PostMan Collection Link
https://documenter.getpostman.com/view/25052728/2s93K1ozQA
