#!/bin/bash

LOG_FILE="/tmp/cloudflared.log"

# Hapus log lama biar nggak bingung baca URL-nya
rm -f $LOG_FILE

echo "🚀 Memulai Cloudflare Tunnel (Protokol: HTTP2)..."

# Jalankan tunnel di background
nohup cloudflared tunnel --protocol http2 --url ssh://localhost:2222 > $LOG_FILE 2>&1 &

echo "⏳ Menunggu URL dari Cloudflare..."
sleep 5

# Ambil URL dari log
TUNNEL_URL=$(grep -o 'https://[-a-zA-Z0-9.]*trycloudflare.com' $LOG_FILE)

if [ -z "$TUNNEL_URL" ]; then
    echo "❌ Gagal dapet URL. Coba cek log pake: tail -n 20 $LOG_FILE"
else
    echo "✅ Tunnel Berhasil!"
    echo "🔗 URL Kamu: $TUNNEL_URL"
    echo ""
    echo "💻 Di LAPTOP kamu, ketik ini untuk login:"
    echo "ssh -o ProxyCommand=\"cloudflared access tcp --hostname %h\" root@${TUNNEL_URL#https://}"
fi
