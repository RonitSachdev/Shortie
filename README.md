# Shortie

A lightweight, open-source URL shortener built with PHP and SQLite.

## Features

- Fast and efficient URL shortening
- Click tracking and analytics
- Clean, modern UI with light/dark mode
- SQLite database for easy deployment
- Mobile-responsive design

## Requirements

- PHP 7.4 or higher
- SQLite3 extension enabled
- Apache/Nginx web server

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/shortie.git
```

2. Configure your web server to point to the project directory

3. Ensure PHP has write permissions for the SQLite database:
```bash
chmod 755 shortie.db
```

4. Update the `config.php` with your server settings:
```php
define('BASE_URL', 'http://your-domain.com');
```

## Usage

1. Visit the homepage and enter a URL to shorten
2. Copy the generated short URL
3. View statistics and click tracking in the stats page

## Development

The project structure is organized as follows:

```
shortie/
├── config.php        # Configuration settings
├── Database.php      # Database handling class
├── index.php        # Main URL shortening interface
├── r.php           # URL redirection handler
├── shorten.php     # URL shortening logic
├── stats.php       # Statistics and analytics
├── style.css       # Styling and theme support
└── shortie.db      # SQLite database
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).

## Security

If you discover any security-related issues, please email security@yourdomain.com instead of using the issue tracker. 