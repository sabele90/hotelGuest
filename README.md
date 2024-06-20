# hotelGuest

Symfony Assignment
![alt text](public/images/logo.png)

Project: Hotel Guest Management System
Description
This repository contains the Hotel Guest Management System, a web application designed to streamline the process of guest registration in hotels. The system offers user authentication, guest management, and additional features such as email notifications and rate limiting.

Project Structure

- Database: Manages user information and guest registrations.
- REST API: Provides endpoints for user authentication and guest management.

Entities
1.Guest:
Attributes: Name, Surname, Date of Birth, Gender (M/F/X), Passport Number, Country, User ID who registered the guest.
2.Registration:
Attributes: List of guests, Check-in Date, Check-out Date.
3.User:
Authentication details.

Features

- User Login: Secure authentication using email and password.
- Guest Registration: Users can register guests with their check-in and check-out dates.
- Validation: Ensures data integrity through immediate feedback on form submissions.
- Email Notification: Sends confirmation emails to users upon successful guest registration.
- Rate Limiting: Limits API requests to 5 submissions per minute to prevent abuse.
- Webhook Integration: Submits guest data to an external webhook for further processing.

Usage

1.User Authentication:
Login using the registered email and password.

2.Register Guests:
Navigate to the guest registration form.
Fill in the required guest details.
Submit the form to register guests.

3.View Registrations:
View the list of registered guests and their check-in/check-out dates.

4.Email Notifications:
Users receive confirmation emails upon successful guest registration.

Future Implementations

- Sort registrations by check-in date.
- Display only non-canceled registrations.
- Log and delete canceled registrations periodically using cron jobs.
- Add cancellation field to registrations.
- Add comments field to the guest entity.
- Add email field for guests to receive reservation confirmations.
- Automatic cancellation of registrations after check-out date.
- Add billing field.
- Edit registrations functionality.
- Edit guest personal information functionality.
- View detailed guest information on click.

Known Issues
1.User Authentication: Issues with infinite loops during login. Solution: Restrict access to routes only for users with specific roles.
2.Email Notifications: Automatic email sending implemented but not functional. Investigate mailer configuration.

Conclusion
Developing this project has been a rewarding experience. Working with Symfony for the first time, I have learned a lot and enjoyed the process. 🚀
