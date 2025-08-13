#!/bin/bash

# Jua Implementation Deployment Script for Coolify
# This script helps prepare and deploy the application to Coolify

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting Jua Implementation deployment process...${NC}"

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: Please run this script from the root of your Laravel project${NC}"
    exit 1
fi

# Ensure the .env.production file exists
if [ ! -f ".env.production" ]; then
    echo -e "${RED}Error: .env.production file not found${NC}"
    echo -e "${YELLOW}Please create a .env.production file with your production environment variables${NC}"
    exit 1
fi

# Build frontend assets
echo -e "${GREEN}Building frontend assets...${NC}"
npm ci
npm run build

# Optimize Laravel for production
echo -e "${GREEN}Optimizing Laravel for production...${NC}"
composer install --no-dev --optimize-autoloader

# Clear caches
echo -e "${GREEN}Clearing caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate optimized class loader
echo -e "${GREEN}Generating optimized class loader...${NC}"
composer dump-autoload -o

# Create storage link if needed
echo -e "${GREEN}Creating storage link...${NC}"
php artisan storage:link

# Run database migrations (optional, comment out if you want to run this manually)
# echo -e "${YELLOW}Do you want to run database migrations? (y/n)${NC}"
# read -r run_migrations
# if [ "$run_migrations" = "y" ]; then
#     echo -e "${GREEN}Running database migrations...${NC}"
#     php artisan migrate --force
# fi

echo -e "${GREEN}Deployment preparation completed!${NC}"
echo -e "${YELLOW}Now you can deploy to Coolify using the following steps:${NC}"
echo -e "1. Log in to your Coolify dashboard"
echo -e "2. Select the 'Jua' project"
echo -e "3. Click 'New Resource' and select 'Application'"
echo -e "4. Choose 'Docker Compose' as the deployment method"
echo -e "5. Connect to your Git repository"
echo -e "6. Set the environment variables from .env.production"
echo -e "7. Deploy the application"

echo -e "${GREEN}Deployment script completed!${NC}"
