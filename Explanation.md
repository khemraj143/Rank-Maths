Technical Specification and Design Overview

Homepage Design:
The homepage is designed to serve as the central entry point for users to either register or log in to the system. The design focuses on simplicity and ease of use, allowing users to easily interact with the system while providing dynamic content based on user status (logged in or not).

Key Features:
Video Background: The homepage features a video background that plays a futuristic digital landscape (futuristic-digital-landscape.mp4) to make the page visually appealing. The background video is muted, autoplaying, and looping.

Logo Display: The website dynamically displays the custom logo defined in the WordPress theme settings. This allows for easy customization of the branding without altering the theme’s code.
User Management Interface:

Login and Register Buttons: Displayed prominently for anonymous users. When clicked, a modal appears for the user to input their email and proceed with registration or login.

User Info: For logged-in users, their character's data (such as first name, last name, country, email, and role) is dynamically pulled from the WordPress user system and displayed.
Dynamic Role-Based User Display: Depending on the logged-in user's role (Cool Kid, Cooler Kid, Coolest Kid), they will have access to a list of other users' character data (name, country, email, and role).

Implementation:
Dynamic Content Rendering: WordPress template tags (get_user_meta(), wp_get_current_user(), etc.) are used to pull user data dynamically. Conditional logic is employed to display certain content based on user roles.

AJAX for Login/Register: The login and registration process uses AJAX to submit the user’s data without needing to refresh the page, enhancing the user experience.

REST API Design:
The API endpoint allows the system to programmatically update user roles, enabling admin users to change roles such as Cool Kid, Cooler Kid, or Coolest Kid. This supports integration with external systems that need to interact with user data.

The API for modifying a user's role is designed to authenticate users via JWT (JSON Web Tokens) and allow administrators to assign specific roles to users. The process involves two primary steps:

Generate JWT Token for Authentication
Assign the Role to a User
Step 1: Generate JWT Token
To modify a user's role, we first need to authenticate using a JWT token. This is done by sending a POST request to the following endpoint:

Request: curl --location --request POST 'http://localhost:10053/wp-json/ck_user/jwt/v1/generate-token/?email=test123%40outlook.com'

Explanation:
Endpoint: wp-json/ck_user/jwt/v1/generate-token/
Query Parameter: email (email address of the user to authenticate, e.g., test123@outlook.com).
If the email corresponds to an existing user in the WordPress system, the API will return a JWT token. This token is used for authentication in the next request to assign the user a new role.

Successful Response Example:
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDE5NjM3ODgsImV4cCI6MTc0MTk2NzM4OCwidXNlciI6eyJJRCI6NCwiZW1haWwiOiJ0ZXN0MTIzQG91dGxvb2suY29tIiwibmljZW5hbWUiOiJhbmdyeXNuYWtlMTk4IiwiZGlzcGxheV9uYW1lIjoiYW5ncnlzbmFrZTE5OCJ9fQ.Cby5Bmllb_n0nA_oSazp9aQ647-MLUN-sxagahLC42U",
    "user_email": "test123@outlook.com",
    "user_nicename": "angrysnake198",
    "user_display_name": "angrysnake198"
}

The response includes:
token: The JWT token to be used for authentication in subsequent requests.
user_email: The authenticated user's email.
user_nicename: The user’s "nice" display name.
user_display_name: The user's display name.
Step 2: Assign Role to User
Once we have the JWT token, we can assign a role to the user by sending another POST request to the following endpoint:

Request:
curl --location --request POST 'http://localhost:10053/wp-json/ck_user/v1/set-role/?email=demo14%40test.com&role=Cooler%20Kid' \
--header 'HTTP_AUTHORIZATION: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDE5NjM3ODgsImV4cCI6MTc0MTk2NzM4OCwidXNlciI6eyJJRCI6NCwiZW1haWwiOiJ0ZXN0MTIzQG91dGxvb2suY29tIiwibmljZW5hbWUiOiJhbmdyeXNuYWtlMTk4IiwiZGlzcGxheV9uYW1lIjoiYW5ncnlzbmFrZTE5OCJ9fQ.Cby5Bmllb_n0nA_oSazp9aQ647-MLUN-sxagahLC42U'

Explanation:
Endpoint: wp-json/ck_user/v1/set-role/
Query Parameters:
email (The email of the user to update the role).
role (The new role to assign, which must be one of the following: Cool Kid, Cooler Kid, Coolest Kid).
Authorization Header: The Bearer token generated in step 1 is used for authentication. This is passed as an Authorization header (HTTP_AUTHORIZATION: Bearer <token>).
Successful Response Example:
"Role 'Cooler Kid' has been successfully assigned to ticklishostrich727."


Technical Decisions and Why?
A custom WordPress theme was chosen for this project for the following reasons:

Flexibility: A custom theme allows for complete control over the design and functionality of the website, ensuring that all user-specific features (like the dynamic character display and role-based access) are tightly integrated into the theme.

Simplified User Flow: The homepage and role-based features (login, registration, and dynamic data display) are built from the ground up, making the user experience seamless without needing external plugins or complicated configurations.

Scalability: By creating a custom theme, it is easier to modify and extend the website in the future if additional features (such as more user roles or an enhanced character system) are needed.

Security and Customization: With a custom theme, the website is not dependent on third-party plugins, which could introduce security vulnerabilities. Custom logic for displaying user roles and data ensures the solution is tailored exactly to the business requirements.

Technical Decisions:
AJAX for User Login/Registration: I implemented AJAX for login and registration to avoid page refreshes, creating a smoother, more modern user experience.

Video Background: The decision to use a video background was made to enhance the visual appeal of the homepage, aligning with the futuristic theme of the “Cool Kids Network.”

Role-Based Content Display: Implementing conditional statements to display user data based on roles allows for a tailored experience for different types of users, such as Cooler Kids and Coolest Kids.

WordPress REST API: I used the WordPress REST API to manage the role assignment feature. This provides a flexible and standard method for performing CRUD operations on user roles, which can be extended to integrate with third-party systems.

How the Solution Achieves the Admin’s Desired Outcome

User Story 1: Sign-Up and Generate Character

The user is presented with a simple form to enter their email. Upon submission, the system automatically generates a fake character using the randomuser.me API (first name, last name, country, and role). This fulfills the need for anonymous users to register and have a character created automatically.

User Story 2: User Login and Character Data Display

After logging in, the user’s character data (such as name, email, country, and role) is displayed on the homepage. This ensures users can view their character information once logged in.

User Story 3: Cooler Kid Role Can View Other Users’ Names and Countries

Users with the role of Cooler Kid or Coolest Kid can see a list of all users with their names and countries. This is handled by querying all users with the specified roles and filtering out sensitive information (like email and role) for users who do not have Coolest Kid status.

User Story 4: Coolest Kid Role Can View All User Data

Users with the Coolest Kid role can see a full list of registered users, including their email and role. This is implemented by ensuring that the API and theme logic display this data conditionally based on the user’s role.

User Story 5: Role Assignment via API

The custom API allows administrators to change the role of any user through a simple POST request. This is achieved by an authenticated endpoint that validates the email, first name, and last name before updating the user’s role. This functionality satisfies the requirement for a third-party integration to modify user roles.

Why This Direction is a Better Solution

1. Complete Control Over Functionality
By creating a custom theme and utilizing the WordPress REST API, we have full control over the functionality, ensuring that it is tailored exactly to the project’s needs. We don’t rely on third-party plugins, which could introduce unnecessary complexity or security risks.

2. Simplified User Management
The decision to implement role-based conditional logic directly within the theme allows for easier management of user data and ensures that only users with appropriate roles (like Cooler Kid or Coolest Kid) can access sensitive data.

3. Scalability and Flexibility
The custom theme structure and the REST API provide scalability for future features. If additional user roles or functionalities (such as new character types or more complex role relationships) are needed, they can be added without overhauling the entire system.

4. Efficient User Experience
The use of AJAX for login and registration ensures that users don’t experience page reloads, providing a modern and seamless experience. The video background enhances the site’s aesthetic, making it more engaging for users.

5. Security and Performance
By using the WordPress REST API with proper authentication and validation, the solution is secure and optimized for performance. The logic for role-based data access ensures that only authorized users can view certain information, maintaining data privacy.
