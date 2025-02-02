Eventis - Event Management System

Eventis is a web-based event management system that enables users to create, manage, and view events, register attendees, and generate event reports. It features a headless API backend that can be integrated with any frontend, along with a built-in frontend interface.



Features

Core Functionalities

User Authentication:

User registration, login, and password reset.

Event Management:

Create, update, view, and delete events.

Events include details such as name, description, date, location, and capacity.

Attendee Registration:

Online registration form for event attendees.

Capacity control to prevent overbooking.

Event Dashboard:

Overview of events and attendees.

Advanced Features:

Reporting, sorting, and filtering options.

Pagination for large datasets.

Download attendee lists in CSV format.

Headless API:

Fully functional API for backend operations.

Installation

Requirements

PHP 7.4 or higher

MySQL 5.7 or higher

Web server (Apache/Nginx)

Composer (for dependencies)

Node.js (for frontend assets)

Installation Steps

Clone the Repository

git clone https://github.com/dipcb07/eventis.git
cd eventis

Set Proper Permissions
Ensure the web server has the necessary write permissions:

chmod -R 775 /path/to/eventis

Run the Installer
Execute the installation script, which will set up environment variables (.env), .htaccess, database, and other configurations:

[domain]/eventis/installer.php

Access Eventis

Login: [domain]/eventis/login

Register: [domain]/eventis/register

Dashboard: [domain]/eventis/dashboard

Attendee Data: [domain]/eventis/attendee_data

Attend Form: [domain]/eventis/attend
