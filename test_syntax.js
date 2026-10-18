const fs = require('fs');
const { execSync } = require('child_process');

const html = fs.readFileSync('c:/Users/majip/Downloads/imat rutine/index.html', 'utf8');

// There might be multiple <script> tags (like Tailwind CDN at the top)
const scriptRegex = /<script\b[^>]*>([\s\S]*?)<\/script>/gi;
let match;
let index = 0;
while ((match = scriptRegex.exec(html)) !== null) {
  index++;
  const scriptContent = match[1].trim();
  if (!scriptContent) continue;
  const tempFile = `c:/Users/majip/Downloads/imat rutine/temp_script_${index}.js`;
  fs.writeFileSync(tempFile, scriptContent, 'utf8');
  console.log(`Checking script ${index} (length: ${scriptContent.length})...`);
  try {
    execSync(`node -c "${tempFile}"`);
    console.log(`Script ${index} syntax is VALID!`);
  } catch (err) {
    console.error(`Script ${index} has SYNTAX ERROR:`, err.message);
  } finally {
    if (fs.existsSync(tempFile)) fs.unlinkSync(tempFile);
  }
}
