# Table of Contents

1. [Mystic Micro Sites](#mystic-micro-sites)
2. [Features](#features)
3. [Use Cases](#use-cases)
  - [Secure Digital Memorabilia](#secure-digital-memorabilia)
  - [Private Collections and Exhibitions](#private-collections-and-exhibitions)
  - [Event Invitations and Information](#event-invitations-and-information)
4. [Installation](#installation)
  - [Prerequisites](#prerequisites)
  - [Setup Process](#setup-process)
    - [Clone the Repository](#clone-the-repository)
    - [Install PHP Dependencies](#install-php-dependencies)
    - [Environment Setup](#environment-setup)
    - [Build the Frontend](#build-the-frontend)
    - [Web Server Configuration](#web-server-configuration)
5. [Usage](#usage)
  - [Access the Editor](#access-the-editor)
  - [Site Generation](#site-generation)
  - [Site Access](#site-access)
6. [Configuration Details](#configuration-details)
  - [Angular Configuration](#angular-configuration)
  - [Backend Configuration](#backend-configuration)
7. [Security Features](#security-features)
8. [Future Enhancements](#future-enhancements)
9. [Contribution](#contribution)
10. [License](#license)

# Mystic Micro Sites

Mystic Micro Sites is a secure and efficient platform for creating mini-websites that are stored encrypted on the server. Access is granted through a unique URL containing a randomly generated password, ensuring that only users with the correct link can access the content. This project includes a frontend built with Angular and a backend implemented in PHP 8.3.

![screenshot of editor](mysticmicrosites-editor-screen.jpg)

## Features

- **Advanced Encryption**: Utilizes a combination of public/private key encryption, symmetric encryption, and Argon2ID hashing for secure data management.
- **JWT Authentication**: Employs JSON Web Tokens for secure API interactions.
- **Rate Limiting and CSRF Protection**: Implements safeguards against misuse and attacks.
- **Obfuscation**: Obfuscation the main view URL, so it's not accessible by reload (after 10 seconds).
- **Automatic Password Management**: Generates and embeds unique passwords in URLs for secure site access.
- **Master Password Recovery**: Allows site recovery using a master password stored on the server.
- **NFC Tag Integration**: Enables NFC tags to link physical objects to digital content, providing an interactive way to access site details.
- **Modular Codebase**: Designed for easy extension and customization, making it suitable for various development needs.
- **Custom Template Engine**: Small high functional Template Engine for dynamic contet rendering, build from scratch.
- **QR Codes:**: Generates QR Codes as downloadable Image of the generated Mystic URL.

## Use cases

1. **Secure Digital Memorabilia**

   - **Scenario:** A user wants to create a digital memory archive linked to physical objects like souvenirs or gifts.
   - **Solution:** By integrating **NFC tags** or **QR codes** with Mystic Micro Sites, users can securely store and share digital content like photos, videos, or stories. When the tag or code is scanned, the content is accessed through an encrypted URL, ensuring privacy and security.
   - **Benefit:** This allows for private sharing of memories with friends or family without the risk of unauthorized access.


3. **Private Collections and Exhibitions**

   - **Scenario:** A collector wants to showcase their collection of rare items or artworks to a select group of individuals.
   - **Solution:** Using Mystic Micro Sites, collectors can create private galleries with controlled access through encrypted URLs. This ensures that only invited guests with the correct link can view the collection.
   - **Benefit:** Collectors can protect their intellectual property and maintain exclusivity while sharing their collections with interested parties.


4. **Event Invitations and Information**

   - **Scenario:** An event organizer needs to send out invitations with sensitive information, such as location details and attendee lists.
   - **Solution:** Organizers can use Mystic Micro Sites to create secure invitation sites with all relevant event details. Access is granted only through a unique URL, reducing the risk of leaks.
   - **Benefit:** This ensures that only invited guests can access the event details, maintaining privacy and enhancing security.

## Installation

### Prerequisites

- Node.js (for frontend operations)
- PHP 8.3+
- Composer (for managing PHP dependencies)
- Web server (e.g., Apache, Nginx)

### Setup Process

1. **Clone the Repository**

   ```bash
   git clone https://github.com/yourusername/mystic-micro-sites.git
   cd mystic-micro-sites
   ```

2. **Install PHP Dependencies**

   Navigate to the `backend` directory and run Composer:

   ```bash
   cd backend
   composer install
   ```

3. **Environment Setup**

   The environment variables are automatically configured. The `install-cli.php` script generates necessary encryption keys and configuration files. Run:

   ```bash
   php install-cli.php
   ```

4. **Build the Frontend**

   Go to the `frontend` directory and build the Angular application:

   ```bash
   cd ../frontend
   npm install
   npm run build
   ```

5. **Web Server Configuration**

   Configure your web server to serve the content from the `dist/dashboard` directory and route API requests to the backend PHP services.

## Usage

1. **Access the Editor**

   Use your browser to navigate to the editor interface, where you can create a personalized mini-site using the provided tools.

2. **Site Generation**

   Upon saving your site, you will receive a unique URL with an embedded password, which is required for accessing the site.

3. **Site Access**

   The URL grants access to the site. If lost, you will need to recreate the site or use the master password to generate a new link.

## Configuration Details

### Angular Configuration

- **frontend/src/Configs/**: There are some configs for the frontend like the API Url and more. Take a look into the structure.

- **Build Commands**:
  - **Development**: Run `ng serve` for a local development server.
  - **Production**: Execute `npm run build` for optimized deployment.

### Backend Configuration

- **PHP Environment Variables**: Automatically set up by the `install-cli.php` script. The `.env` file is managed and updated during installation.
- **API Security**: JWT tokens ensure secure session handling, and CSRF tokens protect against request forgery.

### Security Features

- **Encryption Mechanisms**: Combines symmetric and asymmetric encryption to protect user data.
- **Rate Limiting**: Basic rate limiting is implemented to prevent request abuse.

## Use Cases

- **Personal Memorabilia**: Link digital memories to physical objects using NFC tags for easy retrieval and sharing.
- **Educational Content**: Securely share course materials and resources.
- **Private Collections**: Manage and display collections with controlled access.

## Future Enhancements

- **Performance Optimization**: Explore ways to improve load times and encryption efficiency.
- **UI Improvements**: Enhance user interface for greater usability and accessibility.
- **Extended Plugin System**: Develop a plugin system for additional features and integrations.
- **Testing and CI/CD**: Integrate continuous testing and deployment workflows for automated quality assurance.
- **Advanced Analytics**: Implement analytics to track user interactions and improve user engagement.
- **Time Limit**: Time limited access for microsites.
 
## Contribution

Contributions are encouraged! Please fork the repository and submit a pull request with your improvements.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.
