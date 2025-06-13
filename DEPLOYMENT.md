# Garage.lk Deployment Checklist

## Pre-deployment Tasks

### 1. Server Requirements
- [ ] PHP 7.4 or higher
- [ ] MySQL 5.7 or higher
- [ ] Apache/Nginx web server
- [ ] SSL certificate
- [ ] Sufficient storage for uploads
- [ ] Regular backup system

### 2. Security Setup
- [ ] Configure secure database credentials
- [ ] Set up SSL certificate
- [ ] Configure secure file permissions
- [ ] Set up firewall rules
- [ ] Enable HTTPS redirection
- [ ] Configure secure session handling

### 3. Database Setup
- [ ] Create production database
- [ ] Import schema from `database/schema.sql`
- [ ] Create database user with limited privileges
- [ ] Configure database backup schedule
- [ ] Test database connection

### 4. File Structure
- [ ] Set up proper directory structure
- [ ] Configure document root to `public` directory
- [ ] Create and secure `uploads` directory
- [ ] Set proper file permissions
- [ ] Configure .htaccess for security

### 5. Environment Configuration
- [ ] Update database configuration
- [ ] Configure email settings
- [ ] Set up Google Maps API key
- [ ] Configure error reporting
- [ ] Set up logging

### 6. Testing
- [ ] Test user registration
- [ ] Test garage registration
- [ ] Test document uploads
- [ ] Test email notifications
- [ ] Test location services
- [ ] Test admin functions
- [ ] Test booking system
- [ ] Test payment integration

## Deployment Steps

1. **Prepare Files**
   ```bash
   # Create production directory
   mkdir -p /var/www/garage.lk
   
   # Copy files
   cp -r * /var/www/garage.lk/
   
   # Set permissions
   chown -R www-data:www-data /var/www/garage.lk
   chmod -R 755 /var/www/garage.lk
   chmod -R 777 /var/www/garage.lk/public/uploads
   ```

2. **Configure Web Server**
   ```apache
   # Apache configuration
   <VirtualHost *:80>
       ServerName garage.lk
       DocumentRoot /var/www/garage.lk/public
       
       <Directory /var/www/garage.lk/public>
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/garage.lk-error.log
       CustomLog ${APACHE_LOG_DIR}/garage.lk-access.log combined
   </VirtualHost>
   ```

3. **SSL Configuration**
   ```apache
   # SSL configuration
   <VirtualHost *:443>
       ServerName garage.lk
       DocumentRoot /var/www/garage.lk/public
       
       SSLEngine on
       SSLCertificateFile /etc/letsencrypt/live/garage.lk/fullchain.pem
       SSLCertificateKeyFile /etc/letsencrypt/live/garage.lk/privkey.pem
       
       <Directory /var/www/garage.lk/public>
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

4. **Database Migration**
   ```bash
   # Import database schema
   mysql -u production_user -p garage_lk < database/schema.sql
   ```

5. **Final Checks**
   - [ ] Verify SSL certificate
   - [ ] Test all forms and submissions
   - [ ] Check email delivery
   - [ ] Verify file uploads
   - [ ] Test admin panel
   - [ ] Monitor error logs

## Post-deployment Tasks

### 1. Monitoring
- [ ] Set up server monitoring
- [ ] Configure error logging
- [ ] Set up performance monitoring
- [ ] Configure backup monitoring

### 2. Maintenance
- [ ] Schedule regular backups
- [ ] Set up log rotation
- [ ] Configure security updates
- [ ] Plan for scaling

### 3. Documentation
- [ ] Update deployment documentation
- [ ] Document server configuration
- [ ] Create maintenance procedures
- [ ] Document backup/restore procedures

## Emergency Procedures

### 1. Backup Restoration
```bash
# Restore database
mysql -u production_user -p garage_lk < backup.sql

# Restore files
tar -xzf backup.tar.gz -C /var/www/garage.lk
```

### 2. Emergency Contacts
- System Administrator: [Contact Info]
- Database Administrator: [Contact Info]
- Web Hosting Provider: [Contact Info]
- SSL Certificate Provider: [Contact Info]

## Regular Maintenance

### Daily Tasks
- [ ] Check error logs
- [ ] Monitor server resources
- [ ] Verify backup completion
- [ ] Check email delivery

### Weekly Tasks
- [ ] Review security logs
- [ ] Check SSL certificate status
- [ ] Monitor disk space
- [ ] Review performance metrics

### Monthly Tasks
- [ ] Update system packages
- [ ] Review backup strategy
- [ ] Check SSL certificate expiration
- [ ] Review security measures 