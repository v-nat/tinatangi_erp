Tinatangi Cafe - Web-Based ERP with Decision Support Module

Welcome to the official repository for the Tinatangi Cafe Enterprise Resource Planning (ERP) system. This custom-built software is designed to streamline all operational aspects of our beloved cafe, incorporating a Decision Support System (DSS) to empower data-driven management.

📜 About Tinatangi Cafe

"Tinatangi" means "special" or "the only one," and that's the experience we strive to deliver. This ERP system is the digital backbone of our cafe, empowering us to manage everything from the perfect cup of kapeng barako to our valued customer relationships.

🎯 The Problem & Project Context

Prior to this system, Tinatangi Coffee Shop faced significant operational challenges. Manual processes for transactions, inventory, and HR were inefficient, leading to:

Poor inventory management, resulting in shortages and overstocking.

Limited sales analysis and missed opportunities.

Inaccurate attendance tracking and disorganized data storage.

A lack of actionable insights for informed decision-making.

This project aims to solve these problems by developing a centralized, web-based ERP system that automates core functions and provides valuable insights for strategic planning.

✨ System Features

This ERP is a comprehensive suite of tools designed to handle the day-to-day operations of a bustling cafe.

📈 Decision Support System (DSS): Provides real-time, data-driven insights to help owners and managers make smarter operational and strategic decisions. The module helps monitor financial performance, generate reports, and evaluate supplier reliability.

☕ Point of Sale (POS): A fast and intuitive interface for processing customer orders, handling payments (cash, GCash, card), and managing table service.

📦 Inventory & Supply Chain Management: Real-time tracking of ingredients, coffee beans, and supplies. Supports procurement, supplier coordination, and features low-stock alerts to prevent shortages.

👥 Human Resources (HR) & Payroll: Streamlines employee management, scheduling, attendance tracking, payroll integration, hiring, and performance assessment.

🤝 Customer Relationship Management (CRM): Consolidates customer interactions, allowing staff to manage reservations, handle inquiries, record preferences, and manage a loyalty program.

🍽️ Production & Operations: Facilitates menu planning and provides real-time monitoring of order progress from placement to completion.

💰 Finance & Accounting: Manages financial transactions, including payroll processing, ingredient purchasing, and financial reporting for transparency in expenses and revenue.

🛠️ Tech Stack

This project is built on a modern and robust technology stack:

Backend: PHP / Laravel Framework

Frontend: Blade Templates, JavaScript (ES6+), CSS3

Database: MySQL

Server: Nginx / Apache

Development Environment: Laravel Valet / Docker (optional)

🚀 Getting Started (Installation Guide)

Follow these instructions to set up the project on your local development machine.

Prerequisites

Make sure you have the following software installed:

PHP (>= 8.1)

Composer

Node.js & NPM

A database server (e.g., MySQL)

Installation Steps

Clone the repository:

git clone [https://github.com/your-username/tinatangi-erp.git](https://github.com/your-username/tinatangi-erp.git)
cd tinatangi-erp


Install PHP dependencies:

composer install


Install JavaScript dependencies:

npm install


Set up your environment file:

Copy the .env.example file to a new file named .env.

cp .env.example .env

Update the DB_* variables in your .env file with your local database credentials.

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tinatangi_db
DB_USERNAME=root
DB_PASSWORD=your_password


Generate an application key:

php artisan key:generate


Run database migrations and seeders:

php artisan migrate --seed


Compile frontend assets:

npm run dev


Serve the application:

php artisan serve


You can now access the application at http://127.0.0.1:8000.

🔑 Default Login

After seeding the database, you can log in with the default administrator account:

Email: admin@tinatangi.com

Password: password

💡 System Limitations

Initial user training will be required for staff to fully utilize all features.

The Decision Support System (DSS) enhances decision-making through data analysis and reporting rather than advanced predictive modeling.

🤝 Contributing

We welcome contributions! If you'd like to help improve the Tinatangi Cafe ERP, please fork the repository and submit a pull request.

Fork the Project

Create your Feature Branch (git checkout -b feature/AmazingFeature)

Commit your Changes (git commit -m 'Add some AmazingFeature')

Push to the Branch (git push origin feature/AmazingFeature)

Open a Pull Request

📄 License

This project is licensed under the MIT License. See the LICENSE.md file for details.

<p align="center">
Made with ❤️ in the Philippines.
</p>