#!/bin/bash
# buildcukimay - Build HANYA public folder, SKIP compilation

echo "========================================"
echo "🚀 BUILD-CUKIMAY - PUBLIC FOLDER ONLY"
echo "========================================"

# 1. Cek apakah sudah ada build sebelumnya
if [ ! -d ".next" ]; then
    echo "⚠️  First time: Doing FULL build dulu..."
    npm run build
fi

# 2. Copy semua file public ke .next/static
echo "📁 Copying public files..."
mkdir -p .next/static
cp -r public/* .next/static/ 2>/dev/null || true

# 3. Update BUILD_ID (biar Next.js tau ada perubahan)
echo $(date +%s) > .next/BUILD_ID

# 4. Kill server lama di port 8080
echo "🔫 Killing old server..."
fuser -k 8080/tcp 2>/dev/null || true
sleep 1

# 5. Start server baru
echo "🚀 Starting server on port 8080..."
PORT=8080 nohup npx next start > 8080.log 2>&1 &

# 6. Tunggu dan cek
sleep 3
if curl -s http://localhost:8080 > /dev/null; then
    echo "✅ SERVER RUNNING: http://localhost:8080"
    echo "📋 Logs: tail -f 8080.log"
else
    echo "⚠️  Server starting... Check: tail -f 8080.log"
fi

echo "========================================"
echo "⏱️  Build time: 3-5 detik (PUBLIC ONLY)"
echo "🎯 Untuk: gambar, CSS, file static di public/"
echo "========================================"
