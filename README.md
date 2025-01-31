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
