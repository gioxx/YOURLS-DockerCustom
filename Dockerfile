FROM yourls:latest

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY user/ /usr/src/yourls/user/

RUN set -eux; \
    plugin_url="$(curl -fsSL https://api.github.com/repos/gioxx/YOURLS-PluginManager/releases/latest | sed -n 's/.*"zipball_url": "\([^"]*\)".*/\1/p' | head -n1)"; \
    tmpdir="$(mktemp -d)"; \
    curl -fsSL "$plugin_url" -o "$tmpdir/plugin-manager.zip"; \
    unzip -q "$tmpdir/plugin-manager.zip" -d "$tmpdir"; \
    plugin_dir="$(find "$tmpdir" -mindepth 1 -maxdepth 1 -type d | head -n1)"; \
    mkdir -p /usr/src/yourls/user/plugins/yourls-plugin-manager; \
    cp -a "$plugin_dir"/. /usr/src/yourls/user/plugins/yourls-plugin-manager/; \
    rm -rf "$tmpdir"

RUN set -eux; \
    plugin_url="$(curl -fsSL https://api.github.com/repos/gioxx/YOURLS-LanguageSwitcher/releases/latest | sed -n 's/.*"zipball_url": "\([^"]*\)".*/\1/p' | head -n1)"; \
    tmpdir="$(mktemp -d)"; \
    curl -fsSL "$plugin_url" -o "$tmpdir/language-switcher.zip"; \
    unzip -q "$tmpdir/language-switcher.zip" -d "$tmpdir"; \
    plugin_dir="$(find "$tmpdir" -mindepth 1 -maxdepth 1 -type d | head -n1)"; \
    mkdir -p /usr/src/yourls/user/plugins/language-switcher; \
    cp -a "$plugin_dir"/. /usr/src/yourls/user/plugins/language-switcher/; \
    rm -rf "$tmpdir"

RUN php -m | grep zip
