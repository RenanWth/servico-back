#!/bin/bash
set -e

# Criar diretórios necessários se não existirem
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache

# Ajustar permissões apenas dos diretórios de storage e cache
# Não mexemos nos outros arquivos para manter as permissões do host
chmod -R 775 /var/www/storage 2>/dev/null || true
chmod -R 775 /var/www/bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data /var/www/storage 2>/dev/null || true
chown -R www-data:www-data /var/www/bootstrap/cache 2>/dev/null || true

# Executar comando passado como argumento
exec "$@"

