#!/bin/bash
# Generate self-signed SSL certificate for development
SSL_DIR="$(dirname "$0")/ssl"
mkdir -p "$SSL_DIR"

if [ ! -f "$SSL_DIR/cert.pem" ] || [ ! -f "$SSL_DIR/key.pem" ]; then
    echo "Generating self-signed SSL certificate..."
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout "$SSL_DIR/key.pem" \
        -out "$SSL_DIR/cert.pem" \
        -subj "/C=BR/ST=SP/L=SaoPaulo/O=Alphaview/CN=localhost" \
        2>/dev/null
    echo "SSL certificate generated at $SSL_DIR/"
else
    echo "SSL certificate already exists."
fi
