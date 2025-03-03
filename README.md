# SignupLogin Authentication System

## Overview
SignupLogin is a web-based authentication system that utilizes an image-based login mechanism. Users upload an image during signup, which is segmented into a 3x3 grid. The order of the segments is stored as a graphical password. During login, the user must rearrange the jumbled segments to match the original order to gain access.

## Features
- **User Signup**: Users register with a username, email, password, and an image.
- **Graphical Password**: The uploaded image is divided into a 3x3 grid, and users select the order of segments.
- **Login Authentication**: The system verifies the image order entered during login with the stored order.
- **Session Management**: Users remain logged in until they choose to logout.
- **Secure Authentication**: Prevents unauthorized access by requiring correct image segment order.

## Tech Stack
- **Frontend**: HTML, CSS, Bootstrap
- **Backend**: PHP, MySQL
- **Database**: MySQL

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/signuplogin.git
   ```
2. Move to the project directory:
   ```bash
   cd signuplogin
   ```
3. Set up a MySQL database and import the provided SQL schema.
4. Update `connection.php` with your database credentials.
5. Start a local server using XAMPP, WAMP, or another PHP server.
6. Open the application in a browser:
   ```
   http://localhost/signuplogin/index.php
   ```

## File Structure
```
signuplogin/
├── auth_image.php          # Authentication logic
├── connection.php          # Database connection
├── index.php               # Homepage
├── login.php               # Login page
├── logout.php              # Logout functionality
├── navbar.php              # Navigation bar
├── signup.php              # Signup page
├── style.css               # Stylesheet
├── verify_image.php        # Image verification logic
├── welcome.php             # Dashboard after login
├── images/                 # Stores uploaded and segmented images
│   ├── background.jpg      # Background image
│   ├── grid_x_y_hash.jpg   # Segmented images
```

## Usage
1. **Signup**: Users upload an image and select the segment order.
2. **Login**: Users rearrange the jumbled image to match their stored order.
3. **Dashboard**: Successful login redirects users to `welcome.php`.




## Screenshots

1. Setup and Installation:

   ![image](https://github.com/user-attachments/assets/fc2491ff-2770-4a9f-b4f4-0d1e424ef746)

2. Structure of Database

   ![image](https://github.com/user-attachments/assets/95e44257-d5a9-4b89-85ab-588f0c22c822)

3. GUI Home page

   ![image](https://github.com/user-attachments/assets/4622b3d4-f529-498e-955c-74c876e82725)

4. Signup Page

   ![image](https://github.com/user-attachments/assets/89021593-3554-4ea8-9d14-57ad6c794b6f)

5. Segmented Images

   ![image](https://github.com/user-attachments/assets/9272d542-744b-40f7-8d91-e5f6c4eaf347)

6. Login Page

   ![image](https://github.com/user-attachments/assets/d6ad7e75-bd7b-4413-a8d8-d7ddb009c0be)

7. Arrangement

   ![image](https://github.com/user-attachments/assets/124af559-3665-4984-82bd-1d9f4574ecb9)

8. Welcome page

   ![image](https://github.com/user-attachments/assets/10835630-89f4-4a78-a13f-158ad82ed033)


## Contribution
Contributions are welcome! Feel free to fork the repository and submit pull requests.

## License
This project is licensed under the MIT License.

