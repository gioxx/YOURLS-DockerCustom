# YOURLS Docker Custom Image with ZipArchive

This repository contains a custom Docker image for [YOURLS](https://yourls.org/) based on the official image, with the `ZipArchive` PHP extension enabled. This extension is required by various YOURLS plugins and for file-based features that depend on zip support.

## 🧱 Base Image

This image extends the official [`YOURLS`](https://hub.docker.com/r/yourls/yourls) Docker image.

## ✅ Features

- Based on `yourls:latest`
- Adds the PHP `zip` extension via `docker-php-ext-install`
- Installs required system libraries (`libzip-dev`, `unzip`)
- Clean and production-ready Docker layer

## 📦 Usage

### 1. Clone the repository

```bash
git clone https://github.com/gioxx/YOURLS-DockerCustom.git
cd YOURLS-DockerCustom
```

### 2. Build the image

```bash
docker build -t yourls-custom:zip .
```

### 3. Run a container

```bash
docker run -d --name yourls \
  -e YOURLS_SITE="http://localhost:8080" \
  -e YOURLS_USER="admin" \
  -e YOURLS_PASS="yourpassword" \
  -p 8080:80 \
  yourls-custom:zip
```

### 4. Verify `zip` extension is enabled

```bash
docker exec -it yourls php -m | grep zip
# Expected output: zip
```

## 🛳️ Docker Compose: a practical example

I created and tested the image on Portainer, then created a stack that uses the custom image. I offer a practical example of a Stack (Docker Compose) that you can use as well.  
I recommend, of course, **that you change passwords for the database and YOURLS administrator user**, especially if you plan to use it in a production environment (I only use it for development and plugin testing, in a local environment not exposed to the Internet).

```bash
services:
  yourls:
    image: yourls-custom:latest

    restart: unless-stopped
    ports:
      - 8080:80
    environment:
      YOURLS_DB_PASS: password
      YOURLS_DB_USER: root
      YOURLS_DB_NAME: yourls
      YOURLS_DB_HOST: mysql
      YOURLS_SITE: http://localhost:8080
      YOURLS_USER: admin
      YOURLS_PASS: password
    volumes:
      - /DEMO/YOURLS:/var/www/html/user

  mysql:
    image: mysql
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: password
      MYSQL_DATABASE: yourls

volumes:
  db_data:
```

Having a volume mounted on the local file system will allow you to easily get your hands on all commonly used configurations and folders in YOURLS, but it is optional.

## 🔁 Optional: Push to a Container Registry

You can push this image to Docker Hub or GitHub Container Registry (GHCR) for CI/CD usage or deployment:

```bash
docker tag yourls-custom:zip ghcr.io/gioxx/yourls-custom:zip
docker push ghcr.io/gioxx/yourls-custom:zip
```

## 📂 File Overview

- `Dockerfile`: Extends the base image and installs `ZipArchive`.
- `.dockerignore`: Excludes unnecessary files from the build context.
- `README.md`: This file.

## 📘 References

- [YOURLS Official Site](https://yourls.org/)
- [YOURLS on Docker Hub](https://hub.docker.com/r/yourls/yourls)
- [PHP zip extension](https://www.php.net/manual/en/book.zip.php)

## 📜 License

This repository inherits the [YOURLS license](https://github.com/YOURLS/YOURLS/blob/master/LICENSE). Modifications are provided under the same terms.

## 💬 About

Lovingly developed by the usually-on-vacation brain cell of [Gioxx](https://github.com/gioxx).  
Visit [gioxx.org](https://gioxx.org) for blog posts, tech, and other things.

## 🙌 Contributing

Pull requests and feature suggestions are welcome.  
If you find bugs or have feature requests, [open an issue](https://github.com/gioxx/YOURLS-PluginManager/issues).  
If you find it useful, leave a ⭐ on GitHub! ❤️
