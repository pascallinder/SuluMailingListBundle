#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const {spawnSync} = require('child_process');

const packageRoot = path.resolve(__dirname, '..');
const repoRoot = path.resolve(packageRoot, '..', '..');
const envPath = path.join(repoRoot, '.env');
const defaultSvgDir = path.join(
  repoRoot,
  'assets',
  'admin',
  'node_modules',
  '@fortawesome',
  'fontawesome-free',
  'svgs',
  'solid'
);
const socialDir = path.join(packageRoot, 'Resources', 'social');
const defaultOutDir = path.join(packageRoot, 'icons');

function parseArgs(argv) {
  const args = argv.slice(2);
  let outDir = defaultOutDir;
  let svgDir = defaultSvgDir;
  const colors = [];
  for (let i = 0; i < args.length; i += 1) {
    const arg = args[i];
    if (arg === '--out' || arg === '-o') {
      const next = args[i + 1];
      if (!next) {
        throw new Error('Missing value for --out');
      }
      outDir = next;
      i += 1;
      continue;
    }
    if (arg === '--svg-dir' || arg === '-s') {
      const next = args[i + 1];
      if (!next) {
        throw new Error('Missing value for --svg-dir');
      }
      svgDir = next;
      i += 1;
      continue;
    }
    colors.push(arg);
  }
  return {outDir: path.resolve(outDir), svgDir: path.resolve(svgDir), colors};
}

function readEnvColors() {
  if (!fs.existsSync(envPath)) {
    throw new Error(`Missing .env at ${envPath}`);
  }
  const envContent = fs.readFileSync(envPath, 'utf8');
  const match = envContent.match(/^\s*COLOR_PICKER_COLORS\s*=\s*"?([^"\n]+)"?/m);
  if (!match) {
    throw new Error('COLOR_PICKER_COLORS not found in .env');
  }
  return match[1]
    .split(/\s+/)
    .map((c) => c.trim())
    .filter(Boolean);
}

function ensureConvert() {
  const result = spawnSync('convert', ['-version'], {stdio: 'ignore'});
  if (result.error || result.status !== 0) {
    throw new Error('ImageMagick `convert` not available in PATH.');
  }
}

function listSvgs(dir) {
  if (!fs.existsSync(dir)) {
    return [];
  }
  return fs
    .readdirSync(dir)
    .filter((file) => file.endsWith('.svg'))
    .map((file) => path.join(dir, file));
}

function listAllSvgs(svgDir) {
  if (!fs.existsSync(svgDir)) {
    throw new Error(`SVG directory not found: ${svgDir}`);
  }
  return [...listSvgs(svgDir), ...listSvgs(socialDir)];
}

function runConvert(args) {
  const result = spawnSync('convert', args, {stdio: 'inherit'});
  if (result.status !== 0) {
    throw new Error(`convert failed: ${args.join(' ')}`);
  }
}

function fileExists(filePath) {
  try {
    fs.accessSync(filePath, fs.constants.F_OK);
    return true;
  } catch (err) {
    return false;
  }
}

function generatePngs() {
  ensureConvert();
  const {outDir, svgDir, colors: argColors} = parseArgs(process.argv);
  const colors = argColors.length > 0 ? argColors : readEnvColors();
  const svgs = listAllSvgs(svgDir);

  fs.mkdirSync(outDir, {recursive: true});

  svgs.forEach((svgPath) => {
    const name = path.basename(svgPath, '.svg');
    const basePng = path.join(outDir, `${name}.png`);

    if (!fileExists(basePng)) {
      runConvert(['-background', 'none', svgPath, basePng]);
    }

    colors.forEach((color) => {
      const colorPng = path.join(outDir, `${name}-${color}.png`);
      if (!fileExists(colorPng)) {
        runConvert([
          '-background',
          'none',
          svgPath,
          '-fill',
          color,
          '-colorize',
          '100%',
          colorPng,
        ]);
      }
    });
  });

  console.log(`Generated PNGs in ${outDir}`);
}

try {
  generatePngs();
} catch (err) {
  console.error(err.message);
  process.exit(1);
}
