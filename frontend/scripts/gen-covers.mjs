import { mkdirSync, writeFileSync } from 'node:fs';

// Categorie-covers (1200x630, og-ratio) — kleur-matchend per categorie.
const cats = [
  { slug: 'rijbewijs',     title: 'Rijbewijs gehaald',      emoji: '🚗', a: '#1ea67f', b: '#147a5c' },
  { slug: 'geslaagd',      title: 'Geslaagd',               emoji: '🎓', a: '#e6a008', b: '#9c6b00' },
  { slug: 'eigen-huis',    title: 'Eigen huis gekocht',     emoji: '🏡', a: '#d96638', b: '#a23f17' },
  { slug: 'vaderdag',      title: 'Vaderdag',               emoji: '👔', a: '#4169e6', b: '#2a45a8' },
  { slug: 'moederdag',     title: 'Moederdag',              emoji: '💐', a: '#e0538a', b: '#b13164' },
  { slug: 'kind-geboren',  title: 'Kind geboren',           emoji: '👶', a: '#1f9fcc', b: '#17738f' },
  { slug: 'verjaardag',    title: 'Verjaardag',             emoji: '🎂', a: '#8b54d6', b: '#653ca8' },
  { slug: 'voetbalclubs',  title: 'Team- of clublied',      emoji: '⚽', a: '#4aa838', b: '#347526' },
  { slug: 'bouwbedrijven', title: 'Bouwbedrijven',          emoji: '🏗️', a: '#d97e1a', b: '#8f5212' },
  { slug: 'anders',        title: 'Een ander moment',       emoji: '✨', a: '#ff6a4a', b: '#e04a2a' },
];

function esc(s) {
  return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function svg({ title, emoji, a, b }) {
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" width="1200" height="630" role="img" aria-label="${esc(title)}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="${a}"/>
      <stop offset="1" stop-color="${b}"/>
    </linearGradient>
    <radialGradient id="glow" cx="0.74" cy="0.22" r="0.85">
      <stop offset="0" stop-color="#ffffff" stop-opacity="0.38"/>
      <stop offset="1" stop-color="#ffffff" stop-opacity="0"/>
    </radialGradient>
    <linearGradient id="shade" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0.4" stop-color="#000000" stop-opacity="0"/>
      <stop offset="1" stop-color="#000000" stop-opacity="0.5"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="630" fill="url(#bg)"/>
  <circle cx="990" cy="130" r="200" fill="#ffffff" opacity="0.10"/>
  <circle cx="1110" cy="520" r="150" fill="#ffffff" opacity="0.07"/>
  <circle cx="150" cy="560" r="130" fill="#000000" opacity="0.06"/>
  <rect width="1200" height="630" fill="url(#glow)"/>
  <text x="600" y="312" font-size="300" text-anchor="middle" dominant-baseline="central">${emoji}</text>
  <rect width="1200" height="630" fill="url(#shade)"/>
  <text x="80" y="96" font-family="'Manrope','Segoe UI',sans-serif" font-size="28" font-weight="700" letter-spacing="3" fill="#ffffff" opacity="0.82">VOOR IEDER MOMENT</text>
  <text x="80" y="556" font-family="Georgia,'Times New Roman',serif" font-size="68" font-weight="700" fill="#ffffff">${esc(title)}</text>
</svg>
`;
}

// Schrijft naar frontend/public/momenten/ (relatief t.o.v. dit script).
const outDir = new URL('../public/momenten/', import.meta.url);
mkdirSync(outDir, { recursive: true });
for (const c of cats) {
  writeFileSync(new URL(`${c.slug}.svg`, outDir), svg(c), 'utf8');
}
console.log(`Generated ${cats.length} covers in frontend/public/momenten/`);
