# **Event Management System**

## **Overview**
The **Event Management System** is a web-based application that allows users to create, manage, and register for events. It provides features for user authentication, attendee registration, event reporting, and an interactive dashboard.

## **Features**

### **Core Features**
✔ **User Authentication**  
- Secure login and registration with password hashing.  
- Users can manage their own events.  

✔ **Event Management**  
- Authenticated users can create, update, view, and delete events.  
- Each event includes details like name, description, and capacity.  

✔ **Attendee Registration**  
- Users can register for specific events.  
- The system prevents registrations beyond the event's maximum capacity.  

✔ **Event Dashboard**  
- Displays all events in a **paginated, sortable, and filterable format**.  
- Users can see **total event capacity, total registered attendees, upcoming events, completed events, and today's events**.  

✔ **Event Reports**  
- Admins can **export attendee lists for specific events** in CSV format.  
- **Download all attendees across all events** in CSV format.  

### **Additional Features**
⭐ **Public Event Registration**  
- Unregistered users can register for events directly from the login page by clicking "View Available Events".  

⭐ **Event Insights on Dashboard**  
- Users can view:  
  - **Total event capacity**  
  - **Total registered attendees**  
  - **Upcoming events**  
  - **Completed events**  
  - **Today's events**  

⭐ **Event Management Page**  
- Displays a table with event details:  
  - **Event name**  
  - **Description**  
  - **Capacity**  
  - **Registered attendees**  
- Users can:  
  - **Edit event details**  
  - **Delete events**  
  - **View attendees**  
  - **Register new attendees**  

---

## **Installation Instructions**

### **1. Clone the Repository**
#### Using GitHub CLI:
```bash
gh repo clone mahedi007/event-management
```
Or using Git:
```bash
git clone https://github.com/mahedi007/event-management.git
cd event-management
```

### **2. Setup the Database**
1. Create a MySQL database named `event_management`.
2. Import the SQL file located in `database/event_management.sql` into your database.

### **3. Configure Database Connection**
1. Open the `config/db_connection.php` file.
2. Update the database credentials with your own:

```php
$host = 'localhost'; 
$username = 'your_db_user'; 
$password = 'your_db_password'; 
$database = 'event_management'; 
```

### **4. Start the Server**
- If using XAMPP, move the project folder to `htdocs` and start Apache & MySQL.
- If using a local PHP server, navigate to the project directory and run:

```bash
php -S localhost:8000
```

### **5. Access the Application**
Open your browser and go to:
```
http://localhost/event-management/
```

Use the test login credentials:
```makefile
Email: user@email.com  
Password: bangladesh24  
```

---

## **Security & Best Practices Implemented**
✅ **Password Hashing** - User passwords are securely stored.  
✅ **Prepared Statements** - Prevents SQL injection.  
✅ **Client-Side & Server-Side Validation** - Ensures valid data input.  
✅ **Role-Based Access Control** - Only authorized users can manage events.  

---

## **Live Demo**
👉 **Live URL:** https://events.whizbd.com

---

## **GitHub Repository**
🔗 **Repo Link:** [https://github.com/mahedi007/event-management](https://github.com/mahedi007/event-management)  
📥 **Clone with GitHub CLI:**
```bash
gh repo clone mahedi007/event-management
```

---

## **Author**
Developed by **S.M. Mahedi Hasan** 🚀
