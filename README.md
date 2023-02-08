# EVCS Management System

REST API for an electric vehicle charging station management system using Laravel 9. Due to the minimal requirements, I kept a small number of classes - for a bigger application the whole logic present in the controllers would have been isolated in services / repositories / observers, with DTOs for passing data around.

- [x] Administrators can manage companies.
- [x] Administrators can manage charging stations.
- [x] Companies are arranged in a hierarchical structure.
- [x] Users can search the nearest stations to their position.

## Local Development

This project uses Docker adapted from an Laravel Initializer template - to build and start the containers use:

```shell
make build
make up
```

Some useful commands can be found inside the Makefile.

### Login:

#### Administrator

- email: admin@virta.global
- password: secret

#### Customer

- email: customer@virta.global
- password: secret

### Links

- **Your Application** http://localhost
- **API Documentation** http://localhost/api/documentation#/
- **Preview Emails via Mailpit** http://localhost:8025
- **MeiliSearch Administration Panel** http://localhost:7700
- **Laravel Telescope** http://localhost/telescope
