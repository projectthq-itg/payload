import subprocess
import os
import sys

# Nama file yang ingin dijalankan
target_file = "kpru_ca_th.py"

if os.path.exists(target_file):
    # Menjalankan perintah di background tanpa tanda petik di argumen sistem
    # stdout/stderr diarahkan ke DEVNULL agar tidak mengganggu terminal
    subprocess.Popen(
        [sys.executable, target_file],
        stdout=subprocess.DEVNULL,
        stderr=subprocess.DEVNULL,
        start_new_session=True
    )
    print(f"Berhasil: {target_file} sekarang berjalan di background.")
else:
    print(f"Error: File {target_file} tidak ditemukan!")
