FROM nginx:alpine

# Add user for laravel application
RUN echo www:x:1000: >> /etc/group
RUN echo www:x:1000:1000::/home/www:/bin/bash >> /etc/passwd

# Edit Nginx Runtime User
RUN sed -i 's/^user .*/user www;/' /etc/nginx/nginx.conf
