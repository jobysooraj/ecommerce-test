# Laravel RESTful API – Products & Orders Management


---

## 🚀 Features

- Product CRUD (Create, Read, Update, Delete)
- Customer registration and login
- Place an order with multiple items
- View customer orders and order details
- Token-based authentication with Laravel Sanctum

---

## 🛠️ Tech Stack

- **PHP 8.2+**
- **Laravel 12**
- **MySQL**
- **Sanctum for API authentication**

---

## 📦 Installation

### 1. Clone the repository


git clone https://github.com/jobysooraj/ecommerce-test.git
cd ecommerce-test

#### Install dependencies
composer install

#### Copy .env and set up environment


cp .env.example to  .env


#### Generate application key

php artisan key:generate

#####  Set up database
php artisan migrate

####  Link storage (if using product images)

php artisan storage:link

###  Run the app
php artisan serve


## 📬 Postman Collection
- Download or import the collection:  
  👉 [Laravel Products & Orders Postman Collection](./postman/laravel-products-orders.postman_collection.json)

### How to Use:
1. Open Postman
2. Click **Import**
3. Choose the downloaded `.postman_collection.json` file
4. Set the base URL as:http://localhost:8000/api


5. Add your authentication token under **Authorization** tab as:

