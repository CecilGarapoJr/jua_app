# Jua Implementation Deployment Guide

This guide outlines the steps to deploy the Jua application to your specific Coolify environment (version 4.0.0-beta.420.6).

## Prerequisites

- Access to your Coolify dashboard
- Git repository with your Jua Implementation codebase
- MySQL database (you can use the existing "Jua_db" in Coolify)

## Deployment Options

You have two main deployment options with Coolify:

1. **Docker Compose Deployment** - Recommended for full control
2. **Direct Application Deployment** - Simpler but less customizable

## Option 1: Docker Compose Deployment (Recommended)

### Step 1: Prepare Your Repository

Ensure your repository contains the following files:
- `docker-compose.prod.yml`
- `Dockerfile.prod`
- `.env.production`
- `docker/nginx/conf.d/app.conf`
- `docker/supervisor/supervisord.conf`

### Step 2: Create a New Application in Coolify

1. Log in to your Coolify dashboard
2. Navigate to the "Jua" project (or create a new one)
3. Click "New Resource" and select "Application"
4. Choose "Docker Compose" as the deployment method
5. Connect to your Git repository
6. Set the Docker Compose file path to `docker-compose.prod.yml`

### Step 3: Configure Environment Variables

1. In the Coolify application settings, go to the "Environment Variables" section
2. Import variables from your `.env.production` file or set them manually
3. Make sure to update the following variables:
   - `DB_HOST` - Set to your MySQL database service name or IP
   - `DB_DATABASE` - Set to your database name (e.g., "default")
   - `DB_USERNAME` - Set to your database username (e.g., "mysql")
   - `DB_PASSWORD` - Set to your secure database password
   - `APP_URL` - Set to your application URL (e.g., "https://jua.twunhu.app")
   - `REDIS_HOST` - Set to "redis" if using the included Redis service

### Step 4: Configure Build Settings

1. In the Coolify application settings, go to the "Build" section
2. Set the following options:
   - Root Directory: `/`
   - Docker Compose File: `docker-compose.prod.yml`
   - Docker Compose Service: `app`

### Step 5: Configure Deployment Settings

1. In the Coolify application settings, go to the "Deploy" section
2. Set up automatic deployments on Git push if desired
3. Configure health checks:
   - Path: `/`
   - Port: `80`
   - Interval: `30s`
   - Timeout: `10s`
   - Retries: `3`

### Step 6: Deploy the Application

1. Click the "Deploy" button in the Coolify dashboard
2. Monitor the build and deployment logs
3. Once deployed, access your application at the configured URL

## Option 2: Direct Application Deployment

### Step 1: Create a New Application in Coolify

1. Log in to your Coolify dashboard
2. Navigate to the "Jua" project (or create a new one)
3. Click "New Resource" and select "Application"
4. Choose "PHP Laravel" as the application type
5. Connect to your Git repository

### Step 2: Configure Build Settings

1. In the Coolify application settings, go to the "Build" section
2. Set the following options:
   - PHP Version: `8.2`
   - Node.js Version: `18`
   - Install Command: `composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev`
   - Build Command: `npm ci && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan storage:link`
   - Start Command: `php artisan serve --host=0.0.0.0 --port=80`

### Step 3: Configure Environment Variables

1. In the Coolify application settings, go to the "Environment Variables" section
2. Import variables from your `.env.production` file or set them manually
3. Make sure to update the database connection variables to point to your Coolify MySQL database

### Step 4: Deploy the Application

1. Click the "Deploy" button in the Coolify dashboard
2. Monitor the build and deployment logs
3. Once deployed, access your application at the configured URL

## Database Configuration

### Using Existing Jua_db Database

1. In the Coolify dashboard, go to the "Databases" section
2. Find the "Jua_db" MySQL database
3. Note the connection details (host, port, username, password)
4. Update your application's environment variables to use these connection details

### Creating a New Database

If you need to create a new database:

1. In the Coolify dashboard, go to the "Databases" section
2. Click "New Resource" and select "Database"
3. Choose "MySQL" as the database type
4. Configure the database settings (name, username, password)
5. Once created, update your application's environment variables to use the new database

## SSL/TLS Configuration

For secure HTTPS connections:

1. In the Coolify dashboard, go to your application's settings
2. Navigate to the "Domains" section
3. Add your domain (e.g., "jua.twunhu.app")
4. Enable "Generate SSL Certificate" option
5. Save the changes

## Post-Deployment Tasks

After successful deployment, perform these tasks:

1. Run database migrations:
   ```
   php artisan migrate --force
   ```

2. Seed the database if needed:
   ```
   php artisan db:seed --force
   ```

3. Set up a queue worker:
   - If using Docker Compose, this is already configured in the supervisor settings
   - If using direct deployment, set up a separate queue worker service in Coolify

4. Set up a scheduler:
   - If using Docker Compose, this is already configured in the supervisor settings
   - If using direct deployment, set up a cron job to run `php artisan schedule:run` every minute

## Monitoring and Maintenance

- Monitor application logs in the Coolify dashboard
- Set up health checks to ensure your application is running properly
- Configure automatic backups for your database
- Set up alerts for application downtime or errors

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Verify database credentials in environment variables
   - Check if the database service is running
   - Ensure the database host is correctly specified

2. **Storage Permission Issues**
   - Ensure the storage directory is writable by the web server
   - Run `php artisan storage:link` to create the symbolic link

3. **Build Failures**
   - Check the build logs for specific errors
   - Verify that all dependencies are correctly specified in composer.json and package.json
   - Ensure your code is compatible with PHP 8.2

4. **Application Not Loading**
   - Check the application logs for errors
   - Verify that the application URL is correctly set in environment variables
   - Check if the web server is running and accessible

For additional help, refer to the [Coolify documentation](https://coolify.io/docs) or contact your system administrator.
