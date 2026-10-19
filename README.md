```{=html}
<p align="center">
```
`<img src="docs/images/project-banner.png" alt="Multi-Vendor Ecommerce Store" width="100%">`{=html}
```{=html}
</p>
```
```{=html}
<h1 align="center">
```
Multi-Vendor Ecommerce Store
```{=html}
</h1>
```
```{=html}
<p align="center">
```
A multi-vendor ecommerce platform built with Laravel, Livewire, and
Filament.
```{=html}
</p>
```
```{=html}
<p align="center">
```
`<img src="https://img.shields.io/badge/Laravel-PHP-red?logo=laravel" alt="Laravel">`{=html}
`<img src="https://img.shields.io/badge/Livewire-Frontend-purple" alt="Livewire">`{=html}
`<img src="https://img.shields.io/badge/Filament-Admin%20Panel-orange" alt="Filament">`{=html}
`<img src="https://img.shields.io/badge/Status-In%20Development-blue" alt="Project status">`{=html}
```{=html}
</p>
```

------------------------------------------------------------------------

## 📌 About the Project

The **Multi-Vendor Ecommerce Store** is a web application where multiple
vendors can manage their products while customers browse products,
choose available variants, add items to their cart, and proceed through
checkout.

The project includes a customer-facing storefront and an administrative
product-management workflow. Administrators can review vendor-submitted
products and control their publishing status.

> **Note:** This README describes the project structure and features
> currently represented in the code. Features listed under "Planned
> Enhancements" are ideas for future development and should not be
> considered implemented yet.

## 🖼️ Screenshots

Add your actual application screenshots to `docs/images/` and use the
filenames below. GitHub will display the images once those files exist
in the repository.

### Storefront Home Page

![Storefront home page](docs/images/home-page.png)

### Product Details and Variants

![Product details page showing product images and
variants](docs/images/product-details.png)

### Shopping Cart

![Shopping cart page](docs/images/cart.png)

### Checkout

![Checkout page](docs/images/checkout.png)

### Admin Product Management

![Admin product management screen](docs/images/admin-products.png)

## ✨ Features

### Customer Storefront

-   Browse the product catalog and categories.
-   View product details, descriptions, and product images.
-   Select product variants such as size and color.
-   Display a variant image when one is available.
-   Add selected variants to the shopping cart.
-   Adjust quantities and remove cart items.
-   Proceed to checkout and provide checkout details.

### Vendor Product Management

-   Associate products with vendors.
-   Manage product information and descriptions.
-   Add multiple product images and choose a primary image.
-   Create product variants with individual SKUs.
-   Configure size, color, price, compare-at price, and stock quantity.
-   Submit products for administrator review.

### Admin Product Review

-   Manage products and categories.
-   Review submitted products.
-   Approve or reject products.
-   Set product publishing status.
-   Record review information and rejection reasons.

### Product Images and Variants

-   **Product gallery:** Stores multiple photos of a product, including
    its primary image.
-   **Variant image:** Stores an image for a specific variant, such as a
    particular color.
-   **Variant inventory:** Tracks SKU, price, and available stock for
    each variant.

## 🧰 Technology Stack

  Technology           Purpose
  -------------------- ----------------------------------------
  Laravel              Backend application framework
  PHP                  Server-side language
  Livewire             Interactive Laravel components
  Blade                Server-rendered templates
  Filament             Administrative interface
  Laravel Filesystem   Product image storage
  Git and GitHub       Version control and repository hosting

> Update this table if your installed versions or implementation differ.

## 🗂️ Project Structure

The following is a high-level overview of the main areas used by the
application:

``` text
app/
├── Filament/
│   └── Resources/
│       └── Products/
│           └── Schemas/
├── Livewire/
│   └── Storefront/
├── Models/
└── Services/

config/
database/
├── migrations/
└── seeders/

resources/
└── views/
    ├── components/
    │   └── layouts/
    └── livewire/
        └── storefront/

docs/
└── images/
```

## ⚙️ Installation

### Requirements

Install the prerequisites appropriate for your Laravel version: - PHP
and required PHP extensions - Composer - Node.js and npm - A supported
database such as MySQL or PostgreSQL

### 1. Clone the Repository

Replace `<your-repository-url>` with your GitHub repository URL.

``` bash
git clone <your-repository-url>
cd factory-app
```

### 2. Install Dependencies

``` bash
composer install
npm install
```

### 3. Configure Environment

``` bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and configure your database connection and other required
environment variables.

### 4. Run Database Migrations

``` bash
php artisan migrate
```

If the project has demo seeders and you want sample data, run the
relevant seeder after checking its contents. For example:

``` bash
php artisan db:seed --class=DemoProductsSeeder
```

Only run the example seeder if that class exists and is appropriate for
your environment.

### 5. Link Public Storage

``` bash
php artisan storage:link
```

### 6. Build Frontend Assets

For a production asset build:

``` bash
npm run build
```

For local development with asset watching, use:

``` bash
npm run dev
```

### 7. Start the Laravel Development Server

``` bash
php artisan serve
```

Open the local URL printed by the command in your browser.

## 🧪 Development Notes

-   Keep `APP_URL` consistent with the hostname used to access the
    application during local development.
-   Product image files uploaded to the public disk should be accessible
    through Laravel's storage URL.
-   Ensure that product variants have valid SKUs, prices, and stock
    quantities.
-   Test cart quantity validation against the selected variant's
    available stock.
-   Do not commit `.env`, credentials, API keys, or other secrets to
    GitHub.

## 🚀 Planned Enhancements

The following are possible future improvements and are not claimed as
completed features: - Vendor dashboards and sales reports. - Order
management and vendor-specific order processing. - Payment gateway
integration. - Vendor commissions and payouts. - Customer accounts and
order history. - Email notifications and background jobs. - Product
reviews and ratings. - Vendor subscription plans and recurring billing.

## 🤝 Contributing

1.  Create a feature branch.
2.  Make and test your changes.
3.  Commit your work with a descriptive message.
4.  Open a pull request with a summary of the changes.

## 📄 License

Add the license that applies to this project before distributing it
publicly.

------------------------------------------------------------------------

```{=html}
<p align="center">
```
Made with Laravel
```{=html}
</p>
```