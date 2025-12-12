# ⭐ Laravel URL Shortener

A clean and scalable URL Shortening platform with Role-Based Access
Control (RBAC)

------------------------------------------------------------------------

## 🎯 Overview

This project is a fully-featured **Laravel URL Shortener** built for
team-based usage with multi-company support. It includes:

-   🔐 Multi-role authentication (SuperAdmin, Admin, Member)\
-   🧩 Spatie Permissions for Role-Based Access Control\
-   🏢 Company-based multi-tenancy\
-   🔗 URL shortening with hit analytics\
-   🎨 Modern Bootstrap UI with SweetAlert

------------------------------------------------------------------------

## 📚 Table of Contents

-   [✨ Features](#-features)\
-   [🔐 Roles & Permissions](#-roles--permissions)\
-   [🏢 Multi-Tenancy Rules](#-multi-tenancy-rules)\
-   [🗄 Database Structure](#-database-structure)\
-   [⚙️ Installation Guide](#️-installation-guide)\
-   [🔑 Default SuperAdmin Login](#-default-superadmin-login)\
-   [📌 How It Works](#-how-it-works)\
-   [🛡 Security](#-security)\
-   [🤝 Contributing](#-contributing)\
-   [📄 License](#-license)

------------------------------------------------------------------------

## ✨ Features

-   Shorten long URLs instantly\
-   Auto-increment hit tracker\
-   Role-based permissions using Spatie\
-   Company-level resource segregation\
-   Admin & Member invitation system\
-   Responsive Bootstrap UI\
-   SweetAlert notifications\
-   Secure redirect mechanism

------------------------------------------------------------------------

## 🔐 Roles & Permissions

### **SuperAdmin**

-   Manage all companies\
-   View/manage all URLs\
-   Manage all invites\
-   Access the dashboard

### **Admin**

-   Manage URLs inside their company\
-   Invite Admins and Members\
-   Manage company-specific invites

### **Member**

-   Manage only the URLs they created\
-   Dashboard access

------------------------------------------------------------------------

## 🏢 Multi-Tenancy Rules

  Role         Own Company     Other Companies   Notes
  ------------ --------------- ----------------- ------------------------------------
  SuperAdmin   ✅              ✅                Full system access
  Admin        ✅              ❌                Cannot view/manage other companies
  Member       Only own URLs   ❌                No invitation permissions

Each URL and invite has a `company_id` to enforce data isolation.

------------------------------------------------------------------------

## 🗄 Database Structure

### Core Tables

-   `users`\
-   `companies`\
-   `short_urls`\
-   `invites`

### Spatie Permission Tables

-   `roles`\
-   `permissions`\
-   `model_has_roles`\
-   `role_has_permissions`\
-   `model_has_permissions`

------------------------------------------------------------------------

## ⚙️ Installation Guide

### 1️⃣ Clone the repository

``` bash
git clone https://github.com/ajaysharmaofficial/urlshortener.git
cd urlshortener
```

### 2️⃣ Install dependencies

``` bash
composer install
npm install && npm run build
```

### 3️⃣ Setup environment variables

``` bash
cp .env.example .env
php artisan key:generate
```

### 4️⃣ Run migrations & seeders

``` bash
php artisan migrate:fresh --seed
```

### 5️⃣ Start development server

``` bash
php artisan serve
```

------------------------------------------------------------------------

## 🔑 Default SuperAdmin Login

  Email                      Password
  -------------------------- --------------
  **superadmin@gmail.com**   **12345678**

------------------------------------------------------------------------

## 📌 How It Works

### 🔗 Creating Short URLs

-   Users can generate short URLs with a simple form.\
-   Each URL is stored with the creator and company ID.

### 🔁 Redirection Route

    GET /{short_key}

-   Finds the URL\
-   Increments the hit counter\
-   Redirects to the original URL

### 📩 Invitation Flow

-   Admins & SuperAdmins can send invitations\
-   User accepts and registers\
-   System auto-assigns role and company

------------------------------------------------------------------------


## 🧪 Test Suite (Feature + Unit Tests)

This project includes a fully automated PHPUnit Test Suite to ensure:
- Role-based access control works correctly
- Company-level multi-tenancy isolation
- SuperAdmin/Admin invitation policies
- URL creation, validation & permissions
- Short urls are notpublicly resolvable and redirect to the original url
- Authentication & Registration Flow

------------------------------------------------------------------------

## 🛡 Security

-   RBAC-protected routes\
-   URL validation\
-   Token-based invite system\

------------------------------------------------------------------------

## 🤝 Contributing

Pull requests are welcome!\
For major changes, open an issue first to discuss what you'd like to
change.

------------------------------------------------------------------------

## 📄 License

This project is open-source and licensed under the **MIT License**.

## 👨‍💻 Developed By  
**Ajay Sharma**  
🔗 LinkedIn: https://www.linkedin.com/in/ajaysharmaofficial

