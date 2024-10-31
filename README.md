# Weather Report Project

This project is a weather reporting application that provides current weather conditions based on user-provided locations. It utilizes the GROQ API for data retrieval and sends weather reports via email.

## Features

- Fetches current weather data including temperature, humidity, wind speed, and cloud coverage.
- Sends weather report emails in a formatted HTML layout.
- Supports configuration through environment variables for sensitive information.

## Technologies Used

- PHP
- Composer (for dependency management)
- PHPMailer (for sending emails)
- GROQ API (for weather data)
- Dotenv (for loading environment variables)

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/Aamir-Alam01/weather-report
   ```
### 2. Create and Configure Your Environment
```bash
cp .env.example .env
```
- Edit the .env file to set your API keys and other details.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).